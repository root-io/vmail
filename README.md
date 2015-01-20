vmail
=========

![vmail](https://pbs.twimg.com/profile_images/1515989449/vmail_wallpaper.png)

The simple and secure email service which respects your privacy.

Website: https://www.vmail.me


Technologies
--------------

Arch Linux

Symfony2 (PHP)


Community
--------------

Follow vmail on [Twitter](https://twitter.com/vmail).

Help us with [translation](./www/src/rootio/Bundle/vmailmeBundle/Resources/translations).


Reporting a security issue
--------------

If you think that you have found a security issue in vmail project, please don't use the bug tracker and don't publish it publicly.

Instead, all security issues must be sent to security [at] vmail.me ([PGP public key](https://keybase.io/rootio)).


Getting started
--------------

See [LUKS.md](./LUKS.md) for disk encryption

1. `git clone https://github.com/root-io/vmail.git /home/vmail`

1. `cd /home/vmail/installer`

1. `cp config.conf.example config.conf`

1. `bash install_server.sh`

1. `bash install_website.sh`


Production environment
--------------

```sh
rm /var/www/web/app_dev.php
rm /var/www/web/config.php
```


Commands
---------

```sh
php app/console vmail:ban [username or email] [reason]
php app/console vmail:disable [username or email]
php app/console vmail:enable [username or email]
php app/console vmail:pfdel [username or email]
php app/console vmail:register [username] [recipient]
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


Development environment
--------------

Download and install [VirtualBox](https://www.virtualbox.org/wiki/Downloads)
Download and install [Vagrant](https://www.vagrantup.com/downloads.html)

```sh
git clone https://github.com/root-io/vmail.git
cd vmail/
vagrant up
```

Windows

1. Open "C:\Windows\System32\drivers\etc\hosts" with a text editor in administrator mode

1. Add a new line to the file, with the following text: `1.3.3.7 vmail.dev www.vmail.dev`

1. Save

Mac

1. Open Terminal

1. In the terminal window, type: `sudo bash -c 'echo -e "1.3.3.7 vmail.dev www.vmail.dev" >> /private/etc/hosts'`

Linux

1. Open Terminal

1. In the terminal window, type: `sudo bash -c 'echo -e "1.3.3.7 vmail.dev www.vmail.dev" >> /etc/hosts'`

[Let's go](https://www.vmail.dev/app_dev.php/)


Donate
--------------
Bitcoin: `19n7XtXfS2VpCDMkjKkDA6b3KZqMXxwAg`
