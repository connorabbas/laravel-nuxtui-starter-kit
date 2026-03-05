#!/usr/bin/env bash
set -euo pipefail

# Usage:
# IMAGE_REPOSITORY="ghcr.io/org/repo" IMAGE_TAG="<tag>" STACK_NAME="laravel-nuxtui" ./docker-swarm-deploy.sh

cd "$(dirname "$0")"

STACK_NAME="${STACK_NAME:-laravel-nuxtui}"
COMPOSE_FILE="${COMPOSE_FILE:-docker-compose.swarm.yml}"

if [[ -z "${IMAGE_REPOSITORY:-}" ]]; then
    echo "IMAGE_REPOSITORY is required (example: ghcr.io/org/repo)"
    exit 1
fi

if [[ -z "${IMAGE_TAG:-}" ]]; then
    echo "IMAGE_TAG is required (example: a git sha)"
    exit 1
fi

if [[ "$(docker info --format '{{.Swarm.LocalNodeState}}')" != "active" ]]; then
    echo "Docker Swarm is not active on this host. Initialize first with: docker swarm init"
    exit 1
fi

echo ":: Deploying ${STACK_NAME} with image ${IMAGE_REPOSITORY}:${IMAGE_TAG}"
docker stack deploy --with-registry-auth --prune -c "${COMPOSE_FILE}" "${STACK_NAME}"

echo ":: Swarm services"
docker service ls

echo ":: Recent tasks for ${STACK_NAME}_laravel"
docker service ps --no-trunc "${STACK_NAME}_laravel"

echo ":: Running post-deploy migrations"
APP_CONTAINER_ID="$(docker ps --filter "label=com.docker.swarm.service.name=${STACK_NAME}_laravel" --format '{{.ID}}' | head -n 1)"

if [[ -z "${APP_CONTAINER_ID}" ]]; then
    echo "Could not find a running container for ${STACK_NAME}_laravel"
    exit 1
fi

docker exec "${APP_CONTAINER_ID}" php artisan migrate --force

echo "✅ Swarm deployment complete"
