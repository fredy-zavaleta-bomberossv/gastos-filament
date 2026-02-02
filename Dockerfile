FROM php:8.3-fpm

# ----------------------------
# Instalar extensiones necesarias y Node.js/NPM
# ----------------------------
RUN apt-get update && apt-get install -y \
    git \
    unzip \
    libicu-dev \
    libzip-dev \
    zip \
    nodejs \
    npm \
    && docker-php-ext-install intl zip pdo pdo_mysql

# ----------------------------
# Establecer directorio de trabajo
# ----------------------------
WORKDIR /var/www

# ----------------------------
# Copiar proyecto
# ----------------------------
COPY . .

# ----------------------------
# Instalar dependencias PHP
# ----------------------------
RUN composer install --no-dev --optimize-autoloader

# ----------------------------
# Instalar dependencias JS y construir assets Vite
# ----------------------------
RUN npm install
RUN npm run build

# ----------------------------
# Ajustar permisos de almacenamiento y cache
# ----------------------------
RUN chown -R www-data:www-data storage bootstrap/cache

# ----------------------------
# Ejecutar migraciones y seeders
# ----------------------------
RUN php artisan migrate --force
RUN php artisan db:seed --force

# ----------------------------
# Crear usuario de Filament (admin por defecto)
# ----------------------------
RUN php artisan make:filament-user \
        --name="Fredy Zavaleta" \
        --email="fredyzavaleta@bomberos.gob.sv" \
        --password="zavaleta"

# ----------------------------
# Exponer puerto
# ----------------------------
EXPOSE 8000

# ----------------------------
# Servir la app (solo desarrollo)
# En producción se recomienda usar Nginx + PHP-FPM
# ----------------------------
CMD php artisan serve --host=0.0.0.0 --port=8000

