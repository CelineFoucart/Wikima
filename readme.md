# Wikima-1.1.0

Wikima est un projet développé en symfony pour organiser des informations sur un univers fictif (de type romanesque, jeu de rôle, série). Il permet de gérer du contenu textuel sous forme de Wiki, des images, mais aussi une galerie, une chronologie et des fiches personnages. On peut créer des pages personnalisées et laisser des commentaires sur les articles.

## Prérequis
* PHP 8.0.2
* Composer

## Installation
* Coopier le fichier .env :
```bash
cp .env .env.local
```

* Configurer les informations du projet, comme DATABASE_URL pour la base de données et MAILER_DSN pour l'envoi de mail. Puis, deployer :
```bash
sh deploy.sh
```

* Création d'un compte administrateur :
```bash
php bin/console app:create-user
```

## Consommer les messages avec Messenger de Symfony
```bash
$ php bin/console messenger:consume async
```

Pour voir les détails :
```bash
$ php bin/console messenger:consume async -vv
```