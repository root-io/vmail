#!/bin/bash

cd /var/www
php app/console vmail:activity

exec "$@"
