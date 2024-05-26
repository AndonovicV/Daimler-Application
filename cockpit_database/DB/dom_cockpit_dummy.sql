-- MariaDB dump 10.19  Distrib 10.4.32-MariaDB, for Win64 (AMD64)
--
-- Host: localhost    Database: dom_cockpit_dummy
-- ------------------------------------------------------
-- Server version	10.4.32-MariaDB

/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;
/*!40103 SET @OLD_TIME_ZONE=@@TIME_ZONE */;
/*!40103 SET TIME_ZONE='+00:00' */;
/*!40014 SET @OLD_UNIQUE_CHECKS=@@UNIQUE_CHECKS, UNIQUE_CHECKS=0 */;
/*!40014 SET @OLD_FOREIGN_KEY_CHECKS=@@FOREIGN_KEY_CHECKS, FOREIGN_KEY_CHECKS=0 */;
/*!40101 SET @OLD_SQL_MODE=@@SQL_MODE, SQL_MODE='NO_AUTO_VALUE_ON_ZERO' */;
/*!40111 SET @OLD_SQL_NOTES=@@SQL_NOTES, SQL_NOTES=0 */;

--
-- Table structure for table `org_boards`
--

DROP TABLE IF EXISTS `org_boards`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `org_boards` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(100) NOT NULL,
  `product_type` varchar(50) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `org_boards`
--

LOCK TABLES `org_boards` WRITE;
/*!40000 ALTER TABLE `org_boards` DISABLE KEYS */;
/*!40000 ALTER TABLE `org_boards` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `org_gfts`
--

DROP TABLE IF EXISTS `org_gfts`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `org_gfts` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(100) NOT NULL,
  `moduleteam` varchar(100) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `org_gfts`
--

LOCK TABLES `org_gfts` WRITE;
/*!40000 ALTER TABLE `org_gfts` DISABLE KEYS */;
/*!40000 ALTER TABLE `org_gfts` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `org_line_functions`
--

DROP TABLE IF EXISTS `org_line_functions`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `org_line_functions` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `function` varchar(100) NOT NULL,
  `ordering` int(11) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `org_line_functions`
--

LOCK TABLES `org_line_functions` WRITE;
/*!40000 ALTER TABLE `org_line_functions` DISABLE KEYS */;
/*!40000 ALTER TABLE `org_line_functions` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `org_members_vehicle`
--

DROP TABLE IF EXISTS `org_members_vehicle`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `org_members_vehicle` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(100) NOT NULL,
  `team` varchar(100) NOT NULL,
  `line_function` varchar(100) NOT NULL,
  `gft` varchar(100) NOT NULL,
  `is_nominated_substitute` tinyint(1) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `org_members_vehicle`
--

LOCK TABLES `org_members_vehicle` WRITE;
/*!40000 ALTER TABLE `org_members_vehicle` DISABLE KEYS */;
/*!40000 ALTER TABLE `org_members_vehicle` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `org_moduleteams`
--

DROP TABLE IF EXISTS `org_moduleteams`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `org_moduleteams` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(100) NOT NULL,
  `product_type` varchar(50) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `org_moduleteams`
--

LOCK TABLES `org_moduleteams` WRITE;
/*!40000 ALTER TABLE `org_moduleteams` DISABLE KEYS */;
/*!40000 ALTER TABLE `org_moduleteams` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `package`
--

DROP TABLE IF EXISTS `package`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `package` (
  `ID` varchar(20) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL,
  `title` varchar(255) CHARACTER SET utf8 COLLATE utf8_general_ci DEFAULT NULL,
  `product_type` varchar(255) CHARACTER SET utf8 COLLATE utf8_general_ci DEFAULT NULL,
  `current_phase` varchar(255) CHARACTER SET utf8 COLLATE utf8_general_ci DEFAULT NULL,
  `fasttrack` varchar(15) CHARACTER SET utf8 COLLATE utf8_general_ci DEFAULT NULL,
  `package_responsible` varchar(255) CHARACTER SET utf8 COLLATE utf8_general_ci DEFAULT 'Bitte eintragen',
  `decision` varchar(255) CHARACTER SET utf8 COLLATE utf8_general_ci DEFAULT NULL,
  `start_of_production` varchar(255) DEFAULT NULL,
  `project` text CHARACTER SET utf8 COLLATE utf8_general_ci DEFAULT NULL,
  `lead_module_team` varchar(255) CHARACTER SET utf8 COLLATE utf8_general_ci DEFAULT 'Bitte eintragen',
  `lead_gft` varchar(255) DEFAULT NULL,
  `date_mt` varchar(255) CHARACTER SET utf8 COLLATE utf8_general_ci DEFAULT NULL,
  `date_cmt` varchar(255) CHARACTER SET utf8 COLLATE utf8_general_ci DEFAULT NULL,
  `information` text CHARACTER SET utf8 COLLATE utf8_general_ci DEFAULT NULL,
  PRIMARY KEY (`ID`),
  UNIQUE KEY `ID_UNIQUE` (`ID`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `package`
--

LOCK TABLES `package` WRITE;
/*!40000 ALTER TABLE `package` DISABLE KEYS */;
/*!40000 ALTER TABLE `package` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `spec_book`
--

DROP TABLE IF EXISTS `spec_book`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `spec_book` (
  `Module_Team` varchar(100) DEFAULT NULL,
  `GFT` varchar(100) DEFAULT NULL,
  `Project` varchar(100) DEFAULT NULL,
  `Component` varchar(200) NOT NULL,
  `CRS_Signature` date DEFAULT NULL,
  `CRS_Done` varchar(10) DEFAULT NULL,
  `Supplier_Awarding` varchar(100) DEFAULT NULL,
  `CIS_Alignment` date DEFAULT NULL,
  `CIS_Done` varchar(10) DEFAULT NULL,
  `E_Signing_Completed` date DEFAULT NULL,
  `E_Signing_Done` varchar(50) DEFAULT NULL,
  `Archived` varchar(10) DEFAULT NULL,
  `Doors_Next_Module` varchar(2000) DEFAULT NULL,
  `Comment` varchar(2000) DEFAULT NULL,
  PRIMARY KEY (`Component`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `spec_book`
--

LOCK TABLES `spec_book` WRITE;
/*!40000 ALTER TABLE `spec_book` DISABLE KEYS */;
/*!40000 ALTER TABLE `spec_book` ENABLE KEYS */;
UNLOCK TABLES;
/*!40103 SET TIME_ZONE=@OLD_TIME_ZONE */;

/*!40101 SET SQL_MODE=@OLD_SQL_MODE */;
/*!40014 SET FOREIGN_KEY_CHECKS=@OLD_FOREIGN_KEY_CHECKS */;
/*!40014 SET UNIQUE_CHECKS=@OLD_UNIQUE_CHECKS */;
/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
/*!40111 SET SQL_NOTES=@OLD_SQL_NOTES */;

-- Dump completed on 2024-04-18 18:49:54
