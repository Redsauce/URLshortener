FROM php:7.1-fpm-alpine
MAINTAINER Kenan Rhoton
RUN echo http://nl.alpinelinux.org/alpine/edge/community >> /etc/apk/repositories
RUN echo http://nl.alpinelinux.org/alpine/edge/main >> /etc/apk/repositories
RUN echo http://nl.alpinelinux.org/alpine/edge/testing >> /etc/apk/repositories
RUN curl -sS https://getcomposer.org/installer | php -- --install-dir=/usr/bin --filename=composer 
RUN apk add --no-cache $PHPIZE_DEPS openssl-dev
RUN pecl install mongodb
RUN docker-php-ext-enable mongodb
# Copy our web files
COPY web/ /var/www/html/
