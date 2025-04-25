#!/bin/bash

set -e

# Environment variable fallback
DB_NAME="${DB_NAME:-wordpress}"
DB_USER="${DB_USER:-wpuser}"
DB_PASS="${DB_PASSWORD:-wppass}"
DB_HOST="${DB_HOST:-db}"
SITE_URL="${WORDPRESS_SITE_URL:-http://localhost:8080}"
SITE_TITLE="${WORDPRESS_SITE_TITLE:-WP Persistence Demo}"
ADMIN_USER="${WORDPRESS_ADMIN_USER:-admin}"
ADMIN_PASS="${WORDPRESS_ADMIN_PASSWORD:-admin}"
ADMIN_EMAIL="${WORDPRESS_ADMIN_EMAIL:-admin@example.com}"

echo "Waiting for database connection on ${DB_HOST}"
until mysqladmin ping -h"$DB_HOST" --silent; do
  echo -n "."
  sleep 2
done

cd /var/www/html

echo "Generating wp-config.php..."
/var/www/html/wp-cli.phar config create \
  --url="$SITE_URL" \
  --dbname="$DB_NAME" \
  --dbuser="$DB_USER" \
  --dbpass="$DB_PASS" \
  --dbhost="$DB_HOST" \
  --allow-root \
  --skip-check

echo "Installing WordPress (if not installed)..."
/var/www/html/wp-cli.phar core install \
  --url="$SITE_URL" \
  --title="$SITE_TITLE" \
  --admin_user="$ADMIN_USER" \
  --admin_password="$ADMIN_ASS" \
  --admin_email="$ADMIN_EMAIL" \
  --skip-email \
  --allow-root || true

echo "Activating persistence plugin"
/var/www/html/wp-cli.phar plugin activate wp-sph --allow-root || true
