#!/usr/bin/env bash

ROOT_FOLDER="$( cd "$( dirname "${BASH_SOURCE[0]}" )" >/dev/null 2>&1 && pwd )"
cd "${ROOT_FOLDER}/.." || exit
git pull

cd "${ROOT_FOLDER}/../../docker" || exit
docker network create webproxy
docker compose -f docker-compose.yml -f docker-compose.production.yml build platform
docker compose -f docker-compose.yml -f docker-compose.production.yml build app --no-cache
docker compose -f docker-compose.yml -f docker-compose.production.yml build
docker compose -f docker-compose.yml -f docker-compose.production.yml up -d php-fpm
docker compose -f docker-compose.yml -f docker-compose.production.yml exec php-fpm php artisan storage:link
docker compose -f docker-compose.yml -f docker-compose.production.yml --profile install down
docker compose -f docker-compose.yml -f docker-compose.production.yml down
