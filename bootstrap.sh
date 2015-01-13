#!/bin/bash

REPO_PATH='/home/vmail'

if [ -f $REPO_PATH/installer/config.conf ]; then

    source $REPO_PATH/installer/install_server.sh

    VAGRANT=true
    source $REPO_PATH/installer/install_website.sh
fi
