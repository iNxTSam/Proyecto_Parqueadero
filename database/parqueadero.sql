
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

/*!40000 DROP DATABASE IF EXISTS `parqueadero`*/;

CREATE DATABASE /*!32312 IF NOT EXISTS*/ `parqueadero` /*!40100 DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci */ /*!80016 DEFAULT ENCRYPTION='N' */;

USE `parqueadero`;
DROP TABLE IF EXISTS `alertas`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `alertas` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `tipo` varchar(30) COLLATE utf8mb4_unicode_ci NOT NULL,
  `titulo` varchar(100) COLLATE utf8mb4_unicode_ci NOT NULL,
  `mensaje` text COLLATE utf8mb4_unicode_ci NOT NULL,
  `entidad_tipo` varchar(30) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `entidad_id` bigint unsigned DEFAULT NULL,
  `leida` tinyint(1) NOT NULL DEFAULT '0',
  `usuario_id` bigint unsigned DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `alertas_usuario_id_foreign` (`usuario_id`),
  KEY `alertas_leida_index` (`leida`),
  CONSTRAINT `alertas_usuario_id_foreign` FOREIGN KEY (`usuario_id`) REFERENCES `usuarios` (`id`) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

LOCK TABLES `alertas` WRITE;
/*!40000 ALTER TABLE `alertas` DISABLE KEYS */;
/*!40000 ALTER TABLE `alertas` ENABLE KEYS */;
UNLOCK TABLES;
DROP TABLE IF EXISTS `bahias`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `bahias` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `numero` varchar(10) COLLATE utf8mb4_unicode_ci NOT NULL,
  `tipo_vehiculo_id` bigint unsigned NOT NULL,
  `capacidad_maxima` smallint unsigned NOT NULL DEFAULT '1',
  `ubicacion` varchar(100) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `activa` tinyint(1) NOT NULL DEFAULT '1',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `bahias_numero_unique` (`numero`),
  KEY `bahias_tipo_vehiculo_id_index` (`tipo_vehiculo_id`),
  CONSTRAINT `bahias_tipo_vehiculo_id_foreign` FOREIGN KEY (`tipo_vehiculo_id`) REFERENCES `tipos_vehiculo` (`id`) ON DELETE RESTRICT
) ENGINE=InnoDB AUTO_INCREMENT=7 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

LOCK TABLES `bahias` WRITE;
/*!40000 ALTER TABLE `bahias` DISABLE KEYS */;
INSERT INTO `bahias` VALUES (1,'A001',1,30,'Bloque A - piso 1',1,'2026-11-15 11:05:45','2026-11-15 11:05:45'),(2,'A002',1,20,'Bloque A - piso 2',1,'2026-11-15 11:05:45','2026-11-15 11:05:45'),(3,'M001',2,30,'Zona motos',1,'2026-11-15 11:05:46','2026-11-15 11:05:46'),(4,'M002',2,15,'Zona motos - costado norte',1,'2026-11-15 11:05:46','2026-11-15 11:05:46'),(5,'B001',3,15,'Bicicletero principal',1,'2026-11-15 11:05:46','2026-11-15 11:05:46'),(6,'E001',4,4,'Bicicletero eléctrico',1,'2026-11-15 11:05:47','2026-11-15 11:05:47');
/*!40000 ALTER TABLE `bahias` ENABLE KEYS */;
UNLOCK TABLES;
DROP TABLE IF EXISTS `clientes`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `clientes` (
  `id` varchar(15) COLLATE utf8mb4_unicode_ci NOT NULL,
  `nombre` varchar(70) COLLATE utf8mb4_unicode_ci NOT NULL,
  `telefono` varchar(20) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `correo` varchar(50) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `activo` tinyint(1) NOT NULL DEFAULT '1',
  `rol_id` bigint unsigned NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `clientes_rol_id_index` (`rol_id`),
  CONSTRAINT `clientes_rol_id_foreign` FOREIGN KEY (`rol_id`) REFERENCES `rolclientes` (`id`) ON DELETE RESTRICT
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

LOCK TABLES `clientes` WRITE;
/*!40000 ALTER TABLE `clientes` DISABLE KEYS */;
INSERT INTO `clientes` VALUES ('900000001','Ana María Rodríguez','3001234567','ana@parqueadero.test',1,1,'2026-11-15 11:05:49','2026-11-15 11:05:49'),('900000002','Carlos Pérez','3109876543','carlos@parqueadero.test',1,2,'2026-11-15 11:05:49','2026-11-15 11:05:49');
/*!40000 ALTER TABLE `clientes` ENABLE KEYS */;
UNLOCK TABLES;
DROP TABLE IF EXISTS `contratos`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `contratos` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `vehiculo_id` varchar(10) COLLATE utf8mb4_unicode_ci NOT NULL,
  `propietario_id` varchar(15) COLLATE utf8mb4_unicode_ci NOT NULL,
  `tarifa_id` bigint unsigned NOT NULL,
  `fecha_inicio` date NOT NULL,
  `fecha_fin` date NOT NULL,
  `estado_id` bigint unsigned NOT NULL,
  `valor` decimal(10,2) NOT NULL,
  `observaciones` text COLLATE utf8mb4_unicode_ci,
  `created_by` bigint unsigned NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `contratos_tarifa_id_foreign` (`tarifa_id`),
  KEY `contratos_estado_id_foreign` (`estado_id`),
  KEY `contratos_created_by_foreign` (`created_by`),
  KEY `contratos_propietario_id_foreign` (`propietario_id`),
  KEY `contratos_vehiculo_id_estado_id_index` (`vehiculo_id`,`estado_id`),
  KEY `contratos_fecha_inicio_fecha_fin_index` (`fecha_inicio`,`fecha_fin`),
  CONSTRAINT `contratos_created_by_foreign` FOREIGN KEY (`created_by`) REFERENCES `usuarios` (`id`) ON DELETE RESTRICT,
  CONSTRAINT `contratos_estado_id_foreign` FOREIGN KEY (`estado_id`) REFERENCES `estados_contrato` (`id`) ON DELETE RESTRICT,
  CONSTRAINT `contratos_propietario_id_foreign` FOREIGN KEY (`propietario_id`) REFERENCES `clientes` (`id`) ON DELETE RESTRICT ON UPDATE CASCADE,
  CONSTRAINT `contratos_tarifa_id_foreign` FOREIGN KEY (`tarifa_id`) REFERENCES `tarifas` (`id`) ON DELETE RESTRICT,
  CONSTRAINT `contratos_vehiculo_id_foreign` FOREIGN KEY (`vehiculo_id`) REFERENCES `vehiculos` (`placa`) ON DELETE RESTRICT ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

LOCK TABLES `contratos` WRITE;
/*!40000 ALTER TABLE `contratos` DISABLE KEYS */;
/*!40000 ALTER TABLE `contratos` ENABLE KEYS */;
UNLOCK TABLES;
DROP TABLE IF EXISTS `entrada_salida`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `entrada_salida` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `propietario_id` varchar(15) COLLATE utf8mb4_unicode_ci NOT NULL,
  `vehiculo_id` varchar(10) COLLATE utf8mb4_unicode_ci NOT NULL,
  `ticket` varchar(20) COLLATE utf8mb4_unicode_ci NOT NULL,
  `fecha_entrada` datetime NOT NULL,
  `fecha_salida` datetime DEFAULT NULL,
  `marcar_salida` tinyint(1) NOT NULL DEFAULT '0',
  `registrado_por_id` bigint unsigned DEFAULT NULL,
  `salida_registrada_por_id` bigint unsigned DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `entrada_salida_ticket_unique` (`ticket`),
  KEY `entrada_salida_registrado_por_id_foreign` (`registrado_por_id`),
  KEY `entrada_salida_salida_registrada_por_id_foreign` (`salida_registrada_por_id`),
  KEY `entrada_salida_propietario_id_foreign` (`propietario_id`),
  KEY `entrada_salida_vehiculo_id_fecha_salida_index` (`vehiculo_id`,`fecha_salida`),
  KEY `entrada_salida_fecha_entrada_index` (`fecha_entrada`),
  CONSTRAINT `entrada_salida_propietario_id_foreign` FOREIGN KEY (`propietario_id`) REFERENCES `clientes` (`id`) ON DELETE RESTRICT ON UPDATE CASCADE,
  CONSTRAINT `entrada_salida_registrado_por_id_foreign` FOREIGN KEY (`registrado_por_id`) REFERENCES `usuarios` (`id`) ON DELETE SET NULL,
  CONSTRAINT `entrada_salida_salida_registrada_por_id_foreign` FOREIGN KEY (`salida_registrada_por_id`) REFERENCES `usuarios` (`id`) ON DELETE SET NULL,
  CONSTRAINT `entrada_salida_vehiculo_id_foreign` FOREIGN KEY (`vehiculo_id`) REFERENCES `vehiculos` (`placa`) ON DELETE RESTRICT ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

LOCK TABLES `entrada_salida` WRITE;
/*!40000 ALTER TABLE `entrada_salida` DISABLE KEYS */;
/*!40000 ALTER TABLE `entrada_salida` ENABLE KEYS */;
UNLOCK TABLES;
DROP TABLE IF EXISTS `estados_contrato`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `estados_contrato` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `nombre` varchar(30) COLLATE utf8mb4_unicode_ci NOT NULL,
  `descripcion` text COLLATE utf8mb4_unicode_ci,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `estados_contrato_nombre_unique` (`nombre`)
) ENGINE=InnoDB AUTO_INCREMENT=4 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

LOCK TABLES `estados_contrato` WRITE;
/*!40000 ALTER TABLE `estados_contrato` DISABLE KEYS */;
INSERT INTO `estados_contrato` VALUES (1,'Activo','Contrato vigente.','2026-11-15 11:05:40','2026-11-15 11:05:40'),(2,'Cancelado','Contrato cancelado por el administrador.','2026-11-15 11:05:41','2026-11-15 11:05:41'),(3,'Finalizado','Contrato cumplido o vencido.','2026-11-15 11:05:41','2026-11-15 11:05:41');
/*!40000 ALTER TABLE `estados_contrato` ENABLE KEYS */;
UNLOCK TABLES;
DROP TABLE IF EXISTS `migrations`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `migrations` (
  `id` int unsigned NOT NULL AUTO_INCREMENT,
  `migration` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `batch` int NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=10 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

LOCK TABLES `migrations` WRITE;
/*!40000 ALTER TABLE `migrations` DISABLE KEYS */;
INSERT INTO `migrations` VALUES (1,'2026_11_15_000001_create_catalogos_tables',1),(2,'2026_11_15_000002_create_usuarios_table',1),(3,'2026_11_15_000003_create_clientes_table',1),(4,'2026_11_15_000004_create_vehiculos_table',1),(5,'2026_11_15_000005_create_tarifas_y_bahias_tables',1),(6,'2026_11_15_000006_create_contratos_table',1),(7,'2026_11_15_000007_create_entrada_salida_table',1),(8,'2026_11_15_000008_create_recibos_table',1),(9,'2026_11_15_000009_create_alertas_y_reportes_tables',1);
/*!40000 ALTER TABLE `migrations` ENABLE KEYS */;
UNLOCK TABLES;
DROP TABLE IF EXISTS `recibos`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `recibos` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `numero` varchar(20) COLLATE utf8mb4_unicode_ci NOT NULL,
  `contrato_id` bigint unsigned NOT NULL,
  `fecha_emision` date NOT NULL,
  `valor` decimal(10,2) NOT NULL,
  `concepto` text COLLATE utf8mb4_unicode_ci,
  `estado` varchar(20) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'pendiente',
  `emitido_por_id` bigint unsigned NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `recibos_numero_unique` (`numero`),
  KEY `recibos_contrato_id_foreign` (`contrato_id`),
  KEY `recibos_emitido_por_id_foreign` (`emitido_por_id`),
  KEY `recibos_fecha_emision_index` (`fecha_emision`),
  KEY `recibos_estado_index` (`estado`),
  CONSTRAINT `recibos_contrato_id_foreign` FOREIGN KEY (`contrato_id`) REFERENCES `contratos` (`id`) ON DELETE RESTRICT ON UPDATE CASCADE,
  CONSTRAINT `recibos_emitido_por_id_foreign` FOREIGN KEY (`emitido_por_id`) REFERENCES `usuarios` (`id`) ON DELETE RESTRICT
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

LOCK TABLES `recibos` WRITE;
/*!40000 ALTER TABLE `recibos` DISABLE KEYS */;
/*!40000 ALTER TABLE `recibos` ENABLE KEYS */;
UNLOCK TABLES;
DROP TABLE IF EXISTS `reportes`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `reportes` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `nombre` varchar(100) COLLATE utf8mb4_unicode_ci NOT NULL,
  `tipo` varchar(30) COLLATE utf8mb4_unicode_ci NOT NULL,
  `formato` varchar(10) COLLATE utf8mb4_unicode_ci NOT NULL,
  `ruta_archivo` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `parametros` json DEFAULT NULL,
  `generado_por_id` bigint unsigned NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `reportes_generado_por_id_foreign` (`generado_por_id`),
  KEY `reportes_tipo_index` (`tipo`),
  CONSTRAINT `reportes_generado_por_id_foreign` FOREIGN KEY (`generado_por_id`) REFERENCES `usuarios` (`id`) ON DELETE RESTRICT
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

LOCK TABLES `reportes` WRITE;
/*!40000 ALTER TABLE `reportes` DISABLE KEYS */;
/*!40000 ALTER TABLE `reportes` ENABLE KEYS */;
UNLOCK TABLES;
DROP TABLE IF EXISTS `rolclientes`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `rolclientes` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `nombre` varchar(30) COLLATE utf8mb4_unicode_ci NOT NULL,
  `descripcion` text COLLATE utf8mb4_unicode_ci,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `rolclientes_nombre_unique` (`nombre`)
) ENGINE=InnoDB AUTO_INCREMENT=3 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

LOCK TABLES `rolclientes` WRITE;
/*!40000 ALTER TABLE `rolclientes` DISABLE KEYS */;
INSERT INTO `rolclientes` VALUES (1,'Aprendiz','Cliente con contrato de parqueadero.','2026-11-15 11:05:40','2026-11-15 11:05:40'),(2,'Natural','Cliente ocasional, paga por permanencia.','2026-11-15 11:05:40','2026-11-15 11:05:40');
/*!40000 ALTER TABLE `rolclientes` ENABLE KEYS */;
UNLOCK TABLES;
DROP TABLE IF EXISTS `roles`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `roles` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `nombre` varchar(50) COLLATE utf8mb4_unicode_ci NOT NULL,
  `descripcion` text COLLATE utf8mb4_unicode_ci,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `roles_nombre_unique` (`nombre`)
) ENGINE=InnoDB AUTO_INCREMENT=4 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

LOCK TABLES `roles` WRITE;
/*!40000 ALTER TABLE `roles` DISABLE KEYS */;
INSERT INTO `roles` VALUES (1,'Super administrador','Administra tarifas, bahías, reportes y configuración.','2026-11-15 11:05:39','2026-11-15 11:05:39'),(2,'Administrador','Gestiona usuarios, clientes, vehículos y contratos.','2026-11-15 11:05:39','2026-11-15 11:05:39'),(3,'Vigilante','Registra entradas y salidas de vehículos.','2026-11-15 11:05:40','2026-11-15 11:05:40');
/*!40000 ALTER TABLE `roles` ENABLE KEYS */;
UNLOCK TABLES;
DROP TABLE IF EXISTS `tarifas`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `tarifas` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `nombre` varchar(50) COLLATE utf8mb4_unicode_ci NOT NULL,
  `tipo` varchar(20) COLLATE utf8mb4_unicode_ci NOT NULL,
  `tipo_vehiculo_id` bigint unsigned NOT NULL,
  `valor` decimal(10,2) NOT NULL,
  `dias` smallint unsigned NOT NULL DEFAULT '30',
  `activa` tinyint(1) NOT NULL DEFAULT '1',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `tarifas_tipo_vehiculo_id_index` (`tipo_vehiculo_id`),
  CONSTRAINT `tarifas_tipo_vehiculo_id_foreign` FOREIGN KEY (`tipo_vehiculo_id`) REFERENCES `tipos_vehiculo` (`id`) ON DELETE RESTRICT
) ENGINE=InnoDB AUTO_INCREMENT=9 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

LOCK TABLES `tarifas` WRITE;
/*!40000 ALTER TABLE `tarifas` DISABLE KEYS */;
INSERT INTO `tarifas` VALUES (1,'Carro - Día','Dia',1,8000.00,1,1,'2026-11-15 11:05:43','2026-11-15 11:05:43'),(2,'Carro - Semana','Semana',1,45000.00,7,1,'2026-11-15 11:05:43','2026-11-15 11:05:43'),(3,'Carro - Quincena','Quincena',1,80000.00,15,1,'2026-11-15 11:05:43','2026-11-15 11:05:43'),(4,'Carro - Mes','Mes',1,140000.00,30,1,'2026-11-15 11:05:44','2026-11-15 11:05:44'),(5,'Moto - Día','Dia',2,5000.00,1,1,'2026-11-15 11:05:44','2026-11-15 11:05:44'),(6,'Moto - Semana','Semana',2,28000.00,7,1,'2026-11-15 11:05:44','2026-11-15 11:05:44'),(7,'Moto - Quincena','Quincena',2,50000.00,15,1,'2026-11-15 11:05:44','2026-11-15 11:05:44'),(8,'Moto - Mes','Mes',2,90000.00,30,1,'2026-11-15 11:05:45','2026-11-15 11:05:45');
/*!40000 ALTER TABLE `tarifas` ENABLE KEYS */;
UNLOCK TABLES;
DROP TABLE IF EXISTS `tipos_vehiculo`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `tipos_vehiculo` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `nombre` varchar(50) COLLATE utf8mb4_unicode_ci NOT NULL,
  `descripcion` text COLLATE utf8mb4_unicode_ci,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `tipos_vehiculo_nombre_unique` (`nombre`)
) ENGINE=InnoDB AUTO_INCREMENT=5 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

LOCK TABLES `tipos_vehiculo` WRITE;
/*!40000 ALTER TABLE `tipos_vehiculo` DISABLE KEYS */;
INSERT INTO `tipos_vehiculo` VALUES (1,'Carro','Automóviles y camionetas.','2026-11-15 11:05:41','2026-11-15 11:05:41'),(2,'Motocicleta','Motos de dos ruedas.','2026-11-15 11:05:42','2026-11-15 11:05:42'),(3,'Bicicleta','Bicicletas convencionales.','2026-11-15 11:05:42','2026-11-15 11:05:42'),(4,'Bicicleta eléctrica','Bicicletas con motor eléctrico.','2026-11-15 11:05:42','2026-11-15 11:05:42');
/*!40000 ALTER TABLE `tipos_vehiculo` ENABLE KEYS */;
UNLOCK TABLES;
DROP TABLE IF EXISTS `usuarios`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `usuarios` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `nombre` varchar(100) COLLATE utf8mb4_unicode_ci NOT NULL,
  `cedula` varchar(20) COLLATE utf8mb4_unicode_ci NOT NULL,
  `carnet` varchar(20) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `contacto` varchar(100) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `email` varchar(100) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `password` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `rol_id` bigint unsigned NOT NULL,
  `activo` tinyint(1) NOT NULL DEFAULT '1',
  `remember_token` varchar(100) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `usuarios_cedula_unique` (`cedula`),
  KEY `usuarios_rol_id_index` (`rol_id`),
  CONSTRAINT `usuarios_rol_id_foreign` FOREIGN KEY (`rol_id`) REFERENCES `roles` (`id`) ON DELETE RESTRICT
) ENGINE=InnoDB AUTO_INCREMENT=4 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

LOCK TABLES `usuarios` WRITE;
/*!40000 ALTER TABLE `usuarios` DISABLE KEYS */;
INSERT INTO `usuarios` VALUES (1,'Super Administrador','1000000001',NULL,NULL,'superadmin@parqueadero.test','$2y$12$vcvZskhb/fwKeSaHyZj2AOniGVowpMM5nFf.yXakmGSqWJ1ZvpYDK',1,1,NULL,'2026-11-15 11:05:47','2026-11-15 11:05:47'),(2,'Administrador','1000000002',NULL,NULL,'admin@parqueadero.test','$2y$12$12btPN3PCQ9r6gFWGE/KHOkwHvXTX6hioeuD0o6W0rZma8BogI81S',2,1,NULL,'2026-11-15 11:05:48','2026-11-15 11:05:48'),(3,'Vigilante','1000000003',NULL,NULL,'vigilante@parqueadero.test','$2y$12$8r.TUjlM1B4mDRkyqgKmeu6./W034eKKtry9KPuZvTZ0pdCGgX4iu',3,1,NULL,'2026-11-15 11:05:49','2026-11-15 11:05:49');
/*!40000 ALTER TABLE `usuarios` ENABLE KEYS */;
UNLOCK TABLES;
DROP TABLE IF EXISTS `vehiculos`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `vehiculos` (
  `placa` varchar(10) COLLATE utf8mb4_unicode_ci NOT NULL,
  `tipo_vehiculo_id` bigint unsigned NOT NULL,
  `propietario_id` varchar(15) COLLATE utf8mb4_unicode_ci NOT NULL,
  `autorizado_por_id` bigint unsigned DEFAULT NULL,
  `color` varchar(30) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `marca` varchar(50) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `modelo` varchar(50) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `estado_parqueo` tinyint(1) NOT NULL DEFAULT '0',
  `activo` tinyint(1) NOT NULL DEFAULT '1',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`placa`),
  KEY `vehiculos_autorizado_por_id_foreign` (`autorizado_por_id`),
  KEY `vehiculos_propietario_id_index` (`propietario_id`),
  KEY `vehiculos_tipo_vehiculo_id_index` (`tipo_vehiculo_id`),
  KEY `vehiculos_estado_parqueo_index` (`estado_parqueo`),
  CONSTRAINT `vehiculos_autorizado_por_id_foreign` FOREIGN KEY (`autorizado_por_id`) REFERENCES `usuarios` (`id`) ON DELETE SET NULL,
  CONSTRAINT `vehiculos_propietario_id_foreign` FOREIGN KEY (`propietario_id`) REFERENCES `clientes` (`id`) ON DELETE RESTRICT ON UPDATE CASCADE,
  CONSTRAINT `vehiculos_tipo_vehiculo_id_foreign` FOREIGN KEY (`tipo_vehiculo_id`) REFERENCES `tipos_vehiculo` (`id`) ON DELETE RESTRICT
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

LOCK TABLES `vehiculos` WRITE;
/*!40000 ALTER TABLE `vehiculos` DISABLE KEYS */;
INSERT INTO `vehiculos` VALUES ('ABC123',1,'900000001',NULL,'Gris','Mazda','2020',0,1,'2026-11-15 11:05:49','2026-11-15 11:05:49'),('XYZ45A',2,'900000002',NULL,'Negro','Yamaha','2022',0,1,'2026-11-15 11:05:50','2026-11-15 11:05:50');
/*!40000 ALTER TABLE `vehiculos` ENABLE KEYS */;
UNLOCK TABLES;
/*!40103 SET TIME_ZONE=@OLD_TIME_ZONE */;

/*!40101 SET SQL_MODE=@OLD_SQL_MODE */;
/*!40014 SET FOREIGN_KEY_CHECKS=@OLD_FOREIGN_KEY_CHECKS */;
/*!40014 SET UNIQUE_CHECKS=@OLD_UNIQUE_CHECKS */;
/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
/*!40111 SET SQL_NOTES=@OLD_SQL_NOTES */;

