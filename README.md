# About the Project
This project provides a solution for containerizing Laravel projects.

The production environment is automatically configured in the Traefik load balancer.

Solution structure:
- `./deploy` directory - contains bash scripts for environment deployment without using CI.
- `./docker` directory - contains Docker files, configuration, and docker-compose files.
- `.gitlab-ci.simple.yml` and `.gitlab-ci.build-server.yml` files - used for CI implementation through GitLab.

## Environments

### Local Environment
This environment is used by developers during local development. With this type of deployment, the project's file system will be mounted inside the container, allowing work on the project in an IDE without needing to rebuild containers when files change.

Local deployment uses two docker-compose files:
- `docker-compose.yml`
- `docker-compose.local.yml`

Configuration override of `docker-compose.yml` is used with `docker-compose.local.yml`.

Deployment happens by running one of the bash scripts in the `./deploy/local` directory:
- `bash.sh` - shell launch. Allows running artisan commands.
- `install.sh` - initial environment initialization.
- `start.sh` - environment startup in Daemon mode.
- `stop.sh` - stopping environment running in Daemon mode.

During deployment, the following services will be started:
- `nginx` - HTTP server.
- `php-fpm` - PHP-FPM server.
- `supervisor` - service allowing background process execution.
- `redis` - Redis server.
- `postgres` - PostgreSQL server.

### Production Environment
This environment is used for server deployment. With this type of deployment, self-sufficient containers are built that are not tied to the project's file system.

Deployment uses two docker-compose files:
- `docker-compose.yml`
- `docker-compose.production.yml`

Two volumes are created in the environment:
- `laravel-app` (for `./storage/app` directory) - allows sharing storage file system between running containers.
- `laravel-log` (for `./storage/logs` directory) - allows storing local logs.

During deployment, the following services will be started:
- `nginx` - HTTP server.
- `php-fpm` - PHP-FPM server.
- `supervisor` - service allowing background process execution.
- `redis` - Redis server.

This environment is configured for automatic integration with deployed Traefik. That is, when the project starts, it will automatically be proxied through Traefik. I'll describe the Traefik configuration separately.

### GitLab CI/CD Environment
This environment is used for server deployment through GitLab CI mechanisms.

This project has 2 sets of files:
- `./.gitlab-ci.build-server.yml` and `./docker/docker-compose.ci.build-server.yml` - for deployment through Build server
- `./.gitlab-ci.simple.yml` and `./docker/docker-compose.ci.simple.yml` - for deployment without Build server (building happens on deploy server)

#### Differences between build types:
In the `simple` case, docker container building happens on the deploy server.
Pros: simpler infrastructure
Cons: production server resources are used for building

In the `build-server` case, the following logic applies:
1. Build server gitlab-runner starts (CI tag)
2. Docker images are built
3. Built images are sent to Docker repository
4. Then deploy server gitlab-runner starts (CD_* tag)
5. Runner pulls new images from docker repository and updates currently running instances

Pros:
- building happens on a separate server, so production server resources aren't used for container building
- images are stored in repository
  Cons: more complex infrastructure

#### Preparing servers for Build server building
1. Install gitlab-runner on Build server and register it with `CI` tag
2. Install gitlab-runner on Deploy server and register it with `CD_*` tag (e.g., `CD_DEV_01` for Dev server and `CD_PROD_01` for Production server)
3. Set up Docker registry (I use Nexus CE)
4. Set global variables in GitLab: CI_DOCKER_REGISTRY, CI_DOCKER_REGISTRY_USER, and CI_DOCKER_REGISTRY_PASSWORD (I think it's clear from the names what each is responsible for)
5. Set the COMPOSE_PROJECT_NAME variable in the project - this is the project name that will be used in image names (e.g., `test-project`)

#### Preparation
1. Choose the build type suitable for you.
2. Rename `/.gitlab-ci.*.yml` to `/.gitlab-ci.yml`
3. Rename `./docker/docker-compose.ci.*.yml` to `./docker/docker-compose.ci.yml`

Further in the documentation, reference will be made to `/gitlab-ci.yml` and `./docker/docker-compose.ci.yml`

## Configuration

### Main Service Preparation
The `./docker/docker-compose.yml` file describes the platform service. Other containers inherit from this service.

If necessary, you can change the configuration:
- TZ - container OS time zone. By default taken from `.env` file.
- INSTALL_BCMATH - flag for installing php `bcmath` extension.
- INSTALL_PHPREDIS - flag for installing php `redis` extension.
- INSTALL_OPCACHE - flag for installing php `opcache` extension.
- INSTALL_IMAGEMAGICK - flag for installing php `imagick` extension.
- INSTALL_EXIF - flag for installing php `exif` extension.
- INSTALL_PCNTL - flag for installing php `pcntl` extension.
- INSTALL_INTL - flag for installing php `intl` extension.
- INSTALL_SOAP - flag for installing php `soap` extension.
- INSTALL_PGSQL - flag for installing php `pgsql` and `pdo_pgsql` extensions.
- INSTALL_MYSQL - flag for installing php `pdo_mysql` extension.
- INSTALL_GETTEXT - flag for installing php `gettext` extension.
- INSTALL_SOCKETS - flag for installing php `sockets` extension.
- INSTALL_MEMCACHED - flag for installing php `memcached` extension.
- INSTALL_PECL_SYNC - flag for installing php `sync` extension.
- INSTALL_PECL_MONGODB - flag for installing php `mongodb` extension.
- OPENSSL_ENABLE_GOST_SUPPORT - flag for installing custom `openssl` build with GOST cryptography support

### Local Environment

In the project root, in the .env file, change the following variables:
```text
DB_CONNECTION=pgsql
DB_HOST=postgres # connect to deployed PostgreSQL server
DB_PORT=5432
DB_DATABASE=default
DB_USERNAME=default
DB_PASSWORD=secret

SESSION_DRIVER=redis
QUEUE_CONNECTION=redis
CACHE_STORE=redis
REDIS_HOST=redis # connect to deployed Redis server
```

In the docker directory, create a .env file (`./docker/.env`)
```text
COMPOSE_PROJECT_NAME=project-name # Give a name to the project

TIMEZONE=Europe/Moscow # Server time zone

### Nginx #####################################################
NGINX_HOST_IP=0.0.0.0 # Deployed server IP. 0.0.0.0 - accessible from network, 127.0.0.1 - accessible only locally
NGINX_HOST_PORT=82 # server port, use for simultaneous deployment of multiple environments

### PostgreSQL ################################################
POSTGRES_HOST_IP=0.0.0.0 # Deployed server IP. 0.0.0.0 - accessible from network, 127.0.0.1 - accessible only locally
POSTGRES_HOST_PORT=54322 # server port, use for simultaneous deployment of multiple environments
POSTGRES_DB=default # default database name
POSTGRES_USER=default # database user
POSTGRES_PASSWORD=secret # database password

### Containers ################################################
CONTAINER_NAME_DELIMITER="-" # Image name delimiter. In my experience, Docker sometimes gives image names with "-", and sometimes with "_". If you get an "Image not found" error during building, check image naming - the problem might be in this variable
```

After configuration, you can run `./deploy/local/install.sh` to build the environment.
Then use `./deploy/local/start.sh` to start.

### Production Environment

In the project root, in the .env file, change the following variables:
```text
SESSION_DRIVER=redis
QUEUE_CONNECTION=redis
CACHE_STORE=redis
REDIS_HOST=redis # connect to deployed Redis server
```

In the docker directory, create a .env file (`./docker/.env`)
```text
COMPOSE_PROJECT_NAME=project-name # Give a name to the project

TIMEZONE=Europe/Moscow # Server time zone

### Nginx #####################################################
NGINX_TRAEFIK_DOMAIN=api.tg.starwords.ru # Project domain used for domain forwarding in Traefik

### Containers ################################################
CONTAINER_NAME_DELIMITER="-" # Image name delimiter. In my experience, Docker sometimes gives image names with "-", and sometimes with "_". If you get an "Image not found" error during building, check image naming - the problem might be in this variable
```

After configuration, you can run `./deploy/production/install.sh` to build the environment.
Then use `./deploy/production/start.sh` to start.

### GitLab CI Environment

The `gitlab-ci.yml` file contains container build arguments:
- TIMEZONE - Server time zone
- CONTAINER_NAME_DELIMITER - Image name delimiter. In my experience, Docker sometimes gives image names with "-", and sometimes with "_". If you get an "Image not found" error during building, check image naming - the problem might be in this variable

The `./docker/docker-compose.ci.yml` file contains the `x-laravel-env` variable forwarding block. This block is designed to forward environment variables from "GitLab CI/CD Variables" to running containers.

The following format is chosen for description:
`APP_NAME: ${LARAVEL_APP_NAME}` - reads as: put the content of GitLab variable `LARAVEL_APP_NAME` into env variable `APP_NAME`.

You can extend this block with any variables you need.

So before deployment, you need to:
- describe all variables used in the local `.env` file in the `x-laravel-env` block
- populate GitLab CI/CD Variables with variables
- run deployment

The following variables are used in this example - don't forget to fill them:
- LARAVEL_APP_NAME
- LARAVEL_APP_KEY
- DOMAIN
- LARAVEL_DB_CONNECTION
- LARAVEL_DB_HOST
- LARAVEL_DB_PORT
- LARAVEL_DB_DATABASE
- LARAVEL_DB_USERNAME
- LARAVEL_DB_PASSWORD

## Supervisor
I decided to describe a separate block for supervisor configuration. By default, supervisor is only used for `scheduled tasks`.

Configuration is located in the `./docker/supervisor/conf/supervisor/conf.d/laravel.conf` file.

The configuration has a commented block that ensures the [`horizon`](https://laravel.com/docs/11.x/horizon) library works.
If you use this library - uncomment the block and rebuild the `supervisor` image.

## Traefik
For project deployment, I like the [Traefik](https://traefik.io) load balancer.

In the `./docker/traefik` directory, I've prepared a ready configuration that:
- Searches for containers by label and automatically proxies traffic to them according to these containers' configuration (i.e., configuration is described in services, not in Traefik). This allows deploying Traefik once and not touching it again.
- Automatic obtaining and renewal of [Let's Encrypt](https://letsencrypt.org) certificates

Installation:
- copy the contents of the `./docker/traefik` directory to your server in any location.
- create a .env file based on `env.example` and change the `ADMIN_EMAIL` variable in it.
- run the network creation command `docker network create traefik-net`.
- run `docker compose up -d`.
- you can check functionality by going to `http://{IP}:8080`.

*Note: this build listens to containers in the `traefik-net` network. If traffic isn't being proxied, check that the container is connected to the `traefik-net` network.*

After successful setup, edit `docker-compose.yml`:
- Change `--log.level=DEBUG` to `--log.level=ERROR`
- Remove port forwarding `8080:8080` from the `ports` block
- Restart traefik (`docker compose down && docker compose up -d`)

## Adding to Existing Project

- Copy the `./deploy` and `./docker` directories to your project
- Copy the `gitlab-ci.yml` file to your project
- Configure according to the instructions above
- If scripts from the `./deploy` directory won't run, give execution permissions with the command `cd ./deploy/local && chmod +x *`
