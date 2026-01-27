# Imagen base de PHP con extensiones necesarias
FROM php:8.2-fpm

# Instalar dependencias del sistema
RUN apt-get update && apt-get install -y \
    git \
    unzip \
    curl \
    libpng-dev \
    libonig-dev \
    libxml2-dev \
    zip \
    nodejs \
    npm

# Instalar Composer
COPY --from=composer:2 /usr/bin/composer /usr/bin/composer

# Copiar archivos del proyecto
WORKDIR /var/www/html
COPY . .

# Instalar dependencias de Laravel
RUN composer install --no-dev --optimize-autoloader

# Instalar dependencias de Vite
RUN npm install && npm run build

# Generar clave de Laravel
RUN php artisan key:generate

# Exponer puerto
EXPOSE 80

# Comando de inicio
CMD php artisan migrate --force && php artisan serve --host=0.0.0.0 --port=80
