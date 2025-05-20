<?php
require_once '../config/db_connection.php';

/**
 * Employee Model Class
 * Manages employee records and related job positions
 */
class Employee {
    private $conn;

    public function __construct($dbConnection) {
        $this->conn = $dbConnection;
    }

    public function getAll() {
        $query = "SELECT e.Employee_ID, e.Name, e.Email, e.PhoneNo, j.Job_Title 
                  FROM employee e 
                  LEFT JOIN job j ON e.Job_ID = j.Job_ID";
        $result = $this->conn->query($query);
        return $result->fetch_all(MYSQLI_ASSOC);
    }

    public function getById($id) {
        $query = "SELECT * FROM employee WHERE Employee_ID = ?";
        $stmt = $this->conn->prepare($query);
        $stmt->bind_param("i", $id);
        $stmt->execute();
        return $stmt->get_result()->fetch_assoc();
    }

    public function isDuplicateEmail($email, $excludeId = null) {
        $query = "SELECT COUNT(*) as count FROM employee WHERE Email = ?";
        $params = [$email];
        
        if ($excludeId) {
            $query .= " AND Employee_ID != ?";
            $params[] = $excludeId;
        }
        
        $stmt = $this->conn->prepare($query);
        $stmt->bind_param(str_repeat('s', count($params)), ...$params);
        $stmt->execute();
        return $stmt->get_result()->fetch_assoc()['count'] > 0;
    }

    public function isDuplicatePhone($phone, $excludeId = null) {
        $query = "SELECT COUNT(*) as count FROM employee WHERE PhoneNo = ?";
        $params = [$phone];
        
        if ($excludeId) {
            $query .= " AND Employee_ID != ?";
            $params[] = $excludeId;
        }
        
        $stmt = $this->conn->prepare($query);
        $stmt->bind_param(str_repeat('s', count($params)), ...$params);
        $stmt->execute();
        return $stmt->get_result()->fetch_assoc()['count'] > 0;
    }

    public function isDuplicateName($name, $excludeId = null) {
        $query = "SELECT COUNT(*) as count FROM employee WHERE Name = ?";
        $params = [$name];
        
        if ($excludeId) {
            $query .= " AND Employee_ID != ?";
            $params[] = $excludeId;
        }
        
        $stmt = $this->conn->prepare($query);
        $stmt->bind_param(str_repeat('s', count($params)), ...$params);
        $stmt->execute();
        return $stmt->get_result()->fetch_assoc()['count'] > 0;
    }

    /**
     * Validate and create new employee
     * Checks for duplicate name, email, and phone
     */
    public function create($data) {
        // Check for duplicates
        if ($this->isDuplicateName($data['Name'])) {
            return ['success' => false, 'error' => 'Employee name already exists'];
        }
        if ($this->isDuplicateEmail($data['Email'])) {
            return ['success' => false, 'error' => 'Email already exists'];
        }
        if ($this->isDuplicatePhone($data['PhoneNo'])) {
            return ['success' => false, 'error' => 'Phone number already exists'];
        }

        $query = "INSERT INTO employee (Name, Email, PhoneNo, Job_ID) 
                  VALUES (?, ?, ?, ?)";
        $stmt = $this->conn->prepare($query);
        $stmt->bind_param("sssi", 
            $data['Name'],
            $data['Email'],
            $data['PhoneNo'],
            $data['Job_ID']
        );
        
        if ($stmt->execute()) {
            return [
                'success' => true,
                'new_id' => $this->conn->insert_id
            ];
        }
        return ['success' => false];
    }

    public function update($id, $data) {
        // Check for duplicates
        if ($this->isDuplicateName($data['Name'], $id)) {
            return ['success' => false, 'error' => 'Employee name already exists'];
        }
        if ($this->isDuplicateEmail($data['Email'], $id)) {
            return ['success' => false, 'error' => 'Email already exists'];
        }
        if ($this->isDuplicatePhone($data['PhoneNo'], $id)) {
            return ['success' => false, 'error' => 'Phone number already exists'];
        }

        $query = "UPDATE employee 
                  SET Name = ?, Email = ?, PhoneNo = ?, Job_ID = ? 
                  WHERE Employee_ID = ?";
        $stmt = $this->conn->prepare($query);
        $stmt->bind_param("sssii", 
            $data['Name'],
            $data['Email'],
            $data['PhoneNo'],
            $data['Job_ID'],
            $id
        );
        return $stmt->execute();
    }

    /**
     * Delete employee with admin check
     * Prevents deletion of last admin account
     */
    public function delete($id) {
        // Complex deletion logic with admin check
        // Prevents deletion of last admin account
        if ($this->isAdmin($id)) {
            if ($this->countAdmins() <= 1) {
                return ['success' => false, 'error' => 'This employee is the last admin. Please assign another admin account before deleting this employee.'];
            }
        }

        $query = "DELETE FROM employee WHERE Employee_ID = ?";
        $stmt = $this->conn->prepare($query);
        $stmt->bind_param("i", $id);
        $result = $stmt->execute();
        return ['success' => $result];
    }

    private function isAdmin($id) {
        // Check if employee has admin role through user and type tables
        $query = "SELECT COUNT(*) as count FROM user u 
                  JOIN type t ON u.Type_ID = t.Type_ID 
                  WHERE u.Employee_ID = ? AND t.Type = 'Admin'";
        $stmt = $this->conn->prepare($query);
        $stmt->bind_param("i", $id);
        $stmt->execute();
        $result = $stmt->get_result()->fetch_assoc();
        return $result['count'] > 0;
    }

    private function countAdmins() {
        $query = "SELECT COUNT(*) as count FROM user u 
                  JOIN type t ON u.Type_ID = t.Type_ID 
                  WHERE t.Type = 'Admin'";
        $result = $this->conn->query($query);
        return $result->fetch_assoc()['count'];
    }

    public function getAllJobs() {
        $query = "SELECT * FROM job ORDER BY Job_Title";
        $result = $this->conn->query($query);
        return $result->fetch_all(MYSQLI_ASSOC);
    }
}
