FROM php:8.3-apache

# Включить mod_rewrite
RUN a2enmod rewrite

# Копировать php.ini конфиг
COPY ./php.ini /usr/local/etc/php/

# Установить рабочую директорию
WORKDIR /var/www/html

# Expose port 80
EXPOSE 80

# Start Apache
CMD ["apache2-foreground"]
