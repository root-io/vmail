#!/bin/bash

REPO_PATH='/home/vmail'

if [ -f $REPO_PATH/installer/config.conf ]; then

    ## Config file
    chmod 600 $REPO_PATH/installer/config.conf
    source $REPO_PATH/installer/config.conf


    ## www
    rm -r /var/www
    cp -r $REPO_PATH/www /var/www
    cp /var/www/app/config/parameters.yml.dist /var/www/app/config/parameters.yml
    sed -i -e "s/CONFIG_MARIADB_WWW_PASSWORD/$CONFIG_MARIADB_WWW_PASSWORD/g" /var/www/app/config/parameters.yml
    sed -i -e "s/CONFIG_DOMAIN/$CONFIG_DOMAIN/g" /var/www/app/config/parameters.yml
    composer self-update
    cd /var/www
    composer update
    composer dump-autoload --optimize
    php app/console assets:install
    php app/console doctrine:schema:update --force


    ## Virtual mailbox domain
    mysql -u root -p"$CONFIG_MARIADB_ROOT_PASSWORD" -e "INSERT INTO vmailme.domain SET name=\"$CONFIG_DOMAIN\";"


    ## Console scripts
    cp $REPO_PATH/installer/console/* /var/www
    sed -i -e "s/CONFIG_IP_PRIMARY/$CONFIG_IP_PRIMARY/g" /var/www/dfilter.sh


    ## Symbolic links
    ln -s /usr/share/webapps/roundcubemail/ /var/www/web/webmail
    ln -s /usr/share/webapps/piwik/ /var/www/web/piwik


    chown -R http:http /var/www/
    chmod -R 755 /var/www/
    echo "Deploy www [OK]."
fi
