#!/bin/sh
composer install --no-interaction --prefer-source
composer dump-autoload -o
