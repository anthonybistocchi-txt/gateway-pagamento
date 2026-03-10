FROM php:8.2-cli

RUN apt-get update -y && apt-get install -y \
    libmariadb-dev \
    unzip \
    git

RUN docker-php-ext-install pdo_mysql

COPY --from=composer:latest /usr/bin/composer /usr/bin/composer

WORKDIR /app

CMD php artisan serve --host=0.0.0.0 --port=8000