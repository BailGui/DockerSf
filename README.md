# DockerSf

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




src : https://knplabs.com/fr/blog/comment-dockeriser-un-projet-symfony/