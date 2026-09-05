-- MariaDB dump 10.19  Distrib 10.4.32-MariaDB, for Win64 (AMD64)
--
-- Host: localhost    Database: casaul_hotel
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
-- Table structure for table `cache`
--

DROP TABLE IF EXISTS `cache`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `cache` (
  `key` varchar(255) NOT NULL,
  `value` mediumtext NOT NULL,
  `expiration` int(11) NOT NULL,
  PRIMARY KEY (`key`),
  KEY `cache_expiration_index` (`expiration`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `cache`
--

LOCK TABLES `cache` WRITE;
/*!40000 ALTER TABLE `cache` DISABLE KEYS */;
/*!40000 ALTER TABLE `cache` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `cache_locks`
--

DROP TABLE IF EXISTS `cache_locks`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `cache_locks` (
  `key` varchar(255) NOT NULL,
  `owner` varchar(255) NOT NULL,
  `expiration` int(11) NOT NULL,
  PRIMARY KEY (`key`),
  KEY `cache_locks_expiration_index` (`expiration`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `cache_locks`
--

LOCK TABLES `cache_locks` WRITE;
/*!40000 ALTER TABLE `cache_locks` DISABLE KEYS */;
/*!40000 ALTER TABLE `cache_locks` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `failed_jobs`
--

DROP TABLE IF EXISTS `failed_jobs`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `failed_jobs` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `uuid` varchar(255) NOT NULL,
  `connection` text NOT NULL,
  `queue` text NOT NULL,
  `payload` longtext NOT NULL,
  `exception` longtext NOT NULL,
  `failed_at` timestamp NOT NULL DEFAULT current_timestamp(),
  PRIMARY KEY (`id`),
  UNIQUE KEY `failed_jobs_uuid_unique` (`uuid`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `failed_jobs`
--

LOCK TABLES `failed_jobs` WRITE;
/*!40000 ALTER TABLE `failed_jobs` DISABLE KEYS */;
/*!40000 ALTER TABLE `failed_jobs` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `inventory_items`
--

DROP TABLE IF EXISTS `inventory_items`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `inventory_items` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `category` enum('amenities','event_place','dining') NOT NULL,
  `name` varchar(255) NOT NULL,
  `type` varchar(255) DEFAULT NULL,
  `description` text DEFAULT NULL,
  `price` decimal(10,2) NOT NULL DEFAULT 0.00,
  `status` varchar(255) NOT NULL DEFAULT 'available',
  `location` varchar(255) DEFAULT NULL,
  `capacity` int(10) unsigned DEFAULT NULL,
  `available_from` time DEFAULT NULL,
  `available_to` time DEFAULT NULL,
  `quantity` int(10) unsigned DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=27 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `inventory_items`
--

LOCK TABLES `inventory_items` WRITE;
/*!40000 ALTER TABLE `inventory_items` DISABLE KEYS */;
INSERT INTO `inventory_items` VALUES (1,'amenities','Parking Lot',NULL,'sgdhsjkssa',123556.00,'available',NULL,NULL,NULL,NULL,NULL,'2026-08-20 21:47:35','2026-08-20 21:47:35'),(2,'event_place','ashgdfjklsa;s',NULL,'asdhbfksla',10000.00,'available',NULL,2,NULL,NULL,NULL,'2026-08-20 21:48:45','2026-08-20 21:48:45'),(3,'event_place','ashgdfjklsa;s',NULL,'asdhbfksla',10000.00,'available',NULL,2,NULL,NULL,NULL,'2026-08-20 21:48:45','2026-08-20 21:48:45'),(4,'event_place','ashgdfjklsa;s',NULL,'asdhbfksla',10000.00,'available',NULL,2,NULL,NULL,NULL,'2026-08-20 21:48:45','2026-08-20 21:48:45'),(5,'event_place','ashgdfjklsa;s',NULL,'asdhbfksla',10000.00,'available',NULL,2,NULL,NULL,NULL,'2026-08-20 21:48:46','2026-08-20 21:48:46'),(6,'event_place','ashgdfjklsa;s',NULL,'asdhbfksla',10000.00,'available',NULL,2,NULL,NULL,NULL,'2026-08-20 21:48:46','2026-08-20 21:48:46'),(7,'event_place','ashgdfjklsa;s',NULL,'asdhbfksla',10000.00,'available',NULL,2,NULL,NULL,NULL,'2026-08-20 21:48:46','2026-08-20 21:48:46'),(8,'event_place','ashgdfjklsa;s',NULL,'asdhbfksla',10000.00,'available',NULL,2,NULL,NULL,NULL,'2026-08-20 21:48:47','2026-08-20 21:48:47'),(9,'event_place','ashgdfjklsa;s',NULL,'asdhbfksla',10000.00,'available',NULL,2,NULL,NULL,NULL,'2026-08-20 21:48:47','2026-08-20 21:48:47'),(10,'dining','Darlene Rane Bongon','Menu / Meal','ASDFGHZ',123.00,'available',NULL,NULL,'17:00:00','21:00:00',25,'2026-08-20 21:51:40','2026-08-20 21:51:40'),(11,'amenities','svabs',NULL,'svvbnds',1122.00,'available',NULL,NULL,NULL,NULL,NULL,'2026-08-20 21:53:28','2026-08-20 21:53:28'),(12,'amenities','svabs',NULL,'svvbnds',1122.00,'available',NULL,NULL,NULL,NULL,NULL,'2026-08-20 21:53:28','2026-08-20 21:53:28'),(13,'amenities','svabs',NULL,'svvbnds',1122.00,'available',NULL,NULL,NULL,NULL,NULL,'2026-08-20 21:53:28','2026-08-20 21:53:28'),(14,'amenities','svabs',NULL,'svvbnds',1122.00,'available',NULL,NULL,NULL,NULL,NULL,'2026-08-20 21:53:28','2026-08-20 21:53:28'),(15,'amenities','svabs',NULL,'svvbnds',1122.00,'available',NULL,NULL,NULL,NULL,NULL,'2026-08-20 21:53:29','2026-08-20 21:53:29'),(16,'amenities','svabs',NULL,'svvbnds',1122.00,'available',NULL,NULL,NULL,NULL,NULL,'2026-08-20 21:53:29','2026-08-20 21:53:29'),(17,'amenities','svabs',NULL,'svvbnds',1122.00,'available',NULL,NULL,NULL,NULL,NULL,'2026-08-20 21:53:29','2026-08-20 21:53:29'),(18,'amenities','svabs',NULL,'svvbnds',1122.00,'available',NULL,NULL,NULL,NULL,NULL,'2026-08-20 21:53:29','2026-08-20 21:53:29'),(19,'amenities','svabs',NULL,'svvbnds',1122.00,'available',NULL,NULL,NULL,NULL,NULL,'2026-08-20 21:53:30','2026-08-20 21:53:30'),(20,'amenities','svabs',NULL,'svvbnds',1122.00,'available',NULL,NULL,NULL,NULL,NULL,'2026-08-20 21:53:30','2026-08-20 21:53:30'),(21,'amenities','svabs',NULL,'svvbnds',1122.00,'available',NULL,NULL,NULL,NULL,NULL,'2026-08-20 21:53:30','2026-08-20 21:53:30'),(22,'amenities','svabs',NULL,'svvbnds',1122.00,'available',NULL,NULL,NULL,NULL,NULL,'2026-08-20 21:53:30','2026-08-20 21:53:30'),(23,'amenities','svabs',NULL,'svvbnds',1122.00,'available',NULL,NULL,NULL,NULL,NULL,'2026-08-20 21:53:31','2026-08-20 21:53:31'),(24,'amenities','svabs',NULL,'svvbnds',1122.00,'available',NULL,NULL,NULL,NULL,NULL,'2026-08-20 21:53:31','2026-08-20 21:53:31'),(25,'amenities','svabs',NULL,'svvbnds',1122.00,'available',NULL,NULL,NULL,NULL,NULL,'2026-08-20 21:53:31','2026-08-20 21:53:31'),(26,'amenities','svabs',NULL,'svvbnds',1122.00,'available',NULL,NULL,NULL,NULL,NULL,'2026-08-20 21:53:31','2026-08-20 21:53:31');
/*!40000 ALTER TABLE `inventory_items` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `job_batches`
--

DROP TABLE IF EXISTS `job_batches`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `job_batches` (
  `id` varchar(255) NOT NULL,
  `name` varchar(255) NOT NULL,
  `total_jobs` int(11) NOT NULL,
  `pending_jobs` int(11) NOT NULL,
  `failed_jobs` int(11) NOT NULL,
  `failed_job_ids` longtext NOT NULL,
  `options` mediumtext DEFAULT NULL,
  `cancelled_at` int(11) DEFAULT NULL,
  `created_at` int(11) NOT NULL,
  `finished_at` int(11) DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `job_batches`
--

LOCK TABLES `job_batches` WRITE;
/*!40000 ALTER TABLE `job_batches` DISABLE KEYS */;
/*!40000 ALTER TABLE `job_batches` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `jobs`
--

DROP TABLE IF EXISTS `jobs`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `jobs` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `queue` varchar(255) NOT NULL,
  `payload` longtext NOT NULL,
  `attempts` tinyint(3) unsigned NOT NULL,
  `reserved_at` int(10) unsigned DEFAULT NULL,
  `available_at` int(10) unsigned NOT NULL,
  `created_at` int(10) unsigned NOT NULL,
  PRIMARY KEY (`id`),
  KEY `jobs_queue_index` (`queue`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `jobs`
--

LOCK TABLES `jobs` WRITE;
/*!40000 ALTER TABLE `jobs` DISABLE KEYS */;
/*!40000 ALTER TABLE `jobs` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `maintenance_reports`
--

DROP TABLE IF EXISTS `maintenance_reports`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `maintenance_reports` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `room_number` varchar(255) NOT NULL,
  `room_type` varchar(255) NOT NULL,
  `reported_by` varchar(255) NOT NULL,
  `category` varchar(255) NOT NULL,
  `priority` varchar(255) NOT NULL,
  `problem` varchar(255) NOT NULL,
  `description` text NOT NULL,
  `photo_path` varchar(255) DEFAULT NULL,
  `date_reported` datetime NOT NULL,
  `expected_date` date DEFAULT NULL,
  `technician` varchar(255) NOT NULL,
  `status` varchar(255) NOT NULL DEFAULT 'Pending',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `maintenance_reports`
--

LOCK TABLES `maintenance_reports` WRITE;
/*!40000 ALTER TABLE `maintenance_reports` DISABLE KEYS */;
/*!40000 ALTER TABLE `maintenance_reports` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `messages`
--

DROP TABLE IF EXISTS `messages`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `messages` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `customer_name` varchar(255) NOT NULL,
  `customer_email` varchar(255) NOT NULL,
  `message` text NOT NULL,
  `admin_reply` text DEFAULT NULL,
  `is_replied` tinyint(1) NOT NULL DEFAULT 0,
  `replied_at` timestamp NULL DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `messages`
--

LOCK TABLES `messages` WRITE;
/*!40000 ALTER TABLE `messages` DISABLE KEYS */;
/*!40000 ALTER TABLE `messages` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `migrations`
--

DROP TABLE IF EXISTS `migrations`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `migrations` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `migration` varchar(255) NOT NULL,
  `batch` int(11) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=15 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `migrations`
--

LOCK TABLES `migrations` WRITE;
/*!40000 ALTER TABLE `migrations` DISABLE KEYS */;
INSERT INTO `migrations` VALUES (1,'0001_01_01_000000_create_users_table',1),(2,'0001_01_01_000001_create_cache_table',1),(3,'0001_01_01_000002_create_jobs_table',1),(4,'2026_07_27_171913_create_reservations_table',2),(5,'2026_07_27_171934_create_rooms_table',2),(6,'2026_07_27_171935_create_messages_table',2),(7,'2026_07_31_000001_alter_rooms_status_to_include_reserved',2),(8,'2026_08_01_100000_add_role_to_users_table',2),(9,'2026_08_01_100001_add_cleaning_status_to_rooms_table',2),(10,'2026_08_04_000000_add_guest_fields_to_users_table',2),(11,'2026_08_12_000000_add_account_management_to_users_table',2),(12,'2026_08_21_000000_create_maintenance_reports_table',2),(13,'2026_08_21_000001_create_inventory_items_table',2),(14,'2026_08_21_000002_add_reservation_room_foreign_key',2);
/*!40000 ALTER TABLE `migrations` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `password_reset_tokens`
--

DROP TABLE IF EXISTS `password_reset_tokens`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `password_reset_tokens` (
  `email` varchar(255) NOT NULL,
  `token` varchar(255) NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`email`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `password_reset_tokens`
--

LOCK TABLES `password_reset_tokens` WRITE;
/*!40000 ALTER TABLE `password_reset_tokens` DISABLE KEYS */;
/*!40000 ALTER TABLE `password_reset_tokens` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `reservations`
--

DROP TABLE IF EXISTS `reservations`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `reservations` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `room_id` bigint(20) unsigned NOT NULL,
  `guest_name` varchar(255) NOT NULL,
  `guest_email` varchar(255) NOT NULL,
  `guest_phone` varchar(255) NOT NULL,
  `check_in` date NOT NULL,
  `check_out` date NOT NULL,
  `status` enum('pending','confirmed','cancelled','completed') NOT NULL DEFAULT 'pending',
  `total_amount` decimal(10,2) NOT NULL,
  `special_requests` text DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `reservations_room_id_foreign` (`room_id`),
  CONSTRAINT `reservations_room_id_foreign` FOREIGN KEY (`room_id`) REFERENCES `rooms` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `reservations`
--

LOCK TABLES `reservations` WRITE;
/*!40000 ALTER TABLE `reservations` DISABLE KEYS */;
/*!40000 ALTER TABLE `reservations` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `rooms`
--

DROP TABLE IF EXISTS `rooms`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `rooms` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `room_number` varchar(255) NOT NULL,
  `room_type` varchar(255) NOT NULL,
  `price` decimal(10,2) NOT NULL,
  `floor` varchar(255) NOT NULL,
  `status` varchar(255) NOT NULL DEFAULT 'available',
  `cleaning_status` enum('clean','dirty','in_progress') NOT NULL DEFAULT 'clean',
  `description` text DEFAULT NULL,
  `image` varchar(255) DEFAULT NULL,
  `capacity` int(11) NOT NULL DEFAULT 2,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=44 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `rooms`
--

LOCK TABLES `rooms` WRITE;
/*!40000 ALTER TABLE `rooms` DISABLE KEYS */;
INSERT INTO `rooms` VALUES (33,'106','Deluxe Room',66666.00,'6','available','clean',NULL,NULL,6,'2026-08-11 23:44:07','2026-08-11 23:44:07'),(39,'177','Deluxe Room',1.00,'1','available','clean',NULL,NULL,1,'2026-08-12 00:11:34','2026-08-12 00:11:34'),(40,'102','Deluxe Room',90000.00,'1st','available','clean','sgdfhyduhsjkmgresa',NULL,2,'2026-08-20 18:35:31','2026-08-20 18:35:31'),(41,'104','Deluxe Room',1000.00,'1st','available','clean','darwftfhiwa',NULL,2,'2026-08-20 18:36:12','2026-08-20 18:36:12'),(43,'109','Deluxe Room',123.00,'1st','available','clean','adsf',NULL,10,'2026-08-20 21:33:46','2026-08-20 21:33:46');
/*!40000 ALTER TABLE `rooms` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `sessions`
--

DROP TABLE IF EXISTS `sessions`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `sessions` (
  `id` varchar(255) NOT NULL,
  `user_id` bigint(20) unsigned DEFAULT NULL,
  `ip_address` varchar(45) DEFAULT NULL,
  `user_agent` text DEFAULT NULL,
  `payload` longtext NOT NULL,
  `last_activity` int(11) NOT NULL,
  PRIMARY KEY (`id`),
  KEY `sessions_user_id_index` (`user_id`),
  KEY `sessions_last_activity_index` (`last_activity`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `sessions`
--

LOCK TABLES `sessions` WRITE;
/*!40000 ALTER TABLE `sessions` DISABLE KEYS */;
INSERT INTO `sessions` VALUES ('KHJZjU3xa98suAz3ywqvIy4qwQukDNdFNcujh6wf',1,'127.0.0.1','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/151.0.0.0 Safari/537.36','YTo1OntzOjY6Il90b2tlbiI7czo0MDoibFh5clNCWWZjcEhmTWpPdlE1M3VtTGlHWE5ZMFU1M1R1QnQ4MEZjciI7czozOiJ1cmwiO2E6MDp7fXM6OToiX3ByZXZpb3VzIjthOjI6e3M6MzoidXJsIjtzOjQyOiJodHRwOi8vMTI3LjAuMC4xOjgwMDAvZW1wbG95ZWUvcmVzZXJ2YXRpb24iO3M6NToicm91dGUiO3M6MjA6ImVtcGxveWVlLnJlc2VydmF0aW9uIjt9czo2OiJfZmxhc2giO2E6Mjp7czozOiJvbGQiO2E6MDp7fXM6MzoibmV3IjthOjA6e319czo1MDoibG9naW5fd2ViXzU5YmEzNmFkZGMyYjJmOTQwMTU4MGYwMTRjN2Y1OGVhNGUzMDk4OWQiO2k6MTt9',1787291637);
/*!40000 ALTER TABLE `sessions` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `users`
--

DROP TABLE IF EXISTS `users`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `users` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `first_name` varchar(255) DEFAULT NULL,
  `last_name` varchar(255) DEFAULT NULL,
  `middle_initial` varchar(3) DEFAULT NULL,
  `name` varchar(255) NOT NULL,
  `email` varchar(255) NOT NULL,
  `contact_no` varchar(25) DEFAULT NULL,
  `email_verified_at` timestamp NULL DEFAULT NULL,
  `password` varchar(255) NOT NULL,
  `role` enum('admin','housekeeping','employee','guest') NOT NULL DEFAULT 'admin',
  `is_active` tinyint(1) NOT NULL DEFAULT 1,
  `created_by` bigint(20) unsigned DEFAULT NULL,
  `remember_token` varchar(100) DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `users_email_unique` (`email`)
) ENGINE=InnoDB AUTO_INCREMENT=7 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `users`
--

LOCK TABLES `users` WRITE;
/*!40000 ALTER TABLE `users` DISABLE KEYS */;
INSERT INTO `users` VALUES (1,'Admin','User',NULL,'Admin','admin@casaul.com',NULL,NULL,'$2y$12$e41IjmBcddDktDhoaqP7keZmbvqd8GezCO/vhulrFvqZZAVpd36Na','admin',1,NULL,NULL,'2026-07-31 21:58:29','2026-08-11 18:50:12'),(2,'Housekeeping','Staff',NULL,'Housekeeping','housekeeping@casaul.com',NULL,NULL,'$2y$12$DEZRi9Jz0mvRV.bBjZIZdONtg29tkh/2ikJEYwVh60sGeP6CuPxNK','housekeeping',1,NULL,NULL,'2026-07-31 21:58:30','2026-08-11 18:50:13'),(3,'Employee','Staff',NULL,'Employee','employee@casaul.com',NULL,NULL,'$2y$12$w3J6Ry14.3KqNhvVpfD6Qu3367Wu/vpVveKOpuERlMwAsrsJ62T2q','employee',1,NULL,NULL,'2026-08-04 00:46:46','2026-08-11 18:50:13'),(4,'Markhel','Caneo','E','Markhel E. Caneo','markhel@casaul.com','09653258741',NULL,'$2y$12$cmJ3fCX5E0Q2Ru3vpXeHEOVWClHptqurIaPgp66emkBjeNUS4b.dK','admin',1,1,NULL,'2026-08-11 18:58:55','2026-08-11 18:58:55'),(5,'Darlene','Bongon','F','Darlene F. Bongon','darl@casaul.com','09096794568',NULL,'$2y$12$aau0eHghwkc3wo/L9Ruw4eSkWTRIjAE2hyl3T0fNzCDIEd1Czk.2W','employee',1,1,NULL,'2026-08-11 19:00:05','2026-08-11 19:00:05'),(6,'James','Bibon','B','James B. Bibon','james@casaul.com','09750377994',NULL,'$2y$12$DH7JBbKJgKZmdUg7ZzYhoenKv5AVfjeN6a6G9ZbMPR9sO381R5sba','housekeeping',1,1,NULL,'2026-08-11 19:15:45','2026-08-11 19:15:45');
/*!40000 ALTER TABLE `users` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Dumping routines for database 'casaul_hotel'
--
/*!40103 SET TIME_ZONE=@OLD_TIME_ZONE */;

/*!40101 SET SQL_MODE=@OLD_SQL_MODE */;
/*!40014 SET FOREIGN_KEY_CHECKS=@OLD_FOREIGN_KEY_CHECKS */;
/*!40014 SET UNIQUE_CHECKS=@OLD_UNIQUE_CHECKS */;
/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
/*!40111 SET SQL_NOTES=@OLD_SQL_NOTES */;

-- Dump completed on 2026-08-21 14:07:57
