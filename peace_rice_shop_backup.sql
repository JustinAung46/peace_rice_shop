-- MySQL dump 10.13  Distrib 9.0.1, for macos14.7 (arm64)
--
-- Host: localhost    Database: peace_rice_shop
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
) ENGINE=InnoDB AUTO_INCREMENT=3 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `categories`
--

LOCK TABLES `categories` WRITE;
/*!40000 ALTER TABLE `categories` DISABLE KEYS */;
INSERT INTO `categories` VALUES (1,'Paw San',NULL,'2026-02-22 07:56:24','2026-02-22 07:56:24'),(2,'Oil',NULL,'2026-02-25 15:17:26','2026-02-25 15:17:26');
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
) ENGINE=InnoDB AUTO_INCREMENT=4 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `credit_allocations`
--

LOCK TABLES `credit_allocations` WRITE;
/*!40000 ALTER TABLE `credit_allocations` DISABLE KEYS */;
INSERT INTO `credit_allocations` VALUES (1,4,16,200000,'2026-02-28 07:57:56','2026-02-28 07:57:56'),(2,4,17,1000,'2026-02-28 07:57:56','2026-02-28 07:57:56'),(3,5,17,9000,'2026-02-28 08:09:53','2026-02-28 08:09:53');
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
) ENGINE=InnoDB AUTO_INCREMENT=6 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `credit_payments`
--

LOCK TABLES `credit_payments` WRITE;
/*!40000 ALTER TABLE `credit_payments` DISABLE KEYS */;
INSERT INTO `credit_payments` VALUES (1,2,10000,'Cash','2026-02-26 11:19:04','2026-02-26 11:19:04'),(2,2,1000,NULL,'2026-02-27 05:49:43','2026-02-27 05:49:43'),(3,3,201000,NULL,'2026-02-28 07:57:41','2026-02-28 07:57:41'),(4,4,201000,NULL,'2026-02-28 07:57:56','2026-02-28 07:57:56'),(5,4,9000,NULL,'2026-02-28 08:09:53','2026-02-28 08:09:53');
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
) ENGINE=InnoDB AUTO_INCREMENT=5 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `customers`
--

LOCK TABLES `customers` WRITE;
/*!40000 ALTER TABLE `customers` DISABLE KEYS */;
INSERT INTO `customers` VALUES (2,'Jojn','09784127041',249000,'2026-02-22 08:08:40','2026-02-27 05:49:43','Butar lan'),(3,'Tester','09874384334',86000,'2026-02-28 06:37:27','2026-02-28 07:57:41',NULL),(4,'credit tester',NULL,0,'2026-02-28 07:52:33','2026-02-28 08:09:53',NULL);
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
) ENGINE=InnoDB AUTO_INCREMENT=9 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `migrations`
--

LOCK TABLES `migrations` WRITE;
/*!40000 ALTER TABLE `migrations` DISABLE KEYS */;
INSERT INTO `migrations` VALUES (1,'0001_01_01_000000_baseline_migration',1),(2,'2026_02_24_140545_change_decimals_to_integers_in_inventory_and_pos',2),(3,'2026_02_24_143544_drop_redundant_columns_from_products_table',3),(4,'2026_02_25_000001_create_product_variants_table',4),(5,'2026_02_26_165333_add_is_active_to_products_and_variants',5),(6,'2026_02_26_173745_create_credit_payments_table',6),(7,'2026_02_28_000000_update_sales_payment_status_enum',7),(8,'2026_02_28_141617_create_credit_allocations_and_update_sales_table',8);
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
) ENGINE=InnoDB AUTO_INCREMENT=13 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `product_variants`
--

LOCK TABLES `product_variants` WRITE;
/*!40000 ALTER TABLE `product_variants` DISABLE KEYS */;
INSERT INTO `product_variants` VALUES (1,3,'1 Viss',NULL,'Bottle',12000,NULL,NULL,1,'2026-02-25 15:21:38','2026-02-25 15:21:38'),(2,3,'3 Viss',NULL,'Bottle',35000,NULL,NULL,1,'2026-02-25 15:21:38','2026-02-25 15:21:38'),(3,1,'New 6 Pyi',NULL,'Bag',60000,6,NULL,1,'2026-02-25 15:32:57','2026-02-26 10:48:50'),(4,1,'Old 6 Pyi',NULL,'Bag',70000,6,NULL,1,'2026-02-25 15:33:38','2026-02-26 10:48:50'),(5,2,'Old 24 Pyi',NULL,'Bag',275000,24,NULL,1,'2026-02-25 15:35:26','2026-02-26 10:50:18'),(6,2,'New 24 Pyi',NULL,'Bag',200000,24,NULL,1,'2026-02-25 15:38:18','2026-02-26 10:50:18'),(7,4,'New 24 Pyi',NULL,'Bag',250000,24,12000,1,'2026-02-25 15:59:22','2026-02-25 15:59:22'),(8,1,'New 12 Pyi',NULL,'Bag',120000,12,NULL,1,'2026-02-26 08:39:59','2026-02-26 10:48:50'),(9,1,'New 24 Pyi',NULL,'Bag',240000,24,12000,1,'2026-02-26 09:36:24','2026-02-26 10:48:50'),(10,1,'efere',NULL,'ere',34343,NULL,NULL,0,'2026-02-26 09:36:42','2026-02-26 10:48:50'),(11,1,'334ere',NULL,'rere',33434,NULL,NULL,0,'2026-02-26 09:36:42','2026-02-26 10:48:50'),(12,1,'ererere',NULL,'3r3',3434,NULL,NULL,0,'2026-02-26 09:36:42','2026-02-26 10:48:50');
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
) ENGINE=InnoDB AUTO_INCREMENT=5 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `products`
--

LOCK TABLES `products` WRITE;
/*!40000 ALTER TABLE `products` DISABLE KEYS */;
INSERT INTO `products` VALUES (1,'NC Paw San',1,1,NULL,'products/5AW15gRhBWSWDuBiaNa8mM8jEtAmewqPjJmeEQro.jpg','2026-02-22 07:56:57','2026-02-26 10:48:50'),(2,'Raw Paw San',1,0,NULL,NULL,'2026-02-22 07:57:45','2026-02-26 10:50:18'),(3,'Shwe Mandalay',2,1,NULL,NULL,'2026-02-25 15:21:38','2026-02-25 15:21:38'),(4,'ATH Paw San',1,1,NULL,NULL,'2026-02-25 15:59:22','2026-02-25 15:59:22');
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
) ENGINE=InnoDB AUTO_INCREMENT=25 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `sale_item_batches`
--

LOCK TABLES `sale_item_batches` WRITE;
/*!40000 ALTER TABLE `sale_item_batches` DISABLE KEYS */;
INSERT INTO `sale_item_batches` VALUES (1,1,4,1,200000,'2026-02-22 08:14:10','2026-02-22 08:14:10'),(2,1,6,1,200000,'2026-02-22 08:14:10','2026-02-22 08:14:10'),(3,2,7,2,10000,'2026-02-25 15:23:02','2026-02-25 15:23:02'),(4,3,8,1,33000,'2026-02-25 15:23:02','2026-02-25 15:23:02'),(5,4,12,2,50000,'2026-02-25 15:44:09','2026-02-25 15:44:09'),(6,5,14,1,220000,'2026-02-26 10:43:11','2026-02-26 10:43:11'),(7,6,14,1,220000,'2026-02-26 10:43:52','2026-02-26 10:43:52'),(8,7,10,1,62500,'2026-02-26 11:13:49','2026-02-26 11:13:49'),(9,8,12,2,50000,'2026-02-26 12:10:11','2026-02-26 12:10:11'),(10,9,12,2,50000,'2026-02-26 14:59:31','2026-02-26 14:59:31'),(11,10,10,1,62500,'2026-02-26 14:59:31','2026-02-26 14:59:31'),(12,11,7,2,10000,'2026-02-26 14:59:31','2026-02-26 14:59:31'),(13,12,12,1,50000,'2026-02-26 15:43:35','2026-02-26 15:43:35'),(14,13,8,1,33000,'2026-02-27 05:23:11','2026-02-27 05:23:11'),(15,14,22,1,33000,'2026-02-27 05:23:11','2026-02-27 05:23:11'),(16,15,12,1,50000,'2026-02-27 09:07:28','2026-02-27 09:07:28'),(17,16,10,1,62500,'2026-02-27 09:07:28','2026-02-27 09:07:28'),(18,17,14,2,220000,'2026-02-28 06:51:44','2026-02-28 06:51:44'),(19,18,7,1,10000,'2026-02-28 07:31:56','2026-02-28 07:31:56'),(20,19,17,1,100000,'2026-02-28 07:33:13','2026-02-28 07:33:13'),(21,20,17,1,100000,'2026-02-28 07:37:09','2026-02-28 07:37:09'),(22,21,18,1,230000,'2026-02-28 07:52:55','2026-02-28 07:52:55'),(23,22,7,1,10000,'2026-02-28 07:54:21','2026-02-28 07:54:21'),(24,23,23,1,220000,'2026-02-28 15:44:23','2026-02-28 15:44:23');
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
  KEY `sale_items_created_at_index` (`created_at`),
  KEY `sale_items_product_variant_id_foreign` (`product_variant_id`),
  CONSTRAINT `sale_items_product_id_foreign` FOREIGN KEY (`product_id`) REFERENCES `products` (`id`) ON DELETE CASCADE,
  CONSTRAINT `sale_items_product_variant_id_foreign` FOREIGN KEY (`product_variant_id`) REFERENCES `product_variants` (`id`) ON DELETE CASCADE,
  CONSTRAINT `sale_items_sale_id_foreign` FOREIGN KEY (`sale_id`) REFERENCES `sales` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=24 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `sale_items`
--

LOCK TABLES `sale_items` WRITE;
/*!40000 ALTER TABLE `sale_items` DISABLE KEYS */;
INSERT INTO `sale_items` VALUES (1,1,1,NULL,2,275000,200000,0,550000,550000,400000,'2026-02-22 08:14:10','2026-02-22 08:14:10'),(2,2,3,1,2,12000,10000,0,24000,24000,20000,'2026-02-25 15:23:02','2026-02-25 15:23:02'),(3,2,3,2,1,35000,33000,0,35000,35000,33000,'2026-02-25 15:23:02','2026-02-25 15:23:02'),(4,3,1,3,2,60000,50000,0,120000,120000,100000,'2026-02-25 15:44:09','2026-02-25 15:44:09'),(5,4,1,9,1,288000,220000,0,288000,288000,220000,'2026-02-26 10:43:11','2026-02-26 10:43:11'),(6,5,1,9,1,240000,220000,0,240000,240000,220000,'2026-02-26 10:43:52','2026-02-26 10:43:52'),(7,6,1,4,1,70000,62500,0,70000,70000,62500,'2026-02-26 11:13:49','2026-02-26 11:13:49'),(8,7,1,3,2,60000,50000,1000,120000,119000,100000,'2026-02-26 12:10:11','2026-02-26 12:10:11'),(9,8,1,3,2,60000,50000,1000,120000,119000,100000,'2026-02-26 14:59:31','2026-02-26 14:59:31'),(10,8,1,4,1,70000,62500,0,70000,70000,62500,'2026-02-26 14:59:31','2026-02-26 14:59:31'),(11,8,3,1,2,12000,10000,0,24000,24000,20000,'2026-02-26 14:59:31','2026-02-26 14:59:31'),(12,9,1,3,1,60000,50000,0,60000,60000,50000,'2026-02-26 15:43:35','2026-02-26 15:43:35'),(13,10,3,2,1,35000,33000,0,35000,35000,33000,'2026-02-27 05:23:11','2026-02-27 05:23:11'),(14,10,3,2,1,35000,33000,0,35000,35000,33000,'2026-02-27 05:23:11','2026-02-27 05:23:11'),(15,11,1,3,1,60000,50000,0,60000,60000,50000,'2026-02-27 09:07:27','2026-02-27 09:07:28'),(16,11,1,4,1,70000,62500,0,70000,70000,62500,'2026-02-27 09:07:28','2026-02-27 09:07:28'),(17,12,1,9,2,240000,220000,5000,480000,475000,440000,'2026-02-28 06:51:44','2026-02-28 06:51:44'),(18,13,3,1,1,12000,10000,0,12000,12000,10000,'2026-02-28 07:31:56','2026-02-28 07:31:56'),(19,14,1,8,1,120000,100000,0,120000,120000,100000,'2026-02-28 07:33:13','2026-02-28 07:33:13'),(20,15,1,8,1,120000,100000,0,120000,120000,100000,'2026-02-28 07:37:09','2026-02-28 07:37:09'),(21,16,4,7,1,250000,230000,0,250000,250000,230000,'2026-02-28 07:52:55','2026-02-28 07:52:55'),(22,17,3,1,1,12000,10000,0,12000,12000,10000,'2026-02-28 07:54:21','2026-02-28 07:54:21'),(23,18,1,9,1,240000,220000,0,240000,240000,220000,'2026-02-28 15:44:23','2026-02-28 15:44:23');
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
) ENGINE=InnoDB AUTO_INCREMENT=27 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `sale_payments`
--

LOCK TABLES `sale_payments` WRITE;
/*!40000 ALTER TABLE `sale_payments` DISABLE KEYS */;
INSERT INTO `sale_payments` VALUES (1,1,'Cash',550000,'2026-02-22 08:14:10','2026-02-22 08:14:10'),(2,2,'Cash',59000,'2026-02-25 15:23:02','2026-02-25 15:23:02'),(3,3,'Cash',120000,'2026-02-25 15:44:09','2026-02-25 15:44:09'),(4,4,'Cash',288000,'2026-02-26 10:43:11','2026-02-26 10:43:11'),(5,5,'Cash',240000,'2026-02-26 10:43:52','2026-02-26 10:43:52'),(6,6,'Credit',20000,'2026-02-26 11:13:49','2026-02-26 11:13:49'),(7,6,'Cash',50000,'2026-02-26 11:13:49','2026-02-26 11:13:49'),(8,7,'Cash',10000,'2026-02-26 12:10:11','2026-02-26 12:10:11'),(9,7,'Kpay',109000,'2026-02-26 12:10:11','2026-02-26 12:10:11'),(10,8,'Cash',100000,'2026-02-26 14:59:31','2026-02-26 14:59:31'),(11,8,'Kpay',113000,'2026-02-26 14:59:31','2026-02-26 14:59:31'),(12,9,'Cash',60000,'2026-02-26 15:43:35','2026-02-26 15:43:35'),(13,10,'Cash',70000,'2026-02-27 05:23:11','2026-02-27 05:23:11'),(14,11,'Cash',20000,'2026-02-27 09:07:28','2026-02-27 09:07:28'),(15,11,'Kpay',110000,'2026-02-27 09:07:28','2026-02-27 09:07:28'),(16,12,'Kpay',100000,'2026-02-28 06:51:44','2026-02-28 06:51:44'),(17,12,'Cash',100000,'2026-02-28 06:51:44','2026-02-28 06:51:44'),(18,12,'Credit',275000,'2026-02-28 06:51:44','2026-02-28 06:51:44'),(19,13,'Credit',12000,'2026-02-28 07:31:56','2026-02-28 07:31:56'),(20,14,'Credit',120000,'2026-02-28 07:33:13','2026-02-28 07:33:13'),(21,15,'Credit',120000,'2026-02-28 07:37:09','2026-02-28 07:37:09'),(22,16,'Cash',50000,'2026-02-28 07:52:55','2026-02-28 07:52:55'),(23,16,'Credit',200000,'2026-02-28 07:52:55','2026-02-28 07:52:55'),(24,17,'Cash',2000,'2026-02-28 07:54:21','2026-02-28 07:54:21'),(25,17,'Credit',10000,'2026-02-28 07:54:21','2026-02-28 07:54:21'),(26,18,'Cash',240000,'2026-02-28 15:44:23','2026-02-28 15:44:23');
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
) ENGINE=InnoDB AUTO_INCREMENT=19 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `sales`
--

LOCK TABLES `sales` WRITE;
/*!40000 ALTER TABLE `sales` DISABLE KEYS */;
INSERT INTO `sales` VALUES (1,'INV-20260222-1',NULL,550000,'Cash',0,'completed','paid','retail','2026-02-22 08:14:10','2026-02-22 08:14:10'),(2,'INV-20260225-1',NULL,59000,'Cash',0,'completed','paid','retail','2026-02-25 15:23:02','2026-02-25 15:23:02'),(3,'INV-20260225-2',NULL,120000,'Cash',0,'completed','paid','retail','2026-02-25 15:44:09','2026-02-25 15:44:09'),(4,'INV-20260226-1',NULL,288000,'Cash',0,'completed','paid','retail','2026-02-26 10:43:11','2026-02-26 10:43:11'),(5,'INV-20260226-2',NULL,240000,'Cash',0,'completed','paid','retail','2026-02-26 10:43:52','2026-02-26 10:43:52'),(6,'INV-20260226-3',2,70000,'Multi',0,'completed','paid','retail','2026-02-26 11:13:49','2026-02-26 11:13:49'),(7,'INV-20260226-4',NULL,119000,'Multi',0,'completed','paid','retail','2026-02-26 12:10:11','2026-02-26 12:10:11'),(8,'INV-20260226-5',NULL,213000,'Multi',0,'completed','paid','retail','2026-02-26 14:59:31','2026-02-26 14:59:31'),(9,'INV-20260226-0006',NULL,60000,'Cash',0,'completed','paid','retail','2026-02-26 15:43:35','2026-02-26 15:43:35'),(10,'INV-20260227-1',NULL,70000,'Cash',0,'completed','paid','retail','2026-02-27 05:23:11','2026-02-27 05:23:11'),(11,'INV-20260227-2',NULL,130000,'Multi',0,'completed','paid','retail','2026-02-27 09:07:27','2026-02-27 09:07:28'),(12,'INV-20260228-1',3,475000,'Multi',0,'completed','paid','retail','2026-02-28 06:51:44','2026-02-28 06:51:44'),(13,'INV-20260228-2',3,12000,'Credit',0,'completed','paid','retail','2026-02-28 07:31:56','2026-02-28 07:31:56'),(14,'INV-20260228-3',2,120000,'Credit',0,'completed','paid','retail','2026-02-28 07:33:13','2026-02-28 07:33:13'),(15,'INV-20260228-4',2,120000,'Credit',0,'completed','paid','wholesale','2026-02-28 07:37:09','2026-02-28 07:37:09'),(16,'INV-20260228-5',4,250000,'Multi',0,'completed','paid','wholesale','2026-02-28 07:52:55','2026-02-28 07:57:56'),(17,'INV-20260228-6',4,12000,'Multi',0,'completed','paid','wholesale','2026-02-28 07:54:21','2026-02-28 08:09:53'),(18,'INV-20260228-7',NULL,240000,'Cash',0,'completed','paid','wholesale','2026-02-28 15:44:23','2026-02-28 15:44:23');
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
INSERT INTO `sessions` VALUES ('CX7r4ObcfCKZ8THmiYkjSXz2ofgHoOU6xQihNeei',1,'127.0.0.1','Mozilla/5.0 (Linux; Android 6.0.1; Nexus 10 Build/MOB31T) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/67.0.3396.87 Safari/537.36','YTo1OntzOjY6Il90b2tlbiI7czo0MDoidHhXdHAwb3d2T1V2WFpDS3BnNFNtUU1ldHlMbWV4YmZkRlBwY3FnaiI7czozOiJ1cmwiO2E6MTp7czo4OiJpbnRlbmRlZCI7czoyOToiaHR0cDovL2xvY2FsaG9zdDo4MDAwL2NyZWRpdHMiO31zOjk6Il9wcmV2aW91cyI7YToyOntzOjM6InVybCI7czoyNToiaHR0cDovL2xvY2FsaG9zdDo4MDAwL3BvcyI7czo1OiJyb3V0ZSI7czo5OiJwb3MuaW5kZXgiO31zOjY6Il9mbGFzaCI7YToyOntzOjM6Im9sZCI7YTowOnt9czozOiJuZXciO2E6MDp7fX1zOjUwOiJsb2dpbl93ZWJfNTliYTM2YWRkYzJiMmY5NDAxNTgwZjAxNGM3ZjU4ZWE0ZTMwOTg5ZCI7aToxO30=',1772293465),('fLhMZrw1eAakI6fLRaDdHmQEuGRPiy1DWLhJZi2T',1,'127.0.0.1','Mozilla/5.0 (Macintosh; Intel Mac OS X 10.15; rv:148.0) Gecko/20100101 Firefox/148.0','YTo1OntzOjY6Il90b2tlbiI7czo0MDoiT1gzeXdBb1R3ckZNVllia1J3aE90Tm52aGZ0enlxOGdBWVVtSjJKQyI7czozOiJ1cmwiO2E6MTp7czo4OiJpbnRlbmRlZCI7czoyMToiaHR0cDovL2xvY2FsaG9zdDo4MDAwIjt9czo5OiJfcHJldmlvdXMiO2E6Mjp7czozOiJ1cmwiO3M6MzE6Imh0dHA6Ly9sb2NhbGhvc3Q6ODAwMC9pbnZlbnRvcnkiO3M6NToicm91dGUiO3M6MTU6ImludmVudG9yeS5pbmRleCI7fXM6NjoiX2ZsYXNoIjthOjI6e3M6Mzoib2xkIjthOjA6e31zOjM6Im5ldyI7YTowOnt9fXM6NTA6ImxvZ2luX3dlYl81OWJhMzZhZGRjMmIyZjk0MDE1ODBmMDE0YzdmNThlYTRlMzA5ODlkIjtpOjE7fQ==',1772266706),('iDISKoIx3HgHkGUImWN4Wg5o63ZjHxctoym7zIwG',1,'127.0.0.1','Mozilla/5.0 (Macintosh; Intel Mac OS X 10_15_7) AppleWebKit/605.1.15 (KHTML, like Gecko) Version/26.3 Safari/605.1.15','YTo1OntzOjY6Il90b2tlbiI7czo0MDoieW1uRVAwOVZhbXJPWTF4MDZNUlE4S0dzMEZBbVl0bDdPUjBHeFp5WSI7czozOiJ1cmwiO2E6MTp7czo4OiJpbnRlbmRlZCI7czoyMToiaHR0cDovL2xvY2FsaG9zdDo4MDAwIjt9czo5OiJfcHJldmlvdXMiO2E6Mjp7czozOiJ1cmwiO3M6MjU6Imh0dHA6Ly9sb2NhbGhvc3Q6ODAwMC9wb3MiO3M6NToicm91dGUiO3M6OToicG9zLmluZGV4Ijt9czo2OiJfZmxhc2giO2E6Mjp7czozOiJvbGQiO2E6MDp7fXM6MzoibmV3IjthOjA6e319czo1MDoibG9naW5fd2ViXzU5YmEzNmFkZGMyYjJmOTQwMTU4MGYwMTRjN2Y1OGVhNGUzMDk4OWQiO2k6MTt9',1772293432);
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
  KEY `stock_batches_warehouse_id_foreign` (`warehouse_id`),
  KEY `stock_batches_product_variant_id_foreign` (`product_variant_id`),
  CONSTRAINT `stock_batches_product_id_foreign` FOREIGN KEY (`product_id`) REFERENCES `products` (`id`) ON DELETE CASCADE,
  CONSTRAINT `stock_batches_product_variant_id_foreign` FOREIGN KEY (`product_variant_id`) REFERENCES `product_variants` (`id`) ON DELETE CASCADE,
  CONSTRAINT `stock_batches_warehouse_id_foreign` FOREIGN KEY (`warehouse_id`) REFERENCES `warehouses` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=24 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `stock_batches`
--

LOCK TABLES `stock_batches` WRITE;
/*!40000 ALTER TABLE `stock_batches` DISABLE KEYS */;
INSERT INTO `stock_batches` VALUES (1,1,NULL,2,5,0,200000,'2026-02-22',NULL,'2026-02-22 08:01:31','2026-02-22 08:14:08'),(2,2,NULL,2,8,8,50000,'2026-02-22','TR-1771747314','2026-02-22 08:01:54','2026-02-22 08:01:54'),(3,1,NULL,1,1,0,200000,'2026-02-22',NULL,'2026-02-22 08:03:47','2026-02-22 08:05:14'),(4,1,NULL,1,1,0,200000,'2026-02-22',NULL,'2026-02-22 08:04:45','2026-02-22 08:14:10'),(5,2,NULL,1,4,4,50000,'2026-02-22','TR-1771747514','2026-02-22 08:05:14','2026-02-22 08:05:14'),(6,1,NULL,1,1,0,200000,'2026-02-22',NULL,'2026-02-22 08:14:08','2026-02-22 08:14:10'),(7,3,1,1,10,4,10000,'2026-02-25',NULL,'2026-02-25 15:22:04','2026-02-28 07:54:21'),(8,3,2,1,20,18,33000,'2026-02-25',NULL,'2026-02-25 15:22:33','2026-02-27 05:23:11'),(9,2,5,1,10,8,250000,'2026-02-25',NULL,'2026-02-25 15:36:48','2026-02-25 15:37:13'),(10,1,4,1,8,5,62500,'2026-02-25','TR-1772033833','2026-02-25 15:37:13','2026-02-27 09:07:28'),(11,2,6,1,5,3,200000,'2026-02-25',NULL,'2026-02-25 15:38:34','2026-02-25 15:39:06'),(12,1,3,1,8,0,50000,'2026-02-25','TR-1772033946','2026-02-25 15:39:06','2026-02-27 09:07:27'),(13,4,7,2,5,5,220000,'2026-02-25',NULL,'2026-02-25 15:59:42','2026-02-25 15:59:42'),(14,1,9,1,10,5,220000,'2026-02-26',NULL,'2026-02-26 10:39:35','2026-02-28 15:44:23'),(15,3,1,1,10,10,10000,'2026-02-27',NULL,'2026-02-27 04:22:39','2026-02-27 04:22:39'),(16,3,2,1,5,5,33000,'2026-02-27',NULL,'2026-02-27 04:26:06','2026-02-27 04:26:06'),(17,1,8,1,3,1,100000,'2026-02-27',NULL,'2026-02-27 04:26:47','2026-02-28 07:37:09'),(18,4,7,1,5,4,230000,'2026-02-27',NULL,'2026-02-27 04:36:36','2026-02-28 07:52:55'),(19,4,7,1,5,5,230000,'2026-02-27',NULL,'2026-02-27 04:37:28','2026-02-27 04:37:28'),(20,4,7,2,6,6,230000,'2026-02-27',NULL,'2026-02-27 04:37:51','2026-02-27 04:37:51'),(21,3,1,1,3,3,10000,'2026-02-27',NULL,'2026-02-27 04:38:28','2026-02-27 04:38:28'),(22,3,2,2,3,2,33000,'2026-02-27',NULL,'2026-02-27 04:39:03','2026-02-27 05:23:11'),(23,1,9,2,1,0,220000,'2026-02-26',NULL,'2026-02-28 15:44:23','2026-02-28 15:44:23');
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
  KEY `stock_movements_from_warehouse_id_foreign` (`from_warehouse_id`),
  KEY `stock_movements_to_warehouse_id_foreign` (`to_warehouse_id`),
  KEY `stock_movements_target_product_id_foreign` (`target_product_id`),
  KEY `stock_movements_user_id_foreign` (`user_id`),
  KEY `stock_movements_product_variant_id_foreign` (`product_variant_id`),
  KEY `stock_movements_target_variant_id_foreign` (`target_variant_id`),
  CONSTRAINT `stock_movements_from_warehouse_id_foreign` FOREIGN KEY (`from_warehouse_id`) REFERENCES `warehouses` (`id`) ON DELETE SET NULL,
  CONSTRAINT `stock_movements_product_id_foreign` FOREIGN KEY (`product_id`) REFERENCES `products` (`id`) ON DELETE CASCADE,
  CONSTRAINT `stock_movements_product_variant_id_foreign` FOREIGN KEY (`product_variant_id`) REFERENCES `product_variants` (`id`) ON DELETE SET NULL,
  CONSTRAINT `stock_movements_target_product_id_foreign` FOREIGN KEY (`target_product_id`) REFERENCES `products` (`id`) ON DELETE SET NULL,
  CONSTRAINT `stock_movements_target_variant_id_foreign` FOREIGN KEY (`target_variant_id`) REFERENCES `product_variants` (`id`) ON DELETE SET NULL,
  CONSTRAINT `stock_movements_to_warehouse_id_foreign` FOREIGN KEY (`to_warehouse_id`) REFERENCES `warehouses` (`id`) ON DELETE SET NULL,
  CONSTRAINT `stock_movements_user_id_foreign` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE SET NULL
) ENGINE=InnoDB AUTO_INCREMENT=23 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `stock_movements`
--

LOCK TABLES `stock_movements` WRITE;
/*!40000 ALTER TABLE `stock_movements` DISABLE KEYS */;
INSERT INTO `stock_movements` VALUES (1,'bag_transformation',1,NULL,2,2,2,NULL,2,NULL,1,'2026-02-22 08:01:54','2026-02-22 08:01:54'),(2,'warehouse_transfer',1,NULL,2,1,NULL,NULL,1,NULL,1,'2026-02-22 08:03:47','2026-02-22 08:03:47'),(3,'warehouse_transfer',1,NULL,2,1,NULL,NULL,1,NULL,1,'2026-02-22 08:04:45','2026-02-22 08:04:45'),(4,'bag_transformation',1,NULL,1,1,2,NULL,1,NULL,1,'2026-02-22 08:05:14','2026-02-22 08:05:14'),(5,'warehouse_transfer',1,NULL,2,1,NULL,NULL,1,NULL,1,'2026-02-22 08:14:08','2026-02-22 08:14:08'),(6,'in',3,NULL,NULL,1,NULL,NULL,10,NULL,1,'2026-02-25 15:22:04','2026-02-25 15:22:04'),(7,'in',3,NULL,NULL,1,NULL,NULL,20,NULL,1,'2026-02-25 15:22:33','2026-02-25 15:22:33'),(8,'in',2,NULL,NULL,1,NULL,NULL,10,NULL,1,'2026-02-25 15:36:48','2026-02-25 15:36:48'),(9,'bag_transformation',2,NULL,1,1,1,NULL,2,NULL,1,'2026-02-25 15:37:13','2026-02-25 15:37:13'),(10,'in',2,NULL,NULL,1,NULL,NULL,5,NULL,1,'2026-02-25 15:38:34','2026-02-25 15:38:34'),(11,'bag_transformation',2,NULL,1,1,1,NULL,2,NULL,1,'2026-02-25 15:39:06','2026-02-25 15:39:06'),(12,'in',4,NULL,NULL,2,NULL,NULL,5,NULL,1,'2026-02-25 15:59:42','2026-02-25 15:59:42'),(13,'in',1,NULL,NULL,1,NULL,NULL,10,NULL,1,'2026-02-26 10:39:35','2026-02-26 10:39:35'),(14,'in',3,NULL,NULL,1,NULL,NULL,10,NULL,1,'2026-02-27 04:22:39','2026-02-27 04:22:39'),(15,'in',3,NULL,NULL,1,NULL,NULL,5,NULL,1,'2026-02-27 04:26:06','2026-02-27 04:26:06'),(16,'in',1,NULL,NULL,1,NULL,NULL,3,NULL,1,'2026-02-27 04:26:47','2026-02-27 04:26:47'),(17,'in',4,NULL,NULL,1,NULL,NULL,5,NULL,1,'2026-02-27 04:36:36','2026-02-27 04:36:36'),(18,'in',4,NULL,NULL,1,NULL,NULL,5,NULL,1,'2026-02-27 04:37:28','2026-02-27 04:37:28'),(19,'in',4,NULL,NULL,2,NULL,NULL,6,NULL,1,'2026-02-27 04:37:51','2026-02-27 04:37:51'),(20,'in',3,NULL,NULL,1,NULL,NULL,3,NULL,1,'2026-02-27 04:38:28','2026-02-27 04:38:28'),(21,'in',3,NULL,NULL,2,NULL,NULL,3,NULL,1,'2026-02-27 04:39:03','2026-02-27 04:39:03'),(22,'warehouse_transfer',1,NULL,1,2,NULL,NULL,1,NULL,1,'2026-02-28 15:44:23','2026-02-28 15:44:23');
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
INSERT INTO `users` VALUES (1,'Admin',999,'admin',NULL,'admin@peace.com',NULL,'$2y$12$rzbc6io7sVWY3rnvu0W.MOlclGeuMVfGY14bY4d4RTDBbGCDInyN2',NULL,'2026-02-22 07:54:27','2026-02-22 07:54:27');
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
INSERT INTO `warehouses` VALUES (1,'Shop','Main Street','2026-02-22 07:54:27','2026-02-27 05:14:47'),(2,'Storage','Industrial Zone','2026-02-22 07:54:27','2026-02-27 05:14:55');
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

-- Dump completed on 2026-02-28 22:18:20
