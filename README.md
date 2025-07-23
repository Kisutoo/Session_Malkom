# 📅 Sessions - Projet Symfony

Bienvenue sur **Sessions**, un petit projet Symfony permettant de gérer des sessions de formation, les stagiaires, les formateurs et les modules abordés. Ce projet comprend une interface d’administration sécurisée, des rôles différenciés (utilisateur, formateur, admin), ainsi qu’un affichage dynamique des plannings avec intégration de **Calendly**.

---

## 🧰 Technologies utilisées

- [Symfony](https://symfony.com/) 7+
- Doctrine ORM
- Twig
- Symfony Security
- Symfony RateLimiter
- Google reCAPTCHA (ou autre CAPTCHA)
- Calendly (API)
- Composer
- PHP 8.1+
- MySQL / MariaDB

---

## 🗂️ Fonctionnalités principales

### 🖥️ Front Office

- **Liste des sessions** :
  - Titre, date de début/fin, places réservées et disponibles

- **Détail d’une session** :
  - Stagiaires inscrits
  - Modules abordés avec durées (ex. *Design - 2 jours*)


- **Liste de tous les stagiaires disponibles**

- **Connexion / Inscription sécurisées** :
  - Authentification avec gestion des rôles : `ROLE_USER`,  `ROLE_ADMIN`
  - Sécurisation et filtrage des formulaires côté back-end
  - **CAPTCHA** pour empêcher les bots
  - **Rate limiter** pour éviter les attaques par force brute

---

### 👨‍🏫 Espace Formateur

- Chaque **formateur** est un utilisateur lié à une ou plusieurs sessions
- Dans leur **profil personnel**, les formateurs peuvent :
  - Voir la liste des sessions où ils interviennent
  - Visualiser leur **calendrier des sessions** via une intégration **Calendly**

---

### 🔐 Back Office (Admin)

- **Gestion complète des sessions** :
  - Titre, dates, nombre de places
  - Modules (vérifie que la **somme des durées** ne dépasse pas la durée de la session)
  - Attribution d’un formateur
- **Gestion des stagiaires** :
  - Ajout manuel à une session
  - Contrôle automatique : pas plus de stagiaires que de **places disponibles - réservées**
- **Création sécurisée** :
  - Filtrage des données entrées
  - Validation back-end

---

![2025-07-2323-28-11-ezgif com-crop](https://github.com/user-attachments/assets/690054fa-daf1-493e-8568-7136e738c976)

---

## ✅ Règles métiers

- Une session a :
  - Un **titre**
  - Une **plage de dates**
  - Des **places limitées**
  - Un ou plusieurs **modules**
  - Un **formateur** assigné
- Les modules ne peuvent dépasser la durée de la session
- Le nombre de stagiaires ne peut dépasser le nombre de places disponibles
- Le rôle de formateur est distinct, avec accès restreint à ses propres sessions
- L’intégration **Calendly** permet de visualiser les sessions sous forme de planning
- **Connexion protégée** par :
  - CAPTCHA (anti-bot)
  - RateLimiter (anti-bruteforce)

---

## 🚀 Installation locale

1. Clonez le dépôt :
   ```bash
   git clone https://github.com/votre-utilisateur/sessions.git
   cd sessions
2. Installez les dépendances :
   ```bash
   composer install
3. Configurez votre base de données dans .env.local :<br>
   DATABASE_URL="mysql://user:password@127.0.0.1:3306/sessions_db"

4. Migrations :
   ```bash
   php bin/console doctrine:database:create
   php bin/console doctrine:migrations:migrate
5. Démarrez le serveur :
   ```bash
   symfony server:start

## 👤 Accès & rôles

Après s'être inscrit sur le site :<br>
<br>
UPDATE user SET roles = '["ROLE_ADMIN"]' WHERE email = "admin@example.com"; (Dans la base de donnée pour s'ajouter le role admin)
