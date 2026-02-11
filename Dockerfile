# Estágio 1: Node.js para compilar assets (Vite)
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

# Instalar extensões do PHP
RUN docker-php-ext-configure gd --with-freetype --with-jpeg \
    && docker-php-ext-install gd pdo pdo_mysql mbstring zip bcmath intl

COPY --from=composer:latest /usr/bin/composer /usr/bin/composer

WORKDIR /var/www

# Copia TUDO o que está na raiz (onde está o artisan, app, etc)
COPY . .

# Copia os assets que o Node acabou de gerar
COPY --from=assets-builder /app/public/build ./public/build

RUN composer install --no-dev --optimize-autoloader

# Garante permissões nas pastas de escrita
RUN chown -R www-data:www-data /var/www/storage /var/www/bootstrap/cache

EXPOSE 9000
CMD ["php-fpm"]
