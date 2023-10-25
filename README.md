# ESM v3.0
> Easy Study Managment product of Clinfile

## Table of contents
* [General info](#general-info)
* [Server system](#server_system)
* [Backend Technologies](#backend_technologies)
* [Frontend Technologies](#frontend_technologies)
* [Installation](#installation)
* [Setup](#setup)
* [Features](#features)
* [Status](#status)
* [Contact](#contact)

## General info
ESM Clinfile v3.0 - product by Clinfile

## Server system

Services inside Docker 

* PHP 7.3
* Apache - version 2.4
* Mariadb - version 10.4.11
* Redis (image redis:alpine)

## Backend - Technologies

* Symfony - version 4.4

## Frontend - Technologies

* Boostrap - version 4.5
* Webpack encore - version 1.7
* Sass (node-sass)

## Installation environnement virtualiser DOCKER


* Download and install docker [desktop for win](https://www.docker.com/products/docker-desktop)
* Execute docker desktop.


## Installation environnement virtualiser VAGRANT

* Install VirtualBox [VirtualBox for win](https://download.virtualbox.org/virtualbox/6.1.18/VirtualBox-6.1.18-142142-Win.exe)
* Install Vagrant  [Vagrant for win](https://releases.hashicorp.com/vagrant/2.2.15/vagrant_2.2.15_x86_64.msi)
* Install Packer [Packer for win](https://releases.hashicorp.com/packer/1.7.2/packer_1.7.2_windows_amd64.zip)

- ensure those install binaries run correctly into project path host 
- ensure you use a unix terminal and not Windows terminal (e.g : Git for windows, Cmder, ...)

into /vagrant project directory run :

1 - ```$packer build packer-template.json``` for create vagrant box

2 - ``$vagrant up`` for launching Vagrant box environment

3 - ``$vagrant halt`` for stop Vagrant box environment



## Setup

inside folder - e.g: "C:\sites\www 

* run **Git bash**
* Create folder `template`
* Go to `template` folder
* Clone project
* Switch branch ongoing

with this commands:

```shell
mkdir template
cd template
git clone https://github.com/Clinfile/esm-template-v3.git
cd esm-template-v3
git checkout ongoing
```

### Create necessary folder before build

* `var/cache`
* `var/log`

```
mkdir -p var/{log,cache}
```

### Docker - build and run

```
docker-compose build
docker-compose up -d
```

## Before run project

Edit your hosts file - e.g:  "C:\Windows\System32\drivers\etc\hosts" and add this line inside.

```
127.0.0.1 esm-template-v3.localhost
127.0.0.1 adminer.localhost
```

## Create .env.local file

copy `.env` file to `.env.local`
Edit `.env.local` file

### Change inside `.env.local` file

```
DATABASE_URL=mysql://user:password@127.0.0.1:3306/dbname
```

To

```
DATABASE_NAME=esm_v3_develop
```

### Add other params at the end of `.env.local` file

```
APP_ENV=dev
APP_SECRET=secretsecret
```

## SSH open php__73

```
docker-compose exec php_73 bash
```

## Symfony Install vendor

```
cd /var/www/template/esm-template-v3
composer install
```

# npm install

```
cd /var/www/template/esm-template-v3
npm install
npm run dev
```

## Create database and load fixtures data

```
composer prepare
```

## Run project

Open browser and run

http://esm-template-v3.localhost:8081/

You are ready now to use ESM v3.0 :)

### Browse database

Go to 

http://adminer.localhost:8081/

with these IDs

```
host: mariadb_2
username: root
password: secret
```

### Read emails

with mailhog - Go to 

http://localhost:9510/#/

## Features

* TODO

## Status
Project is: _in progress_

## Contact
Created by [@devclinfile(https://clinfile.com/) 
