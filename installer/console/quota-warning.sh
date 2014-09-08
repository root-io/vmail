#!/bin/bash

recipient=$1
percent=$2

cd /var/www

php app/console vmailme:quota $recipient $percent
