e structure for table accidents
--

CREATE TABLE accidents (
  id int NOT NULL,
  cars_id varchar(50) DEFAULT NULL,
  chauffeurs_id int DEFAULT NULL,
  date_declaration_assurance date NOT NULL,
  procédure enum('normal','forfait','garage a greyer') NOT NULL,
  status_resolution enum('pv','constat','arrangement') NOT NULL,
  commentaire text
) ;

-- --------------------------------------------------------

--
-- Table structure for table cars
--

CREATE TABLE cars (
  matricule varchar(50) NOT NULL,
  marque varchar(100) NOT NULL,
  ville varchar(100) NOT NULL,
  chauffeurs_id int DEFAULT NULL,
  carte_grise varchar(50) NOT NULL,
  date_expiration_carte_grise date NOT NULL,
  visite_technique varchar(50) NOT NULL,
  date_expiration_visite date NOT NULL,
  assurance varchar(50) NOT NULL,
  date_expiration_assurance date NOT NULL,
  vignette varchar(50) NOT NULL,
  date_expiration_vignette date NOT NULL,
  feuille_circulation varchar(50) NOT NULL,
  date_expiration_circulation date NOT NULL,
  feuille_extincteur varchar(50) NOT NULL,
  date_expiration_extincteur date NOT NULL,
  feuille_tachygraphe varchar(50) NOT NULL,
  date_expiration_tachygraphe date NOT NULL,
  status enum('en panne','en service','en maintenance') NOT NULL
) ;

--
-- Dumping data for table cars
--

INSERT INTO cars (matricule, marque, ville, chauffeurs_id, carte_grise, date_expiration_carte_grise, visite_technique, date_expiration_visite, assurance, date_expiration_assurance, vignette, date_expiration_vignette, feuille_circulation, date_expiration_circulation, feuille_extincteur, date_expiration_extincteur, feuille_tachygraphe, date_expiration_tachygraphe, status) VALUES
('test', 'test', 'test', NULL, 'test', '2025-01-15', 'test', '2025-01-14', 'test', '2025-01-15', 'test', '2025-01-21', 'test', '2025-01-23', 'test', '2025-01-16', 'test', '2025-01-16', 'en panne');

-- --------------------------------------------------------

--
-- Table structure for table personnel
--

CREATE TABLE personnel (
  id int NOT NULL,
  nom_complet varchar(255) NOT NULL,
  carte_identite varchar(50) NOT NULL,
  date_expiration_carte date NOT NULL,
  role enum('fonctionnaire','chauffeurs','chef de zone','chef de site','menage','securite') NOT NULL,
  situation_familiale enum('celibataire','marier') NOT NULL,
  ville varchar(100) NOT NULL,
  adresse varchar(255) DEFAULT NULL,
  type_contrat varchar(100) DEFAULT NULL,
  date_embauche date NOT NULL,
  date_demission date DEFAULT NULL,
  permit_conduire varchar(50) DEFAULT NULL,
  date_expiration_permit date DEFAULT NULL,
  visite_medicale varchar(50) DEFAULT NULL,
  date_expiration_visite date DEFAULT NULL,
  photo varchar(255) DEFAULT NULL
) ;

--
-- Dumping data for table personnel
--

INSERT INTO personnel (id, nom_complet, carte_identite, date_expiration_carte, role, situation_familiale, ville, adresse, type_contrat, date_embauche, date_demission, permit_conduire, date_expiration_permit, visite_medicale, date_expiration_visite, photo) VALUES
(1, 'ayoub fetti', 'd764689', '2029-02-06', 'fonctionnaire', 'celibataire', 'meknes', NULL, NULL, '2025-01-28', NULL, NULL, NULL, NULL, NULL, NULL);

-- --------------------------------------------------------

--
-- Table structure for table utilisateur
--

CREATE TABLE utilisateur (
  id int NOT NULL,
  full_name varchar(255) NOT NULL,
  email varchar(255) NOT NULL,
  password varchar(255) NOT NULL,
  role enum('admin','fonctionnaire') NOT NULL
) ;

--
-- Dumping data for table utilisateur
--

INSERT INTO utilisateur (id, full_name, email, password, role) VALUES
(2, 'admin', 'admin@admin', '123', 'admin'),
(3, 'ayoub', 'ayoub@gmail.com', '123123', 'fonctionnaire');

--
-- Indexes for dumped tables
--

--
-- Indexes for table accidents
--
ALTER TABLE accidents
  ADD PRIMARY KEY (id),
  ADD KEY chauffeurs_id (chauffeurs_id),
  ADD KEY cars_id (cars_id);

--
-- Indexes for table cars
--
ALTER TABLE cars
  ADD PRIMARY KEY (matricule),
  ADD KEY chauffeurs_id (chauffeurs_id);

--
-- Indexes for table personnel
--
ALTER TABLE personnel
  ADD PRIMARY KEY (id);

--
-- Indexes for table utilisateur
--
ALTER TABLE utilisateur
  ADD PRIMARY KEY (id);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table accidents
--
ALTER TABLE accidents
  MODIFY id int NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table personnel
--
ALTER TABLE personnel
  MODIFY id int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT for table utilisateur
--
ALTER TABLE utilisateur
  MODIFY id int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- Constraints for dumped tables
--

--
-- Constraints for table accidents
--
ALTER TABLE accidents
  ADD CONSTRAINT accidents_ibfk_1 FOREIGN KEY (chauffeurs_id) REFERENCES personnel (id),
  ADD CONSTRAINT accidents_ibfk_2 FOREIGN KEY (cars_id) REFERENCES cars (matricule);

--
-- Constraints for table cars
--
ALTER TABLE cars
  ADD CONSTRAINT cars_ibfk_1 FOREIGN KEY (chauffeurs_id) REFERENCES personnel (id);
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
