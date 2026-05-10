-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: May 11, 2026 at 12:35 AM
-- Server version: 10.4.32-MariaDB
-- PHP Version: 8.2.12

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `impactventure`
--

-- --------------------------------------------------------

--
-- Table structure for table `demandes_financement`
--

CREATE TABLE `demandes_financement` (
  `id` int(11) NOT NULL,
  `fiche_entreprise_id` int(11) NOT NULL,
  `investisseur_id` int(11) NOT NULL,
  `message` text DEFAULT NULL,
  `montant_demande` decimal(15,2) DEFAULT NULL,
  `currency` enum('TND','EUR','USD') DEFAULT 'TND',
  `phone` varchar(20) DEFAULT NULL,
  `statut` enum('en_attente','accepte','refuse') DEFAULT 'en_attente',
  `date_demande` datetime DEFAULT current_timestamp(),
  `motif_refus` text DEFAULT NULL,
  `montant_investi` decimal(10,2) DEFAULT NULL,
  `date_financement` datetime DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `demandes_financement`
--

INSERT INTO `demandes_financement` (`id`, `fiche_entreprise_id`, `investisseur_id`, `message`, `montant_demande`, `currency`, `phone`, `statut`, `date_demande`, `motif_refus`, `montant_investi`, `date_financement`) VALUES
(1, 1, 2, 'aa', NULL, 'TND', NULL, 'en_attente', '2026-04-13 01:40:56', NULL, NULL, NULL),
(5, 1, 2, 'kk', NULL, 'TND', NULL, 'en_attente', '2026-04-13 01:45:33', NULL, NULL, NULL),
(6, 1, 2, ',,,', NULL, 'TND', NULL, 'en_attente', '2026-04-13 11:17:19', NULL, NULL, NULL),
(9, 1, 2, 'oui oui baguette bonjour', NULL, 'TND', NULL, 'en_attente', '2026-04-13 15:30:56', NULL, NULL, NULL),
(11, 1, 2, 'test demande edit', NULL, 'TND', NULL, 'en_attente', '2026-04-13 16:09:38', NULL, NULL, NULL),
(12, 1, 1, 'wsh la fleche \"pray\"', NULL, 'TND', NULL, 'accepte', '2026-04-27 10:38:10', NULL, 500.00, '2026-04-27 11:35:17'),
(13, 1, 1, 'arrete', NULL, 'TND', NULL, 'accepte', '2026-04-27 10:38:48', NULL, 200.00, '2026-04-27 11:23:02'),
(15, 1, 1, 'azdazdazdazd', NULL, 'TND', NULL, 'refuse', '2026-05-04 00:31:29', 'parceque', NULL, NULL),
(16, 1, 1, 'akakda', 50000.00, 'TND', '+21693600306', 'accepte', '2026-05-04 00:45:09', NULL, NULL, NULL),
(17, 1, 1, 'heka howa', 50000.00, 'TND', '+21693600306', 'accepte', '2026-05-04 12:12:21', NULL, NULL, NULL),
(18, 1, 1, 'heka howa mara okhra', 50000.00, 'TND', '+21693600306', 'accepte', '2026-05-04 12:17:59', NULL, NULL, NULL),
(19, 1, 1, 'test 3', 50000.00, 'TND', '+21693600306', 'accepte', '2026-05-04 12:32:55', NULL, NULL, NULL),
(20, 1, 1, ':,km', 500000.00, 'TND', '+21693600306', 'accepte', '2026-05-04 12:36:02', NULL, NULL, NULL),
(21, 1, 1, '648532', 500000.00, 'TND', '+21693600306', 'refuse', '2026-05-04 12:36:12', NULL, NULL, NULL),
(22, 1, 1, '64z3ad', 20000.00, 'TND', '+21693600306', 'accepte', '2026-05-04 12:39:39', NULL, NULL, NULL),
(23, 1, 1, 'azdlaz', 50000.00, 'TND', '+21693600306', 'accepte', '2026-05-04 12:49:23', NULL, NULL, NULL),
(24, 1, 1, 'heka howa', 552222.00, 'TND', '+21693600306', 'refuse', '2026-05-04 12:51:52', 'hakek', NULL, NULL),
(25, 1, 1, 'azdkaz', 35535445.00, 'TND', '+21693600306', 'accepte', '2026-05-04 12:56:16', NULL, NULL, NULL),
(26, 1, 1, 'aza', 5000.00, 'TND', '+21693600306', 'accepte', '2026-05-04 15:01:13', NULL, NULL, NULL),
(27, 1, 1, '5', 1500.00, 'TND', '+21693600306', 'accepte', '2026-05-04 15:04:49', NULL, NULL, NULL),
(28, 1, 1, 'kjbnjn', 150000.00, 'TND', '+21693600306', 'accepte', '2026-05-04 16:09:21', NULL, NULL, NULL),
(29, 1, 1, 'oaizfj', 65446846.00, 'TND', '+21693600306', 'accepte', '2026-05-04 16:10:35', NULL, NULL, NULL);

-- --------------------------------------------------------

--
-- Table structure for table `fiche_entreprise`
--

CREATE TABLE `fiche_entreprise` (
  `id` int(11) NOT NULL,
  `nom` varchar(150) NOT NULL,
  `description` text NOT NULL,
  `categorie` varchar(100) NOT NULL DEFAULT 'autre',
  `mots_cles` varchar(255) DEFAULT '',
  `score_green` int(11) DEFAULT 0,
  `created_at` datetime DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `fiche_entreprise`
--

INSERT INTO `fiche_entreprise` (`id`, `nom`, `description`, `categorie`, `mots_cles`, `score_green`, `created_at`) VALUES
(1, 'Intelligence Artificielle', 'Projets autour de l IA et du machine learning', 'tech', 'ia,ml,nlp', 70, '2026-04-13 00:54:19'),
(2, 'Green Energy', 'Energie solaire, eolienne et renouvelable', 'energie renouvelable', 'solaire,co2,vert', 90, '2026-04-13 00:54:19'),
(3, 'AgriTech Green', 'Agriculture intelligente et durable', 'agriculture durable', 'bio,capteurs,iot', 88, '2026-04-13 00:54:19'),
(4, 'E-Commerce & Fintech', 'Commerce en ligne et paiement digital', 'digital', 'ecommerce,paiement', 55, '2026-04-13 00:54:19'),
(5, 'Futur du Travail', 'Remote work, freelance et emploi digital', 'entrepreneuriat', 'remote,freelance', 50, '2026-04-13 00:54:19');

-- --------------------------------------------------------

--
-- Table structure for table `investisseurs`
--

CREATE TABLE `investisseurs` (
  `id` int(11) NOT NULL,
  `nom` varchar(150) NOT NULL,
  `organisation` varchar(100) DEFAULT NULL,
  `secteur_focus` varchar(150) DEFAULT NULL,
  `montant_min` int(11) DEFAULT NULL,
  `montant_max` int(11) DEFAULT NULL,
  `description` text DEFAULT NULL,
  `photo` varchar(255) DEFAULT NULL,
  `linkedin` varchar(255) DEFAULT NULL,
  `created_at` datetime DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `investisseurs`
--

INSERT INTO `investisseurs` (`id`, `nom`, `organisation`, `secteur_focus`, `montant_min`, `montant_max`, `description`, `photo`, `linkedin`, `created_at`) VALUES
(1, 'Fatma Ben Amor', 'Tunisia Green Fund', 'Green Energy & AgriTech', 50000, 300000, 'Fonds d’investissement dédié aux projets à fort impact environnemental en Tunisie.', NULL, 'https://linkedin.com/in/fatmabenamor', '2026-04-13 01:05:53'),
(2, 'Mohamed Ali Khelil', 'Flat6Labs Tunis', 'IA + Deeptech', 30000, 150000, 'Accélérateur et fonds qui investit dans les startups IA et Green Tech.', NULL, 'https://linkedin.com/in/mohamedkhelil', '2026-04-13 01:05:53'),
(3, 'Sara Trabelsi', 'Impact Partners Tunisia', 'Social Impact & Fintech', 20000, 250000, 'Investit dans les startups qui résolvent des problèmes sociaux et environnementaux.', NULL, 'https://linkedin.com/in/saratrabelsi', '2026-04-13 01:05:53');

-- --------------------------------------------------------

--
-- Table structure for table `projet`
--

CREATE TABLE `projet` (
  `id` int(11) NOT NULL,
  `nom` varchar(150) NOT NULL,
  `description` text NOT NULL,
  `theme_id` int(11) NOT NULL,
  `statut` enum('en_attente','approuve','rejete') DEFAULT 'en_attente',
  `created_at` datetime DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Indexes for dumped tables
--

--
-- Indexes for table `demandes_financement`
--
ALTER TABLE `demandes_financement`
  ADD PRIMARY KEY (`id`),
  ADD KEY `fiche_entreprise_id` (`fiche_entreprise_id`),
  ADD KEY `investisseur_id` (`investisseur_id`);

--
-- Indexes for table `fiche_entreprise`
--
ALTER TABLE `fiche_entreprise`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `investisseurs`
--
ALTER TABLE `investisseurs`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `projet`
--
ALTER TABLE `projet`
  ADD PRIMARY KEY (`id`),
  ADD KEY `theme_id` (`theme_id`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `demandes_financement`
--
ALTER TABLE `demandes_financement`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=30;

--
-- AUTO_INCREMENT for table `fiche_entreprise`
--
ALTER TABLE `fiche_entreprise`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

--
-- AUTO_INCREMENT for table `investisseurs`
--
ALTER TABLE `investisseurs`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT for table `projet`
--
ALTER TABLE `projet`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `demandes_financement`
--
ALTER TABLE `demandes_financement`
  ADD CONSTRAINT `demandes_financement_ibfk_1` FOREIGN KEY (`fiche_entreprise_id`) REFERENCES `fiche_entreprise` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `demandes_financement_ibfk_2` FOREIGN KEY (`investisseur_id`) REFERENCES `investisseurs` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `projet`
--
ALTER TABLE `projet`
  ADD CONSTRAINT `projet_ibfk_1` FOREIGN KEY (`theme_id`) REFERENCES `fiche_entreprise` (`id`) ON DELETE CASCADE;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
