# Base image
FROM php:8.2.5-fpm

# Copy composer files to var/www
COPY composer.lock composer.json /var/www/

# Set working directory
WORKDIR /var/www

# Install common dependencies
RUN apt-get update \
    && apt-get install -y \
        libzip-dev \
        zip \
        unzip \
        curl \
        git \
    && apt-get clean \
    && rm -rf /var/lib/apt/lists/*

# Clear cache
RUN apt-get clean && rm -rf /var/lib/apt/lists/*

# Install common php extensions
RUN docker-php-ext-install pdo_mysql zip

# Install composer
RUN curl -sS https://getcomposer.org/installer | php -- --install-dir=/usr/local/bin --filename=composer

# Copy application directory contents
COPY . /var/www/html

# Set appropriate permissions
RUN chown -R www-data:www-data /var/www/html

# Copy entrypoint script
COPY docker/entrypoint.sh /usr/bin/
RUN chmod +x /usr/bin/entrypoint.sh

# Expose port 9000
EXPOSE 9000

# Run php-fpm
#CMD ["php-fpm"]
