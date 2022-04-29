ARG COMPOSE_PROJECT_NAME
FROM ${COMPOSE_PROJECT_NAME}_app:latest

USER root

RUN apt-get update && \
    apt-get install -y supervisor

COPY ./conf/supervisor /etc/supervisor

#####################################
#  Clean up APT:
#####################################
RUN apt-get clean && rm -rf /var/lib/apt/lists/* /tmp/* /var/tmp/*

ENTRYPOINT ["supervisord", "--nodaemon", "--configuration", "/etc/supervisor/supervisord.conf"]
