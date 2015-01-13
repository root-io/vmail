#!/bin/bash

if [ "$IP" != "127.0.0.1" ] && [ "$IP" != "::1" ] && [ "$IP" != "CONFIG_IP_PRIMARY" ]; then
    cd /var/www
    php app/console vmailme:postlogin $USER $IP imap
fi

exec "$@"
