<?php
require_once '../config/db_connection.php';
require_once '../model/job_model.php';

// Handles job positions management
class JobController {
    private $model;

    // Initialize with database connection
    public function __construct($dbConnection) {
        $this->model = new Job($dbConnection);
    }

    // Get all job positions
    public function getAllJobs() {
        return $this->model->getAll();
    }

    // Get specific job details
    public function getJobById($id) {
        return $this->model->getById($id);
    }

    // Create new job position
    public function createJob($data) {
        return $this->model->create($data);
    }

    // Update job position details
    public function updateJob($id, $data) {
        return $this->model->update($id, $data);
    }

    // Remove job position
    public function deleteJob($id) {
        return $this->model->delete($id);
    }
}

// Handle AJAX requests
if (isset($_GET['action'])) {
    $controller = new JobController($conn);
    
    switch ($_GET['action']) {
        case 'get':
            $id = $_GET['id'];
            echo json_encode($controller->getJobById($id));
            break;
            
        case 'create':
            echo json_encode($controller->createJob($_POST));
            break;
            
        case 'update':
            $id = $_POST['job_id'];
            echo json_encode($controller->updateJob($id, $_POST));
            break;
            
        case 'delete':
            $id = $_POST['job_id'];
            echo json_encode($controller->deleteJob($id));
            break;
    }
}
