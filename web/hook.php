<?php

$path = "cd /var/www/html/rcinformatica.net.br/web";

//export SYMFONY_ENV=prod && git pull origin master && php -d allow_url_fopen=1 composer.phar install --no-dev --optimize-autoloader && php bin/console cache:clear --env=prod --no-debug --no-warmup && php bin/console cache:clear --env=prod --no-debug --no-warmup && php bin/console cache:warmup --env=prod && php bin/console doctrine:migrations:migrate -n

$commands = "export SYMFONY_ENV=prod";
$commands .= " && git pull origin master";
$commands .= " && php -d allow_url_fopen=1 composer.phar install --no-dev --optimize-autoloader";
$commands .= " && php bin/console cache:clear --env=prod --no-debug --no-warmup";
$commands .= " && php bin/console cache:warmup --env=prod";
$commands .= " && php bin/console doctrine:migrations:migrate -n";

exec("$path $commands");

echo "All done!";