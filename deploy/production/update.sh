#!/usr/bin/env bash

ROOT_FOLDER="$( cd "$( dirname "${BASH_SOURCE[0]}" )" >/dev/null 2>&1 && pwd )"

cd "${ROOT_FOLDER}/.." || exit
git reset --hard
git pull

cd "${ROOT_FOLDER}/../../docker" || exit
docker compose -f docker-compose.yml -f docker-compose.production.yml build app
docker compose -f docker-compose.yml -f docker-compose.production.yml build
docker compose -f docker-compose.yml -f docker-compose.production.yml down
docker compose -f docker-compose.yml -f docker-compose.production.yml up -d
