FROM debian:jessie

MAINTAINER Mathieu Hendey

RUN apt-get update && apt-get install -y nginx

ADD nginx.conf /etc/nginx/
ADD twitteranalyser.conf /etc/nginx/sites-available/

RUN ln -s /etc/nginx/sites-available/twitteranalyser.conf /etc/nginx/sites-enabled/twitteranalyser
RUN rm /etc/nginx/sites-enabled/default

RUN echo "upstream php-upstream { server frontend:9000; }" > /etc/nginx/conf.d/upstream.conf

RUN usermod -u 1000 www-data

CMD ["nginx"]

EXPOSE 80
EXPOSE 443
