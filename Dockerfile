FROM php:8.3-cli-alpine

ARG INSTALL_DEV=true

RUN apk add --no-cache \
    git \
    icu-dev \
    libzip-dev \
    oniguruma-dev \
    linux-headers \
    $PHPIZE_DEPS \
    && docker-php-ext-configure intl \
    && docker-php-ext-install -j$(nproc) intl pdo_mysql bcmath opcache \
    && apk del $PHPIZE_DEPS

COPY --from=composer:2 /usr/bin/composer /usr/bin/composer

WORKDIR /var/www/html

COPY composer.json composer.lock ./

RUN if [ "$INSTALL_DEV" = "false" ]; then \
      composer install --no-dev --no-interaction --no-scripts --prefer-dist; \
    else \
      composer install --no-interaction --no-scripts --prefer-dist; \
    fi

COPY . .

RUN composer dump-autoload --optimize

ENV APP_ENV=local

EXPOSE 8000

CMD ["sh", "-c", "php artisan migrate --force && php artisan db:seed --force && php artisan serve --host=0.0.0.0 --port=8000"]
