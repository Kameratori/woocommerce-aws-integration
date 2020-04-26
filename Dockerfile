FROM php:7.4-apache

# create web root dir
RUN mkdir -p /var/www/html
WORKDIR /var/www/html

# install php-mysqli
RUN docker-php-ext-install mysqli && docker-php-ext-enable mysqli

# install wp-cli
RUN \
  curl -O https://raw.githubusercontent.com/wp-cli/builds/gh-pages/phar/wp-cli.phar \
  && chmod +x wp-cli.phar && mv wp-cli.phar /usr/local/bin/wp

# install wordpress
RUN wp core download --allow-root

# create wordpress config
RUN wp config create --dbname=wordpress --dbuser=wordpress --dbpass=password --dbhost=mysql --skip-check --allow-root
