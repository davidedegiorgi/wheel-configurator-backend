#!/bin/sh

set -e

php artisan migrate --force --no-interaction
php artisan db:seed --class=Database\\Seeders\\SyncAudiColorsSeeder --force

exec php artisan serve --host=0.0.0.0 --port="${PORT:-10000}"
