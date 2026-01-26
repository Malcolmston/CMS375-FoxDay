FROM php:8.2-cli
LABEL authors="malcolmstone"

WORKDIR /usr/src/myapp

RUN docker-php-ext-install pdo_mysql

COPY . .

EXPOSE 8000
CMD [ "php", "-S", "0.0.0.0:8000", "-t", "/usr/src/myapp" ]
