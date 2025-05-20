<?php
require_once '../include/navbar.php';
require_once '../include/sidebar.php';
require_once '../controller/validation_controller.php';
require_once '../controller/user_controller.php';

validateAccess('Admin');

$userController = new UserController($conn);
$users = $userController->getAllUsers();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Account Management</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons/font/bootstrap-icons.css" rel="stylesheet">
    <link href="https://cdn.datatables.net/1.13.7/css/dataTables.bootstrap5.min.css" rel="stylesheet">
    <style>
        .form-control, .form-select {
            border: 2px solid #dee2e6;
            padding: 0.5rem 1rem;
            border-radius: 0.5rem;
            transition: border-color 0.2s ease-in-out, box-shadow 0.2s ease-in-out;
        }

        .form-control:focus, .form-select:focus {
            border-color: #0d6efd;
            box-shadow: 0 0 0 0.25rem rgba(13, 110, 253, 0.25);
        }

        .form-label {
            font-weight: 500;
            margin-bottom: 0.5rem;
            color: #495057;
        }
    </style>
    <script src="https://code.jquery.com/jquery-3.7.0.js"></script>
    <script src="https://cdn.datatables.net/1.13.7/js/jquery.dataTables.min.js"></script>
    <script src="https://cdn.datatables.net/1.13.7/js/dataTables.bootstrap5.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha3/dist/js/bootstrap.bundle.min.js"></script>
</head>
<body class="bg-light">
    <!-- Add/Edit User Modal -->
    <div class="modal fade" id="userModal">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title"></h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <form id="userForm">
                        <input type="hidden" id="user_id" name="user_id">
                        <div class="mb-3">
                            <label class="form-label">Employee</label>
                            <select class="form-select" id="employee_id" name="Employee_ID" required>
                                <option value="">Select Employee</option>
                            </select>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Username</label>
                            <input type="text" class="form-control" id="username" name="Username" required>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Password</label>
                            <input type="password" class="form-control" id="password" name="Password" required>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Type</label>
                            <select class="form-select" id="type_id" name="Type_ID" required>
                                <option value="1">Admin</option>
                                <option value="2">User</option>
                            </select>
                        </div>
                    </form>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                    <button type="button" class="btn btn-primary" onclick="saveUser()">Save</button>
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
                <h1 class="fs-3 fw-bold text-primary">Account Management</h1>
                <button class="btn btn-primary" onclick="showAddModal()">
                    <i class="bi bi-plus-circle me-2"></i>Add Account
                </button>
            </div>

            <!-- Add warning alert with count -->
            <?php
            $adminCount = $userController->getAdminCount()['admin_count'];
            ?>
            <div class="alert alert-warning mb-4" role="alert">
                <i class="bi bi-exclamation-triangle me-2"></i>
                <strong>Note:</strong> At least one admin account must exist at all times.
                <br>
                <small>Current admin accounts: <?= $adminCount ?></small>
            </div>

            <table id="accountsTable" class="table table-bordered table-striped table-hover">
                <thead class="table-dark">
                    <tr>
                        <th>ID</th>
                        <th>Username</th>
                        <th>Employee Name</th>
                        <th>Type</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (empty($users)): ?>
                    <tr>
                        <td colspan="5" class="text-center py-4">
                            <div class="text-muted">
                                <i class="bi bi-person-badge fs-1 mb-3 d-block"></i>
                                <h5>No User Accounts Found</h5>
                                <p>No user accounts have been created yet.</p>
                            </div>
                        </td>
                    </tr>
                    <?php else: ?>
                        <?php foreach ($users as $user): ?>
                    <tr>
                        <td><?= $user['ID'] ?></td>
                        <td><?= $user['Username'] ?></td>
                        <td><?= $user['Employee_Name'] ?></td>
                        <td><?= $user['Type'] ?></td>
                        <td>
                            <button class="btn btn-primary btn-sm" onclick="editUser(<?= $user['ID'] ?>)">
                                <i class="bi bi-pencil"></i>
                            </button>
                            <button class="btn btn-danger btn-sm" onclick="deleteUser(<?= $user['ID'] ?>)">
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
        // Initialize DataTable with custom ordering
        $(document).ready(function() {
            // Initialize DataTable if there are records
            if ($('#accountsTable tbody tr').length > 1) {
                $('#accountsTable').DataTable({
                    "order": [[0, "asc"]],
                    "columnDefs": [
                        { "orderable": false, "targets": 4 }
                    ]
                });
            }
        });

        // Modal and state management
        const userModal = new bootstrap.Modal(document.getElementById('userModal'));
        let isEditMode = false;

        // Fetch employees without existing accounts
        function loadAvailableEmployees() {
            // Fetch employees without user accounts from server
            fetch('../controller/user_controller.php?action=available_employees')
                .then(response => response.json())
                .then(employees => {
                    const select = document.getElementById('employee_id');
                    select.innerHTML = '<option value="">Select Employee</option>';
                    employees.forEach(emp => {
                        select.innerHTML += `<option value="${emp.Employee_ID}">${emp.Name}</option>`;
                    });
                });
        }

        // Modal display functions
        function showAddModal() {
            // Reset form and prepare for new user
            isEditMode = false;
            document.querySelector('.modal-title').textContent = 'Add New Account';
            document.getElementById('userForm').reset();
            document.getElementById('employee_id').disabled = false;
            document.getElementById('employee_id').parentElement.style.display = 'block';
            loadAvailableEmployees();
            userModal.show();
        }

        function editUser(id) {
            // Load existing user data and show edit form
            // Hide employee selection since it can't be changed
            isEditMode = true;
            document.querySelector('.modal-title').textContent = 'Edit Account';
            document.getElementById('employee_id').disabled = true;
            document.getElementById('employee_id').parentElement.style.display = 'none';

            // Fetch user data from server
            fetch(`../controller/user_controller.php?action=get&id=${id}`)
                .then(response => response.json())
                .then(data => {
                    // Populate form with user data
                    document.getElementById('user_id').value = data.ID;
                    document.getElementById('username').value = data.Username;
                    const typeSelect = document.getElementById('type_id');
                    typeSelect.value = data.Type_ID;
                    typeSelect.setAttribute('data-current-type', data.Type_ID);
                    document.getElementById('employee_id').value = data.Employee_ID;
                    userModal.show();
                });
        }

        // Form submission and validation
        function saveUser() {
            // Validate form and check admin count if needed
            const form = document.getElementById('userForm');
            if (!form.checkValidity()) {
                form.reportValidity();
                return;
            }

            // Check if changing from admin to user when editing
            if (isEditMode) {
                const typeSelect = document.getElementById('type_id');
                const currentType = typeSelect.getAttribute('data-current-type');
                if (currentType === '1' && typeSelect.value === '2') {
                    // Check admin count before allowing change
                    fetch('../controller/user_controller.php?action=count_admins')
                        .then(response => response.json())
                        .then(data => {
                            if (data.admin_count <= 1) {
                                alert('Cannot change the last admin to a regular user!');
                                typeSelect.value = '1';
                                return;
                            }
                            proceedWithSave();
                        });
                    return;
                }
            }

            proceedWithSave();
        }

        function proceedWithSave() {
            // Send data to server and handle response
            const formData = new FormData(document.getElementById('userForm'));
            const action = isEditMode ? 'update' : 'create';

            fetch(`../controller/user_controller.php?action=${action}`, {
                method: 'POST',
                body: formData
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    location.reload();
                } else {
                    alert(data.error || 'Error saving account');
                }
            });
        }

        // User deletion with admin check
        function deleteUser(id) {
            // Check admin status before deletion
            // First check if this is an admin account
            fetch(`../controller/user_controller.php?action=get&id=${id}`)
                .then(response => response.json())
                .then(data => {
                    if (data.Type_ID === '1') {
                        // Check admin count
                        fetch('../controller/user_controller.php?action=count_admins')
                            .then(response => response.json())
                            .then(countData => {
                                if (countData.admin_count <= 1) {
                                    alert('Cannot delete the last admin account!');
                                    return;
                                }
                                proceedWithDelete(id);
                            });
                    } else {
                        proceedWithDelete(id);
                    }
                });
        }

        function proceedWithDelete(id) {
            // Execute deletion after confirmation
            if (!confirm('Are you sure you want to delete this account?')) return;

            const formData = new FormData();
            formData.append('user_id', id);

            // Send delete request to server
            fetch('../controller/user_controller.php?action=delete', {
                method: 'POST',
                body: formData
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    location.reload();
                } else {
                    alert(data.error || 'Error deleting account');
                }
            });
        }
    </script>
</body>
</html>
