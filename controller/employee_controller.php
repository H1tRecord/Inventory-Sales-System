<?php
require_once '../config/db_connection.php';
require_once '../model/employee_model.php';

// Handles employee data management
class EmployeeController {
    private $model;

    // Initialize with database connection
    public function __construct($dbConnection) {
        $this->model = new Employee($dbConnection);
    }

    // Get all employees list
    public function getAllEmployees() {
        return $this->model->getAll();
    }

    // Get specific employee details
    public function getEmployeeById($id) {
        return $this->model->getById($id);
    }

    // Add new employee
    public function createEmployee($data) {
        return $this->model->create($data);
    }

    // Update employee information
    public function updateEmployee($id, $data) {
        return $this->model->update($id, $data);
    }

    // Remove employee record
    public function deleteEmployee($id) {
        return $this->model->delete($id);
    }

    // Get available job positions
    public function getAllJobs() {
        return $this->model->getAllJobs();
    }
}

// Handle AJAX requests
if (isset($_GET['action'])) {
    $controller = new EmployeeController($conn);
    
    switch ($_GET['action']) {
        case 'get':
            $id = $_GET['id'];
            echo json_encode($controller->getEmployeeById($id));
            break;
            
        case 'update':
            $id = $_POST['employeeId'];
            $result = $controller->updateEmployee($id, $_POST);
            if (is_array($result)) {
                echo json_encode($result);
            } else {
                echo json_encode(['success' => $result]);
            }
            break;
            
        case 'create':
            $result = $controller->createEmployee($_POST);
            if (is_array($result)) {
                echo json_encode($result);
            } else {
                echo json_encode(['success' => $result]);
            }
            break;

        case 'delete':
            $id = $_POST['employee_id'];
            $result = $controller->deleteEmployee($id);
            echo json_encode($result);
            break;
    }
}
