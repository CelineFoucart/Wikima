# Wikima

Wikima est un projet développé en symfony pour organiser des informations sur un univers fictif (de type romanesque, jeu de rôle, série). Il permet de gérer du contenu textuel sous forme de Wiki, des images, mais aussi une galerie, une chronologie et des fiches personnages. On peut créer des pages personnalisées et laisser des commentaires sur les articles.

Ce repository est une version sans docker de l'application.

## Prérequis

* PHP 8.0.2 minimum
* Composer
* Node et npm
* MariaDB ou MySQL en base de données

## Installation

Cloner le projet avec git clone et installer les dépendances, puis créer un fichier .env.local :

```bash
composer install
npm install
npm run build
touch .env.local
```

Se rendre sur la page d'installation à l'url suivante : **/installation**. Suivre le processus d'installation pour configurer
la base de données, le compte du super administrateur, les informations du site et le système d'envoi de mail.

## Licence

Distribué sous la licence MIT. Voir `LICENSE` pour plus d'information.
