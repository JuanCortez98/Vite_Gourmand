# Vite & Gourmand - Projet scolaire

## Description
Ce projet est un dossier de fin d'études réalisé par un développeur débutant après environ 8 mois de formation. L'objectif est de présenter une application fonctionnelle, structurée et claire, tout en montrant une progression professionnelle.

Le projet utilise une architecture full-stack : un front-end JavaScript côté client (`public/app.php` + `js/app.js`) qui consomme un backend PHP centralisé via `public/api.php`.

L'application permet de passer des commandes pour un traiteur avec trois rôles : administrateur, client et travailleur.

**Date** : mai 2026
**Niveau** : Projet étudiant / examen final

## Ce que j'ai utilisé

- PHP 8+
- MySQL 8+
- HTML5 / CSS / JavaScript
- PDO pour les requêtes SQL
- Protection CSRF pour les formulaires POST
- Sessions PHP pour l'authentification et le panier
- Une mise en page responsive simple avec CSS

## Rôles utilisateurs
- **Admin** : gère les utilisateurs, les menus et les commandes.
- **Client** : consulte ses commandes et passe de nouvelles commandes.
- **Travailleur** : voit les commandes en cours et marque les commandes prêtes.

## Fonctionnalités principales
- Authentification avec rôles (login / inscription)
- CRUD pour utilisateurs, menus et commandes
- Panier simple pour le client
- Changement de statut des commandes (en_cours -> prête -> servie)
- Pages publiques : Accueil, À propos, Menus
- Protection CSRF sur tous les formulaires POST
- Interface responsif basique

## Installation et configuration
1. Installer **XAMPP** (Apache et MySQL)
2. Démarrer Apache et MySQL
3. Créer la base de données `vite_gourmand`
4. Importer les scripts SQL : `database/vite_gourmand.sql` et `database/warframes.sql`
5. Copier le projet dans `C:\xampp\htdocs\vite-gourmand`
6. Ouvrir `http://localhost/vite-gourmand`

**Comptes de test** :
- Admin : admi@vite.fr / admin123
- Client : client@vite.fr / client123
- Travailleur : travailleur@vite.fr / travailleur123

## Structure du projet

- `admin/` -> Interface administrateur
- `client/` -> Interface client
- `travailleur/` -> Interface travailleur
- `Includes/` -> Configuration (connexion BD, fonctions CSRF, modèle)
- `css/` -> Styles globaux et fichiers CSS
- `js/` -> Scripts JavaScript côté client
- `database/` -> Scripts SQL et schéma
- `public/` -> Pages publiques et API
- `assets/` -> Warframes et captures d'écran

## Organisation front-end

Les pages publiques utilisent un shell commun `public/app.php` qui affiche le contenu avec JavaScript via `js/app.js`.

- `public/api.php` sert de point central pour les menus, les commandes, les warframes et la session.
- Les pages `public/index.php`, `public/menus.php`, `public/about.php`, `public/menus-mongo.php`, `public/menus-combined.php`, `public/warframes.php`, `public/mentions-legales.php` et `public/conditions-generales.php` incluent toutes `app.php`.
- Le front-end public est rendu dynamiquement côté client par `js/app.js` : les pages consomment l'API via `fetch()` et affichent leurs sections sans recharger toute la structure.
- L'authentification, la session et le token CSRF sont exposés via `public/api.php?resource=session`, ce qui réduit l'utilisation de PHP direct dans les pages publiques.
- Une partie MongoDB est fournie comme démonstration optionnelle d'une architecture hybride SQL/NoSQL.

## Warframes / maquettes

La dossier `assets/warframes/` contient les maquettes de l'interface.
La base de données SQL contient une table `warframes` pour stocker les métadonnées de design, et l'API peut renvoyer ces données via `public/api.php?resource=warframes`.

- Maquette Accueil : `assets/warframes/warframe-accueil.png`
- Maquette Login : `assets/warframes/warframe-login.png`
- Maquette Inscription : `assets/warframes/warframe-register.png`
- Maquette Dashboard Client : `assets/warframes/warframe-dashboard-client.png`
- Maquette Nouvelle commande : `assets/warframes/warframe-nouvelle-commande.png`
- Maquette Dashboard Admin : `assets/warframes/warframe-dashboard-admin.png`

## Captures d'écran

Les captures sont dans `assets/captures/`.

1. Accueil — `assets/captures/Accueil.jpeg`
2. Page À propos — `assets/captures/About.jpeg`
3. Login — `assets/captures/login.jpeg`
4. Inscription — `assets/captures/register.jpeg`
5. Dashboard Client — `assets/captures/dashboard-client.jpeg`
6. Nouvelle commande (client) — `assets/captures/nouvelle-commande.jpeg`
7. Dashboard Travailleur — `assets/captures/dashboard-travailleur.jpeg`
8. Dashboard Admin — `assets/captures/dashboard-admin.jpeg`
9. Gestion Utilisateurs (admin) — `assets/captures/gestion-utilisateurs-admin.jpeg`
10. Gestion Menus (admin) — `assets/captures/gestion-menus-admin.jpeg`
11. Gestion Commandes (admin) — `assets/captures/gestion-commandes-admin.jpeg`

## Diagramme ER

Diagramme ER disponible dans `assets/captures/diagramme-erd-vite-gourmand.png`.

Relations entre les tables `users`, `menus`, `commandes` et `ligne_commande`.

## Architecture orientée objet

Dans `Includes/Models.php`, il y a des classes simples pour gérer les données :
- `MenuModel` pour les menus SQL
- `OrderModel` pour créer les commandes et gérer les transactions
- `WarframeModel` pour les métadonnées des warframes

Cette couche sépare la logique métier de l'affichage.

## NoSQL et MongoDB

Une partie optionnelle utilise MongoDB dans `Includes/mongo.php`.
C'est un exemple pour montrer une architecture hybride SQL/NoSQL.
- Menus MongoDB : `public/api.php?resource=menus-mongo`
- Warframes MongoDB : `public/api.php?resource=warframes-mongo`

## Sécurité
- Requêtes PDO pour limiter l'injection SQL
- Protection CSRF sur les formulaires
- Contrôle de rôle sur les pages privées
- Mots de passe hachés avec bcrypt

## Améliorations possibles
- Ajouter un paiement simulé ou Stripe
- Gérer le stock réel quand une commande est passée
- Ajouter des notifications par email ou messages toast
- Faire une recherche et des filtres dans les menus

---

> Ce projet est présenté comme un dossier professionnel simple, créé par un développeur en début de carrière. Le code est structuré et clair, mais il reste évolutif pour une mise en production plus avancée.

**Dernière mise à jour :** mai 2026
**Statut :** ✅ Prêt pour validation et examen
