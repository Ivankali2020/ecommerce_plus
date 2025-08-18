#!/bin/bash

# SSL Wildcard Certificate Setup Script for Multi-Tenant Docker
# Usage: ./ssl-setup.sh [domain] [email]

DOMAIN=${1:-padaung.me}
EMAIL=${2:-admin@padaung.me}

echo "Setting up WILDCARD SSL certificates for domain: $DOMAIN"
echo "Email: $EMAIL"
echo ""
echo "🔒 This will generate a wildcard certificate covering:"
echo "   - $DOMAIN"
echo "   - *.$DOMAIN (all subdomains)"
echo ""

# Check if Cloudflare credentials exist
if [ ! -f "nginx/ssl/cloudflare.ini" ]; then
    echo "❌ ERROR: Cloudflare credentials file not found!"
    echo "Please create nginx/ssl/cloudflare.ini with your Cloudflare API token"
    echo "See the template file for instructions."
    exit 1
fi

# Check if credentials are configured
if grep -q "YOUR_CLOUDFLARE_API_TOKEN_HERE" nginx/ssl/cloudflare.ini; then
    echo "❌ ERROR: Please configure your Cloudflare API token in nginx/ssl/cloudflare.ini"
    echo "Replace 'YOUR_CLOUDFLARE_API_TOKEN_HERE' with your actual token"
    exit 1
fi

echo "✅ Cloudflare credentials found"
echo "🚀 Starting wildcard certificate generation..."

# Start services
echo "Starting Docker services..."
docker-compose up -d

echo "Obtaining wildcard SSL certificate via DNS challenge..."
docker-compose run --rm certbot certonly \
    --dns-cloudflare \
    --dns-cloudflare-credentials /etc/letsencrypt/cloudflare.ini \
    --email $EMAIL \
    --agree-tos \
    --no-eff-email \
    -d $DOMAIN \
    -d "*.$DOMAIN"

if [ $? -eq 0 ]; then
    echo "🎉 Wildcard SSL certificate obtained successfully!"
    echo "📋 Certificate covers:"
    echo "   - $DOMAIN"
    echo "   - All subdomains: *.$DOMAIN"
    echo ""
    echo "🔄 Restarting services with SSL configuration..."
    docker-compose restart web
    echo ""
    echo "✅ Multi-tenant SSL setup complete!"
    echo "🌐 All tenant subdomains will automatically have SSL"
    echo "🔄 Certificate will auto-renew every 60 days"
else
    echo "❌ Wildcard SSL certificate generation failed!"
    echo "Please check:"
    echo "   - Cloudflare API token permissions"
    echo "   - Domain is managed by Cloudflare"
    echo "   - Network connectivity"
fi
