/*M!999999\- enable the sandbox mode */ 
-- MariaDB dump 10.19-11.8.6-MariaDB, for debian-linux-gnu (x86_64)
--
-- Host: localhost    Database: iaradia
-- ------------------------------------------------------
-- Server version	11.8.6-MariaDB-5 from Debian

/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;
/*!40103 SET @OLD_TIME_ZONE=@@TIME_ZONE */;
/*!40103 SET TIME_ZONE='+00:00' */;
/*!40014 SET @OLD_UNIQUE_CHECKS=@@UNIQUE_CHECKS, UNIQUE_CHECKS=0 */;
/*!40014 SET @OLD_FOREIGN_KEY_CHECKS=@@FOREIGN_KEY_CHECKS, FOREIGN_KEY_CHECKS=0 */;
/*!40101 SET @OLD_SQL_MODE=@@SQL_MODE, SQL_MODE='NO_AUTO_VALUE_ON_ZERO' */;
/*M!100616 SET @OLD_NOTE_VERBOSITY=@@NOTE_VERBOSITY, NOTE_VERBOSITY=0 */;

--
-- Table structure for table `Agent`
--

DROP TABLE IF EXISTS `Agent`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `Agent` (
  `id_Agent` bigint(20) NOT NULL AUTO_INCREMENT,
  `id_User` bigint(20) DEFAULT NULL,
  `id_Cooperative` bigint(20) DEFAULT NULL,
  PRIMARY KEY (`id_Agent`),
  KEY `FK_Agent_User` (`id_User`),
  KEY `FK_Agent_Cooperative` (`id_Cooperative`),
  CONSTRAINT `FK_Agent_Cooperative` FOREIGN KEY (`id_Cooperative`) REFERENCES `Cooperative` (`id_Cooperative`),
  CONSTRAINT `FK_Agent_User` FOREIGN KEY (`id_User`) REFERENCES `User` (`id_User`)
) ENGINE=InnoDB AUTO_INCREMENT=3 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_uca1400_ai_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `Chauffeur`
--

DROP TABLE IF EXISTS `Chauffeur`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `Chauffeur` (
  `id_Chauffeur` bigint(20) NOT NULL AUTO_INCREMENT,
  `num_permi_Chauffeur` varchar(50) DEFAULT NULL,
  `id_User` bigint(20) DEFAULT NULL,
  `id_Cooperative` bigint(20) DEFAULT NULL,
  PRIMARY KEY (`id_Chauffeur`),
  UNIQUE KEY `num_permi_Chauffeur` (`num_permi_Chauffeur`),
  KEY `FK_Chauffeur_User` (`id_User`),
  KEY `FK_Chauffeur_Cooperative` (`id_Cooperative`),
  CONSTRAINT `FK_Chauffeur_Cooperative` FOREIGN KEY (`id_Cooperative`) REFERENCES `Cooperative` (`id_Cooperative`),
  CONSTRAINT `FK_Chauffeur_User` FOREIGN KEY (`id_User`) REFERENCES `User` (`id_User`)
) ENGINE=InnoDB AUTO_INCREMENT=3 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_uca1400_ai_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `Conduire`
--

DROP TABLE IF EXISTS `Conduire`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `Conduire` (
  `id_Chauffeur` bigint(20) NOT NULL,
  `id_Voyage` bigint(20) NOT NULL,
  PRIMARY KEY (`id_Chauffeur`,`id_Voyage`),
  KEY `FK_Conduire_Voyage` (`id_Voyage`),
  CONSTRAINT `FK_Conduire_Chauffeur` FOREIGN KEY (`id_Chauffeur`) REFERENCES `Chauffeur` (`id_Chauffeur`),
  CONSTRAINT `FK_Conduire_Voyage` FOREIGN KEY (`id_Voyage`) REFERENCES `Voyage` (`id_Voyage`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_uca1400_ai_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `Contact`
--

DROP TABLE IF EXISTS `Contact`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `Contact` (
  `id_Contact` bigint(20) NOT NULL AUTO_INCREMENT,
  `telephone_Contact` varchar(20) DEFAULT NULL,
  `id_User` bigint(20) DEFAULT NULL,
  PRIMARY KEY (`id_Contact`),
  KEY `FK_Contact_User` (`id_User`),
  CONSTRAINT `FK_Contact_User` FOREIGN KEY (`id_User`) REFERENCES `User` (`id_User`)
) ENGINE=InnoDB AUTO_INCREMENT=8 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_uca1400_ai_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `Cooperative`
--

DROP TABLE IF EXISTS `Cooperative`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `Cooperative` (
  `id_Cooperative` bigint(20) NOT NULL AUTO_INCREMENT,
  `nom_Cooperative` varchar(255) DEFAULT NULL,
  `logo_Cooperative` varchar(255) DEFAULT NULL,
  PRIMARY KEY (`id_Cooperative`)
) ENGINE=InnoDB AUTO_INCREMENT=4 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_uca1400_ai_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `Paiment`
--

DROP TABLE IF EXISTS `Paiment`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `Paiment` (
  `reference_Paiment` varchar(100) NOT NULL,
  `montant_Paiment` decimal(10,2) DEFAULT NULL,
  `mode_de_paiment_Paiment` enum('MVola','Airtel_Money','carte','especes') DEFAULT NULL,
  `date_heure_Paiment` datetime DEFAULT NULL,
  `statut_Paiment` enum('en_attente','paye','rembourse') DEFAULT NULL,
  `id_Reservation` bigint(20) DEFAULT NULL,
  PRIMARY KEY (`reference_Paiment`),
  UNIQUE KEY `id_Reservation` (`id_Reservation`),
  CONSTRAINT `FK_Paiment_Reservation` FOREIGN KEY (`id_Reservation`) REFERENCES `Reservation` (`id_Reservation`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_uca1400_ai_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `Rapport`
--

DROP TABLE IF EXISTS `Rapport`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `Rapport` (
  `id_Rapport` bigint(20) NOT NULL AUTO_INCREMENT,
  `meteo_Rapport` varchar(255) DEFAULT NULL,
  `etat_route_Rapport` enum('bon','degrade','dangereux') DEFAULT NULL,
  `commentaire_Rapport` text DEFAULT NULL,
  `heure_Rapport` datetime DEFAULT NULL,
  `id_Voyage` bigint(20) DEFAULT NULL,
  `id_Ville_actuelle` bigint(20) DEFAULT NULL,
  PRIMARY KEY (`id_Rapport`),
  KEY `FK_Rapport_Voyage` (`id_Voyage`),
  KEY `FK_Rapport_Ville` (`id_Ville_actuelle`),
  CONSTRAINT `FK_Rapport_Ville` FOREIGN KEY (`id_Ville_actuelle`) REFERENCES `Ville` (`id_Ville`),
  CONSTRAINT `FK_Rapport_Voyage` FOREIGN KEY (`id_Voyage`) REFERENCES `Voyage` (`id_Voyage`)
) ENGINE=InnoDB AUTO_INCREMENT=3 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_uca1400_ai_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `Reservation`
--

DROP TABLE IF EXISTS `Reservation`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `Reservation` (
  `id_Reservation` bigint(20) NOT NULL AUTO_INCREMENT,
  `date_reservation_Reservation` date DEFAULT NULL,
  `total_prix_Reservation` decimal(10,2) DEFAULT NULL,
  `statut_Reservation` enum('en_attente','confirme','annule') DEFAULT NULL,
  `QR_code_Reservation` varchar(255) DEFAULT NULL,
  `id_Tarif_segment` bigint(20) DEFAULT NULL,
  `id_User` bigint(20) DEFAULT NULL,
  PRIMARY KEY (`id_Reservation`),
  UNIQUE KEY `QR_code_Reservation` (`QR_code_Reservation`),
  KEY `FK_Reservation_Tarif` (`id_Tarif_segment`),
  KEY `FK_Reservation_User` (`id_User`),
  CONSTRAINT `FK_Reservation_Tarif` FOREIGN KEY (`id_Tarif_segment`) REFERENCES `Tarif_segment` (`id_Tarif_segment`),
  CONSTRAINT `FK_Reservation_User` FOREIGN KEY (`id_User`) REFERENCES `User` (`id_User`)
) ENGINE=InnoDB AUTO_INCREMENT=4 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_uca1400_ai_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `Siege`
--

DROP TABLE IF EXISTS `Siege`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `Siege` (
  `id_Siege` bigint(20) NOT NULL AUTO_INCREMENT,
  `statut_Siege` enum('libre','verrouille','reserve') DEFAULT NULL,
  `expirer_dans_Siege` datetime DEFAULT NULL,
  `id_Voyage` bigint(20) DEFAULT NULL,
  `id_Reservation` bigint(20) DEFAULT NULL,
  `numero` int(11) DEFAULT NULL,
  PRIMARY KEY (`id_Siege`),
  UNIQUE KEY `UQ_siege_voyage` (`id_Voyage`,`id_Siege`),
  KEY `FK_Siege_Reservation` (`id_Reservation`),
  CONSTRAINT `FK_Siege_Reservation` FOREIGN KEY (`id_Reservation`) REFERENCES `Reservation` (`id_Reservation`),
  CONSTRAINT `FK_Siege_Voyage` FOREIGN KEY (`id_Voyage`) REFERENCES `Voyage` (`id_Voyage`)
) ENGINE=InnoDB AUTO_INCREMENT=4 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_uca1400_ai_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `Tarif_segment`
--

DROP TABLE IF EXISTS `Tarif_segment`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `Tarif_segment` (
  `id_Tarif_segment` bigint(20) NOT NULL AUTO_INCREMENT,
  `prix_Tarif_segment` decimal(10,2) DEFAULT NULL,
  `id_Trajet` bigint(20) DEFAULT NULL,
  `id_Ville_depart` bigint(20) DEFAULT NULL,
  `id_Ville_arrivee` bigint(20) DEFAULT NULL,
  PRIMARY KEY (`id_Tarif_segment`),
  UNIQUE KEY `UQ_segment` (`id_Trajet`,`id_Ville_depart`,`id_Ville_arrivee`),
  KEY `FK_Tarif_Ville_depart` (`id_Ville_depart`),
  KEY `FK_Tarif_Ville_arrivee` (`id_Ville_arrivee`),
  CONSTRAINT `FK_Tarif_Trajet` FOREIGN KEY (`id_Trajet`) REFERENCES `Trajet` (`id_Trajet`),
  CONSTRAINT `FK_Tarif_Ville_arrivee` FOREIGN KEY (`id_Ville_arrivee`) REFERENCES `Ville` (`id_Ville`),
  CONSTRAINT `FK_Tarif_Ville_depart` FOREIGN KEY (`id_Ville_depart`) REFERENCES `Ville` (`id_Ville`)
) ENGINE=InnoDB AUTO_INCREMENT=4 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_uca1400_ai_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `Trajet`
--

DROP TABLE IF EXISTS `Trajet`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `Trajet` (
  `id_Trajet` bigint(20) NOT NULL AUTO_INCREMENT,
  `distance_km_Trajet` decimal(8,2) DEFAULT NULL,
  `id_Ville_depart` bigint(20) DEFAULT NULL,
  `id_Ville_arrivee` bigint(20) DEFAULT NULL,
  PRIMARY KEY (`id_Trajet`),
  KEY `FK_Trajet_Ville_depart` (`id_Ville_depart`),
  KEY `FK_Trajet_Ville_arrivee` (`id_Ville_arrivee`),
  CONSTRAINT `FK_Trajet_Ville_arrivee` FOREIGN KEY (`id_Ville_arrivee`) REFERENCES `Ville` (`id_Ville`),
  CONSTRAINT `FK_Trajet_Ville_depart` FOREIGN KEY (`id_Ville_depart`) REFERENCES `Ville` (`id_Ville`),
  CONSTRAINT `CONSTRAINT_1` CHECK (`id_Ville_depart` <> `id_Ville_arrivee`)
) ENGINE=InnoDB AUTO_INCREMENT=5 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_uca1400_ai_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `User`
--

DROP TABLE IF EXISTS `User`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `User` (
  `id_User` bigint(20) NOT NULL AUTO_INCREMENT,
  `nom_User` varchar(100) DEFAULT NULL,
  `prenom_User` varchar(100) DEFAULT NULL,
  `email_User` varchar(255) DEFAULT NULL,
  `mdp_User` varchar(255) DEFAULT NULL,
  `role_User` enum('admin','agent','chauffeur','client') DEFAULT NULL,
  PRIMARY KEY (`id_User`),
  UNIQUE KEY `email_User` (`email_User`)
) ENGINE=InnoDB AUTO_INCREMENT=12 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_uca1400_ai_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `Vehicule`
--

DROP TABLE IF EXISTS `Vehicule`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `Vehicule` (
  `immatriculation_Vehicule` varchar(50) NOT NULL,
  `modele_Vehicule` varchar(100) DEFAULT NULL,
  `capacite_Vehicule` tinyint(4) DEFAULT NULL,
  `id_Cooperative` bigint(20) DEFAULT NULL,
  PRIMARY KEY (`immatriculation_Vehicule`),
  KEY `FK_Vehicule_Cooperative` (`id_Cooperative`),
  CONSTRAINT `FK_Vehicule_Cooperative` FOREIGN KEY (`id_Cooperative`) REFERENCES `Cooperative` (`id_Cooperative`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_uca1400_ai_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `Ville`
--
CREATE TABLE Composition_Trajet (
    `id_parent` bigint(20) NOT NULL, -- ex: id de "Tana-Toliara"
    `id_enfant` bigint(20) NOT NULL, -- ex: id de "Antsirabe-Fianara"
    `ordre_segment` int(11) NOT NULL,
    PRIMARY KEY (`id_parent`, `id_enfant`),
    FOREIGN KEY (`id_parent`) REFERENCES Trajet(`id_trajet`),
    FOREIGN KEY (`id_enfant`) REFERENCES Trajet(`id_trajet`)
);
DROP TABLE IF EXISTS `Ville`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `Ville` (
  `id_Ville` bigint(20) NOT NULL AUTO_INCREMENT,
  `nom_Ville` varchar(100) DEFAULT NULL,
  `region_Ville` varchar(100) DEFAULT NULL,
  PRIMARY KEY (`id_Ville`)
) ENGINE=InnoDB AUTO_INCREMENT=6 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_uca1400_ai_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `Voyage`
--

DROP TABLE IF EXISTS `Voyage`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `Voyage` (
  `id_Voyage` bigint(20) NOT NULL AUTO_INCREMENT,
  `date_depart_Voyage` datetime DEFAULT NULL,
  `status_Voyage` enum('planifie','en_cours','termine','annule') DEFAULT NULL,
  `id_Trajet` bigint(20) DEFAULT NULL,
  `immatriculation_Vehicule` varchar(50) DEFAULT NULL,
  PRIMARY KEY (`id_Voyage`),
  KEY `FK_Voyage_Trajet` (`id_Trajet`),
  KEY `FK_Voyage_Vehicule` (`immatriculation_Vehicule`),
  CONSTRAINT `FK_Voyage_Trajet` FOREIGN KEY (`id_Trajet`) REFERENCES `Trajet` (`id_Trajet`),
  CONSTRAINT `FK_Voyage_Vehicule` FOREIGN KEY (`immatriculation_Vehicule`) REFERENCES `Vehicule` (`immatriculation_Vehicule`)
) ENGINE=InnoDB AUTO_INCREMENT=5 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_uca1400_ai_ci;
/*!40101 SET character_set_client = @saved_cs_client */;
/*!40103 SET TIME_ZONE=@OLD_TIME_ZONE */;

/*!40101 SET SQL_MODE=@OLD_SQL_MODE */;
/*!40014 SET FOREIGN_KEY_CHECKS=@OLD_FOREIGN_KEY_CHECKS */;
/*!40014 SET UNIQUE_CHECKS=@OLD_UNIQUE_CHECKS */;
/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
/*M!100616 SET NOTE_VERBOSITY=@OLD_NOTE_VERBOSITY */;

-- Dump completed on 2026-05-06 19:54:38
