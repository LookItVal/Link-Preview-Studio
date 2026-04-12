FROM dunglas/frankenphp

# Grab the composer binary from the official composer image
COPY --from=composer:latest /usr/bin/composer /usr/bin/composer

# Install essential PHP extensions for Laravel
RUN install-php-extensions pcntl bcmath gd intl zip opcache

WORKDIR /app

# Copy the application code
COPY . /app

# Set production environment variables
ENV APP_ENV=production
ENV APP_DEBUG=false
ENV LOG_CHANNEL=stderr

# Install production dependencies
RUN composer install --no-dev --optimize-autoloader

# Create the SQLite file so the app doesn't crash on start
RUN touch /app/database/database.sqlite

# Ensure Octane is installed and the FrankenPHP binary is present
RUN php artisan octane:install --server=frankenphp

# Optimized permissions for the web user
RUN chown -R www-data:www-data /app/storage /app/bootstrap/cache /app/database

# Clear and cache the config for speed
RUN php artisan route:cache && php artisan view:cache

# Start FrankenPHP in worker mode
ENTRYPOINT ["php", "artisan", "octane:frankenphp", "--host=0.0.0.0", "--port=80", "--http"]