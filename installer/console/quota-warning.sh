#!/bin/bash

recipient=$1
percent=$2

cd /var/www
php app/console vmail:quota $recipient $percent
