CREATE DATABASE  IF NOT EXISTS `electro` /*!40100 DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci */ /*!80016 DEFAULT ENCRYPTION='N' */;
USE `electro`;
-- MySQL dump 10.13  Distrib 8.0.41, for Win64 (x86_64)
--
-- Host: localhost    Database: electro
-- ------------------------------------------------------
-- Server version	8.0.41

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
-- Table structure for table `detallesfactura`
--

DROP TABLE IF EXISTS `detallesfactura`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `detallesfactura` (
  `ID_Detalle` int NOT NULL AUTO_INCREMENT,
  `ID_Factura` int DEFAULT NULL,
  `ID_Producto` int DEFAULT NULL,
  `Descuento` decimal(10,2) DEFAULT '0.00',
  `Cantidad` int NOT NULL,
  `PrecioUnitario` decimal(10,2) NOT NULL,
  `Activo` tinyint(1) DEFAULT '1',
  PRIMARY KEY (`ID_Detalle`),
  KEY `ID_Factura` (`ID_Factura`),
  KEY `ID_Producto` (`ID_Producto`),
  CONSTRAINT `detallesfactura_ibfk_1` FOREIGN KEY (`ID_Factura`) REFERENCES `facturas` (`ID_Factura`),
  CONSTRAINT `detallesfactura_ibfk_2` FOREIGN KEY (`ID_Producto`) REFERENCES `productos` (`ID_Producto`)
) ENGINE=InnoDB AUTO_INCREMENT=21 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `detallesfactura`
--

LOCK TABLES `detallesfactura` WRITE;
/*!40000 ALTER TABLE `detallesfactura` DISABLE KEYS */;
INSERT INTO `detallesfactura` VALUES (1,1,1,0.00,1,450.50,1),(2,1,3,0.00,1,780.75,1),(3,2,2,0.00,1,620.00,1),(4,3,4,0.00,2,320.99,1),(5,3,5,0.00,1,150.00,1),(6,4,6,0.00,1,95.20,1),(7,4,7,0.00,2,75.80,1),(8,1,8,0.00,1,55.00,1),(9,5,11,50.00,1,150.00,1),(10,5,10,0.90,1,65.90,1),(11,12,11,0.00,2,150.00,1),(12,12,10,0.00,1,65.90,1),(13,13,9,0.00,3,40.30,1),(18,18,4,0.00,1,320.99,1),(19,19,11,0.00,1,150.00,1),(20,20,2,0.00,5,620.00,1);
/*!40000 ALTER TABLE `detallesfactura` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `empleados`
--

DROP TABLE IF EXISTS `empleados`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `empleados` (
  `ID_Empleado` int NOT NULL AUTO_INCREMENT,
  `ID_Usuario` int DEFAULT NULL,
  `Nombre` varchar(255) NOT NULL,
  `Apellido` varchar(255) NOT NULL,
  `Telefono` varchar(20) DEFAULT NULL,
  `Direccion` varchar(255) DEFAULT NULL,
  `Activo` tinyint(1) DEFAULT '1',
  PRIMARY KEY (`ID_Empleado`),
  KEY `ID_Usuario` (`ID_Usuario`),
  CONSTRAINT `empleados_ibfk_1` FOREIGN KEY (`ID_Usuario`) REFERENCES `usuarios` (`ID_Usuario`)
) ENGINE=InnoDB AUTO_INCREMENT=3 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `empleados`
--

LOCK TABLES `empleados` WRITE;
/*!40000 ALTER TABLE `empleados` DISABLE KEYS */;
INSERT INTO `empleados` VALUES (1,2,'Ana','Pérez','8765-4321','Barrio Central, Calle 5 #123',1),(2,4,'Carlos','López','2233-4455','Residencial Las Flores, Avenida Principal #45',1);
/*!40000 ALTER TABLE `empleados` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `facturas`
--

DROP TABLE IF EXISTS `facturas`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `facturas` (
  `ID_Factura` int NOT NULL AUTO_INCREMENT,
  `Numero_Factura` varchar(45) DEFAULT NULL,
  `Tipo_Pago` varchar(45) DEFAULT 'CREDITO',
  `Telefono` varchar(45) DEFAULT NULL,
  `NumeroSAP` varchar(255) NOT NULL,
  `Nombre_Completo` varchar(100) DEFAULT NULL,
  `Fecha` date NOT NULL,
  `ID_Usuario` int DEFAULT NULL,
  `Activo` tinyint(1) DEFAULT '1',
  PRIMARY KEY (`ID_Factura`),
  KEY `ID_Usuario` (`ID_Usuario`),
  CONSTRAINT `facturas_ibfk_1` FOREIGN KEY (`ID_Usuario`) REFERENCES `usuarios` (`ID_Usuario`)
) ENGINE=InnoDB AUTO_INCREMENT=21 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `facturas`
--

LOCK TABLES `facturas` WRITE;
/*!40000 ALTER TABLE `facturas` DISABLE KEYS */;
INSERT INTO `facturas` VALUES (1,NULL,'CREDITO',NULL,'SAP-001','Juan Rodríguez','2025-04-01',3,1),(2,NULL,'CREDITO',NULL,'SAP-002','María Gómez','2025-04-03',5,1),(3,NULL,'CREDITO',NULL,'SAP-003','Pedro Fernández','2025-04-05',3,1),(4,NULL,'CREDITO',NULL,'SAP-004','Luisa Vargas','2025-04-08',5,1),(5,'12345','CREDITO','8978987','2345543','asdfasdfafd','2025-04-10',NULL,1),(12,'12345','CREDITO','8978987','2345543','asdfasdfafd','2025-04-10',NULL,1),(13,'12349','CREDITO','8978987','23455432','asdfasdfafdada','2025-04-10',NULL,1),(18,'1234534555','CREDITO','23454666','23455432','asdfasdfafdadaDsdasdf','2025-04-10',1,1),(19,'8925','CREDITO','84755181','10106437','evert','2025-04-10',1,1),(20,'8926','CREDITO','84755181','10106437','evert','2025-04-10',1,1);
/*!40000 ALTER TABLE `facturas` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `inventario`
--

DROP TABLE IF EXISTS `inventario`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `inventario` (
  `ID_Movimiento` int NOT NULL AUTO_INCREMENT,
  `ID_Producto` int DEFAULT NULL,
  `Fecha` date NOT NULL,
  `TipoMovimiento` varchar(20) NOT NULL,
  `Cantidad` int NOT NULL,
  `ID_Usuario` int DEFAULT NULL,
  `ID_Factura` int DEFAULT NULL,
  `Activo` tinyint(1) DEFAULT '1',
  PRIMARY KEY (`ID_Movimiento`),
  KEY `ID_Producto` (`ID_Producto`),
  KEY `ID_Usuario` (`ID_Usuario`),
  KEY `ID_Factura` (`ID_Factura`),
  CONSTRAINT `inventario_ibfk_1` FOREIGN KEY (`ID_Producto`) REFERENCES `productos` (`ID_Producto`),
  CONSTRAINT `inventario_ibfk_2` FOREIGN KEY (`ID_Usuario`) REFERENCES `usuarios` (`ID_Usuario`),
  CONSTRAINT `inventario_ibfk_3` FOREIGN KEY (`ID_Factura`) REFERENCES `facturas` (`ID_Factura`)
) ENGINE=InnoDB AUTO_INCREMENT=36 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `inventario`
--

LOCK TABLES `inventario` WRITE;
/*!40000 ALTER TABLE `inventario` DISABLE KEYS */;
INSERT INTO `inventario` VALUES (1,1,'2025-03-15','Entrada',15,1,NULL,1),(2,2,'2025-03-15','Entrada',10,1,NULL,1),(3,3,'2025-03-16','Entrada',12,1,NULL,1),(4,4,'2025-03-17','Entrada',20,1,NULL,1),(5,5,'2025-03-18','Entrada',25,1,NULL,1),(6,6,'2025-03-19','Entrada',30,1,NULL,1),(7,7,'2025-03-20','Entrada',18,1,NULL,1),(8,8,'2025-03-21','Entrada',22,1,NULL,1),(9,9,'2025-03-22','Entrada',40,1,NULL,1),(10,10,'2025-03-23','Entrada',35,1,NULL,1),(11,1,'2025-04-01','Salida',1,2,1,1),(12,3,'2025-04-01','Salida',1,2,1,1),(13,2,'2025-04-03','Salida',1,4,2,1),(14,4,'2025-04-05','Salida',2,2,3,1),(15,5,'2025-04-05','Salida',1,2,3,1),(16,6,'2025-04-08','Salida',1,4,4,1),(17,7,'2025-04-08','Salida',2,4,4,1),(18,8,'2025-04-01','Salida',1,2,1,1),(19,11,'2025-04-10','Entrada',10,1,NULL,1),(20,10,'2025-04-10','Entrada',30,1,NULL,1),(21,11,'2025-04-10','Salida',60,1,NULL,1),(22,11,'2025-04-10','Entrada',50,1,NULL,1),(23,11,'2025-04-10','Entrada',20,1,NULL,1),(24,11,'2025-04-10','Salida',5,1,NULL,1),(25,10,'2025-04-10','Salida',60,1,NULL,1),(26,10,'2025-04-10','Entrada',60,1,NULL,1),(27,2,'2025-04-10','Entrada',1,1,NULL,1),(28,11,'2025-04-10','Salida',1,1,5,1),(29,10,'2025-04-10','Salida',1,1,5,1),(30,11,'2025-04-10','Salida',2,1,12,1),(31,10,'2025-04-10','Salida',1,1,12,1),(32,9,'2025-04-10','Venta',3,1,13,1),(33,4,'2025-04-10','Venta',1,1,18,1),(34,11,'2025-04-10','Venta',1,1,19,1),(35,2,'2025-04-10','Venta',5,1,20,1);
/*!40000 ALTER TABLE `inventario` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `productos`
--

DROP TABLE IF EXISTS `productos`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `productos` (
  `ID_Producto` int NOT NULL AUTO_INCREMENT,
  `Nombre` varchar(255) NOT NULL,
  `Descripcion` text,
  `Precio` decimal(10,2) NOT NULL,
  `Activo` tinyint(1) DEFAULT '1',
  PRIMARY KEY (`ID_Producto`)
) ENGINE=InnoDB AUTO_INCREMENT=12 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `productos`
--

LOCK TABLES `productos` WRITE;
/*!40000 ALTER TABLE `productos` DISABLE KEYS */;
INSERT INTO `productos` VALUES (1,'Lavadora Automática 10kg','Lavadora de carga frontal, 10kg de capacidad, eficiencia energética A+++',450.50,1),(2,'Refrigerador No Frost 300L','Refrigerador de dos puertas, tecnología No Frost, 300 litros de capacidad',620.00,1),(3,'Televisor LED 55\" Smart TV','Televisor LED de 55 pulgadas, resolución 4K UHD, Smart TV con Wi-Fi',780.75,1),(4,'Horno Eléctrico Multifunción','Horno eléctrico con múltiples funciones de cocción, 70 litros de capacidad',320.99,1),(5,'Aspiradora sin Bolsa','Aspiradora ciclónica sin bolsa, alta potencia de succión',150.00,1),(6,'Microondas 20L','Microondas con capacidad de 20 litros, control digital',95.20,1),(7,'Licuadora de Vaso de Vidrio','Licuadora con vaso de vidrio resistente, múltiples velocidades',75.80,1),(8,'Cafetera Eléctrica Programable','Cafetera eléctrica programable, capacidad para 12 tazas',55.00,1),(9,'Plancha de Vapor','Plancha de vapor con suela de cerámica, función anti-goteo',40.30,1),(10,'Ventilador de Torre Sankey','Ventilador de torre con control remoto, 3 velocidades',65.90,1),(11,'Equipo de Sonido LG 5500W','Descripcion X',150.00,1);
/*!40000 ALTER TABLE `productos` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `usuarios`
--

DROP TABLE IF EXISTS `usuarios`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `usuarios` (
  `ID_Usuario` int NOT NULL AUTO_INCREMENT,
  `CorreoElectronico` varchar(255) NOT NULL,
  `Contrasena` varchar(255) NOT NULL,
  `Rol` varchar(50) NOT NULL,
  `Activo` tinyint(1) DEFAULT '1',
  PRIMARY KEY (`ID_Usuario`),
  UNIQUE KEY `CorreoElectronico` (`CorreoElectronico`)
) ENGINE=InnoDB AUTO_INCREMENT=7 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `usuarios`
--

LOCK TABLES `usuarios` WRITE;
/*!40000 ALTER TABLE `usuarios` DISABLE KEYS */;
INSERT INTO `usuarios` VALUES (1,'electro.sqladmin@electrohogar.com','$2y$10$W.krXqN105oyvSUaEB8RJO7asZCuwinsJh0q2SqPT77OuL0HVpaA2','SuperAdmin',1),(2,'admin@electro.com','admin123','Administrador',1),(3,'empleado1@electro.com','empleado456','Vendedor',1),(4,'cliente1@email.com','cliente789','Vendedor',1),(5,'empleado2@electro.com','trabajador10','Vendedor',1),(6,'cliente2@email.com','usuario22','Vendedor',1);
/*!40000 ALTER TABLE `usuarios` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Dumping events for database 'electro'
--

--
-- Dumping routines for database 'electro'
--
/*!50003 DROP PROCEDURE IF EXISTS `sp_AddInvoiceDetail` */;
/*!50003 SET @saved_cs_client      = @@character_set_client */ ;
/*!50003 SET @saved_cs_results     = @@character_set_results */ ;
/*!50003 SET @saved_col_connection = @@collation_connection */ ;
/*!50003 SET character_set_client  = utf8mb4 */ ;
/*!50003 SET character_set_results = utf8mb4 */ ;
/*!50003 SET collation_connection  = utf8mb4_0900_ai_ci */ ;
/*!50003 SET @saved_sql_mode       = @@sql_mode */ ;
/*!50003 SET sql_mode              = 'ONLY_FULL_GROUP_BY,STRICT_TRANS_TABLES,NO_ZERO_IN_DATE,NO_ZERO_DATE,ERROR_FOR_DIVISION_BY_ZERO,NO_ENGINE_SUBSTITUTION' */ ;
DELIMITER ;;
CREATE DEFINER=`root`@`localhost` PROCEDURE `sp_AddInvoiceDetail`(
    IN p_InvoiceId INT,
    IN p_ProductId INT,
    IN p_Descuento DECIMAL(10,2),
    IN p_Quantity INT,
    IN p_UnitPrice DECIMAL(10,2)
)
BEGIN
    INSERT INTO DetallesFactura (
        ID_Factura,
        ID_Producto,
        Descuento,
        Cantidad,
        PrecioUnitario,
        Activo
    ) VALUES (
        p_InvoiceId,
        p_ProductId,
        p_Descuento,
        p_Quantity,
        p_UnitPrice,
        1
    );
END ;;
DELIMITER ;
/*!50003 SET sql_mode              = @saved_sql_mode */ ;
/*!50003 SET character_set_client  = @saved_cs_client */ ;
/*!50003 SET character_set_results = @saved_cs_results */ ;
/*!50003 SET collation_connection  = @saved_col_connection */ ;
/*!50003 DROP PROCEDURE IF EXISTS `sp_CreateInvoice` */;
/*!50003 SET @saved_cs_client      = @@character_set_client */ ;
/*!50003 SET @saved_cs_results     = @@character_set_results */ ;
/*!50003 SET @saved_col_connection = @@collation_connection */ ;
/*!50003 SET character_set_client  = utf8mb4 */ ;
/*!50003 SET character_set_results = utf8mb4 */ ;
/*!50003 SET collation_connection  = utf8mb4_0900_ai_ci */ ;
/*!50003 SET @saved_sql_mode       = @@sql_mode */ ;
/*!50003 SET sql_mode              = 'ONLY_FULL_GROUP_BY,STRICT_TRANS_TABLES,NO_ZERO_IN_DATE,NO_ZERO_DATE,ERROR_FOR_DIVISION_BY_ZERO,NO_ENGINE_SUBSTITUTION' */ ;
DELIMITER ;;
CREATE DEFINER=`root`@`localhost` PROCEDURE `sp_CreateInvoice`(
    IN p_Numero_Factura varchar(45),
    IN p_TipoPago varchar(50),
    IN p_Telefono varchar(45),
    IN p_NumeroSAP VARCHAR(50),
    IN p_NombreCompleto VARCHAR(100),
    IN p_UserId INT
)
BEGIN
    DECLARE v_InvoiceId INT;
    
    INSERT INTO Facturas (
        Numero_Factura,
        Tipo_Pago,
        Telefono,
        NumeroSAP, 
        Nombre_Completo, 
        Fecha, 
        ID_Usuario, 
        Activo
    ) VALUES (
        p_Numero_Factura,
        p_TipoPago,
        p_Telefono,
        p_NumeroSAP, 
        p_NombreCompleto, 
        CURRENT_DATE(), 
        p_UserId, 
        1
    );
    
    SET v_InvoiceId = LAST_INSERT_ID();
    
    SELECT v_InvoiceId as ID_Factura;
END ;;
DELIMITER ;
/*!50003 SET sql_mode              = @saved_sql_mode */ ;
/*!50003 SET character_set_client  = @saved_cs_client */ ;
/*!50003 SET character_set_results = @saved_cs_results */ ;
/*!50003 SET collation_connection  = @saved_col_connection */ ;
/*!50003 DROP PROCEDURE IF EXISTS `sp_GetProductStock` */;
/*!50003 SET @saved_cs_client      = @@character_set_client */ ;
/*!50003 SET @saved_cs_results     = @@character_set_results */ ;
/*!50003 SET @saved_col_connection = @@collation_connection */ ;
/*!50003 SET character_set_client  = utf8mb4 */ ;
/*!50003 SET character_set_results = utf8mb4 */ ;
/*!50003 SET collation_connection  = utf8mb4_0900_ai_ci */ ;
/*!50003 SET @saved_sql_mode       = @@sql_mode */ ;
/*!50003 SET sql_mode              = 'ONLY_FULL_GROUP_BY,STRICT_TRANS_TABLES,NO_ZERO_IN_DATE,NO_ZERO_DATE,ERROR_FOR_DIVISION_BY_ZERO,NO_ENGINE_SUBSTITUTION' */ ;
DELIMITER ;;
CREATE DEFINER=`root`@`localhost` PROCEDURE `sp_GetProductStock`(IN productId INT)
BEGIN
    SELECT COALESCE(SUM(
        CASE 
            WHEN TipoMovimiento = 'Entrada' THEN Cantidad
            ELSE -Cantidad
        END
    ), 0) as stock
    FROM Inventario
    WHERE ID_Producto = productId AND Activo = 1;
END ;;
DELIMITER ;
/*!50003 SET sql_mode              = @saved_sql_mode */ ;
/*!50003 SET character_set_client  = @saved_cs_client */ ;
/*!50003 SET character_set_results = @saved_cs_results */ ;
/*!50003 SET collation_connection  = @saved_col_connection */ ;
/*!50003 DROP PROCEDURE IF EXISTS `sp_GetSalesReport` */;
/*!50003 SET @saved_cs_client      = @@character_set_client */ ;
/*!50003 SET @saved_cs_results     = @@character_set_results */ ;
/*!50003 SET @saved_col_connection = @@collation_connection */ ;
/*!50003 SET character_set_client  = utf8mb4 */ ;
/*!50003 SET character_set_results = utf8mb4 */ ;
/*!50003 SET collation_connection  = utf8mb4_0900_ai_ci */ ;
/*!50003 SET @saved_sql_mode       = @@sql_mode */ ;
/*!50003 SET sql_mode              = 'ONLY_FULL_GROUP_BY,STRICT_TRANS_TABLES,NO_ZERO_IN_DATE,NO_ZERO_DATE,ERROR_FOR_DIVISION_BY_ZERO,NO_ENGINE_SUBSTITUTION' */ ;
DELIMITER ;;
CREATE DEFINER=`root`@`localhost` PROCEDURE `sp_GetSalesReport`(
    IN p_StartDate DATE,
    IN p_EndDate DATE
)
BEGIN
    SELECT 
        f.Fecha,
        f.NumeroSAP,
        f.Nombre_Completo as Cliente,
        p.Nombre as Producto,
        df.Cantidad,
        df.PrecioUnitario,
        (df.Cantidad * df.PrecioUnitario) as Total
    FROM Facturas f
    JOIN DetallesFactura df ON f.ID_Factura = df.ID_Factura
    JOIN Productos p ON df.ID_Producto = p.ID_Producto
    WHERE f.Fecha BETWEEN p_StartDate AND p_EndDate
    AND f.Activo = 1
    ORDER BY f.Fecha DESC;
END ;;
DELIMITER ;
/*!50003 SET sql_mode              = @saved_sql_mode */ ;
/*!50003 SET character_set_client  = @saved_cs_client */ ;
/*!50003 SET character_set_results = @saved_cs_results */ ;
/*!50003 SET collation_connection  = @saved_col_connection */ ;
/*!50003 DROP PROCEDURE IF EXISTS `sp_GetStockMovements` */;
/*!50003 SET @saved_cs_client      = @@character_set_client */ ;
/*!50003 SET @saved_cs_results     = @@character_set_results */ ;
/*!50003 SET @saved_col_connection = @@collation_connection */ ;
/*!50003 SET character_set_client  = utf8mb4 */ ;
/*!50003 SET character_set_results = utf8mb4 */ ;
/*!50003 SET collation_connection  = utf8mb4_0900_ai_ci */ ;
/*!50003 SET @saved_sql_mode       = @@sql_mode */ ;
/*!50003 SET sql_mode              = 'ONLY_FULL_GROUP_BY,STRICT_TRANS_TABLES,NO_ZERO_IN_DATE,NO_ZERO_DATE,ERROR_FOR_DIVISION_BY_ZERO,NO_ENGINE_SUBSTITUTION' */ ;
DELIMITER ;;
CREATE DEFINER=`root`@`localhost` PROCEDURE `sp_GetStockMovements`(
    IN p_StartDate DATE,
    IN p_EndDate DATE
)
BEGIN
    SELECT 
        i.Fecha,
        p.Nombre as Producto,
        i.TipoMovimiento,
        i.Cantidad,
        u.CorreoElectronico as Usuario,
        COALESCE(f.NumeroSAP, 'N/A') as NumeroFactura
    FROM Inventario i
    JOIN Productos p ON i.ID_Producto = p.ID_Producto
    JOIN Usuarios u ON i.ID_Usuario = u.ID_Usuario
    LEFT JOIN Facturas f ON i.ID_Factura = f.ID_Factura
    WHERE i.Fecha BETWEEN p_StartDate AND p_EndDate
    AND i.Activo = 1
    ORDER BY i.Fecha DESC;
END ;;
DELIMITER ;
/*!50003 SET sql_mode              = @saved_sql_mode */ ;
/*!50003 SET character_set_client  = @saved_cs_client */ ;
/*!50003 SET character_set_results = @saved_cs_results */ ;
/*!50003 SET collation_connection  = @saved_col_connection */ ;
/*!50003 DROP PROCEDURE IF EXISTS `sp_GetTopSellingProducts` */;
/*!50003 SET @saved_cs_client      = @@character_set_client */ ;
/*!50003 SET @saved_cs_results     = @@character_set_results */ ;
/*!50003 SET @saved_col_connection = @@collation_connection */ ;
/*!50003 SET character_set_client  = utf8mb4 */ ;
/*!50003 SET character_set_results = utf8mb4 */ ;
/*!50003 SET collation_connection  = utf8mb4_0900_ai_ci */ ;
/*!50003 SET @saved_sql_mode       = @@sql_mode */ ;
/*!50003 SET sql_mode              = 'ONLY_FULL_GROUP_BY,STRICT_TRANS_TABLES,NO_ZERO_IN_DATE,NO_ZERO_DATE,ERROR_FOR_DIVISION_BY_ZERO,NO_ENGINE_SUBSTITUTION' */ ;
DELIMITER ;;
CREATE DEFINER=`root`@`localhost` PROCEDURE `sp_GetTopSellingProducts`(
    IN p_StartDate DATE,
    IN p_EndDate DATE,
    IN p_Limit INT
)
BEGIN
    SELECT 
        p.Nombre,
        SUM(df.Cantidad) as TotalVendido,
        SUM(df.Cantidad * df.PrecioUnitario) as TotalIngresos
    FROM DetallesFactura df
    JOIN Productos p ON df.ID_Producto = p.ID_Producto
    JOIN Facturas f ON df.ID_Factura = f.ID_Factura
    WHERE f.Fecha BETWEEN p_StartDate AND p_EndDate
    AND f.Activo = 1
    GROUP BY p.ID_Producto
    ORDER BY TotalVendido DESC
    LIMIT p_Limit;
END ;;
DELIMITER ;
/*!50003 SET sql_mode              = @saved_sql_mode */ ;
/*!50003 SET character_set_client  = @saved_cs_client */ ;
/*!50003 SET character_set_results = @saved_cs_results */ ;
/*!50003 SET collation_connection  = @saved_col_connection */ ;
/*!50003 DROP PROCEDURE IF EXISTS `sp_RegisterInventoryMovement` */;
/*!50003 SET @saved_cs_client      = @@character_set_client */ ;
/*!50003 SET @saved_cs_results     = @@character_set_results */ ;
/*!50003 SET @saved_col_connection = @@collation_connection */ ;
/*!50003 SET character_set_client  = utf8mb4 */ ;
/*!50003 SET character_set_results = utf8mb4 */ ;
/*!50003 SET collation_connection  = utf8mb4_0900_ai_ci */ ;
/*!50003 SET @saved_sql_mode       = @@sql_mode */ ;
/*!50003 SET sql_mode              = 'ONLY_FULL_GROUP_BY,STRICT_TRANS_TABLES,NO_ZERO_IN_DATE,NO_ZERO_DATE,ERROR_FOR_DIVISION_BY_ZERO,NO_ENGINE_SUBSTITUTION' */ ;
DELIMITER ;;
CREATE DEFINER=`root`@`localhost` PROCEDURE `sp_RegisterInventoryMovement`(
    IN p_ProductId INT,
    IN p_MovementType VARCHAR(20),
    IN p_Quantity INT,
    IN p_UserId INT,
    IN p_InvoiceId INT
)
BEGIN
    INSERT INTO Inventario (
        ID_Producto,
        Fecha,
        TipoMovimiento,
        Cantidad,
        ID_Usuario,
        ID_Factura,
        Activo
    ) VALUES (
        p_ProductId,
        CURRENT_DATE(),
        p_MovementType,
        p_Quantity,
        p_UserId,
        p_InvoiceId,
        1
    );
END ;;
DELIMITER ;
/*!50003 SET sql_mode              = @saved_sql_mode */ ;
/*!50003 SET character_set_client  = @saved_cs_client */ ;
/*!50003 SET character_set_results = @saved_cs_results */ ;
/*!50003 SET collation_connection  = @saved_col_connection */ ;
/*!50003 DROP PROCEDURE IF EXISTS `sp_UpdateProductStock` */;
/*!50003 SET @saved_cs_client      = @@character_set_client */ ;
/*!50003 SET @saved_cs_results     = @@character_set_results */ ;
/*!50003 SET @saved_col_connection = @@collation_connection */ ;
/*!50003 SET character_set_client  = utf8mb4 */ ;
/*!50003 SET character_set_results = utf8mb4 */ ;
/*!50003 SET collation_connection  = utf8mb4_0900_ai_ci */ ;
/*!50003 SET @saved_sql_mode       = @@sql_mode */ ;
/*!50003 SET sql_mode              = 'ONLY_FULL_GROUP_BY,STRICT_TRANS_TABLES,NO_ZERO_IN_DATE,NO_ZERO_DATE,ERROR_FOR_DIVISION_BY_ZERO,NO_ENGINE_SUBSTITUTION' */ ;
DELIMITER ;;
CREATE DEFINER=`root`@`localhost` PROCEDURE `sp_UpdateProductStock`(
    IN p_ProductId INT,
    IN p_Quantity INT,
    IN p_MovementType VARCHAR(20),
    IN p_UserId INT
)
BEGIN
    INSERT INTO Inventario (
        ID_Producto,
        Fecha,
        TipoMovimiento,
        Cantidad,
        ID_Usuario,
        Activo
    ) VALUES (
        p_ProductId,
        CURRENT_DATE(),
        p_MovementType,
        p_Quantity,
        p_UserId,
        1
    );
END ;;
DELIMITER ;
/*!50003 SET sql_mode              = @saved_sql_mode */ ;
/*!50003 SET character_set_client  = @saved_cs_client */ ;
/*!50003 SET character_set_results = @saved_cs_results */ ;
/*!50003 SET collation_connection  = @saved_col_connection */ ;
/*!40103 SET TIME_ZONE=@OLD_TIME_ZONE */;

/*!40101 SET SQL_MODE=@OLD_SQL_MODE */;
/*!40014 SET FOREIGN_KEY_CHECKS=@OLD_FOREIGN_KEY_CHECKS */;
/*!40014 SET UNIQUE_CHECKS=@OLD_UNIQUE_CHECKS */;
/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
/*!40111 SET SQL_NOTES=@OLD_SQL_NOTES */;

-- Dump completed on 2025-04-10 15:56:18
