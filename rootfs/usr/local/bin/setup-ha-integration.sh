#!/bin/bash
# Setup script to integrate custom Home Assistant files into TRMNL BYOS

set -e

echo "Installing custom Home Assistant integration files..."

# Copy controller
if [ -f /var/www/html-custom/app/Http/Controllers/HomeAssistantController.php ]; then
    cp /var/www/html-custom/app/Http/Controllers/HomeAssistantController.php \
       /var/www/html/app/Http/Controllers/
    echo "✓ Installed HomeAssistantController"
fi

# Copy migration
if [ -f /var/www/html-custom/database/migrations/2026_01_01_000001_create_homeassistant_entities_table.php ]; then
    cp /var/www/html-custom/database/migrations/2026_01_01_000001_create_homeassistant_entities_table.php \
       /var/www/html/database/migrations/
    echo "✓ Installed migration"
fi

# Copy views
if [ -d /var/www/html-custom/resources/views/homeassistant ]; then
    mkdir -p /var/www/html/resources/views/homeassistant
    cp -r /var/www/html-custom/resources/views/homeassistant/* \
       /var/www/html/resources/views/homeassistant/
    echo "✓ Installed views"
fi

# Add routes to web.php if not already present
if [ -f /var/www/html-custom/routes/homeassistant.php ]; then
    if ! grep -q "homeassistant.php" /var/www/html/routes/web.php 2>/dev/null; then
        echo "" >> /var/www/html/routes/web.php
        echo "// Home Assistant Integration" >> /var/www/html/routes/web.php
        echo "require __DIR__.'/homeassistant.php';" >> /var/www/html/routes/web.php
        echo "✓ Added routes"
    fi
    cp /var/www/html-custom/routes/homeassistant.php /var/www/html/routes/
fi

echo "Custom Home Assistant integration installed successfully!"
