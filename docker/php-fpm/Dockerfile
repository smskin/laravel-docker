ARG COMPOSE_PROJECT_NAME
FROM ${COMPOSE_PROJECT_NAME}_app:latest

USER root

#####################################
#  Copy config:
#####################################
COPY ./conf/php/php.ini /usr/local/etc/php/php.ini
COPY ./conf/php/laravel.ini /usr/local/etc/php/conf.d
COPY ./conf/php/opcache.ini /usr/local/etc/php/conf.d/opcache.ini
COPY ./conf/php/laravel.ini /usr/local/etc/php/conf.d

COPY ./conf/php-fpm/php-fpm.conf /usr/local/etc/php-fpm.conf
COPY ./conf/php-fpm/php-fpm.d /usr/local/etc/php-fpm.d

CMD ["php-fpm"]
