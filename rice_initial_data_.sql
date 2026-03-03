-- MySQL dump 10.13  Distrib 9.0.1, for macos14.7 (arm64)
--
-- Host: localhost    Database: peace_test
-- ------------------------------------------------------
-- Server version	9.0.1

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
-- Table structure for table `cache`
--

DROP TABLE IF EXISTS `cache`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `cache` (
  `key` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `value` mediumtext COLLATE utf8mb4_unicode_ci NOT NULL,
  `expiration` int NOT NULL,
  PRIMARY KEY (`key`)
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
-- Table structure for table `categories`
--

DROP TABLE IF EXISTS `categories`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `categories` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `description` text COLLATE utf8mb4_unicode_ci,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=7 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `categories`
--

LOCK TABLES `categories` WRITE;
/*!40000 ALTER TABLE `categories` DISABLE KEYS */;
INSERT INTO `categories` VALUES (1,'Paw San',NULL,'2026-02-28 16:01:32','2026-02-28 16:01:32'),(2,'Ayar Min',NULL,'2026-02-28 16:03:22','2026-02-28 16:03:22'),(3,'Shan',NULL,'2026-02-28 16:03:31','2026-02-28 16:03:31'),(4,'Common',NULL,'2026-02-28 16:03:40','2026-02-28 16:03:40'),(5,'Kitchen',NULL,'2026-03-01 04:44:18','2026-03-01 04:44:18'),(6,'Oil',NULL,'2026-03-01 04:48:13','2026-03-01 04:48:13');
/*!40000 ALTER TABLE `categories` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `credit_allocations`
--

DROP TABLE IF EXISTS `credit_allocations`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `credit_allocations` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `credit_payment_id` bigint unsigned NOT NULL,
  `sale_id` bigint unsigned NOT NULL,
  `amount` bigint NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `credit_allocations_credit_payment_id_foreign` (`credit_payment_id`),
  KEY `credit_allocations_sale_id_foreign` (`sale_id`),
  CONSTRAINT `credit_allocations_credit_payment_id_foreign` FOREIGN KEY (`credit_payment_id`) REFERENCES `credit_payments` (`id`) ON DELETE CASCADE,
  CONSTRAINT `credit_allocations_sale_id_foreign` FOREIGN KEY (`sale_id`) REFERENCES `sales` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `credit_allocations`
--

LOCK TABLES `credit_allocations` WRITE;
/*!40000 ALTER TABLE `credit_allocations` DISABLE KEYS */;
/*!40000 ALTER TABLE `credit_allocations` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `credit_payments`
--

DROP TABLE IF EXISTS `credit_payments`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `credit_payments` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `customer_id` bigint unsigned NOT NULL,
  `amount` bigint NOT NULL,
  `note` text COLLATE utf8mb4_unicode_ci,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `credit_payments_customer_id_foreign` (`customer_id`),
  CONSTRAINT `credit_payments_customer_id_foreign` FOREIGN KEY (`customer_id`) REFERENCES `customers` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `credit_payments`
--

LOCK TABLES `credit_payments` WRITE;
/*!40000 ALTER TABLE `credit_payments` DISABLE KEYS */;
/*!40000 ALTER TABLE `credit_payments` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `customers`
--

DROP TABLE IF EXISTS `customers`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `customers` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `phone` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `credit_balance` bigint NOT NULL DEFAULT '0',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `address` text COLLATE utf8mb4_unicode_ci,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `customers`
--

LOCK TABLES `customers` WRITE;
/*!40000 ALTER TABLE `customers` DISABLE KEYS */;
INSERT INTO `customers` VALUES (1,'Walk-in Customer',NULL,0,'2026-02-28 15:57:21','2026-02-28 15:57:21',NULL);
/*!40000 ALTER TABLE `customers` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `jobs`
--

DROP TABLE IF EXISTS `jobs`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `jobs` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `queue` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `payload` longtext COLLATE utf8mb4_unicode_ci NOT NULL,
  `attempts` tinyint unsigned NOT NULL,
  `reserved_at` int unsigned DEFAULT NULL,
  `available_at` int unsigned NOT NULL,
  `created_at` int unsigned NOT NULL,
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
-- Table structure for table `migrations`
--

DROP TABLE IF EXISTS `migrations`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `migrations` (
  `id` int unsigned NOT NULL AUTO_INCREMENT,
  `migration` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `batch` int NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=28 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `migrations`
--

LOCK TABLES `migrations` WRITE;
/*!40000 ALTER TABLE `migrations` DISABLE KEYS */;
INSERT INTO `migrations` VALUES (1,'2026_02_28_222227_create_cache_table',1),(2,'2026_02_28_222227_create_categories_table',1),(3,'2026_02_28_222227_create_credit_allocations_table',1),(4,'2026_02_28_222227_create_credit_payments_table',1),(5,'2026_02_28_222227_create_customers_table',1),(6,'2026_02_28_222227_create_jobs_table',1),(7,'2026_02_28_222227_create_product_variants_table',1),(8,'2026_02_28_222227_create_products_table',1),(9,'2026_02_28_222227_create_sale_item_batches_table',1),(10,'2026_02_28_222227_create_sale_items_table',1),(11,'2026_02_28_222227_create_sale_payments_table',1),(12,'2026_02_28_222227_create_sales_table',1),(13,'2026_02_28_222227_create_sessions_table',1),(14,'2026_02_28_222227_create_stock_batches_table',1),(15,'2026_02_28_222227_create_stock_movements_table',1),(16,'2026_02_28_222227_create_users_table',1),(17,'2026_02_28_222227_create_warehouses_table',1),(18,'2026_02_28_222230_add_foreign_keys_to_credit_allocations_table',1),(19,'2026_02_28_222230_add_foreign_keys_to_credit_payments_table',1),(20,'2026_02_28_222230_add_foreign_keys_to_product_variants_table',1),(21,'2026_02_28_222230_add_foreign_keys_to_products_table',1),(22,'2026_02_28_222230_add_foreign_keys_to_sale_item_batches_table',1),(23,'2026_02_28_222230_add_foreign_keys_to_sale_items_table',1),(24,'2026_02_28_222230_add_foreign_keys_to_sale_payments_table',1),(25,'2026_02_28_222230_add_foreign_keys_to_sales_table',1),(26,'2026_02_28_222230_add_foreign_keys_to_stock_batches_table',1),(27,'2026_02_28_222230_add_foreign_keys_to_stock_movements_table',1);
/*!40000 ALTER TABLE `migrations` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `product_variants`
--

DROP TABLE IF EXISTS `product_variants`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `product_variants` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `product_id` bigint unsigned NOT NULL,
  `name` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `sku` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `unit_label` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'Unit',
  `selling_price` bigint NOT NULL DEFAULT '0',
  `pyi_per_bag` int DEFAULT NULL,
  `price_per_pyi` bigint DEFAULT NULL,
  `is_active` tinyint(1) NOT NULL DEFAULT '1',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `product_variants_sku_unique` (`sku`),
  KEY `product_variants_product_id_foreign` (`product_id`),
  CONSTRAINT `product_variants_product_id_foreign` FOREIGN KEY (`product_id`) REFERENCES `products` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=103 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `product_variants`
--

LOCK TABLES `product_variants` WRITE;
/*!40000 ALTER TABLE `product_variants` DISABLE KEYS */;
INSERT INTO `product_variants` VALUES (1,1,'New 24 Pyi',NULL,'Bag',245000,24,NULL,1,'2026-02-28 16:06:15','2026-03-01 05:40:15'),(2,1,'New 12 Pyi',NULL,'Bag',125000,12,NULL,1,'2026-02-28 16:07:10','2026-03-01 05:40:15'),(3,1,'New 6 Pyi',NULL,'Bag',63000,6,NULL,1,'2026-02-28 16:07:10','2026-03-01 05:40:15'),(4,1,'New 3 Pyi',NULL,'Bag',33000,3,NULL,1,'2026-02-28 16:07:10','2026-03-01 05:40:15'),(5,1,'New 1 Pyi',NULL,'Bag',11000,1,NULL,1,'2026-02-28 16:07:10','2026-03-01 05:40:15'),(6,2,'New 24 Pyi',NULL,'Bag',245000,24,NULL,1,'2026-02-28 16:08:32','2026-03-01 05:46:43'),(7,2,'New 12 Pyi',NULL,'Bag',125000,12,NULL,1,'2026-02-28 16:08:32','2026-03-01 05:46:43'),(8,2,'New 6 Pyi',NULL,'Bag',63000,6,NULL,1,'2026-02-28 16:08:32','2026-03-01 05:46:43'),(9,3,'New 24 Pyi',NULL,'Bag',225000,24,9500,1,'2026-02-28 16:09:48','2026-03-01 05:47:09'),(10,4,'New 24 Pyi',NULL,'Bag',235000,24,10000,1,'2026-02-28 16:10:45','2026-03-01 05:48:09'),(11,5,'New 24 Pyi',NULL,'Bag',0,24,12000,1,'2026-02-28 16:11:59','2026-03-01 05:49:55'),(12,6,'New 24 Pyi',NULL,'Bag',145000,24,6500,1,'2026-02-28 16:12:56','2026-03-02 13:57:19'),(13,7,'New 24 Pyi',NULL,'Bag',245000,24,NULL,1,'2026-02-28 16:14:40','2026-03-01 05:53:03'),(14,7,'New 12 Pyi',NULL,'Bag',125000,12,NULL,1,'2026-02-28 16:14:40','2026-03-01 05:53:03'),(15,7,'New 6 Pyi',NULL,'Bag',63000,6,NULL,1,'2026-02-28 16:14:40','2026-03-01 05:53:03'),(16,8,'New 6 Pyi',NULL,'Bag',58000,6,NULL,1,'2026-02-28 16:15:30','2026-03-01 06:03:39'),(17,9,'New 24 Pyi',NULL,'Bag',180000,24,8000,1,'2026-02-28 16:16:04','2026-03-02 13:53:05'),(18,10,'New 24 Pyi',NULL,'Bag',220000,24,9500,1,'2026-02-28 16:16:59','2026-03-02 13:55:08'),(19,11,'24 Pyi',NULL,'Bag',155000,24,7000,1,'2026-02-28 16:17:34','2026-03-02 13:55:40'),(20,12,'24 Pyi',NULL,'Bag',220000,24,NULL,1,'2026-02-28 16:18:30','2026-03-02 13:56:49'),(21,12,'12 Pyi',NULL,'Bag',110000,12,NULL,1,'2026-02-28 16:18:30','2026-03-02 13:56:49'),(22,12,'6 Pyi',NULL,'Bag',57000,6,NULL,1,'2026-02-28 16:18:30','2026-03-02 13:56:49'),(23,13,'24 Pyi',NULL,'Bag',0,24,NULL,1,'2026-02-28 16:19:02','2026-03-02 13:57:43'),(24,14,'24 Pyi',NULL,'Bag',200000,24,NULL,1,'2026-02-28 16:19:50','2026-03-02 13:58:30'),(26,14,'6 Pyi',NULL,'Bag',54000,6,NULL,1,'2026-02-28 16:19:50','2026-03-02 13:58:30'),(27,15,'24 Pyi',NULL,'Bag',0,24,9000,1,'2026-02-28 16:20:34','2026-03-02 13:59:01'),(28,16,'24 Pyi',NULL,'Bag',135000,24,5500,1,'2026-02-28 16:20:52','2026-03-02 14:00:08'),(29,17,'24 Pyi',NULL,'Bag',220000,24,9500,1,'2026-02-28 16:21:22','2026-03-02 14:00:57'),(30,18,'24 Pyi',NULL,'Bag',170000,24,7500,1,'2026-02-28 16:22:10','2026-03-02 14:01:12'),(31,19,'20 Pyi',NULL,'Bag',120000,20,6500,1,'2026-02-28 16:22:35','2026-03-02 14:03:11'),(32,20,'20 Pyi',NULL,'Bag',125000,NULL,NULL,1,'2026-02-28 16:22:50','2026-03-02 14:03:01'),(33,21,'20 Pyi',NULL,'Bag',120000,20,6500,1,'2026-02-28 16:23:09','2026-03-02 14:03:27'),(34,22,'20 Pyi',NULL,'Bag',125000,NULL,NULL,1,'2026-02-28 16:23:27','2026-03-02 14:03:37'),(35,23,'20 Pyi',NULL,'Bag',155000,20,8000,1,'2026-02-28 16:23:40','2026-03-02 14:05:00'),(36,24,'22 Pyi',NULL,'Bag',125000,22,6500,1,'2026-02-28 16:23:58','2026-03-02 14:04:45'),(37,25,'20 Pyi',NULL,'Bag',120000,20,6500,1,'2026-02-28 16:24:11','2026-03-02 14:05:22'),(38,26,'22 Pyi',NULL,'Bag',165000,22,7500,1,'2026-02-28 16:24:24','2026-03-02 14:05:56'),(39,27,'22 Pyi',NULL,'Bag',145000,22,6500,1,'2026-02-28 16:24:41','2026-03-02 14:06:21'),(40,28,'22 Pyi',NULL,'Bag',110000,22,5500,1,'2026-02-28 16:24:57','2026-03-02 14:07:03'),(41,29,'22 Pyi',NULL,'Bag',165000,22,7500,1,'2026-02-28 16:25:13','2026-03-02 14:07:20'),(42,30,'23 Pyi',NULL,'Bag',125000,23,6000,1,'2026-02-28 16:25:27','2026-03-02 14:08:26'),(43,31,'24 Pyi',NULL,'Bag',0,NULL,NULL,1,'2026-02-28 16:25:40','2026-03-02 14:08:35'),(44,32,'24 Pyi',NULL,'Bag',120000,23,5500,1,'2026-02-28 16:25:59','2026-03-02 14:09:01'),(45,33,'24 Pyi',NULL,'Bag',120000,24,5500,1,'2026-02-28 16:26:12','2026-03-02 14:09:19'),(47,35,'24 Pyi',NULL,'Bag',80000,24,3500,1,'2026-02-28 16:26:45','2026-03-02 14:11:06'),(48,36,'24 Pyi',NULL,'Bag',85000,23,4000,1,'2026-02-28 16:27:03','2026-03-02 14:11:27'),(49,34,'Old 24 Pyi',NULL,'Bag',85000,24,4000,1,'2026-02-28 16:30:39','2026-03-02 14:10:40'),(50,37,'10 Pyi',NULL,'Bag',60000,10,6500,1,'2026-02-28 16:31:33','2026-03-02 14:12:38'),(51,38,'အကြီး',NULL,'Bottle',5500,NULL,NULL,1,'2026-03-01 04:45:57','2026-03-01 04:45:57'),(52,39,'အကြီး',NULL,'Bottle',5000,NULL,NULL,1,'2026-03-01 04:47:28','2026-03-01 04:47:28'),(53,40,'0.25',NULL,'Viss',4000,NULL,NULL,1,'2026-03-01 04:49:48','2026-03-01 04:49:48'),(54,40,'0.5',NULL,'Viss',5500,NULL,NULL,1,'2026-03-01 04:49:48','2026-03-01 04:49:48'),(55,40,'1',NULL,'Viss',12000,NULL,NULL,1,'2026-03-01 04:49:48','2026-03-01 04:49:48'),(56,40,'3',NULL,'Viss',33000,NULL,NULL,1,'2026-03-01 04:49:48','2026-03-01 04:49:48'),(57,40,'5',NULL,'Viss',55000,NULL,NULL,1,'2026-03-01 04:49:48','2026-03-01 04:49:48'),(58,40,'10',NULL,'Viss',115000,NULL,NULL,1,'2026-03-01 04:49:48','2026-03-01 04:49:48'),(59,41,'0.25',NULL,'Viss',3500,NULL,NULL,1,'2026-03-01 04:50:58','2026-03-01 04:50:58'),(60,41,'0.5',NULL,'Viss',5000,NULL,NULL,1,'2026-03-01 04:50:58','2026-03-01 04:50:58'),(61,41,'1',NULL,'Viss',11500,NULL,NULL,1,'2026-03-01 04:50:58','2026-03-01 04:50:58'),(62,41,'3',NULL,'Viss',33000,NULL,NULL,1,'2026-03-01 04:50:58','2026-03-01 04:50:58'),(63,41,'5',NULL,'Viss',55000,NULL,NULL,1,'2026-03-01 04:50:58','2026-03-01 04:50:58'),(64,41,'10',NULL,'Viss',115000,NULL,NULL,1,'2026-03-01 04:50:58','2026-03-01 04:50:58'),(65,42,'1',NULL,'Viss',11500,NULL,NULL,1,'2026-03-01 04:52:12','2026-03-01 04:52:12'),(66,42,'3',NULL,'Viss',33000,NULL,NULL,1,'2026-03-01 04:52:12','2026-03-01 04:52:12'),(67,42,'5',NULL,'Viss',55000,NULL,NULL,1,'2026-03-01 04:52:12','2026-03-01 04:52:12'),(68,42,'10',NULL,'Viss',115000,NULL,NULL,1,'2026-03-01 04:52:12','2026-03-01 04:52:12'),(69,43,'0.5',NULL,'Viss',11500,NULL,NULL,1,'2026-03-01 04:54:20','2026-03-01 04:54:20'),(70,43,'1',NULL,'Viss',23000,NULL,NULL,1,'2026-03-01 04:54:20','2026-03-01 04:54:20'),(71,43,'2',NULL,'Viss',46000,NULL,NULL,1,'2026-03-01 04:54:20','2026-03-01 04:54:20'),(72,43,'3',NULL,'Viss',39000,NULL,NULL,1,'2026-03-01 04:54:20','2026-03-01 04:54:20'),(73,43,'5',NULL,'Viss',115000,NULL,NULL,1,'2026-03-01 04:54:20','2026-03-01 04:54:20'),(74,43,'10',NULL,'Viss',225000,NULL,NULL,1,'2026-03-01 04:54:20','2026-03-01 04:54:20'),(75,44,'0.25',NULL,'Viss',6000,NULL,NULL,1,'2026-03-01 04:56:39','2026-03-01 04:56:39'),(76,44,'0.5',NULL,'Viss',12000,NULL,NULL,1,'2026-03-01 04:56:39','2026-03-01 04:56:39'),(77,44,'1',NULL,'Viss',23500,NULL,NULL,1,'2026-03-01 04:56:39','2026-03-01 04:56:39'),(78,44,'2',NULL,'Viss',47000,NULL,NULL,1,'2026-03-01 04:56:39','2026-03-01 04:56:39'),(79,44,'3',NULL,'Viss',70500,NULL,NULL,1,'2026-03-01 04:56:39','2026-03-01 04:56:39'),(80,44,'5',NULL,'Viss',117500,NULL,NULL,1,'2026-03-01 04:56:39','2026-03-01 04:56:39'),(81,45,'0.5',NULL,'Viss',11000,NULL,NULL,1,'2026-03-01 04:58:20','2026-03-01 04:59:59'),(82,45,'1',NULL,'Viss',22000,NULL,NULL,1,'2026-03-01 04:58:20','2026-03-01 04:59:59'),(83,45,'2',NULL,'Viss',44000,NULL,NULL,1,'2026-03-01 04:58:20','2026-03-01 04:59:59'),(84,45,'3',NULL,'Viss',66000,NULL,NULL,1,'2026-03-01 04:58:20','2026-03-01 04:59:59'),(85,45,'5',NULL,'Viss',110000,NULL,NULL,1,'2026-03-01 04:58:20','2026-03-01 04:59:59'),(86,46,'0.5',NULL,'Viss',11500,NULL,NULL,1,'2026-03-01 05:02:18','2026-03-01 05:02:18'),(87,46,'1',NULL,'Viss',23000,NULL,NULL,1,'2026-03-01 05:02:18','2026-03-01 05:02:18'),(88,46,'2',NULL,'Viss',46000,NULL,NULL,1,'2026-03-01 05:02:18','2026-03-01 05:02:18'),(89,46,'3',NULL,'Viss',69000,NULL,NULL,1,'2026-03-01 05:02:18','2026-03-01 05:02:18'),(90,46,'5',NULL,'Viss',115000,NULL,NULL,1,'2026-03-01 05:02:18','2026-03-01 05:02:18'),(91,47,'0.5',NULL,'Viss',8500,NULL,NULL,1,'2026-03-01 05:02:57','2026-03-01 05:02:57'),(92,47,'1',NULL,'Viss',16000,NULL,NULL,1,'2026-03-01 05:02:57','2026-03-01 05:02:57'),(93,48,'0.5',NULL,'Viss',6000,NULL,NULL,1,'2026-03-01 05:03:47','2026-03-01 05:03:47'),(94,48,'1',NULL,'Viss',14000,NULL,NULL,1,'2026-03-01 05:03:47','2026-03-01 05:03:47'),(95,49,'0.5',NULL,'Viss',7000,NULL,NULL,1,'2026-03-01 05:05:05','2026-03-01 05:05:05'),(96,49,'1',NULL,'Viss',14000,NULL,NULL,1,'2026-03-01 05:05:05','2026-03-01 05:05:05'),(97,49,'3',NULL,'Viss',34500,NULL,NULL,1,'2026-03-01 05:05:05','2026-03-01 05:05:05'),(98,49,'10',NULL,'Viss',120000,NULL,NULL,1,'2026-03-01 05:05:05','2026-03-01 05:05:05'),(99,50,'3',NULL,'Viss',29000,NULL,NULL,1,'2026-03-01 05:05:49','2026-03-01 05:05:49'),(100,51,'3',NULL,'Viss',29000,NULL,NULL,1,'2026-03-01 05:06:16','2026-03-01 05:06:16'),(101,52,'3',NULL,'Viss',29500,NULL,NULL,1,'2026-03-01 05:06:43','2026-03-01 05:06:43'),(102,53,'3',NULL,'Viss',29000,NULL,NULL,1,'2026-03-01 05:07:39','2026-03-01 05:07:39');
/*!40000 ALTER TABLE `product_variants` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `products`
--

DROP TABLE IF EXISTS `products`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `products` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `category_id` bigint unsigned DEFAULT NULL,
  `is_active` tinyint(1) NOT NULL DEFAULT '1',
  `description` text COLLATE utf8mb4_unicode_ci,
  `image_path` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `products_category_id_foreign` (`category_id`),
  CONSTRAINT `products_category_id_foreign` FOREIGN KEY (`category_id`) REFERENCES `categories` (`id`) ON DELETE SET NULL
) ENGINE=InnoDB AUTO_INCREMENT=54 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `products`
--

LOCK TABLES `products` WRITE;
/*!40000 ALTER TABLE `products` DISABLE KEYS */;
INSERT INTO `products` VALUES (1,'Aung Thi Ha',1,1,NULL,'products/YowSjOCCaK46kcVK5yH0Es1EDsDTz0WlvE1APyn7.jpg','2026-02-28 16:06:15','2026-03-01 05:40:15'),(2,'5 Diamond',1,1,NULL,NULL,'2026-02-28 16:08:32','2026-03-01 05:46:43'),(3,'Aung Thi Ha အရော',1,1,NULL,NULL,'2026-02-28 16:09:48','2026-03-01 05:47:09'),(4,'SMK Paw San',1,1,'ဖောက်ရောင်း',NULL,'2026-02-28 16:10:45','2026-03-01 05:48:09'),(5,'Raw Paw San',1,1,NULL,NULL,'2026-02-28 16:11:59','2026-03-01 05:49:55'),(6,'TG ဇလတ်',1,1,NULL,NULL,'2026-02-28 16:12:56','2026-03-02 13:57:19'),(7,'Nyein Chan Paw San',1,1,NULL,NULL,'2026-02-28 16:14:40','2026-03-01 05:53:03'),(8,'Golden Bee',1,1,NULL,NULL,'2026-02-28 16:15:30','2026-03-01 06:03:39'),(9,'Pon Paw San',1,1,NULL,NULL,'2026-02-28 16:16:04','2026-03-02 13:53:05'),(10,'5 Diamond Ayar Min',2,1,NULL,NULL,'2026-02-28 16:16:59','2026-03-02 13:55:08'),(11,'Pon AyarMin',2,1,NULL,NULL,'2026-02-28 16:17:34','2026-03-02 13:55:40'),(12,'Nyein Chan Ayar Min',2,1,NULL,NULL,'2026-02-28 16:18:30','2026-03-02 13:56:49'),(13,'Raw Ayar Min',2,0,NULL,NULL,'2026-02-28 16:19:02','2026-03-02 13:57:43'),(14,'Nyein Chan မဂျမ်းတော',2,1,NULL,NULL,'2026-02-28 16:19:50','2026-03-02 13:58:30'),(15,'Raw မဂျမ်းတော',2,1,NULL,NULL,'2026-02-28 16:20:34','2026-03-02 13:59:01'),(16,'SMK မဂျမ်းရိုးရိုး',2,1,NULL,NULL,'2026-02-28 16:20:52','2026-03-02 14:00:08'),(17,'KKS ဝန်ကြီးဧရာ',2,1,NULL,NULL,'2026-02-28 16:21:22','2026-03-02 14:00:57'),(18,'KKS လုံးသွယ်မွှေး',2,1,NULL,NULL,'2026-02-28 16:22:10','2026-03-02 14:01:12'),(19,'881',3,1,NULL,NULL,'2026-02-28 16:22:35','2026-03-02 14:03:11'),(20,'881 (Arr Yon)',3,1,NULL,NULL,'2026-02-28 16:22:50','2026-03-02 14:03:01'),(21,'502',3,1,NULL,NULL,'2026-02-28 16:23:09','2026-03-02 14:03:27'),(22,'502 (Arr Yon)',3,1,NULL,NULL,'2026-02-28 16:23:27','2026-03-02 14:03:37'),(23,'456 Arr Yon',3,1,NULL,NULL,'2026-02-28 16:23:40','2026-03-02 14:05:00'),(24,'Kaw Lin',3,1,NULL,NULL,'2026-02-28 16:23:58','2026-03-02 14:04:45'),(25,'ရှမ်းနီ',3,1,NULL,NULL,'2026-02-28 16:24:11','2026-03-02 14:05:22'),(26,'ကောက်ညင်းရှယ်',3,1,NULL,NULL,'2026-02-28 16:24:24','2026-03-02 14:05:56'),(27,'ကောက်ညင်းနီ',3,1,NULL,NULL,'2026-02-28 16:24:41','2026-03-02 14:06:21'),(28,'ညင်းဇလတ်',3,1,NULL,NULL,'2026-02-28 16:24:57','2026-03-02 14:07:03'),(29,'ငချိတ်',3,1,NULL,NULL,'2026-02-28 16:25:13','2026-03-02 14:07:20'),(30,'Maw Bi',4,1,NULL,NULL,'2026-02-28 16:25:27','2026-03-02 14:08:26'),(31,'ပုလဲသွယ်',4,0,NULL,NULL,'2026-02-28 16:25:40','2026-03-02 14:08:35'),(32,'ရွှေသပြေ သုခ',4,1,NULL,NULL,'2026-02-28 16:25:59','2026-03-02 14:09:01'),(33,'ရက် ၉၀',4,1,NULL,NULL,'2026-02-28 16:26:12','2026-03-02 14:09:19'),(34,'နှံကောက်',4,1,NULL,NULL,'2026-02-28 16:26:30','2026-03-02 14:10:40'),(35,'ဆန်ကြမ်း',4,1,NULL,NULL,'2026-02-28 16:26:45','2026-03-02 14:11:06'),(36,'ဇကွဲ',4,1,NULL,NULL,'2026-02-28 16:27:03','2026-03-02 14:11:27'),(37,'စတော်ပဲ',4,1,NULL,NULL,'2026-02-28 16:31:33','2026-03-02 14:12:38'),(38,'မြင်းပျံ ကြာညို့',5,1,NULL,NULL,'2026-03-01 04:45:57','2026-03-01 04:45:57'),(39,'ထုံချွင် ကြာညို့',5,1,NULL,NULL,'2026-03-01 04:47:28','2026-03-01 04:47:28'),(40,'Shwe Mandalay',6,1,NULL,NULL,'2026-03-01 04:49:48','2026-03-01 04:49:48'),(41,'Shwe Taung',6,1,NULL,NULL,'2026-03-01 04:50:58','2026-03-01 04:50:58'),(42,'Zarmani',6,1,NULL,NULL,'2026-03-01 04:52:12','2026-03-01 04:52:12'),(43,'Shwe Ohh',6,1,NULL,NULL,'2026-03-01 04:54:20','2026-03-01 04:54:20'),(44,'A May Htwar',6,1,NULL,NULL,'2026-03-01 04:56:39','2026-03-01 04:56:39'),(45,'Pwint Phyu',6,1,NULL,NULL,'2026-03-01 04:58:20','2026-03-01 04:59:59'),(46,'Shwe Ohh',6,1,NULL,NULL,'2026-03-01 05:02:18','2026-03-01 05:02:18'),(47,'Good Choice',6,1,NULL,NULL,'2026-03-01 05:02:57','2026-03-01 05:02:57'),(48,'Duck',6,1,NULL,NULL,'2026-03-01 05:03:47','2026-03-01 05:03:47'),(49,'ပုဇွန်နှစ်ကောင်',6,1,NULL,NULL,'2026-03-01 05:05:05','2026-03-01 05:05:05'),(50,'အုန်းပင်',6,1,NULL,NULL,'2026-03-01 05:05:49','2026-03-01 05:05:49'),(51,'ဖူလီ',6,1,NULL,NULL,'2026-03-01 05:06:16','2026-03-01 05:06:16'),(52,'ပဲဝါ',6,1,NULL,NULL,'2026-03-01 05:06:43','2026-03-01 05:06:43'),(53,'စာတန်း (Fuzhiquan)',6,1,NULL,NULL,'2026-03-01 05:07:39','2026-03-01 05:07:39');
/*!40000 ALTER TABLE `products` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `sale_item_batches`
--

DROP TABLE IF EXISTS `sale_item_batches`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `sale_item_batches` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `sale_item_id` bigint unsigned NOT NULL,
  `stock_batch_id` bigint unsigned NOT NULL,
  `quantity` int NOT NULL,
  `cost_price` bigint NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `sale_item_batches_sale_item_id_foreign` (`sale_item_id`),
  KEY `sale_item_batches_stock_batch_id_foreign` (`stock_batch_id`),
  CONSTRAINT `sale_item_batches_sale_item_id_foreign` FOREIGN KEY (`sale_item_id`) REFERENCES `sale_items` (`id`) ON DELETE CASCADE,
  CONSTRAINT `sale_item_batches_stock_batch_id_foreign` FOREIGN KEY (`stock_batch_id`) REFERENCES `stock_batches` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `sale_item_batches`
--

LOCK TABLES `sale_item_batches` WRITE;
/*!40000 ALTER TABLE `sale_item_batches` DISABLE KEYS */;
/*!40000 ALTER TABLE `sale_item_batches` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `sale_items`
--

DROP TABLE IF EXISTS `sale_items`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `sale_items` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `sale_id` bigint unsigned NOT NULL,
  `product_id` bigint unsigned NOT NULL,
  `product_variant_id` bigint unsigned DEFAULT NULL,
  `quantity` int NOT NULL,
  `unit_price` bigint NOT NULL,
  `cost_price` bigint NOT NULL DEFAULT '0',
  `discount` bigint NOT NULL DEFAULT '0',
  `subtotal` bigint NOT NULL,
  `total_price` bigint NOT NULL DEFAULT '0',
  `total_cost` bigint NOT NULL DEFAULT '0',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `sale_items_sale_id_foreign` (`sale_id`),
  KEY `sale_items_product_id_foreign` (`product_id`),
  KEY `sale_items_product_variant_id_foreign` (`product_variant_id`),
  KEY `sale_items_created_at_index` (`created_at`),
  CONSTRAINT `sale_items_product_id_foreign` FOREIGN KEY (`product_id`) REFERENCES `products` (`id`) ON DELETE CASCADE,
  CONSTRAINT `sale_items_product_variant_id_foreign` FOREIGN KEY (`product_variant_id`) REFERENCES `product_variants` (`id`) ON DELETE CASCADE,
  CONSTRAINT `sale_items_sale_id_foreign` FOREIGN KEY (`sale_id`) REFERENCES `sales` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `sale_items`
--

LOCK TABLES `sale_items` WRITE;
/*!40000 ALTER TABLE `sale_items` DISABLE KEYS */;
/*!40000 ALTER TABLE `sale_items` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `sale_payments`
--

DROP TABLE IF EXISTS `sale_payments`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `sale_payments` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `sale_id` bigint unsigned NOT NULL,
  `payment_method` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `amount` bigint NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `sale_payments_sale_id_foreign` (`sale_id`),
  CONSTRAINT `sale_payments_sale_id_foreign` FOREIGN KEY (`sale_id`) REFERENCES `sales` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `sale_payments`
--

LOCK TABLES `sale_payments` WRITE;
/*!40000 ALTER TABLE `sale_payments` DISABLE KEYS */;
/*!40000 ALTER TABLE `sale_payments` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `sales`
--

DROP TABLE IF EXISTS `sales`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `sales` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `invoice_number` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `customer_id` bigint unsigned DEFAULT NULL,
  `total_amount` bigint DEFAULT NULL,
  `payment_method` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'Cash',
  `credit_remaining` bigint NOT NULL DEFAULT '0',
  `status` enum('completed','cancelled') COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'completed',
  `payment_status` enum('unpaid','partial','paid','outstanding','completed') COLLATE utf8mb4_unicode_ci DEFAULT 'paid',
  `sale_type` enum('retail','wholesale') COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'retail',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `sales_invoice_number_unique` (`invoice_number`),
  KEY `sales_customer_id_foreign` (`customer_id`),
  KEY `sales_created_at_index` (`created_at`),
  CONSTRAINT `sales_customer_id_foreign` FOREIGN KEY (`customer_id`) REFERENCES `customers` (`id`) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `sales`
--

LOCK TABLES `sales` WRITE;
/*!40000 ALTER TABLE `sales` DISABLE KEYS */;
/*!40000 ALTER TABLE `sales` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `sessions`
--

DROP TABLE IF EXISTS `sessions`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `sessions` (
  `id` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `user_id` bigint unsigned DEFAULT NULL,
  `ip_address` varchar(45) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `user_agent` text COLLATE utf8mb4_unicode_ci,
  `payload` longtext COLLATE utf8mb4_unicode_ci NOT NULL,
  `last_activity` int NOT NULL,
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
INSERT INTO `sessions` VALUES ('AItk3m1J1HrkWIxrPl1EI5cGz0hwCrVFOvFTWgyx',1,'127.0.0.1','Mozilla/5.0 (Macintosh; Intel Mac OS X 10_15_7) AppleWebKit/605.1.15 (KHTML, like Gecko) Version/26.3 Safari/605.1.15','YTo1OntzOjY6Il90b2tlbiI7czo0MDoiS2VtemhicmNuMjdMODJoZnhnWUptb3BsUnFpTVFWY2owaFpITXFKRyI7czozOiJ1cmwiO2E6MTp7czo4OiJpbnRlbmRlZCI7czozMToiaHR0cDovL2xvY2FsaG9zdDo4MDAwL2ludmVudG9yeSI7fXM6OToiX3ByZXZpb3VzIjthOjI6e3M6MzoidXJsIjtzOjMxOiJodHRwOi8vbG9jYWxob3N0OjgwMDAvaW52ZW50b3J5IjtzOjU6InJvdXRlIjtzOjE1OiJpbnZlbnRvcnkuaW5kZXgiO31zOjY6Il9mbGFzaCI7YToyOntzOjM6Im9sZCI7YTowOnt9czozOiJuZXciO2E6MDp7fX1zOjUwOiJsb2dpbl93ZWJfNTliYTM2YWRkYzJiMmY5NDAxNTgwZjAxNGM3ZjU4ZWE0ZTMwOTg5ZCI7aToxO30=',1772460866);
/*!40000 ALTER TABLE `sessions` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `stock_batches`
--

DROP TABLE IF EXISTS `stock_batches`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `stock_batches` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `product_id` bigint unsigned NOT NULL,
  `product_variant_id` bigint unsigned DEFAULT NULL,
  `warehouse_id` bigint unsigned NOT NULL,
  `original_quantity` int NOT NULL,
  `remaining_quantity` int NOT NULL,
  `cost_price` bigint DEFAULT NULL,
  `purchase_date` date NOT NULL,
  `batch_code` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `stock_batches_product_id_foreign` (`product_id`),
  KEY `stock_batches_product_variant_id_foreign` (`product_variant_id`),
  KEY `stock_batches_warehouse_id_foreign` (`warehouse_id`),
  CONSTRAINT `stock_batches_product_id_foreign` FOREIGN KEY (`product_id`) REFERENCES `products` (`id`) ON DELETE CASCADE,
  CONSTRAINT `stock_batches_product_variant_id_foreign` FOREIGN KEY (`product_variant_id`) REFERENCES `product_variants` (`id`) ON DELETE CASCADE,
  CONSTRAINT `stock_batches_warehouse_id_foreign` FOREIGN KEY (`warehouse_id`) REFERENCES `warehouses` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `stock_batches`
--

LOCK TABLES `stock_batches` WRITE;
/*!40000 ALTER TABLE `stock_batches` DISABLE KEYS */;
/*!40000 ALTER TABLE `stock_batches` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `stock_movements`
--

DROP TABLE IF EXISTS `stock_movements`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `stock_movements` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `type` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `product_id` bigint unsigned NOT NULL,
  `product_variant_id` bigint unsigned DEFAULT NULL,
  `from_warehouse_id` bigint unsigned DEFAULT NULL,
  `to_warehouse_id` bigint unsigned DEFAULT NULL,
  `target_product_id` bigint unsigned DEFAULT NULL,
  `target_variant_id` bigint unsigned DEFAULT NULL,
  `quantity` int NOT NULL,
  `reference_code` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `user_id` bigint unsigned DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `stock_movements_product_id_foreign` (`product_id`),
  KEY `stock_movements_product_variant_id_foreign` (`product_variant_id`),
  KEY `stock_movements_from_warehouse_id_foreign` (`from_warehouse_id`),
  KEY `stock_movements_to_warehouse_id_foreign` (`to_warehouse_id`),
  KEY `stock_movements_target_product_id_foreign` (`target_product_id`),
  KEY `stock_movements_target_variant_id_foreign` (`target_variant_id`),
  KEY `stock_movements_user_id_foreign` (`user_id`),
  CONSTRAINT `stock_movements_from_warehouse_id_foreign` FOREIGN KEY (`from_warehouse_id`) REFERENCES `warehouses` (`id`) ON DELETE SET NULL,
  CONSTRAINT `stock_movements_product_id_foreign` FOREIGN KEY (`product_id`) REFERENCES `products` (`id`) ON DELETE CASCADE,
  CONSTRAINT `stock_movements_product_variant_id_foreign` FOREIGN KEY (`product_variant_id`) REFERENCES `product_variants` (`id`) ON DELETE SET NULL,
  CONSTRAINT `stock_movements_target_product_id_foreign` FOREIGN KEY (`target_product_id`) REFERENCES `products` (`id`) ON DELETE SET NULL,
  CONSTRAINT `stock_movements_target_variant_id_foreign` FOREIGN KEY (`target_variant_id`) REFERENCES `product_variants` (`id`) ON DELETE SET NULL,
  CONSTRAINT `stock_movements_to_warehouse_id_foreign` FOREIGN KEY (`to_warehouse_id`) REFERENCES `warehouses` (`id`) ON DELETE SET NULL,
  CONSTRAINT `stock_movements_user_id_foreign` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `stock_movements`
--

LOCK TABLES `stock_movements` WRITE;
/*!40000 ALTER TABLE `stock_movements` DISABLE KEYS */;
/*!40000 ALTER TABLE `stock_movements` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `users`
--

DROP TABLE IF EXISTS `users`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `users` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `account_id` bigint unsigned NOT NULL,
  `role` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'employee',
  `permissions` json DEFAULT NULL,
  `email` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `email_verified_at` timestamp NULL DEFAULT NULL,
  `password` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `remember_token` varchar(100) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `users_account_id_unique` (`account_id`),
  UNIQUE KEY `users_email_unique` (`email`)
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `users`
--

LOCK TABLES `users` WRITE;
/*!40000 ALTER TABLE `users` DISABLE KEYS */;
INSERT INTO `users` VALUES (1,'Admin',999,'admin',NULL,'admin@peace.com',NULL,'$2y$12$F2/t/zxM0e/ibq5mSOncJOUgRz8YAezwIdSETMqbMEZ/1yGDSZ1Zu',NULL,'2026-02-28 15:57:21','2026-02-28 15:57:21');
/*!40000 ALTER TABLE `users` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `warehouses`
--

DROP TABLE IF EXISTS `warehouses`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `warehouses` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `location` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=3 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `warehouses`
--

LOCK TABLES `warehouses` WRITE;
/*!40000 ALTER TABLE `warehouses` DISABLE KEYS */;
INSERT INTO `warehouses` VALUES (1,'Shop','Main Street','2026-02-28 15:57:21','2026-02-28 15:57:54'),(2,'Storage','Industrial Zone','2026-02-28 15:57:21','2026-02-28 15:58:05');
/*!40000 ALTER TABLE `warehouses` ENABLE KEYS */;
UNLOCK TABLES;
/*!40103 SET TIME_ZONE=@OLD_TIME_ZONE */;

/*!40101 SET SQL_MODE=@OLD_SQL_MODE */;
/*!40014 SET FOREIGN_KEY_CHECKS=@OLD_FOREIGN_KEY_CHECKS */;
/*!40014 SET UNIQUE_CHECKS=@OLD_UNIQUE_CHECKS */;
/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
/*!40111 SET SQL_NOTES=@OLD_SQL_NOTES */;

-- Dump completed on 2026-03-02 20:45:40
