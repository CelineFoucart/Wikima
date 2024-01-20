# Changelog

All notable changes to this project will be documented in this file.

The format is based on [Keep a Changelog](https://keepachangelog.com/en/1.0.0/),
and this project adheres to [Semantic Versioning](https://semver.org/spec/v2.0.0.html).

## [2.5.0] - 2024-01-20

### Added

- Ajouter le module de messagerie privée : affichage, envoi, conversation, suppression
- Ajouter la désactivation du module messagerie privée
- Ajouter l'export des images d'un portail
- Ajouter l'export des images d'une catégorie
- Ajouter l'affichage du nombre de notes non traitées
- Ajouter l'affichage du nombre de messages privés non lus
- Ajouter la désactivation des commentaires d'un article
- Ajouter l'export en un clic de tous les fichiers et de la page de données

### Changed

- Les articles privées et brouillons ne sont plus affichés dans la fonctionnalité article au hasard

### Fixed

- Corriger le bug des champs non éditables dans l'éditeur de la modal d'ajout public d'une note
- Corriger les problèmes responsives admin et public

## [2.4.0] - 2024-01-11

### Added

- Ajouter l'export en word de la fiche personnage, de la fiche lieu et de la chronologie
- Ajouter l'export en word d'un portail : articles, personnages, lieux, chronologie
- Ajouter la possibilité de dupliquer un épisode de scénario
- Ajouter le champ couleur par défaut sur le scénario

### Changed

- Revoir les boutons d'accès rapide sur les articles pour avoir un accès plus simple à l'édition de l'article

### Fixed

- Corriger le bug du marqueur de carte mal placé sur firefox

## [2.3.0] - 2024-01-05

### Added

- Ajouter le module carte : gestion des cartes, index, page détail, liaison des éléments de la carte aux lieux
- Ajouter au formulaire de la carte le titre de l'image, ses catégories et ses portails dans "créer depuis une image"
- Ajouter le réglage des accès des cartes
- Ajouter l'archivage du scénario et de ses épisodes
- Ajouter un champ personnages dans le syno,  afficher sur le personnage ses synos et préremplir les cartes avec
- Ajouter un champ lieu pour le scénario et préremplir les cartes avec
- Ajouter un champ groupe d'image au formulaire image
- Ajouter l'onglet scenario dans la page détail portail et la page détail categorie

### Changed

- Le lien du groupe d'image du scénario renvoie vers la page de gestion

### Fixed

- Corriger les contrôle des accès manquants
- Corriger du lien manquant dans la gestion du personnage : ajouter son image
- Correction du bug "entityManager is closed" lors de la création d'un log suite à une erreur doctrine

## [2.2.0] - 2023-12-26

### Added

- Ajouter le dossier /public/img/ dans l'export images
- Ajouter une explication : comment réimporter les données
- Ajouter la taille du dossier images
- Ajouter l'export d'une langue en word
- Ajouter la configuration des accès groupes d'image
- Ajouter les logs

### Changed

- Après clic sur l'édition d'une section, le bouton retourner à l'article doit ramener à la section.
- Améliorer la page liste des portails

### Fixed

- Correction du bug du lien de l'auteur d'un sujet dans la liste des sujets
- Ajouter les breadcrumbs manquants

## [2.1.0] - 2023-12-15

### Added

- Ajout des groupes d'images
- Ajout d'un filtre par rôle sur l'index des utilisateurs dans l'administration
- Ajout du bouton accès à la gestion pour les articles, les scénarios et les timelines sur le listing public

### Changed

- Amélioration du responsive de l'administration pour les boutons et les graphiques

### Fixed

- Ajout d'un icône pour indiquer si le contenu de l'épisode de scénario est rédigé ou pas
- Ajout de couleurs plus diversifiée dans le color picker des formulaires épisodes
- Supprimer les boutons inactifs

## [2.0.0] - 2023-12-08

### Added

- Ajout de la génération d'un fichier word pour les articles
- Ajout de la gestion des scénarios : administration, affichage public, export PDF et word
- Ajout de la gestion des catégories de scénarios
- Ajout de la recherche dans le forum

### Fixed

- Correction du bug de tri des événements de chronologie
- Correction du problème d'affichage de l'éditeur des portails

### Removed

- Retrait des fixtures de développement, devenues trop obsolètes.

## [1.4.0] - 2023-12-01

### Fixed

- Ajout du lien manquant vers la licence MIT dans la modal
- Correction du menu latéral non scrollable de l'admin
- Déprécation doctrine : report_fields_where_declared et validate_xml_mapping

### Added

- Ajout du module forum : catégorie de forum, forum, topic, post, groupe d'utilisateur et espace administration
- Ajout de la possibilité d'activer ou de désactiver le module
- Ajout de l'avatar, de la localisation et du rang dans le profil utilisateur
- Ajout d'une flèche pour retourner en haut dans les pages lieu, personnage, article et chronologie
- Ajout du lien vers les catégories dans la navbar
- Ajout de la personnalisation du menu de navigation sur la page d'accueil
- Ajout de l'icône top dans le sommaire des articles avec sections
- Ajout de la configuration des accès des pages principales (articles, portails, catégories, types, lieux, personnages, index du forum, images, accueil)

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
