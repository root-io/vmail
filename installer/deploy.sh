#!/bin/bash

INSTALL_PATH='/home/installer'

## Config file
source ./config.conf


## www
mkdir -p /var/www
chown -R http:http /var/www/
sed -i -e "s/CONFIG_MARIADB_WWW_PASSWORD/$CONFIG_MARIADB_WWW_PASSWORD/g" /var/www/app/config/parameters.yml
cd /var/www

# Cache and logs
rm -r app/cache/*
rm -r app/logs/*
# Managed by Composer
rm -r app/bootstrap.php.cache
rm -r bin/
rm -r vendor/
# Assets
rm -r web/bundles/

php composer.phar self-update
php composer.phar update
php app/console assets:install
php composer.phar dump-autoload --optimize
php app/console doctrine:schema:update --force
chmod -R 777 app/cache
chmod -R 777 app/logs


## Console scripts
cp $INSTALL_PATH/console/* /var/www
chmod 777 /var/www/*.sh
sed -i -e "s/CONFIG_IP_PRIMARY/$CONFIG_IP_PRIMARY/g" /var/www/dfilter.sh


## Symbolic links
ln -s /usr/share/webapps/roundcubemail/ /var/www/web/webmail
ln -s /usr/share/webapps/piwik/ /var/www/web/piwik


echo "Deploy www [OK]."
