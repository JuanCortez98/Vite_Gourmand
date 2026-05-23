# Vite & Gourmand - Application de menus a domicile

## Description
Vite & Gourmand est une application web permettant aux clients des commander des menus préprés par un traiteur, aux serveurs (travailleurs) de les préparer et servir, et à l'administrateur de gérer l'ensemble du système.

**Date** : Février 2026
**ECF Développeur web et web mobile - Niveau 5**

# Technologies utilisées

-PHP 8+
-MySQL 8+
-HTML5 / CSS / Javascript
-PDO pour les requêtes sécurisées (contre injections SQL)
-Protection CSRF sur les formulaires POST
-Sessions PHP pour authentification et panier
-Responsive design (media queries + burger menu)

## Rôles utilisateurs
-- **Admin** : gestion complète (utilisateurs, menus, commandes)
--**Client** : consulter ses commandes, passer une nouvelle commande avec panier
--**Travailleur** : voir les commandes et attente,marquer comme "prête" ou "servie"

## Fonctionnalités principales
-Authentification avec rôles (login / register)
-CRUD complet sur utilisateurs, menus et commandes
-Panier simple côtë client (ajout / retrait de menus, cacul total)
-Changement de status des commandes (en_cours -> prête -> servie)
-Pages publiques : Accueil, À propos et Menu (sans login(menu tu dois faire login pour ajouter la commande au panier))
-Protection CSRF sur tous les formulaires POST
-Responsive mobile (menu burger, scroll horizontal sur tables)

## Installation et configuration
1. Installer **XAMPP** (Apache et MySQL)
2. Démarrer Apache et MySQL
3. Créer une basé de données nomme 'vite_gourmand'
4. Importer les scripts SQL : 'database/vite_gourmand.sql' et 'database/warframes.sql'
5. Copier le projet dans 'C:\xampp\htdocs\vite-gourmand'
6. Accéder à l'application : 'http://localhost/vite-gourmand'

**Utilisateur de test** :
-Admin : admi@vite.fr / admin123
- Client : client@vite.fr / client123
- Travailleur : travailleur@vite.fr / travailleur123

## Structure du projet

- 'admin/' -> Interface administrateur
- 'client/' -> Interface client
- 'travailleur/' -> Interface serveur
- 'Includes/' -> Configuration (connexion BD, fonctions CSRF)
- 'css/' -> Styles globaux et personnalisées pour différentes pages web
- 'database/' -> Scripts SQL et schémas de la base de données
- 'assets/' -> Warframes et captures d'écran

## Architecture front-end actuelle

Les pages publiques utilisent maintenant un shell commun `public/app.php` qui charge le contenu en JavaScript via `js/app.js`.

- `public/api.php` sert une API centralisée pour les menus et la session utilisateur.
- La page `public/index.php`, `public/menus.php`, `public/about.php`, `public/menus-mongo.php`, `public/menus-combined.php`, `public/mentions-legales.php` et `public/conditions-generales.php` sont des wrappers minimes qui incluent `app.php`.
- L'authentification et le token CSRF sont exposés via `public/api.php?resource=session` plutôt que via PHP embarqué dans chaque page.

## Warframes de l'interface utilisateur

Les maquettes de l'interface sont maintenant rangées dans `assets/warframes/`.
Placez vos fichiers PNG/JPEG ici pour garder l'organisation propre.

- Warframe Page d’accueil : `assets/warframes/warframe-accueil.png`
- Warframe Connexion : `assets/warframes/warframe-login.png`
- Warframe Inscription : `assets/warframes/warframe-register.png`
- Warframe Dashboard client : `assets/warframes/warframe-dashboard-client.png`
- Warframe Nouvelle commande : `assets/warframes/warframe-nouvelle-commande.png`
- Warframe Dashboard admin : `assets/warframes/warframe-dashboard-admin.png`

## Captures d'écran

Les captures finales sont stockées dans `assets/captures/`.
Ajoutez les images de validation ici pour le rendu et la présentation.

1. Accueil — `assets/captures/Accueil.jpeg`
2. Page À propos — `assets/captures/About.jpeg`
3. Login — `assets/captures/login.jpeg`
4. Register — `assets/captures/register.jpeg`
5. Dashboard Client — `assets/captures/dashboard-client.jpeg`
6. Nouvelle commande (client) — `assets/captures/nouvelle-commande.jpeg`
7. Dashboard Travailleur — `assets/captures/dashboard-travailleur.jpeg`
8. Dashboard Admin — `assets/captures/dashboard-admin.jpeg`
9. Gestion Utilisateurs (administrateur seulement) — `assets/captures/gestion-utilisateurs-admin.jpeg`
10. Gestion Menus (administrateur seulement) — `assets/captures/gestion-menus-admin.jpeg`
11. Gestion Commandes (administrateur seulement) — `assets/captures/gestion-commandes-admin.jpeg`

## Diagramme Entité-Relation

Diagramme ERD disponible dans `assets/captures/diagramme-erd-vite-gourmand.png`.

Relations entre les tables users, menus, commandes et ligne_commande.

   ## Sécurité mise en place
   - Requêtes préparées PDO (contre injections SQL)
   - Protection CSRG sur tous les formulaires POST
   - Vérification stricte des rôles sur chaque page privée
   - Hashage des mots de passe (bcrypt)

   ## Améliorations possibles
   - Paiement simulé ou intégration Stripe
   - Gestion du stock réel (décrémenter lors de commande)
   - Notifications email ou toast JS
   - Recherche / filtre avancé dans les menus

Projet réalise pour l'ECF développeur Web et Web Mobile - février 2026