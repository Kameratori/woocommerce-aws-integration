#!/bin/bash

set -e

echo "Launching Database..."
docker-compose up -d mysql

echo "Launching WordPress..."
docker-compose up -d wordpress

echo "Making sure database is available..."
while ! docker-compose exec -T mysql mysqladmin -u wordpress -ppassword ping --silent &> /dev/null; do
    sleep 5
done

echo "Resetting database..."
docker-compose exec -T wordpress wp db drop --yes
docker-compose exec -T wordpress wp db create

echo "Installing WordPress..."
docker-compose exec -T wordpress wp core install --url=http://localhost:8888 --title=Test --admin_user=admin --admin_password=password --admin_email=admin@toriverkosto.fi --skip-email

echo "Installing WooCommerce..."
docker-compose exec -T wordpress wp plugin install --activate woocommerce

echo "Installing Storefront..."
docker-compose exec -T wordpress wp theme install --activate storefront

echo "Install WooCommerce pages..."
docker-compose exec -T wordpress wp wc tool run install_pages --user=1

echo "Skipping WooCommerce onboarding..."
docker-compose exec -T wordpress wp option set woocommerce_setup_ab_wc_admin_onboarding '{ "a": "completed" }' --format=json
docker-compose exec -T wordpress wp transient delete-all

echo "Activating plugin..."
docker-compose exec -T wordpress wp plugin activate aws-sns-woocommerce

echo "Creating database dump..."
docker-compose exec -T wordpress wp db dump tests/_data/dump.sql 