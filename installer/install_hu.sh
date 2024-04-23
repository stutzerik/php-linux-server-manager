#!/bin/bash

echo -e "\e[0;36E NextCaligo telepítő \e[0m";
read -p "Biztosan telepíti a szoftvert? A szoftver számos fájlt felül fog írni a Linux rendszerben. (y/n) " yn

case $yn in 
	y ) 
    echo "Rendszer frissítése...";
    apt-get -y update
    echo "Telepítés előkészítése...";
    sudo apt-get -y install libxml2-dev libxml2-utils gcc make autoconf libc-dev pkg-config
    echo "Webszerver & PhP telepítése...";
    apt-get -y install apache2 php8.1-cli php8.1-common php8.1-mysql php8.1-zip php8.1-gd php8.1-mbstring php8.1-curl php8.1-xml php8.1-bcmath install libapache2-mod-php8.1
    apt-get -y install php8.1-ssh2
    apt-get -y install php-libvirt-php
    echo "Argon2 titkosítás telepítése...";
    apt-get -y install -y argon2
    apt-get -y install php-libsodium
    apt-get -y install libsodium
    echo "Apache2 konfigurálása...";
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
    echo "MySQL adatbázis szerver telepítése...";
    apt-get -y install mysql-server mysql-client phpmyadmin
    sudo systemctl start mysql
    sudo systemctl enable mysql
    mysql -u root -p < createdb.sql
    echo "KVM kernel telepítése...";
    apt-get -y install qemu-kvm libvirt-daemon-system virtinst libvirt-clients bridge-utils
    sudo systemctl enable libvirtd
    sudo systemctl start libvirtd
    sudo usermod -aG kvm www-data
    sudo usermod -aG kvm www-data
    echo "Jogok felülírása...";
    sudo rm -rf /etc/sudoers
    sudo cp sudoers /etc
    echo "Telepítő fájl (az első fiók létrehozásához) másolása..."
    cp -r install /var/www/public
    echo "A telepítés véget ért. Köszönjük a bizalmat!";
    echo "A config mappában tudja a telepítés során megadott SQL hozzáférési adatokat megadni a dbconnect.php fájlban.";
    echo "A szerver IPv4 címén a böngészősávban navigáljon a /install könyvtárba, ahol létre is
    hozhatja felhasználói fiókját. Amennyiben ez megtörtént, törölje az install mappát.";
    exit;;
	n ) 
    echo Kilépés a telepítőből...;
	exit;;
	* ) echo Nem elfogadható válasz. Kérem, y vagy n billentyűket használjon!;
		exit 1;;
esac
