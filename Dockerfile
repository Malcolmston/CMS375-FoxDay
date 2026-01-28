FROM php:8.4-cli AS vendor
WORKDIR /app
RUN apt-get update \
    && apt-get install -y --no-install-recommends curl git unzip \
    && rm -rf /var/lib/apt/lists/*
RUN curl -sS https://getcomposer.org/installer | php -- --install-dir=/usr/local/bin --filename=composer
COPY composer.json composer.lock ./
RUN composer install --no-dev --prefer-dist --no-interaction --no-progress
COPY . .

FROM php:8.4-cli
WORKDIR /usr/src/myapp
RUN apt-get update \
    && apt-get install -y --no-install-recommends iputils-ping \
    && rm -rf /var/lib/apt/lists/*
RUN docker-php-ext-install pdo_mysql

COPY --from=vendor /app /usr/src/myapp

EXPOSE 8000
CMD [ "php", "-S", "0.0.0.0:8000", "-t", "/usr/src/myapp" ]
