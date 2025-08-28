FROM php:8.1.33-apache 
WORKDIR /var/www/html 

# Mod Rewrite
RUN a2enmod rewrite