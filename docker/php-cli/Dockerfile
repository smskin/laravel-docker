ARG COMPOSE_PROJECT_NAME
FROM ${COMPOSE_PROJECT_NAME}_app:latest

USER root

#####################################
#  Copy config:
#####################################
COPY ./conf/php/php.ini /usr/local/etc/php/php.ini

USER www-data
