<?php
require_once '../config/db_connection.php';
require_once '../model/supplier_model.php';

// Handles supplier data management
class SupplierController {
    private $model;

    // Initialize with database connection
    public function __construct($dbConnection) {
        $this->model = new Supplier($dbConnection);
    }

    // Get all suppliers list
    public function getAllSuppliers() {
        return $this->model->getAll();
    }

    // Get specific supplier details
    public function getSupplierById($id) {
        return $this->model->getById($id);
    }

    // Add new supplier
    public function createSupplier($data) {
        return $this->model->create($data);
    }

    // Update supplier information
    public function updateSupplier($id, $data) {
        return $this->model->update($id, $data);
    }

    // Remove supplier record
    public function deleteSupplier($id) {
        return $this->model->delete($id);
    }
}

// Handle AJAX requests
if (isset($_GET['action'])) {
    $controller = new SupplierController($conn);
    
    switch ($_GET['action']) {
        case 'get':
            $id = $_GET['id'];
            echo json_encode($controller->getSupplierById($id));
            break;
            
        case 'create':
            echo json_encode($controller->createSupplier($_POST));
            break;
            
        case 'update':
            $id = $_POST['supplier_id'];
            echo json_encode($controller->updateSupplier($id, $_POST));
            break;
            
        case 'delete':
            $id = $_POST['supplier_id'];
            $result = $controller->deleteSupplier($id);
            echo json_encode($result);
            break;
    }
}
