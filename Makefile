.PHONY: install build up down composer-install migrate reset seed test test-db shell audit format

COMPOSE = docker compose
EXEC = $(COMPOSE) exec app
RUN = $(COMPOSE) run --rm --no-deps app

install: .env build composer-install up app-key jwt-secret migrate test

.env:
	cp .env.example .env

build:
	$(COMPOSE) build

up:
	$(COMPOSE) up -d app database

down:
	$(COMPOSE) down

composer-install:
	$(RUN) composer install --no-interaction --prefer-dist

app-key:
	@grep -q '^APP_KEY=base64:' .env || $(EXEC) php artisan key:generate --ansi

jwt-secret:
	@grep -q '^JWT_SECRET=..*' .env || $(EXEC) php artisan jwt:secret --force --ansi

migrate:
	$(EXEC) php artisan migrate --force --ansi

reset:
	$(EXEC) php artisan migrate:fresh --seed --force --ansi

seed:
	$(EXEC) php artisan db:seed --force --ansi

test: test-db
	$(EXEC) php artisan test

test-db:
	@$(COMPOSE) exec -T database /docker-entrypoint-initdb.d/10-create-test-database.sh

shell:
	$(EXEC) bash

audit:
	$(EXEC) composer audit

format:
	$(EXEC) vendor/bin/pint
