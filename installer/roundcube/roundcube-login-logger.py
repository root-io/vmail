#!/usr/bin/python2

import time

import MySQLdb

"""
Roundcube

/var/log/roundcubemail/userlogins

[2014-06-13 05:59:28]: Successful login for test@vmail.me (ID: 11356) from xxx.xxx.xxx.xxx in session s47flehrq2ub842u92fu16m687
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

roundcube = open("/var/log/roundcubemail/userlogins")
roundcube_lines = parse(roundcube)
for line in roundcube_lines:
    line = line.split(" ") # String line to array

    date1 = line[0]
    date1 = date1.replace("[", "")
    date2 = line[1]
    date2 = date2.replace("]:", "")
    date = date1 + ' ' + date2

    email = line[5]

    ip = line[9]

    if ip != "CONFIG_IP_PRIMARY":
        cur.execute("INSERT INTO login (email, ip, protocol, date) VALUES (%s, %s, %s, %s)", (email, ip, "web", date))
        db.commit()

db.close()
