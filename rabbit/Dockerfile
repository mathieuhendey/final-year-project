FROM rabbitmq:3-management

LABEL maintainer="Mathieu Hendey" \
      name="Queue for storing Tweets while waiting for the classifier" \
      version="0.10"

COPY ./docker-healthcheck /usr/local/bin/

HEALTHCHECK --interval=5s --timeout=4s CMD /bin/bash -c 'docker-healthcheck'
