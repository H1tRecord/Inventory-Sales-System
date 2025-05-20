<?php
require_once '../include/navbar.php';
require_once '../controller/validation_controller.php';
require_once '../config/db_connection.php';

validateAccess('Admin');

// Fetch statistics
$employeeCount = $conn->query("SELECT COUNT(*) AS count FROM employee")->fetch_assoc()['count'];
$supplierCount = $conn->query("SELECT COUNT(*) AS count FROM supplier")->fetch_assoc()['count'];
$productCount = $conn->query("SELECT COUNT(*) AS count FROM product")->fetch_assoc()['count'];
$userCount = $conn->query("SELECT COUNT(*) AS count FROM user")->fetch_assoc()['count'];

// Fetch low stock products
$lowStockProducts = $conn->query("SELECT Product_Name, In_Stock FROM product WHERE In_Stock <= 10 ORDER BY In_Stock ASC")->fetch_all(MYSQLI_ASSOC);

// Add these PHP variables near the other fetch queries at the top
$outOfStockCount = 0;
$lowStockCount = 0;
foreach ($lowStockProducts as $product) {
    if ($product['In_Stock'] == 0) {
        $outOfStockCount++;
    } elseif ($product['In_Stock'] <= 10) {
        $lowStockCount++;
    }
}

// Fetch total inventory value
$totalValue = $conn->query("SELECT SUM(In_Stock * Selling_Price) as total FROM product")->fetch_assoc()['total'];

// Fetch recent transactions
$recentTransactions = $conn->query("
    SELECT t.Transaction_ID, t.Customer_Name, t.Customer_Email, t.Customer_Phone,
           t.Transaction_Date, COUNT(td.ID) as items, SUM(td.Quantity * td.Price) as total
    FROM transaction t
    JOIN transaction_details td ON t.Transaction_ID = td.Transaction_ID
    GROUP BY t.Transaction_ID
    ORDER BY t.Transaction_Date DESC
    LIMIT 5
")->fetch_all(MYSQLI_ASSOC);
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Inventory Dashboard</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons/font/bootstrap-icons.css" rel="stylesheet">
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha3/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <style>
.card {
    transition: transform 0.2s;
    border-radius: 0.5rem;
}
.card:hover {
    transform: translateY(-5px);
}
.chart-container {
    position: relative;
    margin: auto;
    height: 300px;
    padding: 1rem;
}
.bg-gradient-primary {
    background: linear-gradient(to right, #1a237e, #283593);
}
.alerts-container {
    height: 300px;
    overflow-y: auto;
    scrollbar-width: thin;
}
.alerts-container::-webkit-scrollbar {
    width: 6px;
}
.alerts-container::-webkit-scrollbar-track {
    background: #f1f1f1;
}
.alerts-container::-webkit-scrollbar-thumb {
    background: #888;
    border-radius: 3px;
}
.alerts-container::-webkit-scrollbar-thumb:hover {
    background: #555;
}
    </style>
</head>
<body class="bg-light">
    <div class="d-flex">
        <div class="flex-shrink-0" style="width: 250px;">
            <?php require_once '../include/sidebar.php'; ?>
        </div>
        <div class="flex-grow-1 p-4">
            <div class="d-flex justify-content-between align-items-center mb-4">
                <h1 class="fs-3 fw-bold text-primary">Inventory Dashboard</h1>
                <div class="text-end">
                    <h5 class="mb-1">Total Inventory Value</h5>
                    <h3 class="text-success mb-0">₱<?= number_format($totalValue, 2) ?></h3>
                </div>
            </div>

            <!-- Metrics Cards -->
            <div class="row g-4 mb-4">
                <div class="col-md-3">
                    <div class="card metric-card h-100 border-0 shadow-sm">
                        <div class="card-body">
                            <div class="d-flex align-items-center">
                                <div class="flex-shrink-0">
                                    <div class="rounded-circle bg-primary bg-opacity-10 p-3">
                                        <i class="bi bi-box-seam text-primary fs-4"></i>
                                    </div>
                                </div>
                                <div class="flex-grow-1 ms-3">
                                    <h6 class="mb-1">Total Products</h6>
                                    <h3 class="mb-0"><?= $productCount ?></h3>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="card metric-card h-100 border-0 shadow-sm">
                        <div class="card-body">
                            <div class="d-flex align-items-center">
                                <div class="flex-shrink-0">
                                    <div class="rounded-circle bg-success bg-opacity-10 p-3">
                                        <i class="bi bi-truck text-success fs-4"></i>
                                    </div>
                                </div>
                                <div class="flex-grow-1 ms-3">
                                    <h6 class="mb-1">Total Suppliers</h6>
                                    <h3 class="mb-0"><?= $supplierCount ?></h3>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="card metric-card h-100 border-0 shadow-sm">
                        <div class="card-body">
                            <div class="d-flex align-items-center">
                                <div class="flex-shrink-0">
                                    <div class="rounded-circle bg-warning bg-opacity-10 p-3">
                                        <i class="bi bi-person-circle text-warning fs-4"></i>
                                    </div>
                                </div>
                                <div class="flex-grow-1 ms-3">
                                    <h6 class="mb-1">Total Employees</h6>
                                    <h3 class="mb-0"><?= $employeeCount ?></h3>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="card metric-card h-100 border-0 shadow-sm">
                        <div class="card-body">
                            <div class="d-flex align-items-center">
                                <div class="flex-shrink-0">
                                    <div class="rounded-circle bg-danger bg-opacity-10 p-3">
                                        <i class="bi bi-person-lines-fill text-danger fs-4"></i>
                                    </div>
                                </div>
                                <div class="flex-grow-1 ms-3">
                                    <h6 class="mb-1">Total Users</h6>
                                    <h3 class="mb-0"><?= $userCount ?></h3>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="row g-4">
                <!-- Stock Status by Category -->
                <div class="col-md-8 mb-4">
                    <div class="card border-0 shadow-sm">
                        <div class="card-header bg-white py-3 border-0">
                            <h5 class="card-title mb-0">Stock Status by Category</h5>
                        </div>
                        <div class="card-body">
                            <div class="chart-container">
                                <canvas id="stockChart"></canvas>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Low Stock Alerts -->
                <div class="col-md-4 mb-4">
                    <div class="card border-0 shadow-sm h-100">
                        <div class="card-header bg-white py-3 d-flex justify-content-between align-items-center">
                            <h5 class="card-title mb-0">Low Stock Alerts</h5>
                            <div>
                                <?php if ($lowStockCount > 0): ?>
                                    <span class="badge bg-warning text-dark me-2">
                                        Low: <?= $lowStockCount ?>
                                    </span>
                                <?php endif; ?>
                                <?php if ($outOfStockCount > 0): ?>
                                    <span class="badge bg-danger">
                                        Out: <?= $outOfStockCount ?>
                                    </span>
                                <?php endif; ?>
                            </div>
                        </div>
                        <div class="card-body p-0">
                            <?php if (empty($lowStockProducts)): ?>
                                <div class="text-center text-muted py-4">
                                    <i class="bi bi-inbox fs-1"></i>
                                    <p class="mt-2">No low stock items found</p>
                                </div>
                            <?php else: ?>
                            <div class="alerts-container">
                                <div class="list-group list-group-flush">
                                    <?php foreach ($lowStockProducts as $product): ?>
                                    <div class="list-group-item d-flex justify-content-between align-items-center <?= $product['In_Stock'] == 0 ? 'bg-danger text-white' : ($product['In_Stock'] <= 5 ? 'bg-warning' : '') ?>">
                                        <div>
                                            <h6 class="mb-0"><?= $product['Product_Name'] ?></h6>
                                            <small class="<?= $product['In_Stock'] == 0 ? 'text-white' : 'text-muted' ?>">
                                                <?= $product['In_Stock'] == 0 ? 'OUT OF STOCK' : 'Low Stock' ?>
                                            </small>
                                        </div>
                                        <span class="badge <?= $product['In_Stock'] == 0 ? 'bg-white text-danger' : 'bg-danger' ?> rounded-pill">
                                            <?= $product['In_Stock'] ?>
                                        </span>
                                    </div>
                                    <?php endforeach; ?>
                                </div>
                            </div>
                            <?php endif; ?>
                        </div>
                    </div>
                </div>

                <!-- Top Products Chart -->
                <div class="col-md-6 mb-4">
                    <div class="card border-0 shadow-sm h-100">
                        <div class="card-header bg-white py-3">
                            <h5 class="card-title mb-0">Top Selling Products</h5>
                        </div>
                        <div class="card-body">
                            <div class="chart-container">
                                <canvas id="topProductsChart"></canvas>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Sales Trend Chart -->
                <div class="col-md-6 mb-4">
                    <div class="card border-0 shadow-sm h-100">
                        <div class="card-header bg-white py-3 d-flex justify-content-between align-items-center">
                            <h5 class="card-title mb-0">Sales Trend</h5>
                            <div class="btn-group">
                                <button type="button" class="btn btn-outline-primary btn-sm" data-period="daily">Daily</button>
                                <button type="button" class="btn btn-outline-primary btn-sm active" data-period="monthly">Monthly</button>
                                <button type="button" class="btn btn-outline-primary btn-sm" data-period="yearly">Yearly</button>
                            </div>
                        </div>
                        <div class="card-body">
                            <div class="chart-container">
                                <canvas id="salesChart"></canvas>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Recent Transactions -->
                <div class="col-12">
                    <div class="card border-0 shadow-sm">
                        <div class="card-header bg-white py-3">
                            <h5 class="card-title mb-0">Recent Transactions</h5>
                        </div>
                        <div class="card-body">
                            <?php if (empty($recentTransactions)): ?>
                                <div class="text-center text-muted py-4">
                                    <i class="bi bi-receipt fs-1"></i>
                                    <p class="mt-2">No recent transactions found</p>
                                </div>
                            <?php else: ?>
                            <div class="table-responsive">
                                <table class="table table-hover">
                                    <thead>
                                        <tr>
                                            <th>ID</th>
                                            <th>Customer</th>
                                            <th>Contact</th>
                                            <th>Date</th>
                                            <th>Items</th>
                                            <th>Total</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php foreach ($recentTransactions as $transaction): ?>
                                        <tr>
                                            <td>#<?= $transaction['Transaction_ID'] ?></td>
                                            <td><?= $transaction['Customer_Name'] ?></td>
                                            <td>
                                                <?php if ($transaction['Customer_Email'] || $transaction['Customer_Phone']): ?>
                                                    <small class="d-block text-muted">
                                                        <?= $transaction['Customer_Email'] ? '<i class="bi bi-envelope-fill me-1"></i>' . $transaction['Customer_Email'] : '' ?>
                                                    </small>
                                                    <small class="d-block text-muted">
                                                        <?= $transaction['Customer_Phone'] ? '<i class="bi bi-telephone-fill me-1"></i>' . $transaction['Customer_Phone'] : '' ?>
                                                    </small>
                                                <?php else: ?>
                                                    <small class="text-muted">No contact info</small>
                                                <?php endif; ?>
                                            </td>
                                            <td><?= date('M d, Y', strtotime($transaction['Transaction_Date'])) ?></td>
                                            <td><?= $transaction['items'] ?></td>
                                            <td>₱<?= number_format($transaction['total'], 2) ?></td>
                                        </tr>
                                        <?php endforeach; ?>
                                    </tbody>
                                </table>
                            </div>
                            <?php endif; ?>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script>
    // Chart color configuration
    const chartColors = {
        background: [
            'rgba(255, 99, 132, 0.7)',
            'rgba(54, 162, 235, 0.7)',
            'rgba(255, 206, 86, 0.7)',
            'rgba(75, 192, 192, 0.7)',
            'rgba(153, 102, 255, 0.7)',
            'rgba(255, 159, 64, 0.7)',
            'rgba(231, 233, 237, 0.7)',
            'rgba(141, 195, 65, 0.7)'
        ],
        border: [
            'rgba(255, 99, 132, 1)',
            'rgba(54, 162, 235, 1)',
            'rgba(255, 206, 86, 1)',
            'rgba(75, 192, 192, 1)',
            'rgba(153, 102, 255, 1)',
            'rgba(255, 159, 64, 1)',
            'rgba(231, 233, 237, 1)',
            'rgba(141, 195, 65, 1)'
        ]
    };

    // Initialize all dashboard charts
    function initCharts() {
        // Stock Status Chart initialization
        fetch('../controller/dashboard_controller.php?action=stock_status')
            .then(response => response.json())
            .then(data => {
                if (data.length === 0) {
                    showNoDataMessage('stockChart', 'No Stock Data');
                    return;
                }

                new Chart(document.getElementById('stockChart'), {
                    type: 'doughnut',
                    data: {
                        labels: data.map(item => item.category),
                        datasets: [{
                            data: data.map(item => item.stock),
                            backgroundColor: chartColors.background,
                            borderColor: chartColors.border,
                            borderWidth: 1
                        }]
                    },
                    options: {
                        responsive: true,
                        maintainAspectRatio: false,
                        plugins: {
                            legend: {
                                display: true,
                                position: 'right'
                            }
                        }
                    }
                });
            });

        // Top Products Chart initialization
        fetch('../controller/dashboard_controller.php?action=top_products')
            .then(response => response.json())
            .then(data => {
                if (data.length === 0) {
                    showNoDataMessage('topProductsChart', 'No Top Products');
                    return;
                }

                const ctx = document.getElementById('topProductsChart').getContext('2d');
                
                new Chart(ctx, {
                    type: 'bar',
                    data: {
                        labels: data.map(item => item.Product_Name),
                        datasets: [{
                            label: 'Units Sold',
                            data: data.map(item => item.total_sold),
                            backgroundColor: chartColors.background[0],
                            borderColor: chartColors.border[0],
                            borderWidth: 1,
                            order: 1
                        }, {
                            label: 'Revenue',
                            data: data.map(item => item.total_revenue),
                            backgroundColor: 'rgba(0,0,0,0)', // transparent
                            borderColor: chartColors.border[2],
                            borderWidth: 2,
                            type: 'line',
                            order: 0,
                            yAxisID: 'y1'
                        }]
                    },
                    options: {
                        responsive: true,
                        maintainAspectRatio: false,
                        plugins: {
                            legend: {
                                display: true,
                                position: 'top'
                            },
                            tooltip: {
                                callbacks: {
                                    label: function(context) {
                                        if (context.dataset.label === 'Revenue') {
                                            return `Revenue: ₱${context.raw.toFixed(2)}`;
                                        }
                                        return `Units Sold: ${context.raw}`;
                                    }
                                }
                            }
                        },
                        scales: {
                            y: {
                                beginAtZero: true,
                                position: 'left',
                                title: {
                                    display: true,
                                    text: 'Units Sold'
                                }
                            },
                            y1: {
                                beginAtZero: true,
                                position: 'right',
                                title: {
                                    display: true,
                                    text: 'Revenue (₱)'
                                },
                                grid: {
                                    drawOnChartArea: false
                                }
                            }
                        }
                    }
                });
            })
            .catch(error => console.error('Error loading chart:', error));
    }

    // Sales trend chart management
    let salesChart = null;

    function updateSalesChart(period) {
        // Update sales chart based on selected period
        fetch(`../controller/dashboard_controller.php?action=sales_trend&period=${period}`)
            .then(response => response.json())
            .then(data => {
                if (data.length === 0) {
                    showNoDataMessage('salesChart', 'No Sales Data');
                    return;
                }
                if (salesChart) {
                    salesChart.destroy();
                }

                salesChart = new Chart(document.getElementById('salesChart'), {
                    type: 'line',
                    data: {
                        labels: data.map(item => item.label),
                        datasets: [{
                            label: 'Sales',
                            data: data.map(item => item.total),
                            borderColor: chartColors.border[1],
                            backgroundColor: chartColors.background[1],
                            fill: true,
                            tension: 0.4
                        }]
                    },
                    options: {
                        responsive: true,
                        maintainAspectRatio: false,
                        plugins: {
                            legend: {
                                display: true
                            },
                            tooltip: {
                                callbacks: {
                                    label: function(context) {
                                        return `Sales: ₱${context.parsed.y.toFixed(2)}`;
                                    }
                                }
                            }
                        },
                        scales: {
                            y: {
                                beginAtZero: true,
                                ticks: {
                                    callback: function(value) {
                                        return '₱' + value.toFixed(2);
                                    }
                                }
                            }
                        }
                    }
                });
            });
    }

    // No data message display handler
    function showNoDataMessage(canvasId, message) {
        const canvas = document.getElementById(canvasId);
        const ctx = canvas.getContext('2d');
        
        // Clear the canvas
        ctx.clearRect(0, 0, canvas.width, canvas.height);
        
        // Draw icon
        ctx.textAlign = 'center';
        ctx.textBaseline = 'middle';
        ctx.font = '24px bootstrap-icons';
        ctx.fillStyle = '#6c757d';
        
        let icon = '';
        let specificMessage = '';
        
        // Set specific icons and messages for each chart type
        switch(canvasId) {
            case 'stockChart':
                icon = '\uf4e3'; // box icon
                specificMessage = 'No stock information available.\nAdd products to view stock status.';
                break;
            case 'salesChart':
                icon = '\uf546'; // graph icon
                specificMessage = 'No sales data available.\nComplete transactions to view sales trends.';
                break;
            case 'topProductsChart':
                icon = '\uf5d0'; // trophy icon
                specificMessage = 'No product sales data available.\nProducts with sales will appear here.';
                break;
        }
        
        // Draw icon
        ctx.fillText(icon, canvas.width / 2, (canvas.height / 2) - 30);
        
        // Draw messages
        ctx.font = 'bold 14px Arial';
        ctx.fillText(message, canvas.width / 2, (canvas.height / 2) + 10);
        
        // Draw specific message
        ctx.font = '12px Arial';
        const lines = specificMessage.split('\n');
        lines.forEach((line, i) => {
            ctx.fillText(line, canvas.width / 2, (canvas.height / 2) + 35 + (i * 20));
        });
    }

    // Initialize dashboard on load
    document.addEventListener('DOMContentLoaded', function() {
        // Initialize all charts
        initCharts();
        
        // Initialize sales chart with monthly data
        updateSalesChart('monthly');

        // Period button click handlers
        document.querySelectorAll('[data-period]').forEach(button => {
            button.addEventListener('click', function() {
                // Update active state
                document.querySelectorAll('[data-period]').forEach(btn => {
                    btn.classList.remove('active');
                });
                this.classList.add('active');

                // Update chart
                updateSalesChart(this.dataset.period);
            });
        });
    });
    </script>
</body>
</html>