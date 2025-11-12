# Host: 127.0.0.1  (Version 5.5.5-10.4.32-MariaDB)
# Date: 2025-10-12 21:05:18
# Generator: MySQL-Front 6.0  (Build 2.20)


#
# Structure for table "categorias"
#

DROP TABLE IF EXISTS `categorias`;
CREATE TABLE `categorias` (
  `idCategoria` tinyint(3) unsigned NOT NULL AUTO_INCREMENT,
  `nombreCategoria` varchar(50) NOT NULL,
  `descripcion` varchar(45) NOT NULL,
  `estado` tinyint(4) NOT NULL DEFAULT 1,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`idCategoria`)
) ENGINE=InnoDB AUTO_INCREMENT=4 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

#
# Data for table "categorias"
#

INSERT INTO `categorias` VALUES (1,'Futbol','',1,NULL,NULL),(2,'Basket','',1,NULL,NULL),(3,'Deportivo','',1,NULL,NULL);

#
# Structure for table "failed_jobs"
#

DROP TABLE IF EXISTS `failed_jobs`;
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

#
# Data for table "failed_jobs"
#


#
# Structure for table "migrations"
#

DROP TABLE IF EXISTS `migrations`;
CREATE TABLE `migrations` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `migration` varchar(255) NOT NULL,
  `batch` int(11) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=23 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

#
# Data for table "migrations"
#

INSERT INTO `migrations` VALUES (1,'2014_10_12_000000_create_users_table',1),(2,'2014_10_12_100000_create_password_reset_tokens_table',1),(3,'2014_10_12_100000_create_password_resets_table',1),(4,'2019_08_19_000000_create_failed_jobs_table',1),(5,'2019_12_14_000001_create_personal_access_tokens_table',1),(6,'2025_07_26_143013_create_categorias_table',1),(7,'2025_07_26_143014_create_cliente_naturals_table',1),(8,'2025_07_26_143015_create_cliente_establecimientos_table',1),(9,'2025_07_26_143016_create_empleados_table',1),(10,'2025_07_26_143019_create_tallas_table',1),(11,'2025_07_26_143025_create_opcions_table',1),(12,'2025_07_26_143027_create_ventas_table',1),(13,'2025_07_26_143028_create_disenos_table',1),(14,'2025_07_26_143029_create_productos_table',1),(15,'2025_07_26_143032_create_detalle_ventas_table',1),(16,'2025_07_26_143054_create_caracteristicas_table',1),(17,'2025_07_26_143100_create_variantes_table',1),(18,'2025_07_26_143101_create_variante_caracteristicas_table',1),(19,'2025_07_26_143105_create_producto_opcions_table',1),(20,'2025_07_26_143110_create_producto_tallas_table',1),(21,'2025_07_26_143133_create_transaccions_table',1),(22,'2025_07_26_143200_add_foreign_keys_final',1);

#
# Structure for table "opcions"
#

DROP TABLE IF EXISTS `opcions`;
CREATE TABLE `opcions` (
  `idOpcion` tinyint(3) unsigned NOT NULL AUTO_INCREMENT,
  `nombre` varchar(50) NOT NULL,
  `descripcion` varchar(45) NOT NULL,
  `estado` tinyint(4) NOT NULL DEFAULT 1,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`idOpcion`)
) ENGINE=InnoDB AUTO_INCREMENT=10 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

#
# Data for table "opcions"
#

INSERT INTO `opcions` VALUES (1,'cuello','futbol, basket, vestir',1,'2025-09-24 17:52:02','2025-09-24 17:52:02'),(2,'manga','futbol, vestir',1,'2025-09-24 17:52:02','2025-09-24 17:52:02'),(3,'tiposublimado','corto',1,'2025-09-24 17:52:02','2025-09-24 17:52:02'),(4,'capucha','chamarra',1,'2025-09-24 17:52:02','2025-09-24 17:52:02'),(5,'pretina','chamarra',1,'2025-09-24 17:52:02','2025-09-24 17:52:02'),(6,'puño','chamarra',1,'2025-09-24 17:52:02','2025-09-24 17:52:02'),(7,'Logotipo','buzo',1,'2025-09-24 17:52:02','2025-09-24 17:52:02'),(8,'tela','Todo',1,'2025-09-24 17:52:02','2025-09-24 17:52:02'),(9,'tallas','Todo',1,'2025-09-24 17:52:02','2025-09-24 17:52:02');

#
# Structure for table "caracteristicas"
#

DROP TABLE IF EXISTS `caracteristicas`;
CREATE TABLE `caracteristicas` (
  `idCaracteristica` tinyint(3) unsigned NOT NULL AUTO_INCREMENT,
  `nombre` varchar(50) NOT NULL,
  `descripcion` varchar(45) NOT NULL,
  `estado` tinyint(4) NOT NULL DEFAULT 1,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `idOpcion` tinyint(3) unsigned NOT NULL,
  PRIMARY KEY (`idCaracteristica`),
  KEY `caracteristicas_idopcion_foreign` (`idOpcion`),
  CONSTRAINT `caracteristicas_idopcion_foreign` FOREIGN KEY (`idOpcion`) REFERENCES `opcions` (`idOpcion`) ON DELETE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=22 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

#
# Data for table "caracteristicas"
#

INSERT INTO `caracteristicas` VALUES (1,'v','P.futbol, P.basket, P.vestir',1,'2025-09-24 17:53:17','2025-09-24 17:53:17',1),(2,'redondo','P.futbol, P.basket, P.vestir',1,'2025-09-24 17:53:17','2025-09-24 17:53:17',1),(3,'semicadete','P.futbol, P.basket, P.vestir',1,'2025-09-24 17:53:17','2025-09-24 17:53:17',1),(4,'camisa','P.futbol, P.basket, P.vestir',1,'2025-09-24 17:53:17','2025-09-24 17:53:17',1),(5,'larga','P.futbol, P.vestir',1,'2025-09-24 17:53:17','2025-09-24 17:53:17',2),(6,'corta','P.futbol, P.vestir',1,'2025-09-24 17:53:17','2025-09-24 17:53:17',2),(7,'full sublimado','corto',1,'2025-09-24 17:53:17','2025-09-24 17:53:17',3),(8,'semisublimado','corto',1,'2025-09-24 17:53:17','2025-09-24 17:53:17',3),(9,'con capucha','chamarra',1,'2025-09-24 17:53:17','2025-09-24 17:53:17',4),(10,'sin capucha','chamarra',1,'2025-09-24 17:53:17','2025-09-24 17:53:17',4),(11,'pretina normal','chamarra',1,'2025-09-24 17:53:17','2025-09-24 17:53:17',5),(12,'pretina tejido','chamarra',1,'2025-09-24 17:53:17','2025-09-24 17:53:17',5),(13,'sin pretina','chamarra',1,'2025-09-24 17:53:17','2025-09-24 17:53:17',5),(14,'puño normal','chamarra',1,'2025-09-24 17:53:17','2025-09-24 17:53:17',6),(15,'puño tejido','chamarra',1,'2025-09-24 17:53:17','2025-09-24 17:53:17',6),(16,'sin puño','chamarra',1,'2025-09-24 17:53:17','2025-09-24 17:53:17',6),(17,'bordado','buzo',1,'2025-09-24 17:53:17','2025-09-24 17:53:17',7),(18,'dtf','buzo',1,'2025-09-24 17:53:17','2025-09-24 17:53:17',7),(19,'drifit','tela',1,'2025-09-24 17:53:17','2025-09-24 17:53:17',8),(20,'win','tela',1,'2025-09-24 17:53:17','2025-09-24 17:53:17',8),(21,'impala','tela',1,'2025-09-24 17:53:17','2025-09-24 17:53:17',8);

#
# Structure for table "password_reset_tokens"
#

DROP TABLE IF EXISTS `password_reset_tokens`;
CREATE TABLE `password_reset_tokens` (
  `email` varchar(255) NOT NULL,
  `token` varchar(255) NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`email`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

#
# Data for table "password_reset_tokens"
#


#
# Structure for table "password_resets"
#

DROP TABLE IF EXISTS `password_resets`;
CREATE TABLE `password_resets` (
  `email` varchar(255) NOT NULL,
  `token` varchar(255) NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  KEY `password_resets_email_index` (`email`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

#
# Data for table "password_resets"
#


#
# Structure for table "personal_access_tokens"
#

DROP TABLE IF EXISTS `personal_access_tokens`;
CREATE TABLE `personal_access_tokens` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `tokenable_type` varchar(255) NOT NULL,
  `tokenable_id` bigint(20) unsigned NOT NULL,
  `name` varchar(255) NOT NULL,
  `token` varchar(64) NOT NULL,
  `abilities` text DEFAULT NULL,
  `last_used_at` timestamp NULL DEFAULT NULL,
  `expires_at` timestamp NULL DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `personal_access_tokens_token_unique` (`token`),
  KEY `personal_access_tokens_tokenable_type_tokenable_id_index` (`tokenable_type`,`tokenable_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

#
# Data for table "personal_access_tokens"
#


#
# Structure for table "tallas"
#

DROP TABLE IF EXISTS `tallas`;
CREATE TABLE `tallas` (
  `idTallas` smallint(5) unsigned NOT NULL AUTO_INCREMENT,
  `nombre` varchar(6) NOT NULL,
  `estado` tinyint(4) DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`idTallas`)
) ENGINE=InnoDB AUTO_INCREMENT=19 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

#
# Data for table "tallas"
#

INSERT INTO `tallas` VALUES (1,'3XL',1,NULL,NULL),(2,'2XL',1,NULL,NULL),(3,'XL',1,NULL,NULL),(4,'L',1,NULL,NULL),(5,'M',1,NULL,NULL),(6,'S',1,NULL,NULL),(7,'12',1,NULL,NULL),(8,'10',1,NULL,NULL),(9,'8',1,NULL,NULL),(10,'6',1,NULL,NULL),(11,'4',1,NULL,NULL),(12,'2',1,NULL,NULL),(13,'2XLD',1,NULL,NULL),(14,'XLD',1,NULL,NULL),(15,'LD',1,NULL,NULL),(16,'MD',1,NULL,NULL),(17,'SD',1,NULL,NULL),(18,'14D',1,NULL,NULL);

#
# Structure for table "users"
#

DROP TABLE IF EXISTS `users`;
CREATE TABLE `users` (
  `idUser` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `ci` varchar(255) DEFAULT NULL,
  `name` varchar(255) NOT NULL,
  `primerApellido` varchar(255) NOT NULL,
  `segundApellido` varchar(255) DEFAULT NULL,
  `email` varchar(255) NOT NULL,
  `estado` tinyint(4) NOT NULL DEFAULT 1,
  `email_verified_at` timestamp NULL DEFAULT NULL,
  `telefono` bigint(1) DEFAULT NULL,
  `password` varchar(255) NOT NULL,
  `remember_token` varchar(100) DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`idUser`),
  UNIQUE KEY `users_email_unique` (`email`)
) ENGINE=InnoDB AUTO_INCREMENT=7 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

#
# Data for table "users"
#

INSERT INTO `users` VALUES (1,'4565763456','administrador','admin',NULL,'admin@gmail.com',1,NULL,NULL,'$2y$10$qsx30vrHOBh0THWoxFhz..S.7RZ98NYL7pH1MnEq9eHb3HjglsG/e','lhR8SsQmlfB4DOutBTujr98e6pafkBIQC7mLXvKV6yeYSfRssPavdpmI9BXi',NULL,NULL),(2,'5236986','Silvia','Coca',NULL,'silvia@gmail.com',1,NULL,489575,'$2y$12$TzuUBvrN09wxdA6EO0LuEeOy/QvcHRpviTO7.ytvRrc42f7m0fXHa',NULL,'2025-09-24 20:46:40','2025-09-24 20:46:40'),(3,'85789','Pamela','Santos',NULL,'pame@gmail.com',1,NULL,70255555,'$2y$12$Ik8JsbZ/BpCLlaDkpz1KIuLPLY6uPe/uqzp8GrHj0hU82Rt5bFYbO',NULL,'2025-09-24 20:54:01','2025-10-11 19:51:15'),(4,'4246464','Eva','suar',NULL,'eva@gmail.com',1,NULL,70546464864,'$2y$12$NnnyVc/me//MN23tEygtsOiaBE5QRZqG9iitsxmIruTJIqA2Hyg6u',NULL,'2025-10-04 15:25:55','2025-10-04 15:25:55'),(5,'54646546','Daniela','Perez',NULL,'dani@gmail.com',1,NULL,4575656,'$2y$12$Ii/8IHPHGdgwvv6YRGWU7Osh8Ck4QSrU8y5O7SfEXInZKxSsYQw4O',NULL,'2025-10-04 15:27:02','2025-10-04 15:27:02'),(6,NULL,'Rosa','Rosa',NULL,'rosa@gmail.com',1,NULL,NULL,'$2y$12$pOL5Jy/aTIFAn0mwFlPc8uYnxMTvI.lNRxYS1MJpCIUCrGA56eMum',NULL,'2025-10-08 21:29:16','2025-10-08 21:29:16');

#
# Structure for table "empleados"
#

DROP TABLE IF EXISTS `empleados`;
CREATE TABLE `empleados` (
  `idEmpleado` int(10) unsigned NOT NULL,
  `cargo` varchar(45) NOT NULL,
  `rol` enum('administrador','diseñador','operador','cliente','vendedor') NOT NULL,
  `estado` tinyint(4) NOT NULL DEFAULT 1,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`idEmpleado`),
  CONSTRAINT `empleados_idempleado_foreign` FOREIGN KEY (`idEmpleado`) REFERENCES `users` (`idUser`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

#
# Data for table "empleados"
#

INSERT INTO `empleados` VALUES (1,'Jefe','administrador',1,NULL,'2025-09-24 19:57:11'),(2,'Costurera','operador',1,NULL,NULL),(3,'Diseño grafico','diseñador',1,NULL,NULL),(4,'Costurera','operador',1,'2025-10-04 15:25:55','2025-10-04 15:25:55'),(5,'Diseñadora Grafico','diseñador',1,'2025-10-04 15:27:02','2025-10-11 21:34:36');

#
# Structure for table "cliente_naturals"
#

DROP TABLE IF EXISTS `cliente_naturals`;
CREATE TABLE `cliente_naturals` (
  `idCliente` int(10) unsigned NOT NULL,
  `nit` bigint(20) DEFAULT NULL,
  `estado` tinyint(4) NOT NULL DEFAULT 1,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`idCliente`),
  CONSTRAINT `cliente_naturals_idcliente_foreign` FOREIGN KEY (`idCliente`) REFERENCES `users` (`idUser`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

#
# Data for table "cliente_naturals"
#

INSERT INTO `cliente_naturals` VALUES (6,366449841,1,NULL,NULL);

#
# Structure for table "cliente_establecimientos"
#

DROP TABLE IF EXISTS `cliente_establecimientos`;
CREATE TABLE `cliente_establecimientos` (
  `idEstablecimiento` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `nit` bigint(20) DEFAULT NULL,
  `razonSocial` varchar(100) NOT NULL,
  `tipoEstablecimiento` varchar(50) NOT NULL,
  `domicilioFiscal` varchar(255) DEFAULT NULL,
  `idRepresentante` int(10) unsigned NOT NULL,
  `estado` tinyint(4) NOT NULL DEFAULT 1,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`idEstablecimiento`),
  KEY `cliente_establecimientos_idrepresentante_foreign` (`idRepresentante`),
  CONSTRAINT `cliente_establecimientos_idrepresentante_foreign` FOREIGN KEY (`idRepresentante`) REFERENCES `users` (`idUser`) ON DELETE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

#
# Data for table "cliente_establecimientos"
#


#
# Structure for table "variantes"
#

DROP TABLE IF EXISTS `variantes`;
CREATE TABLE `variantes` (
  `id` tinyint(3) unsigned NOT NULL AUTO_INCREMENT,
  `nombre` varchar(50) NOT NULL,
  `descripcion` varchar(45) NOT NULL,
  `estado` tinyint(4) NOT NULL DEFAULT 1,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=5 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

#
# Data for table "variantes"
#

INSERT INTO `variantes` VALUES (1,'polera','Camiseta deportiva para fútbol',1,'2025-09-24 19:07:02','2025-09-24 19:07:02'),(2,'corto ','Short deportivo para fútbol',1,'2025-09-24 19:07:02','2025-09-24 19:07:02'),(3,'chamarra','Chaqueta deportiva o casual',1,'2025-09-24 19:07:02','2025-09-24 19:07:02'),(4,'buzo','Sudadera o jersey deportivo',1,'2025-09-24 19:07:02','2025-09-24 19:07:02');

#
# Structure for table "variante_caracteristicas"
#

DROP TABLE IF EXISTS `variante_caracteristicas`;
CREATE TABLE `variante_caracteristicas` (
  `idVariantesCaracteristicas` tinyint(3) unsigned NOT NULL AUTO_INCREMENT,
  `nombre` varchar(50) NOT NULL,
  `descripcion` varchar(45) NOT NULL,
  `estado` tinyint(4) NOT NULL DEFAULT 1,
  `precioAdicional` decimal(8,2) DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `idCaracteristica` tinyint(3) unsigned NOT NULL,
  `idVariante` tinyint(3) unsigned NOT NULL,
  PRIMARY KEY (`idVariantesCaracteristicas`),
  KEY `variante_caracteristicas_idvariante_foreign` (`idVariante`),
  KEY `variante_caracteristicas_idcaracteristica_foreign` (`idCaracteristica`),
  CONSTRAINT `variante_caracteristicas_idcaracteristica_foreign` FOREIGN KEY (`idCaracteristica`) REFERENCES `caracteristicas` (`idCaracteristica`) ON DELETE CASCADE,
  CONSTRAINT `variante_caracteristicas_idvariante_foreign` FOREIGN KEY (`idVariante`) REFERENCES `variantes` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=35 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

#
# Data for table "variante_caracteristicas"
#


#
# Structure for table "ventas"
#

DROP TABLE IF EXISTS `ventas`;
CREATE TABLE `ventas` (
  `idVenta` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `subtotal` decimal(8,2) DEFAULT NULL,
  `total` decimal(10,2) DEFAULT NULL,
  `fechaEntrega` date NOT NULL,
  `lugarEntrega` varchar(100) NOT NULL DEFAULT 'Por definir',
  `estadoPedido` tinyint(4) NOT NULL DEFAULT 0,
  `saldo` decimal(8,2) DEFAULT NULL,
  `estado` char(1) NOT NULL DEFAULT '1' COMMENT '0: Solicitado, 1: Diseño, 2: Confeccion, 3: Entregado',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `idEmpleado` int(10) unsigned NOT NULL,
  `idCliente` int(10) unsigned DEFAULT NULL,
  `idEstablecimiento` int(10) unsigned DEFAULT NULL,
  `idUser` int(10) unsigned DEFAULT NULL,
  PRIMARY KEY (`idVenta`),
  KEY `ventas_idempleado_foreign` (`idEmpleado`),
  KEY `ventas_idcliente_foreign` (`idCliente`),
  KEY `ventas_idestablecimiento_foreign` (`idEstablecimiento`),
  CONSTRAINT `ventas_idcliente_foreign` FOREIGN KEY (`idCliente`) REFERENCES `cliente_naturals` (`idCliente`) ON DELETE CASCADE,
  CONSTRAINT `ventas_idempleado_foreign` FOREIGN KEY (`idEmpleado`) REFERENCES `empleados` (`idEmpleado`) ON DELETE CASCADE,
  CONSTRAINT `ventas_idestablecimiento_foreign` FOREIGN KEY (`idEstablecimiento`) REFERENCES `cliente_establecimientos` (`idEstablecimiento`) ON DELETE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=14 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

#
# Data for table "ventas"
#

INSERT INTO `ventas` VALUES (13,840.00,840.00,'2025-10-24','Recojo en tienda',2,340.00,'0','2025-10-10 17:37:47','2025-10-12 23:20:20',1,6,NULL,NULL),(14,770.00,770.00,'2025-10-29','Recojo en tienda',0,70.00,'0','2025-10-12 23:05:41','2025-10-12 23:20:17',1,6,NULL,NULL),(22,350.00,350.00,'2025-10-18','Recojo en tienda',0,350.00,'1','2025-10-12 23:19:03','2025-10-12 23:19:03',1,6,NULL,NULL);

#
# Structure for table "detalle_ventas"
#

DROP TABLE IF EXISTS `detalle_ventas`;
CREATE TABLE `detalle_ventas` (
  `iddetalleVenta` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `cantidad` int(11) NOT NULL DEFAULT 1,
  `nombrePersonalizado` varchar(45) DEFAULT NULL,
  `numeroPersonalizado` varchar(10) DEFAULT NULL,
  `textoAdicional` varchar(45) DEFAULT NULL,
  `observacion` varchar(45) DEFAULT NULL,
  `precioUnitario` decimal(5,2) NOT NULL,
  `subtotal` decimal(8,2) DEFAULT NULL,
  `descuento` decimal(5,2) DEFAULT NULL,
  `descripcion` varchar(60) DEFAULT NULL,
  `estado` tinyint(4) NOT NULL DEFAULT 1,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `idTallas` smallint(5) unsigned NOT NULL,
  `idVenta` int(10) unsigned NOT NULL,
  `idEmpleado` int(10) unsigned NOT NULL,
  PRIMARY KEY (`iddetalleVenta`),
  KEY `detalle_ventas_idtalla_foreign` (`idTallas`),
  KEY `detalle_ventas_idventa_foreign` (`idVenta`),
  KEY `detalle_ventas_idempleado_foreign` (`idEmpleado`),
  CONSTRAINT `detalle_ventas_idempleado_foreign` FOREIGN KEY (`idEmpleado`) REFERENCES `empleados` (`idEmpleado`) ON DELETE NO ACTION ON UPDATE NO ACTION,
  CONSTRAINT `detalle_ventas_idtalla_foreign` FOREIGN KEY (`idTallas`) REFERENCES `tallas` (`idTallas`) ON DELETE CASCADE,
  CONSTRAINT `detalle_ventas_idventa_foreign` FOREIGN KEY (`idVenta`) REFERENCES `ventas` (`idVenta`) ON DELETE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=15 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

#
# Data for table "detalle_ventas"
#

INSERT INTO `detalle_ventas` VALUES (14,12,NULL,NULL,NULL,NULL,70.00,NULL,NULL,NULL,1,'2025-10-10 17:37:47','2025-10-10 17:37:47',5,13,1),(15,5,NULL,NULL,NULL,NULL,70.00,NULL,NULL,NULL,1,'2025-10-12 23:05:41','2025-10-12 23:05:41',5,14,1),(16,6,NULL,NULL,NULL,NULL,70.00,NULL,NULL,NULL,1,'2025-10-12 23:05:41','2025-10-12 23:05:41',4,14,1),(17,5,NULL,NULL,NULL,NULL,70.00,NULL,NULL,NULL,1,'2025-10-12 23:19:03','2025-10-12 23:19:03',6,22,1);

#
# Structure for table "disenos"
#

DROP TABLE IF EXISTS `disenos`;
CREATE TABLE `disenos` (
  `idDiseno` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `archivo` varchar(255) DEFAULT NULL,
  `comentario` varchar(45) DEFAULT NULL,
  `estado` tinyint(4) NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `estadoDiseño` enum('en proceso','terminado') NOT NULL DEFAULT 'en proceso',
  `iddetalleVenta` int(10) unsigned DEFAULT NULL,
  `idEmpleado` int(10) unsigned DEFAULT NULL,
  PRIMARY KEY (`idDiseno`),
  KEY `disenos_idempleado_foreign` (`idEmpleado`),
  KEY `disenos_iddetalleventa_foreign` (`iddetalleVenta`),
  CONSTRAINT `disenos_iddetalleventa_foreign` FOREIGN KEY (`iddetalleVenta`) REFERENCES `detalle_ventas` (`iddetalleVenta`) ON DELETE NO ACTION ON UPDATE NO ACTION,
  CONSTRAINT `disenos_idempleado_foreign` FOREIGN KEY (`idEmpleado`) REFERENCES `empleados` (`idEmpleado`) ON DELETE NO ACTION ON UPDATE NO ACTION
) ENGINE=InnoDB AUTO_INCREMENT=3 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

#
# Data for table "disenos"
#

INSERT INTO `disenos` VALUES (1,'disenos/1759008104_polera-argentina-1.jpg','Polera Argentina 1',1,'2025-09-27 20:11:23','2025-09-27 21:21:44','en proceso',NULL,1),(2,'disenos/1759008776_polera-argentina.jpg','Polera Argentina',1,'2025-09-27 21:32:56','2025-10-02 21:06:41','en proceso',NULL,NULL),(3,'disenos_personalizados/4y6ERivQZ1HI8XmWjcTL0N1MD9a9YGMXbM30Wutn.jpg',NULL,1,'2025-10-12 23:19:03','2025-10-12 23:19:03','en proceso',NULL,NULL),(4,'disenos_personalizados/4y6ERivQZ1HI8XmWjcTL0N1MD9a9YGMXbM30Wutn.jpg',NULL,1,'2025-10-12 23:19:03','2025-10-12 23:19:03','en proceso',17,NULL);

#
# Structure for table "productos"
#

DROP TABLE IF EXISTS `productos`;
CREATE TABLE `productos` (
  `idProducto` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `SKU` varchar(45) NOT NULL DEFAULT '',
  `nombre` varchar(60) NOT NULL,
  `descripcion` varchar(250) DEFAULT NULL,
  `foto` varchar(250) DEFAULT NULL,
  `cantidad` int(11) DEFAULT NULL,
  `precioVenta` decimal(5,2) NOT NULL,
  `precioProduccion` decimal(5,2) DEFAULT NULL,
  `pedidoMinimo` tinyint(4) NOT NULL,
  `stock` varchar(45) DEFAULT NULL,
  `estado` tinyint(4) NOT NULL DEFAULT 1,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `idCategoria` tinyint(3) unsigned NOT NULL,
  `idDiseno` int(10) unsigned DEFAULT NULL,
  `idVariante` tinyint(3) unsigned DEFAULT NULL,
  PRIMARY KEY (`idProducto`),
  KEY `productos_idcategoria_foreign` (`idCategoria`),
  KEY `productos_iddiseno_foreign` (`idDiseno`),
  KEY `productos_idvariante_foreign` (`idVariante`),
  CONSTRAINT `productos_idcategoria_foreign` FOREIGN KEY (`idCategoria`) REFERENCES `categorias` (`idCategoria`) ON DELETE CASCADE,
  CONSTRAINT `productos_iddiseno_foreign` FOREIGN KEY (`idDiseno`) REFERENCES `disenos` (`idDiseno`) ON DELETE NO ACTION ON UPDATE NO ACTION
) ENGINE=InnoDB AUTO_INCREMENT=22 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

#
# Data for table "productos"
#

INSERT INTO `productos` VALUES (1,'POLF100','Polera',NULL,NULL,NULL,70.00,NULL,1,NULL,1,NULL,NULL,1,NULL,1),(2,'Corto','Corto',NULL,NULL,NULL,60.00,NULL,1,NULL,1,NULL,NULL,1,NULL,2),(3,'Chamarra','Chamarra',NULL,NULL,NULL,120.00,NULL,1,NULL,1,NULL,NULL,3,NULL,3),(4,'Buzo','Buzo',NULL,NULL,NULL,60.00,NULL,1,NULL,1,NULL,NULL,3,NULL,4),(21,'POLF101','Polera lila leoncito',NULL,'productos/1758922069_15.jpg',0,70.00,NULL,1,NULL,1,'2025-09-26 21:27:49','2025-10-11 21:39:01',1,NULL,NULL);

#
# Structure for table "producto_tallas"
#

DROP TABLE IF EXISTS `producto_tallas`;
CREATE TABLE `producto_tallas` (
  `idTallas` smallint(5) unsigned NOT NULL,
  `precioAdicional` varchar(45) DEFAULT NULL,
  `stock` varchar(45) DEFAULT NULL,
  `idProducto` int(10) unsigned NOT NULL,
  PRIMARY KEY (`idTallas`),
  KEY `producto_tallas_idproducto_foreign` (`idProducto`),
  CONSTRAINT `producto_tallas_idproducto_foreign` FOREIGN KEY (`idProducto`) REFERENCES `productos` (`idProducto`) ON DELETE NO ACTION ON UPDATE NO ACTION,
  CONSTRAINT `producto_tallas_idtalla_foreign` FOREIGN KEY (`idTallas`) REFERENCES `tallas` (`idTallas`) ON DELETE NO ACTION ON UPDATE NO ACTION
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

#
# Data for table "producto_tallas"
#


#
# Structure for table "producto_opcions"
#

DROP TABLE IF EXISTS `producto_opcions`;
CREATE TABLE `producto_opcions` (
  `idProductoOpcion` tinyint(3) unsigned NOT NULL AUTO_INCREMENT,
  `nombre` varchar(50) NOT NULL,
  `descripcion` varchar(45) NOT NULL,
  `estado` tinyint(4) NOT NULL DEFAULT 1,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `idProducto` int(10) unsigned NOT NULL,
  `idOpcion` tinyint(3) unsigned NOT NULL,
  PRIMARY KEY (`idProductoOpcion`),
  KEY `producto_opcions_idproducto_foreign` (`idProducto`),
  KEY `producto_opcions_idopcion_foreign` (`idOpcion`),
  CONSTRAINT `producto_opcions_idopcion_foreign` FOREIGN KEY (`idOpcion`) REFERENCES `opcions` (`idOpcion`) ON DELETE CASCADE,
  CONSTRAINT `producto_opcions_idproducto_foreign` FOREIGN KEY (`idProducto`) REFERENCES `productos` (`idProducto`) ON DELETE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=25 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

#
# Data for table "producto_opcions"
#

INSERT INTO `producto_opcions` VALUES (1,'cuello','',1,'2025-09-29 17:14:06','2025-09-29 17:14:06',1,1),(2,'manga','',1,'2025-09-29 17:14:06','2025-09-29 17:14:06',1,2),(4,'tela','Material drifit transpirable',1,'2025-09-29 17:14:06','2025-09-29 17:14:06',1,8),(7,'tipo de sublimado','',1,'2025-09-29 17:14:06','2025-09-29 17:14:06',2,3),(9,'tela','Tela win deportiva',1,'2025-09-29 17:14:06','2025-09-29 17:14:06',2,8),(12,'manga','',1,'2025-09-29 17:14:06','2025-09-29 17:14:06',3,2),(13,'capucha','',1,'2025-09-29 17:14:06','2025-09-29 17:14:06',3,4),(14,'pretina','',1,'2025-09-29 17:14:06','2025-09-29 17:14:06',3,5),(15,'puño','',1,'2025-09-29 17:14:06','2025-09-29 17:14:06',3,6),(22,'logotipo','',1,'2025-09-29 17:14:06','2025-09-29 17:14:06',4,7),(23,'tela','Algodón suave',1,'2025-09-29 17:14:06','2025-09-29 17:14:06',4,8);

#
# Structure for table "transaccions"
#

DROP TABLE IF EXISTS `transaccions`;
CREATE TABLE `transaccions` (
  `idTransaccion` tinyint(3) unsigned NOT NULL AUTO_INCREMENT,
  `tipoTransaccion` varchar(20) NOT NULL,
  `monto` decimal(8,2) NOT NULL,
  `metodoPago` varchar(20) NOT NULL,
  `observaciones` varchar(255) DEFAULT NULL,
  `estado` tinyint(4) NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `idVenta` int(10) unsigned NOT NULL,
  `idUser` int(10) unsigned DEFAULT NULL,
  PRIMARY KEY (`idTransaccion`),
  KEY `transaccions_idventa_foreign` (`idVenta`),
  CONSTRAINT `transaccions_idventa_foreign` FOREIGN KEY (`idVenta`) REFERENCES `ventas` (`idVenta`) ON DELETE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=21 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

#
# Data for table "transaccions"
#

INSERT INTO `transaccions` VALUES (19,'pago',500.00,'efectivo',NULL,1,'2025-10-10 17:37:47','2025-10-10 17:37:47',13,NULL),(20,'pago',700.00,'efectivo',NULL,1,'2025-10-12 23:05:41','2025-10-12 23:05:41',14,NULL);
