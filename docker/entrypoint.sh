#!/bin/sh
# Container startup: prepare the database and caches, then hand over to PHP-FPM.
set -e

if [ -z "$APP_KEY" ]; then
    echo "ERROR: APP_KEY is not set. Generate one with:" >&2
    echo "  echo \"APP_KEY=base64:\$(openssl rand -base64 32)\" > .env" >&2
    exit 1
fi

# SQLite lives on a volume (/data) so it survives container restarts.
if [ "$DB_CONNECTION" = "sqlite" ] && [ ! -f "$DB_DATABASE" ]; then
    echo "Creating SQLite database at $DB_DATABASE"
    touch "$DB_DATABASE"
fi

php artisan migrate --force
php artisan config:cache
php artisan view:cache

exec "$@"
