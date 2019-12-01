<?php

//$out = '';
//putenv('COMPOSER_HOME=/opt/cpanel/composer/bin');
//exec('php-cli /opt/cpanel/composer/bin/composer install', $out);
//var_dump($out);

echo "Test";

if ($_GET['deploy_config']) {
    file_put_contents(
        'config.json',
        <<<DELIM
{
  "database": {
    "driver": "sqlite",
    "db_name": "soa_demo"
  }
}
DELIM
    );
}