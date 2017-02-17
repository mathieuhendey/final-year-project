FROM mysql

COPY ./docker-healthcheck /usr/local/bin/

RUN /bin/bash -c 'ls -l /usr/local/bin'

HEALTHCHECK --interval=5s --timeout=4s CMD /bin/bash -c 'docker-healthcheck'

EXPOSE 3306
