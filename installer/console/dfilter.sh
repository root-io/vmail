#!/bin/bash

SENDMAIL=/usr/bin/sendmail

sender=$1
ip=$2
recipient=$3

cd /var/www

if [ "$ip" != "127.0.0.1" ] && [ "$ip" != "::1" ] && [ "$ip" != "CONFIG_IP_PRIMARY" ]; then
    php app/console vmailme:delivery $sender $ip $recipient
fi

php app/console vmailme:warden $sender

$SENDMAIL -i -f $sender -- $recipient

exit $?
