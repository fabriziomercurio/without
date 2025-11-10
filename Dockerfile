FROM php:8.4.12-apache 
WORKDIR /var/www/html 

COPY ./src/ /var/www/html/

# Mod Rewrite
RUN a2enmod rewrite

#Install dependencies 
RUN docker-php-ext-install mysqli pdo pdo_mysql && docker-php-ext-enable mysqli pdo pdo_mysql

# create user non-root
RUN adduser --disabled-password --gecos "" appuser

# Set Apache to use the user appuser
RUN sed -i 's/www-data/appuser/g' /etc/apache2/envvars

# Set the user for subsequent commands
USER appuser