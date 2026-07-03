FROM php:7.4-apache

RUN apt-get update && apt-get install -y \
    unzip \
    zip \
    libzip-dev \
    && docker-php-ext-install mysqli pdo pdo_mysql zip \
    && a2enmod rewrite \
    && apt-get clean \
    && rm -rf /var/lib/apt/lists/*

WORKDIR /var/www/html

COPY . /var/www/html

RUN chown -R www-data:www-data /var/www/html

EXPOSE 80

CMD ["apache2-foreground"]
