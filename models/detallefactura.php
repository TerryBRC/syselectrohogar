class DetalleFactura {
    private $conn;
    private $table_name = "DetallesFactura";

    public function __construct($db) {
        $this->conn = $db;
    }

    public function create($facturaId, $productoId, $cantidad, $precioUnitario) {
        try {
            $query = "INSERT INTO " . $this->table_name . " 
                    (ID_Factura, ID_Producto, Cantidad, PrecioUnitario, Activo) 
                    VALUES (?, ?, ?, ?, 1)";
            
            $stmt = $this->conn->prepare($query);
            return $stmt->execute([$facturaId, $productoId, $cantidad, $precioUnitario]);
        } catch (PDOException $e) {
            error_log("Error creating invoice detail: " . $e->getMessage());
            return false;
        }
    }
}