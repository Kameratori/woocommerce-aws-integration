#!/bin/bash

set -e

echo "Launching WordPress..."
docker-compose up -d

echo "Resetting database..."
docker-compose exec wordpress wp db drop --yes
docker-compose exec wordpress wp db create

echo "Installing WordPress..."
docker-compose exec wordpress wp core install --url=http://localhost:8888 --title=Test --admin_user=admin --admin_password=password --admin_email=admin@toriverkosto.fi --skip-email

echo "Installing WooCommerce..."
docker-compose exec wordpress wp plugin install --activate woocommerce

echo "Installing Storefront..."
docker-compose exec wordpress wp theme install --activate storefront

echo "Install WooCommerce pages..."
docker-compose exec wordpress wp wc tool run install_pages --user=1

echo "Skipping WooCommerce onboarding..."
docker-compose exec wordpress wp option set woocommerce_setup_ab_wc_admin_onboarding '{ "a": "completed" }' --format=json

echo "Activating plugin..."
docker-compose exec wordpress wp plugin activate aws-sns-woocommerce

echo "Creating database dump..."
docker-compose exec wordpress wp db dump tests/_data/dump.sql 