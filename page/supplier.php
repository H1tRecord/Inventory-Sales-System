<?php
require_once '../include/navbar.php';
require_once '../include/sidebar.php';
require_once '../controller/validation_controller.php';
require_once '../controller/supplier_controller.php';

validateAccess('Admin');

$supplierController = new SupplierController($conn);
$suppliers = $supplierController->getAllSuppliers();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Supplier Management</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons/font/bootstrap-icons.css" rel="stylesheet">
    <link href="https://cdn.datatables.net/1.13.7/css/dataTables.bootstrap5.min.css" rel="stylesheet">
    <style>
        .form-control {
            border: 2px solid #dee2e6;
            padding: 0.5rem 1rem;
            border-radius: 0.5rem;
            transition: border-color 0.2s ease-in-out, box-shadow 0.2s ease-in-out;
        }

        .form-control:focus {
            border-color: #0d6efd;
            box-shadow: 0 0 0 0.25rem rgba(13, 110, 253, 0.25);
        }

        .form-label {
            font-weight: 500;
            margin-bottom: 0.5rem;
            color: #495057;
        }

        .form-text {
            color: #6c757d;
            font-size: 0.875rem;
            margin-top: 0.25rem;
        }
    </style>
    <script src="https://code.jquery.com/jquery-3.7.0.js"></script>
    <script src="https://cdn.datatables.net/1.13.7/js/jquery.dataTables.min.js"></script>
    <script src="https://cdn.datatables.net/1.13.7/js/dataTables.bootstrap5.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha3/dist/js/bootstrap.bundle.min.js"></script>
</head>
<body class="bg-light">
    <!-- Add/Edit Supplier Modal -->
    <div class="modal fade" id="supplierModal">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title"></h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <form id="supplierForm">
                        <input type="hidden" id="supplier_id" name="supplier_id">
                        <div class="mb-3">
                            <label class="form-label">Supplier Name</label>
                            <input type="text" class="form-control" id="supplier_name" name="Supplier_Name" required>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Contact Number</label>
                            <input type="text" class="form-control" id="contact_number" name="Contact_Number" required>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Email</label>
                            <input type="email" class="form-control" id="email" name="Email" required>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Address</label>
                            <input type="text" class="form-control" id="address" name="Address" required>
                        </div>
                    </form>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                    <button type="button" class="btn btn-primary" onclick="saveSupplier()">Save</button>
                </div>
            </div>
        </div>
    </div>

    <div class="d-flex">
        <div class="flex-shrink-0" style="width: 250px;">
            <?php require_once '../include/sidebar.php'; ?>
        </div>
        <div class="flex-grow-1 p-4">
            <div class="d-flex justify-content-between align-items-center mb-4">
                <h1 class="fs-3 fw-bold text-primary">Supplier Management</h1>
                <button class="btn btn-primary" onclick="showAddModal()">
                    <i class="bi bi-plus-circle me-2"></i>Add Supplier
                </button>
            </div>
            
            <table id="supplierTable" class="table table-bordered table-striped table-hover">
                <thead class="table-dark">
                    <tr>
                        <th>ID</th>
                        <th>Supplier Name</th>
                        <th>Contact Number</th>
                        <th>Email</th>
                        <th>Address</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (empty($suppliers)): ?>
                    <tr>
                        <td colspan="6" class="text-center py-4">
                            <div class="text-muted">
                                <i class="bi bi-truck fs-1 mb-3 d-block"></i>
                                <h5>No Suppliers Found</h5>
                                <p>No suppliers have been added to the system yet.</p>
                            </div>
                        </td>
                    </tr>
                    <?php else: ?>
                        <?php foreach ($suppliers as $supplier): ?>
                        <tr>
                            <td><?= $supplier['Supplier_ID'] ?></td>
                            <td><?= $supplier['Supplier_Name'] ?></td>
                            <td><?= $supplier['Contact_Number'] ?></td>
                            <td><?= $supplier['Email'] ?></td>
                            <td><?= $supplier['Address'] ?></td>
                            <td>
                                <button class="btn btn-primary btn-sm" onclick="editSupplier(<?= $supplier['Supplier_ID'] ?>)">
                                    <i class="bi bi-pencil"></i>
                                </button>
                                <button class="btn btn-danger btn-sm" onclick="deleteSupplier(<?= $supplier['Supplier_ID'] ?>)">
                                    <i class="bi bi-trash"></i>
                                </button>
                            </td>
                        </tr>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>

    <script>
        // Initialize DataTable with supplier sorting
        $(document).ready(function() {
            if ($('#supplierTable tbody tr').length > 1) {
                $('#supplierTable').DataTable({
                    "order": [[0, "asc"]],
                    "columnDefs": [
                        { "orderable": false, "targets": 5 }
                    ]
                });
            }
        });

        // Modal management
        const supplierModal = new bootstrap.Modal(document.getElementById('supplierModal'));
        let isEditMode = false;

        // Modal display functions
        function showAddModal() {
            isEditMode = false;
            document.querySelector('.modal-title').textContent = 'Add New Supplier';
            document.getElementById('supplierForm').reset();
            supplierModal.show();
        }

        // Supplier data loading and form population
        function editSupplier(id) {
            isEditMode = true;
            document.querySelector('.modal-title').textContent = 'Edit Supplier';
            
            fetch(`../controller/supplier_controller.php?action=get&id=${id}`)
                .then(response => response.json())
                .then(data => {
                    document.getElementById('supplier_id').value = data.Supplier_ID;
                    document.getElementById('supplier_name').value = data.Supplier_Name;
                    document.getElementById('contact_number').value = data.Contact_Number;
                    document.getElementById('email').value = data.Email;
                    document.getElementById('address').value = data.Address;
                    supplierModal.show();
                });
        }

        // Input validation functions
        function validateEmail(email) {
            const emailRegex = /^[a-zA-Z0-9._-]+@[a-zA-Z0-9.-]+\.[a-zA-Z]{2,}$/;
            return emailRegex.test(email);
        }

        function validatePhone(phone) {
            return phone.length === 11;
        }

        // Form submission and validation
        function saveSupplier() {
            const form = document.getElementById('supplierForm');
            const email = document.getElementById('email').value;
            const phone = document.getElementById('contact_number').value;

            if (!form.checkValidity()) {
                form.reportValidity();
                return;
            }

            if (!validateEmail(email)) {
                alert('Please enter a valid email address');
                return;
            }

            if (!validatePhone(phone)) {
                alert('Phone number must be 11 digits');
                return;
            }

            const formData = new FormData(form);
            const action = isEditMode ? 'update' : 'create';

            fetch(`../controller/supplier_controller.php?action=${action}`, {
                method: 'POST',
                body: formData
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    location.reload();
                } else {
                    alert(data.error || 'Error saving supplier');
                }
            });
        }

        // Delete supplier with confirmation
        function deleteSupplier(id) {
            if (!confirm('Are you sure you want to delete this supplier?')) return;

            const formData = new FormData();
            formData.append('supplier_id', id);

            fetch('../controller/supplier_controller.php?action=delete', {
                method: 'POST',
                body: formData
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    location.reload();
                } else {
                    alert(data.error || 'Error deleting supplier');
                }
            });
        }
    </script>
</body>
</html>
