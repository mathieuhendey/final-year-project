FROM debian:jessie

LABEL maintainer="Mathieu Hendey" \
      name="Nginx for serving PHP FPM to the Internet" \
      version="0.10"

RUN apt-get update \
    && apt-get install -y --no-install-recommends nginx \
    && apt-get clean

COPY nginx.conf /etc/nginx/
COPY twitteranalyser.conf /etc/nginx/sites-available/

RUN ln -s /etc/nginx/sites-available/twitteranalyser.conf /etc/nginx/sites-enabled/twitteranalyser
RUN rm /etc/nginx/sites-enabled/default

RUN echo "upstream php-upstream { server frontend:9000; }" > /etc/nginx/conf.d/upstream.conf

RUN usermod -u 1000 www-data

CMD ["nginx"]
