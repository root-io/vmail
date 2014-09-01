#!/bin/bash

## Cron Spamassassin
crontab -l > mycron
echo "@daily /usr/bin/vendor_perl/sa-update && systemctl restart spamassassin.service" >> mycron
crontab mycron
rm mycron
echo "Cron Spamassassin [OK]."


## Cron Backup
crontab -l > mycron
echo "@daily bash /usr/local/bin/backup.sh" >> mycron
crontab mycron
rm mycron
echo "Cron Backup [OK]."


## Cron clean Roundcube DB
crontab -l > mycron
echo "@daily php /usr/share/webapps/roundcubemail/bin/cleandb.sh" >> mycron
crontab mycron
rm mycron
echo "Cron clean Roundcube DB [OK]."


## Cron Piwik archive
crontab -l > mycron
echo "@hourly php /usr/share/webapps/piwik/console core:archive --url=https://www.vmail.me/piwik/" >> mycron
crontab mycron
rm mycron
echo "Cron Piwik archive [OK]."
