-- phpMyAdmin SQL Dump
-- version 5.2.0
-- https://www.phpmyadmin.net/
--
-- Host: localhost:3306
-- Generation Time: Feb 22, 2025 at 01:25 PM
-- Server version: 8.0.30
-- PHP Version: 8.4.4

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
  `procédure` enum('normal','forfait','garage agrée') CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci NOT NULL,
  `status_resolution` enum('pv','constat','arrangement') NOT NULL,
  `commentaire` text,
  `date_accident` date DEFAULT NULL,
  `date_reparation` date DEFAULT NULL,
  `suivie` enum('En cours','Cloture','Arrangement') DEFAULT (_utf8mb4'En cours')
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Dumping data for table `accidents`
--

INSERT INTO `accidents` (`id`, `cars_id`, `chauffeurs_id`, `date_declaration_assurance`, `procédure`, `status_resolution`, `commentaire`, `date_accident`, `date_reparation`, `suivie`) VALUES
(9, 'Ut dolorum iure vel ', 9, '1972-07-18', 'forfait', 'arrangement', 'Rerum laboriosam no', '2006-07-14', '2006-06-09', 'Cloture'),
(16, 'Ut dolorum iure vel ', 9, '1972-04-28', 'forfait', 'constat', 'Quis et quo sint lab', '2016-01-04', '1983-09-25', 'En cours');

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
('Ut dolorum iure vel ', 'Aliquip et odio ab e', 'Vel dignissimos mole', 9, 'uploads/cars/carte_grise/67b37f16b661e_motivation letter.pdf', '2025-12-12', 'uploads/cars/visite_technique/67b37f16b681e_motivation letter.pdf', '2025-12-12', 'uploads/cars/assurance/67b37f16b692e_motivation letter.pdf', '2025-12-12', 'uploads/cars/vignette/67b37f16b6a45_motivation letter.pdf', '2025-12-12', 'uploads/cars/circulation/67b37f16b6b6b_motivation letter.pdf', '2025-12-12', 'uploads/cars/extincteur/67b37f16b6c9e_motivation letter.pdf', '2025-12-12', 'uploads/cars/tachygraphe/67b37f16b6d96_motivation letter.pdf', '2030-12-12', 'en maintenance');

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
(9, 'ayoub', 'uploads/cartes_identite/67b3771a7dd64_motivation letter.pdf', '2025-12-12', 'chauffeurs', 'celibataire', 'meknes', 'ville', 'uploads/contrat/67b3771a7e66a_motivation letter.pdf', '2025-12-12', '2025-12-12', 'uploads/permis/67b3771a7e9cb_motivation letter.pdf', '2025-12-12', 'uploads/visites_medicales/67b3771a7eca3_motivation letter.pdf', '2030-12-12', 'uploads/photos/67b3771a7ef3a_Postgresql_elephant.svg.png'),
(10, 'Voluptas vero est in', 'uploads/cartes_identite/67b37ea7cc6b8_motivation letter.pdf', '2001-05-25', 'chauffeurs', 'marier', 'Debitis doloribus eu', 'Suscipit enim proide', 'uploads/contrat/67b37ea7cc862_motivation letter.pdf', '2010-08-30', '2001-01-22', 'uploads/permis/67b37ea7cc9e7_motivation letter.pdf', '2001-05-14', 'uploads/visites_medicales/67b37ea7ccb04_motivation letter.pdf', '2023-05-09', 'uploads/photos/67b37ea7ccbf7_Atlas.png'),
(11, 'Duis et qui enim at ', 'uploads/cartes_identite/67b37ec399856_ayoub-fetti.pdf', '2025-12-12', 'menage', 'celibataire', 'Optio quae officia ', 'Itaque non ut accusa', 'uploads/contrat/67b37ec399b80_ayoub-fetti.pdf', '2025-12-12', '2025-12-12', 'uploads/permis/67b37ec399e97_ayoub-fetti.pdf', '2025-12-12', 'uploads/visites_medicales/67b37ec39a10f_ayoub-fetti.pdf', '2012-12-12', 'uploads/photos/67b37ec39a4da_Atlas (1).png');

-- --------------------------------------------------------

--
-- Table structure for table `utilisateur`
--

CREATE TABLE `utilisateur` (
  `id` int NOT NULL,
  `full_name` varchar(255) NOT NULL,
  `email` varchar(255) NOT NULL,
  `password` varchar(255) NOT NULL,
  `role` enum('admin','fonctionnaire','admin_principale') CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Dumping data for table `utilisateur`
--

INSERT INTO `utilisateur` (`id`, `full_name`, `email`, `password`, `role`) VALUES
(2, 'admin', 'admin@admin.com', '123', 'admin_principale'),
(33, 'ayoub', 'ayoub@gmail.com', '123', 'fonctionnaire'),
(34, 'test', 'test@gmail.com', '123', 'fonctionnaire');

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
  MODIFY `id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=17;

--
-- AUTO_INCREMENT for table `personnel`
--
ALTER TABLE `personnel`
  MODIFY `id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=12;

--
-- AUTO_INCREMENT for table `utilisateur`
--
ALTER TABLE `utilisateur`
  MODIFY `id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=35;

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
