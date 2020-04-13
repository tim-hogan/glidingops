#!/bin/sh

# If you would like to do some extra provisioning you may
# add any commands you wish to this file and they will
# be run after the Homestead machine is provisioned.
#
# If you have user-specific configurations you would like
# to apply, you may also create user-customizations.sh,
# which will be run after this script.

sudo update-alternatives --set php /usr/bin/php7.4
sudo apt-get -y install php7.4-fpm php7.4-mysql php7.4-mbstring php7.4-xml php7.4-gd php7.4-curl
cd code/lrv
composer install
php artisan migrate
php artisan db:seed