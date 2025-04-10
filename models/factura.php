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
            $query = "CALL sp_CreateInvoice(:numeroSAP, :nombreCompleto, :userId)";
            
            $stmt = $this->conn->prepare($query);
            $stmt->bindParam(':numeroSAP', $data['numeroSAP']);
            $stmt->bindParam(':nombreCompleto', $data['nombreCliente']);
            $stmt->bindParam(':userId', $_SESSION['user_id']);
            
            return $stmt->execute();
        } catch (PDOException $e) {
            error_log("Error creating invoice: " . $e->getMessage());
            return false;
        }
    }

    public function addDetail($invoiceId, $productId, $quantity, $price) {
        $query = "EXEC sp_AddInvoiceDetail :invoiceId, :productId, :quantity, :price";
        try {
            $this->conn->beginTransaction();
        
            // Call sp_CreateInvoice stored procedure
            $query = "EXEC sp_CreateInvoice @NumeroSAP = ?, @NombreCliente = ?, @Total = ?";
            $stmt = $this->conn->prepare($query);
            $stmt->execute([
                $data['numeroSAP'],
                $data['nombreCliente'],
                $data['total']
            ]);
            
            $facturaId = $this->conn->lastInsertId();
        
            // Add invoice details using sp_AddInvoiceDetail
            foreach ($data['productos'] as $producto) {
                $query = "EXEC sp_AddInvoiceDetail @ID_Factura = ?, @ID_Producto = ?, @Cantidad = ?, @PrecioUnitario = ?";
                $stmt = $this->conn->prepare($query);
                $stmt->execute([
                    $facturaId,
                    $producto['id'],
                    $producto['cantidad'],
                    $producto['precio']
                ]);
            }

            $this->conn->commit();
            return true;
        } catch (PDOException $e) {
            $this->conn->rollBack();
            error_log("Error creating invoice: " . $e->getMessage());
            return false;
        }
    }
}
?>