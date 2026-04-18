-- MySQL dump 10.13  Distrib 8.0.43, for macos15 (x86_64)
--
-- Host: localhost    Database: cotizador_ips
-- ------------------------------------------------------
-- Server version	9.4.0

/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!50503 SET NAMES utf8 */;
/*!40103 SET @OLD_TIME_ZONE=@@TIME_ZONE */;
/*!40103 SET TIME_ZONE='+00:00' */;
/*!40014 SET @OLD_UNIQUE_CHECKS=@@UNIQUE_CHECKS, UNIQUE_CHECKS=0 */;
/*!40014 SET @OLD_FOREIGN_KEY_CHECKS=@@FOREIGN_KEY_CHECKS, FOREIGN_KEY_CHECKS=0 */;
/*!40101 SET @OLD_SQL_MODE=@@SQL_MODE, SQL_MODE='NO_AUTO_VALUE_ON_ZERO' */;
/*!40111 SET @OLD_SQL_NOTES=@@SQL_NOTES, SQL_NOTES=0 */;

--
-- Table structure for table `categorias_examen`
--

DROP TABLE IF EXISTS `categorias_examen`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `categorias_examen` (
  `id` int NOT NULL AUTO_INCREMENT,
  `nombre` varchar(100) NOT NULL,
  `estado` tinyint(1) DEFAULT '1',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=9 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `categorias_examen`
--

LOCK TABLES `categorias_examen` WRITE;
/*!40000 ALTER TABLE `categorias_examen` DISABLE KEYS */;
INSERT INTO `categorias_examen` VALUES (1,'LABORATORIOS',1),(2,'EXÁMENES',1),(3,'EXÁMENES COMPLEMENTARIOS',1),(4,'CARNÉ MANIPULACION DE ALIMENTOS',1),(5,'CONSULTAS',1),(6,'ELEMENTOS',1),(7,'OTROS SERVICIOS',1),(8,'VACUNAS',1);
/*!40000 ALTER TABLE `categorias_examen` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `ciudades`
--

DROP TABLE IF EXISTS `ciudades`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `ciudades` (
  `id` int NOT NULL AUTO_INCREMENT,
  `nombre` varchar(100) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=46 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `ciudades`
--

LOCK TABLES `ciudades` WRITE;
/*!40000 ALTER TABLE `ciudades` DISABLE KEYS */;
INSERT INTO `ciudades` VALUES (1,'AGUACHICA'),(2,'ARAUCA'),(3,'BOGOTA'),(4,'CARTAGENA'),(5,'CUCUTA'),(6,'CALI'),(7,'IBAGUE'),(8,'RIOHACHA'),(9,'SANTA MARTA'),(10,'MAGANGUE'),(11,'NEIVA'),(12,'VALLEDUPAR'),(13,'MONTERIA'),(14,'OCANA'),(15,'MOSQUERA'),(16,'MEDELLIN'),(17,'BUCARAMANGA'),(18,'QUIBDO'),(19,'VILLAVICENCIO'),(20,'PLATO'),(21,'SABANA GRANDE'),(22,'FUNDACION'),(23,'EL BANCO MAGDALENA'),(24,'BOSCONIA'),(25,'PLATO MAGDALENA'),(26,'CIENAGA'),(27,'PIVIJAY'),(28,'PIJINO'),(29,'ARMENIA'),(30,'BARRANCABERMEJA'),(31,'LA DORADA'),(32,'MANIZALEZ'),(33,'PALMIRA'),(34,'PASTO'),(35,'PEREIRA'),(36,'SINCELEJO'),(37,'YOPAL'),(38,'FLORENCIA'),(39,'CUNDINAMARCA'),(40,'OCAÑA'),(41,'COVEÑAS'),(42,'PUERTO CARREÑO'),(43,'POPAYAN'),(44,'CUMDINAMARCA'),(45,'LAS JAGUA DE IBIRICO');
/*!40000 ALTER TABLE `ciudades` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `cotizaciones`
--

DROP TABLE IF EXISTS `cotizaciones`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `cotizaciones` (
  `id` int NOT NULL AUTO_INCREMENT,
  `fecha` datetime DEFAULT CURRENT_TIMESTAMP,
  `cliente_nombre` varchar(150) DEFAULT NULL,
  `cliente_nit` varchar(50) DEFAULT NULL,
  `total` decimal(12,2) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=5 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `cotizaciones`
--

--
-- Table structure for table `cotizaciones_detalle`
--

DROP TABLE IF EXISTS `cotizaciones_detalle`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `cotizaciones_detalle` (
  `id` int NOT NULL AUTO_INCREMENT,
  `id_cotizacion` int NOT NULL,
  `id_proveedor` int NOT NULL,
  `id_examen` int NOT NULL,
  `precio_costo` decimal(12,2) NOT NULL DEFAULT '0.00',
  `precio_venta` decimal(10,2) NOT NULL,
  PRIMARY KEY (`id`),
  KEY `id_cotizacion` (`id_cotizacion`),
  KEY `id_proveedor` (`id_proveedor`),
  KEY `id_examen` (`id_examen`),
  CONSTRAINT `cotizaciones_detalle_ibfk_1` FOREIGN KEY (`id_cotizacion`) REFERENCES `cotizaciones` (`id`) ON DELETE CASCADE,
  CONSTRAINT `cotizaciones_detalle_ibfk_2` FOREIGN KEY (`id_proveedor`) REFERENCES `proveedores` (`id`) ON DELETE RESTRICT,
  CONSTRAINT `cotizaciones_detalle_ibfk_3` FOREIGN KEY (`id_examen`) REFERENCES `examenes` (`id`) ON DELETE RESTRICT
) ENGINE=InnoDB AUTO_INCREMENT=17 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `cotizaciones_detalle`
--

--
-- Table structure for table `examenes`
--

DROP TABLE IF EXISTS `examenes`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `examenes` (
  `id` int NOT NULL AUTO_INCREMENT,
  `id_categoria` int DEFAULT NULL,
  `nombre` varchar(550) NOT NULL,
  `estado` tinyint(1) DEFAULT '1',
  PRIMARY KEY (`id`),
  KEY `fk_categoria` (`id_categoria`),
  CONSTRAINT `fk_categoria` FOREIGN KEY (`id_categoria`) REFERENCES `categorias_examen` (`id`) ON DELETE SET NULL
) ENGINE=InnoDB AUTO_INCREMENT=1166 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `examenes`
--

--
-- Table structure for table `proveedores`
--

DROP TABLE IF EXISTS `proveedores`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `proveedores` (
  `id` int NOT NULL AUTO_INCREMENT,
  `id_ciudad` int NOT NULL,
  `nit` varchar(50) DEFAULT NULL,
  `nombre_ips` varchar(150) NOT NULL,
  `direccion` varchar(600) DEFAULT NULL,
  `telefonos` varchar(100) DEFAULT NULL,
  `nombre_contacto` varchar(150) DEFAULT NULL,
  `correos` varchar(350) DEFAULT NULL,
  `observaciones` text,
  `estado` tinyint(1) DEFAULT '1',
  `enlace_conceptos` varchar(800) DEFAULT 'N/A',
  PRIMARY KEY (`id`),
  UNIQUE KEY `nit` (`nit`),
  KEY `id_ciudad` (`id_ciudad`),
  CONSTRAINT `proveedores_ibfk_1` FOREIGN KEY (`id_ciudad`) REFERENCES `ciudades` (`id`) ON DELETE RESTRICT
) ENGINE=InnoDB AUTO_INCREMENT=51 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `proveedores`
--

--
-- Table structure for table `tarifas`
--

DROP TABLE IF EXISTS `tarifas`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `tarifas` (
  `id` int NOT NULL AUTO_INCREMENT,
  `id_proveedor` int NOT NULL,
  `id_examen` int NOT NULL,
  `anio` year NOT NULL,
  `precio_costo` decimal(10,2) NOT NULL,
  `precio_venta` decimal(10,2) NOT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `unique_tarifa_proveedor_anio` (`id_examen`,`id_proveedor`,`anio`),
  KEY `id_proveedor` (`id_proveedor`),
  CONSTRAINT `tarifas_ibfk_1` FOREIGN KEY (`id_proveedor`) REFERENCES `proveedores` (`id`) ON DELETE CASCADE,
  CONSTRAINT `tarifas_ibfk_2` FOREIGN KEY (`id_examen`) REFERENCES `examenes` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=46 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `tarifas`
--


--
-- Table structure for table `usuarios`
--

DROP TABLE IF EXISTS `usuarios`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `usuarios` (
  `id` int NOT NULL AUTO_INCREMENT,
  `nombre` varchar(150) NOT NULL,
  `usuario` varchar(50) NOT NULL,
  `clave` varchar(255) NOT NULL,
  `rol` enum('admin','general','visualizador') NOT NULL,
  `estado` tinyint(1) DEFAULT '1',
  PRIMARY KEY (`id`),
  UNIQUE KEY `usuario` (`usuario`)
) ENGINE=InnoDB AUTO_INCREMENT=3 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `usuarios`
--

LOCK TABLES `usuarios` WRITE;
/*!40000 ALTER TABLE `usuarios` DISABLE KEYS */;
INSERT INTO `usuarios` VALUES (1,'Administrador del Sistema','admin','$2y$12$sp5X1Tu7xwCjHNYg2osnn.KMfqhf5bunSxYK4zREnPQX0b1AAx46K','admin',1),(2,'lector','lector','$2y$12$P0QrKS6y6sBmWS/vZ2aV3uGU.pvnhEysD/eHagJpUS6uW58Ge04sy','visualizador',1);
/*!40000 ALTER TABLE `usuarios` ENABLE KEYS */;
UNLOCK TABLES;
/*!40103 SET TIME_ZONE=@OLD_TIME_ZONE */;

/*!40101 SET SQL_MODE=@OLD_SQL_MODE */;
/*!40014 SET FOREIGN_KEY_CHECKS=@OLD_FOREIGN_KEY_CHECKS */;
/*!40014 SET UNIQUE_CHECKS=@OLD_UNIQUE_CHECKS */;
/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
/*!40111 SET SQL_NOTES=@OLD_SQL_NOTES */;

-- Dump completed on 2026-03-18  9:51:56
