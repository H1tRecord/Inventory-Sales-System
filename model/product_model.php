<?php
class Product {
    private $conn;

    public function __construct($dbConnection) {
        $this->conn = $dbConnection;
    }

    public function getAll() {
        // Retrieve full product details with related data
        // Uses LEFT JOIN to include products without suppliers/categories
        $query = "SELECT p.*, s.Supplier_Name, c.Category_Name 
                 FROM product p 
                 LEFT JOIN supplier s ON p.Supplier_ID = s.Supplier_ID
                 LEFT JOIN category c ON p.Category_ID = c.Category_ID
                 ORDER BY p.Product_ID";
        $result = $this->conn->query($query);
        return $result->fetch_all(MYSQLI_ASSOC);
    }

    public function getById($id) {
        $query = "SELECT * FROM product WHERE Product_ID = ?";
        $stmt = $this->conn->prepare($query);
        $stmt->bind_param("i", $id);
        $stmt->execute();
        return $stmt->get_result()->fetch_assoc();
    }

    private function isProductCodeDuplicate($productCode, $excludeId = null) {
        // Ensures unique product codes across inventory
        // ExcludeId parameter allows updates to existing products
        $query = "SELECT COUNT(*) as count FROM product WHERE Product_Code = ?";
        $params = [$productCode];
        
        if ($excludeId) {
            $query .= " AND Product_ID != ?";
            $params[] = $excludeId;
        }
        
        $stmt = $this->conn->prepare($query);
        $stmt->bind_param(str_repeat('s', count($params)), ...$params);
        $stmt->execute();
        return $stmt->get_result()->fetch_assoc()['count'] > 0;
    }

    public function create($data) {
        if ($this->isProductCodeDuplicate($data['Product_Code'])) {
            return ['success' => false, 'error' => 'Product Code already exists'];
        }

        $query = "INSERT INTO product (Product_Code, Product_Name, Description, Category_ID, 
                                     In_Stock, Selling_Price, Product_Added, Supplier_ID) 
                 VALUES (?, ?, ?, ?, ?, ?, NOW(), ?)";
        $stmt = $this->conn->prepare($query);
        $stmt->bind_param("sssiids", 
            $data['Product_Code'],
            $data['Product_Name'],
            $data['Description'],
            $data['Category_ID'],
            $data['In_Stock'],
            $data['Selling_Price'],
            $data['Supplier_ID']
        );
        
        if ($stmt->execute()) {
            return ['success' => true, 'new_id' => $this->conn->insert_id];
        }
        return ['success' => false, 'error' => 'Database error'];
    }

    public function update($id, $data) {
        if ($this->isProductCodeDuplicate($data['Product_Code'], $id)) {
            return ['success' => false, 'error' => 'Product Code already exists'];
        }

        $query = "UPDATE product SET 
                    Product_Code = ?, 
                    Product_Name = ?, 
                    Description = ?, 
                    Category_ID = ?, 
                    In_Stock = ?, 
                    Selling_Price = ?, 
                    Supplier_ID = ? 
                 WHERE Product_ID = ?";
        $stmt = $this->conn->prepare($query);
        $stmt->bind_param("sssiidsi", 
            $data['Product_Code'],
            $data['Product_Name'],
            $data['Description'],
            $data['Category_ID'],
            $data['In_Stock'],
            $data['Selling_Price'],
            $data['Supplier_ID'],
            $id
        );
        return ['success' => $stmt->execute()];
    }

    public function delete($id) {
        $query = "DELETE FROM product WHERE Product_ID = ?";
        $stmt = $this->conn->prepare($query);
        $stmt->bind_param("i", $id);
        return $stmt->execute();
    }
}
