# Integration Guide: Auth Profile for Mobile App (Ionic/Angular)

## Service Implementation

### 1. Update User Model (`src/app/core/models/user.model.ts`)

```typescript
export interface User {
  id: number;
  name: string;
  username: string;
  email: string;
  email_verified_at: string | null;
  role: UserRole;
  is_active: boolean;
  created_at: string;
  updated_at: string;
  
  // Role-specific data
  siswa?: SiswaProfile;
  guru?: GuruProfile;
  orang_tua?: OrangTuaProfile;
}

export interface SiswaProfile {
  id: number;
  nis: string;
  nama: string;
  jenis_kelamin: 'L' | 'P';
  jenis_kelamin_text: string;
  tempat_lahir: string;
  tanggal_lahir: string;
  alamat: string;
  no_hp: string;
  tahun_masuk: string;
  url_photo: string | null;
  url_cover: string | null;
  is_active: boolean;
  kelas?: KelasInfo;
}

export interface GuruProfile {
  id: number;
  nip: string;
  nama: string;
  jenis_kelamin: 'L' | 'P';
  jenis_kelamin_text: string;
  tempat_lahir: string;
  tanggal_lahir: string;
  alamat: string;
  no_hp: string;
  url_photo: string | null;
  url_cover: string | null;
  is_active: boolean;
}

export interface OrangTuaProfile {
  id: number;
  nama: string;
  jenis_kelamin: 'L' | 'P';
  jenis_kelamin_text: string;
  tempat_lahir: string;
  tanggal_lahir: string;
  alamat: string;
  no_hp: string;
  pendidikan: string;
  pekerjaan: string;
  penghasilan: string;
  penghasilan_formatted: string;
  url_photo: string | null;
  url_cover: string | null;
  is_active: boolean;
}

export interface KelasInfo {
  id: number;
  nama: string;
  ruangan: string;
  tingkat: number;
}

export type UserRole = 
  | 'siswa' 
  | 'orang-tua' 
  | 'guru' 
  | 'wali-kelas' 
  | 'kepala-sekolah' 
  | 'tata-usaha' 
  | 'yayasan' 
  | 'admin' 
  | 'super-admin';
```

### 2. Update Auth Service (`src/app/core/services/auth.service.ts`)

```typescript
import { Injectable } from '@angular/core';
import { HttpClient } from '@angular/common/http';
import { BehaviorSubject, Observable, tap } from 'rxjs';
import { User } from '../models/user.model';
import { environment } from '../../../environments/environment';

interface ApiResponse<T> {
  success: boolean;
  message: string;
  data: T;
}

interface LoginResponse {
  user: User;
  access_token: string;
  token_type: string;
  expires_at: string | null;
}

@Injectable({
  providedIn: 'root'
})
export class AuthService {
  private apiUrl = environment.apiUrl;
  private currentUserSubject = new BehaviorSubject<User | null>(null);
  public currentUser$ = this.currentUserSubject.asObservable();

  constructor(private http: HttpClient) {
    this.loadStoredUser();
  }

  /**
   * Login user
   */
  login(email: string, password: string): Observable<ApiResponse<LoginResponse>> {
    return this.http.post<ApiResponse<LoginResponse>>(
      `${this.apiUrl}/auth/login`,
      { email, password }
    ).pipe(
      tap(response => {
        if (response.success) {
          this.setToken(response.data.access_token);
          this.setUser(response.data.user);
        }
      })
    );
  }

  /**
   * Get current user profile
   */
  getProfile(): Observable<ApiResponse<User>> {
    return this.http.get<ApiResponse<User>>(
      `${this.apiUrl}/auth/profile`
    ).pipe(
      tap(response => {
        if (response.success) {
          this.setUser(response.data);
        }
      })
    );
  }

  /**
   * Update user profile
   */
  updateProfile(data: {
    name?: string;
    email?: string;
    current_password?: string;
    password?: string;
    password_confirmation?: string;
  }): Observable<ApiResponse<User>> {
    return this.http.put<ApiResponse<User>>(
      `${this.apiUrl}/auth/profile`,
      data
    ).pipe(
      tap(response => {
        if (response.success) {
          this.setUser(response.data);
        }
      })
    );
  }

  /**
   * Logout user
   */
  logout(): Observable<ApiResponse<any>> {
    return this.http.post<ApiResponse<any>>(
      `${this.apiUrl}/auth/logout`,
      {}
    ).pipe(
      tap(() => {
        this.clearAuth();
      })
    );
  }

  /**
   * Get current user value
   */
  getCurrentUser(): User | null {
    return this.currentUserSubject.value;
  }

  /**
   * Check if user is authenticated
   */
  isAuthenticated(): boolean {
    return !!this.getToken() && !!this.getCurrentUser();
  }

  /**
   * Check if user has specific role
   */
  hasRole(role: string): boolean {
    const user = this.getCurrentUser();
    return user?.role === role;
  }

  /**
   * Check if user has any of the given roles
   */
  hasAnyRole(roles: string[]): boolean {
    const user = this.getCurrentUser();
    return user ? roles.includes(user.role) : false;
  }

  /**
   * Get display name based on role
   */
  getDisplayName(): string {
    const user = this.getCurrentUser();
    if (!user) return '';

    // Priority: nama dari profile role-specific > name dari user
    if (user.siswa?.nama) return user.siswa.nama;
    if (user.guru?.nama) return user.guru.nama;
    if (user.orang_tua?.nama) return user.orang_tua.nama;
    
    return user.name;
  }

  /**
   * Get profile photo URL
   */
  getProfilePhoto(): string | null {
    const user = this.getCurrentUser();
    if (!user) return null;

    if (user.siswa?.url_photo) return user.siswa.url_photo;
    if (user.guru?.url_photo) return user.guru.url_photo;
    if (user.orang_tua?.url_photo) return user.orang_tua.url_photo;
    
    return null;
  }

  /**
   * Get cover photo URL
   */
  getCoverPhoto(): string | null {
    const user = this.getCurrentUser();
    if (!user) return null;

    if (user.siswa?.url_cover) return user.siswa.url_cover;
    if (user.guru?.url_cover) return user.guru.url_cover;
    if (user.orang_tua?.url_cover) return user.orang_tua.url_cover;
    
    return null;
  }

  // Private helper methods
  private setToken(token: string): void {
    localStorage.setItem('access_token', token);
  }

  private getToken(): string | null {
    return localStorage.getItem('access_token');
  }

  private setUser(user: User): void {
    localStorage.setItem('current_user', JSON.stringify(user));
    this.currentUserSubject.next(user);
  }

  private loadStoredUser(): void {
    const userJson = localStorage.getItem('current_user');
    if (userJson) {
      try {
        const user = JSON.parse(userJson);
        this.currentUserSubject.next(user);
      } catch (error) {
        console.error('Error parsing stored user:', error);
        this.clearAuth();
      }
    }
  }

  private clearAuth(): void {
    localStorage.removeItem('access_token');
    localStorage.removeItem('current_user');
    this.currentUserSubject.next(null);
  }
}
```

### 3. Profile Page Component (`src/app/pages/profil/profil.page.ts`)

```typescript
import { Component, OnInit } from '@angular/core';
import { AuthService } from '../../core/services/auth.service';
import { User } from '../../core/models/user.model';

@Component({
  selector: 'app-profil',
  templateUrl: './profil.page.html',
  styleUrls: ['./profil.page.scss'],
})
export class ProfilPage implements OnInit {
  user: User | null = null;
  loading = false;
  error: string | null = null;

  constructor(private authService: AuthService) {}

  ngOnInit() {
    this.loadProfile();
  }

  loadProfile() {
    this.loading = true;
    this.error = null;

    this.authService.getProfile().subscribe({
      next: (response) => {
        this.user = response.data;
        this.loading = false;
      },
      error: (error) => {
        console.error('Error loading profile:', error);
        this.error = 'Gagal memuat profil';
        this.loading = false;
      }
    });
  }

  getDisplayName(): string {
    return this.authService.getDisplayName();
  }

  getProfilePhoto(): string {
    return this.authService.getProfilePhoto() || 'assets/images/default-avatar.png';
  }

  getCoverPhoto(): string {
    return this.authService.getCoverPhoto() || 'assets/images/default-cover.jpg';
  }

  getRoleText(role: string): string {
    const roleMap: { [key: string]: string } = {
      'siswa': 'Siswa',
      'orang-tua': 'Orang Tua',
      'guru': 'Guru',
      'wali-kelas': 'Wali Kelas',
      'kepala-sekolah': 'Kepala Sekolah',
      'tata-usaha': 'Tata Usaha',
      'yayasan': 'Yayasan',
      'admin': 'Admin',
      'super-admin': 'Super Admin'
    };
    return roleMap[role] || role;
  }

  logout() {
    this.authService.logout().subscribe({
      next: () => {
        // Navigate to login page
        // this.router.navigate(['/login']);
      },
      error: (error) => {
        console.error('Logout error:', error);
      }
    });
  }
}
```

### 4. Profile Page Template (`src/app/pages/profil/profil.page.html`)

```html
<ion-header>
  <ion-toolbar>
    <ion-title>Profil</ion-title>
  </ion-toolbar>
</ion-header>

<ion-content>
  <!-- Loading State -->
  <div *ngIf="loading" class="loading-container">
    <ion-spinner></ion-spinner>
    <p>Memuat profil...</p>
  </div>

  <!-- Error State -->
  <ion-card *ngIf="error && !loading" color="danger">
    <ion-card-content>
      {{ error }}
      <ion-button expand="block" fill="outline" (click)="loadProfile()">
        Coba Lagi
      </ion-button>
    </ion-card-content>
  </ion-card>

  <!-- Profile Content -->
  <div *ngIf="user && !loading">
    <!-- Cover Photo -->
    <div class="cover-photo" [style.background-image]="'url(' + getCoverPhoto() + ')'">
      <div class="cover-overlay"></div>
    </div>

    <!-- Profile Photo & Name -->
    <div class="profile-header">
      <ion-avatar class="profile-avatar">
        <img [src]="getProfilePhoto()" [alt]="getDisplayName()">
      </ion-avatar>
      <h2>{{ getDisplayName() }}</h2>
      <ion-badge [color]="user.is_active ? 'success' : 'danger'">
        {{ getRoleText(user.role) }}
      </ion-badge>
    </div>

    <!-- User Basic Info -->
    <ion-card>
      <ion-card-header>
        <ion-card-title>Informasi Akun</ion-card-title>
      </ion-card-header>
      <ion-card-content>
        <ion-list lines="none">
          <ion-item>
            <ion-icon name="person-outline" slot="start"></ion-icon>
            <ion-label>
              <p>Username</p>
              <h3>{{ user.username }}</h3>
            </ion-label>
          </ion-item>
          <ion-item>
            <ion-icon name="mail-outline" slot="start"></ion-icon>
            <ion-label>
              <p>Email</p>
              <h3>{{ user.email }}</h3>
            </ion-label>
          </ion-item>
        </ion-list>
      </ion-card-content>
    </ion-card>

    <!-- Siswa Specific Info -->
    <ion-card *ngIf="user.siswa">
      <ion-card-header>
        <ion-card-title>Data Siswa</ion-card-title>
      </ion-card-header>
      <ion-card-content>
        <ion-list lines="none">
          <ion-item>
            <ion-icon name="card-outline" slot="start"></ion-icon>
            <ion-label>
              <p>NIS</p>
              <h3>{{ user.siswa.nis }}</h3>
            </ion-label>
          </ion-item>
          <ion-item *ngIf="user.siswa.kelas">
            <ion-icon name="school-outline" slot="start"></ion-icon>
            <ion-label>
              <p>Kelas</p>
              <h3>{{ user.siswa.kelas.nama }} - {{ user.siswa.kelas.ruangan }}</h3>
            </ion-label>
          </ion-item>
          <ion-item>
            <ion-icon name="transgender-outline" slot="start"></ion-icon>
            <ion-label>
              <p>Jenis Kelamin</p>
              <h3>{{ user.siswa.jenis_kelamin_text }}</h3>
            </ion-label>
          </ion-item>
          <ion-item>
            <ion-icon name="location-outline" slot="start"></ion-icon>
            <ion-label>
              <p>Tempat Lahir</p>
              <h3>{{ user.siswa.tempat_lahir }}</h3>
            </ion-label>
          </ion-item>
          <ion-item>
            <ion-icon name="calendar-outline" slot="start"></ion-icon>
            <ion-label>
              <p>Tanggal Lahir</p>
              <h3>{{ user.siswa.tanggal_lahir }}</h3>
            </ion-label>
          </ion-item>
          <ion-item>
            <ion-icon name="call-outline" slot="start"></ion-icon>
            <ion-label>
              <p>No. HP</p>
              <h3>{{ user.siswa.no_hp || '-' }}</h3>
            </ion-label>
          </ion-item>
          <ion-item>
            <ion-icon name="home-outline" slot="start"></ion-icon>
            <ion-label>
              <p>Alamat</p>
              <h3>{{ user.siswa.alamat || '-' }}</h3>
            </ion-label>
          </ion-item>
        </ion-list>
      </ion-card-content>
    </ion-card>

    <!-- Guru Specific Info -->
    <ion-card *ngIf="user.guru">
      <ion-card-header>
        <ion-card-title>Data Guru</ion-card-title>
      </ion-card-header>
      <ion-card-content>
        <ion-list lines="none">
          <ion-item>
            <ion-icon name="card-outline" slot="start"></ion-icon>
            <ion-label>
              <p>NIP</p>
              <h3>{{ user.guru.nip }}</h3>
            </ion-label>
          </ion-item>
          <ion-item>
            <ion-icon name="transgender-outline" slot="start"></ion-icon>
            <ion-label>
              <p>Jenis Kelamin</p>
              <h3>{{ user.guru.jenis_kelamin_text }}</h3>
            </ion-label>
          </ion-item>
          <ion-item>
            <ion-icon name="call-outline" slot="start"></ion-icon>
            <ion-label>
              <p>No. HP</p>
              <h3>{{ user.guru.no_hp || '-' }}</h3>
            </ion-label>
          </ion-item>
          <ion-item>
            <ion-icon name="home-outline" slot="start"></ion-icon>
            <ion-label>
              <p>Alamat</p>
              <h3>{{ user.guru.alamat || '-' }}</h3>
            </ion-label>
          </ion-item>
        </ion-list>
      </ion-card-content>
    </ion-card>

    <!-- Orang Tua Specific Info -->
    <ion-card *ngIf="user.orang_tua">
      <ion-card-header>
        <ion-card-title>Data Orang Tua</ion-card-title>
      </ion-card-header>
      <ion-card-content>
        <ion-list lines="none">
          <ion-item>
            <ion-icon name="transgender-outline" slot="start"></ion-icon>
            <ion-label>
              <p>Jenis Kelamin</p>
              <h3>{{ user.orang_tua.jenis_kelamin_text }}</h3>
            </ion-label>
          </ion-item>
          <ion-item>
            <ion-icon name="school-outline" slot="start"></ion-icon>
            <ion-label>
              <p>Pendidikan</p>
              <h3>{{ user.orang_tua.pendidikan || '-' }}</h3>
            </ion-label>
          </ion-item>
          <ion-item>
            <ion-icon name="briefcase-outline" slot="start"></ion-icon>
            <ion-label>
              <p>Pekerjaan</p>
              <h3>{{ user.orang_tua.pekerjaan || '-' }}</h3>
            </ion-label>
          </ion-item>
          <ion-item>
            <ion-icon name="cash-outline" slot="start"></ion-icon>
            <ion-label>
              <p>Penghasilan</p>
              <h3>{{ user.orang_tua.penghasilan_formatted || '-' }}</h3>
            </ion-label>
          </ion-item>
          <ion-item>
            <ion-icon name="call-outline" slot="start"></ion-icon>
            <ion-label>
              <p>No. HP</p>
              <h3>{{ user.orang_tua.no_hp || '-' }}</h3>
            </ion-label>
          </ion-item>
          <ion-item>
            <ion-icon name="home-outline" slot="start"></ion-icon>
            <ion-label>
              <p>Alamat</p>
              <h3>{{ user.orang_tua.alamat || '-' }}</h3>
            </ion-label>
          </ion-item>
        </ion-list>
      </ion-card-content>
    </ion-card>

    <!-- Action Buttons -->
    <ion-card>
      <ion-card-content>
        <ion-button expand="block" fill="outline" color="primary">
          <ion-icon name="create-outline" slot="start"></ion-icon>
          Edit Profil
        </ion-button>
        <ion-button expand="block" fill="outline" color="medium">
          <ion-icon name="key-outline" slot="start"></ion-icon>
          Ubah Password
        </ion-button>
        <ion-button expand="block" fill="solid" color="danger" (click)="logout()">
          <ion-icon name="log-out-outline" slot="start"></ion-icon>
          Logout
        </ion-button>
      </ion-card-content>
    </ion-card>
  </div>
</ion-content>
```

### 5. Profile Page Styles (`src/app/pages/profil/profil.page.scss`)

```scss
.loading-container {
  display: flex;
  flex-direction: column;
  align-items: center;
  justify-content: center;
  height: 100%;
  gap: 1rem;
}

.cover-photo {
  height: 200px;
  background-size: cover;
  background-position: center;
  position: relative;

  .cover-overlay {
    position: absolute;
    top: 0;
    left: 0;
    right: 0;
    bottom: 0;
    background: linear-gradient(to bottom, transparent, rgba(0,0,0,0.5));
  }
}

.profile-header {
  text-align: center;
  margin-top: -60px;
  padding: 0 1rem 1rem;

  .profile-avatar {
    width: 120px;
    height: 120px;
    margin: 0 auto 1rem;
    border: 4px solid var(--ion-background-color);
    box-shadow: 0 4px 12px rgba(0,0,0,0.15);
  }

  h2 {
    margin: 0.5rem 0;
    font-size: 1.5rem;
    font-weight: 600;
  }

  ion-badge {
    margin-top: 0.5rem;
  }
}

ion-card {
  margin: 1rem;

  ion-list {
    padding: 0;
    
    ion-item {
      --padding-start: 0;
      --inner-padding-end: 0;
      
      ion-label {
        p {
          color: var(--ion-color-medium);
          font-size: 0.875rem;
          margin-bottom: 0.25rem;
        }
        
        h3 {
          color: var(--ion-color-dark);
          font-weight: 500;
        }
      }
    }
  }
}
```

## Usage Example

```typescript
// In any component
constructor(private authService: AuthService) {}

ngOnInit() {
  // Subscribe to current user
  this.authService.currentUser$.subscribe(user => {
    console.log('Current user:', user);
    
    if (user) {
      // Access role-specific data
      if (user.siswa) {
        console.log('Siswa NIS:', user.siswa.nis);
        console.log('Kelas:', user.siswa.kelas?.nama);
      }
    }
  });
  
  // Or get current user value
  const user = this.authService.getCurrentUser();
  if (user?.siswa) {
    // Do something with siswa data
  }
}
```

## Notes

1. Update `environment.ts` dengan URL API yang benar
2. Pastikan interceptor sudah menambahkan token ke setiap request
3. Handle token expiration dengan proper error handling
4. Implement refresh mechanism jika diperlukan
5. Add proper error handling untuk network failures
