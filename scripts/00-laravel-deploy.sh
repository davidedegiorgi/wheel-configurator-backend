#!/bin/bash

# Wheel Configurator Backend - Deployment Script
# This script automates the deployment process for the Wheel Configurator backend

set -e

echo "🚀 Starting Wheel Configurator Backend Deployment..."

# Install dependencies
echo "📦 Installing Composer dependencies..."
composer install --no-dev --optimize-autoloader

# Run migrations
echo "🗄️  Running database migrations..."
php artisan migrate --force

# Seed database (optional)
# php artisan db:seed --force

# Cache configuration
echo "⚡ Caching configuration..."
php artisan config:cache
php artisan route:cache
php artisan view:cache

# Clear logs
echo "🧹 Clearing logs..."
rm -f storage/logs/laravel.log

echo "✅ Deployment completed successfully!"
