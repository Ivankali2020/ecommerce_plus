#!/bin/bash

# SSL Certificate Renewal Script
# Add this to your server's crontab to run daily:
# 0 3 * * * /path/to/your/project/ssl-renew.sh

cd "$(dirname "$0")"

echo "$(date): Checking for certificate renewal..."

# Try to renew certificates
docker-compose run --rm certbot renew --quiet

# If renewal happened, reload Nginx
if [ $? -eq 0 ]; then
    echo "$(date): Certificates renewed successfully, reloading Nginx..."
    docker-compose exec web nginx -s reload
else
    echo "$(date): No renewal needed or renewal failed"
fi
