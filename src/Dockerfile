FROM php:8.3-apache

RUN apt-get update && apt-get install -y curl zip unzip && rm -rf /var/lib/apt/lists/*
RUN a2enmod rewrite
RUN curl -sS https://getcomposer.org/installer | php -- --install-dir=/usr/local/bin --filename=composer

COPY php.ini /usr/local/etc/php/
COPY entrypoint.sh /usr/local/bin/entrypoint.sh
RUN chmod +x /usr/local/bin/entrypoint.sh

WORKDIR /var/www/html
EXPOSE 80
ENTRYPOINT ["/usr/local/bin/entrypoint.sh"]
