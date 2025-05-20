<?php
require_once '../config/db_connection.php';

// Retrieve detailed transaction information by ID
if (isset($_GET['transaction_id'])) {
    $transactionId = $_GET['transaction_id'];
    
    // Check if this is a search request
    if (isset($_GET['search'])) {
        $searchTerm = "%{$_GET['search']}%";
        
        // Search within transaction details
        $query = $conn->prepare("
            SELECT COUNT(*) as match_count
            FROM transaction_details td
            WHERE td.Transaction_ID = ? 
            AND (
                td.Product_Code LIKE ? OR
                td.Product_Name LIKE ? OR
                td.Employee_Name LIKE ? OR
                td.Job_Title LIKE ? OR
                CAST(td.Quantity AS CHAR) LIKE ? OR
                CAST(td.Price AS CHAR) LIKE ?
            )
        ");
        $query->bind_param("issssss", 
            $transactionId, 
            $searchTerm, 
            $searchTerm, 
            $searchTerm, 
            $searchTerm, 
            $searchTerm, 
            $searchTerm
        );
        $query->execute();
        $result = $query->get_result()->fetch_assoc();
        
        echo json_encode(['matches' => $result['match_count'] > 0]);
        exit;
    }
    
    // Get transaction line items with calculations
    $query = $conn->prepare("
        SELECT td.ID, td.Product_Code, td.Product_Name, td.Quantity, td.Price, 
               td.Employee_Name, td.Job_Title,
               (td.Quantity * td.Price) as Subtotal
        FROM transaction_details td
        WHERE td.Transaction_ID = ?
    ");
    $query->bind_param("i", $transactionId);
    $query->execute();
    $result = $query->get_result();

    $details = [];
    $total = 0;
    while ($row = $result->fetch_assoc()) {
        $details[] = $row;
        $total += $row['Subtotal'];
    }

    // Process and return transaction details
    echo json_encode(['details' => $details, 'total' => $total]);
}
?>
