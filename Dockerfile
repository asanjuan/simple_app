FROM php:8.2-apache

# Instalar dependencias necesarias
RUN apt-get update && apt-get install -y \
    git \
    unzip \
    zip \
    libpng-dev \
    libjpeg-dev \
    libonig-dev \
    libxml2-dev \
    libzip-dev \
    curl \
    && docker-php-ext-install mysqli pdo pdo_mysql mbstring xml gd zip

# Habilitar mod_rewrite
RUN a2enmod rewrite

# Clonar solo la carpeta html del repositorio
COPY html/ /var/www/html/

# Dar propiedad a Apache
RUN chown -R www-data:www-data /var/www/html \
    && find /var/www/html -type d -exec chmod 775 {} \; \
    && find /var/www/html -type f -exec chmod 664 {} \;

WORKDIR /var/www/html
