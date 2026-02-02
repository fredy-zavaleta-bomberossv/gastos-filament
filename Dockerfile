# ----------------------------
# Etapa 1: Construcción
# ----------------------------
FROM node:20-bullseye AS build

WORKDIR /var/www

# Copiar package.json y package-lock.json primero para cache de Docker
COPY package*.json ./

# Instalar dependencias JS
RUN npm install

# Copiar todo el proyecto
COPY . .

# Construir assets de Vite
RUN npm run build

# ----------------------------
# Etapa 2: Producción PHP-FPM + Nginx
# ----------------------------
FROM php:8.3-fpm

# Instalar extensiones PHP necesarias y Nginx
RUN apt-get update && apt-get install -y \
    git \
    unzip \
    libicu-dev \
    libzip-dev \
    zip \
    nginx \
    && docker-php-ext-install intl zip pdo pdo_mysql

# Instalar Composer
COPY --from=composer:2 /usr/bin/composer /usr/bin/composer

# Establecer directorio de trabajo
WORKDIR /var/www

# Copiar proyecto y assets compilados desde etapa build
COPY --from=build /var/www /var/www

# Instalar dependencias PHP
RUN composer install --no-dev --optimize-autoloader

# Ajustar permisos
RUN chown -R www-data:www-data storage bootstrap/cache public/build

# Ejecutar migraciones y seeders
RUN php artisan migrate --force
RUN php artisan db:seed --force

# Crear usuario de Filament admin
RUN php artisan make:filament-user \
        --name="Fredy Zavaleta" \
        --email="fredyzavaleta@bomberos.gob.sv" \
        --password="zavaleta"

# Configurar Nginx
RUN rm /etc/nginx/sites-enabled/default
COPY ./nginx.conf /etc/nginx/sites-available/filament
RUN ln -s /etc/nginx/sites-available/filament /etc/nginx/sites-enabled/

# Exponer puerto 80
EXPOSE 80

# Comando para iniciar PHP-FPM y Nginx
CMD service nginx start && php-fpm


