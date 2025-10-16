# Integrasi dengan Ionic/Angular

Panduan lengkap untuk mengintegrasikan API authentication Laravel ini dengan aplikasi Ionic/Angular.

## 📦 Installation

Install dependencies yang diperlukan:
```bash
npm install @capacitor/storage
npm install @capacitor/preferences  # Untuk Capacitor 4+
```

## 🔧 Configuration

### 1. Environment Configuration

**src/environments/environment.ts:**
```typescript
export const environment = {
  production: false,
  apiUrl: 'http://127.0.0.1:8000/api'  // Local development
};
```

**src/environments/environment.prod.ts:**
```typescript
export const environment = {
  production: true,
  apiUrl: 'https://your-domain.com/api'  // Production
};
```

### 2. CORS Configuration (Laravel)

Pastikan file `config/cors.php` sudah dikonfigurasi dengan benar:
```php
'paths' => ['api/*'],
'allowed_origins' => ['*'],  // Atau specific domain untuk production
'allowed_methods' => ['*'],
'allowed_headers' => ['*'],
'supports_credentials' => false,
```

## 🔐 Auth Service

**src/app/services/auth.service.ts:**
```typescript
import { Injectable } from '@angular/core';
import { HttpClient, HttpHeaders } from '@angular/common/http';
import { Observable, BehaviorSubject, from } from 'rxjs';
import { tap, map, catchError } from 'rxjs/operators';
import { Preferences } from '@capacitor/preferences';
import { environment } from '../../environments/environment';

export interface User {
  id: number;
  name: string;
  email: string;
  created_at: string;
  updated_at: string;
}

export interface AuthResponse {
  success: boolean;
  message: string;
  data: {
    user: User;
    access_token: string;
    token_type: string;
  };
}

export interface ApiResponse {
  success: boolean;
  message: string;
  data?: any;
  errors?: any;
}

@Injectable({
  providedIn: 'root'
})
export class AuthService {
  private apiUrl = environment.apiUrl;
  private tokenKey = 'auth_token';
  private userKey = 'auth_user';
  
  // Observable untuk status authentication
  private isAuthenticatedSubject = new BehaviorSubject<boolean>(false);
  public isAuthenticated$ = this.isAuthenticatedSubject.asObservable();
  
  // Observable untuk user data
  private currentUserSubject = new BehaviorSubject<User | null>(null);
  public currentUser$ = this.currentUserSubject.asObservable();

  constructor(private http: HttpClient) {
    this.loadStoredToken();
  }

  // Load token dari storage saat app start
  private async loadStoredToken() {
    const token = await this.getToken();
    const user = await this.getStoredUser();
    
    if (token && user) {
      this.isAuthenticatedSubject.next(true);
      this.currentUserSubject.next(user);
    }
  }

  // Get HTTP Headers
  private async getHeaders(): Promise<HttpHeaders> {
    const token = await this.getToken();
    return new HttpHeaders({
      'Content-Type': 'application/json',
      'Accept': 'application/json',
      'Authorization': token ? `Bearer ${token}` : ''
    });
  }

  // Register
  register(name: string, email: string, password: string, passwordConfirmation: string): Observable<AuthResponse> {
    return this.http.post<AuthResponse>(`${this.apiUrl}/auth/register`, {
      name,
      email,
      password,
      password_confirmation: passwordConfirmation
    }).pipe(
      tap(async (response) => {
        if (response.success) {
          await this.saveToken(response.data.access_token);
          await this.saveUser(response.data.user);
          this.isAuthenticatedSubject.next(true);
          this.currentUserSubject.next(response.data.user);
        }
      })
    );
  }

  // Login
  login(email: string, password: string): Observable<AuthResponse> {
    return this.http.post<AuthResponse>(`${this.apiUrl}/auth/login`, {
      email,
      password
    }).pipe(
      tap(async (response) => {
        if (response.success) {
          await this.saveToken(response.data.access_token);
          await this.saveUser(response.data.user);
          this.isAuthenticatedSubject.next(true);
          this.currentUserSubject.next(response.data.user);
        }
      })
    );
  }

  // Logout
  logout(): Observable<ApiResponse> {
    return from(this.getHeaders()).pipe(
      map(headers => this.http.post<ApiResponse>(`${this.apiUrl}/auth/logout`, {}, { headers })),
      tap(async () => {
        await this.clearAuth();
      })
    );
  }

  // Get Profile
  getProfile(): Observable<ApiResponse> {
    return from(this.getHeaders()).pipe(
      map(headers => this.http.get<ApiResponse>(`${this.apiUrl}/auth/profile`, { headers })),
      tap(async (response: any) => {
        if (response.success) {
          await this.saveUser(response.data);
          this.currentUserSubject.next(response.data);
        }
      })
    );
  }

  // Update Profile
  updateProfile(data: { name?: string; email?: string; current_password?: string; password?: string; password_confirmation?: string }): Observable<ApiResponse> {
    return from(this.getHeaders()).pipe(
      map(headers => this.http.put<ApiResponse>(`${this.apiUrl}/auth/profile`, data, { headers })),
      tap(async (response: any) => {
        if (response.success) {
          await this.saveUser(response.data);
          this.currentUserSubject.next(response.data);
        }
      })
    );
  }

  // Forgot Password
  forgotPassword(email: string): Observable<ApiResponse> {
    return this.http.post<ApiResponse>(`${this.apiUrl}/auth/forgot-password`, { email });
  }

  // Reset Password
  resetPassword(token: string, email: string, password: string, passwordConfirmation: string): Observable<ApiResponse> {
    return this.http.post<ApiResponse>(`${this.apiUrl}/auth/reset-password`, {
      token,
      email,
      password,
      password_confirmation: passwordConfirmation
    });
  }

  // Revoke All Tokens
  revokeAllTokens(): Observable<ApiResponse> {
    return from(this.getHeaders()).pipe(
      map(headers => this.http.post<ApiResponse>(`${this.apiUrl}/auth/revoke-tokens`, {}, { headers })),
      tap(async () => {
        await this.clearAuth();
      })
    );
  }

  // Token Management
  private async saveToken(token: string): Promise<void> {
    await Preferences.set({ key: this.tokenKey, value: token });
  }

  async getToken(): Promise<string | null> {
    const { value } = await Preferences.get({ key: this.tokenKey });
    return value;
  }

  // User Management
  private async saveUser(user: User): Promise<void> {
    await Preferences.set({ key: this.userKey, value: JSON.stringify(user) });
  }

  private async getStoredUser(): Promise<User | null> {
    const { value } = await Preferences.get({ key: this.userKey });
    return value ? JSON.parse(value) : null;
  }

  // Clear Authentication
  private async clearAuth(): Promise<void> {
    await Preferences.remove({ key: this.tokenKey });
    await Preferences.remove({ key: this.userKey });
    this.isAuthenticatedSubject.next(false);
    this.currentUserSubject.next(null);
  }

  // Check if user is authenticated
  async isLoggedIn(): Promise<boolean> {
    const token = await this.getToken();
    return !!token;
  }
}
```

## 🔒 Auth Guard

**src/app/guards/auth.guard.ts:**
```typescript
import { Injectable } from '@angular/core';
import { CanActivate, Router } from '@angular/router';
import { AuthService } from '../services/auth.service';

@Injectable({
  providedIn: 'root'
})
export class AuthGuard implements CanActivate {
  
  constructor(
    private authService: AuthService,
    private router: Router
  ) {}

  async canActivate(): Promise<boolean> {
    const isAuthenticated = await this.authService.isLoggedIn();
    
    if (!isAuthenticated) {
      this.router.navigate(['/login']);
      return false;
    }
    
    return true;
  }
}
```

## 📄 Example Pages

### 1. Login Page

**src/app/pages/login/login.page.ts:**
```typescript
import { Component } from '@angular/core';
import { Router } from '@angular/router';
import { AuthService } from '../../services/auth.service';
import { LoadingController, ToastController } from '@ionic/angular';

@Component({
  selector: 'app-login',
  templateUrl: './login.page.html',
  styleUrls: ['./login.page.scss'],
})
export class LoginPage {
  email: string = '';
  password: string = '';

  constructor(
    private authService: AuthService,
    private router: Router,
    private loadingController: LoadingController,
    private toastController: ToastController
  ) {}

  async login() {
    if (!this.email || !this.password) {
      this.showToast('Please fill in all fields', 'warning');
      return;
    }

    const loading = await this.loadingController.create({
      message: 'Logging in...',
    });
    await loading.present();

    this.authService.login(this.email, this.password).subscribe({
      next: async (response) => {
        await loading.dismiss();
        if (response.success) {
          this.showToast('Login successful!', 'success');
          this.router.navigate(['/home']);
        }
      },
      error: async (error) => {
        await loading.dismiss();
        const message = error.error?.message || 'Login failed';
        this.showToast(message, 'danger');
      }
    });
  }

  async showToast(message: string, color: string) {
    const toast = await this.toastController.create({
      message,
      duration: 2000,
      color,
      position: 'top'
    });
    toast.present();
  }

  goToRegister() {
    this.router.navigate(['/register']);
  }

  goToForgotPassword() {
    this.router.navigate(['/forgot-password']);
  }
}
```

**src/app/pages/login/login.page.html:**
```html
<ion-header>
  <ion-toolbar>
    <ion-title>Login</ion-title>
  </ion-toolbar>
</ion-header>

<ion-content class="ion-padding">
  <div class="login-container">
    <ion-card>
      <ion-card-content>
        <ion-item>
          <ion-label position="floating">Email</ion-label>
          <ion-input type="email" [(ngModel)]="email"></ion-input>
        </ion-item>

        <ion-item>
          <ion-label position="floating">Password</ion-label>
          <ion-input type="password" [(ngModel)]="password"></ion-input>
        </ion-item>

        <ion-button expand="block" (click)="login()" class="ion-margin-top">
          Login
        </ion-button>

        <ion-button expand="block" fill="clear" (click)="goToForgotPassword()">
          Forgot Password?
        </ion-button>

        <ion-button expand="block" fill="outline" (click)="goToRegister()">
          Create Account
        </ion-button>
      </ion-card-content>
    </ion-card>
  </div>
</ion-content>
```

### 2. Register Page

**src/app/pages/register/register.page.ts:**
```typescript
import { Component } from '@angular/core';
import { Router } from '@angular/router';
import { AuthService } from '../../services/auth.service';
import { LoadingController, ToastController } from '@ionic/angular';

@Component({
  selector: 'app-register',
  templateUrl: './register.page.html',
  styleUrls: ['./register.page.scss'],
})
export class RegisterPage {
  name: string = '';
  email: string = '';
  password: string = '';
  passwordConfirmation: string = '';

  constructor(
    private authService: AuthService,
    private router: Router,
    private loadingController: LoadingController,
    private toastController: ToastController
  ) {}

  async register() {
    if (!this.name || !this.email || !this.password || !this.passwordConfirmation) {
      this.showToast('Please fill in all fields', 'warning');
      return;
    }

    if (this.password !== this.passwordConfirmation) {
      this.showToast('Passwords do not match', 'warning');
      return;
    }

    const loading = await this.loadingController.create({
      message: 'Creating account...',
    });
    await loading.present();

    this.authService.register(this.name, this.email, this.password, this.passwordConfirmation).subscribe({
      next: async (response) => {
        await loading.dismiss();
        if (response.success) {
          this.showToast('Account created successfully!', 'success');
          this.router.navigate(['/home']);
        }
      },
      error: async (error) => {
        await loading.dismiss();
        const message = error.error?.message || 'Registration failed';
        this.showToast(message, 'danger');
      }
    });
  }

  async showToast(message: string, color: string) {
    const toast = await this.toastController.create({
      message,
      duration: 2000,
      color,
      position: 'top'
    });
    toast.present();
  }

  goToLogin() {
    this.router.navigate(['/login']);
  }
}
```

### 3. Profile Page

**src/app/pages/profile/profile.page.ts:**
```typescript
import { Component, OnInit } from '@angular/core';
import { Router } from '@angular/router';
import { AuthService, User } from '../../services/auth.service';
import { LoadingController, ToastController, AlertController } from '@ionic/angular';

@Component({
  selector: 'app-profile',
  templateUrl: './profile.page.html',
  styleUrls: ['./profile.page.scss'],
})
export class ProfilePage implements OnInit {
  user: User | null = null;

  constructor(
    private authService: AuthService,
    private router: Router,
    private loadingController: LoadingController,
    private toastController: ToastController,
    private alertController: AlertController
  ) {}

  ngOnInit() {
    this.authService.currentUser$.subscribe(user => {
      this.user = user;
    });
    this.loadProfile();
  }

  async loadProfile() {
    const loading = await this.loadingController.create({
      message: 'Loading profile...',
    });
    await loading.present();

    this.authService.getProfile().subscribe({
      next: async () => {
        await loading.dismiss();
      },
      error: async (error) => {
        await loading.dismiss();
        this.showToast('Failed to load profile', 'danger');
      }
    });
  }

  async logout() {
    const alert = await this.alertController.create({
      header: 'Confirm Logout',
      message: 'Are you sure you want to logout?',
      buttons: [
        {
          text: 'Cancel',
          role: 'cancel'
        },
        {
          text: 'Logout',
          handler: async () => {
            const loading = await this.loadingController.create({
              message: 'Logging out...',
            });
            await loading.present();

            this.authService.logout().subscribe({
              next: async () => {
                await loading.dismiss();
                this.showToast('Logged out successfully', 'success');
                this.router.navigate(['/login']);
              },
              error: async (error) => {
                await loading.dismiss();
                this.showToast('Logout failed', 'danger');
              }
            });
          }
        }
      ]
    });

    await alert.present();
  }

  async showToast(message: string, color: string) {
    const toast = await this.toastController.create({
      message,
      duration: 2000,
      color,
      position: 'top'
    });
    toast.present();
  }
}
```

## 🛣️ Routing Configuration

**src/app/app-routing.module.ts:**
```typescript
import { NgModule } from '@angular/core';
import { PreloadAllModules, RouterModule, Routes } from '@angular/router';
import { AuthGuard } from './guards/auth.guard';

const routes: Routes = [
  {
    path: '',
    redirectTo: 'home',
    pathMatch: 'full'
  },
  {
    path: 'login',
    loadChildren: () => import('./pages/login/login.module').then(m => m.LoginPageModule)
  },
  {
    path: 'register',
    loadChildren: () => import('./pages/register/register.module').then(m => m.RegisterPageModule)
  },
  {
    path: 'forgot-password',
    loadChildren: () => import('./pages/forgot-password/forgot-password.module').then(m => m.ForgotPasswordPageModule)
  },
  {
    path: 'home',
    loadChildren: () => import('./pages/home/home.module').then(m => m.HomePageModule),
    canActivate: [AuthGuard]
  },
  {
    path: 'profile',
    loadChildren: () => import('./pages/profile/profile.module').then(m => m.ProfilePageModule),
    canActivate: [AuthGuard]
  }
];

@NgModule({
  imports: [
    RouterModule.forRoot(routes, { preloadingStrategy: PreloadAllModules })
  ],
  exports: [RouterModule]
})
export class AppRoutingModule { }
```

## 🔧 HTTP Interceptor (Optional)

Untuk automatic token injection dan error handling:

**src/app/interceptors/auth.interceptor.ts:**
```typescript
import { Injectable } from '@angular/core';
import { HttpRequest, HttpHandler, HttpEvent, HttpInterceptor, HttpErrorResponse } from '@angular/common/http';
import { Observable, from, throwError } from 'rxjs';
import { catchError, switchMap } from 'rxjs/operators';
import { AuthService } from '../services/auth.service';
import { Router } from '@angular/router';

@Injectable()
export class AuthInterceptor implements HttpInterceptor {
  
  constructor(
    private authService: AuthService,
    private router: Router
  ) {}

  intercept(request: HttpRequest<any>, next: HttpHandler): Observable<HttpEvent<any>> {
    return from(this.authService.getToken()).pipe(
      switchMap(token => {
        if (token) {
          request = request.clone({
            setHeaders: {
              Authorization: `Bearer ${token}`
            }
          });
        }
        
        return next.handle(request).pipe(
          catchError((error: HttpErrorResponse) => {
            if (error.status === 401) {
              // Token expired or invalid
              this.router.navigate(['/login']);
            }
            return throwError(() => error);
          })
        );
      })
    );
  }
}
```

Register di **app.module.ts:**
```typescript
import { HTTP_INTERCEPTORS } from '@angular/common/http';
import { AuthInterceptor } from './interceptors/auth.interceptor';

@NgModule({
  providers: [
    {
      provide: HTTP_INTERCEPTORS,
      useClass: AuthInterceptor,
      multi: true
    }
  ]
})
export class AppModule { }
```

## ✅ Testing

Untuk testing API dari development environment:

1. **Android**: Gunakan IP address komputer Anda (bukan localhost)
   ```typescript
   apiUrl: 'http://192.168.1.100:8000/api'
   ```

2. **iOS Simulator**: Bisa gunakan localhost
   ```typescript
   apiUrl: 'http://localhost:8000/api'
   ```

3. **Production**: Gunakan domain yang sebenarnya
   ```typescript
   apiUrl: 'https://yourdomain.com/api'
   ```

## 🚀 Build & Deploy

```bash
# Build for production
ionic build --prod

# Add platforms
ionic capacitor add android
ionic capacitor add ios

# Sync files
ionic capacitor sync

# Open in Android Studio / Xcode
ionic capacitor open android
ionic capacitor open ios
```

## 📝 Notes

1. **CORS**: Pastikan Laravel CORS sudah dikonfigurasi dengan benar
2. **HTTPS**: Untuk production, gunakan HTTPS
3. **Token Storage**: Gunakan SecureStorage untuk production
4. **Error Handling**: Implement proper error handling di semua request
5. **Loading States**: Selalu tampilkan loading indicator saat request
6. **Network Detection**: Handle offline scenarios

---

**Ready to integrate with your Ionic app!** 🎉
