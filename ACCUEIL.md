# ✅ Guide de démarrage rapide

## 🎉 Le projet est prêt

J'ai réalisé ce dossier de fin d'études après environ 8 mois de formation en programmation. C'est une application fonctionnelle et organisée, adaptée à un premier vrai projet.

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

> Cette application est full-stack : le front-end public est géré par `public/app.php` et `js/app.js`, tandis que `public/api.php` fournit l'API backend centralisée.

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
  ...
```

---

Consulte aussi :
- `CORRECTIONS.md` pour les changements réalisés
- `README.md` pour la documentation
- `database/vite_gourmand.sql` pour le schéma de la base de données

---

**Dernière mise à jour :** mai 2026
**Statut :** ✅ Prêt pour l'examen
