<?php
class Inventario {
    private $conn;
    private $table_name = "Inventario";

    public function __construct($db) {
        $this->conn = $db;
    }

    public function getCurrentStock($productoId) {
        try {
            $query = "SELECT COALESCE(SUM(CASE 
                        WHEN TipoMovimiento = 'Entrada' THEN Cantidad 
                        WHEN TipoMovimiento LIKE 'Traslado%' OR TipoMovimiento = 'Venta' THEN -Cantidad
                        ELSE 0 END), 0) as Stock 
                     FROM " . $this->table_name . " 
                     WHERE ID_Producto = ? AND Activo = 1";
            
            $stmt = $this->conn->prepare($query);
            $stmt->execute([$productoId]);
            $row = $stmt->fetch(PDO::FETCH_ASSOC);
            return $row['Stock'];
        } catch (PDOException $e) {
            error_log("Error getting stock: " . $e->getMessage());
            return 0;
        }
    }

    public function registerMovement($data) {
        try {
            // Check stock if it's not an entry
            if ($data['tipo'] !== 'Entrada') {
                $currentStock = $this->getCurrentStock($data['producto_id']);
                if ($currentStock < $data['cantidad']) {
                    throw new Exception("Stock insuficiente. Stock actual: " . $currentStock);
                }
            }

            $query = "INSERT INTO " . $this->table_name . " 
                 (ID_Producto, TipoMovimiento, Cantidad, ID_Usuario, Fecha) 
                 VALUES (:productId, :movementType, :quantity, :userId, NOW())";
            
            $stmt = $this->conn->prepare($query);
            $stmt->bindParam(':productId', $data['producto_id'], PDO::PARAM_INT);
            $stmt->bindParam(':movementType', $data['tipo'], PDO::PARAM_STR);
            $stmt->bindParam(':quantity', $data['cantidad'], PDO::PARAM_INT);
            $stmt->bindParam(':userId', $_SESSION['user_id'], PDO::PARAM_INT);
            
            return $stmt->execute();
        } catch (Exception $e) {
            error_log("Error registering movement: " . $e->getMessage());
            throw $e;
        }
    }

    public function getLowStockProducts() {
        $threshold = defined('STOCK_ALERT_THRESHOLD') ? STOCK_ALERT_THRESHOLD : 10;
        
        try {
            $query = "SELECT p.ID_Producto, p.Nombre, 
                      COALESCE(SUM(CASE 
                          WHEN i.TipoMovimiento = 'Entrada' THEN i.Cantidad 
                          WHEN i.TipoMovimiento LIKE 'Traslado%' OR i.TipoMovimiento = 'Venta' THEN -i.Cantidad
                          ELSE 0 END), 0) as stock_actual
                      FROM productos p
                      LEFT JOIN inventario i ON p.ID_Producto = i.ID_Producto AND i.Activo = 1
                      WHERE p.Activo = 1
                      GROUP BY p.ID_Producto, p.Nombre
                      HAVING stock_actual <= :threshold
                      ORDER BY stock_actual ASC";
                      
            $stmt = $this->conn->prepare($query);
            $stmt->bindParam(':threshold', $threshold, PDO::PARAM_INT);
            $stmt->execute();
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            error_log("Error getting low stock products: " . $e->getMessage());
            return [];
        }
    }

    public function getTotalMovements() {
        try {
            $query = "SELECT COUNT(*) as total FROM " . $this->table_name . " WHERE Activo = 1";
            $stmt = $this->conn->prepare($query);
            $stmt->execute();
            $row = $stmt->fetch(PDO::FETCH_ASSOC);
            return $row['total'];
        } catch (PDOException $e) {
            error_log("Error getting total movements: " . $e->getMessage());
            return 0;
        }
    }

    public function getMovements($page = 1, $limit = 10) {
        try {
            $offset = ($page - 1) * $limit;
            $query = "SELECT 
                        i.Fecha,
                        p.Nombre as NombreProducto,
                        i.TipoMovimiento,
                        i.Cantidad,
                        u.CorreoElectronico as Usuario,
                        COALESCE(f.NumeroSAP, 'N/A') as FacturaSAP,
                        (
                            SELECT COALESCE(SUM(CASE 
                                WHEN TipoMovimiento = 'Entrada' THEN Cantidad
                                ELSE -Cantidad
                            END), 0)
                            FROM " . $this->table_name . " i2
                            WHERE i2.ID_Producto = i.ID_Producto
                            AND i2.Fecha <= i.Fecha
                            AND i2.Activo = 1
                        ) as StockActual
                    FROM " . $this->table_name . " i
                    JOIN productos p ON i.ID_Producto = p.ID_Producto
                    JOIN usuarios u ON i.ID_Usuario = u.ID_Usuario
                    LEFT JOIN facturas f ON i.ID_Factura = f.ID_Factura
                    WHERE i.Activo = 1
                    ORDER BY i.Fecha DESC, i.ID_Movimiento DESC
                    LIMIT :limit OFFSET :offset";
            
            $stmt = $this->conn->prepare($query);
            $stmt->bindValue(':limit', $limit, PDO::PARAM_INT);
            $stmt->bindValue(':offset', $offset, PDO::PARAM_INT);
            $stmt->execute();
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            error_log("Error getting movements: " . $e->getMessage());
            throw $e;
        }
    }
}
?>