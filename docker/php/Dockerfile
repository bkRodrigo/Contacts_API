FROM php:7.4-fpm

# Install system dependencies
RUN apt-get update && apt-get install -y \
    apt-utils \
    git \
    curl \
    libpng-dev \
    libonig-dev \
    libxml2-dev \
    zip \
    unzip

# Clear cache
RUN apt-get clean && rm -rf /var/lib/apt/lists/*

# Install PHP extensions
RUN docker-php-ext-install pdo_mysql mbstring exif pcntl bcmath

# Get latest Composer
COPY --from=composer:latest /usr/bin/composer /usr/bin/composer

# Create system user to run Composer and Artisan Commands
RUN useradd -G www-data,root -u ${APP_UID} -d /home/${APP_USER} ${APP_USER}
RUN mkdir -p /home/${APP_USER}/.composer && \
    chown -R ${APP_USER}:${APP_USER} /home/${APP_USER}

# Set working directory
WORKDIR /var/www

USER ${APP_USER}
