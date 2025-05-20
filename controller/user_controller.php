<?php
require_once '../config/db_connection.php';
require_once '../model/user_model.php';

// Handles user management and authentication
class UserController {
    private $model;

    // Initialize with database connection
    public function __construct($dbConnection) {
        $this->model = new User($dbConnection);
    }

    // Get list of all users
    public function getAllUsers() {
        return $this->model->getAll();
    }

    // Get specific user details
    public function getUserById($id) {
        return $this->model->getById($id);
    }

    // Get employees without user accounts
    public function getAvailableEmployees() {
        return $this->model->getAvailableEmployees();
    }

    // Create new user account
    public function createUser($data) {
        return $this->model->create($data);
    }

    // Update existing user details
    public function updateUser($id, $data) {
        return $this->model->update($id, $data);
    }

    // Remove user account
    public function deleteUser($id) {
        return $this->model->delete($id);
    }

    // Get total number of admin accounts
    public function getAdminCount() {
        return ['admin_count' => $this->model->countAdmins()];
    }
}

// Handle AJAX requests
if (isset($_GET['action'])) {
    $controller = new UserController($conn);
    
    switch ($_GET['action']) {
        case 'get':
            $id = $_GET['id'];
            echo json_encode($controller->getUserById($id));
            break;
            
        case 'create':
            echo json_encode($controller->createUser($_POST));
            break;
            
        case 'update':
            $id = $_POST['user_id'];
            echo json_encode($controller->updateUser($id, $_POST));
            break;
            
        case 'delete':
            $id = $_POST['user_id'];
            $result = $controller->deleteUser($id);
            echo json_encode($result);
            break;

        case 'available_employees':
            echo json_encode($controller->getAvailableEmployees());
            break;

        case 'count_admins':
            echo json_encode($controller->getAdminCount());
            break;
    }
}
