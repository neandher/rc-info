<?php

$mirrordir = "/var/www/html/rcinformatica.net.br/web";
$gitdir = $mirrordir . "/.git";

$commands = "/usr/libexec/git-core/git --work-tree=$mirrordir --git-dir=$gitdir pull -f origin master";
$commands .= " && export SYMFONY_ENV=prod";
$commands .= " && cd $mirrordir";
$commands .= " && php -d allow_url_fopen=1 composer.phar install --no-dev --optimize-autoloader";
$commands .= " && php bin/console cache:clear --env=prod --no-debug --no-warmup";
$commands .= " && php bin/console cache:warmup --env=prod";
$commands .= " && php bin/console doctrine:migrations:migrate -n";

exec("$commands");

echo "All done!";