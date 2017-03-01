FROM debian:jessie

LABEL maintainer "Mathieu Hendey"

RUN \
  apt-get update && apt-get install -y \
  git \
  wget \
  curl \
  make \
  apt-transport-https \
  lsb-release \
  ca-certificates \
  vim

  RUN wget -O- https://packages.sury.org/php/apt.gpg | apt-key add - && \
      echo "deb https://packages.sury.org/php/ $(lsb_release -sc) main" > /etc/apt/sources.list.d/php.list

RUN apt-get update && apt-get install -y --no-install-recommends \
  php7.1-apcu \
  php7.1-cli \
  php7.1-curl \
  php7.1-dev \
  php7.1-fpm \
  php7.1-intl \
  php7.1-mcrypt \
  php7.1-mysqli \
  php7.1-opcache \
  php7.1-xml \
  php7.1-zip

COPY timezone.ini /etc/php7.1/fpm/conf.d/
COPY timezone.ini /etc/php7.1/cli/conf.d/
COPY twitteranalyser.pool.conf /etc/php7.1/fpm/pool.d/

RUN sed -i "/listen = .*/c\listen = [::]:9000" /etc/php/7.1/fpm/pool.d/www.conf \
    && sed -i "/;access.log = .*/c\access.log = /proc/self/fd/2" /etc/php/7.1/fpm/pool.d/www.conf \
    && sed -i "/;clear_env = .*/c\clear_env = no" /etc/php/7.1/fpm/pool.d/www.conf \
    && sed -i "/;catch_workers_output = .*/c\catch_workers_output = yes" /etc/php/7.1/fpm/pool.d/www.conf \
    && sed -i "/pid = .*/c\;pid = /run/php/php7.1-fpm.pid" /etc/php/7.1/fpm/php-fpm.conf \
    && sed -i "/;daemonize = .*/c\daemonize = no" /etc/php/7.1/fpm/php-fpm.conf \
    && sed -i "/error_log = .*/c\error_log = /proc/self/fd/2" /etc/php/7.1/fpm/php-fpm.conf \
    && usermod -u 1000 www-data

ENV PHP_ERROR_REPORTING "E_ALL \& ~E_NOTICE \& ~E_STRICT \& ~E_DEPRECATED"

CMD ["/usr/sbin/php-fpm7.1"]
