<?php
class Factura {
    private $conn;
    private $table_name = "Facturas";

    public function __construct($db) {
        $this->conn = $db;
    }

    public function getMonthlyInvoices() {
        try {
            $query = "SELECT COUNT(*) as total FROM " . $this->table_name . " 
                     WHERE MONTH(Fecha) = MONTH(CURRENT_DATE()) 
                     AND YEAR(Fecha) = YEAR(CURRENT_DATE()) 
                     AND Activo = 1";
            
            $stmt = $this->conn->prepare($query);
            $stmt->execute();
            $row = $stmt->fetch(PDO::FETCH_ASSOC);
            return $row['total'];
        } catch (PDOException $e) {
            error_log("Error getting monthly invoices: " . $e->getMessage());
            return 0;
        }
    }

    public function create($data) {
        try {
            $query = "CALL sp_CreateInvoice(:numeroFactura, :tipoPago, :telefono, :numeroSAP, :nombreCompleto, :userId)";
            
            $stmt = $this->conn->prepare($query);
            $stmt->bindParam(':numeroFactura', $data['numeroFactura']);
            $stmt->bindParam(':tipoPago', $data['tipoPago']);
            $stmt->bindParam(':telefono', $data['telefono']);
            $stmt->bindParam(':numeroSAP', $data['numeroSAP']);
            $stmt->bindParam(':nombreCompleto', $data['nombreCliente']);
            $stmt->bindParam(':userId', $_SESSION['user_id']);
            
            $stmt->execute();
            
            // Get the result from the stored procedure
            $result = $stmt->fetch(PDO::FETCH_ASSOC);
            
            if (!$result || !isset($result['ID_Factura'])) {
                throw new Exception('No se pudo obtener el ID de la factura');
            }
            
            return $result['ID_Factura'];
        } catch (PDOException $e) {
            error_log("Error creating invoice: " . $e->getMessage());
            throw new Exception('Error al crear la factura: ' . $e->getMessage());
        }
    }

    public function addDetail($invoiceId, $productId, $descuento, $quantity, $price) {
        try {
            $query = "CALL sp_AddInvoiceDetail(?, ?, ?, ?, ?)";
            $stmt = $this->conn->prepare($query);
            return $stmt->execute([$invoiceId, $productId, $descuento, $quantity, $price]);
        } catch (PDOException $e) {
            error_log("Error adding invoice detail: " . $e->getMessage());
            throw $e;
        }
    }

    public function getCountByDateRange($startDate, $endDate) {
        $query = "SELECT COUNT(*) as total FROM facturas 
                  WHERE Fecha BETWEEN :startDate AND :endDate 
                  AND Activo = 1";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':startDate', $startDate);
        $stmt->bindParam(':endDate', $endDate);
        $stmt->execute();
        $row = $stmt->fetch(PDO::FETCH_ASSOC);
        return $row['total'];
    }
}
?>