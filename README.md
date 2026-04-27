 # Laravel REST API Starter Kit

A production-ready Laravel 13 REST API boilerplate with JWT authentication and role-based access control.

## What's included

- JWT Authentication (register, login, logout)
- Role-based access control (Admin and User roles)
- Posts CRUD endpoints
- Admin-only routes
- SQLite database (zero config)
- Clean folder structure

## Requirements

- PHP 8.2+
- Composer

## Setup

1. Clone or extract the project
2. Run `composer install`
3. Copy `.env.example` to `.env`
4. Run `php artisan key:generate`
5. Run `php artisan jwt:secret`
6. Run `php artisan migrate`
7. Run `php artisan serve`

## API Endpoints

| Method | Endpoint | Auth Required |
|--------|----------|---------------|
| POST | /api/register | No |
| POST | /api/login | No |
| POST | /api/logout | Yes |
| GET | /api/me | Yes |
| GET | /api/posts | Yes |
| POST | /api/posts | Yes |
| PUT | /api/posts/{id} | Yes |
| DELETE | /api/posts/{id} | Yes |
| GET | /api/admin/users | Admin only |

## License

MIT