#!/usr/bin/env bash

# -e          Exit immediately if a pipeline returns a non-zero status
# -o pipefail Produce a failure return code if any command errors
set -eo pipefail

# Check whether the `docker` command exists:
if ! [ -x "$(command -v docker)" ]; then
	echo 'Error: Docker is not installed' >&2
	exit 1
fi

# Start the containers if necessary:
if docker container inspect wp-crontrol-database > /dev/null 2>&1; then
	DOCKER_ALREADY_RUNNING=true
else
	DOCKER_ALREADY_RUNNING=false
	docker compose up -d
fi

# Prep:
WP_PORT=`docker port wp-crontrol-server | grep "[0-9]+$" -ohE | head -1`
CHROME_PORT=`docker port wp-crontrol-chrome | grep "[0-9]+$" -ohE | head -1`
DATABASE_PORT=`docker port wp-crontrol-database | grep "[0-9]+$" -ohE | head -1`
WP_URL="http://host.docker.internal:${WP_PORT}"
WP="docker compose run --rm wpcli wp --url=${WP_URL}"

# Reset or install the test database:
echo "Installing database..."
$WP db reset --yes

# Install WordPress:
echo "Installing WordPress..."
$WP core install \
	--title="Example" \
	--admin_user="admin" \
	--admin_password="admin" \
	--admin_email="admin@example.com" \
	--skip-email \
	--require="wp-content/plugins/wp-crontrol/bin/mysqli_report.php"
echo "Home URL: $WP_URL"
$WP plugin activate wp-crontrol

# Run the acceptance tests:
echo "Running tests..."
TEST_SITE_WEBDRIVER_PORT=$CHROME_PORT \
	TEST_SITE_DATABASE_PORT=$DATABASE_PORT \
	TEST_SITE_WP_URL=$WP_URL \
	./vendor/bin/codecept run acceptance --steps "$1"

# Stop the containers if they weren't already running:
if [ "$DOCKER_ALREADY_RUNNING" = false ]; then
	docker compose down
fi
