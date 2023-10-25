#!/bin/sh

php bin/console doctrine:schema:drop --force
php bin/console doctrine:schema:update -f
php bin/console doctrine:fixtures:load -n --group=prod
