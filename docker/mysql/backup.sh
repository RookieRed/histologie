#!/usr/bin/env bash

if [ "$EUID" -ne 0 ]; then
    echo "Please run as root inside the mysql service"
    exit 1
fi;

filename=histo-`date +%Y%m%d-%H%M%S`.sql

echo "Starting backup"

mysqldump -u "$MYSQL_USER" --password="$MYSQL_PASSWORD" "$MYSQL_DATABASE" > /web/bdd/backups/"$filename"

echo "File saved in bdd/backups/$filename"

if [ "$#" -gt 0 ] && [[ $1 == --drop-commands ]]; then
    echo "Script is about to delete the past commands"

    mysql -u "$MYSQL_USER" --database="$MYSQL_DATABASE" --password="$MYSQL_PASSWORD" \
        --execute="DELETE FROM Commande; "
fi;
