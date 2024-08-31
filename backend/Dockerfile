FROM php:8.2-fpm

# Instal·lar les dependències del sistema
RUN apt-get update && apt-get install -y \
    libpng-dev \
    libjpeg62-turbo-dev \
    libfreetype6-dev \
    zip \
    unzip \
    git \
    curl \
    libssl-dev \
    pkg-config \
    libcurl4-openssl-dev \
    libonig-dev \
    libxml2-dev \
    libzip-dev

# Instal·la les extensions PHP necessàries
RUN docker-php-ext-configure gd --with-freetype --with-jpeg \
    && docker-php-ext-install -j$(nproc) pdo pdo_mysql mbstring exif pcntl bcmath gd zip

# Instal·la Composer
COPY --from=composer:latest /usr/bin/composer /usr/bin/composer

# Instal·lar l'extensió de MongoDB
RUN pecl install mongodb && docker-php-ext-enable mongodb

# Estableix el directori de treball
WORKDIR /var/www

# Copia els arxius del projecte
COPY . .

# Instal·la les dependències del projecte
RUN composer install

# Dona permisos
RUN chown -R www-data:www-data /var/www

EXPOSE 9000

CMD ["php-fpm"]
