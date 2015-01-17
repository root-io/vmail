#!/bin/bash

REPO_PATH='/home/vmail'
VAGRANT=true

if [ -f $REPO_PATH/installer/config.conf ]; then

    source $REPO_PATH/installer/install_server.sh
    source $REPO_PATH/installer/install_website.sh
fi
