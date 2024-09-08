FROM php:8.1-cli-alpine

WORKDIR /usr/src/relative

RUN apk --no-cache --update add \
    $PHPIZE_DEPS \
    curl \
    libpng-dev \
    libzip-dev \
    linux-headers

ENV USER=relative
ENV GROUPNAME=$USER
ENV UID=1000
ENV GID=1000

RUN addgroup --gid "$GID" "$GROUPNAME" \
 && adduser --disabled-password --gecos "" --no-create-home --ingroup "$GROUPNAME" --uid "$UID" $USER

# libpng-dev: need for gd
# linux-headers: need for xdebug
# libzip-dev: need for zip

RUN docker-php-ext-install gd zip \
 && pecl install xdebug \
 && docker-php-ext-enable xdebug

RUN mv "$PHP_INI_DIR/php.ini-development" "$PHP_INI_DIR/php.ini"

RUN curl -sS https://getcomposer.org/installer | php -- --install-dir=/usr/bin --filename=composer

ENTRYPOINT ["./.docker/php-cli/entrypoint.sh"]
