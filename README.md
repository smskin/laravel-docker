# About the Project
This project provides a solution for containerizing a Laravel project.

The production environment is automatically configured in the Traefik load balancer.

Project Structure:
- `./deploy` directory — contains bash scripts for deploying the environment without using CI.
- `./docker` directory — contains Dockerfiles, configurations, and docker-compose files.
- `.gitlab-ci.yml` file — used for implementing CI through GitLab.

## Environments

### Local Environment
This environment is used by developers for local development. In this deployment type, the project's filesystem will be mounted inside the container, which allows working on the project in an IDE without needing to rebuild the containers when files are changed.

Two docker-compose files are used in the local deployment:
- `docker-compose.yml`
- `docker-compose.local.yml`

The `docker-compose.local.yml` overrides the `docker-compose.yml` configuration.

Deployment is performed by running one of the bash scripts in the `./deploy/local` directory:
- `bash.sh` -  starts the shell, allows running Artisan commands.
- `install.sh` - initial environment setup.
- `start.sh` - starts the environment in Daemon mode.
- `stop.sh` - stops the environment running in Daemon mode.

The following services will be started during deployment:
- `nginx` - HTTP server.
- `php-fpm` - PHP-FPM server.
- `supervisor` - is a service that allows running background processes.
- `redis` - Redis server.
- `postgres` - PostgreSQL server.

### Production Environment
This environment is used for deployment on the server. In this deployment type, self-contained containers are built, not linked to the project's filesystem.

Two docker-compose files are used in the production deployment:
- `docker-compose.yml`
- `docker-compose.production.yml`

Two volumes are created in the environment:
- `laravel-app` (for the `./storage/app` directory) -  allows sharing the storage filesystem between running containers.
- `laravel-log` (for the `./storage/logs` directory) - stores local logs.

The following services will be started during deployment:
- `nginx` - HTTP server.
- `php-fpm` - PHP-FPM server.
- `supervisor` - is a service that allows running background processes.
- `redis` - Redis server.

This environment is configured for automatic integration with the deployed Traefik. This means that, upon project startup, it will automatically be proxied through Traefik. The Traefik configuration will be described separately.

### GitLab CI Environment
his environment is used for deployment on the server through GitLab CI mechanisms.

CI initialization is performed using the `./gitlab-ci.yml` file. The principle is similar to the previous deployment type, but the following docker-compose files are used:
- `docker-compose.yml`
- `docker-compose.ci.yml`

## Configuration

### Setting Up the Main Service
The `./docker/docker-compose.yml` file describes the platform service. Other containers inherit from this service. 

If necessary, you can modify the following configurations:
- TZ — the time zone of the container OS. By default, it is taken from the `.env` file.
- INSTALL_BCMATH — flag to install the PHP `bcmath` extension.
- INSTALL_PHPREDIS — flag to install the PHP `redis` extension.
- INSTALL_OPCACHE — flag to install the PHP `opcache` extension.
- INSTALL_IMAGEMAGICK — flag to install the PHP `imagick` extension.
- INSTALL_EXIF — flag to install the PHP `exif` extension.
- INSTALL_PCNTL — flag to install the PHP `pcntl` extension.
- INSTALL_INTL — flag to install the PHP `intl` extension.
- INSTALL_SOAP — flag to install the PHP `soap` extension.
- INSTALL_PGSQL — flag to install the PHP `pgsql` and `pdo_pgsql` extensions.
- INSTALL_MYSQL — flag to install the PHP `pdo_mysql` extension.
- INSTALL_GETTEXT — flag to install the PHP `gettext` extension.
- INSTALL_SOCKETS — flag to install the PHP `sockets` extension.
- INSTALL_MEMCACHED — flag to install the PHP `memcached` extension.
- INSTALL_PECL_SYNC — flag to install the PHP `sync` extension.
- INSTALL_PECL_MONGODB — flag to install the PHP `mongodb` extension.
- OPENSSL_ENABLE_GOST_SUPPORT - flag for enable OpenSSL GOST provider support

### Local Environment

In the project root, modify the following variables in the `.env` file:
```text
DB_CONNECTION=pgsql
DB_HOST=postgres # connect to the deployed PostgreSQL server
DB_PORT=5432
DB_DATABASE=default
DB_USERNAME=default
DB_PASSWORD=secret

SESSION_DRIVER=redis
QUEUE_CONNECTION=redis
CACHE_STORE=redis
REDIS_HOST=redis # connect to the deployed Redis server
```

In the `docker` directory, create the `.env` file (if not already created) (`./docker/.env`):
```text
COMPOSE_PROJECT_NAME=project-name # Project name

TIMEZONE=Europe/Moscow # Server timezone

### Nginx #####################################################
NGINX_HOST_IP=0.0.0.0 # IP of the deployed server. 0.0.0.0 — accessible from the network, 127.0.0.1 — accessible only locally
NGINX_HOST_PORT=82 # Server port, use for simultaneous deployment of multiple environments

### PostgreSQL ################################################
POSTGRES_HOST_IP=0.0.0.0 # IP of the deployed server. 0.0.0.0 — accessible from the network, 127.0.0.1 — accessible only locally
POSTGRES_HOST_PORT=54322 # Server port, use for simultaneous deployment of multiple environments
POSTGRES_DB=default # Default database name
POSTGRES_USER=default # Database user
POSTGRES_PASSWORD=secret # Database password

### Containers ################################################
CONTAINER_NAME_DELIMITER="-" #  Separator for image names. When building, Docker sometimes gives image names with "-", sometimes with "_". If you encounter an "Image not found" error during build, check the image names, the problem may lie in this variable
```

After setting it up, you can run `./deploy/local/install.sh` to build the environment. Then, use `./deploy/local/start.sh` to start it.

### Production Environment

In the project root, modify the following variables in the `.env` file:
```text
SESSION_DRIVER=redis
QUEUE_CONNECTION=redis
CACHE_STORE=redis
REDIS_HOST=redis # connect to the deployed Redis server
```

In the `docker` directory, create the `.env` file (if not already created) (`./docker/.env`):
```text
COMPOSE_PROJECT_NAME=project-name # Project name

TIMEZONE=Europe/Moscow # Server timezone

### Nginx #####################################################
NGINX_TRAEFIK_DOMAIN=api.tg.starwords.ru # Project domain used for forwarding the domain to Traefik

### Containers ################################################
CONTAINER_NAME_DELIMITER="-" # Separator for image names. When building, Docker sometimes gives image names with "-", sometimes with "_". If you encounter an "Image not found" error during build, check the image names, the problem may lie in this variable
```

After setting it up, you can run `./deploy/production/install.sh` to build the environment. Then, use `./deploy/production/start.sh` to start it.

### GitLab CI Environment

In the `gitlab-ci.yml` file, the build container arguments are specified:
- TIMEZONE - the server's time zone.
- CONTAINER_NAME_DELIMITER - separator for image names. When building, Docker sometimes gives image names with "-", sometimes with "_". If you encounter an "Image not found" error during build, check the image names, the problem may lie in this variable.

In the `./docker/docker-compose.ci.yml` file, there is a block for passing variables `x-laravel-env`. This block is used to pass environment variables from the "GitLab CI/CD Variables" into the containers being started.

The following format is used for the description: 
`APP_NAME: ${LARAVEL_APP_NAME}` — reads as: put the contents of the GitLab variable `LARAVEL_APP_NAME` into the `APP_NAME` environment variable.

You can extend this block with any variables you need.

So, before deploying:
- Describe all the variables used in the local `.env` file in the `x-laravel-env block`.
- Populate the GitLab CI/CD Variables with the required variables.
- Start the deployment.

In this example, the following variables are used — don't forget to fill them in:
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
For the Supervisor configuration, I decided to describe a separate block. By default, Supervisor is only used for scheduled tasks.

The configuration is located in the file `./docker/supervisor/conf/supervisor/conf.d/laravel.conf`.

There is a commented block in the configuration that ensures the operation of the [`Horizon library`](https://laravel.com/docs/11.x/horizon). If you are using this library, uncomment the block and rebuild the `supervisor` image.

## Traefik
For project deployment, I prefer using the [Traefik load balancer](https://traefik.io).

In the `./docker/traefik` directory, I have prepared a ready-made configuration, which:
- Finds containers by label and automatically proxies traffic to them based on the configuration of these containers (i.e., the configuration is described in services, not in Traefik). This allows you to deploy Traefik once and never touch it again.
- Automatically obtains and renews [Let’s Encrypt certificates](https://letsencrypt.org).

Installation:
- Copy the contents of the `./docker/traefik` directory to your server in any location.
- Create a .env file based on `env.example` and change the `ADMIN_EMAIL` variable in it.
- Run the command to create the network: `docker network create traefik-net`.
- Run `docker compose up -d`.
- You can check if it's working by visiting `http://{IP}:8080`.

*Note: this build listens for containers in the `traefik-net` network. If the traffic is not proxied, check that the container is connected to the `traefik-net` network.*

After successful configuration, edit the `docker-compose.yml`:
- Change `--log.level=DEBUG` to `--log.level=ERROR`
- Remove port forwarding `8080:8080` from the `ports` block.
- Restart Traefik (`docker compose down && docker compose up -d`)

## Adding to an Existing Project
- Copy the `./deploy` and `./docker` directories into your project.
- Copy the `gitlab-ci.yml` file.
- Configure it as described above.
- If the scripts in the `./deploy` directory don't run, give execution permissions by running `cd ./deploy/local && chmod +x *`.
