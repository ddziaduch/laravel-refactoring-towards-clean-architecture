#!/bin/sh
# Creates the database used by the PHPUnit suite (see phpunit.xml).
# Runs automatically on a fresh Postgres volume via /docker-entrypoint-initdb.d,
# and can be executed again at any time - it is idempotent.
set -e

TEST_DB="${TEST_DB:-app_test}"

if psql -U "$POSTGRES_USER" -d "$POSTGRES_DB" -tAc \
    "SELECT 1 FROM pg_database WHERE datname = '$TEST_DB'" | grep -q 1; then
    echo "Test database '$TEST_DB' already exists."
else
    createdb -U "$POSTGRES_USER" -O "$POSTGRES_USER" "$TEST_DB"
    echo "Created test database '$TEST_DB'."
fi
