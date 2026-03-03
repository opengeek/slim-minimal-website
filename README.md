# Minimal Slim Website

A minimal but fully-featured skeleton for building a Slim 4 website, with dependency injection,
Twig templating, structured logging, custom error handling, environment configuration, and a CLI
console.

## Requirements

- PHP 8.3+
- [Composer](https://getcomposer.org/)
- [DDEV](https://ddev.readthedocs.io/) (optional, for local development)

## Installation

    composer create-project opengeek/slim-minimal-website my-project-name
    cd my-project-name
    cp config/.env.example config/.env

Edit `config/.env` to set your environment and application name:

    APP_ENV="DEVELOPMENT"
    APP_NAME="My Project"

## Running Locally with DDEV

    ddev start

Navigate to https://minimal-website.slim.test

## Project Structure

```
├── bin/
│   ├── console              # Console entry point (executable)
│   └── console.php          # Console bootstrap
├── config/
│   ├── bin/
│   │   ├── dependencies.php # DI definitions for CLI context
│   │   └── settings.php     # Settings overrides for CLI context
│   ├── .env                 # Local environment variables (not committed)
│   ├── .env.example         # Example environment file
│   ├── dependencies.php     # DI container definitions
│   ├── middleware.php       # Middleware pipeline
│   ├── routes.php           # Route definitions
│   └── settings.php         # Application settings
├── public/
│   └── index.php           # Web entry point
├── src/
│   ├── Console/
│   │   └── Cache.php       # cache:clear console command
│   ├── Controllers/
│   │   └── Home.php        # Example home page controller
│   ├── Exceptions/
│   │   ├── Exception.php    # Base exception class
│   │   └── HttpException.php # HTTP exception factory methods
│   ├── Handlers/
│   │   └── ErrorHandler.php # Custom error handler
│   ├── Collection.php       # Generic array-backed collection
│   ├── Configuration.php    # Application configuration wrapper
│   └── functions.php        # Global helper functions
├── templates/
│   ├── base.twig           # Base HTML layout (Bootstrap 5)
│   ├── error.twig          # Error page template
│   └── index.twig          # Home page template
└── var/
    ├── compiled/           # Compiled DI container (production)
    ├── log/                # Application log files
    └── twig/               # Twig template cache
```

## Features

### Dependency Injection

[PHP-DI](https://php-di.org/) provides autowiring and a compiled container for production use.
Service definitions live in `config/dependencies.php`. In production (`APP_ENV=PRODUCTION`), the
container is compiled to `var/compiled/` for improved performance.

### Twig Templating

[Twig](https://twig.symfony.com/) templates live in `templates/`. The base layout (`base.twig`)
includes Bootstrap 5 via CDN. Template caching is enabled automatically in production.

### Logging

[Monolog](https://github.com/Seldaek/monolog) writes rotating log files to `var/log/`. Log
retention defaults to 7 days. The log level is `DEBUG` in development and `ERROR` in production.

### Custom Exception & Error Handling

`HttpException` provides static factory methods for common HTTP errors:

```php
throw HttpException::notFound('Page not found');
throw HttpException::unauthorized('Login required');
throw HttpException::forbidden('Access denied');
throw HttpException::badRequest('Invalid input');
```

The custom `ErrorHandler` renders errors as HTML (using `error.twig`) or JSON depending on the
`Accept` header. Full exception details are shown in development; only a generic message is shown
in production.

### Environment Configuration

Three environments are supported: `DEVELOPMENT`, `TEST`, and `PRODUCTION`. Set `APP_ENV` in
`config/.env`. Environment-dependent behavior includes log level, error detail display, Twig
caching, and DI container compilation.

### Console

A CLI console powered by [Silly](https://github.com/mnapoli/silly) is available at `bin/console`.

    bin/console cache:clear

The `cache:clear` command removes all files from the configured cache directories (`var/twig/`,
`var/compiled/`).

### Collection & Configuration Utilities

`Collection` is a generic, `ArrayAccess`-compatible data structure used as the base for
`Configuration`. Both support standard array-style access, iteration, and counting.

## License

MIT — Copyright © 2026 Jason Coward
