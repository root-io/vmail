Vmail
=====

![Vmail](https://www.vmail.me/media/images/facebook_share.png)

The simple and secure email service which respects your privacy.

Website: https://www.vmail.me


Technologies
------------

Arch Linux

Symfony2 (PHP)


Reporting a security issue
--------------------------

If you think that you have found a security issue in Vmail project, please don't use the bug tracker and don't publish it publicly.

Instead, all security issues must be sent to security [at] vmail.me ([PGP public key](https://keybase.io/rootio)).


Getting started
---------------

See [LUKS.md](./LUKS.md) for disk encryption


### Development environment

Download and install [VirtualBox](https://www.virtualbox.org/wiki/Downloads)

Download and install [Vagrant](https://www.vagrantup.com/downloads.html)

```sh
git clone https://github.com/root-io/vmail.git
cd vmail
cp installer/config.conf.example installer/config.conf
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


### Production environment

```sh
git clone https://github.com/root-io/vmail.git /home/vmail
cd /home/vmail/installer
bash install_server.sh
bash install_website.sh
rm /var/www/web/app_dev.php
rm /var/www/web/config.php
```


Commands
--------

```sh
php app/console vmail:ban [username or email] [reason]
php app/console vmail:disable [username or email]
php app/console vmail:enable [username or email]
php app/console vmail:pfdel [username or email]
php app/console vmail:register [username] [recipient]
```


Tools
-----

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
apg -MCLN -m 48 -x 64 -n 5
```

Erase disk
```sh
shred -fuzv /dev/sdx
```


License
-------

Vmail is licensed under the MIT license.
