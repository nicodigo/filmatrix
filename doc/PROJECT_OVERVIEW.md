# Filmatrix

A vanilla PHP web application for movie reviews.

## Overview

Filmatrix lets users browse a catalog of movies, create an account, authenticate via native PHP sessions, and manage their profile. It follows a Controller-Service-Repository pattern without any PHP framework.

## Project Structure

```
├── public/               # Web root (entry point, CSS, JS, images)
│   └── index.php         # Front controller: starts session, requires bootstrap
├── src/                  # Application logic
│   ├── bootstrap.php     # Dependency wiring, routes, and dispatcher setup
│   ├── Controllers/      # Handles HTTP requests, passes data to views
│   ├── Services/         # Business logic layer (orchestrates repositories)
│   ├── Repository/       # Data access layer (PDO prepared statements)
│   ├── Models/           # Domain objects (User)
│   ├── Middleware/        # Request filters (e.g., auth guard)
│   ├── Core/             # Framework-like classes (Router, Request, Config, DB, Exceptions)
│   └── Core/Traits/      # Reusable traits (Loggable)
├── views/                # Plain PHP template files
│   ├── layouts/          # Base layout wrapper
│   ├── pages/            # Page templates (home, login, register, profile…)
│   └── partials/         # Reusable UI pieces (header, footer)
├── config/               # Application configuration files
├── db/migrations/        # Phinx database migrations
├── doc/                  # Documentation assets
├── storage/              # Cache, uploads (git‑kept empty)
├── tests/                # Test skeleton (empty)
├── vendor/               # Composer dependencies
├── .env.example          # Environment template
└── composer.json         # Dependency manifest
```

## Basic Request Flow

1. A request hits `public/index.php`.
2. `index.php` configures session parameters, starts the session, and requires `src/bootstrap.php`.
3. `bootstrap.php` instantiates all dependencies (Config, PDO, Repository, Service, Middleware) and maps routes to closures.
4. The `Router` matches the request URI+method to a closure and calls it.
5. The closure creates the appropriate Controller and invokes a method.
6. The Controller calls the Service layer; the Service calls the Repository.
7. The Repository executes a PDO prepared statement against the database.
8. The Controller renders a view (plain PHP include) or redirects the user.
9. `Router::call()` executes the closure; output is sent directly.

## Key Concepts

- **Separation of concerns**: Controllers only handle input/response; Services contain business logic; Repositories handle data access.
- **PDO + prepared statements**: All database queries use parameterised queries to prevent SQL injection.
- **Native session authentication**: login/register sets `$_SESSION['user_id']`, `user_role`, and `username`. Middleware checks existence for protected routes.
- **No external auth library**: passwords hashed with `password_hash(PASSWORD_DEFAULT)` and verified with `password_verify`.
- **Convention**: Public assets in `public/assets/`, views in `views/`, autoloading via Composer PSR‑4 (namespace `App\` points to `src/`).

## Getting Started

### Requirements

- PHP 8.2+
- PostgreSQL (or any PDO‑supported database defined in your `.env`)
- Composer

### Quick start

```bash
cp .env.example .env       # edit database credentials
composer install
php vendor/bin/phinx migrate   # run migrations
php -S localhost:8000 -t public
```

Open `http://localhost:8000` in your browser.

## Notes for Developers

- **Adding a new page**: define a method in a Controller, register a route in `src/bootstrap.php`, create a corresponding view file in `views/pages/`.
- **New database queries**: extend a Repository class with prepared statements; never write raw SQL outside Repository.
- **Business logic**: add methods to a Service class; keep Controllers skinny.
- **Environment**: sensitive credentials go into `.env` (never committed).
- **Migrations**: use Phinx to create/modify tables. Migration files live in `db/migrations/`.
- **Logging**: the `$log_app` logger (Monolog) is available throughout the stack; use `$this->logger->info()` etc. where needed.
