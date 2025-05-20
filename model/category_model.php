<?php
/**
 * Category Model Class
 * Handles database operations for product categories
 */
class Category {
    private $conn;

    public function __construct($dbConnection) {
        $this->conn = $dbConnection;
    }

    /**
     * Get all categories with their product counts
     * Uses LEFT JOIN to include empty categories
     */
    public function getAll() {
        // Get categories with product count using LEFT JOIN
        // LEFT JOIN ensures we get categories even with no products
        // Product_Count will be 0 for categories with no products
        $query = "SELECT c.*, COUNT(p.Product_ID) as Product_Count 
                  FROM category c 
                  LEFT JOIN product p ON c.Category_ID = p.Category_ID 
                  GROUP BY c.Category_ID 
                  ORDER BY c.Category_Name";
        $result = $this->conn->query($query);
        return $result->fetch_all(MYSQLI_ASSOC);
    }

    public function getById($id) {
        $query = "SELECT * FROM category WHERE Category_ID = ?";
        $stmt = $this->conn->prepare($query);
        $stmt->bind_param("i", $id);
        $stmt->execute();
        return $stmt->get_result()->fetch_assoc();
    }

    /**
     * Validate and create new category
     * Checks for duplicate names before insertion
     */
    public function create($data) {
        if ($this->isDuplicateName($data['Category_Name'])) {
            return ['success' => false, 'error' => 'Category name already exists'];
        }

        $query = "INSERT INTO category (Category_Name) VALUES (?)";
        $stmt = $this->conn->prepare($query);
        $stmt->bind_param("s", $data['Category_Name']);
        
        if ($stmt->execute()) {
            return ['success' => true, 'new_id' => $this->conn->insert_id];
        }
        return ['success' => false, 'error' => 'Database error'];
    }

    public function update($id, $data) {
        if ($this->isDuplicateName($data['Category_Name'], $id)) {
            return ['success' => false, 'error' => 'Category name already exists'];
        }

        $query = "UPDATE category SET Category_Name = ? WHERE Category_ID = ?";
        $stmt = $this->conn->prepare($query);
        $stmt->bind_param("si", $data['Category_Name'], $id);
        return ['success' => $stmt->execute()];
    }

    public function delete($id) {
        // Check for products first
        if ($this->hasProducts($id)) {
            return false;
        }

        $query = "DELETE FROM category WHERE Category_ID = ?";
        $stmt = $this->conn->prepare($query);
        $stmt->bind_param("i", $id);
        return $stmt->execute();
    }

    private function hasProducts($categoryId) {
        $query = "SELECT COUNT(*) as count FROM product WHERE Category_ID = ?";
        $stmt = $this->conn->prepare($query);
        $stmt->bind_param("i", $categoryId);
        $stmt->execute();
        return $stmt->get_result()->fetch_assoc()['count'] > 0;
    }

    /**
     * Helper: Check if category name exists
     * @param string $name Category name to check
     * @param int|null $excludeId ID to exclude from check during updates
     */
    private function isDuplicateName($name, $excludeId = null) {
        // Dynamic query building for checking duplicates
        // excludeId is used when updating to ignore current record
        $query = "SELECT COUNT(*) as count FROM category WHERE Category_Name = ?";
        $params = [$name];
        
        if ($excludeId) {
            $query .= " AND Category_ID != ?";
            $params[] = $excludeId;
        }
        
        // Build dynamic parameter binding string based on number of parameters
        $stmt = $this->conn->prepare($query);
        $stmt->bind_param(str_repeat('s', count($params)), ...$params);
        $stmt->execute();
        return $stmt->get_result()->fetch_assoc()['count'] > 0;
    }
}
