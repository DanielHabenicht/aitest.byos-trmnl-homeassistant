########################
# Home Assistant Add-on for TRMNL BYOS
# Reuses the official TRMNL BYOS Docker image
########################

# Use official TRMNL BYOS Laravel image as base
FROM ghcr.io/usetrmnl/byos_laravel:latest

# Switch to root for system modifications
USER root

# Install Home Assistant add-on requirements
RUN apk add --no-cache \
    bash \
    curl

# Create data directory for persistent storage
RUN mkdir -p /data/database

# Copy custom Home Assistant integration files
COPY rootfs/var/www/html-custom /var/www/html-custom
COPY rootfs/usr/local/bin/setup-ha-integration.sh /usr/local/bin/
COPY run.sh /run.sh

# Set permissions
RUN chmod +x /run.sh && \
    chmod +x /usr/local/bin/setup-ha-integration.sh && \
    chown -R www-data:www-data /var/www/html-custom

# Expose port (TRMNL uses 8080)
EXPOSE 8080

# Health check
HEALTHCHECK --interval=30s --timeout=3s --start-period=40s --retries=3 \
    CMD curl -f http://localhost:8080/ || exit 1

# Use our custom run script
CMD ["/run.sh"]
