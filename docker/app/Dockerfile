ARG COMPOSE_PROJECT_NAME
FROM ${COMPOSE_PROJECT_NAME}_platform:latest

#####################################
#  Copy sources:
#####################################
USER root
COPY . /var/www/html
RUN chown -R www-data:www-data /var/www/html

#####################################
#  Install dependencies:
#####################################
USER www-data

RUN php -d memory_limit=-1 /usr/local/bin/composer install --no-dev && \
    php artisan storage:link && \
    php artisan optimize && \
    composer dump-autoload

ARG INSTALL_NODE=false

RUN if [ ${INSTALL_NODE} = true ]; then \
    npm install && \
    npm run production \
;fi

#####################################
#  Optimize public images:
#####################################
ARG OPTIMIZE_PUBLIC_IMAGES=false

RUN if [ ${OPTIMIZE_PUBLIC_IMAGES} = true ]; then \
    find /var/www/html/public -type f -name "*.jpg" -exec jpegoptim --strip-all {} \; && \
    find /var/www/html/public -type f -name "*.jpeg" -exec jpegoptim --strip-all {} \; && \
    find /var/www/html/public -type f -name "*.png" -exec optipng {} \; \
;fi

#####################################
#  Compress public scripts:
#  https://nginx.org/ru/docs/http/ngx_http_gzip_static_module.html
#####################################
RUN ["/var/www/html/docker/app/tools/compress.sh", "/var/www/html/public"]

WORKDIR /var/www/html
