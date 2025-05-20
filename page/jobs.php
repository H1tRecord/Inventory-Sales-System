<?php
require_once '../include/navbar.php';
require_once '../include/sidebar.php';
require_once '../controller/validation_controller.php';
require_once '../controller/job_controller.php';

validateAccess('Admin');

$jobController = new JobController($conn);
$jobs = $jobController->getAllJobs();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Job Management</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons/font/bootstrap-icons.css" rel="stylesheet">
    <link href="https://cdn.datatables.net/1.13.7/css/dataTables.bootstrap5.min.css" rel="stylesheet">
    <script src="https://code.jquery.com/jquery-3.7.0.js"></script>
    <script src="https://cdn.datatables.net/1.13.7/js/jquery.dataTables.min.js"></script>
    <script src="https://cdn.datatables.net/1.13.7/js/dataTables.bootstrap5.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha3/dist/js/bootstrap.bundle.min.js"></script>
</head>
<body class="bg-light">
    <!-- Add/Edit Job Modal -->
    <div class="modal fade" id="jobModal">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title"></h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <form id="jobForm">
                        <input type="hidden" id="job_id" name="job_id">
                        <div class="mb-3">
                            <label class="form-label">Job Title</label>
                            <input type="text" class="form-control" id="job_title" name="Job_Title" required>
                        </div>
                    </form>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                    <button type="button" class="btn btn-primary" onclick="saveJob()">Save</button>
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
                <h1 class="fs-3 fw-bold text-primary">Job Title Management</h1>
                <button class="btn btn-primary" onclick="showAddModal()">
                    <i class="bi bi-plus-circle me-2"></i>Add Job Title
                </button>
            </div>
            
            <table id="jobTable" class="table table-bordered table-striped table-hover">
                <thead class="table-dark">
                    <tr>
                        <th>Job Title</th>
                        <th>Employees Assigned</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (empty($jobs)): ?>
                    <tr>
                        <td colspan="3" class="text-center py-4">
                            <div class="text-muted">
                                <i class="bi bi-briefcase fs-1 mb-3 d-block"></i>
                                <h5>No Job Titles Found</h5>
                                <p>No job titles have been added yet.</p>
                            </div>
                        </td>
                    </tr>
                    <?php else: ?>
                        <?php foreach ($jobs as $job): ?>
                        <tr>
                            <td><?= $job['Job_Title'] ?></td>
                            <td><?= $job['Employee_Count'] ?></td>
                            <td>
                                <button class="btn btn-primary btn-sm" onclick="editJob(<?= $job['Job_ID'] ?>)">
                                    <i class="bi bi-pencil"></i>
                                </button>
                                <button class="btn btn-danger btn-sm" onclick="deleteJob(<?= $job['Job_ID'] ?>)">
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
        // Initialize DataTable for job titles
        $(document).ready(function() {
            if ($('#jobTable tbody tr').length > 1) {
                $('#jobTable').DataTable({
                    "order": [[0, "asc"]],
                    "columnDefs": [
                        { "orderable": false, "targets": 2 }
                    ]
                });
            }
        });

        // Modal management
        const jobModal = new bootstrap.Modal(document.getElementById('jobModal'));
        let isEditMode = false;

        // Modal display functions
        function showAddModal() {
            isEditMode = false;
            document.querySelector('.modal-title').textContent = 'Add New Job Title';
            document.getElementById('jobForm').reset();
            jobModal.show();
        }

        // Job data loading and form population
        function editJob(id) {
            isEditMode = true;
            document.querySelector('.modal-title').textContent = 'Edit Job Title';
            
            fetch(`../controller/job_controller.php?action=get&id=${id}`)
                .then(response => response.json())
                .then(data => {
                    document.getElementById('job_id').value = data.Job_ID;
                    document.getElementById('job_title').value = data.Job_Title;
                    jobModal.show();
                });
        }

        // Form submission with validation
        function saveJob() {
            const form = document.getElementById('jobForm');
            if (!form.checkValidity()) {
                form.reportValidity();
                return;
            }

            const formData = new FormData(form);
            const action = isEditMode ? 'update' : 'create';

            fetch(`../controller/job_controller.php?action=${action}`, {
                method: 'POST',
                body: formData
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    location.reload();
                } else {
                    alert(data.error || 'Error saving job title');
                }
            });
        }

        // Delete job with confirmation
        function deleteJob(id) {
            if (!confirm('Are you sure you want to delete this job title?')) return;

            const formData = new FormData();
            formData.append('job_id', id);

            fetch('../controller/job_controller.php?action=delete', {
                method: 'POST',
                body: formData
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    location.reload();
                } else {
                    alert(data.error || 'Error deleting job title');
                }
            });
        }
    </script>
</body>
</html>
