ARG BUILD_FROM
FROM $BUILD_FROM

# Set shell
SHELL ["/bin/bash", "-o", "pipefail", "-c"]

# Install base dependencies
RUN apk add --no-cache \
    nginx \
    php82 \
    php82-fpm \
    php82-pdo \
    php82-pdo_sqlite \
    php82-pdo_mysql \
    php82-pdo_pgsql \
    php82-mbstring \
    php82-openssl \
    php82-tokenizer \
    php82-xml \
    php82-ctype \
    php82-json \
    php82-curl \
    php82-fileinfo \
    php82-session \
    php82-bcmath \
    php82-dom \
    php82-xmlwriter \
    php82-simplexml \
    composer \
    git \
    curl \
    sqlite

# Create necessary directories
RUN mkdir -p /var/www/html /run/nginx /run/php-fpm82 /data/database

# Set working directory
WORKDIR /var/www/html

# Clone TRMNL BYOS repository
RUN git clone https://github.com/usetrmnl/byos_laravel.git /var/www/html && \
    rm -rf .git

# Install Composer dependencies
RUN composer install --no-dev --optimize-autoloader --no-interaction

# Copy nginx configuration
COPY rootfs/etc/nginx/nginx.conf /etc/nginx/nginx.conf
COPY rootfs/etc/nginx/http.d/default.conf /etc/nginx/http.d/default.conf

# Copy PHP-FPM configuration
COPY rootfs/etc/php82/php-fpm.d/www.conf /etc/php82/php-fpm.d/www.conf

# Copy custom files
COPY rootfs /

# Set permissions
RUN chown -R nginx:nginx /var/www/html /data/database && \
    chmod -R 755 /var/www/html/storage /var/www/html/bootstrap/cache && \
    chmod +x /run.sh && \
    chmod +x /usr/local/bin/setup-ha-integration.sh

# Expose port
EXPOSE 8080

# Health check
HEALTHCHECK --interval=30s --timeout=3s --start-period=40s --retries=3 \
    CMD curl -f http://localhost:8080/ || exit 1

# Start services
CMD ["/run.sh"]
