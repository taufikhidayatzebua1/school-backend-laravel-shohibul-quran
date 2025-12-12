# 📋 Requirements - SQ Hafalan Backend

## System Requirements

### Server Requirements
- **PHP:** >= 8.2
- **Database:** MySQL 8.0+ / PostgreSQL 15+ / SQLite 3.35+
- **Web Server:** Apache 2.4+ / Nginx 1.18+
- **Redis:** >= 6.0 (optional, for caching & queues)
- **Composer:** >= 2.5
- **Node.js:** >= 20.x (for Vite assets)
- **NPM:** >= 10.x

### PHP Extensions
Required PHP extensions:
- OpenSSL
- PDO (with MySQL/PostgreSQL driver)
- Mbstring
- Tokenizer
- XML
- Ctype
- JSON
- BCMath
- Fileinfo
- GD or Imagick (for image processing)
- Redis (optional)

## Dependencies

### Backend Dependencies (Composer)

#### Core Dependencies
```json
{
  "php": "^8.2",
  "laravel/framework": "^12.0",
  "laravel/sanctum": "^4.0",
  "laravel/tinker": "^2.10.1"
}
```

#### Development Dependencies
```json
{
  "fakerphp/faker": "^1.23",
  "knuckleswtf/scribe": "^5.3",
  "laravel/pail": "^1.2.2",
  "laravel/pint": "^1.24",
  "laravel/sail": "^1.41",
  "mockery/mockery": "^1.6",
  "nunomaduro/collision": "^8.6",
  "phpunit/phpunit": "^11.5.3"
}
```

### Frontend Dependencies (NPM)

```json
{
  "@tailwindcss/vite": "^4.0.0",
  "axios": "^1.11.0",
  "concurrently": "^9.0.1",
  "laravel-vite-plugin": "^2.0.0",
  "tailwindcss": "^4.0.0",
  "vite": "^7.0.7"
}
```

## Installation Requirements

### 1. Environment Setup
- Copy `.env.example` to `.env`
- Configure database credentials
- Set `APP_KEY` (auto-generated with `php artisan key:generate`)
- Configure mail settings for password reset

### 2. Database
- Create MySQL/PostgreSQL database
- Run migrations: `php artisan migrate`
- (Optional) Seed data: `php artisan db:seed`

### 3. File Permissions
Laravel requires write permissions for:
- `storage/` directory and subdirectories
- `bootstrap/cache/` directory

**Linux/Mac:**
```bash
chmod -R 775 storage bootstrap/cache
chown -R www-data:www-data storage bootstrap/cache
```

**Windows:**
Ensure the web server user has write access to these directories.

### 4. Queue Configuration (Optional)
For background jobs:
- Configure queue driver in `.env` (database, redis, etc.)
- Run queue worker: `php artisan queue:work`

### 5. Cache Configuration (Optional)
For improved performance:
- Install Redis server
- Set `CACHE_DRIVER=redis` in `.env`
- Set `QUEUE_CONNECTION=redis` in `.env`

## Production Requirements

### Security
- ✅ HTTPS/SSL certificate required
- ✅ Configure CORS in `config/cors.php`
- ✅ Set `APP_DEBUG=false` in production
- ✅ Use strong `APP_KEY`
- ✅ Configure rate limiting
- ✅ Enable security headers

### Performance
- ✅ Enable OPcache for PHP
- ✅ Use Redis for cache and sessions
- ✅ Configure Laravel Horizon for queues (optional)
- ✅ Optimize autoloader: `composer install --optimize-autoloader --no-dev`
- ✅ Cache configuration: `php artisan config:cache`
- ✅ Cache routes: `php artisan route:cache`
- ✅ Cache views: `php artisan view:cache`

### Monitoring
- ✅ Configure logging in `config/logging.php`
- ✅ Set up error tracking (e.g., Sentry)
- ✅ Monitor server resources (CPU, RAM, disk)
- ✅ Database query monitoring

## Development Tools

### Recommended Tools
- **IDE:** PHPStorm, VS Code with PHP extensions
- **Database Client:** TablePlus, phpMyAdmin, Adminer
- **API Testing:** Postman, Insomnia, Thunder Client
- **Version Control:** Git
- **Local Environment:** Laravel Sail, Laragon, XAMPP, Docker

### Testing
```bash
# Run all tests
php artisan test

# Run specific test suite
php artisan test --testsuite=Feature
php artisan test --testsuite=Unit

# Generate coverage report
php artisan test --coverage
```

## API Documentation

Auto-generated API documentation is available at:
```
http://your-domain/api/v1/docs
```

Generate/update documentation:
```bash
php artisan scribe:generate
```

## Quick Start Commands

```bash
# Install dependencies
composer install
npm install

# Setup environment
cp .env.example .env
php artisan key:generate

# Run migrations
php artisan migrate

# Build assets
npm run build

# Start development server
composer run dev
# OR separately:
php artisan serve
npm run dev
```

## Environment Variables

### Required Variables
```env
APP_NAME="SQ Backend"
APP_ENV=local
APP_KEY=
APP_DEBUG=true
APP_TIMEZONE=Asia/Jakarta
APP_URL=http://localhost

DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=your_database
DB_USERNAME=your_username
DB_PASSWORD=your_password

CACHE_DRIVER=file
QUEUE_CONNECTION=sync
SESSION_DRIVER=file
```

### Optional Variables
```env
# Redis
REDIS_HOST=127.0.0.1
REDIS_PASSWORD=null
REDIS_PORT=6379

# Mail
MAIL_MAILER=smtp
MAIL_HOST=smtp.mailtrap.io
MAIL_PORT=2525
MAIL_USERNAME=null
MAIL_PASSWORD=null
MAIL_ENCRYPTION=null

# API Configuration
API_VERSION=v1
API_RATE_LIMIT_AUTH=10
API_RATE_LIMIT_PUBLIC=60
API_RATE_LIMIT_PROTECTED=200
```

## Troubleshooting

### Common Issues

**1. Storage Permission Error**
```bash
chmod -R 775 storage bootstrap/cache
```

**2. Class not found**
```bash
composer dump-autoload
```

**3. Route not found**
```bash
php artisan route:clear
php artisan cache:clear
```

**4. Migration errors**
```bash
php artisan migrate:fresh
```

## Support

For issues and questions:
- Check Laravel documentation: https://laravel.com/docs
- Laravel community: https://laracasts.com/discuss
- Stack Overflow: https://stackoverflow.com/questions/tagged/laravel

## Version Information

- **Laravel:** 12.x
- **PHP:** 8.2+
- **Database:** MySQL 8.0+ / PostgreSQL 15+
- **Node.js:** 20.x
- **API Version:** v1
