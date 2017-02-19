FROM mysql

LABEL maintainer "Mathieu Hendey"

COPY ./docker-healthcheck /usr/local/bin/

HEALTHCHECK --interval=10s --timeout=5s --retries=10 CMD /bin/bash -c 'docker-healthcheck'
