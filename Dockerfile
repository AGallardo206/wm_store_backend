FROM php:8.1.0-apache

# Set working directory
WORKDIR /var/www/html

# Enable Apache mod_rewrite
RUN a2enmod rewrite

# Install necessary Linux libraries and PHP extensions
RUN apt-get update && apt-get install -y \
    libicu-dev \
    libpq-dev \
    unzip \
    zip \
    zlib1g-dev \
    libpng-dev \
    libjpeg-dev \
    libfreetype6-dev \
    && docker-php-ext-configure gd --with-freetype --with-jpeg \
    && docker-php-ext-install -j$(nproc) gd gettext intl pdo_pgsql \
    && apt-get clean && rm -rf /var/lib/apt/lists/*

# Habilitar driver de PostgreSQL
RUN echo "extension=pdo_pgsql" >> /usr/local/etc/php/conf.d/docker-php-ext-pgsql.ini

# Verificar si el driver de PostgreSQL está habilitado
RUN php -m | grep pgsql

# Copy application files
COPY . .

# Install Composer
COPY --from=composer:latest /usr/bin/composer /usr/bin/composer

# Install dependencies with Composer
RUN composer install --ignore-platform-reqs --no-dev --no-scripts --verbose

# Generate autoload.php
RUN composer dump-autoload

RUN php artisan route:clear

# Generar claves de Passport
RUN php artisan passport:keys --force

RUN php artisan vendor:publish --tag=passport-config

RUN php artisan storage:link

# Configure Apache to use the public directory
RUN sed -i 's|DocumentRoot /var/www/html|DocumentRoot /var/www/html/public|g' /etc/apache2/sites-available/000-default.conf

# Set ServerName to suppress warning
RUN echo "ServerName localhost" >> /etc/apache2/apache2.conf

# Set permissions for the application
RUN chown -R www-data:www-data /var/www/html

# Enable DirectoryIndex in Apache configuration
RUN sed -i 's/# Options Indexes/Options Indexes/g' /etc/apache2/apache2.conf \
    && echo "DirectoryIndex index.php index.html" >> /etc/apache2/apache2.conf

# Verify Apache configuration
RUN apachectl configtest

# Expose port 80 (for HTTP)
EXPOSE 8100

# Specify the entry command
CMD ["apache2-foreground"]
