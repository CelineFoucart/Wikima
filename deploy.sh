#!/bin/bash

# Deploy dev
# deploy dev environment application

# ---------------------------------- 
# install php and symfony dependencies
composer install --no-dev
php bin/console assets:install

# install node modules and build styles and js
npm install
npm run build

# Create the database
php bin/console doctrine:database:create --if-not-exists
php bin/console doctrine:migrations:migrate
