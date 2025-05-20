<?php
require_once '../config/db_connection.php';
require_once '../model/product_model.php';

// Handles product inventory management
class ProductController {
    private $model;
    private $conn;

    // Initialize with database connection
    public function __construct($dbConnection) {
        $this->model = new Product($dbConnection);
        $this->conn = $dbConnection;
    }

    // Get complete product inventory
    public function getAllProducts() {
        return $this->model->getAll();
    }

    // Get specific product details
    public function getProductById($id) {
        return $this->model->getById($id);
    }

    // Add new product to inventory
    public function createProduct($data) {
        return $this->model->create($data);
    }

    // Update product information
    public function updateProduct($id, $data) {
        return $this->model->update($id, $data);
    }

    // Remove product from inventory
    public function deleteProduct($id) {
        return $this->model->delete($id);
    }

    // Get available product categories
    public function getAllCategories() {
        $result = $this->conn->query("SELECT * FROM category ORDER BY Category_Name");
        return $result->fetch_all(MYSQLI_ASSOC);
    }

    // Get available suppliers
    public function getAllSuppliers() {
        $result = $this->conn->query("SELECT * FROM supplier ORDER BY Supplier_Name");
        return $result->fetch_all(MYSQLI_ASSOC);
    }
}

// Handle AJAX requests
if (isset($_GET['action'])) {
    $controller = new ProductController($conn);
    
    switch ($_GET['action']) {
        case 'get':
            $id = $_GET['id'];
            echo json_encode($controller->getProductById($id));
            break;
            
        case 'create':
            echo json_encode($controller->createProduct($_POST));
            break;
            
        case 'update':
            $id = $_POST['product_id'];
            echo json_encode($controller->updateProduct($id, $_POST));
            break;
            
        case 'delete':
            $id = $_POST['product_id'];
            echo json_encode(['success' => $controller->deleteProduct($id)]);
            break;
            
        case 'get_categories':
            echo json_encode($controller->getAllCategories());
            break;
            
        case 'get_suppliers':
            echo json_encode($controller->getAllSuppliers());
            break;
    }
}
