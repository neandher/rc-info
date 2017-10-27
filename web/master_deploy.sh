#!/bin/bash

cd /var/www/html/rcinformatica.net.br/web
export SYMFONY_ENV=prod
git pull origin master
php -d allow_url_fopen=1 composer.phar install --no-dev --optimize-autoloader
php bin/console cache:clear --env=prod --no-debug --no-warmup
php bin/console cache:warmup --env=prod
php bin/console doctrine:migrations:migrate -n
echo "All done!"