#!/bin/sh

php bin/console cache:clear --env=dev
php bin/console cache:clear --env=prod

php bin/console doctrine:cache:clear-metadata
php bin/console doctrine:cache:clear-query
php bin/console doctrine:cache:clear-result
rm -rf var/cache/*
