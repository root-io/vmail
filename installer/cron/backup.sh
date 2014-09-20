#!/bin/bash

#
# Backup www, logs, databases, mailboxes & queue
# Keep last 7 days backup and the first backup of each last three months
#
# @author David Routhieau <rootio@vmail.me>
#

mkdir -p /home/backup

tar -zcf "/home/backup/$(date +"%Y%m%d")-www.tar.gz" /var/www
tar -zcf "/home/backup/$(date +"%Y%m%d")-log.tar.gz" /var/log

databases=`/usr/bin/mysql -u root -p'CONFIG_MARIADB_ROOT_PASSWORD' -e "SHOW DATABASES;" | grep -Ev "(Database|information_schema|mysql|performance_schema|test)"`
for db in $databases; do
    mysqldump --force --opt --user=root -p'CONFIG_MARIADB_ROOT_PASSWORD' --databases $db | gzip > "/home/backup/$(date +"%Y%m%d")-$db.sql.gz"
done

tar -zcf "/home/backup/$(date +"%Y%m%d")-mailboxes.tar.gz" /home/mailboxes
tar -zcf "/home/backup/$(date +"%Y%m%d")-queue.tar.gz" /var/spool/postfix

find /home/backup/* ! \( -name *01.*gz \) -atime +7 -exec rm '{}' \;
find /home/backup/* \( -name *01.*gz \) -atime +90 -exec rm '{}' \;
