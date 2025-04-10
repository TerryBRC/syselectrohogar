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

    public function getTotalActive() {
        $query = "SELECT COUNT(*) as total FROM productos WHERE Activo = 1";
        $stmt = $this->conn->prepare($query);
        $stmt->execute();
        $row = $stmt->fetch(PDO::FETCH_ASSOC);
        return $row['total'];
    }

    public function getAll($page = 1, $limit = 10, $includeInactive = false) {
        try {
            $offset = ($page - 1) * $limit;
            $activeCondition = $includeInactive ? "" : "WHERE p.Activo = 1";
            
            $query = "SELECT p.*, 
                      COALESCE(SUM(CASE 
                          WHEN i.TipoMovimiento = 'Entrada' THEN i.Cantidad
                          WHEN i.TipoMovimiento = 'Salida' THEN -i.Cantidad
                          ELSE 0
                      END), 0) as stock_actual
                      FROM " . $this->table_name . " p
                      LEFT JOIN Inventario i ON p.ID_Producto = i.ID_Producto AND i.Activo = 1
                      {$activeCondition}
                      GROUP BY p.ID_Producto, p.Nombre, p.Descripcion, p.Precio, p.Activo
                      ORDER BY p.ID_Producto DESC
                      LIMIT :limit OFFSET :offset";
            
            $stmt = $this->conn->prepare($query);
            $stmt->bindValue(':limit', $limit, PDO::PARAM_INT);
            $stmt->bindValue(':offset', $offset, PDO::PARAM_INT);
            $stmt->execute();
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            error_log("Error getting products: " . $e->getMessage());
            return [];
        }
    }

    public function getTotalPages($limit = 10, $includeInactive = false) {
        try {
            $activeCondition = $includeInactive ? "" : "WHERE Activo = 1";
            $query = "SELECT CEIL(COUNT(*) / :limit) as total_pages FROM " . $this->table_name . " " . $activeCondition;
            $stmt = $this->conn->prepare($query);
            $stmt->bindValue(':limit', $limit, PDO::PARAM_INT);
            $stmt->execute();
            return $stmt->fetch(PDO::FETCH_ASSOC)['total_pages'];
        } catch (PDOException $e) {
            error_log("Error getting total pages: " . $e->getMessage());
            return 0;
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
            $result = $stmt->execute([$id]);
            
            if ($result) {
                error_log("Product deleted successfully: " . $id);
            } else {
                error_log("Error deleting product: " . $id);
            }
            
            return $result;
        } catch (PDOException $e) {
            error_log("Exception deleting product: " . $e->getMessage());
            return false;
        }
    }

    public function restore($id) {
        try {
            $query = "UPDATE " . $this->table_name . " 
                    SET Activo = 1 
                    WHERE ID_Producto = ?";
            
            $stmt = $this->conn->prepare($query);
            $result = $stmt->execute([$id]);
            
            if ($result) {
                error_log("Product restored successfully: " . $id);
            } else {
                error_log("Error restoring product: " . $id);
            }
            
            return $result;
        } catch (PDOException $e) {
            error_log("Exception restoring product: " . $e->getMessage());
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

    public function getInactiveProducts() {
        try {
            $query = "SELECT p.*, 
                    COALESCE(SUM(CASE 
                        WHEN i.TipoMovimiento = 'Entrada' THEN i.Cantidad
                        WHEN i.TipoMovimiento = 'Salida' THEN -i.Cantidad
                        ELSE 0
                    END), 0) as stock_actual
                    FROM " . $this->table_name . " p
                    LEFT JOIN Inventario i ON p.ID_Producto = i.ID_Producto AND i.Activo = 1
                    WHERE p.Activo = 0
                    GROUP BY p.ID_Producto, p.Nombre, p.Descripcion, p.Precio, p.Activo";
            
            $stmt = $this->conn->prepare($query);
            $stmt->execute();
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            error_log("Error getting inactive products: " . $e->getMessage());
            return [];
        }
    }
}
?>