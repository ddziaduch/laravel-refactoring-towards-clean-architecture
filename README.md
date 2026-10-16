# Refactoring Towards Clean Architecture — Laravel Workshop

The backend is the Conduit RealWorld example application running on a standard Laravel 13 application skeleton. The `main` workshop baseline intentionally keeps the Laravel-style implementation; architectural refactoring belongs in separate workshop branches.

This branch (`modular/bounded-contexts`) shows the destination: the whole API split into modules (bounded contexts), each designed with clean architecture inside and talking to the others only through published contracts. See [Modules](#modules).

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

The API contract is described by the RealWorld Conduit API v2 specification in [`openapi.yml`](openapi.yml).

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

## Modules

The code lives in `src/`, one directory per bounded context. `app/` keeps only framework glue:
error rendering, the `ConduitException` type and the JWT header parser.

| Module | Owns the tables | Endpoints | Publishes (`PublicApi`) |
|---|---|---|---|
| **Identity** | `users` | `POST /users`, `POST /users/login`, `GET/PUT /user` | `UserDirectory` |
| **Profile** | `followers` | `GET /profiles/{username}`, `POST/DELETE /profiles/{username}/follow` | `Profiles`, `FollowGraph` |
| **Article** | `articles`, `tags`, `article_tag`, `article_user` (favorites) | `/articles`, `/articles/feed`, `/articles/{slug}`, `/articles/{slug}/favorite`, `/tags` | `Articles` |
| **Engagement** | `comments` | `/articles/{slug}/comments`, `/articles/{slug}/comments/{id}`, `php artisan comment:create` | nothing, no module depends on it |

Dependencies between modules point one way only:

```
Engagement ──► Article ──► Profile ──► Identity
     └──────────────────────►┘
```

### Inside a module

```
src/<Module>/
  Domain/                  entities, value objects, domain exceptions - plain PHP
  Application/
    Port/Primary/          use case interfaces (what the module can do)
    Port/Secondary/        what the module needs: repositories, queries, other modules
    UseCase/               use case implementations
    ReadModel/             response models of the module
    PublicApi/             implementation of the module's published contracts
  PublicApi/               published contracts: interfaces and immutable DTOs only
  Adapter/
    Primary/Http|Cli/      controllers, form requests, resources, console commands
    Secondary/Eloquent|Database/   persistence of the module's own tables
    Secondary/<OtherModule>/       anti-corruption layer to another module's PublicApi
    Laravel/               service provider (port bindings) and routes
```

### Rules

1. **A module talks to another one only through its `PublicApi`.** Never through its models,
   tables, use cases or exceptions.
2. **Only adapters may call another module.** The application layer declares its own port,
   for example `Article\Application\Port\Secondary\AuthorProfiles`, and an adapter such as
   `Article\Adapter\Secondary\Profile\ProfileAuthors` implements it with Profile's public API.
   Replacing Profile, or faking it in a unit test, never touches the Article use cases.
3. **No cross-module joins or Eloquent relations.** Modules refer to each other's data by id.
   A list of articles is one query on Article's tables plus one *batch* call for all authors
   on the page, so there is no N+1.
4. **Each module keeps its own models**, even when they look alike: Article and Engagement
   both have an `AuthorReadModel`, on purpose.
5. **The framework stays at the edge.** Domain and Application import no `Illuminate\*`.

`make deptrac` enforces rules 1, 2 and 5 together with the layering inside each module.

### Deliberate compromises

- **One shared database.** Each module owns its tables, but foreign keys and `ON DELETE CASCADE`
  between modules remain. Deleting an article removes its comments through the database, not
  through an event from Article to Engagement. Splitting the database would mean replacing the
  cascades with events.
- **Uniqueness of username and email** is validated in Identity's form requests against the
  `users` table, as the API contract requires (`409` / "has already been taken").
- **The authenticated user id** comes from Laravel's auth guard in every module's HTTP adapter.
  Identity owns the guard's user model; the other modules only ever see an `int`.

### Tests

- `tests/Feature` - black-box API contract tests. They passed on the Laravel-style `main`
  before the refactoring and still pass unchanged. Fixtures are created through the API only.
- `tests/Unit` - use cases with in-memory adapters; neighbouring modules are replaced by fakes
  of the consuming module's own ports.
- `tests/Integration` - the published APIs, resolved from the container exactly as other
  modules get them.

## Common commands

```bash
make up                 # start the application and database
make down               # stop containers
make test               # run the test suite
make deptrac            # check module boundaries and architectural layers
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
