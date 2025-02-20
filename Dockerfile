FROM php:7.4-cli

RUN apt-get update && apt-get install -y git libzip-dev zip unzip
RUN docker-php-ext-install pdo zip
RUN pecl channel-update pecl.php.net
# RUN pecl install xdebug-3.1.5
# RUN docker-php-ext-enable xdebug

COPY --from=composer:2 /usr/bin/composer /usr/bin/composer

# default in the JetBrains IDE
WORKDIR /opt/project

COPY . .

RUN composer install

EXPOSE 8000

CMD ["php", "artisan", "serve", "--port", "8000"]
