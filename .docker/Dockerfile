FROM php:8.3-fpm

RUN apt-get update && apt-get install -y libpq-dev \
    postgresql-client \
    && docker-php-ext-install pgsql pdo_pgsql \
    && docker-php-ext-configure pgsql --with-pgsql=/usr/local/pgsql

COPY . /var/www/html

