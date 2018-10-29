FROM php:7.2-fpm-alpine

# Install Linux dependencies : composer / bash
RUN apk add --update composer util-linux bash

# Install PHP dependencies : PDO drivers
RUN docker-php-ext-install mysqli pdo_mysql

# LDAP
RUN  apk add --no-cache --virtual .build-deps $PHPIZE_DEPS icu-dev openldap-dev && \
    docker-php-ext-install ldap  && \
    docker-php-ext-enable ldap && \
    apk del .build-deps
RUN apk add --no-cache --virtual .persistent-deps libldap
