rdbcache Tests
--------------

rdbcache test suite is built on top of PHP Yii2 framework. It requires php 5.4.0+ and Yii2 framework.

For linux, here are steps:

yum -y install php71-fpm

yum -y install php71-intl php71-gd php71-imap php71-mbstring php71-opcache php71-pdo php71-pecl-apcu php71-mysqlnd php71-pgsql php71-pecl-imagick php71-pecl-memcache php71-pecl-redis

curl -sS https://getcomposer.org/installer | php

mv composer.phar /usr/local/bin/composer

git clone https://github.com/rdbcache/rdbcache-test.git

cd rdbcache-test

composer update

Setup Environment Variables:
----------------------------

Please replace the values with the proper ones for your environment.

export RDBCACHE_SERVER=localhost

export RDBCACHE_PORT=8181

export REDIS_SERVER=localhost

export DATABASE_NAME=testdb

export DATABASE_SERVER=localhost

export DB_USER_NAME=dbuser

export DB_USER_PASS=rdbcache

Run Tests
---------

To run the whole test suite:

./rdbcache_test

A previous test result has been saved in file rdbcache-test-result.txt.

You also can run individual as followings:

./yii test-get

./yii test-get2

./yii test-set

./yii test-set2

./yii test-put

./yii test-getset

./yii test-minus-expire

./yii test-pull

./yii test-push

./yii test-delkey

./yii test-delall

./yii test-employees

./yii test-select

./yii test-select-pull

./yii test-save

./yii test-save-pull

