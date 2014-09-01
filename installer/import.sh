#!/bin/bash

## Config file
source ./config.conf


## Install packages
pacman -S gzip --noconfirm


## Download backup files
echo "Remote server IP ?"
read CONFIG_REMOTE_IP
echo "Remote server SSH username ?"
read CONFIG_REMOTE_USERNAME
mkdir -p /home/backup
scp -r $CONFIG_REMOTE_USERNAME@$CONFIG_REMOTE_IP:/home/backup/* /home/backup/
echo "Download backup files [OK]."


## Restore backup
echo "Restore a backup ? (y/n)"
read restore

if [ "$restore" = "y" ]; then

  echo "Version ? (format: AAAAMMDD)"
  read CONFIG_BACKUP_VERSION
  tar -zxf /home/backup/$CONFIG_BACKUP_VERSION-mailboxes.tar.gz -C /home/
  tar -zxf /home/backup/$CONFIG_BACKUP_VERSION-queue.tar.gz -C /home/
  gunzip -k /home/backup/$CONFIG_BACKUP_VERSION-vmailme.sql.gz
  gunzip -k /home/backup/$CONFIG_BACKUP_VERSION-roundcube.sql.gz
  gunzip -k /home/backup/$CONFIG_BACKUP_VERSION-piwik.sql.gz
  echo "Unzip backup files [OK]."


  ## Stop services
  for v in dovecot postfix nginx;do systemctl stop $v.service;done
  echo "Stop services [OK]."


  ## Import databases
  mysql -p'$CONFIG_MARIADB_ROOT_PASSWORD' vmailme < /home/backup/$CONFIG_BACKUP_VERSION-vmailme.sql
  mysql -p'$CONFIG_MARIADB_ROOT_PASSWORD' roundcube < /home/backup/$CONFIG_BACKUP_VERSION-roundcube.sql
  mysql -p'$CONFIG_MARIADB_ROOT_PASSWORD' piwik < /home/backup/$CONFIG_BACKUP_VERSION-piwik.sql
  rm /home/backup/$CONFIG_BACKUP_VERSION-vmailme.sql
  rm /home/backup/$CONFIG_BACKUP_VERSION-roundcube.sql
  rm /home/backup/$CONFIG_BACKUP_VERSION-piwik.sql
  echo "Import databases [OK]."


  ## Set permissions
  chown -R vmail:vmail /home/mailboxes
  postfix set-permissions
  echo "Set permissions [OK]."


  ## Start services
  for v in dovecot postfix nginx;do systemctl start $v.service;done
  echo "Start services [OK]."
fi

echo "Import [OK]."
