-- MySQL dump 10.13  Distrib 8.0.18, for Win64 (x86_64)
--
-- Host: localhost    Database: letseat
-- ------------------------------------------------------
-- Server version	8.0.18

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

--
-- Table structure for table `candidate`
--

DROP TABLE IF EXISTS `candidate`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `candidate` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `poll_id` int(11) DEFAULT NULL,
  `restaurant_id` int(11) DEFAULT NULL,
  `create_date` text COLLATE utf8mb4_general_ci,
  `modified_date` text COLLATE utf8mb4_general_ci,
  `delete_date` text COLLATE utf8mb4_general_ci,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `candidate`
--

LOCK TABLES `candidate` WRITE;
/*!40000 ALTER TABLE `candidate` DISABLE KEYS */;
/*!40000 ALTER TABLE `candidate` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `feast_group`
--

DROP TABLE IF EXISTS `feast_group`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `feast_group` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `name` text COLLATE utf8mb4_general_ci,
  `owner_id` int(11) DEFAULT NULL,
  `create_date` text COLLATE utf8mb4_general_ci,
  `modified_date` text COLLATE utf8mb4_general_ci,
  `delete_date` text COLLATE utf8mb4_general_ci,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `feast_group`
--

LOCK TABLES `feast_group` WRITE;
/*!40000 ALTER TABLE `feast_group` DISABLE KEYS */;
/*!40000 ALTER TABLE `feast_group` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `feast_restaurant_link`
--

DROP TABLE IF EXISTS `feast_restaurant_link`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `feast_restaurant_link` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `feast_group_id` int(11) DEFAULT NULL,
  `restaurant_group_id` int(11) DEFAULT NULL,
  `create_date` text COLLATE utf8mb4_general_ci,
  `modified_date` text COLLATE utf8mb4_general_ci,
  `delete_date` text COLLATE utf8mb4_general_ci,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `feast_restaurant_link`
--

LOCK TABLES `feast_restaurant_link` WRITE;
/*!40000 ALTER TABLE `feast_restaurant_link` DISABLE KEYS */;
/*!40000 ALTER TABLE `feast_restaurant_link` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `feast_user_link`
--

DROP TABLE IF EXISTS `feast_user_link`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `feast_user_link` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `feast_group_id` int(11) DEFAULT NULL,
  `user_id` int(11) DEFAULT NULL,
  `create_date` text COLLATE utf8mb4_general_ci,
  `modified_date` text COLLATE utf8mb4_general_ci,
  `delete_date` text COLLATE utf8mb4_general_ci,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `feast_user_link`
--

LOCK TABLES `feast_user_link` WRITE;
/*!40000 ALTER TABLE `feast_user_link` DISABLE KEYS */;
/*!40000 ALTER TABLE `feast_user_link` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `poll`
--

DROP TABLE IF EXISTS `poll`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `poll` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `name` text COLLATE utf8mb4_general_ci,
  `notes` text COLLATE utf8mb4_general_ci,
  `create_date` text COLLATE utf8mb4_general_ci,
  `end_date` text COLLATE utf8mb4_general_ci,
  `modified_date` text COLLATE utf8mb4_general_ci,
  `delete_date` text COLLATE utf8mb4_general_ci,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `poll`
--

LOCK TABLES `poll` WRITE;
/*!40000 ALTER TABLE `poll` DISABLE KEYS */;
/*!40000 ALTER TABLE `poll` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `restaurant`
--

DROP TABLE IF EXISTS `restaurant`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `restaurant` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `name` text COLLATE utf8mb4_general_ci,
  `location` text COLLATE utf8mb4_general_ci,
  `notes` text COLLATE utf8mb4_general_ci,
  `create_date` text COLLATE utf8mb4_general_ci,
  `modified_date` text COLLATE utf8mb4_general_ci,
  `delete_date` text COLLATE utf8mb4_general_ci,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `restaurant`
--

LOCK TABLES `restaurant` WRITE;
/*!40000 ALTER TABLE `restaurant` DISABLE KEYS */;
/*!40000 ALTER TABLE `restaurant` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `restaurant_group`
--

DROP TABLE IF EXISTS `restaurant_group`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `restaurant_group` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `name` text COLLATE utf8mb4_general_ci,
  `create_date` text COLLATE utf8mb4_general_ci,
  `modified_date` text COLLATE utf8mb4_general_ci,
  `delete_date` text COLLATE utf8mb4_general_ci,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `restaurant_group`
--

LOCK TABLES `restaurant_group` WRITE;
/*!40000 ALTER TABLE `restaurant_group` DISABLE KEYS */;
/*!40000 ALTER TABLE `restaurant_group` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `restaurant_link`
--

DROP TABLE IF EXISTS `restaurant_link`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `restaurant_link` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `restaurant_group_id` int(11) DEFAULT NULL,
  `restaurant_id` int(11) DEFAULT NULL,
  `create_date` text COLLATE utf8mb4_general_ci,
  `modified_date` text COLLATE utf8mb4_general_ci,
  `delete_date` text COLLATE utf8mb4_general_ci,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `restaurant_link`
--

LOCK TABLES `restaurant_link` WRITE;
/*!40000 ALTER TABLE `restaurant_link` DISABLE KEYS */;
/*!40000 ALTER TABLE `restaurant_link` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `user_account`
--

DROP TABLE IF EXISTS `user_account`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `user_account` (
  `user_id` int(11) NOT NULL,
  `authority` int(11) NOT NULL DEFAULT '0',
  `email` text,
  `name` text,
  `notification` int(11) NOT NULL DEFAULT '1',
  `create_date` text,
  `modified_date` text,
  `delete_date` text,
  PRIMARY KEY (`user_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `user_account`
--

LOCK TABLES `user_account` WRITE;
/*!40000 ALTER TABLE `user_account` DISABLE KEYS */;
/*!40000 ALTER TABLE `user_account` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `user_forgot`
--

DROP TABLE IF EXISTS `user_forgot`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `user_forgot` (
  `user_id` int(11) NOT NULL,
  `token` text,
  `create_date` text,
  `expire_date` text,
  `modified_date` text,
  `delete_date` text,
  PRIMARY KEY (`user_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `user_forgot`
--

LOCK TABLES `user_forgot` WRITE;
/*!40000 ALTER TABLE `user_forgot` DISABLE KEYS */;
/*!40000 ALTER TABLE `user_forgot` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `user_pass`
--

DROP TABLE IF EXISTS `user_pass`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `user_pass` (
  `user_id` int(11) NOT NULL,
  `password` text,
  `create_date` text,
  `expire_date` text,
  `modified_date` text,
  `delete_date` text,
  PRIMARY KEY (`user_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `user_pass`
--

LOCK TABLES `user_pass` WRITE;
/*!40000 ALTER TABLE `user_pass` DISABLE KEYS */;
/*!40000 ALTER TABLE `user_pass` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `user_push`
--

DROP TABLE IF EXISTS `user_push`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `user_push` (
  `user_id` int(11) NOT NULL,
  `token` text,
  `create_date` text,
  `modified_date` text,
  `delete_date` text,
  PRIMARY KEY (`user_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `user_push`
--

LOCK TABLES `user_push` WRITE;
/*!40000 ALTER TABLE `user_push` DISABLE KEYS */;
/*!40000 ALTER TABLE `user_push` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `user_token`
--

DROP TABLE IF EXISTS `user_token`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `user_token` (
  `user_id` int(11) NOT NULL,
  `token` text,
  `reset_token` text,
  `create_date` text,
  `expire_date` text,
  `modified_date` text,
  `delete_date` text,
  PRIMARY KEY (`user_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `user_token`
--

LOCK TABLES `user_token` WRITE;
/*!40000 ALTER TABLE `user_token` DISABLE KEYS */;
/*!40000 ALTER TABLE `user_token` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `vote`
--

DROP TABLE IF EXISTS `vote`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `vote` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `poll_id` int(11) DEFAULT NULL,
  `candidate_id` int(11) DEFAULT NULL,
  `user_id` int(11) DEFAULT NULL,
  `create_date` text COLLATE utf8mb4_general_ci,
  `modified_date` text COLLATE utf8mb4_general_ci,
  `delete_date` text COLLATE utf8mb4_general_ci,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `vote`
--

LOCK TABLES `vote` WRITE;
/*!40000 ALTER TABLE `vote` DISABLE KEYS */;
/*!40000 ALTER TABLE `vote` ENABLE KEYS */;
UNLOCK TABLES;
/*!40103 SET TIME_ZONE=@OLD_TIME_ZONE */;

/*!40101 SET SQL_MODE=@OLD_SQL_MODE */;
/*!40014 SET FOREIGN_KEY_CHECKS=@OLD_FOREIGN_KEY_CHECKS */;
/*!40014 SET UNIQUE_CHECKS=@OLD_UNIQUE_CHECKS */;
/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
/*!40111 SET SQL_NOTES=@OLD_SQL_NOTES */;

-- Dump completed on 2022-10-29  8:05:54
