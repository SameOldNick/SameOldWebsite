FROM php:8.2-fpm-alpine

WORKDIR /var/www/app

RUN apk update && apk upgrade

RUN apk add \
    curl \
    libzip-dev \
    libpng-dev \
    libxml2-dev \
    zip \
    unzip \
    supervisor

RUN apk --no-cache add nodejs npm

ADD --chmod=0755 https://github.com/mlocati/docker-php-extension-installer/releases/latest/download/install-php-extensions /usr/local/bin/

RUN install-php-extensions \
    @composer \
    pdo \
    pdo_mysql \
    mysqli \
    opcache \
    pcntl \
    gd \
    bcmath \
    posix \
    zip \
    redis

COPY supervisord.conf /etc/supervisor.d/supervisord.conf

ADD --chmod=0755 start-container /usr/local/bin/start-container
ENTRYPOINT ["start-container"]
