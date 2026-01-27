FROM composer:2 AS vendor
WORKDIR /app
COPY composer.json composer.lock ./
RUN composer install --no-dev --prefer-dist --no-interaction --no-progress
COPY . .

FROM php:8.4-cli
WORKDIR /usr/src/myapp
RUN docker-php-ext-install pdo_mysql

COPY --from=vendor /app /usr/src/myapp

EXPOSE 8000
CMD [ "php", "-S", "0.0.0.0:8000", "-t", "/usr/src/myapp" ]
