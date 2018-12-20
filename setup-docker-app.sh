#!/usr/bin/env bash

if [ "$1" = '--help' ]; then
    echo "    -- Setup Docker de l'application histologie --"
    echo "    ----------------------------------------------"
    echo "Utilisation : setup-docker-app.sh [<fichier sql>] [--drop-database]"
    exit;
fi

docker-compose stop;
docker-compose build;

if [ "$2" = '--drop-database' ]; then
    docker-compose rm -fv mysql
fi;

docker-compose up -d;
docker-compose exec php /bin/bash \
    -c "mkdir -p /web/logs /web/commande/pdf /web/img/logos; \
        chown -R www-data:www-data /web/logs /web/commande/pdf /web/bdd/backups/ /web/img/; \
        cd /web && composer install;";
docker-compose exec mysql /bin/bash \
    -c "crontab -r; \
        crontab < /web/docker/mysql/crontab.txt; \
        service cron start"

if [ "$#" -gt 0 ] &&  [ -f $1 ]; then

    if [ "$2" = '--drop-database' ]; then
        docker-compose stop mysql
        docker-compose rm -fv mysql
        docker-compose up -d mysql
    fi

    mysql_base_cmd='mysql -u "$MYSQL_USER" --database="$MYSQL_DATABASE" --password="$MYSQL_PASSWORD"';
    echo "Waiting for mysql service to be ready...";
    sleep 2m;
    docker-compose exec mysql sh -c "$mysql_base_cmd < /web/$1";
    echo "Import from sql file completed !";
fi
