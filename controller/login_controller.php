<?php
require_once '../config/db_connection.php';

// Handles user authentication and session management
class LoginController {
    private $conn;

    // Initialize database connection
    public function __construct($dbConnection) {
        $this->conn = $dbConnection;
    }

    // Authenticate user and create session
    public function login($username, $password) {
        // Query user data including employee and role information
        $sql = "SELECT u.Username, u.Password, t.Type, e.Employee_ID, e.Job_ID 
                FROM user u 
                JOIN type t ON u.Type_ID = t.Type_ID 
                JOIN employee e ON u.Employee_ID = e.Employee_ID 
                WHERE u.Username = ? AND u.Password = ?";
        $stmt = $this->conn->prepare($sql);
        $stmt->bind_param("ss", $username, $password);
        $stmt->execute();
        $result = $stmt->get_result();

        if ($result->num_rows == 1) {
            $row = $result->fetch_assoc();
            session_start();
            $_SESSION['username'] = $row['Username'];
            $_SESSION['user_role'] = $row['Type'];
            $_SESSION['employee_id'] = $row['Employee_ID'];
            $_SESSION['job_id'] = $row['Job_ID'];

            if ($row['Type'] == 'Admin') {
                header("Location: ../page/inventory_dashboard.php");
            } elseif ($row['Type'] == 'User') {
                header("Location: ../page/cashier_dashboard.php");
            }
            exit();
        } else {
            return "Invalid username or password.";
        }
    }
}

// Process login form submission
$error = null;
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $username = $_POST['username'];
    $password = $_POST['password'];

    $loginController = new LoginController($conn);
    $error = $loginController->login($username, $password);
}
?>
