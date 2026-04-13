#!/bin/sh

set -eu

cd /var/www/html

require_env_var() {
    variable_name="$1"
    eval "variable_value=\${$variable_name:-}"

    if [ -z "$variable_value" ]; then
        echo "Missing required environment variable: $variable_name" >&2
        exit 1
    fi
}

if [ "${APP_ENV:-production}" = "production" ]; then
    require_env_var APP_KEY
fi

if [ "${DB_CONNECTION:-mysql}" != "sqlite" ]; then
    require_env_var DB_HOST
    require_env_var DB_PORT
    require_env_var DB_DATABASE
    require_env_var DB_USERNAME
fi

php artisan package:discover --ansi || true

if [ "${APP_ENV:-production}" = "production" ] && [ "${APP_SKIP_FILAMENT_ASSETS:-0}" != "1" ]; then
    php artisan filament:assets --no-interaction --ansi || true
fi

if [ "${APP_ENV:-production}" = "production" ] && [ "${APP_SKIP_MIGRATIONS:-0}" != "1" ]; then
    php artisan migrate --force --ansi
fi

if [ "${APP_ENV:-production}" = "production" ] && [ "${APP_SKIP_WARMUP:-0}" != "1" ]; then
    php artisan config:cache --ansi || true
    php artisan route:cache --ansi || true
    php artisan view:cache --ansi || true
fi

if [ "$#" -gt 0 ]; then
    exec "$@"
fi

exec supervisord -c /etc/supervisord.conf
