#!/usr/bin/env bash

# Set non-interactive mode
export DEBIAN_FRONTEND=noninteractive

# Update the box #######################################################################################################

sudo apt-get -y update
sudo apt-get -y install linux-headers-$(uname -r) build-essential
sudo apt-get -y install zlib1g-dev libssl-dev libreadline-gplv2-dev
sudo apt-get -y install curl zip unzip
sudo apt-get -y install software-properties-common
sudo apt-get -y install gnupg2
sudo apt-get -y install whois

# others tools #########################################################################################################

sudo apt-get -y install nano
sudo apt-get -y install vim
sudo apt-get -y install aptitude
sudo apt-get -y install git
sudo apt-get -y install dos2unix
sudo apt-get -y install net-tools
sudo apt-get -y install nmap

# java #################################################################################################################

sudo add-apt-repository ppa:openjdk-r/ppa
sudo apt-get update
sudo apt-get install openjdk-8-jdk

# PHP 7.3 ##############################################################################################################

sudo add-apt-repository ppa:ondrej/php
sudo apt-get update

# deb http://ppa.launchpad.net/ondrej/php/ubuntu bionic main
# deb-src http://ppa.launchpad.net/ondrej/php/ubuntu bionic main

sudo apt-get -y install php7.3
sudo apt-get -y install php7.3-cli
sudo apt-get -y install php7.3-fpm
sudo apt-get -y install php7.3-json
sudo apt-get -y install php7.3-pdo
sudo apt-get -y install php7.3-mysql
sudo apt-get -y install php7.3-zip
sudo apt-get -y install php7.3-gd
sudo apt-get -y install php7.3-mbstring
sudo apt-get -y install php7.3-curl
sudo apt-get -y install php7.3-dev
sudo apt-get -y install php7.3-xdebug
sudo apt-get -y install php7.3-xml
sudo apt-get -y install php7.3-bcmath
sudo apt-get -y install php7.3-jsonva

sudo apt-get -y install php-pear
sudo apt-get -y install phpunit

# Database MariaDB #####################################################################################################

# echo mysql-server mysql-server/root_password password strangehat | sudo debconf-set-selections
# echo mysql-server mysql-server/root_password_again password strangehat | sudo debconf-set-selections
sudo apt-get -y install mariadb-server-10.1

sudo mysql_secure_installation 2>/dev/null <<MSI

n
y
strangehat
strangehat
y
n
y
y

MSI

sudo mysql -e "CREATE DATABASE esm_v3_develop /*\!40100 DEFAULT CHARACTER SET utf8 */;"
sudo mysql -e "CREATE USER vagrant@localhost IDENTIFIED BY 'strangehat';"
sudo mysql -e "GRANT ALL PRIVILEGES ON *.* TO 'root'@'localhost' IDENTIFIED BY 'strangehat';"
sudo mysql -e "FLUSH PRIVILEGES;"
sudo mysql -e "GRANT ALL PRIVILEGES ON *.* TO 'vagrant'@'localhost' IDENTIFIED BY 'strangehat';"
sudo mysql -e "FLUSH PRIVILEGES;"

# Apache ###############################################################################################################
sudo apt-get -y install apache2

sudo apt-get -y install libapache2-mod-fastcgi
sudo apt-get -y install libapache2-mod-php7.3

sed -i 's/listen = \/run\/php\/php7.3-fpm.sock/listen=127.0.0.1:9000/' /etc/php/7.3/fpm/pool.d/www.conf

sudo a2enconf php7.3-fpm

# activation module apache
sudo a2enmod php7.3
sudo a2enmod proxy_fcgi
sudo a2enmod ssl
sudo a2enmod rewrite
sudo a2enmod proxy
#sudo a2enmod proxy_balancer
#sudo a2enmod proxy_http
#sudo a2enmod proxy_ajp

sudo update-alternatives --set php /usr/bin/php7.3
sudo update-alternatives --set phar /usr/bin/phar7.3

# mise en place du serverName apache dans le hosts du system
sudo sed -i 's/\blocalhost\b/& 86.86.86.100/' /etc/hosts

# build esm-v3 project
sudo mkdir /var/www/esm-v3
sudo mkdir /var/www/esm-v3/public
sudo mkdir /var/www/esm-v3/public/files
sudo mkdir /var/www/esm-v3/public/files/documentsTransverse
sudo mkdir /var/www/esm-v3/public/build

# Debugger
sudo mv -f /tmp/xdebug.ini /etc/php/7.3/mods-available/
sudo touch /var/www/esm-v3/var/log/xdebug.log

# OpenSSL
#cd /var/www/esm-v3 || return
#sudo openssl genrsa -out esm-v3.key 2048
#sudo openssl req -new -x509 -key esm-v3.key -out esm-v3.cert -days 3650 -subj /CN=esm-v3
#sudo sed -i '/^        DocumentRoot.*/a\ \ \ \ \ \ \ \ \SSLEngine on' /etc/apache2/sites-available/esm-v3.conf
sudo mv /tmp/esm-v3.cert /var/www/esm-v3
sudo mv /tmp/esm-v3.key /var/www/esm-v3
sudo mv /tmp/.env.local /var/www/esm-v3

# good rights
sudo chgrp -R www-data /var/www/esm-v3
sudo chown -R www-data /var/www/esm-v3
sudo chmod -R 777 /var/www/esm-v3

sudo mv /tmp/esm-v3.conf /etc/apache2/sites-available
sudo a2ensite esm-v3

# build adminer project
sudo mkdir /var/www/adminer
sudo mv /tmp/adminer.php /var/www/adminer

sudo chgrp -R vagrant /var/www/adminer
sudo chown -R vagrant /var/www/adminer

sudo mv /tmp/adminer.conf /etc/apache2/sites-available
sudo a2ensite adminer

# desactivation vhost de base et activation des autres
sudo a2dissite default

# activation de l'url rewriting
a2enmod rewrite

# add ServerName config in apache2.conf
sudo sed -i '/ServerRoot "\/etc\/apache2"/a \\nServerName 86.86.86.100' /etc/apache2/apache2.conf

# restart apache
sudo service apache2 restart

# memcached
sudo apt-get -y install memcached
sudo apt-get -y install php-memcached
sudo apt-get -y install libmemcached-tools



# Composer #############################################################################################################

curl -sS https://getcomposer.org/installer | php
sudo mv composer.phar /usr/local/bin/composer

# Symfony ##############################################################################################################

sudo wget https://get.symfony.com/cli/installer -O - | bash
sudo mv /root/.symfony/bin/symfony /usr/local/bin/symfony

# REDIS ################################################################################################################

sudo apt-get -y install redis-server
sed -i 's/supervised\sno/supervised systemd/' /etc/redis/redis.conf

# NodeJS ###############################################################################################################
#wget -qO- https://deb.nodesource.com/setup_10.x | sudo bash -
#sudo apt-get install --yes nodejs

curl -o- https://raw.githubusercontent.com/nvm-sh/nvm/v0.38.0/install.sh | bash

export NVM_DIR="/home/vagrant/.nvm"
[ -s "$NVM_DIR/nvm.sh" ] && \. "$NVM_DIR/nvm.sh"
[ -s "$NVM_DIR/bash_completion" ] && \. "$NVM_DIR/bash_completion"

nvm install --lts

sudo chmod 0777 /var/www/esm-v3
sudo chmod 0777 /var/www/adminer

npm install -g yarn
npm install -g less
npm install -g maildev

# PERL #################################################################################################################

sudo apt-get -y install perl

# Python ###############################################################################################################

# Install latest Setuptools system-wide.
curl https://bootstrap.pypa.io/ez_setup.py -o - | python

# Install Pip.
easy_install pip

sudo apt-get -y install python-software-properties

# Install Virtualenv + Virtualenvwrapper.
pip install virtualenv virtualenvwrapper

# Configure Virtualenvwrapper.
cat <<EOF >> /home/vagrant/.bashrc
# Virtualenvwrapper configuration.
export WORKON_HOME=\$HOME/.virtualenvs
export PROJECT_HOME=\$HOME/Devel
source /usr/local/bin/virtualenvwrapper.sh
EOF

# RUBY #################################################################################################################

sudo su -

cd
wget http://ftp.ruby-lang.org/pub/ruby/2.3/ruby-2.3.0.tar.gz
tar -xzvf ruby-2.3.0.tar.gz
cd ruby-2.3.0/
./configure
make
sudo make install
ruby -v

gem install bundler
gem install compass

# SYSTEM ###############################################################################################################

# ajout d'un utilisateur autre que 'vagrant' (for KDE login)
sudo useradd --home /home/toto --create-home --password `mkpasswd pippo` toto
sudo adduser toto sudo

# Set up sudo
echo 'vagrant ALL=NOPASSWD:ALL' > /etc/sudoers.d/vagrant

# Tweak sshd to prevent DNS resolution (speed up logins)
echo 'UseDNS no' >> /etc/ssh/sshd_config

# authorize SSH connection with root account
sed -i 's/#PermitRootLogin prohibit-password/PermitRootLogin yes/' /etc/ssh/sshd_config
sudo service ssh restart

# change password root
echo "root:vagrant"|chpasswd

# add vagrant user to www-data group
sudo usermod -a -G www-data vagrant

# Customize Bash settings.
cat <<EOF > /home/vagrant/.bashrc
# Colorize the prompt.
yellow=\$(tput setaf 3)
green=\$(tput setaf 2)
blue=\$(tput setaf 104)
bold=\$(tput bold)
reset=\$(tput sgr0)

PS1="\[\$yellow\]\u\[\$reset\]@\[\$green\]\h\[\$reset\]:\[\$blue\$bold\]\w\[\$reset\]\$ "

# Don't put duplicate lines or lines starting with space in the history.
# See bash(1) for more options.
export HISTCONTROL=ignoreboth

# Append to the history file, don't overwrite it.
shopt -s histappend

# History size up to 1000 commands.
export HISTSIZE=1000

# Make less more friendly for non-text input files, see lesspipe(1).
[ -x /usr/bin/lesspipe ] && eval "\$(SHELL=/bin/sh lesspipe)"

# Enable color support of ls and also add handy aliases.
export CLICOLOR=1
export LSCOLORS=ExFxCxDxBxegedabagacad
if [ -x /usr/bin/dircolors ]; then
    test -r ~/.dircolors && eval "\$(dircolors -b ~/.dircolors)" || eval "\$(dircolors -b)"
    alias ls='ls --color=auto'
    alias grep='grep --color=auto'
    alias fgrep='fgrep --color=auto'
    alias egrep='egrep --color=auto'
fi

# Enable programmable completion features (you don't need to enable
# this, if it's already enabled in /etc/bash.bashrc and /etc/profile
# sources /etc/bash.bashrc).
if [ -f /etc/bash_completion ] && ! shopt -oq posix; then
    . /etc/bash_completion
fi

# Set your favorite editor here.
VISUAL=vim; export VISUAL
EDITOR=vim; export EDITOR

# Append /usr/local/bin to the path.
export PATH=/usr/local/bin:/usr/local/share/python/:\$PATH
EOF
