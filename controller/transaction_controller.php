<?php
require_once '../config/db_connection.php';

/**
 * Transaction Controller Class
 * Manages sales transactions and inventory updates
 */
class TransactionController {
    private $conn;

    // Initialize with database connection
    public function __construct($dbConnection) {
        $this->conn = $dbConnection;
    }

    /**
     * Process new sale transaction
     * - Validates stock levels
     * - Creates transaction record
     * - Records line items
     * - Updates inventory
     * Uses transaction to ensure data consistency
     */
    public function createTransaction($data) {
        try {
            // Validate required fields
            if (!isset($data['customerName']) || !isset($data['items'])) {
                throw new Exception('Invalid transaction data');
            }

            // Get employee details for transaction record
            $employeeQuery = "SELECT e.Name as Employee_Name, j.Job_Title 
                            FROM employee e 
                            JOIN job j ON e.Job_ID = j.Job_ID 
                            WHERE e.Employee_ID = ?";
            $empStmt = $this->conn->prepare($employeeQuery);
            $empStmt->bind_param("i", $data['employeeId']);
            $empStmt->execute();
            $employeeInfo = $empStmt->get_result()->fetch_assoc();

            if (!$employeeInfo) {
                throw new Exception('Employee information not found');
            }

            // Start transaction for data consistency
            $this->conn->begin_transaction();

            // Create main transaction record
            $query = "INSERT INTO transaction (Customer_Name, Customer_Email, Customer_Phone, No_of_Items_Bought, Transaction_Date) 
                     VALUES (?, ?, ?, ?, NOW())";
            $stmt = $this->conn->prepare($query);
            $stmt->bind_param("sssi", 
                $data['customerName'],
                $data['customerEmail'],
                $data['customerPhone'],
                $data['totalItems']
            );

            if (!$stmt->execute()) {
                throw new Exception('Error creating transaction: ' . $stmt->error);
            }
            
            $transactionId = $this->conn->insert_id;

            // Update the details query to use Product_Code
            $detailsQuery = "INSERT INTO transaction_details 
                            (Transaction_ID, Product_Code, Product_Name, Quantity, Price, Employee_Name, Job_Title) 
                            VALUES (?, ?, ?, ?, ?, ?, ?)";
            $detailsStmt = $this->conn->prepare($detailsQuery);

            // Process each item in transaction
            foreach ($data['items'] as $item) {
                // Check stock availability
                $stockCheck = "SELECT In_Stock, Product_Name, Product_Code FROM product WHERE Product_ID = ?";
                $checkStmt = $this->conn->prepare($stockCheck);
                $checkStmt->bind_param("i", $item['Product_ID']);
                $checkStmt->execute();
                $result = $checkStmt->get_result();
                $productInfo = $result->fetch_assoc();

                if ($productInfo['In_Stock'] < $item['quantity']) {
                    throw new Exception("Insufficient stock for product: {$productInfo['Product_Name']}");
                }

                $detailsStmt->bind_param("sssidss",
                    $transactionId,
                    $productInfo['Product_Code'],
                    $productInfo['Product_Name'],
                    $item['quantity'],
                    $item['Selling_Price'],
                    $employeeInfo['Employee_Name'],
                    $employeeInfo['Job_Title']
                );

                if (!$detailsStmt->execute()) {
                    throw new Exception('Error creating transaction details: ' . $detailsStmt->error);
                }

                // Update inventory
                $updateStock = "UPDATE product SET In_Stock = In_Stock - ? WHERE Product_ID = ?";
                $updateStmt = $this->conn->prepare($updateStock);
                $updateStmt->bind_param("ii", $item['quantity'], $item['Product_ID']);
                
                if (!$updateStmt->execute()) {
                    throw new Exception('Error updating stock: ' . $updateStmt->error);
                }
            }

            $this->conn->commit();
            return ['success' => true, 'transaction_id' => $transactionId];

        } catch (Exception $e) {
            // Rollback on any error
            $this->conn->rollback();
            error_log("Transaction Error: " . $e->getMessage());
            return ['success' => false, 'error' => $e->getMessage()];
        }
    }
}

// JSON API endpoint handler
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    header('Content-Type: application/json');
    
    try {
        $rawData = file_get_contents('php://input');
        $data = json_decode($rawData, true);
        
        if (json_last_error() !== JSON_ERROR_NONE) {
            throw new Exception('Invalid JSON data');
        }

        $controller = new TransactionController($conn);

        switch ($data['action']) {
            case 'create':
                echo json_encode($controller->createTransaction($data));
                break;
            default:
                throw new Exception('Invalid action');
        }
        
    } catch (Exception $e) {
        echo json_encode([
            'success' => false,
            'error' => $e->getMessage()
        ]);
    }
}
