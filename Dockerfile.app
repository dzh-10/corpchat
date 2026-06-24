# Stage 1: Build Assets
FROM node:20-alpine AS assets-builder
WORKDIR /app
COPY package*.json vite.config.js ./
COPY resources/ ./resources/
RUN npm install && npm run build

# Stage 2: PHP Application Base
FROM php:8.3-fpm-alpine
RUN apk add --no-cache icu-dev \
    && docker-php-ext-install pdo pdo_mysql intl
RUN echo "listen = 0.0.0.0:9000" >> /usr/local/etc/php-fpm.d/zz-docker.conf

WORKDIR /var/www/html

# Copy application files
COPY . .

# Copy compiled assets from Stage 1
COPY --from=assets-builder /app/public/build ./public/build

EXPOSE 9000
CMD ["php-fpm"]
