#!/usr/bin/env bash

ROOT_FOLDER="$( cd "$( dirname "${BASH_SOURCE[0]}" )" >/dev/null 2>&1 && pwd )"
cd "${ROOT_FOLDER}/../../docker" || exit

docker compose -f docker-compose.yml -f docker-compose.local.yml exec -u root php-fpm bash
