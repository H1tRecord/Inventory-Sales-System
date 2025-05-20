<?php
require_once '../config/db_connection.php';
require_once '../model/category_model.php';

// Handles category CRUD operations and API endpoints
class CategoryController {
    private $model;

    // Initialize with database connection
    public function __construct($dbConnection) {
        $this->model = new Category($dbConnection);
    }

    // Retrieve all categories
    public function getAllCategories() {
        return $this->model->getAll();
    }

    // Get single category by ID
    public function getCategoryById($id) {
        return $this->model->getById($id);
    }

    // Create new category from POST data
    public function createCategory($data) {
        return $this->model->create($data);
    }

    // Update existing category
    public function updateCategory($id, $data) {
        return $this->model->update($id, $data);
    }

    // Delete category if no products are linked
    public function deleteCategory($id) {
        return $this->model->delete($id);
    }
}

// API endpoint handler
if (isset($_GET['action'])) {
    $controller = new CategoryController($conn);
    
    switch ($_GET['action']) {
        case 'get':
            $id = $_GET['id'];
            echo json_encode($controller->getCategoryById($id));
            break;
            
        case 'create':
            echo json_encode($controller->createCategory($_POST));
            break;
            
        case 'update':
            $id = $_POST['category_id'];
            echo json_encode($controller->updateCategory($id, $_POST));
            break;
            
        case 'delete':
            $id = $_POST['category_id'];
            $result = $controller->deleteCategory($id);
            if (!$result) {
                echo json_encode(['success' => false, 'error' => 'Cannot delete category that has products']);
            } else {
                echo json_encode(['success' => true]);
            }
            break;
    }
}
