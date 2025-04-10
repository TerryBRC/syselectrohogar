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
                        ELSE -Cantidad END), 0) as Stock 
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
            $query = "CALL sp_RegisterInventoryMovement(:productId, :movementType, :quantity, :userId, :invoiceId)";
            
            $stmt = $this->conn->prepare($query);
            $stmt->bindParam(':productId', $data['producto_id']);
            $stmt->bindParam(':movementType', $data['tipo']);
            $stmt->bindParam(':quantity', $data['cantidad']);
            $stmt->bindParam(':userId', $_SESSION['user_id']);
            $stmt->bindParam(':invoiceId', $data['factura_id'], PDO::PARAM_INT);
            
            return $stmt->execute();
        } catch (PDOException $e) {
            error_log("Error registering movement: " . $e->getMessage());
            return false;
        }
    }

    public function getLowStockProducts() {
        $threshold = defined('STOCK_ALERT_THRESHOLD') ? STOCK_ALERT_THRESHOLD : 10;
        
        $query = "SELECT p.ID_Producto, p.Nombre, 
                  COALESCE(SUM(CASE 
                      WHEN i.TipoMovimiento = 'Entrada' THEN i.Cantidad 
                      ELSE -i.Cantidad END), 0) as stock_actual
                  FROM productos p
                  LEFT JOIN inventario i ON p.ID_Producto = i.ID_Producto AND i.Activo = 1
                  WHERE p.Activo = 1
                  GROUP BY p.ID_Producto, p.Nombre
                  HAVING stock_actual <= :threshold";
                  
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':threshold', $threshold, PDO::PARAM_INT);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
}
?>