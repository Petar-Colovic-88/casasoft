FROM php:8.1-fpm-alpine

# Install dependencies
RUN apk update && apk upgrade && \
    apk --no-cache --update add postgresql-dev && \
    rm -rf /tmp/* && \
    rm -rf /var/cache/apk/*

# Build and install PHP extensions
RUN docker-php-ext-install pdo pdo_pgsql

# Install xdebug
RUN apk add --no-cache --virtual .build-deps $PHPIZE_DEPS && \
    pecl install xdebug && \
    docker-php-ext-enable xdebug && \
    apk del -f .build-deps && \
    echo "xdebug.mode=debug" >> /usr/local/etc/php/conf.d/docker-php-ext-xdebug.ini && \
    echo "xdebug.start_with_request=trigger" >> /usr/local/etc/php/conf.d/docker-php-ext-xdebug.ini && \
    echo "xdebug.discover_client_host=1" >> /usr/local/etc/php/conf.d/docker-php-ext-xdebug.ini;

# Install composer
RUN curl -sS https://getcomposer.org/installer | php -- --install-dir=/usr/local/bin --filename=composer