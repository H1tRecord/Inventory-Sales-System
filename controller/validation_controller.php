<?php
// Initialize session if not started
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Validates user access based on role(s)
// Accepts single role string or array of roles
function validateAccess($requiredRole) {
    // Redirect to login if no session exists
    if (!isset($_SESSION['user_role'])) {
        header("Location: ../page/login.php");
        exit();
    }

    // Handle multiple role validation
    if (is_array($requiredRole)) {
        if (!in_array($_SESSION['user_role'], $requiredRole)) {
            header("Location: ../page/login.php");
            exit();
        }
    } else {
        // Single role validation
        if ($_SESSION['user_role'] !== $requiredRole) {
            header("Location: ../page/login.php");
            exit();
        }
    }
}
?>
