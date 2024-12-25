ARG PLATFORM_CONTAINER_NAME
FROM ${PLATFORM_CONTAINER_NAME}

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

WORKDIR /var/www/html
