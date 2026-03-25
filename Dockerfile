FROM php:8.4.12-apache 
WORKDIR /var/www/html 

COPY php.ini /usr/local/etc/php/conf.d/php.ini

# Install Composer
COPY --from=composer:2 /usr/bin/composer /usr/bin/composer

# Install ZIP support for Composer
RUN apt-get update && apt-get install -y \
    zip \
    unzip \
    libzip-dev \
    && docker-php-ext-install zip

# Copy composer files FIRST
COPY composer.json /var/www/html/

# Install dependencies (vendor/)
RUN composer install 

# Install PHPStan
RUN composer require --dev phpstan/phpstan 

COPY ./src/ /var/www/html/

# Mod Rewrite
RUN a2enmod rewrite 

# Install system dependencies for GD
RUN apt-get update && apt-get install -y \
    libfreetype6-dev \
    libjpeg62-turbo-dev \
    libpng-dev \
    && docker-php-ext-configure gd --with-freetype --with-jpeg \
    && docker-php-ext-install -j$(nproc) gd

#Install dependencies 
RUN docker-php-ext-install mysqli pdo pdo_mysql && docker-php-ext-enable mysqli pdo pdo_mysql 

RUN usermod -u 1000 www-data
RUN groupmod -g 1000 www-data

# create user non-root
# RUN adduser --disabled-password --gecos "" appuser

# # Set Apache to use the user appuser
# RUN sed -i 's/www-data/appuser/g' /etc/apache2/envvars

# # Set the user for subsequent commands
# USER appuser