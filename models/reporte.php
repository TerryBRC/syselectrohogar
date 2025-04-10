<?php
class Reporte {
    private $conn;

    public function __construct($db) {
        $this->conn = $db;
    }

    public function getSalesReport($startDate, $endDate) {
        try {
            $query = "CALL sp_GetSalesReport(:startDate, :endDate)";
            $stmt = $this->conn->prepare($query);
            $stmt->bindParam(':startDate', $startDate);
            $stmt->bindParam(':endDate', $endDate);
            $stmt->execute();
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            error_log("Error getting sales report: " . $e->getMessage());
            return [];
        }
    }

    public function getProductosMasVendidos() {
        try {
            $query = "EXEC sp_GetTopSellingProducts";
            $stmt = $this->conn->prepare($query);
            $stmt->execute();
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            error_log("Error getting top selling products: " . $e->getMessage());
            return [];
        }
    }

    public function getMovimientosStock() {
        try {
            $query = "EXEC sp_GetStockMovements";
            $stmt = $this->conn->prepare($query);
            $stmt->execute();
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            error_log("Error getting stock movements: " . $e->getMessage());
            return [];
        }
    }
}
?>