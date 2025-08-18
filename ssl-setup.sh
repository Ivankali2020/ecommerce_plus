#!/bin/bash

# SSL Certificate Setup Script for Docker
# Usage: ./ssl-setup.sh [domain] [email]

DOMAIN=${1:-padaung.me}
EMAIL=${2:-admin@padaung.me}

echo "Setting up SSL certificates for domain: $DOMAIN"
echo "Email: $EMAIL"

# First, create a temporary Nginx config for initial setup (HTTP only)
cat > nginx/default-temp.conf << EOF
server {
    listen 80;
    server_name $DOMAIN www.$DOMAIN;
    
    location /.well-known/acme-challenge/ {
        root /var/www/certbot;
    }
    
    location / {
        proxy_set_header X-Forwarded-Proto http;
        try_files \$uri \$uri/ /index.php?\$query_string;
        gzip_static on;
    }
    
    location ~ \.php$ {
        try_files \$uri =404;
        fastcgi_split_path_info ^(.+\.php)(/.+)$;
        fastcgi_pass app:9000;
        fastcgi_index index.php;
        include fastcgi_params;
        fastcgi_param SCRIPT_FILENAME \$document_root\$fastcgi_script_name;
        fastcgi_param PATH_INFO \$fastcgi_path_info;
        fastcgi_read_timeout 300;
        fastcgi_send_timeout 300;
    }
}
EOF

# Backup original config
cp nginx/default.conf nginx/default.conf.backup

# Use temporary config
cp nginx/default-temp.conf nginx/default.conf

echo "Starting services with HTTP-only configuration..."
docker-compose up -d web

echo "Waiting for Nginx to start..."
sleep 10

echo "Obtaining SSL certificate..."
docker-compose run --rm certbot certonly \
    --webroot \
    --webroot-path=/var/www/certbot \
    --email $EMAIL \
    --agree-tos \
    --no-eff-email \
    -d $DOMAIN \
    -d www.$DOMAIN

if [ $? -eq 0 ]; then
    echo "SSL certificate obtained successfully!"
    
    # Update the Nginx config with the actual domain
    sed -i.bak "s/your-domain.com/$DOMAIN/g" nginx/default.conf.backup
    sed -i.bak "s/your-email@domain.com/$EMAIL/g" nginx/default.conf.backup
    
    # Restore the SSL-enabled config
    cp nginx/default.conf.backup nginx/default.conf
    
    echo "Restarting Nginx with SSL configuration..."
    docker-compose restart web
    
    echo "SSL setup complete! Your site should now be accessible via HTTPS."
    echo "Certificate will auto-renew. Test renewal with: docker-compose run --rm certbot renew --dry-run"
else
    echo "SSL certificate generation failed!"
    echo "Restoring original configuration..."
    cp nginx/default.conf.backup nginx/default.conf
    docker-compose restart web
fi

# Cleanup
rm -f nginx/default-temp.conf nginx/default.conf.bak
