create database electro;
use electro;
CREATE TABLE `productos` (
  `ID_Producto` int NOT NULL AUTO_INCREMENT,
  `Nombre` varchar(255) NOT NULL,
  `Descripcion` text,
  `Precio` decimal(10,2) NOT NULL,
  `Activo` tinyint(1) DEFAULT '1',
  PRIMARY KEY (`ID_Producto`)
);
CREATE TABLE `usuarios` (
  `ID_Usuario` int NOT NULL AUTO_INCREMENT,
  `CorreoElectronico` varchar(255) NOT NULL,
  `Contrasena` varchar(255) NOT NULL,
  `Rol` varchar(50) NOT NULL,
  `Activo` tinyint(1) DEFAULT '1',
  PRIMARY KEY (`ID_Usuario`),
  UNIQUE KEY `CorreoElectronico` (`CorreoElectronico`)
);
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
);

CREATE TABLE `facturas` (
  `ID_Factura` int NOT NULL AUTO_INCREMENT,
  `NumeroSAP` varchar(255) NOT NULL,
  `Nombre_Completo` varchar(100) DEFAULT NULL,
  `Fecha` date NOT NULL,
  `ID_Usuario` int DEFAULT NULL,
  `Activo` tinyint(1) DEFAULT '1',
  PRIMARY KEY (`ID_Factura`),
  UNIQUE KEY `NumeroSAP` (`NumeroSAP`),
  KEY `ID_Usuario` (`ID_Usuario`),
  CONSTRAINT `facturas_ibfk_1` FOREIGN KEY (`ID_Usuario`) REFERENCES `usuarios` (`ID_Usuario`)
);
CREATE TABLE `detallesfactura` (
  `ID_Detalle` int NOT NULL AUTO_INCREMENT,
  `ID_Factura` int DEFAULT NULL,
  `ID_Producto` int DEFAULT NULL,
  `Cantidad` int NOT NULL,
  `PrecioUnitario` decimal(10,2) NOT NULL,
  `Activo` tinyint(1) DEFAULT '1',
  PRIMARY KEY (`ID_Detalle`),
  KEY `ID_Factura` (`ID_Factura`),
  KEY `ID_Producto` (`ID_Producto`),
  CONSTRAINT `detallesfactura_ibfk_1` FOREIGN KEY (`ID_Factura`) REFERENCES `facturas` (`ID_Factura`),
  CONSTRAINT `detallesfactura_ibfk_2` FOREIGN KEY (`ID_Producto`) REFERENCES `productos` (`ID_Producto`)
);

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
);
DELIMITER $$
CREATE DEFINER=`root`@`localhost` PROCEDURE `sp_AddInvoiceDetail`(
    IN p_InvoiceId INT,
    IN p_ProductId INT,
    IN p_Quantity INT,
    IN p_UnitPrice DECIMAL(10,2)
)
BEGIN
    INSERT INTO DetallesFactura (
        ID_Factura,
        ID_Producto,
        Cantidad,
        PrecioUnitario,
        Activo
    ) VALUES (
        p_InvoiceId,
        p_ProductId,
        p_Quantity,
        p_UnitPrice,
        1
    );
END$$
DELIMITER ;
DELIMITER $$
CREATE DEFINER=`root`@`localhost` PROCEDURE `sp_CreateInvoice`(
    IN p_NumeroSAP VARCHAR(50),
    IN p_NombreCompleto VARCHAR(100),
    IN p_UserId INT
)
BEGIN
    DECLARE v_InvoiceId INT;
    
    START TRANSACTION;
    
    INSERT INTO Facturas (NumeroSAP, Nombre_Completo, Fecha, ID_Usuario, Activo)
    VALUES (p_NumeroSAP, p_NombreCompleto, CURRENT_DATE(), p_UserId, 1);
    
    SET v_InvoiceId = LAST_INSERT_ID();
    
    SELECT v_InvoiceId as ID_Factura;
    
    COMMIT;
END$$
DELIMITER ;
DELIMITER $$
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
END$$
DELIMITER ;
DELIMITER $$
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
END$$
DELIMITER ;
DELIMITER $$
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
END$$
DELIMITER ;

DELIMITER $$
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
END$$
DELIMITER ;
DELIMITER $$
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
END$$
DELIMITER ;
DELIMITER $$
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
END$$
DELIMITER ;
