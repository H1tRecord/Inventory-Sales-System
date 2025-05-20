<?php
class User {
    private $conn;

    public function __construct($dbConnection) {
        $this->conn = $dbConnection;
    }

    public function getAll() {
        $query = "SELECT u.ID, u.Username, u.Employee_ID, t.Type, 
                        e.Name as Employee_Name 
                 FROM user u 
                 JOIN type t ON u.Type_ID = t.Type_ID
                 JOIN employee e ON u.Employee_ID = e.Employee_ID";
        $result = $this->conn->query($query);
        return $result->fetch_all(MYSQLI_ASSOC);
    }

    public function getById($id) {
        $query = "SELECT * FROM user WHERE ID = ?";
        $stmt = $this->conn->prepare($query);
        $stmt->bind_param("i", $id);
        $stmt->execute();
        return $stmt->get_result()->fetch_assoc();
    }

    public function getAvailableEmployees() {
        // Find employees without user accounts using LEFT JOIN
        // NULL check in WHERE clause finds unassigned employees
        $query = "SELECT e.Employee_ID, e.Name 
                 FROM employee e 
                 LEFT JOIN user u ON e.Employee_ID = u.Employee_ID 
                 WHERE u.ID IS NULL";
        $result = $this->conn->query($query);
        return $result->fetch_all(MYSQLI_ASSOC);
    }

    public function create($data) {
        // Check for duplicate username
        if ($this->isDuplicateUsername($data['Username'])) {
            return ['success' => false, 'error' => 'Username already exists'];
        }

        $query = "INSERT INTO user (Employee_ID, Username, Password, Type_ID) VALUES (?, ?, ?, ?)";
        $stmt = $this->conn->prepare($query);
        $stmt->bind_param("issi", 
            $data['Employee_ID'],
            $data['Username'],
            $data['Password'],
            $data['Type_ID']
        );
        
        if ($stmt->execute()) {
            return ['success' => true, 'new_id' => $this->conn->insert_id];
        }
        return ['success' => false, 'error' => 'Database error'];
    }

    public function update($id, $data) {
        if ($this->isDuplicateUsername($data['Username'], $id)) {
            return ['success' => false, 'error' => 'Username already exists'];
        }

        $query = "UPDATE user SET Username = ?, Password = ?, Type_ID = ? WHERE ID = ?";
        $stmt = $this->conn->prepare($query);
        $stmt->bind_param("ssii", 
            $data['Username'],
            $data['Password'],
            $data['Type_ID'],
            $id
        );
        return ['success' => $stmt->execute()];
    }

    public function delete($id) {
        // Prevent deletion of last admin account
        // First check user type
        $userQuery = "SELECT Type_ID FROM user WHERE ID = ?";
        $stmt = $this->conn->prepare($userQuery);
        $stmt->bind_param("i", $id);
        $stmt->execute();
        $user = $stmt->get_result()->fetch_assoc();

        if ($user['Type_ID'] == 1) { // Admin type
            // Verify not last admin before deletion
            $adminCount = $this->countAdmins();
            if ($adminCount <= 1) {
                return ['success' => false, 'error' => 'Cannot delete the last admin account'];
            }
        }

        $query = "DELETE FROM user WHERE ID = ?";
        $stmt = $this->conn->prepare($query);
        $stmt->bind_param("i", $id);
        $result = $stmt->execute();
        return ['success' => $result];
    }

    public function countAdmins() {
        $query = "SELECT COUNT(*) as count FROM user WHERE Type_ID = 1";
        $result = $this->conn->query($query);
        return $result->fetch_assoc()['count'];
    }

    private function isDuplicateUsername($username, $excludeId = null) {
        $query = "SELECT COUNT(*) as count FROM user WHERE Username = ?";
        $params = [$username];
        
        if ($excludeId) {
            $query .= " AND ID != ?";
            $params[] = $excludeId;
        }
        
        $stmt = $this->conn->prepare($query);
        $stmt->bind_param(str_repeat('s', count($params)), ...$params);
        $stmt->execute();
        return $stmt->get_result()->fetch_assoc()['count'] > 0;
    }
}
