<?php
require_once '../include/navbar.php';
require_once '../include/sidebar.php';
require_once '../controller/validation_controller.php';
require_once '../config/db_connection.php';

validateAccess('Admin');

$transactions = $conn->query("SELECT * FROM transaction ORDER BY Transaction_Date DESC");

?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Transaction Management</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons/font/bootstrap-icons.css" rel="stylesheet">
    <link href="https://cdn.datatables.net/1.13.7/css/dataTables.bootstrap5.min.css" rel="stylesheet">
    <script src="https://code.jquery.com/jquery-3.7.0.js"></script>
    <script src="https://cdn.datatables.net/1.13.7/js/jquery.dataTables.min.js"></script>
    <script src="https://cdn.datatables.net/1.13.7/js/dataTables.bootstrap5.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha3/dist/js/bootstrap.bundle.min.js"></script>
</head>
<body class="bg-light">
    <div class="d-flex">
        <div class="flex-shrink-0" style="width: 250px;">
            <?php require_once '../include/sidebar.php'; ?>
        </div>
        <div class="flex-grow-1 p-4"> 
            <!-- Transactions Table -->
            <div class="d-flex justify-content-between align-items-center mb-4">
                <h1 class="fs-3 fw-bold text-primary">Transactions</h1>
            </div>
            <table id="transactionTable" class="table table-bordered table-striped table-hover">
                <thead class="table-dark">
                    <tr>
                        <th>Transaction ID</th>
                        <th>Customer Name</th>
                        <th>Contact Info</th>
                        <th>No. of Items</th>
                        <th>Transaction Date</th>
                        <th>Action</th>
                    </tr>
                </thead>
                <tbody>
                    <?php 
                    if (!$transactions || $transactions->num_rows === 0): 
                    ?>
                    <tr>
                        <td colspan="6" class="text-center py-4">
                            <div class="text-muted">
                                <i class="bi bi-receipt fs-1 mb-3 d-block"></i>
                                <h5>No Transactions Found</h5>
                                <p>No transactions have been recorded yet.</p>
                            </div>
                        </td>
                    </tr>
                    <?php else: ?>
                        <?php while ($row = $transactions->fetch_assoc()): ?>
                        <tr>
                            <td><?php echo $row['Transaction_ID']; ?></td>
                            <td><?php echo $row['Customer_Name']; ?></td>
                            <td>
                                <?php if ($row['Customer_Email'] || $row['Customer_Phone']): ?>
                                    <?php echo $row['Customer_Email'] ? 'Email: ' . $row['Customer_Email'] . '<br>' : ''; ?>
                                    <?php echo $row['Customer_Phone'] ? 'Phone: ' . $row['Customer_Phone'] : ''; ?>
                                <?php else: ?>
                                    <span class="text-muted">No contact info</span>
                                <?php endif; ?>
                            </td>
                            <td><?php echo $row['No_of_Items_Bought']; ?></td>
                            <td><?php echo $row['Transaction_Date']; ?></td>
                            <td>
                                <button class='btn btn-primary btn-sm' data-bs-toggle='modal' data-bs-target='#transactionModal' data-transaction-id='<?php echo $row['Transaction_ID']; ?>'>
                                    View More
                                </button>
                            </td>
                        </tr>
                        <?php endwhile; ?>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>

    <!-- Transaction Details Modal -->
    <div class="modal fade" id="transactionModal" tabindex="-1" aria-labelledby="transactionModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header bg-primary text-white">
                    <h5 class="modal-title" id="transactionModalLabel">
                        <i class="bi bi-receipt me-2"></i>Transaction Details
                    </h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body p-4">
                    <div class="row mb-4">
                        <div class="col-md-6">
                            <div class="customer-info p-3 bg-light rounded">
                                <h6 class="fw-bold mb-2">Customer Information</h6>
                                <p class="mb-1" id="customerName"></p>
                                <p class="mb-1" id="customerEmail"></p>
                                <p class="mb-1" id="customerPhone"></p>
                                <p class="mb-1" id="transactionDate"></p>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="employee-info p-3 bg-light rounded">
                                <h6 class="fw-bold mb-2">Staff Information</h6>
                                <p class="mb-1" id="employeeName"></p>
                                <p class="mb-1" id="employeeJob"></p>
                                <p class="mb-1">Transaction <span id="transactionNumber"></span></p>
                            </div>
                        </div>
                    </div>

                    <div class="table-responsive">
                        <table class="table table-bordered table-hover">
                            <thead class="table-dark">
                                <tr>
                                    <th>Product Code</th>
                                    <th>Product</th>
                                    <th class="text-center">Quantity</th>
                                    <th class="text-end">Unit Price</th>
                                    <th class="text-end">Subtotal</th>
                                </tr>
                            </thead>
                            <tbody id="transactionDetailsBody">
                                <!-- Transaction details loaded via JavaScript -->
                            </tbody>
                            <tfoot class="table-light">
                                <tr>
                                    <td colspan="4" class="text-end fw-bold">Total Amount:</td>
                                    <td id="totalPrice" class="text-end fw-bold text-primary"></td>
                                </tr>
                            </tfoot>
                        </table>
                    </div>
                </div>
                <div class="modal-footer bg-light">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                </div>
            </div>
        </div>
    </div>

    <script>
        // Initialize DataTable with custom search
        $(document).ready(function() {
            if ($('#transactionTable tbody tr').length > 1) {
                $('#transactionTable').DataTable({
                    "order": [[0, "desc"]],
                    "columnDefs": [
                        { "orderable": false, "targets": 5 }
                    ],
                    // Add custom search functionality
                    "initComplete": function() {
                        var api = this.api();

                        // Custom search handler
                        $('#transactionTable_filter input').off().on('keyup', function() {
                            var searchTerm = $(this).val();
                            searchTransactions(api, searchTerm);
                        });
                    }
                });
            }
        });

        // Custom search function for transactions
        function searchTransactions(api, searchTerm) {
            api.rows().every(function() {
                const rowNode = this.node();
                const transactionId = this.data()[0];

                // First check visible row data
                const rowText = $(rowNode).text().toLowerCase();
                let matches = rowText.includes(searchTerm.toLowerCase());

                if (!matches && searchTerm.length > 0) {
                    // If no match in visible data, check transaction details
                    fetch(`../controller/get_transaction_details.php?transaction_id=${transactionId}&search=${searchTerm}`)
                        .then(response => response.json())
                        .then(data => {
                            if (data.matches) {
                                $(rowNode).show();
                            } else {
                                $(rowNode).hide();
                            }
                        });
                } else {
                    $(this.node()).toggle(matches);
                }
            });
        }

        // Bootstrap modal instance and transaction details container
        const modal = new bootstrap.Modal(document.getElementById('transactionModal'));
        const transactionDetailsBody = document.getElementById('transactionDetailsBody');

        // Handle "View More" button clicks 
        document.querySelectorAll('[data-bs-toggle="modal"]').forEach(button => {
            button.addEventListener('click', function() {
                // Extract transaction data from row
                const transactionId = this.getAttribute('data-transaction-id');
                const row = this.closest('tr');
                
                // Parse customer info
                const customerName = row.cells[1].textContent.trim();
                const contactInfo = row.cells[2].textContent.trim();
                
                // Extract contact details
                const email = contactInfo.includes('Email:') ? 
                    contactInfo.split('Email:')[1].split('Phone:')[0].trim() : '';
                const phone = contactInfo.includes('Phone:') ? 
                    contactInfo.split('Phone:')[1].trim() : '';

                // Update modal with customer info
                document.getElementById('customerName').innerHTML = `<i class="bi bi-person-fill me-2"></i>${customerName}`;
                document.getElementById('customerEmail').innerHTML = email ? 
                    `<i class="bi bi-envelope-fill me-2"></i>${email}` : '';
                document.getElementById('customerPhone').innerHTML = phone ? 
                    `<i class="bi bi-telephone-fill me-2"></i>${phone}` : '';
                document.getElementById('transactionDate').innerHTML = 
                    `<i class="bi bi-calendar-fill me-2"></i>${row.cells[4].textContent}`;
                document.getElementById('transactionNumber').innerHTML = `#${row.cells[0].textContent}`;

                // Fetch and display transaction details
                fetch(`../controller/get_transaction_details.php?transaction_id=${transactionId}`)
                    .then(response => response.json())
                    .then(data => {
                        // Render transaction details
                        transactionDetailsBody.innerHTML = '';
                        if (data.details.length > 0) {
                            document.getElementById('employeeName').innerHTML = `<i class="bi bi-person-badge-fill me-2"></i>${data.details[0].Employee_Name}`;
                            document.getElementById('employeeJob').innerHTML = `<i class="bi bi-briefcase-fill me-2"></i>${data.details[0].Job_Title}`;
                        }
                        
                        // Calculate and display transaction details
                        data.details.forEach(detail => {
                            const subtotal = detail.Quantity * detail.Price;
                            transactionDetailsBody.innerHTML += `
                                <tr>
                                    <td><div class="fw-bold">${detail.Product_Code}</div></td>
                                    <td>
                                        <div class="fw-bold">${detail.Product_Name}</div>
                                    </td>
                                    <td class="text-center">${detail.Quantity}</td>
                                    <td class="text-end">₱${parseFloat(detail.Price).toFixed(2)}</td>
                                    <td class="text-end fw-bold">₱${parseFloat(subtotal).toFixed(2)}</td>
                                </tr>
                            `;
                        });
                        document.getElementById('totalPrice').innerHTML = `₱${parseFloat(data.total).toFixed(2)}`;
                        modal.show();
                    })
                    .catch(error => {
                        console.error('Error:', error);
                        alert('Error fetching transaction details');
                    });
            });
        });
    </script>
</body>
</html>
