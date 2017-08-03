FROM composer
RUN apk update
RUN docker-php-ext-install pdo_mysql