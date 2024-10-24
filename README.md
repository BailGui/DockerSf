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

