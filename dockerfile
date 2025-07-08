# Multi-stage build pour optimiser l'image de production
FROM php:8.2-apache AS base

# Installation des extensions PHP nécessaires
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

# Installation de Composer
COPY --from=composer:latest /usr/bin/composer /usr/bin/composer

# Configuration Apache pour CesiZen
RUN echo '<VirtualHost *:80>' > /etc/apache2/sites-available/cesizen.conf \
    && echo '    DocumentRoot /var/www/html/vues' >> /etc/apache2/sites-available/cesizen.conf \
    && echo '    <Directory /var/www/html/vues>' >> /etc/apache2/sites-available/cesizen.conf \
    && echo '        AllowOverride All' >> /etc/apache2/sites-available/cesizen.conf \
    && echo '        Require all granted' >> /etc/apache2/sites-available/cesizen.conf \
    && echo '        DirectoryIndex index.php' >> /etc/apache2/sites-available/cesizen.conf \
    && echo '    </Directory>' >> /etc/apache2/sites-available/cesizen.conf \
    && echo '    <Directory /var/www/html/api>' >> /etc/apache2/sites-available/cesizen.conf \
    && echo '        AllowOverride All' >> /etc/apache2/sites-available/cesizen.conf \
    && echo '        Require all granted' >> /etc/apache2/sites-available/cesizen.conf \
    && echo '    </Directory>' >> /etc/apache2/sites-available/cesizen.conf \
    && echo '</VirtualHost>' >> /etc/apache2/sites-available/cesizen.conf \
    && a2dissite 000-default \
    && a2ensite cesizen \
    && a2enmod rewrite

# Configuration PHP pour la production
RUN echo "display_errors = Off" >> /usr/local/etc/php/conf.d/docker-php-prod.ini \
    && echo "error_reporting = E_ERROR | E_WARNING | E_PARSE" >> /usr/local/etc/php/conf.d/docker-php-prod.ini \
    && echo "upload_max_filesize = 64M" >> /usr/local/etc/php/conf.d/docker-php-prod.ini \
    && echo "post_max_size = 64M" >> /usr/local/etc/php/conf.d/docker-php-prod.ini \
    && echo "max_execution_time = 300" >> /usr/local/etc/php/conf.d/docker-php-prod.ini \
    && echo "memory_limit = 256M" >> /usr/local/etc/php/conf.d/docker-php-prod.ini \
    && echo "session.cookie_secure = On" >> /usr/local/etc/php/conf.d/docker-php-prod.ini \
    && echo "session.cookie_httponly = On" >> /usr/local/etc/php/conf.d/docker-php-prod.ini

# Stage pour le développement avec Xdebug
FROM base AS development

RUN pecl install xdebug \
    && docker-php-ext-enable xdebug \
    && echo "xdebug.mode=coverage,debug" >> /usr/local/etc/php/conf.d/docker-php-xdebug.ini \
    && echo "display_errors = On" >> /usr/local/etc/php/conf.d/docker-php-dev.ini \
    && echo "error_reporting = E_ALL" >> /usr/local/etc/php/conf.d/docker-php-dev.ini

# Stage de production
FROM base AS production

# Copie du code source
COPY . /var/www/html/

# Installation des dépendances Composer (production seulement)
RUN composer install --no-dev --optimize-autoloader --no-scripts --no-interaction

# Configuration des permissions
RUN chown -R www-data:www-data /var/www/html \
    && chmod -R 755 /var/www/html \
    && chmod -R 777 /var/www/html/uploads 2>/dev/null || mkdir -p /var/www/html/uploads && chmod -R 777 /var/www/html/uploads

# Suppression des fichiers sensibles et inutiles en production
RUN rm -rf /var/www/html/.git \
    && rm -rf /var/www/html/.github \
    && rm -rf /var/www/html/tests \
    && rm -f /var/www/html/.gitignore \
    && rm -f /var/www/html/README.md \
    && rm -f /var/www/html/docker-compose.yml \
    && rm -f /var/www/html/phpunit.xml

# Exposition du port
EXPOSE 80

# Configuration du healthcheck
HEALTHCHECK --interval=30s --timeout=3s --start-period=5s --retries=3 \
    CMD curl -f http://localhost/index.php || exit 1

# Point d'entrée
CMD ["apache2-foreground"]