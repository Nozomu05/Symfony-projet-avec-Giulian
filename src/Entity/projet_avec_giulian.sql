-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1:3306
-- Generation Time: Oct 17, 2025 at 03:49 PM
-- Server version: 9.1.0
-- PHP Version: 8.3.14

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `projet_avec_giulian`
--

-- --------------------------------------------------------

--
-- Table structure for table `auteur`
--

DROP TABLE IF EXISTS `auteur`;
CREATE TABLE IF NOT EXISTS `auteur` (
  `id` int NOT NULL AUTO_INCREMENT,
  `nom` varchar(50) COLLATE utf8mb3_unicode_ci NOT NULL,
  `prenom` varchar(50) COLLATE utf8mb3_unicode_ci NOT NULL,
  `biographie` longtext COLLATE utf8mb3_unicode_ci,
  `date_naissance` date DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=5 DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_unicode_ci;

--
-- Dumping data for table `auteur`
--

INSERT INTO `auteur` (`id`, `nom`, `prenom`, `biographie`, `date_naissance`) VALUES
(1, 'Orwell', 'George', NULL, NULL),
(2, 'Huxley', 'Aldous', NULL, NULL),
(3, 'Asimov', 'Isaac', NULL, NULL),
(4, 'Herbert', 'Frank', NULL, NULL);

-- --------------------------------------------------------

--
-- Table structure for table `categorie`
--

DROP TABLE IF EXISTS `categorie`;
CREATE TABLE IF NOT EXISTS `categorie` (
  `id` int NOT NULL AUTO_INCREMENT,
  `nom` varchar(50) COLLATE utf8mb3_unicode_ci NOT NULL,
  `description` longtext COLLATE utf8mb3_unicode_ci NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=4 DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_unicode_ci;

--
-- Dumping data for table `categorie`
--

INSERT INTO `categorie` (`id`, `nom`, `description`) VALUES
(1, 'Classique', 'Aucune description'),
(2, 'Fiction', 'Romans de fiction générale'),
(3, 'Science-Fiction', 'Aucune description');

-- --------------------------------------------------------

--
-- Table structure for table `emprunt`
--

DROP TABLE IF EXISTS `emprunt`;
CREATE TABLE IF NOT EXISTS `emprunt` (
  `id` int NOT NULL AUTO_INCREMENT,
  `utilisateur_id` int NOT NULL,
  `livre_id` int NOT NULL,
  `date_emprunt` date NOT NULL,
  `date_retour` date DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `IDX_364071D7FB88E14F` (`utilisateur_id`),
  KEY `IDX_364071D737D925CB` (`livre_id`)
) ENGINE=InnoDB AUTO_INCREMENT=3 DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_unicode_ci;

--
-- Dumping data for table `emprunt`
--

INSERT INTO `emprunt` (`id`, `utilisateur_id`, `livre_id`, `date_emprunt`, `date_retour`) VALUES
(1, 1, 4, '2025-10-17', '2025-10-17'),
(2, 1, 2, '2025-10-17', NULL);

-- --------------------------------------------------------

--
-- Table structure for table `livre`
--

DROP TABLE IF EXISTS `livre`;
CREATE TABLE IF NOT EXISTS `livre` (
  `id` int NOT NULL AUTO_INCREMENT,
  `titre` varchar(50) COLLATE utf8mb3_unicode_ci NOT NULL,
  `date_publication` date NOT NULL,
  `disponible` tinyint(1) NOT NULL,
  `categorie_id` int NOT NULL,
  `auteur_id` int NOT NULL,
  PRIMARY KEY (`id`),
  KEY `IDX_AC634F99BCF5E72D` (`categorie_id`),
  KEY `IDX_AC634F9960BB6FE6` (`auteur_id`)
) ENGINE=InnoDB AUTO_INCREMENT=5 DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_unicode_ci;

--
-- Dumping data for table `livre`
--

INSERT INTO `livre` (`id`, `titre`, `date_publication`, `disponible`, `categorie_id`, `auteur_id`) VALUES
(2, 'Fondation', '1951-05-01', 0, 3, 3),
(3, 'Fondation', '1951-05-01', 1, 3, 3),
(4, 'Dune', '1965-06-01', 1, 3, 4);

-- --------------------------------------------------------

--
-- Table structure for table `utilisateur`
--

DROP TABLE IF EXISTS `utilisateur`;
CREATE TABLE IF NOT EXISTS `utilisateur` (
  `id` int NOT NULL AUTO_INCREMENT,
  `nom` varchar(50) COLLATE utf8mb3_unicode_ci NOT NULL,
  `prenom` varchar(50) COLLATE utf8mb3_unicode_ci NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_unicode_ci;

--
-- Dumping data for table `utilisateur`
--

INSERT INTO `utilisateur` (`id`, `nom`, `prenom`) VALUES
(1, 'Dupont', 'Jean');

--
-- Constraints for dumped tables
--

--
-- Constraints for table `emprunt`
--
ALTER TABLE `emprunt`
  ADD CONSTRAINT `FK_364071D737D925CB` FOREIGN KEY (`livre_id`) REFERENCES `livre` (`id`),
  ADD CONSTRAINT `FK_364071D7FB88E14F` FOREIGN KEY (`utilisateur_id`) REFERENCES `utilisateur` (`id`);

--
-- Constraints for table `livre`
--
ALTER TABLE `livre`
  ADD CONSTRAINT `FK_AC634F9960BB6FE6` FOREIGN KEY (`auteur_id`) REFERENCES `auteur` (`id`),
  ADD CONSTRAINT `FK_AC634F99BCF5E72D` FOREIGN KEY (`categorie_id`) REFERENCES `categorie` (`id`);
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
