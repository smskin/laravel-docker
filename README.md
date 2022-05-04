# Example of Laravel project for in docker deployment
This is production ready example of project based on Laravel framework and docker.

## Docker services
### Platform (platform)
Base docker service with installed php extensions for using it's later. This service based on official php-fpm image.

Building process has this steps:
- Installing composer
- Installing nodejs (if required, installation defines by INSTALL_NODE argument)
- Installing php extensions (installation defines by INSTALL_PHP_EXT_* arguments)
- Installing image optimizers (installation defines by OPTIMIZE_PUBLIC_IMAGES argument)

### App (app)
Base docker service, that on build copies source code, installs dependencies, builds js & css assets and optimize it for use in production.
This service based on platform service.

Building process has this steps:
- Copying source code
- Installing composer dependencies
- Installing npm dependencies (if required, installation defines by INSTALL_NODE argument)
- Building css & js assets by webpack (if required, the process is regulated by the presence of INSTALL_NODE argument)
- Optimizing jpeg & png images (stored in the public folder, the process is regulated by the presence of OPTIMIZE_PUBLIC_IMAGES argument)
- Compressing public assets (css, js, eot, svg, ttf, woff, html) for reduce the load on the nginx server gzip module (ngx_http_gzip_static_module)

As a result, we get container with sources, installed dependencies, compressed and optimized assets for use it in future services.

### PHP CLI (php-cli)
Command prompt for execute artisan commands, installs composer & npm dependencies, build js & css assets by webpack.

Building this service rewrites some default configs for optimize.
This service based on app service (described before) and it updates  on base container rebuilds.

### PHP FPM (php-fpm)
Service php-fpm for observe requests from nginx.

Building this service rewrites some default configs for optimize.
This service based on app service (described before) and it updates  on base container rebuilds.

### Nginx (nginx)
Frontend service for processing incoming http requests.

Building this service rewrites some default configs for optimize.
Important changes in config:
- Configured expires headers (ETag, Cache-Control, Expires)
- Activated gzip for compress assets

Source code copies from app service.

### Supervisor (supervisor)
Supervisor process control system need for:
- execution scheduled tasks (https://laravel.com/docs/9.x/scheduling#introduction)
- execution queueable jobs & listeners via horizon (https://laravel.com/docs/9.x/queues#main-content)

Documentation: https://laravel.com/docs/9.x/horizon#supervisors

### PostgreSQL (postgres)
PostgreSQL is open source object-relational database system

### Redis (redis)
In-memory data store. Uses as storage for:
- php sessions
- laravel cache
- queueable jobs & listeners (for execute it later by horizon)

### Memcached (memcached)
In-memory data store. Uses as storage for operation, that need fast runtime (mutex, for example)

### MinIO (minio)
MinIO offers high-performance, S3 compatible object storage. 

It uses as primary storage for user files (for replace local project storage).

We can't store files in project file structure because it drops on rebuild containers. We need share files between nginx, php-fpm, php-cli & supervisor services. Using volumes is not good idea. As volume  replacement i using this storage.

Documentation: https://docs.min.io/

### MinIO installation service (minio-install)
This is service, that starts only on installation. It configures MinIO, creates users, creates and configures default buckets

Documentation: https://docs.min.io/

### Imaginary (imaginary)
Fast HTTP microservice for high-level image processing. I use it to process custom images. It allows you to manipulate images and convert them from one format to another.

Project repository: https://github.com/h2non/imaginary

## Env files
### Docker env (docker/.env)
```angular2html
COMPOSE_PROJECT_NAME=example-app # name of current project.
TIMEZONE=Europe/Moscow # system time zone of the services
OPTIMIZE_PUBLIC_IMAGES=true # flag for optimize public images (in public folder)

### Nginx #################################################
NGINX_HOST_IP=0.0.0.0 # binding ip address of nginx service
NGINX_HOST_PORT=81 # binding port of nginx service
NGINX_TRAEFIK_DOMAIN=example.com # Traefik label: public domain
NGINX_TRAEFIK_FRONTEND_ENTRY_POINTS=http,https # Traefik label: allowed protocols
NGINX_TREFIK_SSL_REDIRECT=true # Traefik label: redirect from http to https
NGINX_TRAEFIK_FORCE_SSL=true # Traefik label: force ssl header
NGINX_TRAEFIK_WEIGHT=1 # Traefik label: weight for balancer

### PostgreSQL #################################################
POSTGRES_HOST_IP=0.0.0.0 # binding ip address of PostgreSQL service
POSTGRES_HOST_PORT=54321 # binding port of PostgreSQL service
POSTGRES_DB=default # the database that will be created at the first launch
POSTGRES_USER=default # user of default database
POSTGRES_PASSWORD=secret # password of user

### PHP Settings #################################################
INSTALL_PHP_EXT_BCMATH=true # flag for install bcmath php extension
INSTALL_PHP_EXT_PHPREDIS=true # flag for install redis php extension
INSTALL_PHP_EXT_OPCACHE=true # flag for install opcache php extension
INSTALL_PHP_EXT_IMAGEMAGICK=false # flag for install imagemagick php extension
INSTALL_PHP_EXT_EXIF=false # flag for install exif php extension
INSTALL_PHP_EXT_PCNTL=true  # flag for install pcntl php extension
INSTALL_PHP_EXT_INTL=false  # flag for install intl php extension
INSTALL_PHP_EXT_SOAP=false  # flag for install soap php extension
INSTALL_PHP_EXT_PGSQL=true  # flag for install pgsql & pdo_pgsql php extensions
INSTALL_PHP_EXT_MYSQL=false # flag for install pdo_mysql php extension
INSTALL_PHP_EXT_GETTEXT=false # flag for install gettext php extension
INSTALL_PHP_EXT_SOCKETS=false # flag for install sockets php extension
INSTALL_PHP_EXT_MEMCACHED=true # flag for install memcached php extension

### NodeJS Settings #################################################
INSTALL_NODE=true  # flag for install NodeJS
NODE_VERSION=node # version of installation NodeJS
INSTALL_NPM_GULP=true # flag for install gulp
INSTALL_NPM_BOWER=false # flag for install bower
INSTALL_NPM_VUE_CLI=true # flag for install vue-cli
INSTALL_NPM_ANGULAR_CLI=false # flag for install angular-cli
NPM_REGISTRY= # custom NPM registry url
NPM_FETCH_RETRIES=2 # npm config
NPM_FETCH_RETRY_FACTOR=10 # npm config
NPM_FETCH_RETRY_MINTIMEOUT=10000 # npm config
NPM_FETCH_RETRY_MAXTIMEOUT=60000 # npm config
NVM_NODEJS_ORG_MIRROR= # url of nodejs mirror

### Minio Settings ###################################################
MINIO_API_HOST_IP=0.0.0.0 # binding ip address of MinIO service
MINIO_API_HOST_PORT=9000 # binding port of MinIO service
MINIO_CONSOLE_HOST_IP=0.0.0.0 # binding ip address of MinIO Admin service
MINIO_CONSOLE_HOST_PORT=9001 # binding port of MinIO Admin service
MINIO_ROOT_USER=minioadmin # root user of MinIO Admin service
MINIO_ROOT_PASSWORD=minioadmin # password of root user
MINIO_ACCESS_KEY=laravel # user for integration with Laravel
MINIO_SECRET_KEY= # secret key of user (64 random characters)
MINIO_TRAEFIK_DOMAIN=minio.example.com # Traefik label: public domain
MINIO_TRAEFIK_FRONTEND_ENTRY_POINTS=http,https  # Traefik label: allowed protocols
MINIO_TREFIK_SSL_REDIRECT=true # Traefik label: redirect from http to https
MINIO_TRAEFIK_FORCE_SSL=true # Traefik label: force ssl header
MINIO_TRAEFIK_WEIGHT=1 # Traefik label: weight for balancer

### Imaginary Settings ###############################################
IMAGINARY_HOST_IP=0.0.0.0  # binding ip address of Imaginary service
IMAGINARY_HOST_PORT=9002 # binding port of Imaginary service
IMAGINARY_ALLOWED_ORIGINS=http://minio:9000/ # Allowed remote urls
IMAGINARY_TRAEFIK_DOMAIN=imaginary.example.com # Traefik label: public domain
IMAGINARY_TRAEFIK_FRONTEND_ENTRY_POINTS=http,https # Traefik label: allowed protocols
IMAGINARY_TREFIK_SSL_REDIRECT=true # Traefik label: redirect from http to https
IMAGINARY_TRAEFIK_FORCE_SSL=true # Traefik label: force ssl header
IMAGINARY_TRAEFIK_WEIGHT=1 # Traefik label: weight for balancer

### Redis WebUI Settings #############################################
REDIS_WEBUI_HOST_IP=0.0.0.0 # binding ip address of Redis Web UI service
REDIS_WEBUI_HOST_POST=9987 # binding port of Redis Web UI service
REDIS_WEBUI_USERNAME=admin # user for access to Web UI
REDIS_WEBUI_PASSWORD=admin # password of user

### PHP PG Admin Settings ############################################
PHP_PG_ADMIN_HOST_IP=0.0.0.0 # binding ip address of PHP Pg Admin service
PHP_PG_ADMIN_HOST_POST=8060 # binding port of PHP Pg Admin service
```

### Laravel env (.env)
```angular2html
DB_CONNECTION=pgsql
DB_HOST=postgres #internal link to docker service with PostgreSQL
DB_PORT=5432
DB_DATABASE=default # default database (defined in docker env in POSTGRES_DB variable)
DB_USERNAME=default # default user (defined in docker env in POSTGRES_USER variable)
DB_PASSWORD=secret # password of user (defined in docker env in POSTGRES_PASSWORD variable)

CACHE_DRIVER=redis # store cache in redis
FILESYSTEM_DRIVER=minio_private # minio private filesystem driver as default filesystem driver
QUEUE_CONNECTION=redis # redis as default queue connection
SESSION_DRIVER=redis # store sessions in redis

MEMCACHED_HOST=memcached #internal link to docker service with Memcached

REDIS_HOST=redis  # internal link to docker service with Redis
REDIS_PASSWORD=null
REDIS_PORT=6379

MINIO_ACCESS_KEY_ID=laravel # MinIO user (defined in docker env in MINIO_ACCESS_KEY variable)
MINIO_SECRET_ACCESS_KEY= # MinIO secret key (defined in docker env in MINIO_SECRET_KEY variable)
MINIO_DEFAULT_REGION=us-east-1
MINIO_USE_PATH_STYLE_ENDPOINT=true
MINIO_ENDPOINT=http://minio:9000 # internal link to minio api webservice
MINIO_BUCKET_PUBLIC=public
MINIO_BUCKET_PUBLIC_URL=http://minio.example.com/public # public link to minio public bucket
MINIO_BUCKET_PRIVATE=private
MINIO_BUCKET_PRIVATE_URL=http://minio.example.com/private # public link to minio private bucket
```

## Deployment
This projects has 2 sets of deployment scripts:
- for local deployment
- for production deployment

Scripts:
- bash.sh - connects to php-cli console. In console you can run artisan commands, install composer & npm packages
- install.sh - installation script. It prepares deployment for use
- migrate.sh - script for execute database migrations
- start.sh - script starts services in daemon (background) mode
- stop.sh - script stops ran services
- update.sh - script rebuild the services

### Notes for local deployment
When deployed locally, the source code of the project is mounted in a container. This allows you to work on a project in real time without reassembling services.

#### Notes for production deployment.
The product deployment differs from the local one in that Nginx, MinIO and Imaginary stops processing incoming requests coming to it through the port (stops listening to the host port).
They start processing only the traffic coming from the load balancer. I use Traefik (https://traefik.io/traefik/) as a load balancer in the configuration of the hosts by labels.

Documentation: https://doc.traefik.io/traefik/routing/providers/service-by-label/

My configured Traefik service (with bind by labels and Letsencrypt): https://github.com/smskin/traefik

## Example of Laravel projects
In this example, I proposed my own implementation of the modular (service) approach.

Modules place in app/Modules directory.
- Base module is named as Core. It has base classes, that will be extending later.
- The ExampleModule shows an example of the implementation of the modular approach.

Basic idea:
- Modules are facades hiding the implementation. The module contains public methods that can be called from outside the module.
- Module has artisan commands, controllers, actions, requests, listeners, and events
- Artisan commands can call only controllers of this module.
- The module method can take as an argument only an instance of the BaseRequest object
- The module method can call the controller
- The controller can call another controller (of this module), action or event

### Module components
#### Command (Artisan command)
- Artisan commands can't contain any business logic.
- Artisan commands can call only controllers of this module.

#### Requests
- An object that allows you to pass something to the module method.
- Request has validation rules
- Request will be validated by rules on calling of controller (or manually by call the validate method)

#### Controllers
- Base execution class. It contains all business logic of module method
- As an incoming argument, it gets an instance of the BaseRequest class
- Controller can return some object in the result variable
- Controller can call other controllers, actions and events. But only from this module.

#### Actions
- Atomic execution item. It execute only one business action.
- It can't call any classes (excluding classes of external libraries that are necessary to perform this atomic action)
