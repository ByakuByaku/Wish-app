#!/bin/bash

if [ ! -f /var/www/html/server/database/database.sqlite ]; then
    echo "Инициализация БД..."
    touch /var/www/html/server/database/database.sqlite
    chmod 777 /var/www/html/server/database/database.sqlite
    php /var/www/html/server/database/init.php
else
    echo "БД уже существует"
fi

exec apache2-foreground
