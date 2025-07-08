# Utilise l'image officielle PHP avec Apache
FROM php:8.2-apache

# Installation des extensions PHP couramment utilisées avec WAMP
RUN apt-get update && apt-get install -y \
    libzip-dev \
    libpng-dev \
    libjpeg-dev \
    libfreetype6-dev \
    libonig-dev \
    libxml2-dev \
    unzip \
    git \
    && docker-php-ext-configure gd --with-freetype --with-jpeg \
    && docker-php-ext-install \
    pdo \
    pdo_mysql \
    mysqli \
    mbstring \
    zip \
    exif \
    pcntl \
    bcmath \
    gd \
    && apt-get clean \
    && rm -rf /var/lib/apt/lists/*

# Active le module de réécriture Apache
RUN a2enmod rewrite

# Configuration PHP pour le développement (similaire à WAMP)
RUN echo "display_errors = On" >> /usr/local/etc/php/conf.d/docker-php-dev.ini \
    && echo "error_reporting = E_ALL" >> /usr/local/etc/php/conf.d/docker-php-dev.ini \
    && echo "upload_max_filesize = 64M" >> /usr/local/etc/php/conf.d/docker-php-dev.ini \
    && echo "post_max_size = 64M" >> /usr/local/etc/php/conf.d/docker-php-dev.ini \
    && echo "max_execution_time = 300" >> /usr/local/etc/php/conf.d/docker-php-dev.ini \
    && echo "memory_limit = 256M" >> /usr/local/etc/php/conf.d/docker-php-dev.ini

# Copie le code source dans le répertoire web d'Apache
COPY . /var/www/html/

# Définit les permissions appropriées
RUN chown -R www-data:www-data /var/www/html \
    && chmod -R 755 /var/www/html

# Expose le port 80
EXPOSE 80