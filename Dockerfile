FROM php:8.3-apache

# Instalar dependencias del sistema
RUN apt-get update && apt-get install -y \
    libpng-dev \
    libonig-dev \
    libxml2-dev \
    zip \
    unzip \
    git \
    curl

# Limpiar cache de apt
RUN apt-get clean && rm -rf /var/lib/apt/lists/*

# Instalar extensiones de PHP necesarias para Laravel
RUN docker-php-ext-install pdo_mysql mbstring exif pcntl bcmath gd

# Habilitar mod_rewrite de Apache para las URLs amigables de Laravel
RUN a2enmod rewrite

# Cambiar el DocumentRoot de Apache a la carpeta public/ de Laravel
ENV APACHE_DOCUMENT_ROOT /var/www/html/public
RUN sed -ri -e 's!/var/www/html!${APACHE_DOCUMENT_ROOT}!g' /etc/apache2/sites-available/*.conf
RUN sed -ri -e 's!/var/www/!${APACHE_DOCUMENT_ROOT}!g' /etc/apache2/apache2.conf /etc/apache2/conf-available/*.conf

# Directorio de trabajo
WORKDIR /var/www/html