FROM php:8.5-apache AS final

RUN apt update && apt install zip unzip
RUN docker-php-ext-install pdo pdo_mysql

RUN mv "$PHP_INI_DIR/php.ini-development" "$PHP_INI_DIR/php.ini"

COPY .. /var/www/html

ENV APACHE_DOCUMENT_ROOT /var/www/html/public

RUN a2enmod rewrite

RUN sed -ri -e 's!/var/www/html!${APACHE_DOCUMENT_ROOT}!g' /etc/apache2/sites-available/*.conf
RUN sed -ri -e 's!/var/www/!${APACHE_DOCUMENT_ROOT}!g' /etc/apache2/apache2.conf /etc/apache2/conf-available/*.conf

RUN chown -R www-data /var/www/html/storage
RUN chgrp -R www-data /var/www/html/storage
