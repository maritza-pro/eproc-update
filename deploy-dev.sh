#!/bin/sh
redis-cli flushall
# activate maintenance mode
php8.4 artisan down

# update source code
git checkout development
git pull

# update php8.4 dependencies
php8.4 /usr/bin/composer install --no-interaction --no-dev --prefer-dist --apcu-autoloader --optimize-autoloader --classmap-authoritative

# run migration
php8.4 artisan migrate --force

# build assets
yarn install
yarn build

# clear cache
php8.4 artisan modelCache:clear
php8.4 artisan optimize:clear

php8.4 artisan optimize
php8.4 artisan config:cache
php8.4 artisan route:cache
php8.4 artisan view:cache
php8.4 artisan event:cache

php8.4 artisan storage:link
php8.4 artisan queue:restart

# Reload Octane
# php8.4 artisan octane:reload

# disable maintenance mode
php8.4 artisan up

php8.4 artisan opcache:compile --force
# run bloom filter
# php8.4 artisan auth:bloom-filter --debug
