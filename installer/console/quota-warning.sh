#!/bin/bash

recipient=$1
percent=$2

php app/console vmailme:quota $recipient $percent
