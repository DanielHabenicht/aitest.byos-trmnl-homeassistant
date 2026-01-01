#!/usr/bin/env bashio

set -e

bashio::log.info "Starting TRMNL BYOS Add-on..."

# Install custom Home Assistant integration
bashio::log.info "Setting up Home Assistant integration..."
if [ -x /usr/local/bin/setup-ha-integration.sh ]; then
    /usr/local/bin/setup-ha-integration.sh
fi

# Get configuration from Home Assistant
APP_KEY=$(bashio::config 'app_key')
APP_URL=$(bashio::config 'app_url')
DB_CONNECTION=$(bashio::config 'db_connection')

# Set up environment file
cat > /var/www/html/.env << EOF
APP_NAME="TRMNL BYOS"
APP_ENV=production
APP_KEY=${APP_KEY}
APP_DEBUG=false
APP_URL=${APP_URL}

LOG_CHANNEL=stderr
LOG_LEVEL=info

DB_CONNECTION=${DB_CONNECTION}
DB_DATABASE=/data/database/trmnl.sqlite

BROADCAST_DRIVER=log
CACHE_DRIVER=file
FILESYSTEM_DISK=local
QUEUE_CONNECTION=sync
SESSION_DRIVER=file
SESSION_LIFETIME=120

# Home Assistant Integration
SUPERVISOR_TOKEN=${SUPERVISOR_TOKEN}
HOMEASSISTANT_URL=http://supervisor/core
EOF

# Generate app key if not provided
if [ -z "$APP_KEY" ] || [ "$APP_KEY" = "null" ]; then
    bashio::log.info "Generating application key..."
    cd /var/www/html
    php artisan key:generate --force
    NEW_KEY=$(grep APP_KEY= .env | cut -d '=' -f2)
    bashio::log.warning "Please add this key to your add-on configuration: ${NEW_KEY}"
fi

# Set up database
if [ "$DB_CONNECTION" = "sqlite" ]; then
    bashio::log.info "Setting up SQLite database..."
    if [ ! -f /data/database/trmnl.sqlite ]; then
        touch /data/database/trmnl.sqlite
    fi
    # Ensure proper ownership regardless of whether file existed
    chown -R nginx:nginx /data/database
    chmod 644 /data/database/trmnl.sqlite
fi

# Run migrations
bashio::log.info "Running database migrations..."
cd /var/www/html
php artisan migrate --force

# Optimize Laravel
bashio::log.info "Optimizing Laravel application..."
php artisan config:cache
php artisan route:cache
php artisan view:cache

# Set permissions
chown -R nginx:nginx /var/www/html/storage /var/www/html/bootstrap/cache
chmod -R 755 /var/www/html/storage /var/www/html/bootstrap/cache

# Start PHP-FPM
bashio::log.info "Starting PHP-FPM..."
php-fpm82 -D

# Start nginx
bashio::log.info "Starting nginx..."
exec nginx -g "daemon off;"
