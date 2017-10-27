<?php

$path = "cd /var/www/html/rcinformatica.net.br/web";

shell_exec("{$path} export SYMFONY_ENV=prod");
shell_exec("$path git pull origin master");
shell_exec("$path php -d allow_url_fopen=1 composer.phar install --no-dev --optimize-autoloader");
shell_exec("$path php bin/console cache:clear --env=prod --no-debug --no-warmup");
shell_exec("$path php bin/console cache:warmup --env=prod");
//exec("$path php bin/console assetic:dump --env=prod --no-debug");
shell_exec("$path php bin/console doctrine:migrations:migrate -n");

echo "All done!";