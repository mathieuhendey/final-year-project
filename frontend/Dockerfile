FROM debian:jessie

MAINTAINER Mathieu Hendey

RUN \
  apt-get update && \
  apt-get install -y \
  curl \
  wget \
  git

RUN echo "deb http://packages.dotdeb.org jessie all" >> /etc/apt/sources.list.d/dotdeb.org.list && \
  echo "deb-src http://packages.dotdeb.org jessie all" >> /etc/apt/sources.list.d/dotdeb.org.list && \
  wget -O- http://www.dotdeb.org/dotdeb.gpg | apt-key add -

RUN apt-get update && apt-get install -y php7.0-dev php7.0-fpm php7.0-cli php7.0-mcrypt php7.0-mysql php7.0-apcu php7.0-gd php7.0-imagick php7.0-curl php7.0-intl php7.0-xml php7.0-zip git

ADD timezone.ini /etc/php7.0/fpm/conf.d/
ADD timezone.ini /etc/php7.0/cli/conf.d/

ADD twitteranalyser.pool.conf /etc/php7.0/fpm/pool.d/

RUN sed -i "/listen = .*/c\listen = [::]:9000" /etc/php/7.0/fpm/pool.d/www.conf \
    && sed -i "/;access.log = .*/c\access.log = /proc/self/fd/2" /etc/php/7.0/fpm/pool.d/www.conf \
    && sed -i "/;clear_env = .*/c\clear_env = no" /etc/php/7.0/fpm/pool.d/www.conf \
    && sed -i "/;catch_workers_output = .*/c\catch_workers_output = yes" /etc/php/7.0/fpm/pool.d/www.conf \
    && sed -i "/pid = .*/c\;pid = /run/php/php7.0-fpm.pid" /etc/php/7.0/fpm/php-fpm.conf \
    && sed -i "/;daemonize = .*/c\daemonize = no" /etc/php/7.0/fpm/php-fpm.conf \
    && sed -i "/error_log = .*/c\error_log = /proc/self/fd/2" /etc/php/7.0/fpm/php-fpm.conf \
    && usermod -u 1000 www-data

RUN git clone https://github.com/nikic/php-ast.git &&  cd php-ast && /usr/bin/phpize && ./configure && make && make install && echo 'extension=ast.so' > /etc/php/7.0/cli/php.ini

CMD ["/usr/sbin/php-fpm7.0"]

EXPOSE 9000
