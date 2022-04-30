#!/usr/bin/env bash

ROOT_FOLDER="$( cd "$( dirname "${BASH_SOURCE[0]}" )" >/dev/null 2>&1 && pwd )"
cd "${ROOT_FOLDER}/.." || exit
git pull

cd "${ROOT_FOLDER}/../../docker" || exit
docker compose -f docker-compose.yml -f docker-compose.local.yml build app
docker compose -f docker-compose.yml -f docker-compose.local.yml build
docker compose -f docker-compose.yml -f docker-compose.local.yml up -d php-cli
docker compose -f docker-compose.yml -f docker-compose.local.yml exec php-cli php -d memory_limit=-1 /usr/local/bin/composer install --no-dev
docker compose -f docker-compose.yml -f docker-compose.local.yml exec npm install
docker compose -f docker-compose.yml -f docker-compose.local.yml exec php-cli php artisan storage:link
docker compose -f docker-compose.yml -f docker-compose.local.yml exec -u root php-cli chown www-data:www-data -R /var/www/html/bootstrap/cache
docker compose -f docker-compose.yml -f docker-compose.local.yml exec -u root php-cli chown www-data:www-data -R /var/www/html/storage
docker compose -f docker-compose.yml -f docker-compose.local.yml --profile install up minio-install
docker compose -f docker-compose.yml -f docker-compose.local.yml --profile install down
docker compose -f docker-compose.yml -f docker-compose.local.yml down
