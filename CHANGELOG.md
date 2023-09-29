# Changelog

All notable changes to this project will be documented in this file.

The format is based on [Keep a Changelog](https://keepachangelog.com/en/1.0.0/),
and this project adheres to [Semantic Versioning](https://semver.org/spec/v2.0.0.html).

## [1.4.0] - 2023-09-29

### Fixed

- Ajout du lien manquant vers la licence MIT dans la modal

### Added

- Ajout du module forum : catégorie de forum, forum, topic, post, groupe d'utilisateur et espace administration
- Ajout de la possibilité d'activer ou de désactiver le module
- Ajout de l'avatar, de la localisation et du rang dans le profil utilisateur

### Changed

- Affichage de l'avatar à la place de l'icone utilisateur dans les commentaires, si l'utilisateur a un avatar.

## [1.3.1] - 2023-08-25

### Fixed

- Validation W3C

### Added

- Ajout de la page recherche avancée
- Ajout de la recherche par tag
- Ajout de la possibilité d'ordonner les articles de langue
- Statistiques sur le tableau de bord

## [1.3.0] - 2023-08-19

### Fixed

- Correction de l'erreur 500 à l'ajout d'un lieu depuis un autre lieu
- Correction de problèmes d'affichage en mobile

### Added

- Ajout du titre de l'image sous forme de tooltip dans l'administration dans les pages de gestion de la galerie d'un élément
- Ajout des informations sur la version courante du logiciel et sur la taille de la base de données
- Ajout du style du bloc figure sur l'impression
- Ajout de l'impression d'un lieu et d'un personnage
- Ajout de la date de mise à jour sur l'affichage public des articles
- Ajout des tags d'images
- Ajout sur la page d'une catégorie des personages et lieux épinglés
- Ajout sur la page d'un portail des articles, des personages et lieux épinglés

### Changed

- Renommer "EXPLORER" en "CATEGORIE" dans le menu
- Trier les articles de l'impression catégorie par ordre alphabétique

## [1.2.0] - 2023-08-01

### Fixed

- Impossibilité de supprimer une image liée à un personnage ou un lieu

### Added

- Ajout du module langue : langue, article de langue, catégorie d'articles linguistiques
- Ajout d'une impression globale pour la langue et ses articles
- Ajout de la page module avec possibilité de désactiver un module (commentaire d'article et langue)
- Ajout du menu d'exploration dans la navbar
- Ajout de la lightbox pour les images des lieux et des personnages
- Ajout des articles liés à l'image depuis la page de détail d'une image
- Ajout d'un bouton impression javascript sur la page d'impression
- Ajout de bordure aux tableaux dans les impressions
- Ajout des icônes dans le dropdown encyclopédie du menu

### Changed

- Afficher la galerie sous les articles
- Ajout en javascript dans l'éditeur ckeditor de la recherche de lien personnage, lieu, articles

### Removed

- Retrait de la recherche de lien d'image dans l'éditeur, car les liens des fichiers sont vite obsolètes

## [1.1.0] - 2023-07-14

### Added

- Ajout option "remember me"
- Ajout des personnages (affichage et administration)
- Ajout des chronologies (affichage et administration)
- Ajout des lieux (affichage et administration)
- Ajout des events de chronologie (affichage et administration) avec possibilité de modifier l'ordre
- Ajout de la recherche pour les chronologies
- Ajout de sections pour les articles avec possibilité de modifier l'ordre
- Ajout de la possibilité de définir une présentation du site
- Favicon dans l'administration
- Possibilité de désactiver l'inscription et la page de contact
- Ajout d'un fichier changelog
- Ajout du bouton générer le slug dans l'administration
- Ajout du bouton générer le nom complet d'un personnage
- Ajout d'un page FAQ
- Ajout de la licence
- Ajout du module d'installation
- Ajout de la page de configuration
- Ajout de la possibilité de rechercher un article, un lieu, un personnage et une image dans l'espace édition
- Ajout directement de CKEditor sans passer par un bundle
- Ajout de la lightbox
- Ajout de la fonctionnalité d'archivage
- Ajout de la recherche de catégorie et d'article dans le menu

### Changed

- Définition des permissions du panneau d'administration
- Import de l'espace édition dans l'espace administration
- Lier les pages aux catégories et aux portails
- Modification de style

### Removed

- Espace édition
- CKEditorBundle

## [1.0.0] - 2022-03-15

### Added

- Affichage des articles
- Affichage des catégories
- Affichage des portails
- Affichage des images
- Affichage des pages
- Connexion
- Création de compte
- Définition des rôles admin, utilisateur et contributeur
- Création du module commentaire (ajout, édition, suppression)
- Création de l'espace d'administration (tableau de bord, gestion articles, catégories, images, portails, utilisateurs, page et commentaire)
- Création de l'espace éditeur pour gérer ses propres articles
- Création du profil éditeur
- Mise en place de la recherche image et article
- Page de contact
