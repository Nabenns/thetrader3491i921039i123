#!/bin/bash

# Check if running as root (required for System Nginx setup)
if [ "$EUID" -ne 0 ]; then 
  echo "Please run as root (use sudo ./deploy.sh)"
  exit 1
fi

# Ask for Domain
read -p "Enter your domain name (e.g., example.com): " DOMAIN_NAME

# Set standard ports for Nginx Proxy
# We use 8080 internally to avoid conflict with System Nginx
APP_PORT=8080
APP_SSL_PORT=8443 # Not used for external access in this setup

# Stop existing web servers to free up port 80/443
# echo "🛑 Checking for conflicting services..."
# if systemctl is-active --quiet nginx; then
#     echo "Stopping system Nginx..."
#     systemctl stop nginx
#     systemctl disable nginx
# fi
# if systemctl is-active --quiet apache2; then
#     echo "Stopping system Apache2..."
#     systemctl stop apache2
#     systemctl disable apache2
# fi

# Create .env if not exists
if [ ! -f .env ]; then
    echo "📝 Creating .env file from .env.example..."
    cp .env.example .env
fi

# Force Configuration (More robust than sed)
echo "⚙️  Configuring Environment..."

# Remove Windows line endings from .env just in case
sed -i 's/\r$//' .env

# Database - Force MySQL (Robust: Delete and Append)
sed -i '/DB_CONNECTION/d' .env
sed -i '/DB_HOST/d' .env
sed -i '/DB_PORT/d' .env
sed -i '/DB_DATABASE/d' .env
sed -i '/DB_USERNAME/d' .env
sed -i '/DB_PASSWORD/d' .env

echo "DB_CONNECTION=mysql" >> .env
echo "DB_HOST=db" >> .env
echo "DB_PORT=3306" >> .env
echo "DB_DATABASE=thetrader" >> .env
echo "DB_USERNAME=thetrader" >> .env
echo "DB_PASSWORD=root" >> .env

# Redis
sed -i '/REDIS_HOST/d' .env
echo "REDIS_HOST=redis" >> .env

# Other settings
sed -i 's/APP_ENV=.*/APP_ENV=production/' .env
sed -i 's/APP_DEBUG=.*/APP_DEBUG=false/' .env
sed -i "s|APP_URL=.*|APP_URL=https://${DOMAIN_NAME}|" .env

# Fix permissions on .env so container can read/write
chmod 666 .env

# Export variables for docker-compose
export APP_PORT=$APP_PORT
export APP_SSL_PORT=$APP_SSL_PORT
export WWWUSER=$(id -u)
export WWWGROUP=$(id -g)

# Load .env variables so docker-compose can see them
set -a
source .env
set +a

# Reset Database Volume (Ensure fresh start with correct credentials)
echo "🗑️  Resetting Database Volume..."
docker compose -f docker-compose.prod.yml down -v

# Internal Nginx Configuration (HTTP Only - SSL handled by System Nginx)
echo "🔧 Configuring Docker Nginx for HTTP only (Port $APP_PORT)..."

# Update default.conf for HTTP only
cat > ./docker/nginx/proxy/conf.d/default.conf <<EOF
server {
    listen 80;
    server_name $DOMAIN_NAME www.$DOMAIN_NAME;
    server_tokens off;

    location / {
        proxy_pass http://web:80;
        proxy_set_header    Host                \$http_host;
        proxy_set_header    X-Real-IP           \$remote_addr;
        proxy_set_header    X-Forwarded-For     \$proxy_add_x_forwarded_for;
    }
}
EOF

echo "🐳 Building and Starting Containers..."
docker compose -f docker-compose.prod.yml up -d --build

echo "🔧 Running Post-Deployment Tasks..."

# Fix permissions immediately after build
echo "Fixing permissions..."
docker compose -f docker-compose.prod.yml exec -u root app chown -R www-data:www-data /var/www/storage /var/www/bootstrap/cache /var/www/public
docker compose -f docker-compose.prod.yml exec -u root app chmod -R 775 /var/www/storage /var/www/bootstrap/cache /var/www/public

# Create Certbot directory if not exists
mkdir -p ./docker/certbot/www

# Install Dependencies
echo "Installing Dependencies..."
docker compose -f docker-compose.prod.yml exec -u root app composer install --no-dev --optimize-autoloader

# Generate key if APP_KEY is empty
if grep -q "APP_KEY=$" .env || grep -q "APP_KEY=\s*$" .env; then
    echo "Generating Application Key..."
    docker compose -f docker-compose.prod.yml exec app php artisan key:generate
fi

# Wait for Database
echo "⏳ Waiting for Database to be ready..."
docker compose -f docker-compose.prod.yml exec app php -r "set_time_limit(60); for(;;){if(@fsockopen('db',3306)){break;}echo \"Waiting for DB...\n\";sleep(1);}"

# Run migrations
echo "Running Migrations..."
docker compose -f docker-compose.prod.yml exec app php artisan migrate --force

# Link Storage
echo "Linking Storage..."
docker compose -f docker-compose.prod.yml exec app php artisan storage:link

# Optimize
echo "Optimizing Application..."
docker compose -f docker-compose.prod.yml exec app php artisan optimize
docker compose -f docker-compose.prod.yml exec app php artisan config:cache
docker compose -f docker-compose.prod.yml exec app php artisan route:cache
docker compose -f docker-compose.prod.yml exec app php artisan view:cache

echo "✅ Docker Deployment Complete!"
echo "🌍 Internal Docker App is running on: http://localhost:${APP_PORT}"

# -----------------------------------------------------------------------------
# System Nginx Configuration (The "Magic" Part)
# -----------------------------------------------------------------------------

echo ""
echo "======================================================="
echo "   Setting up System Nginx Proxy for $DOMAIN_NAME"
echo "======================================================="

CONFIG_FILE="/etc/nginx/sites-available/$DOMAIN_NAME"

echo "📝 Creating System Nginx configuration..."

# -----------------------------------------------------------------------------
# Conflict Resolution: Remove other configs for this domain
# -----------------------------------------------------------------------------
echo "⚔️  Checking for conflicting Nginx configurations..."
for file in /etc/nginx/sites-enabled/*; do
    # Skip if it's the file we are about to create (resolved symlink)
    if [ "$(readlink -f "$file")" == "/etc/nginx/sites-available/$DOMAIN_NAME" ]; then
        continue
    fi

    # Check if the file contains the domain name
    if grep -q "server_name.*$DOMAIN_NAME" "$file"; then
        echo "⚠️  Found conflicting config in $file. Disabling it..."
        rm "$file"
        echo "   Disabled $file"
    fi
done
# -----------------------------------------------------------------------------

cat > "$CONFIG_FILE" <<EOF
server {
    listen 80;
    server_name $DOMAIN_NAME www.$DOMAIN_NAME;

    location / {
        proxy_pass http://localhost:$APP_PORT;
        proxy_http_version 1.1;
        proxy_set_header Upgrade \$http_upgrade;
        proxy_set_header Connection 'upgrade';
        proxy_set_header Host \$host;
        proxy_cache_bypass \$http_upgrade;
        proxy_set_header X-Real-IP \$remote_addr;
        proxy_set_header X-Forwarded-For \$proxy_add_x_forwarded_for;
        proxy_set_header X-Forwarded-Proto \$scheme;
    }
}
EOF

echo "🔗 Enabling site..."
ln -sf "$CONFIG_FILE" "/etc/nginx/sites-enabled/"

echo "🔄 Reloading Nginx..."
nginx -t && systemctl reload nginx

echo "🔒 Setting up SSL..."

# DEBUG: Show what's in the directory
echo "🔍 Debugging: Listing /etc/letsencrypt/live/..."
ls -la /etc/letsencrypt/live/ 2>/dev/null || echo "Directory not found or empty."

# Find the valid certificate directory
CERT_DIR=""
# Check standard path first
if [ -f "/etc/letsencrypt/live/$DOMAIN_NAME/fullchain.pem" ]; then
    CERT_DIR="/etc/letsencrypt/live/$DOMAIN_NAME"
else
    # Search for suffixed directories (e.g., domain.com-0001)
    for dir in /etc/letsencrypt/live/${DOMAIN_NAME}-*; do
        if [ -f "$dir/fullchain.pem" ]; then
            CERT_DIR="$dir"
            break
        fi
    done
fi

# Variables for Cert Paths
SSL_CERT=""
SSL_KEY=""

if [ -n "$CERT_DIR" ]; then
    echo "✅ Found existing Let's Encrypt certificate in: $CERT_DIR"
    SSL_CERT="$CERT_DIR/fullchain.pem"
    SSL_KEY="$CERT_DIR/privkey.pem"
else
    echo "⚠️  No existing Let's Encrypt certificate found."
    
    # Try to run Certbot
    if ! command -v certbot &> /dev/null; then
        echo "Installing Certbot..."
        apt-get update
        apt-get install -y certbot python3-certbot-nginx
    fi

    echo "Attempting to obtain new certificate..."
    certbot --nginx -d "$DOMAIN_NAME" -d "www.$DOMAIN_NAME" --non-interactive --agree-tos --redirect --register-unsafely-without-email
    
    # Check again if it worked
    if [ -f "/etc/letsencrypt/live/$DOMAIN_NAME/fullchain.pem" ]; then
        CERT_DIR="/etc/letsencrypt/live/$DOMAIN_NAME"
        SSL_CERT="$CERT_DIR/fullchain.pem"
        SSL_KEY="$CERT_DIR/privkey.pem"
    fi
fi

# FALLBACK: If still no cert (e.g. rate limit), generate Self-Signed
if [ -z "$SSL_CERT" ]; then
    echo "⚠️  Certbot failed (likely rate limit). Generating Self-Signed Certificate..."
    echo "   This will ensure your site works, but you will see a browser warning."
    
    mkdir -p /etc/nginx/ssl
    openssl req -x509 -nodes -days 365 -newkey rsa:2048 \
        -keyout /etc/nginx/ssl/selfsigned.key \
        -out /etc/nginx/ssl/selfsigned.crt \
        -subj "/C=US/ST=State/L=City/O=Organization/CN=$DOMAIN_NAME"
        
    SSL_CERT="/etc/nginx/ssl/selfsigned.crt"
    SSL_KEY="/etc/nginx/ssl/selfsigned.key"
    
    # Create dummy dhparams if needed
    if [ ! -f /etc/letsencrypt/ssl-dhparams.pem ]; then
        openssl dhparam -out /etc/letsencrypt/ssl-dhparams.pem 2048
    fi
fi

echo "📝 Updating System Nginx configuration with SSL..."
cat > "$CONFIG_FILE" <<EOF
server {
    listen 80;
    server_name $DOMAIN_NAME www.$DOMAIN_NAME;
    return 301 https://\$host\$request_uri;
}

server {
    listen 443 ssl;
    server_name $DOMAIN_NAME www.$DOMAIN_NAME;

    ssl_certificate $SSL_CERT;
    ssl_certificate_key $SSL_KEY;
    
    # Basic SSL Settings
    ssl_protocols TLSv1.2 TLSv1.3;
    ssl_prefer_server_ciphers on;
    ssl_ciphers ECDHE-ECDSA-AES128-GCM-SHA256:ECDHE-RSA-AES128-GCM-SHA256:ECDHE-ECDSA-AES256-GCM-SHA384:ECDHE-RSA-AES256-GCM-SHA384:ECDHE-ECDSA-CHACHA20-POLY1305:ECDHE-RSA-CHACHA20-POLY1305:DHE-RSA-AES128-GCM-SHA256:DHE-RSA-AES256-GCM-SHA384;

    location / {
        proxy_pass http://localhost:$APP_PORT;
        proxy_http_version 1.1;
        proxy_set_header Upgrade \$http_upgrade;
        proxy_set_header Connection 'upgrade';
        proxy_set_header Host \$host;
        proxy_cache_bypass \$http_upgrade;
        proxy_set_header X-Real-IP \$remote_addr;
        proxy_set_header X-Forwarded-For \$proxy_add_x_forwarded_for;
        proxy_set_header X-Forwarded-Proto \$scheme;
    }
}
EOF

echo "🔄 Reloading Nginx..."
nginx -t && systemctl reload nginx

echo "✅ Nginx configuration updated!"
echo "-------------------------------------------------------"
echo "Current Nginx Config for $DOMAIN_NAME:"
cat "$CONFIG_FILE"
echo "-------------------------------------------------------"

echo ""
echo "======================================================="
echo "   🎉 FULL DEPLOYMENT COMPLETE!"
echo "   Your app is accessible at: https://$DOMAIN_NAME"
echo "======================================================="
