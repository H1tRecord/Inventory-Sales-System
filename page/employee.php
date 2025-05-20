<?php
require_once '../include/navbar.php';
require_once '../include/sidebar.php';
require_once '../controller/validation_controller.php';
require_once '../controller/employee_controller.php';

validateAccess('Admin');

$employeeController = new EmployeeController($conn);
$employees = $employeeController->getAllEmployees();

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['delete_employee'])) {
    $employeeId = $_POST['employee_id'];
    if ($employeeController->deleteEmployee($employeeId)) {
        header("Location: employee.php?success=Employee deleted successfully");
    } else {
        header("Location: employee.php?error=Failed to delete employee");
    }
    exit;
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Employee Management</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons/font/bootstrap-icons.css" rel="stylesheet">
    <link href="https://cdn.datatables.net/1.13.7/css/dataTables.bootstrap5.min.css" rel="stylesheet">
    <style>
        .form-control, .form-select {
            border: 2px solid #dee2e6;
            padding: 0.5rem 1rem;
            border-radius: 0.5rem;
            transition: all 0.2s ease-in-out;
            background-color: #f8f9fa;
        }

        .form-control:focus, .form-select:focus {
            border-color: #0d6efd;
            box-shadow: 0 0 0 0.25rem rgba(13, 110, 253, 0.25);
            background-color: #fff;
        }

        .form-label {
            font-weight: 500;
            margin-bottom: 0.5rem;
            color: #495057;
        }

        .form-control:hover, .form-select:hover {
            border-color: #adb5bd;
        }

        .modal-body {
            background-color: #f8f9fa;
            border-radius: 0.5rem;
            padding: 1.5rem;
        }
    </style>
    <script src="https://code.jquery.com/jquery-3.7.0.js"></script>
    <script src="https://cdn.datatables.net/1.13.7/js/jquery.dataTables.min.js"></script>
    <script src="https://cdn.datatables.net/1.13.7/js/dataTables.bootstrap5.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha3/dist/js/bootstrap.bundle.min.js"></script>
</head>
<body class="bg-light">
    <!-- Add Employee Modal -->
    <div class="modal fade" id="addEmployeeModal" tabindex="-1">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Add New Employee</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <form id="addEmployeeForm">
                        <div class="mb-3">
                            <label for="newName" class="form-label">Name</label>
                            <input type="text" class="form-control" id="newName" name="Name" required>
                        </div>
                        <div class="mb-3">
                            <label for="newEmail" class="form-label">Email</label>
                            <input type="email" class="form-control" id="newEmail" name="Email" required>
                        </div>
                        <div class="mb-3">
                            <label for="newPhone" class="form-label">Phone</label>
                            <input type="text" class="form-control" id="newPhone" name="PhoneNo">
                        </div>
                        <div class="mb-3">
                            <label for="newJobId" class="form-label">Job Title</label>
                            <select class="form-select" id="newJobId" name="Job_ID" required>
                                <?php foreach ($employeeController->getAllJobs() as $job): ?>
                                    <option value="<?= $job['Job_ID'] ?>"><?= $job['Job_Title'] ?></option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                    </form>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                    <button type="button" class="btn btn-primary" onclick="createEmployee()">Save</button>
                </div>
            </div>
        </div>
    </div>

    <!-- Edit Employee Modal -->
    <div class="modal fade" id="employeeModal" tabindex="-1">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Employee Details</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <form id="employeeForm">
                        <input type="hidden" id="employeeId" name="employeeId">
                        <div class="mb-3">
                            <label for="name" class="form-label">Name</label>
                            <input type="text" class="form-control" id="name" name="Name" required>
                        </div>
                        <div class="mb-3">
                            <label for="email" class="form-label">Email</label>
                            <input type="email" class="form-control" id="email" name="Email" required>
                        </div>
                        <div class="mb-3">
                            <label for="phone" class="form-label">Phone</label>
                            <input type="text" class="form-control" id="phone" name="PhoneNo">
                        </div>
                        <div class="mb-3">
                            <label for="jobId" class="form-label">Job Title</label>
                            <select class="form-select" id="jobId" name="Job_ID" required>
                                <?php
                                $jobs = $employeeController->getAllJobs();
                                foreach ($jobs as $job) {
                                    echo "<option value='{$job['Job_ID']}'>{$job['Job_Title']}</option>";
                                }
                                ?>
                            </select>
                        </div>
                    </form>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                    <button type="button" class="btn btn-primary" onclick="saveEmployee()">Save</button>
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
                <h1 class="fs-3 fw-bold text-primary">Employee Management</h1>
                <button type="button" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#addEmployeeModal">
                    <i class="bi bi-plus-circle me-2"></i>Add Employee
                </button>
            </div>
            
            <table id="employeeTable" class="table table-bordered table-striped table-hover">
                <thead class="table-dark">
                    <tr>
                        <th>ID</th>
                        <th>Name</th>
                        <th>Email</th>
                        <th>Phone</th>
                        <th>Job Title</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (empty($employees)): ?>
                    <tr>
                        <td colspan="6" class="text-center py-4">
                            <div class="text-muted">
                                <i class="bi bi-people fs-1 mb-3 d-block"></i>
                                <h5>No Employees Found</h5>
                                <p>No employees have been added yet.</p>
                            </div>
                        </td>
                    </tr>
                    <?php else: ?>
                        <?php foreach ($employees as $employee): ?>
                        <tr>
                            <td><?= $employee['Employee_ID'] ?></td>
                            <td><?= $employee['Name'] ?></td>
                            <td><?= $employee['Email'] ?></td>
                            <td><?= $employee['PhoneNo'] ?></td>
                            <td><?= $employee['Job_Title'] ?></td>
                            <td>
                                <button class="btn btn-primary btn-sm" onclick="editEmployee(<?= $employee['Employee_ID'] ?>)">
                                    <i class="bi bi-pencil"></i>
                                </button>
                                <button class="btn btn-danger btn-sm" onclick="deleteEmployee(<?= $employee['Employee_ID'] ?>)">
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
        $(document).ready(function() {
            if ($('#employeeTable tbody tr').length > 1) {  // Only initialize if there's data
                $('#employeeTable').DataTable({
                    "order": [[0, "asc"]],
                    "columnDefs": [
                        { "orderable": false, "targets": 5 }
                    ]
                });
            }
        });

        function validatePhoneNumber(phone) {
            return phone.length === 11;
        }

        function validateEmail(email) {
            const emailRegex = /^[a-zA-Z0-9._-]+@[a-zA-Z0-9.-]+\.[a-zA-Z]{2,}$/;
            return emailRegex.test(email);
        }

        function validateForm(formId) {
            const phoneInput = document.querySelector(`#${formId} [name="PhoneNo"]`);
            const emailInput = document.querySelector(`#${formId} [name="Email"]`);
            
            if (!validatePhoneNumber(phoneInput.value)) {
                alert('Phone number must be 11 digits');
                return false;
            }
            
            if (!validateEmail(emailInput.value)) {
                alert('Please enter a valid email address');
                return false;
            }
            
            return true;
        }

        function showAddModal() {
            const modal = new bootstrap.Modal(document.getElementById('addEmployeeModal'));
            modal.show();
        }

        function createEmployee() {
            if (!validateForm('addEmployeeForm')) return;

            const form = document.getElementById('addEmployeeForm');
            const formData = new FormData(form);

            fetch('../controller/employee_controller.php?action=create', {
                method: 'POST',
                body: formData
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    location.reload();
                } else {
                    alert(data.error || 'Error creating employee');
                }
            })
            .catch(error => {
                console.error('Error:', error);
                alert('Error creating employee');
            });
        }

        function editEmployee(id) {
            fetch(`../controller/employee_controller.php?action=get&id=${id}`)
                .then(response => response.json())
                .then(data => {
                    document.getElementById('employeeId').value = data.Employee_ID;
                    document.getElementById('name').value = data.Name;
                    document.getElementById('email').value = data.Email;
                    document.getElementById('phone').value = data.PhoneNo;
                    document.getElementById('jobId').value = data.Job_ID;
                    
                    const editModal = new bootstrap.Modal(document.getElementById('employeeModal'));
                    editModal.show();
                })
                .catch(error => {
                    console.error('Error:', error);
                    alert('Error fetching employee data');
                });
        }

        function saveEmployee() {
            if (!validateForm('employeeForm')) return;

            const form = document.getElementById('employeeForm');
            const formData = new FormData(form);

            fetch('../controller/employee_controller.php?action=update', {
                method: 'POST',
                body: formData
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    location.reload();
                } else {
                    alert(data.error || 'Error updating employee');
                }
            })
            .catch(error => {
                console.error('Error:', error);
                alert('Error updating employee');
            });
        }

        function deleteEmployee(id) {
            if (!confirm('Are you sure you want to delete this employee?')) return;

            const formData = new FormData();
            formData.append('employee_id', id);

            fetch('../controller/employee_controller.php?action=delete', {
                method: 'POST',
                body: formData
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    location.reload();
                } else {
                    alert(data.error || 'Error deleting employee');
                }
            })
            .catch(error => {
                console.error('Error:', error);
                alert('Error deleting employee');
            });
        }
    </script>
</body>
</html>
