/*!40103 SET @OLD_TIME_ZONE=@@TIME_ZONE */;
/*!40103 SET TIME_ZONE='+00:00' */;
/*!40014 SET @OLD_UNIQUE_CHECKS=@@UNIQUE_CHECKS, UNIQUE_CHECKS=0 */;
/*!40014 SET @OLD_FOREIGN_KEY_CHECKS=@@FOREIGN_KEY_CHECKS, FOREIGN_KEY_CHECKS=0 */;
/*!40101 SET @OLD_SQL_MODE=@@SQL_MODE, SQL_MODE='NO_AUTO_VALUE_ON_ZERO' */;
/*!40111 SET @OLD_SQL_NOTES=@@SQL_NOTES, SQL_NOTES=0 */;
DROP TABLE IF EXISTS `aduanas`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `aduanas` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `description` varchar(255) COLLATE utf8_unicode_ci DEFAULT NULL,
  `pais` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `lat` varchar(255) COLLATE utf8_unicode_ci DEFAULT NULL,
  `lon` varchar(255) COLLATE utf8_unicode_ci DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  `provincia` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `km_from_town` int(11) NOT NULL,
  `link_maps` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;
DROP TABLE IF EXISTS `agencias`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `agencias` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `description` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `razon_social` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `tax_id` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `puerto` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `contact_name` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `contact_phone` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `contact_mail` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  `user` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `empresa` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `observation_gral` text COLLATE utf8_unicode_ci NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;
DROP TABLE IF EXISTS `asign`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `asign` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `driver` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `cntr_number` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `booking` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `truck` varchar(255) COLLATE utf8_unicode_ci DEFAULT NULL,
  `truck_semi` varchar(255) COLLATE utf8_unicode_ci DEFAULT NULL,
  `transport` varchar(255) COLLATE utf8_unicode_ci DEFAULT NULL,
  `transport_agent` varchar(255) COLLATE utf8_unicode_ci DEFAULT NULL,
  `crt` varchar(255) COLLATE utf8_unicode_ci DEFAULT NULL,
  `fletero_razon_social` varchar(255) COLLATE utf8_unicode_ci DEFAULT NULL,
  `fletero_cuit` varchar(255) COLLATE utf8_unicode_ci DEFAULT NULL,
  `fletero_domicilio` varchar(255) COLLATE utf8_unicode_ci DEFAULT NULL,
  `fletero_paut` varchar(255) COLLATE utf8_unicode_ci DEFAULT NULL,
  `fletero_permiso` varchar(255) COLLATE utf8_unicode_ci DEFAULT NULL,
  `fletero_vto_permiso` date DEFAULT NULL,
  `observation_load` text COLLATE utf8_unicode_ci DEFAULT NULL,
  `file_instruction` varchar(255) COLLATE utf8_unicode_ci DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  `user` varchar(255) COLLATE utf8_unicode_ci NOT NULL DEFAULT 'TTL',
  `company` varchar(255) COLLATE utf8_unicode_ci DEFAULT NULL,
  `agent_port` varchar(255) COLLATE utf8_unicode_ci DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `cntr_number` (`cntr_number`),
  KEY `cntr_number_2` (`cntr_number`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;
DROP TABLE IF EXISTS `calification_customer`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `calification_customer` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `calification_customer` int(2) NOT NULL,
  `cntr_number` varchar(11) COLLATE utf8_unicode_ci NOT NULL,
  `booking` varchar(20) COLLATE utf8_unicode_ci NOT NULL,
  `Created_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  `user` varchar(20) COLLATE utf8_unicode_ci NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;
DROP TABLE IF EXISTS `calification_driver`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `calification_driver` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `calification_driver` int(2) NOT NULL,
  `cntr_number` varchar(11) COLLATE utf8_unicode_ci NOT NULL,
  `booking` varchar(20) COLLATE utf8_unicode_ci NOT NULL,
  `Created_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  `user` varchar(20) COLLATE utf8_unicode_ci NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;
DROP TABLE IF EXISTS `calification_transport`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `calification_transport` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `calification_transport` int(2) NOT NULL,
  `cntr_number` varchar(11) COLLATE utf8_unicode_ci NOT NULL,
  `booking` varchar(20) COLLATE utf8_unicode_ci NOT NULL,
  `Created_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  `user` varchar(20) COLLATE utf8_unicode_ci NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;
DROP TABLE IF EXISTS `carga`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `carga` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `booking` varchar(20) COLLATE utf8_unicode_ci NOT NULL,
  `bl_hbl` varchar(255) COLLATE utf8_unicode_ci DEFAULT NULL,
  `shipper` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `commodity` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `load_place` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `trader` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `importador` varchar(255) COLLATE utf8_unicode_ci DEFAULT NULL,
  `load_date` date NOT NULL,
  `unload_place` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `cut_off_fis` datetime NOT NULL,
  `cut_off_doc` date NOT NULL,
  `oceans_line` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `vessel` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `voyage` varchar(11) COLLATE utf8_unicode_ci NOT NULL,
  `final_point` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `ETA` date NOT NULL,
  `ETD` date NOT NULL,
  `consignee` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `notify` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `custom_place` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `custom_agent` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `custom_place_impo` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `custom_agent_impo` int(11) NOT NULL,
  `ref_customer` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `senasa` varchar(5) COLLATE utf8_unicode_ci DEFAULT NULL,
  `senasa_string` varchar(255) COLLATE utf8_unicode_ci DEFAULT NULL,
  `referencia_carga` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `comercial_reference` varchar(255) COLLATE utf8_unicode_ci DEFAULT '-',
  `observation_customer` text COLLATE utf8_unicode_ci NOT NULL,
  `tarifa_ref` decimal(11,2) NOT NULL,
  `user` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `empresa` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `status` varchar(255) COLLATE utf8_unicode_ci NOT NULL DEFAULT 'NO ASIGNED',
  `big_state` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `confirm_date` timestamp NOT NULL DEFAULT current_timestamp(),
  `document_bookingConf` varchar(255) COLLATE utf8_unicode_ci DEFAULT NULL,
  `created_at` timestamp(6) NOT NULL DEFAULT current_timestamp(6) ON UPDATE current_timestamp(6),
  `type` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `booking` (`booking`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;
DROP TABLE IF EXISTS `choferes`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `choferes` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `nombre` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `foto` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `documento` varchar(11) COLLATE utf8_unicode_ci NOT NULL,
  `vto_carnet` date NOT NULL,
  `WhatsApp` bigint(20) NOT NULL,
  `mail` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `user` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `empresa` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `transporte` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  `status_chofer` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `place` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `Observaciones` text COLLATE utf8_unicode_ci NOT NULL,
  `customer_id` int(11) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;
DROP TABLE IF EXISTS `cntr`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `cntr` (
  `id_cntr` int(11) NOT NULL AUTO_INCREMENT,
  `booking` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `cntr_number` varchar(25) COLLATE utf8_unicode_ci DEFAULT NULL,
  `cntr_seal` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `cntr_type` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `net_weight` int(11) NOT NULL,
  `retiro_place` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `set_` int(11) NOT NULL,
  `set_humidity` int(11) NOT NULL,
  `set_vent` int(11) NOT NULL,
  `document_invoice` varchar(255) COLLATE utf8_unicode_ci DEFAULT NULL,
  `document_packing` varchar(255) COLLATE utf8_unicode_ci DEFAULT NULL,
  `user_cntr` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `company` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `status_cntr` varchar(255) COLLATE utf8_unicode_ci NOT NULL DEFAULT 'NO ASIGNED',
  `main_status` varchar(255) COLLATE utf8_unicode_ci NOT NULL DEFAULT 'NO ASIGNED',
  `in_usd` decimal(11,2) NOT NULL,
  `company_invoice_out` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `modo_pago_in` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `plazo_de_pago_in` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `out_usd` decimal(11,2) NOT NULL,
  `observation_out` varchar(255) COLLATE utf8_unicode_ci DEFAULT NULL,
  `plazo_de_pago_out` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `modo_pago_out` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `created_at` timestamp(6) NOT NULL DEFAULT current_timestamp(6) ON UPDATE current_timestamp(6),
  `interchange` varchar(255) COLLATE utf8_unicode_ci DEFAULT NULL,
  `cntr_crt` varchar(255) COLLATE utf8_unicode_ci DEFAULT NULL,
  `cntr_micdta` varchar(255) COLLATE utf8_unicode_ci DEFAULT NULL,
  `profit` decimal(11,2) NOT NULL,
  `calificacion_carga` int(2) DEFAULT NULL,
  `feedback_customer` mediumtext COLLATE utf8_unicode_ci NOT NULL,
  PRIMARY KEY (`id_cntr`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;
DROP TABLE IF EXISTS `cntr_type`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `cntr_type` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `title` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `teu` int(11) NOT NULL,
  `weight` decimal(11,2) NOT NULL,
  `height` decimal(11,2) NOT NULL,
  `width` decimal(11,2) NOT NULL,
  `longitud` decimal(11,2) NOT NULL,
  `observation` text COLLATE utf8_unicode_ci NOT NULL,
  `created_at` int(11) NOT NULL,
  `user` varchar(20) COLLATE utf8_unicode_ci NOT NULL,
  `company` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;
DROP TABLE IF EXISTS `commodity`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `commodity` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `commodity` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `user` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  `company` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;
DROP TABLE IF EXISTS `custom_agent`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `custom_agent` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `razon_social` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `tax_id` bigint(11) NOT NULL,
  `pais` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `provincia` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  `mail` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `phone` bigint(11) NOT NULL,
  `user` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `empresa` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;
DROP TABLE IF EXISTS `customer.cnee`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `customer.cnee` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `razon_social` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `tax_id` bigint(11) NOT NULL,
  `address` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `city` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `country` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `postal_code` varchar(11) COLLATE utf8_unicode_ci NOT NULL,
  `create_user` varchar(22) COLLATE utf8_unicode_ci NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  `company` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `remarks` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;
DROP TABLE IF EXISTS `customer.ntfy`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `customer.ntfy` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `razon_social` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `tax_id` bigint(11) NOT NULL,
  `address` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `city` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `country` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `postal_code` varchar(11) COLLATE utf8_unicode_ci NOT NULL,
  `create_user` varchar(22) COLLATE utf8_unicode_ci NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  `company` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `remarks` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;
DROP TABLE IF EXISTS `customer.shipper`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `customer.shipper` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `razon_social` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `tax_id` bigint(30) NOT NULL,
  `address` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `city` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `country` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `postal_code` varchar(11) COLLATE utf8_unicode_ci NOT NULL,
  `create_user` varchar(22) COLLATE utf8_unicode_ci NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  `company` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `remarks` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;
DROP TABLE IF EXISTS `customer_load_place`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `customer_load_place` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `description` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `address` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `link_maps` text COLLATE utf8_unicode_ci NOT NULL,
  `lat` varchar(255) COLLATE utf8_unicode_ci DEFAULT NULL,
  `lon` varchar(255) COLLATE utf8_unicode_ci DEFAULT NULL,
  `lat_lon` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `country` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `city` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `km_from_town` int(11) NOT NULL,
  `user` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `company` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `remarks` text COLLATE utf8_unicode_ci NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;
DROP TABLE IF EXISTS `customer_unload_place`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `customer_unload_place` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `description` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `address` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `link_maps` text COLLATE utf8_unicode_ci NOT NULL,
  `lat_lon` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `lat` varchar(255) COLLATE utf8_unicode_ci DEFAULT NULL,
  `lon` varchar(255) COLLATE utf8_unicode_ci DEFAULT NULL,
  `country` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `city` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `km_from_town` int(11) NOT NULL,
  `user` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `company` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `remarks` text COLLATE utf8_unicode_ci NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;
DROP TABLE IF EXISTS `customers`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `customers` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `registered_name` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `tax_id` bigint(50) NOT NULL,
  `contact_name` varchar(50) COLLATE utf8mb4_unicode_ci NOT NULL,
  `contact_mail` varchar(50) COLLATE utf8mb4_unicode_ci NOT NULL,
  `contact_phone` bigint(50) NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT '0000-00-00 00:00:00',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;
DROP TABLE IF EXISTS `depositos_de_retiro`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `depositos_de_retiro` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `title` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `address` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `country` varchar(20) COLLATE utf8_unicode_ci NOT NULL,
  `city` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `km_from_town` int(11) NOT NULL,
  `lat_lon` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `link_maps` text COLLATE utf8_unicode_ci NOT NULL,
  `user` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `empresa` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;
DROP TABLE IF EXISTS `documets`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `documets` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `doc` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `booking` varchar(20) COLLATE utf8mb4_unicode_ci NOT NULL,
  `extension` varchar(20) COLLATE utf8mb4_unicode_ci NOT NULL,
  `cntr` varchar(30) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `user` varchar(30) COLLATE utf8mb4_unicode_ci NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT '0000-00-00 00:00:00',
  `eliminado` int(1) NOT NULL DEFAULT 0,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;
DROP TABLE IF EXISTS `empresas`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `empresas` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `razon_social` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `CUIT` bigint(11) NOT NULL,
  `IIBB` int(255) NOT NULL,
  `mail_admin` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `mail_logistic` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `name_admin` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `name_logistic` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `cel_admin` bigint(20) NOT NULL,
  `cel_logistic` bigint(20) NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  `direccion` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `user` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `empresa` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `pais` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `Provincia` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;
DROP TABLE IF EXISTS `instruction`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `instruction` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `booking` varchar(255) NOT NULL,
  `cntr_number` varchar(255) NOT NULL,
  `type_instruction` varchar(255) NOT NULL,
  `invoice_number` varchar(255) NOT NULL,
  `packing_number` varchar(255) NOT NULL,
  `transport` varchar(255) NOT NULL,
  `transport_agent` varchar(255) NOT NULL,
  `port_agent` varchar(255) NOT NULL,
  `transport_driver` varchar(255) NOT NULL,
  `mic_dta` varchar(255) NOT NULL,
  `doc_mic_dta` blob NOT NULL,
  `crt` varchar(255) NOT NULL,
  `doc_crt` blob NOT NULL,
  `out_usd` int(11) NOT NULL,
  `rs_invoice_out` varchar(255) NOT NULL,
  `modo_de_pago_out` varchar(255) NOT NULL,
  `plazo_de_pago_out` varchar(255) NOT NULL,
  `observation_payment_out` varchar(255) NOT NULL,
  `user_instruction` varchar(255) NOT NULL,
  `file_instruction` varchar(255) DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;
DROP TABLE IF EXISTS `insurances`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `insurances` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `customer` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `beneficiario` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `tax_id` bigint(20) NOT NULL,
  `commodity` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `suma_asegurada` decimal(11,2) NOT NULL,
  `truck_transport` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `truck_domain` varchar(10) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `truck_trailer` varchar(10) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `truck_device` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `load_place` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `download_place` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `reference_doc` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `load_date` datetime DEFAULT NULL,
  `truck_driver` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `truck_document` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `driver_phone` bigint(20) DEFAULT NULL,
  `email` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `status` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'solicitado',
  `commercial` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'fbar',
  `created_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `observation_customer` text COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `observation_commercial` text COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;
DROP TABLE IF EXISTS `logapis`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `logapis` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `user` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `detalle` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT '0000-00-00 00:00:00',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;
DROP TABLE IF EXISTS `mensajes`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `mensajes` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `titulo` varchar(100) NOT NULL,
  `mensaje` text NOT NULL,
  `de` int(11) NOT NULL,
  `para` int(11) NOT NULL,
  `fecha` datetime NOT NULL,
  `leido` int(11) NOT NULL,
  `estado` varchar(100) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;
DROP TABLE IF EXISTS `migrations`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `migrations` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `migration` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `batch` int(11) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;
DROP TABLE IF EXISTS `modos_de_pago`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `modos_de_pago` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `title` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `description` text COLLATE utf8_unicode_ci NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  `user` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `empresa` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;
DROP TABLE IF EXISTS `notification`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `notification` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `title` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `description` text COLLATE utf8_unicode_ci NOT NULL,
  `user_to` varchar(20) COLLATE utf8_unicode_ci NOT NULL,
  `Created_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  `status` varchar(11) COLLATE utf8_unicode_ci DEFAULT NULL,
  `sta_carga` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `user_create` varchar(20) COLLATE utf8_unicode_ci NOT NULL,
  `company_create` varchar(250) COLLATE utf8_unicode_ci NOT NULL,
  `cntr_number` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `booking` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;
DROP TABLE IF EXISTS `oauth_access_tokens`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `oauth_access_tokens` (
  `id` varchar(100) COLLATE utf8mb4_unicode_ci NOT NULL,
  `user_id` bigint(20) unsigned DEFAULT NULL,
  `client_id` bigint(20) unsigned NOT NULL,
  `name` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `scopes` text COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `revoked` tinyint(1) NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `expires_at` datetime DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `oauth_access_tokens_user_id_index` (`user_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;
DROP TABLE IF EXISTS `oauth_auth_codes`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `oauth_auth_codes` (
  `id` varchar(100) COLLATE utf8mb4_unicode_ci NOT NULL,
  `user_id` bigint(20) unsigned NOT NULL,
  `client_id` bigint(20) unsigned NOT NULL,
  `scopes` text COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `revoked` tinyint(1) NOT NULL,
  `expires_at` datetime DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `oauth_auth_codes_user_id_index` (`user_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;
DROP TABLE IF EXISTS `oauth_clients`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `oauth_clients` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `user_id` bigint(20) unsigned DEFAULT NULL,
  `name` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `secret` varchar(100) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `provider` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `redirect` text COLLATE utf8mb4_unicode_ci NOT NULL,
  `personal_access_client` tinyint(1) NOT NULL,
  `password_client` tinyint(1) NOT NULL,
  `revoked` tinyint(1) NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `oauth_clients_user_id_index` (`user_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;
DROP TABLE IF EXISTS `oauth_personal_access_clients`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `oauth_personal_access_clients` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `client_id` bigint(20) unsigned NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;
DROP TABLE IF EXISTS `oauth_refresh_tokens`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `oauth_refresh_tokens` (
  `id` varchar(100) COLLATE utf8mb4_unicode_ci NOT NULL,
  `access_token_id` varchar(100) COLLATE utf8mb4_unicode_ci NOT NULL,
  `revoked` tinyint(1) NOT NULL,
  `expires_at` datetime DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `oauth_refresh_tokens_access_token_id_index` (`access_token_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;
DROP TABLE IF EXISTS `ocean_lines`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `ocean_lines` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `razon_social` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `pais` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `tax_id` int(11) NOT NULL,
  `user` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `empresa` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  `andress` varchar(250) COLLATE utf8_unicode_ci NOT NULL,
  `mail` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;
DROP TABLE IF EXISTS `personal_access_tokens`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `personal_access_tokens` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `tokenable_type` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `tokenable_id` bigint(20) unsigned NOT NULL,
  `name` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `token` varchar(64) COLLATE utf8mb4_unicode_ci NOT NULL,
  `abilities` text COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `last_used_at` timestamp NULL DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `personal_access_tokens_token_unique` (`token`),
  KEY `personal_access_tokens_tokenable_type_tokenable_id_index` (`tokenable_type`,`tokenable_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;
DROP TABLE IF EXISTS `plazos_de_pago`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `plazos_de_pago` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `title` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `description` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  `user` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `empresa` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;
DROP TABLE IF EXISTS `port`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `port` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `description` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `pais` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `provincia` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `sigla` varchar(3) COLLATE utf8_unicode_ci NOT NULL,
  `link_maps` text COLLATE utf8_unicode_ci NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  `km_from_frontier` int(11) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;
DROP TABLE IF EXISTS `positions`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `positions` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `dominio` varchar(20) COLLATE utf8mb4_unicode_ci NOT NULL,
  `lat` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `lng` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  `created_at` timestamp NOT NULL DEFAULT '0000-00-00 00:00:00',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;
DROP TABLE IF EXISTS `profit`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `profit` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `cntr_number` varchar(11) COLLATE utf8_unicode_ci NOT NULL,
  `in_usd` decimal(11,2) NOT NULL,
  `in_razon_social` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `in_detalle` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `out_usd` decimal(11,2) NOT NULL,
  `out_razon_social` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `out_detalle` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  `user` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;
DROP TABLE IF EXISTS `pruebas_models`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `pruebas_models` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `contenido` text COLLATE utf8mb4_unicode_ci NOT NULL,
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  `created_at` timestamp NOT NULL DEFAULT '0000-00-00 00:00:00',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;
DROP TABLE IF EXISTS `razon_social`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `razon_social` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `title` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `img` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `cuit` bigint(11) NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;
DROP TABLE IF EXISTS `status`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `status` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `status` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `avisado` int(1) NOT NULL DEFAULT 0,
  `main_status` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `cntr_number` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `user_status` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  `created_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;
DROP TABLE IF EXISTS `status_type`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `status_type` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `STATUS` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `description` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `created_at` int(11) NOT NULL,
  `user` varchar(20) COLLATE utf8_unicode_ci NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;
DROP TABLE IF EXISTS `tipo_de_unidades`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `tipo_de_unidades` (
  `id` int(11) NOT NULL,
  `tittle` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `description` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  `user` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `empresa` varchar(255) COLLATE utf8_unicode_ci NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;
DROP TABLE IF EXISTS `trailers`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `trailers` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `type` varchar(250) COLLATE utf8mb4_unicode_ci NOT NULL,
  `domain` varchar(20) COLLATE utf8mb4_unicode_ci NOT NULL,
  `year` int(4) NOT NULL,
  `chasis` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `poliza` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `vto_poliza` date DEFAULT NULL,
  `doc_poliza` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `user_id` int(11) NOT NULL,
  `transport_id` int(11) NOT NULL,
  `customer_id` int(11) NOT NULL,
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  `created_at` timestamp NOT NULL DEFAULT '0000-00-00 00:00:00',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;
DROP TABLE IF EXISTS `transporte`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `transporte` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `razon_social` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `logo` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `CUIT` bigint(11) NOT NULL,
  `Direccion` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `Provincia` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `Pais` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `paut` varchar(255) COLLATE utf8_unicode_ci DEFAULT NULL,
  `permiso` varchar(255) COLLATE utf8_unicode_ci DEFAULT NULL,
  `vto_permiso` date DEFAULT NULL,
  `contacto_logistica_nombre` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `contacto_logistica_celular` bigint(30) NOT NULL,
  `contacto_logistica_mail` varchar(50) COLLATE utf8_unicode_ci NOT NULL,
  `contacto_admin_nombre` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `contacto_admin_celular` bigint(20) NOT NULL,
  `contacto_admin_mail` varchar(50) COLLATE utf8_unicode_ci NOT NULL,
  `empresa` varchar(250) COLLATE utf8_unicode_ci NOT NULL,
  `user` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  `customer_id` int(11) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;
DROP TABLE IF EXISTS `trucks`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `trucks` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `model` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `type` varchar(20) COLLATE utf8mb4_unicode_ci NOT NULL,
  `domain` varchar(10) COLLATE utf8mb4_unicode_ci NOT NULL,
  `id_satelital` int(11) DEFAULT NULL,
  `act_owner` int(11) NOT NULL DEFAULT 1,
  `year` int(4) NOT NULL,
  `device_truck` int(1) NOT NULL DEFAULT 0,
  `satelital_location` int(1) NOT NULL DEFAULT 0,
  `user` varchar(30) COLLATE utf8mb4_unicode_ci NOT NULL,
  `customer_id` int(10) NOT NULL,
  `transport_id` int(11) NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT '0000-00-00 00:00:00',
  `chasis` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `poliza` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `vto_poliza` date DEFAULT NULL,
  `doc_poliza` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;
DROP TABLE IF EXISTS `users`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `users` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `username` varchar(255) NOT NULL,
  `photo` varchar(255) NOT NULL,
  `email` varchar(255) NOT NULL,
  `pass` varchar(255) NOT NULL,
  `celular` bigint(20) NOT NULL,
  `name` varchar(20) NOT NULL,
  `last_name` varchar(29) NOT NULL,
  `Created_at` timestamp(6) NOT NULL DEFAULT current_timestamp(6) ON UPDATE current_timestamp(6),
  `empresa` varchar(255) NOT NULL,
  `permiso` varchar(255) DEFAULT NULL,
  `customer_id` int(10) DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `username` (`username`),
  UNIQUE KEY `email` (`email`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;
DROP TABLE IF EXISTS `variables`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `variables` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `sandbox` int(1) DEFAULT NULL,
  `db` varchar(155) COLLATE utf8mb4_unicode_ci NOT NULL,
  `email` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `api` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;
/*!40103 SET TIME_ZONE=@OLD_TIME_ZONE */;

/*!40101 SET SQL_MODE=@OLD_SQL_MODE */;
/*!40014 SET FOREIGN_KEY_CHECKS=@OLD_FOREIGN_KEY_CHECKS */;
/*!40014 SET UNIQUE_CHECKS=@OLD_UNIQUE_CHECKS */;
/*!40111 SET SQL_NOTES=@OLD_SQL_NOTES */;


INSERT INTO `aduanas` (`id`, `description`, `pais`, `lat`, `lon`, `created_at`, `provincia`, `km_from_town`, `link_maps`) VALUES
(1, 'PTM', 'Argentina', '-32.87485', '-68.66906', '2022-11-28 18:39:55', 'Mendoza, AR', 0, 'https://www.google.es/maps?q=-32.91069959504066, -68.8458320017414'),
(2, 'Puerto Seco', 'Argentina', NULL, NULL, '0000-00-00 00:00:00', 'Mendoza, AR', 0, 'https://www.google.es/maps?q=-32.936154, -68.826932'),
(3, 'Mar Cantabrico SA', 'Argentina', NULL, NULL, '0000-00-00 00:00:00', 'Mendoza, AR', 0, 'https://www.google.es/maps?q=-32.933800, -68.826720'),
(4, 'TRAC SA', 'Argentina', NULL, NULL, '0000-00-00 00:00:00', 'Mendoza, AR', 0, 'https://www.google.es/maps?q=-32.947156, -68.734942'),
(5, 'PLANTA FGF', 'Argentina', '-26.441444177229663', '-65.30480886164791', '2023-03-14 13:44:09', 'Tucuman, AR', 0, 'https://www.google.es/maps?q=-26.441444177229663, -65.30480886164791'),
(6, 'ZPA VILLA MERCEDES', 'ARGENTINA ', NULL, NULL, '2022-06-03 22:01:11', 'SAN LUIS', 0, 'https://www.google.es/maps?q=-33.64122484471936, -65.52932234092175'),
(7, 'DFC CORDOBA', 'ARGENTINA ', NULL, NULL, '2022-06-03 22:02:57', 'CORDOBA', 0, 'https://www.google.es/maps?q=-31.375231221391676, -64.07610684467404'),
(8, 'PTLA ', 'CHILE', NULL, NULL, '2022-06-03 22:08:22', 'LOS ANDES', 0, 'https://www.google.es/maps?q=-32.83661521012171, -70.5480049753141'),
(9, 'PINO HACHADO ', 'CHILE', NULL, NULL, '2022-06-03 22:08:22', 'LONQUIMAY', 0, 'https://www.google.es/maps?q=-38.64595660006634, -71.08756768906795');

INSERT INTO `agencias` (`id`, `description`, `razon_social`, `tax_id`, `puerto`, `contact_name`, `contact_phone`, `contact_mail`, `created_at`, `user`, `empresa`, `observation_gral`) VALUES
(1, 'Agencia de Prueba ', 'Agencia SA', '5467899', 'San Antonio, Cl', '', '542612128105', 'pablorio@botzero.tech', '2022-05-27 01:55:40', 'TCARGO', 'TCargoComex', 'Observaciones para probar '),
(3, 'Otra Agencia', 'Otra Agencia SA', '5467899', 'Santiago, CL', 'Juan Gomez', '65786595876578568', 'mendoza@juan.com', '2022-06-03 20:28:29', 'TCARGO', '', 'hola Juan'),
(4, 'Rep en LOS ANDES', 'UNITED Agencia', '8300526-2', 'PTLA', 'Jeanette Espinoza', '+56950718792', 'jeanette.united@gmail.com', '2022-07-01 22:15:02', 'TCARGO', '', '');


INSERT INTO `ata` (`id`, `razon_social`, `tax_id`, `provincia`, `pais`, `created_at`, `phone`, `mail`, `user`, `empresa`) VALUES
(1, 'Gustavo Puebla', 30710342580, 'Mendoza', 'Argentina', '2022-05-27 01:53:25', 5492611473402, 'vitae.sodales@Vivamusnisi.net', 'TCARGO', 'TCargoComex'),
(2, 'Enrique Torres', 30719181970, 'San Juan', 'Argentina', '2020-09-02 19:59:18', 5492611369715, 'dignissim.tempor@Vivamussit.co.uk', 'USER ', 'Total Trade Group'),
(3, 'Juan Dominguez', 30719262254, 'Mendoza', 'Argentina', '2020-09-02 19:59:18', 5492618969105, 'ipsum.Suspendisse.sagittis@sitametnulla.edu', 'USER ', 'Total Trade Group'),
(4, 'Marcos Perez', 30716963281, 'San Juan', 'Argentina', '2020-09-02 19:59:18', 5492618973961, 'Vivamus.euismod.urna@odiotristique.co.uk', 'USER ', 'Total Trade Group'),
(5, 'Juan Jose Lopez', 30712908977, 'Mendoza', 'Argentina', '2020-09-02 19:59:18', 5492612193840, 'convallis.est@Nullam.ca', 'USER ', 'Total Trade Group'),
(6, 'Marcelo De Paz', 30716300043, 'San Juan', 'Argentina', '2020-09-02 19:59:18', 5492617523271, 'auctor@Phaselluselit.net', 'USER ', 'Total Trade Group'),
(7, 'Martin Rodriguez', 30719024899, 'Mendoza', 'Argentina', '2020-09-02 19:59:18', 5492610026791, 'euismod.est.arcu@lectuspede.co.uk', 'USER ', 'Total Trade Group'),
(8, 'Leonardo fernandez', 30713645056, 'San Juan', 'Argentina', '2020-09-02 19:59:18', 5492610165316, 'id@nonenimcommodo.ca', 'USER ', 'Total Trade Group'),
(9, 'Jorge', 30300030030, 'MEndoza', 'Argentina', '2022-05-27 00:35:54', 5492612128105, 'juan@perez.com', 'TCARGO', 'TCargoComex');


INSERT INTO `choferes` (`id`, `nombre`, `foto`, `documento`, `vto_carnet`, `WhatsApp`, `mail`, `user`, `empresa`, `transporte`, `created_at`, `status_chofer`, `place`, `Observaciones`, `customer_id`) VALUES
(1, 'JDEFAVERI PABLO GASTON', '', '29681528', '2022-11-27', 549261218105, 'pablorio@botzero.tech', 'Fzgaib', 'TTL', 'RYD VIRDIANO S.A.', '2023-03-22 13:11:31', 'ocupado', 'Valparaiso', 'PAUT:16223\r\nN° PERMISO INT: 15344C15052 VENCIMIENTO:12/03/2031', 0),
(2, 'JUAN VIRDIANO', '', '24585984', '2026-01-29', 5492612128105, 'pablorio@botzero.tech', 'Fzgaib', 'TTL', 'RYD VIRDIANO S.A.', '2023-03-22 17:08:59', 'ocupado', 'AGRICOLA TARAPACA SA', 'Cargar WhatsApp, licencia y Correo reales', 0),
(3, 'BRAIAN JULIO VOLKMER', '', '42966552', '2026-01-29', 5492612128105, 'pablorio@botzero.tech', 'Fzgaib', 'TTL', 'RYD VIRDIANO S.A.', '2022-11-25 15:41:36', 'ocupado', 'Valparaiso', 'Cargar Datos de WhatsApp, Correo y Licencia correctos', 0);


INSERT INTO `cntr_type` (`id`, `title`, `teu`, `weight`, `height`, `width`, `longitud`, `observation`, `created_at`, `user`, `company`) VALUES
(1, '20 DRY', 1, '2300.00', '2.50', '2.40', '6.00', '', 0, 'USER', 'TCargoComex'),
(2, '40 DRY', 2, '3500.00', '2.60', '2.40', '12.00', '', 0, 'USER', 'TCargoComex'),
(3, '20 AF', 1, '2300.00', '2.50', '2.40', '6.00', '', 0, 'USER', 'TCargoComex'),
(5, '40 RF', 2, '3500.00', '2.60', '2.40', '12.00', '', 0, 'USER', 'TCargoComex'),
(6, '40 RHC', 2, '3500.00', '2.89', '2.40', '12.00', '', 0, 'USER', 'TCargoComex'),
(9, '40 OT', 2, '2000.00', '2.59', '2.44', '12.00', '', 0, 'USER', 'TCargoComex'),
(10, 'Carga Suelta', 0, '0.00', '0.00', '0.00', '0.00', '', 0, '', 'TCargoComex');


INSERT INTO `commodity` (`id`, `commodity`, `user`, `created_at`, `company`) VALUES
(1, 'SUGAR', 'USER', '2020-09-02 20:00:20', 'TCargoComex'),
(2, 'MAIZ PARTIDO', 'DATABASE', '2022-06-03 21:43:23', ''),
(3, 'MUEBLES', 'DATABASE', '2022-06-03 21:43:34', ''),
(4, 'CASCARAS DE LIMON', 'SETEO', '2023-03-14 14:04:09', 'TTL'),
(5, 'VINO', 'SETEO', '2023-03-21 19:59:23', '');


INSERT INTO `customer.cnee` (`id`, `razon_social`, `tax_id`, `address`, `city`, `country`, `postal_code`, `create_user`, `created_at`, `company`, `remarks`) VALUES
(3, 'Agrícola TARAPACA SA', 85120400, 'Los Carrera 444', 'Melipilla', 'Chile', '9580000', 'TCARGO CUSTOMER', '2022-06-03 04:10:07', 'TCargoComex', ''),
(4, 'COM. AGRICOLA TRAPANANDA SpA', 775282258, 'Huerfanos 1055 Dpto 503', 'Santiago', 'Chile', '8320000', 'TCARGO CUSTOMER', '2022-07-01 20:46:54', 'TCargoComex', ''),
(5, 'Consenee de Prueba', 2034818812, '12 de octubre', 'Las Heras', 'Argentina', '5539', 'customerTTL', '2022-08-12 14:11:02', 'TTL', '');

--
-- Volcado de datos para la tabla `customer.ntfy`
--

INSERT INTO `customer.ntfy` (`id`, `razon_social`, `tax_id`, `address`, `city`, `country`, `postal_code`, `create_user`, `created_at`, `company`, `remarks`) VALUES
(2, 'Notify de Prueba', 123456789, '12 de octubre', 'Las Heras', 'Argentina', '5539', 'TCARGO CUSTOMER', '2022-05-28 02:55:36', 'TCargoComex', 'Algun Dato'),
(3, 'DON ZOILO Comercializadora LTDA', 76837471, 'Juarez 621 Of. 403', 'Recoleta - SANTIAGO', 'Chile', '8420000', 'TCARGO CUSTOMER', '2022-06-03 04:12:26', 'TCargoComex', ''),
(4, 'COM AGRICOLA TRAPANANDA SpA', 775282258, 'Huerfanos 1055 Dpto 503', 'Santiago', 'Chile', '8320000', 'TCARGO CUSTOMER', '2022-07-01 20:56:28', 'TCargoComex', ''),
(5, 'Notify de Prueba', 2034818812, '12 de octubre', 'Las Heras', 'Argentina', '5539', 'customerTTL', '2022-08-12 14:11:11', 'TTL', '');

--
-- Volcado de datos para la tabla `customer.shipper`
--

INSERT INTO `customer.shipper` (`id`, `razon_social`, `tax_id`, `address`, `city`, `country`, `postal_code`, `create_user`, `created_at`, `company`, `remarks`) VALUES
(3, 'Shipper de Prueba', 5467899, '12 de octubre 879', 'Las Heras', 'Argentina', '5500', 'TCARGO CUSTOMER', '2022-05-27 02:29:16', 'TCargoComex', 'Algun dato de interes'),
(4, 'Corporación MC', 30716711044, 'Brown 1787', 'Venado Tuerto', 'Santa Fe', '2600', 'TCARGO CUSTOMER', '2022-06-03 02:55:10', 'TCargoComex', 'Contacto: Ezequiel'),
(5, 'NUTRIX SA', 30716862204, 'Ruta 148 - Colectora O KM 765.8 Villa Mercedes', 'San Luis', 'Argentina', '5730', 'TCARGO CUSTOMER', '2022-07-01 20:39:34', 'TCargoComex', ''),
(6, 'FORWARDING', 33710556259, 'confirmar', 'confirmar', 'confirmar', '5539', 'customerTTL', '2023-03-14 14:03:10', 'TTL', ''),
(7, 'PREMEXAR SA', 2034818812, 'RP50 5326', 'RODE', 'USA', '5501', 'customerTTL', '2022-11-24 16:13:02', 'TTL', ''),
(8, 'LCL ', 5467899, '12 de octubre', 'Las Heras', 'Argentina', '5539', 'customerTTL', '2023-03-21 19:53:18', 'TTL', 'ZUCCARDI – PERNOD RICARD – GARBIN ESTATE');

--
-- Volcado de datos para la tabla `customers`
--

INSERT INTO `customers` (`id`, `registered_name`, `tax_id`, `contact_name`, `contact_mail`, `contact_phone`, `created_at`, `updated_at`) VALUES
(6, 'Juan de los Palotes', 2034818812, 'Pablo Agustin RIO PELLIZA', 'prio@gmail.com', 2612128105, '2023-02-08 18:15:03', '0000-00-00 00:00:00'),
(7, 'Juan de los Palote', 2034818814, 'Pablo  RIO PELLIZA', 'sanjuan@juan.com', 2612128105, '2023-02-08 16:58:12', '0000-00-00 00:00:00'),
(8, 'FORWARDING TSL SA', 33710556259, 'FRANCISCO VINUESA', 'fvinuesa@forwarding.com.ar', 54, '2023-03-14 13:36:28', '0000-00-00 00:00:00'),
(9, 'SAVINO DEL VENE', 20348188128, 'Pablo Agustin RIO PELLIZA', 'pablorio@botzero.tech', 2612128105, '2023-03-21 19:47:43', '0000-00-00 00:00:00');

--
-- Volcado de datos para la tabla `customer_load_place`
--

INSERT INTO `customer_load_place` (`id`, `description`, `address`, `link_maps`, `lat`, `lon`, `lat_lon`, `country`, `city`, `km_from_town`, `user`, `company`, `remarks`, `created_at`) VALUES
(1, 'FGF Trapani S.A.', 'RN9 Km 1341, Choromoro', 'https://www.google.es/maps?q=-32.9394737850459, -68.82076823298118', '-32.9394737850459', '-68.82076823298118', '-26.441600535257315, -65.30539811533997', 'Argentina', 'Tucuman', 0, 'customerTTL', 'TTL', 'HAY QUE IR DE GALA', '0000-00-00 00:00:00'),
(2, 'PLANTA SANES', 'RP50 5326', 'https://www.google.es/maps?q=-32.99361567913533, -68.67760710369645', '-32.99361567913533', '-68.67760710369645', '-32.99361567913533, -68.67760710369645', 'ARGENTINA', 'MENDOZA', 0, 'customerTTL', 'TTL', '', '2022-11-24 16:11:05'),
(3, 'TRAPANI S.A.', 'RN9 Km 1341, Choromoro, Tucumán', 'https://www.google.es/maps?q=-26.4416918,-65.3075439,17', NULL, NULL, '-26.4416918,-65.3075439,17', 'Argentina', 'Tucumán', 0, 'customerTTL', 'TTL', '', '2023-03-14 13:42:47'),
(4, 'B- OCEANIC', 'Acceso Sur y Olavarría, Lateral Oeste 3185. Perdriel. Luján de Cuyo', 'https://www.google.es/maps?q=', NULL, NULL, '-33.01442038968421, -68.85662897732892', 'Argentina', 'Mendoza', 0, 'customerTTL', 'TTL', '', '0000-00-00 00:00:00');

--
-- Volcado de datos para la tabla `customer_unload_place`
--

INSERT INTO `customer_unload_place` (`id`, `description`, `address`, `link_maps`, `lat_lon`, `lat`, `lon`, `country`, `city`, `km_from_town`, `user`, `company`, `remarks`, `created_at`) VALUES
(1, 'Valparaiso', 'CAMINO DEL MOÑO 45', 'https://www.google.es/maps?q=-32.93983388835002, -68.8198670275701', '-32.99766056830598', '-32.99766056830598', '-68.67369060229964', 'CHILE', 'SAN ANTONIO', 0, 'customerTTL', 'TTL', 'AHI SE DESACARGA', '2022-11-14 15:16:34'),
(2, 'SAN ANTONIO', 'Antonio Núñez de Fonseca 1552, 2660000 Valparaíso, San Antonio', 'https://www.google.es/maps?q=-33.57435665751155, -71.62658364324291', '-33.57435665751155, -71.62658364324291', NULL, NULL, 'Chile', 'Valparaiso', 0, 'customerTTL', 'TTL', '', '2023-03-14 13:53:51');

--
-- Volcado de datos para la tabla `custom_agent`
--

INSERT INTO `custom_agent` (`id`, `razon_social`, `tax_id`, `pais`, `provincia`, `created_at`, `mail`, `phone`, `user`, `empresa`) VALUES
(1, 'LGA Despa', 30710087911, 'Argentina', 'Mendoza', '2022-05-27 01:54:57', 'lga@estudioabel.com.ar', 2614311898, 'TCARGO', 'TCargoComex'),
(2, 'Sul Mineira SA', 30710087912, 'Argentina', 'Mendoza', '2020-09-02 20:00:52', 'info@sulmineira.com', 2614311898, 'USER', 'Total Trade Group'),
(3, 'Fontana', 30710087913, 'Argentina', 'Mendoza', '2020-09-02 20:00:52', 'info@fontana.com', 2614311898, 'USER', 'Total Trade Group'),
(4, 'Estudio Traetta', 30710087914, 'Argentina', 'Mendoza', '2020-09-02 20:00:52', 'info@traetta.com.ar', 2614311898, 'USER', 'Total Trade Group'),
(5, 'All IN', 30710087915, 'Argentina', 'Mendoza', '2020-09-02 20:00:52', 'Info@allin.com.ar', 2614311898, 'USER', 'Total Trade Group'),
(6, 'Christian Garcia', 0, 'Argentina', 'Tucuman', '2023-03-14 13:45:44', 'confirmar', 5493815767700, 'Fzgaib', 'TTL'),
(7, 'Herrero & Asoc', 30710087917, 'Argentina', 'San Juan', '2020-09-02 20:00:52', 'info@herrero.com.ar', 2614311898, 'USER', 'Total Trade Group'),
(8, 'AREOPAGO - Alvaro Aciar', 2034818812, '', 'MENDOZA', '2022-11-24 16:14:48', 'pablorio@botzero.tech', 5492616294414, 'Fzgaib', 'TTL');

--
-- Volcado de datos para la tabla `depositos_de_retiro`
--

INSERT INTO `depositos_de_retiro` (`id`, `title`, `address`, `country`, `city`, `km_from_town`, `lat_lon`, `link_maps`, `user`, `empresa`, `created_at`) VALUES
(1, 'TPS VALPARAISO j', 'Antonio Varas 2, Valparaiso', 'Chile ', 'Valparaiso', 0, '-33.033709, 71.629729', 'https://www.google.es/maps?q=-33.033709, 71.629729', 'TCARGO', 'TCargoComex', '2022-05-27 01:55:54'),
(2, 'SITRANS SAN ANTONIO', 'San Anotnio ', 'Chile ', 'Valparaiso', 0, '-33.592411, -71.586848', 'https://www.google.es/maps?q=-33.592411, -71.586848', 'USER ', 'Total Trade Group', '2020-09-02 20:01:17'),
(3, 'CONTOPSA SAN ANTONIO', 'Av. Las Factorias 8150, San Antonio', 'Chile ', 'Valparaiso', 0, '-33.576035, -71.536752', 'https://www.google.es/maps?q=-33.576035, -71.536752', 'USER ', 'Total Trade Group', '2020-09-02 20:01:17'),
(4, 'PRONAVE SA', 'Montevideo 545, Mendoza', 'Argentina', 'Mendoza', 0, '-32.967099, -68.875468', 'https://www.google.es/maps?q=-32.967099, -68.875468', 'USER ', 'Total Trade Group', '2020-09-02 20:01:17'),
(5, 'EXOLGAN SA', 'Manual Alberti 4, Dock Sud Prov. BA', 'Argentina', 'Buenos Aires', 0, '-34.641978, -58.348184', 'https://www.google.es/maps?q=-34.641978, -58.348184', 'USER ', 'Total Trade Group', '2020-09-02 20:01:17'),
(6, 'DYC SANTIAGO ', 'Pudahuel, Region Metropolitana', 'Chile ', 'Santiago', 0, '-33.429545, -70.821136', 'https://www.google.es/maps?q=-33.429545, -70.821136', 'USER ', 'Total Trade Group', '2020-09-02 20:01:17'),
(7, 'SUD CONTAINERS S.A.', 'Gral. Gutierres 88, Coquimbito - Maipu', 'Argentina', 'Mendoza', 0, '-32.969711, -68.777957', 'https://www.google.es/maps?q=-32.969711, -68.777957', 'USER ', 'Total Trade Group', '2020-09-02 20:01:17'),
(8, 'SAAM SAN ANTONIO', 'Pablo Neruda 289, San Antonio', 'Chile ', 'Valparaiso', 0, '-33.602502, -71.618800', 'https://www.google.es/maps?q=-33.602502, -71.618800', 'USER ', 'Total Trade Group', '2020-09-02 20:01:17'),
(9, 'SAAM VALPARAISO', 'Blanco 937', 'Chile ', 'Valparaiso', 0, '-33.038376, -71.625899', 'https://www.google.es/maps?q=-33.038376, -71.625899', 'USER ', 'Total Trade Group', '2020-09-02 20:01:17'),
(10, 'DYC SAN ANTONIO', 'Ruta G94 F Nuevo Acceso al Puerto 35590, Barrio Industrial', 'Chile ', 'Valparaiso', 0, '-33.588609, -71.584687', 'https://www.google.es/maps?q=-33.588609, -71.584687', 'USER ', 'Total Trade Group', '2020-09-02 20:01:17'),
(11, 'DYC VALPARAISO', 'Camino La Polvora, ', 'Chile ', 'Valparaiso', 0, ' -33.069810, -71.636814', 'https://www.google.es/maps?q=-33.069810, -71.636814', 'USER ', 'Total Trade Group', '2020-09-02 20:01:17'),
(12, 'TRANSPORTE MOSCA SA ', 'Neuquen 98, Mendoza', 'Argentina', 'Mendoza', 0, '-32.865488, -68.830979', 'https://www.google.es/maps?q=-32.865488, -68.830979', 'USER ', 'Total Trade Group', '2020-09-02 20:01:17');

--
-- Volcado de datos para la tabla `documets`
--


INSERT INTO `empresas` (`id`, `razon_social`, `CUIT`, `IIBB`, `mail_admin`, `mail_logistic`, `name_admin`, `name_logistic`, `cel_admin`, `cel_logistic`, `created_at`, `direccion`, `user`, `empresa`, `pais`, `Provincia`) VALUES
(1, 'TTL', 2034818812, 2345566, 'pablorio@botzero.tech', 'pablorio@botzero.tech', 'Pachi', 'Juan', 2612128105, 2612128105, '2022-08-17 17:08:06', '', 'Fzgaib', 'TTL', 'Argentina', 'Mendoza');


INSERT INTO `modos_de_pago` (`id`, `title`, `description`, `created_at`, `user`, `empresa`) VALUES
(1, 'TRANSFERENCIA BANCARIA BANCO FRANCES', 'Transferencia realizadas ?nicamente por Banco Frances', '2020-09-02 23:01:43', 'USER', 'T-CARGO COMEX'),
(6, 'CHEQUE ELECTRONICO ', 'CHEQUE ELECTRONICO', '2022-06-03 20:25:57', 'TCARGO', 'TCargoComex');

--

INSERT INTO `ocean_lines` (`id`, `razon_social`, `pais`, `tax_id`, `user`, `empresa`, `created_at`, `andress`, `mail`) VALUES
(1, 'HAMBURG SUD', 'ALEMANIA', 8900090, 'DATABASE', 'T-CARGO COMEX', '2022-06-01 20:41:45', '', ''),
(2, 'SEALAND MAERSK', '', 0, '', '', '2023-03-21 19:56:13', '', ''),
(3, 'HAPAG LLOYD', '', 2147483647, '', '', '2023-03-14 14:05:18', '', '');


INSERT INTO `plazos_de_pago` (`id`, `title`, `description`, `created_at`, `user`, `empresa`) VALUES
(1, '+ 30 DIAS FECHA FACTURA', '30 DIAS DESDE QUE SE EMITE LA FACTURA', '2022-05-28 03:53:07', 'DATABASE', 'T-CARGO COMEX'),
(2, '+ 7 DIAS FECHA FACTURA', '7 DIAS FECHA FACTURA', '2022-06-03 20:17:44', 'TCARGO', 'TCargoComex'),
(6, 'FECHA VIAJE', 'ANTES DE SALIR', '2022-06-03 20:21:11', 'TCARGO', 'TCargoComex'),
(7, '+ 45 DIAS FECHA VIAJE', '30 DIAS FECHA VIAJE', '2022-06-03 20:21:57', 'TCARGO', 'TCargoComex'),
(8, '90 DIAS', '90 DIAS', '2022-06-03 20:22:55', 'TCARGO', 'TCargoComex');

--
-- Volcado de datos para la tabla `port`
--

INSERT INTO `port` (`id`, `description`, `pais`, `provincia`, `sigla`, `link_maps`, `created_at`, `km_from_frontier`) VALUES
(1, 'Valparaiso', 'Chile', 'Valparaiso', 'VPO', '-33.03399208089151, -71.62941618824557', '0000-00-00 00:00:00', 23),
(2, 'AGRICOLA TARAPACA SA', 'Chile', 'Los Carrera N° 444', 'ATA', 'https://www.google.es/maps?q=-32.891877914848145, -68.84841387904711', '2022-06-03 21:57:13', 0),
(3, 'PORT EVERGLADES', 'USA', 'FLORIDA', 'PVG', '', '2023-03-14 14:06:45', 0),
(4, 'SAN ANTONIO', 'CHILE', 'VALPARAISO', 'SAI', '', '2023-03-14 14:10:07', 0),
(5, 'Corinto', 'Nicaragua', 'Corinto', 'CRT', 'https://www.google.com/maps/search/corinto+nicaragua+puerto+%22sigla%22/@12.492836,-87.1961261,14z/data=!3m1!4b1', '2023-03-21 19:58:25', 0),
(6, 'Corinto', 'Nicaragua', 'Corinto', 'CRT', 'https://www.google.com/maps/search/corinto+nicaragua+puerto+%22sigla%22/@12.492836,-87.1961261,14z/data=!3m1!4b1', '2023-03-21 19:58:38', 0);

--
INSERT INTO `razon_social` (`id`, `title`, `img`, `cuit`, `created_at`) VALUES
(2, 'TTL', '', 12121212122, '2023-03-14 14:07:22'),
(3, 'LSH', '', 12121289, '2023-03-14 14:07:39'),
(4, 'AASA', '', 890890890, '2023-03-14 14:07:39');


INSERT INTO `status_type` (`id`, `STATUS`, `description`, `created_at`, `user`) VALUES
(1, 'ASIGNADA', '0', 0, 'USER'),
(2, 'YENDO A CARGAR', '0', 0, 'USER'),
(3, 'CARGANDO', '0', 0, 'USER'),
(4, 'EN ADUANA', '0', 0, 'USER'),
(5, 'YENDO A DESCARGAR', '0', 0, 'USER'),
(6, 'STACKING', '0', 0, 'USER'),
(7, 'CON PROBLEMA', '0', 0, 'USER'),
(8, 'ON BOARD', '0', 0, 'USER'),
(9, 'NO ASIGNADA', '0', 0, 'USER'),
(10, 'TERMINADA', '', 0, 'USER');


INSERT INTO `trailers` (`id`, `type`, `domain`, `year`, `chasis`, `poliza`, `vto_poliza`, `doc_poliza`, `user_id`, `transport_id`, `customer_id`, `updated_at`, `created_at`) VALUES
(2, 'palletizado', 'PII014', 2015, '123456SJ465434JD', 'SURICH123', '2023-03-25', '', 46, 1, 2, '2023-03-09 17:44:19', '2022-11-02 13:47:51'),
(3, 'palletizado', 'AWH307', 2005, NULL, NULL, NULL, '', 46, 1, 2, '2022-11-09 11:11:11', '2022-11-09 11:11:11'),
(4, 'palletizado', 'OQB411', 2014, NULL, NULL, NULL, '', 46, 1, 2, '2022-11-09 11:47:32', '2022-11-09 11:47:32'),
(5, 'palletizado', 'GPM743', 2021, NULL, NULL, NULL, '', 46, 1, 2, '2022-11-25 12:41:10', '2022-11-25 12:41:10'),
(6, 'palletizado', 'AA123KL', 2023, 'AS22345KL899808KL', 'JKL890MENDOZA', '2023-03-25', NULL, 46, 3, 2, '2023-03-09 17:49:44', '2023-03-09 17:49:44');


INSERT INTO `transporte` (`id`, `razon_social`, `logo`, `CUIT`, `Direccion`, `Provincia`, `Pais`, `paut`, `permiso`, `vto_permiso`, `contacto_logistica_nombre`, `contacto_logistica_celular`, `contacto_logistica_mail`, `contacto_admin_nombre`, `contacto_admin_celular`, `contacto_admin_mail`, `empresa`, `user`, `created_at`, `customer_id`) VALUES
(1, 'RYD VIRDIANO S.A.', '', 203481818128, 'SEVERO DEL CASTILLO 8263- LOS CORRALITOS- GUAYMALLÉN', 'MENDOZA', 'ARG', '47868', 'cualqu214', '2023-03-18', 'Algun Nombre', 5492612128105, 'pablorio@botzero.tech', 'CONFIGURAR NOMBRE', 5492612128105, 'pablorio@botzero.tech', 'TTL', 'Fzgaib', '2023-03-09 19:33:29', 0),
(3, 'Transportes Pachiman', '', 20358797130, 'Calle de la Alegría', 'Mendoza', 'Argentina', '7886223', 'PER6780753', '2023-04-02', 'Juan Pablo Logistica', 5492612128105, 'traficottl@botzero.ar', 'Juan Administración', 5492612128105, 'traficottl@botzero.ar', 'TTL', 'Fzgaib', '2023-03-09 19:40:59', 0);



INSERT INTO `trucks` (`id`, `model`, `type`, `domain`, `id_satelital`, `act_owner`, `year`, `device_truck`, `satelital_location`, `user`, `customer_id`, `transport_id`, `created_at`, `updated_at`, `chasis`, `poliza`, `vto_poliza`, `doc_poliza`) VALUES
(2, 'IVECO', 'conRemolque', 'ERA981', 33597, 1, 2004, 0, 0, 'Fzgaib', 2, 1, '2023-03-22 13:06:55', '2022-11-09 11:59:20', 'QUINIELA678907', 'POLIZASANCOR123', '2023-03-23', NULL),
(3, 'IVECO', 'conRemolque', 'GSX013', 33599, 1, 2007, 0, 0, 'Fzgaib', 2, 1, '2023-03-15 15:40:53', '2023-03-15 12:40:53', 'CHASISDEPRUEBA11234', 'POLIASURICH', '2023-12-01', NULL),
(4, 'IVECO', 'conRemolque', 'GSX014', 33607, 1, 2007, 1, 1, 'Fzgaib', 2, 1, '2022-11-23 15:54:42', '2022-11-09 11:59:43', NULL, NULL, NULL, NULL),
(5, 'ETIOS', 'conRemolque', 'AE792WJ', 32077, 1, 2021, 1, 1, 'Fzgaib', 2, 1, '2022-11-14 12:26:57', '0000-00-00 00:00:00', NULL, NULL, NULL, NULL),
(6, 'IVECO', 'conRemolque', 'AA234JJ', NULL, 1, 2023, 0, 0, 'Fzgaib', 2, 3, '2023-03-09 20:25:21', '2023-03-09 17:25:21', '123456SJ465434JD', 'SURICH123', '2023-03-31', NULL);


INSERT INTO `users` (`id`, `username`, `photo`, `email`, `pass`, `celular`, `name`, `last_name`, `Created_at`, `empresa`, `permiso`, `customer_id`) VALUES
(44, 'MASTER', '', 'priopelliza2@gmail.com', 'c31d7c9fee9b5829aed1486a82516c20', 5492612128105, 'Master', 'TCargo', '2022-08-09 18:40:05.511622', 'TCargoComex', 'Master', 1),
(45, 'USER', '', 'priopelliza@gmail.com', 'e4f3677c51963c08ff8c83d88079c698', 5492612128105, 'Mechelle', 'Oneal', '2022-08-11 19:06:27.419142', 'TTL', 'Master', 2),
(46, 'Fzgaib', '', 'traficottl@botzero.ar', '2427ac1227347930b9c5412ca729f502', 2612347373, 'Fernando', 'Zgaib', '2023-03-08 14:25:17.239031', 'TTL', 'Traffic', 2),
(47, 'customer', '', 'fzgaib@ttlgroup.com', '031d2476c1f6dad37cd911a6b80f5010', 5492612128105, 'Customer', 'Prueba', '2022-08-11 19:06:41.204088', 'TTL', 'Customer', 2),
(48, 'customerEZ', '', 'prio@ezlog.com.ar', '031d2476c1f6dad37cd911a6b80f5010', 5492612128105, 'Customer', 'EZ', '2022-08-11 19:06:44.627027', 'TTL', 'Customer', 2),
(49, 'customerTTL', '', 'customer@totaltrade.cl', 'e4f3677c51963c08ff8c83d88079c698', 5492612128105, 'Customer', 'Total Trade', '2022-08-11 19:06:56.089545', 'TTL', 'Customer', 2),
(50, 'UsuarioCliente', '', 'cliente@deprueba.con', 'd197439d90ad1e54d91138092641a615', 5492612128105, 'Cliente', 'DePrueba', '2022-08-11 18:30:23.766673', 'Empresa de Prueba', 'Customer', 2);


INSERT INTO `variables` (`id`, `sandbox`, `db`, `email`, `api`) VALUES
(1, 0, 'u101685278_sbttl', 'sandbox@botzero.ar', 'https://sapittl.botzero.ar');
COMMIT;

