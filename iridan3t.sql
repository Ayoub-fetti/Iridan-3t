-- phpMyAdmin SQL Dump
-- version 5.2.0
-- https://www.phpmyadmin.net/
--
-- Host: localhost:3306
-- Generation Time: Jan 28, 2025 at 11:12 AM
-- Server version: 8.0.30
-- PHP Version: 8.1.10

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `iridan3t`
--

-- --------------------------------------------------------

--
-- Table structure for table `accidents`
--

CREATE TABLE `accidents` (
  `id` int NOT NULL,
  `cars_id` varchar(50) DEFAULT NULL,
  `chauffeurs_id` int DEFAULT NULL,
  `date_declaration_assurance` date NOT NULL,
  `procédure` enum('normal','forfait','garage a greyer') NOT NULL,
  `status_resolution` enum('pv','constat','arrangement') NOT NULL,
  `commentaire` text
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

-- --------------------------------------------------------

--
-- Table structure for table `cars`
--

CREATE TABLE `cars` (
  `matricule` varchar(50) NOT NULL,
  `marque` varchar(250) CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci NOT NULL,
  `ville` varchar(100) NOT NULL,
  `chauffeurs_id` int DEFAULT NULL,
  `carte_grise` varchar(250) CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci NOT NULL,
  `date_expiration_carte_grise` date NOT NULL,
  `visite_technique` varchar(250) CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci NOT NULL,
  `date_expiration_visite` date NOT NULL,
  `assurance` varchar(250) CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci NOT NULL,
  `date_expiration_assurance` date NOT NULL,
  `vignette` varchar(250) CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci NOT NULL,
  `date_expiration_vignette` date NOT NULL,
  `feuille_circulation` varchar(250) CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci NOT NULL,
  `date_expiration_circulation` date NOT NULL,
  `feuille_extincteur` varchar(250) CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci NOT NULL,
  `date_expiration_extincteur` date NOT NULL,
  `feuille_tachygraphe` varchar(250) CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci NOT NULL,
  `date_expiration_tachygraphe` date NOT NULL,
  `status` enum('en panne','en service','en maintenance') NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Dumping data for table `cars`
--

INSERT INTO `cars` (`matricule`, `marque`, `ville`, `chauffeurs_id`, `carte_grise`, `date_expiration_carte_grise`, `visite_technique`, `date_expiration_visite`, `assurance`, `date_expiration_assurance`, `vignette`, `date_expiration_vignette`, `feuille_circulation`, `date_expiration_circulation`, `feuille_extincteur`, `date_expiration_extincteur`, `feuille_tachygraphe`, `date_expiration_tachygraphe`, `status`) VALUES
('test', 'test', 'test', NULL, 'test', '2025-01-15', 'test', '2025-01-14', 'test', '2025-01-15', 'test', '2025-01-21', 'test', '2025-01-23', 'test', '2025-01-16', 'test', '2025-01-16', 'en panne');

-- --------------------------------------------------------

--
-- Table structure for table `personnel`
--

CREATE TABLE `personnel` (
  `id` int NOT NULL,
  `nom_complet` varchar(255) NOT NULL,
  `carte_identite` varchar(250) CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci NOT NULL,
  `date_expiration_carte` date NOT NULL,
  `role` enum('fonctionnaire','chauffeurs','chef de zone','chef de site','menage','securite') NOT NULL,
  `situation_familiale` enum('celibataire','marier') NOT NULL,
  `ville` varchar(250) CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci NOT NULL,
  `adresse` varchar(255) DEFAULT NULL,
  `contrat` varchar(250) CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci DEFAULT NULL,
  `date_embauche` date NOT NULL,
  `date_demission` date DEFAULT NULL,
  `permit_conduire` varchar(250) CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci DEFAULT NULL,
  `date_expiration_permit` date DEFAULT NULL,
  `visite_medicale` varchar(250) CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci DEFAULT NULL,
  `date_expiration_visite` date DEFAULT NULL,
  `photo` varchar(255) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Dumping data for table `personnel`
--

INSERT INTO `personnel` (`id`, `nom_complet`, `carte_identite`, `date_expiration_carte`, `role`, `situation_familiale`, `ville`, `adresse`, `contrat`, `date_embauche`, `date_demission`, `permit_conduire`, `date_expiration_permit`, `visite_medicale`, `date_expiration_visite`, `photo`) VALUES
(2, 'ayoub', 'uploads/cartes_identite/6798baf97da0a_CV-ayoub_fetti.pdf', '2030-02-01', 'chauffeurs', 'celibataire', 'meknes', 'nr 52 ', 'cdd', '2025-12-12', NULL, 'uploads/permis/6798baf97dc4f_CV-ayoub_fetti.pdf', '2025-12-12', 'uploads/visites_medicales/6798baf97dd4b_CV-ayoub_fetti.pdf', '2025-12-12', 'uploads/photos/6798baf97de22_DSC_6805.jpg');

-- --------------------------------------------------------

--
-- Table structure for table `utilisateur`
--

CREATE TABLE `utilisateur` (
  `id` int NOT NULL,
  `full_name` varchar(255) NOT NULL,
  `email` varchar(255) NOT NULL,
  `password` varchar(255) NOT NULL,
  `role` enum('admin','fonctionnaire') NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Dumping data for table `utilisateur`
--

INSERT INTO `utilisateur` (`id`, `full_name`, `email`, `password`, `role`) VALUES
(2, 'admin', 'admin@admin.com', '123', 'admin'),
(3, 'ayoub', 'ayoub@gmail.com', '123123', 'fonctionnaire'),
(26, 'mehdi 1', 'mehdi@gmail.com', '123456', 'fonctionnaire');

--
-- Indexes for dumped tables
--

--
-- Indexes for table `accidents`
--
ALTER TABLE `accidents`
  ADD PRIMARY KEY (`id`),
  ADD KEY `chauffeurs_id` (`chauffeurs_id`),
  ADD KEY `cars_id` (`cars_id`);

--
-- Indexes for table `cars`
--
ALTER TABLE `cars`
  ADD PRIMARY KEY (`matricule`),
  ADD KEY `chauffeurs_id` (`chauffeurs_id`);

--
-- Indexes for table `personnel`
--
ALTER TABLE `personnel`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `utilisateur`
--
ALTER TABLE `utilisateur`
  ADD PRIMARY KEY (`id`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `accidents`
--
ALTER TABLE `accidents`
  MODIFY `id` int NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `personnel`
--
ALTER TABLE `personnel`
  MODIFY `id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT for table `utilisateur`
--
ALTER TABLE `utilisateur`
  MODIFY `id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=27;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `accidents`
--
ALTER TABLE `accidents`
  ADD CONSTRAINT `accidents_ibfk_1` FOREIGN KEY (`chauffeurs_id`) REFERENCES `personnel` (`id`),
  ADD CONSTRAINT `accidents_ibfk_2` FOREIGN KEY (`cars_id`) REFERENCES `cars` (`matricule`);

--
-- Constraints for table `cars`
--
ALTER TABLE `cars`
  ADD CONSTRAINT `cars_ibfk_1` FOREIGN KEY (`chauffeurs_id`) REFERENCES `personnel` (`id`);
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
