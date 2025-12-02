#!/bin/bash

# Production startup script
# Runs Docker with SSL configuration for padaung.me

echo "🚀 Starting production environment with SSL..."

docker-compose -f docker-compose.yaml -f docker-compose.prod.yaml up -d --build

echo ""
echo "✅ Application running at https://padaung.me"
echo ""
echo "📋 Useful commands:"
echo "   View logs:    docker-compose logs -f"
echo "   Stop:         docker-compose down"
echo "   SSL Renew:    ./ssl-renew.sh"
