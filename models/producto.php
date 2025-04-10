<?php
class Producto {
    private $conn;
    private $table_name = "Productos";

    public function __construct($db) {
        $this->conn = $db;
    }

    public function getTotalProducts() {
        try {
            $query = "SELECT COUNT(*) as total FROM " . $this->table_name . " WHERE Activo = 1";
            $stmt = $this->conn->prepare($query);
            $stmt->execute();
            $row = $stmt->fetch(PDO::FETCH_ASSOC);
            return $row['total'];
        } catch (PDOException $e) {
            error_log("Error getting total products: " . $e->getMessage());
            return 0;
        }
    }

    public function getAll() {
        try {
            $query = "SELECT * FROM " . $this->table_name . " WHERE Activo = 1";
            $stmt = $this->conn->prepare($query);
            $stmt->execute();
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            error_log("Error getting products: " . $e->getMessage());
            return [];
        }
    }

    public function create($data) {
        try {
            $query = "INSERT INTO " . $this->table_name . " 
                    (Nombre, Descripcion, Precio, Activo) 
                    VALUES (?, ?, ?, 1)";
            
            $stmt = $this->conn->prepare($query);
            return $stmt->execute([
                $data['nombre'],
                $data['descripcion'],
                $data['precio']
            ]);
        } catch (PDOException $e) {
            error_log("Error creating product: " . $e->getMessage());
            return false;
        }
    }

    public function update($data) {
        try {
            $query = "UPDATE " . $this->table_name . " 
                    SET Nombre = ?, 
                        Descripcion = ?, 
                        Precio = ? 
                    WHERE ID_Producto = ?";
            
            $stmt = $this->conn->prepare($query);
            return $stmt->execute([
                $data['nombre'],
                $data['descripcion'],
                $data['precio'],
                $data['id']
            ]);
        } catch (PDOException $e) {
            error_log("Error updating product: " . $e->getMessage());
            return false;
        }
    }

    public function delete($id) {
        try {
            $query = "UPDATE " . $this->table_name . " 
                    SET Activo = 0 
                    WHERE ID_Producto = ?";
            
            $stmt = $this->conn->prepare($query);
            return $stmt->execute([$id]);
        } catch (PDOException $e) {
            error_log("Error deleting product: " . $e->getMessage());
            return false;
        }
    }

    public function getById($id) {
        try {
            $query = "SELECT * FROM " . $this->table_name . " 
                    WHERE ID_Producto = ? AND Activo = 1";
            $stmt = $this->conn->prepare($query);
            $stmt->execute([$id]);
            return $stmt->fetch(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            error_log("Error getting product: " . $e->getMessage());
            return null;
        }
    }

    public function updateStock($productId, $newStock) {
        $query = "EXEC sp_UpdateProductStock :productId, :newStock";
        $stmt = $this->conn->prepare($query);
        $stmt->execute();
    }

    public function getStock($productoId) {
        try {
            $query = "SELECT i.ID_Producto, 
                    COALESCE(SUM(CASE 
                        WHEN i.TipoMovimiento = 'Entrada' THEN i.Cantidad
                        WHEN i.TipoMovimiento = 'Salida' THEN -i.Cantidad
                        ELSE 0
                    END), 0) as stock_actual
                    FROM Inventario i 
                    WHERE i.ID_Producto = ? 
                    AND i.Activo = 1
                    GROUP BY i.ID_Producto";
            
            $stmt = $this->conn->prepare($query);
            $stmt->execute([$productoId]);
            $row = $stmt->fetch(PDO::FETCH_ASSOC);
            return $row ? $row['stock_actual'] : 0;
        } catch (PDOException $e) {
            error_log("Error getting product stock: " . $e->getMessage());
            return 0;
        }
    }

    public function getLowStockProducts($threshold = 10) {
        try {
            $query = "SELECT p.ID_Producto, p.Nombre,
                    (SELECT COALESCE(SUM(CASE 
                        WHEN i.TipoMovimiento = 'Entrada' THEN i.Cantidad
                        WHEN i.TipoMovimiento = 'Salida' THEN -i.Cantidad
                        ELSE 0
                    END), 0)
                    FROM Inventario i 
                    WHERE i.ID_Producto = p.ID_Producto 
                    AND i.Activo = 1) as stock_actual
                    FROM " . $this->table_name . " p
                    WHERE p.Activo = 1
                    HAVING stock_actual <= ?
                    ORDER BY stock_actual ASC";

            $stmt = $this->conn->prepare($query);
            $stmt->execute([$threshold]);
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            error_log("Error getting low stock products: " . $e->getMessage());
            return [];
        }
    }
}
?>