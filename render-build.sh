#!/bin/bash
# Build script for Render.com deployment

set -e

echo "🚀 Starting build process..."

# Install PHP dependencies
echo "📦 Installing Composer dependencies..."
composer install --no-dev --optimize-autoloader --no-interaction

# Generate application key if not exists
if [ -z "$APP_KEY" ]; then
    echo "🔑 Generating application key..."
    php artisan key:generate --force
fi

# Create storage link
echo "🔗 Creating storage symlink..."
php artisan storage:link || true

# Run migrations
echo "🗄️ Running database migrations..."
php artisan migrate --force

# Seed database
echo "🌱 Seeding database..."
php artisan db:seed --force

# Install Node dependencies
echo "📦 Installing NPM dependencies..."
npm install

# Build assets
echo "🏗️ Building assets..."
npm run build

# Clear and cache config (only in production, ignore errors)
if [ "$APP_ENV" = "production" ]; then
    echo "🧹 Optimizing application..."
    php artisan config:cache || true
    php artisan route:cache || true
    php artisan view:cache || true
fi

echo "✅ Build completed successfully!"

