# DockerSf

#### Installation de Docker dans une app Symfony déjà existante

## Première étape : Installer Docker

La première chose à faire est l'installation de Docker sur votre ordinateur.

Nous avons besoin de :

Docker
Docker compose
Docker apporte un moyen simplifié pour encapsuler nos logiciels dans des conteneurs qui apportent tout le nécessaire afin de les lancer : code, exécutables, outils systèmes et librairies.

Docker compose est un outil pour définir et exécuter des applications nécessitant plusieurs conteneurs. C'est utilisé principalement pour permettre à nos conteneurs de communiquer entre eux via un réseau privé et partager leur propre système de fichiers via des volumes.

## Installation

Si vous êtes sur un système basé sur Ubuntu, vous aurez juste besoin de faire :

```
apt-get install docker docker-compose 
```

Voir https://docs.docker.com/install/ & https://docs.docker.com/compose/install/ pour la documentation complète.

Et c'est … fini. Vous avez tout ce qu'il vous faut sur votre ordinateur.

Note: Il s'agit ici de l'installation pour docker compose v1. Pour utiliser docker compose v2, veuillez vous référer à : https://www.digitalocean.com/community/tutorials/how-to-install-and-use-docker-on-ubuntu-22-04

## Deuxième étape : Organiser la structure du projet

```
    - apps/
       - my-symfony-app/ [← Le dossier contenant votre application Symfony]
     - bin/
     - docker/
     - .env
     - docker-compose.yaml
```

Important : Cette structure de dossiers est propre à ce que nous faisons sur nos projets à KNP Labs. Ce n'est basé sur aucune convention et c'est à vous de voir si vous souhaitez la suivre ou non. Il faut juste savoir que les prochaines étapes se baseront sur cette architecture et que vous devrez adapter votre code pour suivre votre propre architecture.

## Troisième étape : Le fichier docker-compose

Vous allez avoir besoin d'un fichier listant toutes les images requises pour votre application. À la racine de votre project, créez un fichier "docker-compose.yaml".

Maintenant, nous allons énumérer tout ce que l'on a besoin dans notre application Symfony.

## Mettre en place la base de donnée

Tout d'abord, il nous faut un moteur de base de données. Prenons MySQL. Vous pouvez bien sûr utiliser Postgres ou MariaDB.

Essayons de créer notre fichier.

Vous pouvez copier le code ci-après :

```
 mysql:
    image: mysql:8.0
    environment:
      MYSQL_ROOT_PASSWORD: root
      MYSQL_DATABASE: symfony
      MYSQL_USER: user
      MYSQL_PASSWORD: password
    ports:
      - "3306:3306"
    volumes:
      - mysql-data:/var/lib/mysql
    networks:
      - symfony-network

```

Qu'avons nous fait ?

La première ligne spécifie la version de la syntaxe docker compose. Ensuite nous ajoutons la liste des services nécessaire à notre application. C'est une liste d'images Docker. Vous pouvez voir toutes les images disponibles sur Docker Hub : https://hub.docker.com/.
Nous avons juste ajouté la dernière version de MySQL. Nous avons aussi mis en place les variables d'environnement nécessaires comme le mot de passe de l'utilisateur ainsi que le nom de la base de données. Toutes les options sont disponibles ici : https://hub.docker.com/_/mysql

Pour ceux qui veulent avoir une interface web, vous pouvez facilement ajouter Adminer. Ajoutez le code suivant :

```
version: '3.8'
services:
    mysql:
        …
    
    adminer:
        image: adminer
        restart: on-failure
        ports:
            - '8080:8080'

```

La partie "Ports" est utile pour gérer la communication entre vos conteneurs et votre ordinateur. Nous relions le port 8080 de notre conteneur vers celui de votre machine. Ainsi vous pourrez accéder à Adminer en accédant au lien suivant : http://localhost:8080/

## Configurer le serveur HTTP

Maintenant que l'on a un moteur de base de données, il nous faut un serveur HTTP comme Apache ou Nginx. Pour cet exemple, nous utiliserons Nginx.

Dans votre fichier docker-compose, vous pouvez ajouter :

Attention de bien respecter les espacements, yaml est très strict.
```
services:
  php:
    build:
      context: .
      dockerfile: Dockerfile
    volumes:
      - ./:/var/www/html

    networks:
      - symfony-network

  nginx:
    image: nginx:latest
    volumes:
      - ./:/var/www/html
      - ./nginx/default.conf:/etc/nginx/conf.d/default.conf
    ports:
      - "8080:80"
    networks:
      - symfony-network
    depends_on:
      - php

  mysql:
    image: mysql:8.0
    environment:
      MYSQL_ROOT_PASSWORD: root
      MYSQL_DATABASE: symfony
      MYSQL_USER: user
      MYSQL_PASSWORD: password
    ports:
      - "3306:3306"
    volumes:
      - mysql-data:/var/lib/mysql
    networks:
      - symfony-network

```

## Configurer phpmyadmin 

Dans votre fichier docker-compose, vous pouvez ajouter :

```
phpmyadmin:
    image: phpmyadmin/phpmyadmin
    environment:
      PMA_HOST: mysql
      MYSQL_USER: user
      MYSQL_PASSWORD: password
    ports:
      - "8081:80"
    networks:
      - symfony-network

volumes:
  mysql-data:

networks:
  symfony-network:

```

## Création du fichier Dockerfile

Le Dockerfile est utilisé pour définir des instructions sur nos images. Vous pouvez par exemple installer des extensions PHP ou exécuter des commandes Unix.

Dans cet exemple, nous allons créer un sous-dossier "php" dans le dossier "docker". Dans ce sous-dossier, nous créerons un fichier "Dockerfile".

```
FROM php:8.2-fpm

# Installer les extensions PDO et MySQL
RUN docker-php-ext-install pdo pdo_mysql

# Installer Composer
COPY --from=composer:latest /usr/bin/composer /usr/bin/composer

# Installer APCu
RUN pecl install apcu && docker-php-ext-enable apcu

# Installer Xdebug
RUN pecl install xdebug && docker-php-ext-enable xdebug

# Configurer Xdebug
RUN echo "xdebug.mode=debug" >> /usr/local/etc/php/conf.d/docker-php-ext-xdebug.ini \
    && echo "xdebug.start_with_request=yes" >> /usr/local/etc/php/conf.d/docker-php-ext-xdebug.ini \
    && echo "xdebug.client_host=host.docker.internal" >> /usr/local/etc/php/conf.d/docker-php-ext-xdebug.ini \
    && echo "xdebug.client_port=9003" >> /usr/local/etc/php/conf.d/docker-php-ext-xdebug.ini \
    && echo "xdebug.log=/tmp/xdebug.log" >> /usr/local/etc/php/conf.d/docker-php-ext-xdebug.ini

# Activer et configurer OPcache
RUN docker-php-ext-install opcache \
    && echo "opcache.enable=1" >> /usr/local/etc/php/conf.d/opcache.ini \
    && echo "opcache.memory_consumption=128" >> /usr/local/etc/php/conf.d/opcache.ini \
    && echo "opcache.interned_strings_buffer=8" >> /usr/local/etc/php/conf.d/opcache.ini \
    && echo "opcache.max_accelerated_files=10000" >> /usr/local/etc/php/conf.d/opcache.ini \
    && echo "opcache.revalidate_freq=0" >> /usr/local/etc/php/conf.d/opcache.ini \
    && echo "opcache.validate_timestamps=1" >> /usr/local/etc/php/conf.d/opcache.ini

```

## Lancement de Docker

```
docker-compose down
docker-compose build
docker-compose up -d

```






src : https://knplabs.com/fr/blog/comment-dockeriser-un-projet-symfony/
src : https://github.com/mikhawa/Sym2024dock/blob/main/README.md