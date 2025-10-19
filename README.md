# 🕌 SQ Backend - Hafalan Al-Quran Management API

[![Laravel](https://img.shields.io/badge/Laravel-11-red.svg)](https://laravel.com)
[![PHP](https://img.shields.io/badge/PHP-8.2+-blue.svg)](https://php.net)
[![License](https://img.shields.io/badge/License-MIT-green.svg)](LICENSE)

Backend API untuk sistem manajemen hafalan Al-Quran di sekolah/madrasah dengan Laravel 11, Laravel Sanctum, dan fitur production-ready.

## � Table of Contents

- [Features](#-features)
- [User Roles](#-user-roles)
- [Tech Stack](#-tech-stack)
- [Installation](#-installation)
- [Configuration](#-configuration)
- [API Documentation](#-api-documentation)
- [Testing](#-testing)
- [Project Structure](#-project-structure)
- [Security](#-security)
- [Contributing](#-contributing)
- [License](#-license)

## ✨ Features

### 🔐 Authentication & Authorization
- **JWT Authentication** via Laravel Sanctum
- **Role-Based Access Control (RBAC)** - 9 user roles with granular permissions
- **Multi-Device Login** - Token management per device
- **Password Reset** via email with secure tokens
- **Profile Management** - Update user information

### 📚 Hafalan Management
- **CRUD Operations** for Hafalan (Quran memorization records)
- **Student Management** - Complete student data with relationships
- **Teacher Management** - Teacher profiles with role assignments
- **Parent Management** - Parent/guardian information
- **Class Management** - Class organization and student grouping
- **Academic Year** - Flexible academic year management

### 🚀 Production-Ready Features
- ✅ **API Documentation** - Auto-generated with Scribe at `/api/v1/docs`
- ✅ **Response Caching** - Redis-ready caching for public endpoints
- ✅ **Rate Limiting** - Configurable per endpoint (60/10/200 req/min)
- ✅ **Request ID Tracking** - UUID for debugging and logging
- ✅ **Query Optimization** - Eager loading, no N+1 queries
- ✅ **Form Validation** - Request classes with Indonesian messages
- ✅ **API Resources** - Public & Protected data transformation
- ✅ **Security Headers** - XSS, CSRF, clickjacking protection
- ✅ **Audit Logging** - Security events with request tracking
- ✅ **Database Transactions** - ACID compliance for critical operations

## 👥 User Roles

The system supports **9 hierarchical user roles** with distinct permissions:

| Role | Description | Access Level |
|------|-------------|--------------|
| **super-admin** | Super Administrator | Full system access |
| **admin** | Administrator | Full data management |
| **tata-usaha** | Administrative Staff | Student/Teacher CRUD |
| **yayasan** | Foundation Management | Read-only monitoring |
| **kepala-sekolah** | School Principal | Academic monitoring |
| **wali-kelas** | Homeroom Teacher | Class & student management |
| **guru** | Teacher | Hafalan management |
| **siswa** | Student | View own records |
| **orang-tua** | Parent/Guardian | View child's records |

### Permission Matrix

| Resource | Create | Read | Update | Delete |
|----------|--------|------|--------|--------|
| **Guru** | Admin¹ | All² | Admin¹ | Admin¹ |
| **Siswa** | Admin¹ | All² | Admin¹ | Admin¹ |
| **Orang Tua** | Admin¹ | All² | Admin¹ | Admin¹ |
| **Hafalan** | Guru³ | All² | Guru³ | Guru³ |
| **Kelas** | Admin¹ | All² | Admin¹ | Admin¹ |

¹ Admin roles: `super-admin`, `admin`, `tata-usaha`  
² All authenticated users with base roles  
³ Guru roles: `guru`, `wali-kelas`, `kepala-sekolah`

**📖 Documentation:**
- `ROLE_SYSTEM.md` - Complete role system documentation
- `AUTHORIZATION_RBAC.md` - RBAC implementation details

## 💻 Tech Stack

- **Framework:** Laravel 11
- **Authentication:** Laravel Sanctum
- **Database:** MySQL 8.0+
- **PHP Version:** 8.2+
- **Cache:** Redis (optional)
- **Queue:** Redis/Database (optional)
- **Mail:** SMTP

## 📦 Installation

### Prerequisites

- PHP >= 8.2
- Composer
- MySQL >= 8.0
- Node.js & NPM (for frontend assets)

### Steps

1. **Clone the repository**
   ```bash
   git clone https://github.com/yourusername/sq-backend.git
   cd sq-backend
   ```

2. **Install dependencies**
   ```bash
   composer install
   ```

3. **Environment setup**
   ```bash
   cp .env.example .env
   php artisan key:generate
   ```

4. **Configure database**
   
   Edit `.env` file:
   ```env
   DB_CONNECTION=mysql
   DB_HOST=127.0.0.1
   DB_PORT=3306
   DB_DATABASE=your_database_name
   DB_USERNAME=your_database_user
   DB_PASSWORD=your_database_password
   ```

5. **Configure mail** (for password reset)
   
   Edit `.env` file:
   ```env
   MAIL_MAILER=smtp
   MAIL_HOST=your_mail_host
   MAIL_PORT=587
   MAIL_USERNAME=your_email@example.com
   MAIL_PASSWORD=your_email_password
   MAIL_ENCRYPTION=tls
   MAIL_FROM_ADDRESS="noreply@example.com"
   MAIL_FROM_NAME="${APP_NAME}"
   ```

6. **Run migrations and seeders**
   ```bash
   php artisan migrate:fresh --seed
   ```

7. **Start development server**
   ```bash
   php artisan serve
   ```

   Server will run at: `http://127.0.0.1:8000`

## ⚙️ Configuration

### API Versioning

Configure in `config/api.php`:
```php
'version' => env('API_VERSION', 'v1'),
```

### Rate Limiting

Configure in `config/api.php`:
```php
'rate_limit' => [
    'auth' => env('API_RATE_LIMIT_AUTH', 10),      // Login/register
    'public' => env('API_RATE_LIMIT_PUBLIC', 60),  // Public endpoints
    'protected' => env('API_RATE_LIMIT_PROTECTED', 200), // Auth required
],
```

### Response Caching

Configure in `config/api.php`:
```php
'cache' => [
    'public_endpoints' => env('API_CACHE_PUBLIC', 30), // Minutes
],
```

## � API Documentation

### Base URL
```
Development: http://localhost:8000/api/v1
Production: https://your-domain.com/api/v1
```

### Auto-Generated Documentation
Visit `/api/v1/docs` for interactive API documentation generated by Scribe.

### Quick Start

#### 1. Authentication

**Login**
```bash
POST /api/v1/auth/login
Content-Type: application/json

{
  "username": "your_username",
  "password": "your_password"
}
```

**Response:**
```json
{
  "success": true,
  "message": "Login berhasil",
  "data": {
    "user": {
      "id": 1,
      "username": "john_doe",
      "email": "john@example.com",
      "role": "admin"
    },
    "token": "1|xxxxxxxxxxxxxxxxxxxxx"
  }
}
```

#### 2. Using the API

All protected endpoints require the Bearer token:

```bash
GET /api/v1/siswa
Authorization: Bearer {your_token}
Accept: application/json
```

### Main Endpoints

| Method | Endpoint | Description | Auth | Role Required |
|--------|----------|-------------|------|---------------|
| **Authentication** |
| POST | `/auth/login` | User login | No | - |
| POST | `/auth/logout` | User logout | Yes | All |
| GET | `/auth/profile` | Get user profile | Yes | All |
| PUT | `/auth/profile` | Update profile | Yes | All |
| **Students** |
| GET | `/siswa` | List students | Yes | All |
| POST | `/siswa` | Create student | Yes | Admin¹ |
| GET | `/siswa/{id}` | Get student detail | Yes | All |
| PUT | `/siswa/{id}` | Update student | Yes | Admin¹ |
| DELETE | `/siswa/{id}` | Delete student | Yes | Admin¹ |
| **Teachers** |
| GET | `/guru` | List teachers | Yes | All |
| POST | `/guru` | Create teacher | Yes | Admin¹ |
| GET | `/guru/{id}` | Get teacher detail | Yes | All |
| PUT | `/guru/{id}` | Update teacher | Yes | Admin¹ |
| DELETE | `/guru/{id}` | Delete teacher | Yes | Admin¹ |
| **Hafalan** |
| GET | `/hafalan` | List hafalan records | Yes | Guru² |
| POST | `/hafalan` | Create hafalan | Yes | Guru² |
| GET | `/hafalan/{id}` | Get hafalan detail | Yes | Guru² |
| PUT | `/hafalan/{id}` | Update hafalan | Yes | Guru² |
| DELETE | `/hafalan/{id}` | Delete hafalan | Yes | Guru² |

¹ Admin roles: `super-admin`, `admin`, `tata-usaha`  
² Guru roles: `guru`, `wali-kelas`, `kepala-sekolah`, `admin`, `super-admin`

### Response Format

**Success Response:**
```json
{
  "success": true,
  "message": "Operation successful",
  "data": { /* response data */ }
}
```

**Error Response:**
```json
{
  "success": false,
  "message": "Error description",
  "errors": { /* validation errors if any */ }
}
```

### HTTP Status Codes

| Code | Description |
|------|-------------|
| 200 | Success |
| 201 | Created |
| 400 | Bad Request |
| 401 | Unauthorized |
| 403 | Forbidden |
| 404 | Not Found |
| 422 | Validation Error |
| 429 | Too Many Requests |
| 500 | Server Error |

## 🧪 Testing

### Comprehensive Test Suite

The application includes **38 automated tests** covering all major features:

```bash
# Run all tests
php run_all_tests.php

# Or run individual test files
php test_authentication.php
php test_api_errors.php
php test_validation.php
```

### Test Coverage

| Test File | Tests | Coverage |
|-----------|-------|----------|
| `test_authentication.php` | 7 | Auth & authorization |
| `test_api_errors.php` | 5 | Error responses |
| `test_caching.php` | 5 | Response caching |
| `test_resources.php` | 5 | Data transformation |
| `test_validation.php` | 7 | Form validation |
| `test_rate_limiting.php` | 2 | Rate limiting |
| `test_security_headers.php` | 6 | Security headers |
| `test_n1_problem.php` | 1 | Query optimization |

**Result:** ✅ 38/38 tests passing (100%)

### Interactive Testing Tools

HTML-based testing tools for manual testing:

- `public/test-guru.html` - Test Guru API endpoints
- `public/test-orang-tua.html` - Test Parent API endpoints
- `public/test-authorization.html` - Test RBAC permissions

Visit: `http://localhost:8000/test-authorization.html`

### Using Postman

Import `postman_collection.json` to Postman for easy API testing with pre-configured requests.

## 📁 Project Structure

```
sq-backend/
├── app/
│   ├── Http/
│   │   ├── Controllers/        # API Controllers
│   │   ├── Middleware/         # Custom middleware (RBAC, etc)
│   │   ├── Requests/          # Form validation requests
│   │   └── Resources/         # API response transformers
│   ├── Models/                # Eloquent models
│   └── Policies/              # Authorization policies
├── database/
│   ├── migrations/            # Database migrations
│   ├── seeders/               # Database seeders
│   └── factories/             # Model factories
├── routes/
│   ├── api.php                # API routes (versioned)
│   └── web.php                # Web routes
├── config/
│   ├── api.php                # API configuration
│   ├── cors.php               # CORS configuration
│   └── sanctum.php            # Authentication config
├── public/
│   ├── test-guru.html         # Testing tools
│   └── test-authorization.html
├── docs/                      # Documentation
│   ├── AUTHORIZATION_RBAC.md
│   ├── GURU_IMPLEMENTATION.md
│   └── TESTING_GUIDE.md
└── tests/                     # PHPUnit tests
```

## � Useful Commands

### Development

```bash
# Start development server
php artisan serve

# Run migrations
php artisan migrate

# Seed database with sample data
php artisan db:seed

# Fresh migration with seeding
php artisan migrate:fresh --seed
```

### Cache & Optimization

```bash
# Clear all caches
php artisan optimize:clear

# Optimize for production
php artisan optimize
php artisan config:cache
php artisan route:cache
php artisan view:cache
```

### Database

```bash
# Create new migration
php artisan make:migration create_table_name

# Create new seeder
php artisan make:seeder TableSeeder

# Create model with migration and controller
php artisan make:model ModelName -mcr
```

### Code Quality

```bash
# View all routes
php artisan route:list

# Check for errors
php artisan route:list --path=api/v1

# Generate API documentation
php artisan scribe:generate
```

## � Security

### Implemented Security Features

- ✅ **CSRF Protection** - Laravel's built-in CSRF tokens
- ✅ **SQL Injection Prevention** - Eloquent ORM with parameter binding
- ✅ **XSS Protection** - Output escaping and security headers
- ✅ **Rate Limiting** - Configurable per endpoint
- ✅ **Password Hashing** - Bcrypt with salt
- ✅ **API Authentication** - Laravel Sanctum tokens
- ✅ **Role-Based Access Control** - Middleware-based RBAC
- ✅ **Security Headers** - XSS, clickjacking, MIME sniffing protection
- ✅ **Input Validation** - Request validation classes
- ✅ **Database Transactions** - ACID compliance

### Best Practices for Production

1. **Environment Variables**
   - Never commit `.env` file
   - Use strong `APP_KEY`
   - Set `APP_DEBUG=false` in production

2. **HTTPS**
   - Always use HTTPS in production
   - Configure `SANCTUM_STATEFUL_DOMAINS`

3. **Database**
   - Use strong database passwords
   - Implement database backups
   - Monitor slow queries

4. **Monitoring**
   - Set up error tracking (Sentry, Bugsnag)
   - Monitor server resources
   - Track API usage and errors

5. **Updates**
   - Keep Laravel and dependencies updated
   - Review security advisories regularly

## 🚀 Deployment

### Requirements for Production

- PHP >= 8.2 with extensions: BCMath, Ctype, JSON, Mbstring, OpenSSL, PDO, Tokenizer, XML
- MySQL >= 8.0 or PostgreSQL >= 12
- Nginx or Apache with mod_rewrite
- Composer 2.x
- Redis (recommended for cache and queues)

### Deployment Steps

1. **Clone and install**
   ```bash
   git clone https://github.com/yourusername/sq-backend.git
   cd sq-backend
   composer install --no-dev --optimize-autoloader
   ```

2. **Configure environment**
   ```bash
   cp .env.example .env
   php artisan key:generate
   # Edit .env with production settings
   ```

3. **Setup database**
   ```bash
   php artisan migrate --force
   php artisan db:seed --force
   ```

4. **Optimize**
   ```bash
   php artisan config:cache
   php artisan route:cache
   php artisan view:cache
   ```

5. **Set permissions**
   ```bash
   chmod -R 755 storage bootstrap/cache
   chown -R www-data:www-data storage bootstrap/cache
   ```

### Nginx Configuration Example

```nginx
server {
    listen 80;
    server_name your-domain.com;
    root /var/www/sq-backend/public;

    add_header X-Frame-Options "SAMEORIGIN";
    add_header X-Content-Type-Options "nosniff";

    index index.php;

    charset utf-8;

    location / {
        try_files $uri $uri/ /index.php?$query_string;
    }

    location ~ \.php$ {
        fastcgi_pass unix:/var/run/php/php8.2-fpm.sock;
        fastcgi_param SCRIPT_FILENAME $realpath_root$fastcgi_script_name;
        include fastcgi_params;
    }

    location ~ /\.(?!well-known).* {
        deny all;
    }
}
```

## 📚 Additional Documentation

- **API Documentation:** `/api/v1/docs` (auto-generated)
- **Role System:** `docs/ROLE_SYSTEM.md`
- **Authorization:** `docs/AUTHORIZATION_RBAC.md`
- **Testing Guide:** `docs/TESTING_GUIDE.md`
- **Guru API:** `docs/GURU_IMPLEMENTATION.md`
- **Orang Tua API:** `docs/ORANG_TUA_IMPLEMENTATION.md`

## 🤝 Contributing

Contributions are welcome! Please follow these guidelines:

1. Fork the repository
2. Create a feature branch (`git checkout -b feature/AmazingFeature`)
3. Commit your changes (`git commit -m 'Add some AmazingFeature'`)
4. Push to the branch (`git push origin feature/AmazingFeature`)
5. Open a Pull Request

### Coding Standards

- Follow [PSR-12](https://www.php-fig.org/psr/psr-12/) coding standards
- Write meaningful commit messages
- Add tests for new features
- Update documentation as needed

## 📄 License

This project is licensed under the MIT License - see the [LICENSE](LICENSE) file for details.

## 👨‍💻 Authors

- **Your Name** - *Initial work* - [YourGitHub](https://github.com/yourusername)

## � Acknowledgments

- Laravel Framework team
- Contributors and community
- Islamic educational institutions using this system

## 📞 Support

For support, email support@example.com or open an issue in the repository.

## 🔗 Links

- **Documentation:** [Full Documentation](https://your-docs-url.com)
- **API Docs:** [API Documentation](https://your-api-url.com/api/v1/docs)
- **Report Bug:** [Issue Tracker](https://github.com/yourusername/sq-backend/issues)
- **Request Feature:** [Feature Requests](https://github.com/yourusername/sq-backend/issues)

---

**Built with ❤️ using Laravel 11 & Laravel Sanctum**

**For Islamic Education 🕌**
