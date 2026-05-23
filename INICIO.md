# ✅ Guide de démarrage rapide

## 🎉 Le projet est prêt

Ce projet est un dossier de fin d'études réalisé par un développeur débutant après environ 8 mois de formation en programmation. Il est conçu comme une application fonctionnelle et organisée, avec un niveau professionnel adapté à un premier projet.

La base de données **vite_gourmand** est déjà créée avec les tables nécessaires et des données de test.

---

## 📊 Vérification de la base de données

```
✅ Base de données : vite_gourmand
✅ Table users : 3 utilisateurs
✅ Table menus : 4 menus
✅ Table commandes : 1 commande
```

### 👤 Comptes de test :

| Email | Rôle | Mot de passe |
|-------|------|--------------|
| admi@vite.fr | Admin | admin123 |
| client@vite.fr | Client | client123 |
| travailleur@vite.fr | Travailleur | travailleur123 |

> Si les comptes ne fonctionnent pas, crée un nouvel utilisateur sur la page d'inscription.

**Note :** les mots de passe sont hachés avec bcrypt (PASSWORD_DEFAULT). Pour tester, tu peux :
- créer un nouvel utilisateur, ou
- remplacer le hash par un mot de passe connu dans la base de données

---

## 🚀 Comment lancer l'application

### Option 1 : Avec XAMPP (recommandé)

1. Ouvre le panneau XAMPP :
   ```
   C:\xampp\xampp-control.exe
   ```
2. Démarre les services :
   - Apache ✅
   - MySQL ✅
3. Accède à l'application :
   - http://localhost/vite-gourmand/public/index.php

### Option 2 : Par terminal

Si XAMPP est déjà lancé, il suffit de :

```powershell
cd C:\xampp\htdocs\vite-gourmand
```

Puis ouvre dans le navigateur :

http://localhost/vite-gourmand/public/index.php

> Ce projet est présenté comme une application full-stack : le front-end public est géré par `public/app.php` et `js/app.js`, tandis que `public/api.php` contient le backend API centralisé.

---

## 🔐 Fonctionnalités implémentées

### ✅ Sécurité
- [x] Tokens CSRF sur tous les formulaires
- [x] Hachage des mots de passe avec bcrypt
- [x] Protection des sessions
- [x] Validation JWT
- [x] Contrôle d'accès par rôle

### ✅ Authentification
- [x] Login par email / mot de passe
- [x] Inscription de nouveaux utilisateurs
- [x] Déconnexion sécurisée
- [x] Redirections selon le rôle (admin/client/travailleur)

### ✅ Fonctions principales
- [x] Gestion des menus (admin)
- [x] Gestion des commandes (travailleur)
- [x] Dashboard client
- [x] Affichage des menus disponibles

---

## 📁 Structure des fichiers

```
vite-gourmand/
├── public/
│   ├── index.php          (Page d'accueil)
│   ├── menus.php          (Catalogue des menus)
│   ├── about.php          (À propos)
│   ├── warframes.php      (Page warframes)
│   ├── mentions-legales.php
│   └── api.php            (API centralisée)
├── autentification/
│   ├── login.php
│   ├── register.php
│   └── logout.php
├── client/
│   ├── dashboard.php
│   └── nouvelle-commande.php
├── admin/
│   ├── index.php
│   ├── gestion-menus.php
│   ├── gestion-commandes.php
│   └── gestion-utilisateurs.php
├── travailleur/
│   └── dashboard.php
├── Includes/
│   ├── config.php
│   ├── jwt.php
│   ├── Models.php
│   └── mongo.php
├── css/
│   ├── style.css
│   └── old/
├── js/
│   └── app.js
└── database/
    ├── vite_gourmand.sql
    └── warframes.sql
```

---

## 🧪 Tests rapides

1. Ouvre la page d'accueil :
   - http://localhost/vite-gourmand/public/index.php
2. Essaye de te connecter :
   - http://localhost/vite-gourmand/autentification/login.php
3. Inscris un nouvel utilisateur :
   - http://localhost/vite-gourmand/autentification/register.php
4. Accède au dashboard admin après connexion :
   - http://localhost/vite-gourmand/admin/index.php

---

## ⚠️ Étapes importantes pour la version finale

1. Modifier les identifiants de la base de données dans `Includes/config.php`
2. Utiliser un fichier `.env` pour les secrets
3. Activer HTTPS pour la production
4. Ajouter un rate limiting sur les connexions
5. Ajouter des logs pour les erreurs et les connexions
6. Vérifier que les comptes de test sont fonctionnels

---

## 🐛 Résolution de problèmes

### Impossible de se connecter à MySQL

- Vérifie que MySQL est bien lancé dans XAMPP.

### Page 404

- Vérifie que le projet est dans `C:\xampp\htdocs\vite-gourmand\`.

### Table introuvable

- Réimporte le script SQL :
  ```powershell
  Get-Content "database/vite_gourmand.sql" | C:\xampp\mysql\bin\mysql -u root
  ```

### Token CSRF invalide

- Recharge la page ou recommence l'action, le token change si la page est rafraîchie.

---

## 📞 Support

Consulte aussi :
- `CORRECCIONES.md` pour les changements réalisés
- `README.md` pour la documentation
- `database/vite_gourmand.sql` pour le schéma de la base de données

---

**Dernière mise à jour :** mai 2026
**Statut :** ✅ Prêt pour l'examen

