#!/bin/bash

# Ask for Domain
read -p "Enter your domain name (e.g., example.com): " DOMAIN_NAME

# Set standard ports for Nginx Proxy
APP_PORT=80

# Stop existing web servers to free up port 80/443
echo "🛑 Checking for conflicting services..."
if systemctl is-active --quiet nginx; then
    echo "Stopping system Nginx..."
    systemctl stop nginx
    systemctl disable nginx
fi
if systemctl is-active --quiet apache2; then
    echo "Stopping system Apache2..."
    systemctl stop apache2
    systemctl disable apache2
fi

# Create .env if not exists
if [ ! -f .env ]; then
    echo "📝 Creating .env file from .env.example..."
    cp .env.example .env
fi

# Force Configuration (More robust than sed)
echo "⚙️  Configuring Environment..."
# Use sed to replace if exists, otherwise append (simplified approach: just force replace common keys)
sed -i 's/APP_ENV=.*/APP_ENV=production/' .env
sed -i 's/APP_DEBUG=.*/APP_DEBUG=false/' .env
sed -i "s|APP_URL=.*|APP_URL=https://${DOMAIN_NAME}|" .env

# Database - Force MySQL
sed -i 's/DB_CONNECTION=.*/DB_CONNECTION=mysql/' .env
sed -i 's/# DB_HOST=.*/DB_HOST=db/' .env
sed -i 's/DB_HOST=.*/DB_HOST=db/' .env
sed -i 's/# DB_PORT=.*/DB_PORT=3306/' .env
sed -i 's/DB_PORT=.*/DB_PORT=3306/' .env
sed -i 's/# DB_DATABASE=.*/DB_DATABASE=thetrader/' .env
sed -i 's/DB_DATABASE=.*/DB_DATABASE=thetrader/' .env
sed -i 's/# DB_USERNAME=.*/DB_USERNAME=root/' .env
sed -i 's/DB_USERNAME=.*/DB_USERNAME=root/' .env
sed -i 's/# DB_PASSWORD=.*/DB_PASSWORD=root/' .env
sed -i 's/DB_PASSWORD=.*/DB_PASSWORD=root/' .env

# Redis
sed -i 's/REDIS_HOST=.*/REDIS_HOST=redis/' .env

# Fix permissions on .env so container can read/write
chmod 666 .env

# Export variables for docker-compose
export APP_PORT=$APP_PORT
export WWWUSER=$(id -u)
export WWWGROUP=$(id -g)

# Load .env variables so docker-compose can see them
set -a
source .env
set +a

# Reset Database Volume (Ensure fresh start with correct credentials)
echo "🗑️  Resetting Database Volume..."
docker-compose -f docker-compose.prod.yml down -v

# SSL Setup
data_path="./docker/certbot"
rsa_key_size=4096
domains=("$DOMAIN_NAME" "www.$DOMAIN_NAME")
email="" # Adding a valid email is recommended
staging=0 # Set to 1 if you're testing your setup to avoid hitting request limits

if [ -d "$data_path" ]; then
    read -p "Existing data found for $domains. Continue and replace existing certificate? (y/N) " decision
    if [ "$decision" != "Y" ] && [ "$decision" != "y" ]; then
        exit
    fi
fi

if [ ! -e "$data_path/conf/options-ssl-nginx.conf" ] || [ ! -e "$data_path/conf/ssl-dhparams.pem" ]; then
    echo "### Downloading recommended TLS parameters ..."
    mkdir -p "$data_path/conf"
    curl -s https://raw.githubusercontent.com/certbot/certbot/master/certbot-nginx/certbot_nginx/_internal/tls_configs/options-ssl-nginx.conf > "$data_path/conf/options-ssl-nginx.conf"
    curl -s https://raw.githubusercontent.com/certbot/certbot/master/certbot/certbot/ssl-dhparams.pem > "$data_path/conf/ssl-dhparams.pem"
fi

echo "### Creating dummy certificate for $domains ..."
path="/etc/letsencrypt/live/$domains"
mkdir -p "$data_path/conf/live/$domains"
docker-compose -f docker-compose.prod.yml run --rm --entrypoint "\
  openssl req -x509 -nodes -newkey rsa:$rsa_key_size -days 1\
    -keyout '$path/privkey.pem' \
    -out '$path/fullchain.pem' \
    -subj '/CN=localhost'" certbot

echo "### Starting nginx ..."
docker-compose -f docker-compose.prod.yml up --force-recreate -d proxy

echo "### Deleting dummy certificate for $domains ..."
docker-compose -f docker-compose.prod.yml run --rm --entrypoint "\
  rm -Rf /etc/letsencrypt/live/$domains && \
  rm -Rf /etc/letsencrypt/archive/$domains && \
  rm -Rf /etc/letsencrypt/renewal/$domains.conf" certbot

echo "### Requesting Let's Encrypt certificate for $domains ..."
#Join $domains to -d args
domain_args=""
for domain in "${domains[@]}"; do
  domain_args="$domain_args -d $domain"
done

# Select appropriate email arg
case "$email" in
  "") email_arg="--register-unsafely-without-email" ;;
  *) email_arg="--email $email" ;;
esac

# Enable staging mode if needed
if [ "$staging" != "0" ]; then staging_arg="--staging"; fi

docker-compose -f docker-compose.prod.yml run --rm --entrypoint "\
  certbot certonly --webroot -w /var/www/certbot \
    $staging_arg \
    $email_arg \
    $domain_args \
    --rsa-key-size $rsa_key_size \
    --agree-tos \
    --force-renewal" certbot

echo "### Reloading nginx ..."
docker-compose -f docker-compose.prod.yml exec proxy nginx -s reload

echo "🐳 Building and Starting Containers..."
docker-compose -f docker-compose.prod.yml up -d --build

echo "🔧 Running Post-Deployment Tasks..."

# Fix permissions immediately after build
echo "Fixing permissions..."
docker-compose -f docker-compose.prod.yml exec -u root app chown -R www-data:www-data /var/www/storage /var/www/bootstrap/cache /var/www/public
docker-compose -f docker-compose.prod.yml exec -u root app chmod -R 775 /var/www/storage /var/www/bootstrap/cache /var/www/public

# Create Certbot directory if not exists
mkdir -p ./docker/certbot/www

# Install Dependencies
echo "Installing Dependencies..."
docker-compose -f docker-compose.prod.yml exec -u root app composer install --no-dev --optimize-autoloader

# Generate key if APP_KEY is empty
if grep -q "APP_KEY=$" .env || grep -q "APP_KEY=\s*$" .env; then
    echo "Generating Application Key..."
    docker-compose -f docker-compose.prod.yml exec app php artisan key:generate
fi

# Wait for Database
echo "⏳ Waiting for Database to be ready..."
docker-compose -f docker-compose.prod.yml exec app php -r "set_time_limit(60); for(;;){if(@fsockopen('db',3306)){break;}echo \"Waiting for DB...\n\";sleep(1);}"

# Run migrations
echo "Running Migrations..."
docker-compose -f docker-compose.prod.yml exec app php artisan migrate --force

# Link Storage
echo "Linking Storage..."
docker-compose -f docker-compose.prod.yml exec app php artisan storage:link

# Optimize
echo "Optimizing Application..."
docker-compose -f docker-compose.prod.yml exec app php artisan optimize
docker-compose -f docker-compose.prod.yml exec app php artisan config:cache
docker-compose -f docker-compose.prod.yml exec app php artisan route:cache
docker-compose -f docker-compose.prod.yml exec app php artisan view:cache

echo "✅ Deployment Complete!"
echo "🌍 Your application is accessible at: http://${DOMAIN_NAME}:${APP_PORT}"
echo "⚠️  Make sure to configure your firewall to allow traffic on port ${APP_PORT}"
