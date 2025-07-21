# Utilise l'image officielle PHP avec Apache
FROM php:8.2-apache

# Installation des extensions PHP nécessaires pour CesiZen
RUN apt-get update && apt-get install -y \
    libzip-dev \
    libpng-dev \
    libjpeg-dev \
    libfreetype6-dev \
    libonig-dev \
    libxml2-dev \
    unzip \
    git \
    curl \
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

# Active le module de réécriture Apache
RUN a2enmod rewrite

# Copie le code source dans le répertoire web d'Apache
COPY . /var/www/html/

# Configuration Apache - MÉTHODE SIMPLIFIÉE
RUN echo '<VirtualHost *:80>' > /etc/apache2/sites-available/000-default.conf \
    && echo '    DocumentRoot /var/www/html/vues' >> /etc/apache2/sites-available/000-default.conf \
    && echo '    ServerName localhost' >> /etc/apache2/sites-available/000-default.conf \
    && echo '    <Directory /var/www/html/vues>' >> /etc/apache2/sites-available/000-default.conf \
    && echo '        AllowOverride All' >> /etc/apache2/sites-available/000-default.conf \
    && echo '        Require all granted' >> /etc/apache2/sites-available/000-default.conf \
    && echo '        DirectoryIndex index.php' >> /etc/apache2/sites-available/000-default.conf \
    && echo '        Options Indexes FollowSymLinks' >> /etc/apache2/sites-available/000-default.conf \
    && echo '    </Directory>' >> /etc/apache2/sites-available/000-default.conf \
    && echo '    Alias /api /var/www/html/api' >> /etc/apache2/sites-available/000-default.conf \
    && echo '    <Directory /var/www/html/api>' >> /etc/apache2/sites-available/000-default.conf \
    && echo '        AllowOverride All' >> /etc/apache2/sites-available/000-default.conf \
    && echo '        Require all granted' >> /etc/apache2/sites-available/000-default.conf \
    && echo '    </Directory>' >> /etc/apache2/sites-available/000-default.conf \
    && echo '    Alias /assets /var/www/html/assets' >> /etc/apache2/sites-available/000-default.conf \
    && echo '    <Directory /var/www/html/assets>' >> /etc/apache2/sites-available/000-default.conf \
    && echo '        AllowOverride All' >> /etc/apache2/sites-available/000-default.conf \
    && echo '        Require all granted' >> /etc/apache2/sites-available/000-default.conf \
    && echo '    </Directory>' >> /etc/apache2/sites-available/000-default.conf \
    && echo '    ErrorLog ${APACHE_LOG_DIR}/error.log' >> /etc/apache2/sites-available/000-default.conf \
    && echo '    CustomLog ${APACHE_LOG_DIR}/access.log combined' >> /etc/apache2/sites-available/000-default.conf \
    && echo '</VirtualHost>' >> /etc/apache2/sites-available/000-default.conf

# Configuration PHP optimisée pour CesiZen
RUN echo "; Configuration PHP pour CesiZen" > /usr/local/etc/php/conf.d/cesizen.ini \
    && echo "display_errors = On" >> /usr/local/etc/php/conf.d/cesizen.ini \
    && echo "error_reporting = E_ALL" >> /usr/local/etc/php/conf.d/cesizen.ini \
    && echo "log_errors = On" >> /usr/local/etc/php/conf.d/cesizen.ini \
    && echo "upload_max_filesize = 64M" >> /usr/local/etc/php/conf.d/cesizen.ini \
    && echo "post_max_size = 64M" >> /usr/local/etc/php/conf.d/cesizen.ini \
    && echo "max_execution_time = 300" >> /usr/local/etc/php/conf.d/cesizen.ini \
    && echo "memory_limit = 256M" >> /usr/local/etc/php/conf.d/cesizen.ini \
    && echo "max_input_vars = 3000" >> /usr/local/etc/php/conf.d/cesizen.ini \
    && echo "session.gc_maxlifetime = 3600" >> /usr/local/etc/php/conf.d/cesizen.ini \
    && echo "date.timezone = Europe/Paris" >> /usr/local/etc/php/conf.d/cesizen.ini

# Installation des dépendances Composer si composer.json existe
RUN if [ -f /var/www/html/composer.json ]; then \
        cd /var/www/html && \
        composer install --optimize-autoloader --no-scripts --no-interaction --no-dev; \
    fi

# Création des dossiers nécessaires avec les bonnes permissions
RUN mkdir -p /var/www/html/uploads \
    && mkdir -p /var/www/html/logs \
    && mkdir -p /var/www/html/cache \
    && chown -R www-data:www-data /var/www/html \
    && chmod -R 755 /var/www/html \
    && chmod -R 777 /var/www/html/uploads \
    && chmod -R 777 /var/www/html/logs \
    && chmod -R 777 /var/www/html/cache

# Configuration du healthcheck pour vérifier que l'application fonctionne
HEALTHCHECK --interval=30s --timeout=10s --start-period=30s --retries=3 \
    CMD curl -f http://localhost/ || exit 1

# Exposition du port 80
EXPOSE 80

# Variables d'environnement par défaut
ENV DB_HOST=database
ENV DB_NAME=cesizentest
ENV DB_USER=cesizen
ENV DB_PASS=cesizen123

# Point d'entrée
CMD ["apache2-foreground"]