#!/usr/bin/env bash
set -euo pipefail

# Usage: chmod +x docker-deploy.sh && ./docker-deploy.sh

# Make sure we're in the project root
cd "$(dirname "$0")"

# Maintenance mode
echo ":: Entering maintenance mode"
docker compose exec laravel php artisan down || true

# Pull latest code
echo ":: Fetching latest from Git"
git pull origin master

# Rebuild & restart container
echo ":: Rebuilding app image & restarting"
docker compose up -d --build --force-recreate laravel

# Wait for successful health check
echo ":: Waiting for app to report healthy..."
until docker compose ps laravel | grep -q "(healthy)"; do
  sleep 3
done

# Clear out cached data
echo ":: Clearing cache"
docker compose exec laravel php artisan optimize:clear
docker compose exec laravel php artisan cache:clear

# Run database migrations
echo ":: Running migrations"
docker compose exec laravel php artisan migrate --force

# Optimize
echo ":: Caching config, routes, and views"
docker compose exec laravel php artisan optimize

# Live again
echo ":: Bringing application back up"
docker compose exec laravel php artisan up || true

# Prune dangling image builds
echo "Prune dangling images"
docker image prune -a -f \
  --filter "label=com.docker.compose.project=$(basename $(pwd))" \
  --filter "label=com.docker.compose.service=laravel"

echo ":: Docker disk space summary"
docker system df

#echo ":: Prune build cache"
#docker builder prune -f

echo "✅ Deployment complete!"
