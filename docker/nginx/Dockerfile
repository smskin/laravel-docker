ARG APP_CONTAINER_NAME
FROM nginx:latest as nginx

#####################################
# Set Timezone
#####################################
ARG TZ=UTC
RUN ln -snf /usr/share/zoneinfo/$TZ /etc/localtime && echo $TZ > /etc/timezone

#####################################
# Set logs output
#####################################
RUN ln -sf /dev/stdout /var/log/nginx/access.log && ln -sf /dev/stderr /var/log/nginx/error.log

#####################################
#  Copy config:
#####################################
COPY ./conf/nginx/nginx.conf /etc/nginx/nginx.conf
COPY ./conf/nginx/conf.d /etc/nginx/conf.d

#####################################
#  Copy sources:
#####################################
FROM ${APP_CONTAINER_NAME} as app
FROM nginx

USER root
COPY --from=app /var/www/html /var/www/html

WORKDIR /var/www/html
