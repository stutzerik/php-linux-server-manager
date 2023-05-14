#!/bin/bash

echo -e "\e[0;36E NextCaligo Installer \e[0m";
read -p "Are you sure you want to install the software? The software will overwrite many files in your Linux system. (y/n) " yn

case $yn in 
	y ) 
    echo "Upgrade system...";
    apt-get -y update
    echo "Preparing to install...";
    sudo apt-get -y install libxml2-dev libxml2-utils gcc make autoconf libc-dev pkg-config
    echo "Installing Webserver & PhP...";
    apt-get -y install apache2 php8.1-cli php8.1-common php8.1-mysql php8.1-zip php8.1-gd php8.1-mbstring php8.1-curl php8.1-xml php8.1-bcmath install libapache2-mod-php8.1
    apt-get -y install php8.1-ssh2
    apt-get -y install php-libvirt-php
    echo "Installing Argon2 encryption...";
    apt-get -y install -y argon2
    apt-get -y install php-libsodium
    apt-get -y install libsodium
    echo "Configuring Apache2...";
    rm -rf /var/www/html
    mkdir /var/www/public
    cp -r config includes languages mail public src ssh_keys vendor composer.json composer.lock /var/www
    sudo rm -rf /etc/apache2/sites-available/000-default.conf 
    sudo cp 000-default.conf /etc/apache2/sites-available
    sudo rm -rf /etc/apache2/apache2.conf
    sudo cp apache2.conf /etc/apache2
    sudo a2enmod rewrite
    sudo systemctl restart apache2
    sudo systemctl enable apache2
    echo "Installing MySQL database server...";
    apt-get -y install mysql-server mysql-client phpmyadmin
    sudo systemctl start mysql
    sudo systemctl enable mysql
    mysql -uroot -p < createdb.sql
    echo "Installing KVM hypervisor...";
    apt-get -y install qemu-kvm libvirt-daemon-system virtinst libvirt-clients bridge-utils
    sudo systemctl enable libvirtd
    sudo systemctl start libvirtd
    sudo usermod -aG kvm www-data
    sudo usermod -aG kvm www-data
    echo "Override rights...";
    sudo rm -rf /etc/sudoers
    sudo cp sudoers /etc
    echo "Copying setup file (for first account creation)..."
    cp -r install /var/www/public
    echo "The installation is finished. Thank you for your trust!";
    echo "In the config folder, you can enter the SQL access data specified during installation in the dbconnect.php file.";
    echo "http://your-ip/install";
    exit;;
	n ) 
    echo Exiting the installer...;
	exit;;
	* ) echo Not an acceptable answer. Please use y or n keys!;
		exit 1;;
esac
