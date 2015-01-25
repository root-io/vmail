#!/bin/bash

SENDMAIL=/usr/bin/sendmail

sender=$1
recipient=$2

cd /var/www
php app/console vmail:activity $sender

$SENDMAIL -i -f $sender -- $recipient

exit $?
