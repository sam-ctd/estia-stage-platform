-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Hôte : 127.0.0.1:3306
-- Généré le : lun. 16 fév. 2026 à 16:23
-- Version du serveur : 9.1.0
-- Version de PHP : 8.3.14

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Base de données : `estia_stage`
--

-- --------------------------------------------------------

--
-- Structure de la table `applications`
--

DROP TABLE IF EXISTS `applications`;
CREATE TABLE IF NOT EXISTS `applications` (
  `id` int NOT NULL AUTO_INCREMENT,
  `user_id` int NOT NULL,
  `job_id` int NOT NULL,
  `status` enum('en_attente','entretien','accepte','refuse','nouveau') DEFAULT 'nouveau',
  `date` datetime DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  KEY `user_id` (`user_id`),
  KEY `job_id` (`job_id`)
) ENGINE=MyISAM AUTO_INCREMENT=5 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Déchargement des données de la table `applications`
--

INSERT INTO `applications` (`id`, `user_id`, `job_id`, `status`, `date`) VALUES
(1, 6, 9, 'en_attente', '2026-01-23 08:46:32'),
(2, 6, 10, 'entretien', '2026-01-23 08:46:38'),
(3, 6, 11, 'en_attente', '2026-01-23 08:46:43'),
(4, 6, 12, 'en_attente', '2026-01-23 12:13:10');

-- --------------------------------------------------------

--
-- Structure de la table `jobs`
--

DROP TABLE IF EXISTS `jobs`;
CREATE TABLE IF NOT EXISTS `jobs` (
  `id` int NOT NULL AUTO_INCREMENT,
  `recruiter_id` int NOT NULL,
  `title` varchar(255) NOT NULL,
  `company` varchar(255) NOT NULL,
  `location` varchar(100) NOT NULL,
  `type` varchar(50) NOT NULL,
  `sector` varchar(50) NOT NULL,
  `description` text,
  `status` enum('published','pending') DEFAULT 'pending',
  `created_at` datetime DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  KEY `recruiter_id` (`recruiter_id`)
) ENGINE=MyISAM AUTO_INCREMENT=32 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Déchargement des données de la table `jobs`
--

INSERT INTO `jobs` (`id`, `recruiter_id`, `title`, `company`, `location`, `type`, `sector`, `description`, `status`, `created_at`) VALUES
(1, 2, 'Ingénieur Aéronautique Junior', 'Airbus', 'Toulouse', 'Stage', 'aero', 'Participation à la conception des systèmes de vol.', 'published', '2026-01-14 12:17:24'),
(2, 2, 'Data Analyst Flight Ops', 'Airbus', 'Blagnac', 'Alternance', 'tech', 'Analyse des données de vol pour optimisation.', 'published', '2026-01-14 12:17:24'),
(3, 3, 'Développeur Full Stack JS', 'Capgemini', 'Bidart', 'Stage', 'tech', 'Développement d\'applications web React/Node.', 'published', '2026-01-14 12:17:24'),
(4, 3, 'Consultant Transformation Digitale', 'Capgemini', 'Paris', 'CDI', 'conseil', 'Accompagnement des clients dans leur stratégie.', 'published', '2026-01-14 12:17:24'),
(5, 4, 'Concepteur Mécanique', 'Safran', 'Bordes', 'Stage', 'aero', 'Conception de pièces moteurs sous CATIA.', 'published', '2026-01-14 12:17:24'),
(6, 4, 'Chef de Projet Industriel', 'Safran', 'Tarnos', 'Alternance', 'aero', 'Suivi de production et amélioration continue.', 'published', '2026-01-14 12:17:24'),
(7, 5, 'Ingénieur Cybersécurité', 'Thales', 'Mérignac', 'Stage', 'tech', 'Audit et sécurisation des systèmes embarqués.', 'published', '2026-01-14 12:17:24'),
(8, 5, 'Développeur Système Embarqué', 'Thales', 'Brest', 'CDI', 'tech', 'Développement C++ sur cibles temps réel.', 'published', '2026-01-14 12:17:24'),
(9, 2, 'Ingénieur Maintenance Prédictive', 'Airbus', 'Toulouse', 'Alternance', 'aero', 'Mise en place d\'algorithmes de maintenance pour les moteurs.', 'published', '2026-01-14 12:19:03'),
(10, 2, 'Stage Support Client International', 'Airbus', 'Toulouse', 'Stage', 'conseil', 'Gestion de la relation client avec les compagnies étrangères.', 'published', '2026-01-14 12:19:03'),
(11, 3, 'Consultant Cybersécurité', 'Capgemini', 'Paris', 'CDI', 'tech', 'Protection des infrastructures critiques de nos clients.', 'published', '2026-01-14 12:19:03'),
(12, 3, 'Stage Développeur Mobile iOS', 'Capgemini', 'Bidart', 'Stage', 'tech', 'Développement de l\'application compagnon pour un grand groupe bancaire.', 'published', '2026-01-14 12:19:03'),
(13, 3, 'Business Analyst Junior', 'Capgemini', 'Lyon', 'Alternance', 'conseil', 'Analyse des besoins métiers pour la transformation digitale.', 'published', '2026-01-14 12:19:03'),
(14, 4, 'Ingénieur Qualité Fournisseurs', 'Safran', 'Bordes', 'CDI', 'aero', 'Audit et suivi de la qualité chez nos sous-traitants.', 'published', '2026-01-14 12:19:03'),
(15, 4, 'Stage RH & Marque Employeur', 'Safran', 'Tarnos', 'Stage', 'conseil', 'Développement de l\'attractivité du groupe auprès des écoles.', 'published', '2026-01-14 12:19:03'),
(16, 5, 'Ingénieur Système Radar Naval', 'Thales', 'Brest', 'CDI', 'aero', 'Optimisation des signaux radars pour la défense maritime.', 'published', '2026-01-14 12:19:03'),
(17, 5, 'Stage IA & Machine Learning', 'Thales', 'Mérignac', 'Stage', 'tech', 'Reconnaissance d\'images satellite via réseaux de neurones.', 'published', '2026-01-14 12:19:03'),
(18, 5, 'Expert Cloud AWS/Azure', 'Thales', 'Paris', 'CDI', 'tech', 'Migration d\'architectures on-premise vers le cloud.', 'published', '2026-01-14 12:19:03');

-- --------------------------------------------------------

--
-- Structure de la table `messages`
--

DROP TABLE IF EXISTS `messages`;
CREATE TABLE IF NOT EXISTS `messages` (
  `id` int NOT NULL AUTO_INCREMENT,
  `sender_id` int NOT NULL,
  `receiver_id` int NOT NULL,
  `message` text NOT NULL,
  `created_at` datetime DEFAULT CURRENT_TIMESTAMP,
  `job_id` int NOT NULL,
  PRIMARY KEY (`id`),
  KEY `sender_id` (`sender_id`),
  KEY `receiver_id` (`receiver_id`),
  KEY `fk_message_job` (`job_id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

-- --------------------------------------------------------

--
-- Structure de la table `notifications`
--

DROP TABLE IF EXISTS `notifications`;
CREATE TABLE IF NOT EXISTS `notifications` (
  `id` int NOT NULL AUTO_INCREMENT,
  `user_id` int NOT NULL,
  `type` varchar(50) NOT NULL,
  `title` varchar(255) NOT NULL,
  `message` text NOT NULL,
  `is_read` tinyint(1) DEFAULT '0',
  `created_at` datetime DEFAULT CURRENT_TIMESTAMP,
  `data` text,
  PRIMARY KEY (`id`),
  KEY `user_id` (`user_id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

-- --------------------------------------------------------

--
-- Structure de la table `reports`
--

DROP TABLE IF EXISTS `reports`;
CREATE TABLE IF NOT EXISTS `reports` (
  `id` int NOT NULL AUTO_INCREMENT,
  `user_id` int NOT NULL,
  `job_id` int NOT NULL,
  `reason` varchar(50) NOT NULL,
  `created_at` datetime DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  KEY `user_id` (`user_id`),
  KEY `job_id` (`job_id`)
) ENGINE=MyISAM AUTO_INCREMENT=13 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Déchargement des données de la table `reports`
--

INSERT INTO `reports` (`id`, `user_id`, `job_id`, `reason`, `created_at`) VALUES
(12, 6, 9, 'spam', '2026-01-23 10:51:05');

-- --------------------------------------------------------

--
-- Structure de la table `users`
--

DROP TABLE IF EXISTS `users`;
CREATE TABLE IF NOT EXISTS `users` (
  `id` int NOT NULL AUTO_INCREMENT,
  `name` varchar(100) NOT NULL,
  `email` varchar(100) NOT NULL,
  `password` varchar(255) NOT NULL,
  `role` enum('student','recruiter','admin') NOT NULL,
  `phone` varchar(20) DEFAULT NULL,
  `created_at` datetime DEFAULT CURRENT_TIMESTAMP,
  `status` enum('active','pending','inactive') DEFAULT 'active',
  `company` varchar(255) DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `email` (`email`)
) ENGINE=MyISAM AUTO_INCREMENT=10 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Déchargement des données de la table `users`
--

INSERT INTO `users` (`id`, `name`, `email`, `password`, `role`, `phone`, `created_at`, `status`, `company`) VALUES
(1, 'Administrateur', 'admin@estia.fr', '1234', 'admin', '0559000000', '2026-01-14 12:17:23', 'active', 'ESTIA'),
(2, 'Sophie Martin', 'rh@airbus.com', '1234', 'recruiter', '0559123456', '2026-01-14 12:17:23', 'active', 'Airbus'),
(3, 'Thomas Durand', 'rh@capgemini.com', '1234', 'recruiter', '0559654321', '2026-01-14 12:17:23', 'active', 'Capgemini'),
(4, 'Julie Petit', 'rh@safran.com', '1234', 'recruiter', '0559987654', '2026-01-14 12:17:23', 'active', 'Safran'),
(5, 'Marc Dubois', 'rh@thales.com', '1234', 'recruiter', '0559111222', '2026-01-14 12:17:23', 'active', 'Thales'),
(6, 'Marie Dupont', 'marie@estia.fr', '1234', 'student', '0611223344', '2026-01-14 12:17:23', 'active', NULL),
(7, 'Lucas Bernard', 'lucas@estia.fr', '1234', 'student', '0699887766', '2026-01-14 12:17:23', 'active', NULL);
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
