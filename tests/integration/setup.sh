#!/bin/bash

base=/var/www

cd $base

rm -rf site-a
rm -rf site-b

curl -sS https://getcomposer.org/installer | php

composer="${base}/composer.phar"

php $composer create-project --no-scripts symfony/framework-standard-edition site-a
php $composer create-project --no-scripts symfony/framework-standard-edition site-b

cd "${base}/site-a" && $composer dump-autoload --optimize
cd "${base}/site-b" && $composer dump-autoload --optimize

cd $base
