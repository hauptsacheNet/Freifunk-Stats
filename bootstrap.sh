#!/usr/bin/env bash

apt-get update

debconf-set-selections <<< 'mysql-server-5.5 mysql-server/root_password password rootpass'
debconf-set-selections <<< 'mysql-server-5.5 mysql-server/root_password_again password rootpass'

apt-get install -y mysql-server mysql-client

apt-get install -y apache2 apache2-mpm-worker libapache2-mod-fastcgi

apt-get install -y php5 php5-cli php5-fpm php5-mysql php5-dev php5-intl php5-xsl php5-xdebug php-apc php-pear

pear config-set auto_discover 1
pear install -f pear.phpqatools.org/phpqatools
pear install -f pear.netpirates.net/phpDox-0.5.0



apt-get install -y openjdk-7-jdk

apt-get install -y ant

apt-get install -y curl



# ensure that necessary modules are enabled
a2enmod actions fastcgi alias

#copy php-fpm config to apache's conf.d directory
cp /vagrant/php5-fpm.conf /etc/apache2/conf.d/php5-fpm.conf

rm -rf /var/www
ln -fs /vagrant /var/www

service apache2 restart



