#!/usr/bin/with-contenv bash
# Home Assistant Add-on startup script for TRMNL BYOS
# This script extends the TRMNL BYOS image with Home Assistant integration

set -e

echo "Starting TRMNL BYOS Home Assistant Add-on..."

# Install custom Home Assistant integration
echo "Setting up Home Assistant integration..."
if [ -x /usr/local/bin/setup-ha-integration.sh ]; then
    /usr/local/bin/setup-ha-integration.sh
fi

# Get configuration from Home Assistant (if bashio is available)
if command -v bashio &> /dev/null; then
    APP_KEY=$(bashio::config 'app_key' || echo "")
    APP_URL=$(bashio::config 'app_url' || echo "http://homeassistant.local:8080")
    DB_CONNECTION=$(bashio::config 'db_connection' || echo "sqlite")
else
    # Fallback for testing
    APP_KEY="${APP_KEY:-}"
    APP_URL="${APP_URL:-http://homeassistant.local:8080}"
    DB_CONNECTION="${DB_CONNECTION:-sqlite}"
fi

# Update or create .env file with Home Assistant specific settings
if [ -f /var/www/html/.env ]; then
    # Update existing .env file
    sed -i "s|^APP_URL=.*|APP_URL=${APP_URL}|" /var/www/html/.env
    sed -i "s|^DB_CONNECTION=.*|DB_CONNECTION=${DB_CONNECTION}|" /var/www/html/.env
    
    # Add Home Assistant integration settings if not present
    if ! grep -q "SUPERVISOR_TOKEN" /var/www/html/.env; then
        cat >> /var/www/html/.env << EOF

# Home Assistant Integration
SUPERVISOR_TOKEN=${SUPERVISOR_TOKEN}
HOMEASSISTANT_URL=http://supervisor/core
EOF
    fi
else
    # Create new .env file
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
fi

# Generate app key if not provided
if [ -z "$APP_KEY" ] || [ "$APP_KEY" = "null" ]; then
    echo "Generating application key..."
    cd /var/www/html
    php artisan key:generate --force
    NEW_KEY=$(grep APP_KEY= .env | cut -d '=' -f2)
    echo "WARNING: Please add this key to your add-on configuration: ${NEW_KEY}"
fi

# Set up database for Home Assistant integration
if [ "$DB_CONNECTION" = "sqlite" ]; then
    echo "Setting up SQLite database..."
    mkdir -p /data/database
    if [ ! -f /data/database/trmnl.sqlite ]; then
        touch /data/database/trmnl.sqlite
    fi
    # Update DB path in .env
    sed -i "s|^DB_DATABASE=.*|DB_DATABASE=/data/database/trmnl.sqlite|" /var/www/html/.env
    
    # Ensure proper ownership
    chown -R www-data:www-data /data/database
    chmod 644 /data/database/trmnl.sqlite
fi

# Run migrations
echo "Running database migrations..."
cd /var/www/html
php artisan migrate --force

# Clear and optimize Laravel caches
echo "Optimizing Laravel application..."
php artisan config:clear
php artisan route:clear
php artisan view:clear
php artisan config:cache
php artisan route:cache
php artisan view:cache

# Set proper permissions
chown -R www-data:www-data /var/www/html/storage /var/www/html/bootstrap/cache
chmod -R 755 /var/www/html/storage /var/www/html/bootstrap/cache

echo "TRMNL BYOS Home Assistant Add-on ready!"

# Execute the original TRMNL BYOS entrypoint
# The TRMNL image uses s6-overlay, so we exec to the proper entrypoint
exec /init
