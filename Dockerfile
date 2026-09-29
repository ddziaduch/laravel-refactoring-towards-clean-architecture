FROM php:8.4-cli-bookworm

ARG INSTALL_XDEBUG=true

RUN apt-get update \
    && apt-get install -y --no-install-recommends git libpq-dev libzip-dev unzip \
    && docker-php-ext-install pdo_pgsql pcntl zip \
    && if [ "$INSTALL_XDEBUG" = "true" ]; then pecl install xdebug && docker-php-ext-enable xdebug; fi \
    && apt-get clean \
    && rm -rf /var/lib/apt/lists/*

COPY --from=composer:2 /usr/bin/composer /usr/bin/composer

WORKDIR /opt/project

COPY composer.json composer.lock ./
RUN composer install --no-interaction --prefer-dist --no-scripts --no-autoloader

COPY . .
RUN composer install --no-interaction --prefer-dist --optimize-autoloader

EXPOSE 8000

CMD ["php", "artisan", "serve", "--host=0.0.0.0", "--port=8000"]
