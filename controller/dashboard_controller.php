<?php
require_once '../config/db_connection.php';
require_once '../model/dashboard_model.php';

// Handles dashboard analytics and reporting
class DashboardController {
    private $model;

    // Initialize dashboard data model
    public function __construct($dbConnection) {
        $this->model = new Dashboard($dbConnection);
    }

    // Get sales data for specified time period
    public function getSalesTrend($period) {
        $data = $this->model->getSalesTrendData($period);
        $formattedData = array_map(function($item) use ($period) {
            return [
                'label' => $this->formatLabel($item['label'], $period),
                'total' => floatval($item['total'])
            ];
        }, $data);
        
        return $formattedData;
    }

    // Format date labels based on period type
    private function formatLabel($label, $period) {
        switch ($period) {
            case 'daily':
                return date('M d', strtotime($label));
            case 'monthly':
                return date('M Y', strtotime($label . '-01'));
            case 'yearly':
            default:
                return $label;
        }
    }

    // Get current inventory levels by category
    public function getStockStatus() {
        $data = $this->model->getStockStatusData();
        return array_map(function($item) {
            return [
                'category' => $item['category'],
                'stock' => intval($item['stock'])
            ];
        }, $data);
    }

    // Get best-selling products with revenue
    public function getTopProducts() {
        $data = $this->model->getTopProductsData();
        return array_map(function($item) {
            return [
                'Product_Name' => $item['Product_Name'],
                'total_sold' => intval($item['total_sold']),
                'total_revenue' => floatval($item['total_revenue'])
            ];
        }, $data);
    }

    // Get category performance metrics
    public function getCategoryPerformance() {
        $data = $this->model->getCategoryPerformance();
        return array_map(function($item) {
            return [
                'category' => $item['Category_Name'],
                'total_products' => intval($item['total_products']),
                'total_stock' => intval($item['total_stock']),
                'inventory_value' => floatval($item['inventory_value']),
                'units_sold' => intval($item['units_sold']),
                'total_revenue' => floatval($item['total_revenue'])
            ];
        }, $data);
    }

    // Get sales metrics with period filter
    public function getSalesMetrics($period) {
        $data = $this->model->getSalesMetrics($period);
        return [
            'total_orders' => intval($data['total_orders']),
            'unique_customers' => intval($data['unique_customers']),
            'total_items' => intval($data['total_items']),
            'total_revenue' => floatval($data['total_revenue']),
            'average_order_value' => floatval($data['average_order_value']),
            'highest_order_value' => floatval($data['highest_order_value'])
        ];
    }
}

// Handle dashboard API requests
if (isset($_GET['action'])) {
    $controller = new DashboardController($conn);
    header('Content-Type: application/json');
    
    switch ($_GET['action']) {
        case 'sales_trend':
            $period = isset($_GET['period']) ? $_GET['period'] : 'monthly';
            echo json_encode($controller->getSalesTrend($period));
            break;
            
        case 'stock_status':
            echo json_encode($controller->getStockStatus());
            break;
            
        case 'top_products':
            echo json_encode($controller->getTopProducts());
            break;
            
        case 'category_performance':
            echo json_encode($controller->getCategoryPerformance());
            break;
            
        case 'sales_metrics':
            $period = isset($_GET['period']) ? (int)$_GET['period'] : 30;
            echo json_encode($controller->getSalesMetrics($period));
            break;
    }
}
?>
