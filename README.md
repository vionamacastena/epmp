# EPMP - Enterprise Project Management Platform

##  Tech Stack

### Backend
- Laravel 13.x
- PHP 8.5
- PostgreSQL 15
- Redis 8.x

### Frontend
- Next.js 16.x
- TypeScript 5.x
- TailwindCSS 4.x

##  Instalimi

### Backend
```bash
cd ~/epmp
composer install
cp .env.example .env
php artisan key:generate
php artisan migrate
php artisan serve
