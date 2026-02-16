Read this in [English](README.en.md) | Leer esto en [Español](README.es.md)

# 🎓 ESTIA Stage - Plateforme de Recrutement

![Status](https://img.shields.io/badge/Status-Academic_Project-blue)
![Langage](https://img.shields.io/badge/Backend-PHP-purple)
![Frontend](https://img.shields.io/badge/Frontend-TailwindCSS-teal)

## ℹ️ À propos de ce projet

**⚠️ Contexte Académique :**
Ce projet a été développé dans le cadre d'un cursus académique à l'**ESTIA**. Il s'agit d'une application web développée en **local** à des fins pédagogiques. Elle n'a pas vocation à être déployée en production telle quelle, mais sert de démonstration de compétences en développement Fullstack (PHP/JS/SQL).

L'objectif était de créer une plateforme fonctionnelle mettant en relation des étudiants et des recruteurs, avec un panneau d'administration pour la modération.

---

## ✨ Fonctionnalités Principales

L'application est divisée en trois espaces distincts :

### 👨‍🎓 Espace Étudiant
- **Tableau de bord :** Vue d'ensemble des candidatures et statistiques.
- **Recherche d'offres :** Filtrage par secteur, type de contrat et recherche par mots-clés.
- **Candidature :** Postuler aux offres, gestion des favoris.
- **Messagerie :** Chat en temps réel (simulation) avec les recruteurs.
- **Signalement :** Possibilité de signaler une offre suspecte à l'administration.
- **Profil :** Gestion des informations personnelles et upload de CV.

### 💼 Espace Recruteur
- **Gestion des offres :** Création, modification et suppression d'offres de stage/emploi.
- **Suivi des candidats :** Gestion des statuts (En attente, Entretien, Accepté, Refusé).
- **Messagerie :** Contact direct avec les candidats.
- **Notifications :** Alertes lors de la réception de messages.

### 🛡️ Espace Administrateur
- **Vue globale :** Statistiques sur les utilisateurs et les offres.
- **Gestion utilisateurs :** CRUD (Créer, Lire, Mettre à jour, Supprimer) sur les étudiants et recruteurs.
- **Modération :** Validation des offres en attente et traitement des signalements (suppression ou ignorance).

---

## 🛠️ Stack Technique

* **Frontend :** HTML5, CSS3 (TailwindCSS via CDN), JavaScript (Vanilla ES6).
* **Backend :** PHP (Natif, sans framework).
* **Base de données :** MySQL.
* **Design :** FontAwesome (Icônes), Google Fonts (Inter).

---

## 🚀 Installation et Lancement

Ce projet est conçu pour tourner sur un serveur local (WAMP, XAMPP, MAMP ou Laragon).

### Prérequis
* Un serveur local supportant PHP et MySQL.
* Navigateur web moderne.

### Étapes
1.  **Cloner le dépôt :**
    ```bash
    git clone [https://github.com/sam-ctd/estia-stage-platform.git](https://github.com/sam-ctd/estia-stage-platform.git)
    ```
2.  **Base de données :**
    * Ouvrez votre gestionnaire de BDD (ex: phpMyAdmin).
    * Créez une base de données nommée `estia_stage` (ou autre).
    * Importez le fichier `estia_stage.sql` fourni à la racine.
3.  **Configuration :**
    * Ouvrez le fichier `db.php`.
    * Modifiez les identifiants de connexion PDO si nécessaire (`host`, `dbname`, `username`, `password`).
4.  **Lancement :**
    * Placez le dossier du projet dans le répertoire `www` ou `htdocs` de votre serveur.
    * Accédez à `http://localhost/estia-stage-platform/login.html`.

---

## 🔑 Comptes de Démonstration

Pour tester les différents rôles, vous pouvez utiliser ces comptes (s'ils sont présents dans le fichier SQL importé) :

| Rôle | Email | Mot de passe |
| :--- | :--- | :--- |
| **Étudiant** | `marie@estia.fr` | `1234` |
| **Recruteur** | `rh@airbus.com` | `1234` |
| **Admin** | `admin@estia.fr` | `1234` |

---

## 📂 Structure du Projet

```text
/
├── admin.html       # Dashboard Administrateur
├── login.html       # Page de connexion
├── recruiter.html   # Dashboard Recruteur
├── signup.html      # Page d'inscription
├── student.html     # Dashboard Étudiant
├── style.css        # Styles CSS personnalisés
├── api.php          # Backend (API REST en PHP)
├── db.php           # Connexion à la base de données
├── estia_stage.sql  # Structure et données de la base de données
└── README.md        # Documentation

Auteur : Samuel CHATARD
Date : 16/02/2026