FROM php:8.2-cli

RUN apt-get update && apt-get install -y \
    libpng-dev \
    libjpeg-dev \
    libfreetype6-dev \
    libzip-dev \
    unzip \
    git \
    && docker-php-ext-configure gd --with-freetype --with-jpeg \
    && docker-php-ext-install gd zip

COPY --from=composer:latest /usr/bin/composer /usr/bin/composer

WORKDIR /var/www

COPY . .

RUN composer install --no-interaction --prefer-dist
RUN docker-php-ext-install pdo pdo_mysql
RUN apt-get update && apt-get install -y default-mysql-client

EXPOSE 8000

CMD ["php", "-S", "0.0.0.0:8000", "-t", "public"]
