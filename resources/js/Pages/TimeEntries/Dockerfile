# Estágio 1: Node.js para compilar assets (Inertia/Vite)
FROM node:20-alpine AS assets-builder
WORKDIR /app
COPY package*.json ./
RUN npm install
COPY . .
RUN npm run build

# Estágio 2: PHP-FPM para a aplicação
FROM php:8.3-fpm-alpine

# Instalar dependências do sistema
RUN apk add --no-cache \
    libpng-dev \
    libjpeg-turbo-dev \
    freetype-dev \
    zip \
    libzip-dev \
    unzip \
    git \
    oniguruma-dev \
    icu-dev

# Instalar extensões do PHP necessárias para Laravel
RUN docker-php-ext-configure gd --with-freetype --with-jpeg \
    && docker-php-ext-install gd pdo pdo_mysql mbstring zip bcmath intl

# Copiar Composer
COPY --from=composer:latest /usr/bin/composer /usr/bin/composer

WORKDIR /var/www

# Copiar código da aplicação
COPY . .

# Copiar assets compilados do estágio anterior
COPY --from=assets-builder /app/public/build ./public/build

# Instalar dependências do PHP (sem dev para produção)
RUN composer install --no-dev --optimize-autoloader

# Permissões para o Laravel
RUN chown -R www-data:www-data /var/www/storage /var/www/bootstrap/cache

EXPOSE 9000
CMD ["php-fpm"]
