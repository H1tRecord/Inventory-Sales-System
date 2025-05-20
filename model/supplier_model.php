<?php
class Supplier {
    private $conn;

    public function __construct($dbConnection) {
        $this->conn = $dbConnection;
    }

    public function getAll() {
        $query = "SELECT * FROM supplier ORDER BY Supplier_ID";
        $result = $this->conn->query($query);
        return $result->fetch_all(MYSQLI_ASSOC);
    }

    public function getById($id) {
        $query = "SELECT * FROM supplier WHERE Supplier_ID = ?";
        $stmt = $this->conn->prepare($query);
        $stmt->bind_param("i", $id);
        $stmt->execute();
        return $stmt->get_result()->fetch_assoc();
    }

    private function isDuplicate($field, $value, $excludeId = null) {
        // Generic duplicate checker for any supplier field
        // Used for Name, Email, and Contact validation
        // $excludeId allows updating existing record
        $query = "SELECT COUNT(*) as count FROM supplier WHERE $field = ?";
        $params = [$value];
        
        if ($excludeId) {
            $query .= " AND Supplier_ID != ?";
            $params[] = $excludeId;
        }
        
        $stmt = $this->conn->prepare($query);
        $stmt->bind_param(str_repeat('s', count($params)), ...$params);
        $stmt->execute();
        return $stmt->get_result()->fetch_assoc()['count'] > 0;
    }

    public function create($data) {
        // Check for duplicates
        if ($this->isDuplicate('Supplier_Name', $data['Supplier_Name'])) {
            return ['success' => false, 'error' => 'Supplier name already exists'];
        }
        if ($this->isDuplicate('Contact_Number', $data['Contact_Number'])) {
            return ['success' => false, 'error' => 'Contact number already exists'];
        }
        if ($this->isDuplicate('Email', $data['Email'])) {
            return ['success' => false, 'error' => 'Email already exists'];
        }

        $query = "INSERT INTO supplier (Supplier_Name, Contact_Number, Email, Address) VALUES (?, ?, ?, ?)";
        $stmt = $this->conn->prepare($query);
        $stmt->bind_param("ssss", 
            $data['Supplier_Name'],
            $data['Contact_Number'],
            $data['Email'],
            $data['Address']
        );
        
        if ($stmt->execute()) {
            return ['success' => true, 'new_id' => $this->conn->insert_id];
        }
        return ['success' => false, 'error' => 'Database error'];
    }

    public function update($id, $data) {
        // Check for duplicates
        if ($this->isDuplicate('Supplier_Name', $data['Supplier_Name'], $id)) {
            return ['success' => false, 'error' => 'Supplier name already exists'];
        }
        if ($this->isDuplicate('Contact_Number', $data['Contact_Number'], $id)) {
            return ['success' => false, 'error' => 'Contact number already exists'];
        }
        if ($this->isDuplicate('Email', $data['Email'], $id)) {
            return ['success' => false, 'error' => 'Email already exists'];
        }

        $query = "UPDATE supplier SET Supplier_Name = ?, Contact_Number = ?, Email = ?, Address = ? WHERE Supplier_ID = ?";
        $stmt = $this->conn->prepare($query);
        $stmt->bind_param("ssssi", 
            $data['Supplier_Name'],
            $data['Contact_Number'],
            $data['Email'],
            $data['Address'],
            $id
        );
        return ['success' => $stmt->execute()];
    }

    public function hasAssociatedProducts($id) {
        $query = "SELECT COUNT(*) as count FROM product WHERE Supplier_ID = ?";
        $stmt = $this->conn->prepare($query);
        $stmt->bind_param("i", $id);
        $stmt->execute();
        $result = $stmt->get_result()->fetch_assoc();
        return $result['count'] > 0;
    }

    public function delete($id) {
        // First check for linked products before deletion
        // Prevents orphaned product records
        if ($this->hasAssociatedProducts($id)) {
            return ['success' => false, 'error' => 'Cannot delete supplier: There are products associated with this supplier'];
        }

        $query = "DELETE FROM supplier WHERE Supplier_ID = ?";
        $stmt = $this->conn->prepare($query);
        $stmt->bind_param("i", $id);
        return ['success' => $stmt->execute()];
    }
}
