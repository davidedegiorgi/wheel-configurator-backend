#!/bin/sh

set -e

php artisan optimize:clear

php artisan tinker --execute='echo "MAIL_MAILER=".config("mail.default").PHP_EOL; echo "MAIL_HOST=".config("mail.mailers.smtp.host").PHP_EOL; echo "MAIL_FROM=".config("mail.from.address").PHP_EOL;'

php artisan migrate --force --no-interaction

exec php artisan serve --host=0.0.0.0 --port="${PORT:-10000}"
