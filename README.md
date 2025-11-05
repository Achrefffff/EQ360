# EQ360 - Personal Development Management API

## 📋 Description

**EQ360** est une API REST développée avec Symfony pour la gestion du développement personnel et professionnel gamifié. Le système permet aux utilisateurs d'organiser leur vie à travers différentes facettes de leur personnalité (SPPA - Sous-Personnalités d'Activité) et de suivre leur progression via un système d'expérience et de niveaux.

## ✨ Fonctionnalités Principales

- **Gestion des SPPA** : Créez différentes facettes de votre personnalité (Développeur, Artiste, Sportif, etc.)
- **Projets** : Organisez vos projets par SPPA avec budget, dates et statut
- **Objectifs** : Définissez des objectifs SMART liés à vos projets
- **Tâches** : Décomposez vos objectifs en tâches avec priorité, difficulté et enthousiasme
- **Gamification** : Système d'XP, niveaux et heures accumulées par SPPA
- **Authentification JWT** : Sécurisation des endpoints avec tokens JWT



## 🛠️ Technologies

- **Framework** : Symfony 5.4
- **ORM** : Doctrine
- **Authentification** : LexikJWTAuthenticationBundle
- **Base de données** : MySQL
- **Architecture** : REST API avec pattern Service/Repository

## 📦 Installation

```bash
# Cloner le projet
git clone https://github.com/votre-username/eq360.git
cd eq360

# Installer les dépendances
composer install

# Configurer la base de données (.env)
DATABASE_URL="mysql://user:password@127.0.0.1:3306/eq360"

# Créer la base de données
php bin/console doctrine:database:create
php bin/console doctrine:migrations:migrate

# Générer les clés JWT
php bin/console lexik:jwt:generate-keypair

# Lancer le serveur
symfony server:start