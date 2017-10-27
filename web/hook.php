<?php

$path = '/usr/bin/env -i HOME=/var/www/html/rcinformatica.net.br/web';

exec("$path export SYMFONY_ENV=prod");
exec("$path git pull origin master");
exec("$path php -d allow_url_fopen=1 composer.phar install --no-dev --optimize-autoloader");
exec("$path php bin/console cache:clear --env=prod --no-debug --no-warmup");
exec("$path php bin/console cache:warmup --env=prod");
//exec("$path php bin/console assetic:dump --env=prod --no-debug");
exec("$path php bin/console doctrine:migrations:migrate -n");

echo "All done!";