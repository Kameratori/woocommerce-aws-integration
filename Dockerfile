FROM php:7.4-apache

# create web root dir
RUN mkdir -p /var/www/html
WORKDIR /var/www/html

# install libraries
RUN apt-get update && apt-get install -y \
	mariadb-client \
	zip \
	libzip-dev \
	zlib1g-dev \
	libpng-dev \
	libsodium-dev

# install php extensions
RUN docker-php-ext-install \
	zip \
	pdo \
	pdo_mysql \
	mysqli \
	gd \
	sodium 

# install wp-cli
RUN \
	curl -O https://raw.githubusercontent.com/wp-cli/builds/gh-pages/phar/wp-cli.phar \
	&& mv wp-cli.phar /usr/local/bin/wp-cli.phar \
	&& echo '#!/bin/bash' >> /usr/local/bin/wp \
	&& echo 'wp-cli.phar "$@" --allow-root' >> /usr/local/bin/wp \
	&& chmod +x /usr/local/bin/wp-cli.phar && chmod +x /usr/local/bin/wp

# install composer
COPY --from=composer:latest /usr/bin/composer /usr/bin/composer

# set up empty composer project
RUN composer init -n

# add composer bin to path
ENV PATH="${PATH}:/var/www/html/vendor/bin"

# install codeception for wordpress testing
RUN composer require --dev \
	lucatume/wp-browser \
	codeception/module-asserts \
	codeception/module-filesystem \
	codeception/module-db \
	codeception/module-phpbrowser \
	codeception/module-webdriver \
	codeception/module-cli \
	codeception/util-universalframework

# install latest wordpress
RUN wp core download

# create wordpress config
RUN wp config create --dbname=test --dbuser=wordpress --dbpass=password --dbhost=mysql --skip-check

ENV PORT=8888
EXPOSE 8888
ENTRYPOINT []
CMD sed -i "s/80/$PORT/g" /etc/apache2/sites-available/000-default.conf /etc/apache2/ports.conf && docker-php-entrypoint apache2-foreground
