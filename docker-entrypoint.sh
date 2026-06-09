#!/bin/bash
set -e

# Update Apache Port to match Render's expected PORT
if [ -n "$PORT" ]; then
    sed -i "s/80/$PORT/g" /etc/apache2/sites-available/000-default.conf /etc/apache2/ports.conf
fi



# Run migrations and cache config if we are in production
if [ "$APP_ENV" = "production" ]; then
    echo "Running production setup..."
    php artisan config:cache
    php artisan route:cache
    php artisan view:cache
    php artisan migrate --force
    # If the database is completely empty and needs seeds, we could run db:seed,
    # but for safety, it's better to run it manually later or check if admin exists.
fi

# Fix permissions for storage and cache directories
if [ -d "storage" ]; then
    chmod -R 775 storage
    chown -R www-data:www-data storage
fi

if [ -d "bootstrap/cache" ]; then
    chmod -R 775 bootstrap/cache
    chown -R www-data:www-data bootstrap/cache
fi

# Execute the original command
exec "$@"
