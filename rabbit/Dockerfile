FROM rabbitmq:3-management

LABEL maintainer "Mathieu Hendey"

COPY ./docker-healthcheck /usr/local/bin/

HEALTHCHECK --interval=5s --timeout=4s CMD /bin/bash -c 'docker-healthcheck'
