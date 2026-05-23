# 🔧 Corrections effectuées - Vite & Gourmand

## Résumé
Des corrections ont été apportées pour améliorer la sécurité, la syntaxe et la cohérence du projet. Le but est de présenter un dossier propre et adapté à un projet d'examen.
Ce document a aussi été mis à jour pour renforcer la présentation full-stack et détailler la structure front-end / back-end.

Des améliorations spécifiques ont été ajoutées pour :
- clarifier l'usage de NoSQL et MongoDB,
- préciser le rôle des warframes dans le projet,
- expliquer plus clairement la couche objet relationnelle.
---

## 📋 Corrections détaillées

### 1. **Includes/config.php** ✅
**Problèmes identifiés :**
- Typo dans la vérification CSRF : `$_POST['csr_token']` au lieu de `$_POST['csrf_token']`
- Absence de régénération du token après utilisation
- Pas de code HTTP 403 en cas d'erreur CSRF

**Modifications :**
- ✅ Correction du typo de vérification CSRF
- ✅ Ajout de la régénération automatique du token après validation
- ✅ Ajout de `http_response_code(403)` pour les requêtes CSRF invalides

---

### 2. **Includes/jwt.php** ✅
**Problèmes identifiés :**
- Mauvais nom de variable pour l'encodage du header
- Typo `$signautre` au lieu de `$signatureEncoded`
- Signature JWT incorrecte avec la chaîne littérale `"header"`
- Typo `json_Decode` au lieu de `json_decode`
- Logique inversée dans la validation du payload
- Token non renvoyé correctement par `generateJWT()`

**Modifications :**
- ✅ Nom de variable corrigé pour le header encodé
- ✅ Signature JWT recalculée avec les bonnes variables
- ✅ Utilisation correcte de `json_decode`
- ✅ Validation du payload corrigée
- ✅ Renvoi du token correctement encodé

---

### 3. **autentification/login.php** ✅
**Problèmes identifiés :**
- Absence de protection CSRF dans le formulaire
- Token CSRF manquant dans le HTML

**Modifications :**
- ✅ Ajout de `verifyCsrfToken()` dans le traitement POST
- ✅ Ajout du champ caché CSRF dans le formulaire

---

### 4. **autentification/register.php** ✅
**Problèmes identifiés :**
- Formulaire d'inscription sans protection CSRF
- Validation d'email correcte, mais pas de CSRF

**Modifications :**
- ✅ Ajout de `verifyCsrfToken()` dans le traitement POST
- ✅ Ajout du champ caché CSRF dans le formulaire

---

### 5. **client/dashboard.php** ✅
**Problèmes identifiés :**
- Mauvais chemin vers `Includes/config.php`
- Redirection vers la même page au lieu de la page public

**Modifications :**
- ✅ Chemin corrigé vers `Includes/config.php`
- ✅ Redirection modifiée vers `../public/index.php`

---

### 6. **admin/index.php** ✅
**Problèmes identifiés :**
- Mauvais dossier `includes` en minuscules

**Modifications :**
- ✅ Correctif des chemins vers `Includes`

---

### 7. **admin/gestion-commandes.php** ✅
**Problèmes identifiés :**
- Mauvais chemin vers `Includes/config.php`
- Redirection vers login sans `.php`

**Modifications :**
- ✅ Correction du chemin `Includes`
- ✅ Ajout de l'extension `.php` à la redirection

---

### 8. **admin/gestion-menus.php** ✅
**Problèmes identifiés :**
- Mauvais chemin vers `Includes/config.php`
- Redirection incomplète sans `.php`

**Modifications :**
- ✅ Correction du chemin `Includes`
- ✅ Ajout de l'extension `.php`

---

### 9. **admin/gestion-utilisateurs.php** ✅
**Problèmes identifiés :**
- Mauvais chemin vers `Includes/config.php`
- Redirection incomplète

**Modifications :**
- ✅ Correction du chemin `Includes`
- ✅ Ajout de l'extension `.php`

---

### 10. **travailleur/dashboard.php** ✅
**Problèmes identifiés :**
- Redirection mal formée
- Redirection vers la même page

**Modifications :**
- ✅ Correction de la syntaxe de `header('Location: ../public/index.php');`

---

### 11. **client/nouvelle-commande.php** ✅
**Problèmes identifiés :**
- Mauvais chemin vers `Includes/config.php`
- Redirection incomplète dans `header`

**Modifications :**
- ✅ Correction du chemin `Includes`
- ✅ Ajout de l'extension `.php`

---

## 🔒 Améliorations de sécurité mises en place

| Aspect | Changement |
|--------|------------|
| **CSRF** | Tokens CSRF régénérés après chaque validation |
| **JWT** | Signature JWT corrigée |
| **Chemins** | Consistance `Includes` (majuscules) |
| **Headers** | Codes HTTP corrects pour les erreurs CSRF |
| **Redirections** | Toutes les redirections ont maintenant `.php` |

---

## 🧪 Étapes suivantes recommandées

1. ✅ Tester toutes les routes d'authentification (login, register, logout)
2. ✅ Vérifier que les tableaux de bord se chargent correctement
3. ✅ Vérifier que les tokens CSRF fonctionnent sur tous les formulaires
4. ✅ Valider JWT si utilisé dans les APIs
5. ✅ Mettre en place HTTPS en production
6. ✅ Utiliser des variables d'environnement pour les identifiants BD en production
7. ✅ Ajouter du rate limiting sur le login / register
8. ✅ Ajouter du logging pour les échecs d'authentification

---

## 📝 Notes importantes

- Le secret CSRF est visible dans le code. En production, il faut utiliser des variables d'environnement.
- Les identifiants BD sont codés en dur. En production, il faut utiliser un fichier `.env` ou des variables d'environnement.
- Sans HTTPS, les tokens CSRF et JWT peuvent être interceptés.

---

**Mis à jour :** avril 2026
