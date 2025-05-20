<?php
class Job {
    private $conn;

    public function __construct($dbConnection) {
        $this->conn = $dbConnection;
    }

    public function getAll() {
        // Get jobs with employee count using LEFT JOIN
        // Shows jobs even with no assigned employees
        $query = "SELECT j.*, COUNT(e.Employee_ID) as Employee_Count 
                  FROM job j 
                  LEFT JOIN employee e ON j.Job_ID = e.Job_ID 
                  GROUP BY j.Job_ID 
                  ORDER BY j.Job_Title";
        $result = $this->conn->query($query);
        return $result->fetch_all(MYSQLI_ASSOC);
    }

    public function getById($id) {
        $query = "SELECT * FROM job WHERE Job_ID = ?";
        $stmt = $this->conn->prepare($query);
        $stmt->bind_param("i", $id);
        $stmt->execute();
        return $stmt->get_result()->fetch_assoc();
    }

    public function create($data) {
        if ($this->isDuplicateTitle($data['Job_Title'])) {
            return ['success' => false, 'error' => 'Job title already exists'];
        }

        $query = "INSERT INTO job (Job_Title) VALUES (?)";
        $stmt = $this->conn->prepare($query);
        $stmt->bind_param("s", $data['Job_Title']);
        
        if ($stmt->execute()) {
            return ['success' => true, 'new_id' => $this->conn->insert_id];
        }
        return ['success' => false, 'error' => 'Database error'];
    }

    public function update($id, $data) {
        if ($this->isDuplicateTitle($data['Job_Title'], $id)) {
            return ['success' => false, 'error' => 'Job title already exists'];
        }

        $query = "UPDATE job SET Job_Title = ? WHERE Job_ID = ?";
        $stmt = $this->conn->prepare($query);
        $stmt->bind_param("si", $data['Job_Title'], $id);
        return ['success' => $stmt->execute()];
    }

    public function delete($id) {
        // Prevent deletion of jobs with assigned employees
        // Maintains data integrity
        if ($this->hasEmployees($id)) {
            return ['success' => false, 'error' => 'Cannot delete job with assigned employees'];
        }

        $query = "DELETE FROM job WHERE Job_ID = ?";
        $stmt = $this->conn->prepare($query);
        $stmt->bind_param("i", $id);
        return ['success' => $stmt->execute()];
    }

    private function isDuplicateTitle($title, $excludeId = null) {
        $query = "SELECT COUNT(*) as count FROM job WHERE Job_Title = ?";
        $params = [$title];
        
        if ($excludeId) {
            $query .= " AND Job_ID != ?";
            $params[] = $excludeId;
        }
        
        $stmt = $this->conn->prepare($query);
        $stmt->bind_param(str_repeat('s', count($params)), ...$params);
        $stmt->execute();
        return $stmt->get_result()->fetch_assoc()['count'] > 0;
    }

    private function hasEmployees($jobId) {
        $query = "SELECT COUNT(*) as count FROM employee WHERE Job_ID = ?";
        $stmt = $this->conn->prepare($query);
        $stmt->bind_param("i", $jobId);
        $stmt->execute();
        return $stmt->get_result()->fetch_assoc()['count'] > 0;
    }
}
