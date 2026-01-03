#!/bin/bash

# Function to generate a random port between 10000 and 65000
generate_random_port() {
    echo $((10000 + RANDOM % 55000))
}

echo "🚀 Starting Deployment Setup..."

# Check if Docker is installed
if ! command -v docker &> /dev/null; then
    echo "Docker could not be found. Installing Docker..."
    curl -fsSL https://get.docker.com -o get-docker.sh
    sh get-docker.sh
    rm get-docker.sh
fi

# Check if Docker Compose is installed
if ! command -v docker-compose &> /dev/null; then
    echo "Docker Compose could not be found. Installing..."
    sudo curl -L "https://github.com/docker/compose/releases/download/v2.24.1/docker-compose-$(uname -s)-$(uname -m)" -o /usr/local/bin/docker-compose
    sudo chmod +x /usr/local/bin/docker-compose
fi

# Ask for Domain
read -p "Enter your domain name (e.g., example.com): " DOMAIN_NAME

# Ask for Port
read -p "Enter the port you want to expose (leave empty for a random weird port): " APP_PORT

if [ -z "$APP_PORT" ]; then
    APP_PORT=$(generate_random_port)
    echo "🎲 Selected random port: $APP_PORT"
fi

# Create .env if not exists
if [ ! -f .env ]; then
    echo "📝 Creating .env file from .env.example..."
    cp .env.example .env
    # Configure for Production
    sed -i 's/APP_ENV=local/APP_ENV=production/' .env
    sed -i 's/APP_DEBUG=true/APP_DEBUG=false/' .env
    sed -i "s/APP_URL=.*/APP_URL=http:\/\/${DOMAIN_NAME}:${APP_PORT}/" .env
    
    # Configure Database
    sed -i 's/DB_CONNECTION=sqlite/DB_CONNECTION=mysql/' .env
    sed -i 's/# DB_HOST=127.0.0.1/DB_HOST=db/' .env
    sed -i 's/# DB_PORT=3306/DB_PORT=3306/' .env
    sed -i 's/# DB_DATABASE=laravel/DB_DATABASE=thetrader/' .env
    sed -i 's/# DB_USERNAME=root/DB_USERNAME=root/' .env
    sed -i 's/# DB_PASSWORD=/DB_PASSWORD=root/' .env
    
    # Configure Redis
    sed -i 's/REDIS_HOST=127.0.0.1/REDIS_HOST=redis/' .env
fi

# Export variables for docker-compose
export APP_PORT=$APP_PORT
export WWWUSER=$(id -u)
export WWWGROUP=$(id -g)

echo "🐳 Building and Starting Containers..."
docker-compose -f docker-compose.prod.yml up -d --build

echo "🔧 Running Post-Deployment Tasks..."

# Fix permissions
echo "Fixing permissions..."
docker-compose -f docker-compose.prod.yml exec -u root app chown -R www-data:www-data /var/www/storage /var/www/bootstrap/cache

# Install Dependencies
echo "Installing Dependencies..."
docker-compose -f docker-compose.prod.yml exec -u root app composer install --no-dev --optimize-autoloader

# Generate key if APP_KEY is empty
if grep -q "APP_KEY=$" .env || grep -q "APP_KEY=\s*$" .env; then
    echo "Generating Application Key..."
    docker-compose -f docker-compose.prod.yml exec app php artisan key:generate
fi

# Run migrations
echo "Running Migrations..."
docker-compose -f docker-compose.prod.yml exec app php artisan migrate --force

# Link Storage
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
