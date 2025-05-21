FROM php:8.3-fpm

# Set working directory
WORKDIR /var/www

# Install dependencies
RUN apt-get update && apt-get install -y \
    build-essential \
    libpng-dev \
    libzip-dev \
    libpq-dev \
    libjpeg62-turbo-dev \
    libfreetype6-dev \
    locales \
    zip \
    unzip \
    git \
    curl \
    libonig-dev \
    libxml2-dev \
    postgresql-client

# Clear cache
RUN apt-get clean && rm -rf /var/lib/apt/lists/*

# Install PHP extensions
# Configure and install GD extension with freetype and jpeg support
RUN docker-php-ext-configure gd --with-freetype --with-jpeg \
    && docker-php-ext-install -j$(nproc) gd \
    && docker-php-ext-install pdo_pgsql mbstring exif pcntl bcmath zip xml

# Increase memory limit and other PHP settings
RUN echo "memory_limit=512M" > /usr/local/etc/php/conf.d/memory-limit.ini \
    && echo "upload_max_filesize=20M" > /usr/local/etc/php/conf.d/uploads.ini \
    && echo "post_max_size=20M" >> /usr/local/etc/php/conf.d/uploads.ini

# Install Composer
COPY --from=composer:latest /usr/bin/composer /usr/bin/composer

# Configure Git for safer directory usage
RUN git config --global --add safe.directory /var/www

# Create composer cache directory
RUN mkdir -p /.composer/cache && chmod -R 777 /.composer

# Copy docker entrypoint
COPY docker/php/docker-entrypoint.sh /usr/local/bin/docker-entrypoint
RUN chmod +x /usr/local/bin/docker-entrypoint

# Copy existing application directory contents
COPY . /var/www

# Set proper permissions
RUN chown -R www-data:www-data /var/www
RUN chmod -R 755 /var/www/public
RUN mkdir -p /var/www/var && chmod -R 777 /var/www/var

# Change current user to www-data
USER www-data

# Expose port 9000
EXPOSE 9000

# Define the entrypoint
ENTRYPOINT ["docker-entrypoint"]

# Start PHP-FPM
CMD ["php-fpm"]
