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
    # Generate key
    # We can't run php artisan key:generate yet as container is not up, 
    # but we will do it after build.
fi

# Update .env with port and domain (optional, mostly for reference or if used in app)
# sed -i "s/APP_URL=.*/APP_URL=http:\/\/${DOMAIN_NAME}:${APP_PORT}/" .env
# sed -i "s/APP_PORT=.*/APP_PORT=${APP_PORT}/" .env

# Export variables for docker-compose
export APP_PORT=$APP_PORT
export WWWUSER=$(id -u)
export WWWGROUP=$(id -g)

echo "🐳 Building and Starting Containers..."
docker-compose -f docker-compose.prod.yml up -d --build

echo "🔧 Running Post-Deployment Tasks..."

# Generate key if APP_KEY is empty
if grep -q "APP_KEY=$" .env || grep -q "APP_KEY=\s*$" .env; then
    echo "Generating Application Key..."
    docker-compose -f docker-compose.prod.yml exec app php artisan key:generate
fi

# Run migrations
echo "Running Migrations..."
docker-compose -f docker-compose.prod.yml exec app php artisan migrate --force

# Optimize
echo "Optimizing Application..."
docker-compose -f docker-compose.prod.yml exec app php artisan optimize
docker-compose -f docker-compose.prod.yml exec app php artisan config:cache
docker-compose -f docker-compose.prod.yml exec app php artisan route:cache
docker-compose -f docker-compose.prod.yml exec app php artisan view:cache

echo "✅ Deployment Complete!"
echo "🌍 Your application is accessible at: http://${DOMAIN_NAME}:${APP_PORT}"
echo "⚠️  Make sure to configure your firewall to allow traffic on port ${APP_PORT}"
