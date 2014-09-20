vmail.me
=========

![vmail.me](https://pbs.twimg.com/profile_images/1515989449/vmail_wallpaper.png)

The simple and secure email service which respects your privacy.

Website: https://www.vmail.me


Technologies
--------------

Arch Linux

Symfony2 (PHP)


Community
--------------

Follow vmail.me on [Twitter](https://twitter.com/vmailme) and [Facebook](https://www.facebook.com/pages/vmailme/181655708543682).

Help us with [translation](./www/src/rootio/Bundle/vmailmeBundle/Resources/translations).


Reporting a security issue
--------------

If you think that you have found a security issue in vmail.me project, please don't use the bug tracker and don't publish it publicly.

Instead, all security issues must be sent to support [at] vmail.me.


Getting started
--------------

See [LUKS.md](./LUKS.md) for disk encryption

1. `git clone https://github.com/root-io/vmail.git /home/vmail`

1. `cd /home/vmail/installer`

1. `cp config.conf.example config.conf`

1. `bash install.sh`


Production environment
--------------

```sh
rm /var/www/web/app_dev.php
rm /var/www/web/config.php
```


Commands
---------

```sh
php app/console vmailme:ban [username or email] [reason]
php app/console vmailme:disable [username or email]
php app/console vmailme:enable [username or email]
```


Tools
---------

Generate SSL/TLS certificates
```sh
openssl req -nodes -newkey rsa:4096 -nodes -keyout /etc/ssl/private/server.key -out /etc/ssl/certs/server.csr
```

Self-sign SSL/TLS certificates
```sh
openssl x509 -req -days 365 -in /etc/ssl/certs/server.csr -signkey /etc/ssl/private/server.key -out /etc/ssl/certs/server.crt
```

Generate passwords
```sh
apg -a 1 -m 16 -n 5
```

Erase disk
```sh
shred -f -z -v -u /dev/sdx
```


Donate
--------------
Bitcoin: `19n7XtXfS2VpCDMkjKkDA6b3KZqMXxwAg`
