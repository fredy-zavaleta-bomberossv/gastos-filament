FROM php:8.3-fpm

# Instalar extensiones necesarias
RUN apt-get update && apt-get install -y \
    git \
    unzip \
    libicu-dev \
    libzip-dev \
    zip \
    && docker-php-ext-install intl zip pdo pdo_mysql


    # Instalar Node y NPM para Vite
RUN apt-get update && apt-get install -y nodejs npm

# Instalar dependencias JS
RUN npm install

# Construir assets de Vite
RUN npm run build


# Instalar Composer
COPY --from=composer:2 /usr/bin/composer /usr/bin/composer

# Establecer directorio de trabajo
WORKDIR /var/www

# Copiar proyecto
COPY . .

# Instalar dependencias de PHP
RUN composer install --no-dev --optimize-autoloader

# Ajustar permisos
RUN chown -R www-data:www-data storage bootstrap/cache

# Ejecutar migraciones
RUN php artisan migrate --force

# Crear usuario de Filament (ya será admin por defecto)
RUN php artisan make:filament-user \
        --name="Fredy Zavaleta" \
        --email="fredyzavaleta@bomberos.gob.sv" \
        --password="zavaleta"

# Exponer puerto
EXPOSE 8000

# Comando para iniciar el servidor
CMD php artisan serve --host=0.0.0.0 --port=8000

