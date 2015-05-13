#!/bin/sh

./vendor/bin/phpcs -v ./src ./test --standard=PSR2
./vendor/bin/phpunit test