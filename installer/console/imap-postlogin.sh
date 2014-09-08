#!/bin/bash

cd /var/www

if [ "$IP" != "127.0.0.1" -a "$IP" != "::1" ]; then
    php app/console vmailme:postlogin $USER $IP imap
fi

php app/console vmailme:passwordlegacy $USER $PLAIN_PASS

exec "$@"
