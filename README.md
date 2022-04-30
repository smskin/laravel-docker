# Example of Laravel project for in docker deployment
This is production ready example of project based on Laravel framework and docker.

## Docker services
### App (app)
Base docker container, that on build copies source code, installs dependencies, builds js & css assets and optimize it for use in production.

Building process has this steps:
- Installing composer
- Installing nodejs (if required, installation defines by INSTALL_NODE argument)
- Installing php extensions (installation defines by INSTALL_PHP_EXT_* arguments)
- Installing image optimizers (installation defines by OPTIMIZE_PUBLIC_IMAGES argument)
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
