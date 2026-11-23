/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!50503 SET NAMES utf8mb4 */;
/*!40103 SET @OLD_TIME_ZONE=@@TIME_ZONE */;
/*!40103 SET TIME_ZONE='+00:00' */;
/*!40014 SET @OLD_UNIQUE_CHECKS=@@UNIQUE_CHECKS, UNIQUE_CHECKS=0 */;
/*!40014 SET @OLD_FOREIGN_KEY_CHECKS=@@FOREIGN_KEY_CHECKS, FOREIGN_KEY_CHECKS=0 */;
/*!40101 SET @OLD_SQL_MODE=@@SQL_MODE, SQL_MODE='NO_AUTO_VALUE_ON_ZERO' */;
/*!40111 SET @OLD_SQL_NOTES=@@SQL_NOTES, SQL_NOTES=0 */;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `abonnements` (
  `id_abonnement` int NOT NULL AUTO_INCREMENT,
  `nom_abonnement` varchar(60) NOT NULL,
  `description_abonnement` varchar(10000) NOT NULL,
  `prix_abonnement` decimal(10,2) NOT NULL,
  `inactive_abonnement` tinyint NOT NULL DEFAULT '0',
  PRIMARY KEY (`id_abonnement`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;
/*!40101 SET character_set_client = @saved_cs_client */;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `activities` (
  `id_activities` int NOT NULL AUTO_INCREMENT,
  `id_typeActivities` int DEFAULT NULL,
  PRIMARY KEY (`id_activities`),
  KEY `fk_activities_typeActivities` (`id_typeActivities`),
  CONSTRAINT `fk_activities_typeActivities` FOREIGN KEY (`id_typeActivities`) REFERENCES `typeactivities` (`id_typeActivities`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;
/*!40101 SET character_set_client = @saved_cs_client */;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `box` (
  `id_box` int NOT NULL AUTO_INCREMENT,
  `name_box` varchar(45) NOT NULL,
  `streetNameAddress_box` varchar(30) NOT NULL,
  `numeroAddress_box` varchar(5) NOT NULL,
  `postBoxAddress_box` varchar(60) NOT NULL,
  `postalCode_box` varchar(5) NOT NULL,
  `town_box` varchar(60) NOT NULL,
  `telephone_box` varchar(45) NOT NULL,
  `numberPeople_box` varchar(5) NOT NULL,
  `actif_box` tinyint NOT NULL DEFAULT 1,
  PRIMARY KEY (`id_box`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;
/*!40101 SET character_set_client = @saved_cs_client */;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `boxuser` (
  `id_boxuser` int NOT NULL AUTO_INCREMENT,
  `idBox_boxuser` int DEFAULT NULL,
  `idUser_boxuser` int unsigned DEFAULT NULL,
  `idAbonnement_boxuser` int DEFAULT NULL,
  `datePayment_boxuser` date NOT NULL,
  `subscriptionBegin_boxuser` date NOT NULL,
  `subscriptionEnd_boxuser` date NOT NULL,
  `idPayment_boxuser` varchar(255) DEFAULT NULL,
  PRIMARY KEY (`id_boxuser`),
  UNIQUE KEY `uk_boxuser_payment` (`idPayment_boxuser`),
  KEY `fk_boxuser_box` (`idBox_boxuser`),
  KEY `fk_boxuser_user` (`idUser_boxuser`),
  KEY `fk_boxuser_abonnement` (`idAbonnement_boxuser`),
  CONSTRAINT `fk_boxuser_abonnement` FOREIGN KEY (`idAbonnement_boxuser`) REFERENCES `abonnements` (`id_abonnement`),
  CONSTRAINT `fk_boxuser_box` FOREIGN KEY (`idBox_boxuser`) REFERENCES `box` (`id_box`),
  CONSTRAINT `fk_boxuser_user` FOREIGN KEY (`idUser_boxuser`) REFERENCES `user` (`id_user`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;
/*!40101 SET character_set_client = @saved_cs_client */;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `coach` (
  `id_coach` int NOT NULL AUTO_INCREMENT,
  `id_user` int unsigned DEFAULT NULL,
  `actualLevel_coach` varchar(45) NOT NULL,
  `actif_coach` tinyint NOT NULL,
  `salary_coach` decimal(10,2) DEFAULT '1200.05',
  `specialisation_coach` varchar(1000) NOT NULL,
  PRIMARY KEY (`id_coach`),
  KEY `fk_coach_user` (`id_user`),
  CONSTRAINT `fk_coach_user` FOREIGN KEY (`id_user`) REFERENCES `user` (`id_user`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;
/*!40101 SET character_set_client = @saved_cs_client */;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `coachhistory` (
  `id_coachHistory` int NOT NULL AUTO_INCREMENT,
  `id_coach` int DEFAULT NULL,
  `levelNumber_history` varchar(45) NOT NULL,
  `date_history` date NOT NULL,
  `place_history` varchar(45) NOT NULL,
  `salary_history` decimal(10,2) NOT NULL,
  PRIMARY KEY (`id_coachHistory`),
  KEY `fk_coachHistory_coach` (`id_coach`),
  CONSTRAINT `fk_coachHistory_coach` FOREIGN KEY (`id_coach`) REFERENCES `coach` (`id_coach`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;
/*!40101 SET character_set_client = @saved_cs_client */;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `discovery` (
  `id_discovery` bigint unsigned NOT NULL AUTO_INCREMENT,
  `idOrganisation_discovery` int NOT NULL,
  `nom_discovery` varchar(60) NOT NULL,
  `prenom_discovery` varchar(60) NOT NULL,
  `email_discovery` varchar(60) NOT NULL,
  `telephone_discovery` varchar(60) NOT NULL,
  `note_discovery` varchar(10000) DEFAULT NULL,
  `adresseIP_discovery` varchar(45) NOT NULL,
  `webBrowser_discovery` varchar(50) NOT NULL,
  PRIMARY KEY (`id_discovery`),
  KEY `fk_discovery_organisation` (`idOrganisation_discovery`),
  CONSTRAINT `fk_discovery_organisation` FOREIGN KEY (`idOrganisation_discovery`) REFERENCES `organisation` (`id_organisation`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;
/*!40101 SET character_set_client = @saved_cs_client */;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `organisation` (
  `id_organisation` int NOT NULL AUTO_INCREMENT,
  `id_coach` int DEFAULT NULL,
  `id_owner` int DEFAULT NULL,
  `id_activities` int DEFAULT NULL,
  `id_box` int DEFAULT NULL,
  `begin_organisation` datetime NOT NULL,
  `end_organisation` datetime NOT NULL,
  `feedbackCoachOnOrganisation_organisation` varchar(1000) DEFAULT NULL,
  PRIMARY KEY (`id_organisation`),
  KEY `fk_organisation_coach` (`id_coach`),
  KEY `fk_organisation_owner` (`id_owner`),
  KEY `fk_organisation_activities` (`id_activities`),
  KEY `fk_organisation_box` (`id_box`),
  CONSTRAINT `fk_organisation_activities` FOREIGN KEY (`id_activities`) REFERENCES `activities` (`id_activities`),
  CONSTRAINT `fk_organisation_box` FOREIGN KEY (`id_box`) REFERENCES `box` (`id_box`),
  CONSTRAINT `fk_organisation_coach` FOREIGN KEY (`id_coach`) REFERENCES `coach` (`id_coach`),
  CONSTRAINT `fk_organisation_owner` FOREIGN KEY (`id_owner`) REFERENCES `owner` (`id_owner`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;
/*!40101 SET character_set_client = @saved_cs_client */;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `organisationuser` (
  `id_organisation` int NOT NULL,
  `id_user` int unsigned NOT NULL,
  `performance_organisationuser` varchar(1000) NOT NULL,
  `date_organisationuser` date NOT NULL,
  `feedbackUserOnCoach_organisationuser` varchar(1000) DEFAULT NULL,
  `feedbackUserOnOrganisation_organisationuser` varchar(1000) DEFAULT NULL,
  `feedbackCoachOnUser_organisationuser` varchar(1000) DEFAULT NULL,
  PRIMARY KEY (`id_organisation`,`id_user`),
  KEY `fk_orguser_user` (`id_user`),
  CONSTRAINT `fk_orguser_organisation` FOREIGN KEY (`id_organisation`) REFERENCES `organisation` (`id_organisation`),
  CONSTRAINT `fk_orguser_user` FOREIGN KEY (`id_user`) REFERENCES `user` (`id_user`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;
/*!40101 SET character_set_client = @saved_cs_client */;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `owner` (
  `id_owner` int NOT NULL AUTO_INCREMENT,
  `id_user` int unsigned DEFAULT NULL,
  `email_owner` varchar(45) NOT NULL,
  `telephone_owner` varchar(45) NOT NULL,
  `salary_owner` decimal(10,2) DEFAULT '1200.05',
  PRIMARY KEY (`id_owner`),
  KEY `fk_owner_user` (`id_user`),
  CONSTRAINT `fk_owner_user` FOREIGN KEY (`id_user`) REFERENCES `user` (`id_user`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;
/*!40101 SET character_set_client = @saved_cs_client */;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `ownerbox` (
  `id_box` int NOT NULL,
  `id_owner` int NOT NULL,
  `dateBegin_ownerbox` date NOT NULL,
  `dateEnd_ownerbox` date NOT NULL,
  PRIMARY KEY (`id_box`,`id_owner`),
  KEY `fk_boxowner_owner` (`id_owner`),
  CONSTRAINT `fk_boxowner_box` FOREIGN KEY (`id_box`) REFERENCES `box` (`id_box`),
  CONSTRAINT `fk_boxowner_owner` FOREIGN KEY (`id_owner`) REFERENCES `owner` (`id_owner`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;
/*!40101 SET character_set_client = @saved_cs_client */;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `session` (
  `id_session` bigint unsigned NOT NULL AUTO_INCREMENT,
  `idUser_session` int unsigned NOT NULL,
  `tokenHash_session` char(64) NOT NULL,
  `ipAddress_session` varchar(45) DEFAULT NULL,
  `userAgent_session` varchar(255) DEFAULT NULL,
  `createdAt_session` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  `expiresAt_session` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id_session`),
  UNIQUE KEY `tokenHash_session` (`tokenHash_session`),
  KEY `fk_session_user` (`idUser_session`),
  CONSTRAINT `fk_session_user` FOREIGN KEY (`idUser_session`) REFERENCES `user` (`id_user`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;
/*!40101 SET character_set_client = @saved_cs_client */;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `typeactivities` (
  `id_typeActivities` int NOT NULL AUTO_INCREMENT,
  `name_typeActivity` varchar(100) NOT NULL,
  `description_typeActivity` varchar(1000) NOT NULL,
  `color_typeActivity` tinyint NOT NULL,
  `allowGuest_typeActivity` tinyint NOT NULL DEFAULT '0',
  PRIMARY KEY (`id_typeActivities`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;
/*!40101 SET character_set_client = @saved_cs_client */;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `user` (
  `id_user` int unsigned NOT NULL AUTO_INCREMENT,
  `lastName_user` varchar(60) NOT NULL,
  `firstName_user` varchar(60) NOT NULL,
  `birthdate_user` date NOT NULL,
  `gender_user` varchar(30) NOT NULL,
  `poids_user` decimal(5,2) DEFAULT NULL,
  `taille_user` int DEFAULT NULL,
  `streetNameAddress_user` varchar(30) NOT NULL,
  `numeroAddress_user` varchar(5) NOT NULL,
  `postBoxAddress_user` varchar(60) DEFAULT NULL,
  `postalCode_user` varchar(5) NOT NULL,
  `town_user` varchar(60) NOT NULL,
  `telephone_user` varchar(20) DEFAULT NULL,
  `firstContactTelephone_user` varchar(20) DEFAULT NULL,
  `secondContactTelephone_user` varchar(20) DEFAULT NULL,
  `emailAddress_user` varchar(60) NOT NULL,
  `password_user` varchar(60) DEFAULT NULL,
  `tokenInvitation_user` varchar(255) DEFAULT NULL,
  `dateInvitation_user` date NOT NULL,
  `dateCreation_user` date NOT NULL,
  `wound_user` varchar(500) NOT NULL,
  `actualLevel_user` varchar(45) NOT NULL,
  `needDisabled` tinyint NOT NULL DEFAULT '0',
  `notesCoach_user` varchar(10000) NOT NULL,
  `avatar_user` varchar(100) DEFAULT NULL,
  `anonymisation_user` tinyint(1) DEFAULT '0',
  `resetPasswordToken` varchar(255) DEFAULT NULL,
  `resetPasswordExpiration` datetime DEFAULT NULL,
  PRIMARY KEY (`id_user`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;
/*!40101 SET character_set_client = @saved_cs_client */;
/*!40103 SET TIME_ZONE=@OLD_TIME_ZONE */;

/*!40101 SET SQL_MODE=@OLD_SQL_MODE */;
/*!40014 SET FOREIGN_KEY_CHECKS=@OLD_FOREIGN_KEY_CHECKS */;
/*!40014 SET UNIQUE_CHECKS=@OLD_UNIQUE_CHECKS */;
/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
/*!40111 SET SQL_NOTES=@OLD_SQL_NOTES */;

