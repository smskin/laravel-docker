ARG PLATFORM_CONTAINER_NAME
ARG APP_CONTAINER_NAME
FROM ${PLATFORM_CONTAINER_NAME} as supervisor

USER root

RUN apt-get update && \
    apt-get install -y supervisor

COPY ./conf/supervisor /etc/supervisor

#####################################
#  Clean up APT:
#####################################
RUN apt-get clean && rm -rf /var/lib/apt/lists/* /tmp/* /var/tmp/*

FROM ${APP_CONTAINER_NAME} as app
FROM supervisor

USER root
COPY --from=app /var/www/html /var/www/html

ENTRYPOINT ["supervisord", "--nodaemon", "--configuration", "/etc/supervisor/supervisord.conf"]
