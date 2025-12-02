#!/bin/bash

# Local development startup script
# Runs Docker without SSL for localhost testing

echo "🚀 Starting local development environment..."

docker-compose -f docker-compose.yaml -f docker-compose.local.yaml up -d --build

echo ""
echo "✅ Application running at http://localhost"
echo ""
echo "📋 Useful commands:"
echo "   View logs:    docker-compose logs -f"
echo "   Stop:         docker-compose down"
echo "   Rebuild:      docker-compose -f docker-compose.yaml -f docker-compose.local.yaml up -d --build"
