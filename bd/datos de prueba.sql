-- Inserting data into the 'productos' table
INSERT INTO productos (Nombre, Descripcion, Precio) VALUES
('Lavadora Automática 10kg', 'Lavadora de carga frontal, 10kg de capacidad, eficiencia energética A+++', 450.50),
('Refrigerador No Frost 300L', 'Refrigerador de dos puertas, tecnología No Frost, 300 litros de capacidad', 620.00),
('Televisor LED 55" Smart TV', 'Televisor LED de 55 pulgadas, resolución 4K UHD, Smart TV con Wi-Fi', 780.75),
('Horno Eléctrico Multifunción', 'Horno eléctrico con múltiples funciones de cocción, 70 litros de capacidad', 320.99),
('Aspiradora sin Bolsa', 'Aspiradora ciclónica sin bolsa, alta potencia de succión', 150.00),
('Microondas 20L', 'Microondas con capacidad de 20 litros, control digital', 95.20),
('Licuadora de Vaso de Vidrio', 'Licuadora con vaso de vidrio resistente, múltiples velocidades', 75.80),
('Cafetera Eléctrica Programable', 'Cafetera eléctrica programable, capacidad para 12 tazas', 55.00),
('Plancha de Vapor', 'Plancha de vapor con suela de cerámica, función anti-goteo', 40.30),
('Ventilador de Torre', 'Ventilador de torre con control remoto, 3 velocidades', 65.90);

-- Inserting data into the 'usuarios' table
INSERT INTO usuarios (CorreoElectronico, Contrasena, Rol) VALUES
('admin@electro.com', 'admin123', 'administrador'),
('empleado1@electro.com', 'empleado456', 'empleado'),
('cliente1@email.com', 'cliente789', 'cliente'),
('empleado2@electro.com', 'trabajador10', 'empleado'),
('cliente2@email.com', 'usuario22', 'cliente');

-- Inserting data into the 'empleados' table
INSERT INTO empleados (ID_Usuario, Nombre, Apellido, Telefono, Direccion) VALUES
(2, 'Ana', 'Pérez', '8765-4321', 'Barrio Central, Calle 5 #123'),
(4, 'Carlos', 'López', '2233-4455', 'Residencial Las Flores, Avenida Principal #45');

-- Inserting data into the 'facturas' table
INSERT INTO facturas (NumeroSAP, Nombre_Completo, Fecha, ID_Usuario) VALUES
('SAP-001', 'Juan Rodríguez', '2025-04-01', 3),
('SAP-002', 'María Gómez', '2025-04-03', 5),
('SAP-003', 'Pedro Fernández', '2025-04-05', 3),
('SAP-004', 'Luisa Vargas', '2025-04-08', 5);

-- Inserting data into the 'detallesfactura' table
INSERT INTO detallesfactura (ID_Factura, ID_Producto, Cantidad, PrecioUnitario) VALUES
(1, 1, 1, 450.50),
(1, 3, 1, 780.75),
(2, 2, 1, 620.00),
(3, 4, 2, 320.99),
(3, 5, 1, 150.00),
(4, 6, 1, 95.20),
(4, 7, 2, 75.80),
(1, 8, 1, 55.00);

-- Inserting data into the 'inventario' table (simulating initial stock and some sales)
-- Entradas (Initial Stock)
INSERT INTO inventario (ID_Producto, Fecha, TipoMovimiento, Cantidad, ID_Usuario) VALUES
(1, '2025-03-15', 'Entrada', 15, 1),
(2, '2025-03-15', 'Entrada', 10, 1),
(3, '2025-03-16', 'Entrada', 12, 1),
(4, '2025-03-17', 'Entrada', 20, 1),
(5, '2025-03-18', 'Entrada', 25, 1),
(6, '2025-03-19', 'Entrada', 30, 1),
(7, '2025-03-20', 'Entrada', 18, 1),
(8, '2025-03-21', 'Entrada', 22, 1),
(9, '2025-03-22', 'Entrada', 40, 1),
(10, '2025-03-23', 'Entrada', 35, 1);

-- Salidas (Sales based on facturas)
INSERT INTO inventario (ID_Producto, Fecha, TipoMovimiento, Cantidad, ID_Usuario, ID_Factura) VALUES
(1, '2025-04-01', 'Salida', 1, 2, 1),
(3, '2025-04-01', 'Salida', 1, 2, 1),
(2, '2025-04-03', 'Salida', 1, 4, 2),
(4, '2025-04-05', 'Salida', 2, 2, 3),
(5, '2025-04-05', 'Salida', 1, 2, 3),
(6, '2025-04-08', 'Salida', 1, 4, 4),
(7, '2025-04-08', 'Salida', 2, 4, 4),
(8, '2025-04-01', 'Salida', 1, 2, 1);