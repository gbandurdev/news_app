#!/bin/bash
set -e

# Wait for the database to be ready
echo "Waiting for PostgreSQL to be ready..."
until PGPASSWORD=$POSTGRES_PASSWORD psql -h "database" -U "symfony" -c '\q'; do
  >&2 echo "PostgreSQL is unavailable - sleeping"
  sleep 1
done

echo "PostgreSQL is up - executing command"

# Run composer install if vendor directory doesn't exist
if [ ! -d "vendor" ]; then
  echo "Running composer install..."
  composer install --no-interaction --optimize-autoloader
fi

# Check if database schema exists, if not, create it
echo "Checking database schema..."
php bin/console doctrine:schema:validate --skip-sync -q || php bin/console doctrine:schema:create --no-interaction

# Run migrations
echo "Running migrations..."
php bin/console doctrine:migrations:migrate --no-interaction

# Clear cache
echo "Clearing cache..."
php bin/console cache:clear

# Warm up cache
echo "Warming up cache..."
php bin/console cache:warmup

# Load fixtures if --fixtures option is provided and in dev environment
if [ "$APP_ENV" = "dev" ] && [ "$LOAD_FIXTURES" = "true" ]; then
  echo "Loading fixtures..."
  php bin/console doctrine:fixtures:load --no-interaction
fi

# First arg is `-f` or `--some-option`
if [ "${1#-}" != "$1" ]; then
  set -- php-fpm "$@"
fi

exec "$@"
