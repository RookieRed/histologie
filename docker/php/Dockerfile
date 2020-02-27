FROM php:7.2-fpm-alpine

# Install Linux dependencies : composer / bash
RUN apk add --update composer util-linux bash zlib-dev libpng-dev openldap-dev

# Install PHP dependencies : PDO drivers
RUN docker-php-ext-install mysqli pdo_mysql zip gd ldap
