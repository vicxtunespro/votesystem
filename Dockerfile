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

RUN apt-get update && apt-get install -y \
    git unzip curl

RUN curl -sS https://getcomposer.org/installer | php \
    && mv composer.phar /usr/local/bin/composer

RUN chown -R www-data:www-data /var/www/html
RUN chown -R www-data:www-data /var/www/html/images
RUN chmod -R 775 /var/www/html/images

EXPOSE 80

CMD ["apache2-foreground"]
