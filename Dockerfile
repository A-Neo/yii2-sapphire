# Установка базового образа
FROM php:7.4-apache

# Установка расширений PHP
RUN docker-php-ext-install pdo_mysql

# Включение модуля Apache Rewrite
RUN a2enmod rewrite

# Копирование приложения Yii2 в контейнер
COPY . /var/www/html/

# Установка Composer
COPY --from=composer:latest /usr/bin/composer /usr/bin/composer

# Установка зависимостей через Composer
RUN composer install

# Настройка прав доступа
RUN chown -R www-data:www-data /var/www/html/
