FROM mysql

LABEL maintainer="Mathieu Hendey" \
      name="MySQL container with custom schema loaded on container build" \
      version="0.10"

COPY ./docker-healthcheck /usr/local/bin/

HEALTHCHECK --interval=10s --timeout=5s --retries=10 CMD /bin/bash -c 'docker-healthcheck'
