FROM node:latest as frontendDeps
WORKDIR /app
COPY resources /app/resources
COPY ./vite.config.js ./vite.config.js
RUN --mount=type=bind,source=package.json,target=package.json \
    --mount=type=bind,source=package-lock.json,target=package-lock.json,readonly=false \
    --mount=type=cache,target=/tmp/npm-cache \
    npm install && npm run build

FROM php:8.1.28-fpm

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

# Create system user to run Composer and Artisan Commands
RUN useradd -G www-data,root -u $uid -d /home/$user $user

USER $user

COPY --from=frontendDeps --chown=$user:www-data /app/public/ /var/www/html/public/
COPY --chown=$user:www-data ./ /var/www/html

RUN composer install --no-dev --no-interaction --optimize-autoloader



ENTRYPOINT ["/tmp/docker-php-entrypoint/php-init.sh"]
