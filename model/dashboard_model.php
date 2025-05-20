<?php
/**
 * Dashboard Model Class
 * Provides analytics and reporting data
 */
class Dashboard {
    private $conn;

    public function __construct($dbConnection) {
        $this->conn = $dbConnection;
    }

    /**
     * Get sales trend data for specified period
     * Supports daily, monthly, and yearly aggregation
     */
    public function getSalesTrendData($period) {
        switch ($period) {
            case 'daily':
                // Last 30 days sales aggregated by day
                // Using DATE() to group by full date
                // DATE_SUB for date range calculation
                $query = "
                    SELECT 
                        DATE(t.Transaction_Date) as label,
                        SUM(td.Quantity * td.Price) as total
                    FROM transaction t
                    JOIN transaction_details td ON t.Transaction_ID = td.Transaction_ID
                    WHERE t.Transaction_Date >= DATE_SUB(NOW(), INTERVAL 30 DAY)
                    GROUP BY DATE(t.Transaction_Date)
                    ORDER BY label";
                break;

            case 'monthly':
                // Last 12 months sales using DATE_FORMAT
                // %Y-%m formats date as YYYY-MM for monthly grouping
                $query = "
                    SELECT 
                        DATE_FORMAT(t.Transaction_Date, '%Y-%m') as label,
                        SUM(td.Quantity * td.Price) as total
                    FROM transaction t
                    JOIN transaction_details td ON t.Transaction_ID = td.Transaction_ID
                    WHERE t.Transaction_Date >= DATE_SUB(NOW(), INTERVAL 12 MONTH)
                    GROUP BY DATE_FORMAT(t.Transaction_Date, '%Y-%m')
                    ORDER BY label";
                break;

            case 'yearly':
                $query = "
                    SELECT 
                        YEAR(t.Transaction_Date) as label,
                        SUM(td.Quantity * td.Price) as total
                    FROM transaction t
                    JOIN transaction_details td ON t.Transaction_ID = td.Transaction_ID
                    GROUP BY YEAR(t.Transaction_Date)
                    ORDER BY label";
                break;
        }

        $result = $this->conn->query($query);
        return $result->fetch_all(MYSQLI_ASSOC);
    }

    /**
     * Get current inventory levels by category
     * Returns total stock count per category
     */
    public function getStockStatusData() {
        $query = "
            SELECT 
                c.Category_Name as category,
                SUM(p.In_Stock) as stock
            FROM product p
            JOIN category c ON p.Category_ID = c.Category_ID
            GROUP BY c.Category_ID, c.Category_Name
            ORDER BY stock DESC";
        
        $result = $this->conn->query($query);
        return $result->fetch_all(MYSQLI_ASSOC);
    }

    /**
     * Get top 5 selling products in last 30 days
     * Returns product names, quantities sold, and revenue
     */
    public function getTopProductsData() {
        $query = "
            SELECT 
                td.Product_Name,
                SUM(td.Quantity) as total_sold,
                SUM(td.Quantity * td.Price) as total_revenue
            FROM transaction_details td
            JOIN transaction t ON t.Transaction_ID = td.Transaction_ID
            WHERE t.Transaction_Date >= DATE_SUB(NOW(), INTERVAL 30 DAY)
            GROUP BY td.Product_Name
            ORDER BY total_sold DESC
            LIMIT 5";
        
        $result = $this->conn->query($query);
        return $result->fetch_all(MYSQLI_ASSOC);
    }

    /**
     * Get category performance metrics
     */
    public function getCategoryPerformance() {
        $query = "
            SELECT 
                c.Category_Name,
                COUNT(DISTINCT p.Product_ID) as total_products,
                SUM(p.In_Stock) as total_stock,
                SUM(p.In_Stock * p.Selling_Price) as inventory_value,
                COALESCE(SUM(td.Quantity), 0) as units_sold,
                COALESCE(SUM(td.Quantity * td.Price), 0) as total_revenue
            FROM category c
            LEFT JOIN product p ON c.Category_ID = p.Category_ID
            LEFT JOIN transaction_details td ON p.Product_Code = td.Product_Code
            GROUP BY c.Category_ID, c.Category_Name
            ORDER BY total_revenue DESC";
        
        $result = $this->conn->query($query);
        return $result->fetch_all(MYSQLI_ASSOC);
    }

    /**
     * Get sales performance metrics
     */
    public function getSalesMetrics($period) {
        $query = "
            SELECT 
                COUNT(DISTINCT t.Transaction_ID) as total_orders,
                COUNT(DISTINCT t.Customer_Name) as unique_customers,
                SUM(td.Quantity) as total_items,
                SUM(td.Quantity * td.Price) as total_revenue,
                AVG(sub.order_total) as average_order_value,
                MAX(sub.order_total) as highest_order_value
            FROM transaction t
            JOIN transaction_details td ON t.Transaction_ID = td.Transaction_ID
            JOIN (
                SELECT 
                    Transaction_ID, 
                    SUM(Quantity * Price) as order_total
                FROM transaction_details 
                GROUP BY Transaction_ID
            ) sub ON t.Transaction_ID = sub.Transaction_ID
            WHERE t.Transaction_Date >= DATE_SUB(NOW(), INTERVAL ? DAY)";
        
        $stmt = $this->conn->prepare($query);
        $stmt->bind_param('i', $period);
        $stmt->execute();
        return $stmt->get_result()->fetch_assoc();
    }
}
