# Refactoring Towards Clean Architecture — Laravel Workshop

The backend is the Conduit RealWorld example application running on a standard Laravel 13 application skeleton. The `main` workshop baseline intentionally keeps the Laravel-style implementation; architectural refactoring belongs in separate workshop branches.

## Requirements

- Git
- Docker with Docker Compose
- `make`

No host installation of PHP, Composer, or PostgreSQL is required.

## Installation

```bash
git clone <repository-url>
cd laravel-refactoring-towards-clean-architecture
make install
```

`make install` builds the PHP image, installs Composer dependencies, starts PostgreSQL and the application, generates application and JWT secrets, runs migrations, and verifies the project with the test suite.

The API is available at <http://localhost:8001/api/articles>. The health endpoint is available at <http://localhost:8001/up>.

## Host installation (without Docker)

Use this only if you cannot run Docker.

**Additional prerequisites:** PHP 8.3+ with the `pdo_pgsql`, `pcntl` and `zip` extensions, Composer, PostgreSQL 16.

1. Clone the repository (see above).
2. Create the environment file:
   ```bash
   cp .env.example .env
   ```
   Point the database settings at your local PostgreSQL instance. `DB_HOST=database` is the
   Docker service name and will not resolve on the host:
   ```
   DB_HOST=127.0.0.1
   DB_PORT=5432
   DB_DATABASE=app
   DB_USERNAME=<your user>
   DB_PASSWORD=<your password>
   ```
3. Create the development and test databases (the test database name `app_test` is fixed in `phpunit.xml`):
   ```bash
   createdb app
   createdb app_test
   ```
4. Install dependencies and generate secrets:
   ```bash
   composer install
   php artisan key:generate
   php artisan jwt:secret
   ```
5. Run migrations, optionally loading the seed data:
   ```bash
   php artisan migrate          # or: php artisan migrate:fresh --seed
   ```
6. Verify everything works:
   ```bash
   php artisan test
   ```
7. Start the application on the same port as the Docker setup:
   ```bash
   php artisan serve --port=8001
   ```

The `make` targets run inside the Docker containers, so they do not work in a host installation —
use the `php artisan` / `composer` / `vendor/bin/pint` commands directly instead.

## Common commands

```bash
make up                 # start the application and database
make down               # stop containers
make test               # run the test suite
make shell              # open a shell in the PHP container
make audit              # check Composer security advisories
make format             # format PHP code with Pint
make migrate            # run pending migrations
make reset              # recreate and seed the database (destructive)
```

## Database

The application and the test suite both run on the PostgreSQL container from `compose.yaml`.
Tests use a separate `app_test` database so they never touch development data; it is created
on a fresh volume by `docker/postgres/create-test-database.sh` and, for existing volumes, by
`make test-db` (which `make test` runs for you). Every test case uses Laravel's
`RefreshDatabase`, so each test runs inside a transaction that is rolled back afterwards and
the test database is left clean.

## Seed data

`make reset` recreates the schema and loads deterministic fixtures: three users (`john`,
`jane`, `alice` - all with the password `password`), six tagged articles, four comments, plus
a few follows and favorites.

Xdebug is installed in the development image but disabled by default. Enable it for a command or session with `XDEBUG_MODE=debug`.
