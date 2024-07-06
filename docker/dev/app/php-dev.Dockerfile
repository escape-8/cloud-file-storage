FROM php:8.2-fpm

WORKDIR /var/www/html

# Arguments defined in docker-compose.yml
ARG user
ARG uid

COPY --from=composer:latest /usr/bin/composer /usr/bin/composer

RUN apt-get update && apt-get install -y \
      apt-utils \
      libpng-dev \
      curl \
      nano \
      libzip-dev \
      zip unzip && \
      docker-php-ext-install pdo_mysql  && \
      docker-php-ext-install bcmath && \
      docker-php-ext-install gd && \
      docker-php-ext-install zip && \
      apt-get clean && \
      rm -rf /var/lib/apt/lists/* /tmp/* /var/tmp/*

RUN pecl install xdebug

# Create system user to run Composer and Artisan Commands
RUN useradd -G www-data,root -u $uid -d /home/$user $user

USER $user