FROM php:8.1.33-apache 
WORKDIR /var/www/html 

# Mod Rewrite
RUN a2enmod rewrite

#Install dependencies 
RUN docker-php-ext-install mysqli pdo pdo_mysql && docker-php-ext-enable mysqli pdo pdo_mysql