#!/bin/sh
set -e

case "$1" in
    */supervisord|supervisord)
        RUN_INIT=1
        ;;
    *)
        RUN_INIT=0
        ;;
esac

if [ "$RUN_INIT" = "1" ]; then
    if [ -d /var/www/public-source ]; then
        mkdir -p /var/www/public
        cp -a /var/www/public-source/. /var/www/public/
        chown -R www-data:www-data /var/www/public
    fi

    php /var/www/artisan storage:link >/dev/null 2>&1 || true

    chown -R www-data:www-data /var/www/storage /var/www/bootstrap/cache 2>/dev/null || true
    chmod -R ug+rwX /var/www/storage /var/www/bootstrap/cache 2>/dev/null || true

    php /var/www/artisan config:clear >/dev/null 2>&1 || true
    php /var/www/artisan route:clear  >/dev/null 2>&1 || true
    php /var/www/artisan view:clear   >/dev/null 2>&1 || true
    php /var/www/artisan config:cache
    php /var/www/artisan route:cache
    php /var/www/artisan view:cache
fi

exec "$@"
