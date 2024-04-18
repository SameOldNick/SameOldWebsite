FROM php:8.2-fpm-alpine

WORKDIR /var/www/app

RUN apk update && apk add \
    curl \
    libzip-dev \
    libpng-dev \
    libxml2-dev \
    zip \
    unzip

RUN docker-php-ext-install pdo pdo_mysql mysqli opcache pcntl gd bcmath posix zip \
    && apk --no-cache add nodejs npm

RUN apk add --no-cache pcre-dev $PHPIZE_DEPS \
    && pecl install redis \
    && docker-php-ext-enable redis.so

# COPY --from=composer:latest /usr/bin/composer /usr/local/bin/composer
RUN curl -sLS https://getcomposer.org/installer | php -- --install-dir=/usr/bin/ --filename=composer

USER root

RUN chmod 777 -R /var/www/app
