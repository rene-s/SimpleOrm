#!/bin/sh
file=/tmp/psr-2-rsd_ruleset_`date +"%H"`.xml
url=https://raw.githubusercontent.com/rene-s/psr-2-rsd/master/psr-2-rsd_ruleset.xml

if [ ! -f $file ]; then
    wget $url -O $file
fi

./vendor/bin/phpcs -v ./src ./test --standard=$file
./vendor/bin/phpunit test