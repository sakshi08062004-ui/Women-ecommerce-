FROM php:8.2-apache

# Install MySQL support for PHP
RUN docker-php-ext-install pdo pdo_mysql

# Enable Apache mod_rewrite
RUN a2enmod rewrite

# Copy project files into container
COPY . /var/www/html/
