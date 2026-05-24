#!/bin/bash
set -e

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

