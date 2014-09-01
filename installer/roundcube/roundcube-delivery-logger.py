#!/usr/bin/python2

import time

import MySQLdb

"""
Roundcube

/var/log/roundcubemail/sendmail

[2014-06-17 17:47:16]: User sender@vmail.me [xxx.xxx.xxx.xxx]; Message for recipient@vmail.me; 250: 2.0.0 Ok: queued as 3169313C00F1
"""

def parse(file):
    file.seek(0,2) # Go to the end of the file
    while True:
        line = file.readline()
        line = line.strip() # Delete "\n"
        if not line:
            time.sleep(0.1) # Sleep briefly
            continue
        yield line

db = MySQLdb.connect(host="localhost", user="server", passwd="CONFIG_MARIADB_SERVER_PASSWORD", db="vmailme")

cur = db.cursor()

roundcube = open("/var/log/roundcubemail/sendmail")
roundcube_lines = parse(roundcube)
for line in roundcube_lines:
    line = line.split(" ") # String line to array

    date1 = line[0]
    date1 = date1.replace("[", "")
    date2 = line[1]
    date2 = date2.replace("]:", "")
    date = date1 + ' ' + date2

    sender = line[3]

    ip = line[4]
    ip = ip.replace("[", "")
    ip = ip.replace("];", "")

    recipient = line[7]
    recipient = recipient.replace(";", "")

    cur.execute("INSERT INTO delivery (sender, ip, recipient, date) VALUES (%s, %s, %s, %s)", (sender, ip, recipient, date))

    db.commit()

db.close()
