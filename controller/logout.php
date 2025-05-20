<?php
// Start session if not already started
session_start();
// Clear all session data
session_destroy();
// Redirect to login page
header("Location: ../page/login.php");
exit();
