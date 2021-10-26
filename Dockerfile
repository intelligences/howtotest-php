FROM php:7.4-cli

ARG USER_ID=1000
ARG GROUP_ID=1000

RUN apt-get update && apt-get install -y --no-install-recommends \
    apt-utils \
    zip \
    unzip \
    ssh \
    g++ \
    git \
    curl \
    libcurl4-gnutls-dev \
    libpq-dev \
    libicu-dev

RUN docker-php-ext-install \
    intl \
    curl \
    bcmath \
    gettext

RUN curl -sS https://getcomposer.org/installer | php \
    && mv composer.phar /usr/local/bin/ \
    && ln -s /usr/local/bin/composer.phar /usr/local/bin/composer

WORKDIR /app

RUN usermod -u ${USER_ID} www-data
RUN chown -R www-data:www-data /app /var/www

USER "${USER_ID}:${GROUP_ID}"

CMD php-fpm -F
