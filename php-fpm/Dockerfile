FROM debian:jessie

MAINTAINER Mathieu Hendey

RUN \
  apt-get update && \
  apt-get install -y \
  curl \
  wget \
  git \
  apt-transport-https \
  lsb-release \
  ca-certificates

RUN wget -O /etc/apt/trusted.gpg.d/php.gpg https://packages.sury.org/php/apt.gpg && \
  echo "deb http://packages.dotdeb.org jessie all" >> /etc/apt/sources.list.d/dotdeb.org.list && \
  echo "deb-src http://packages.dotdeb.org jessie all" >> /etc/apt/sources.list.d/dotdeb.org.list && \
  echo "deb https://packages.sury.org/php/ $(lsb_release -sc) main" > /etc/apt/sources.list.d/php.list && \
  wget -O- http://www.dotdeb.org/dotdeb.gpg | apt-key add -

RUN apt-get update && apt-get install -y php7.1-dev php7.1-fpm php7.1-cli php7.1-mcrypt php7.1-mysql php7.1-apcu php7.1-gd php7.1-imagick php7.1-curl php7.1-intl php7.1-xml php7.1-zip git

ADD symfony.ini /etc/php7.1/fpm/conf.d/
ADD symfony.ini /etc/php7.1/cli/conf.d/

ADD symfony.pool.conf /etc/php7.1/fpm/pool.d/

RUN sed -i "/listen = .*/c\listen = [::]:9000" /etc/php/7.1/fpm/pool.d/www.conf \
    && sed -i "/;access.log = .*/c\access.log = /proc/self/fd/2" /etc/php/7.1/fpm/pool.d/www.conf \
    && sed -i "/;clear_env = .*/c\clear_env = no" /etc/php/7.1/fpm/pool.d/www.conf \
    && sed -i "/;catch_workers_output = .*/c\catch_workers_output = yes" /etc/php/7.1/fpm/pool.d/www.conf \
    && sed -i "/pid = .*/c\;pid = /run/php/php7.1-fpm.pid" /etc/php/7.1/fpm/php-fpm.conf \
    && sed -i "/;daemonize = .*/c\daemonize = no" /etc/php/7.1/fpm/php-fpm.conf \
    && sed -i "/error_log = .*/c\error_log = /proc/self/fd/2" /etc/php/7.1/fpm/php-fpm.conf \
    && usermod -u 1000 www-data

RUN git clone https://github.com/nikic/php-ast.git &&  cd php-ast && /usr/bin/phpize && ./configure && make && make install && echo 'extension=ast.so' > /etc/php/7.1/cli/php.ini

CMD ["/usr/sbin/php-fpm7.1"]

EXPOSE 9000
