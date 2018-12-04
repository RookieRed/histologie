#!/usr/bin/env bash

docker-compose stop;
docker-compose build mysql;
docker-compose build php;
docker-compose up -d;

docker-compose exec php /bin/bash \
    -c "mkdir -p /web/logs /web/commande/pdf; \
        chown -R www-data:www-data /web/logs /web/commande/pdf; \
        cd /web && composer install;";
docker-compose exec mysql /bin/bash \
    -c "crontab -r; \
        crontab < /web/docker/mysql/crontab.txt; \
        service cron start"

if [ "$#" -gt 0 ] &&  [ -f $1 ]; then
    mysql_base_cmd='mysql -u "$MYSQL_USER" --database="$MYSQL_DATABASE" --password="$MYSQL_PASSWORD"';
    echo "Waiting for mysql service to be ready...";
    sleep 2m;
    docker-compose exec mysql sh -c "$mysql_base_cmd < /web/$1";
    echo "Import from sql file completed !";
fi
