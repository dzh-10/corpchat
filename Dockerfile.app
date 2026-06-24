FROM php:8.3-fpm-alpine
RUN apk add --no-cache icu-dev \
    && docker-php-ext-install pdo pdo_mysql intl
RUN echo "listen = 0.0.0.0:9000" >> /usr/local/etc/php-fpm.d/zz-docker.conf
