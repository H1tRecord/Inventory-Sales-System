<?php
require_once '../include/navbar.php';
require_once '../include/sidebar.php';
require_once '../controller/validation_controller.php';
require_once '../controller/category_controller.php';

validateAccess('Admin');

$categoryController = new CategoryController($conn);
$categories = $categoryController->getAllCategories();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Category Management</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons/font/bootstrap-icons.css" rel="stylesheet">
    <link href="https://cdn.datatables.net/1.13.7/css/dataTables.bootstrap5.min.css" rel="stylesheet">
    <script src="https://code.jquery.com/jquery-3.7.0.js"></script>
    <script src="https://cdn.datatables.net/1.13.7/js/jquery.dataTables.min.js"></script>
    <script src="https://cdn.datatables.net/1.13.7/js/dataTables.bootstrap5.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha3/dist/js/bootstrap.bundle.min.js"></script>
</head>
<body class="bg-light">
    <!-- Add/Edit Category Modal -->
    <div class="modal fade" id="categoryModal">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title"></h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <form id="categoryForm">
                        <input type="hidden" id="category_id" name="category_id">
                        <div class="mb-3">
                            <label class="form-label">Category Name</label>
                            <input type="text" class="form-control" id="category_name" name="Category_Name" required>
                        </div>
                    </form>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                    <button type="button" class="btn btn-primary" onclick="saveCategory()">Save</button>
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
                <h1 class="fs-3 fw-bold text-primary">Category Management</h1>
                <button class="btn btn-primary" onclick="showAddModal()">
                    <i class="bi bi-plus-circle me-2"></i>Add Category
                </button>
            </div>
            
            <table id="categoryTable" class="table table-bordered table-striped table-hover">
                <thead class="table-dark">
                    <tr>
                        <th style="display:none;">ID</th>
                        <th>Category Name</th>
                        <th>Products Assigned</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (empty($categories)): ?>
                    <tr>
                        <td colspan="4" class="text-center py-4">
                            <div class="text-muted">
                                <i class="bi bi-tags fs-1 mb-3 d-block"></i>
                                <h5>No Categories Found</h5>
                                <p>No product categories have been added yet.</p>
                            </div>
                        </td>
                    </tr>
                    <?php else: ?>
                        <?php foreach ($categories as $category): ?>
                        <tr>
                            <td style="display:none;"><?= $category['Category_ID'] ?></td>
                            <td><?= $category['Category_Name'] ?></td>
                            <td><?= $category['Product_Count'] ?></td>
                            <td>
                                <button class="btn btn-primary btn-sm" onclick="editCategory(<?= $category['Category_ID'] ?>)">
                                    <i class="bi bi-pencil"></i>
                                </button>
                                <button class="btn btn-danger btn-sm" onclick="deleteCategory(<?= $category['Category_ID'] ?>)">
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
            if ($('#categoryTable tbody tr').length > 1) {
                $('#categoryTable').DataTable({
                    "order": [[1, "asc"]],
                    "columnDefs": [
                        { "targets": [0], "visible": false },
                        { "orderable": false, "targets": 3 }
                    ]
                });
            }
        });

        const categoryModal = new bootstrap.Modal(document.getElementById('categoryModal'));
        let isEditMode = false;

        function showAddModal() {
            isEditMode = false;
            document.querySelector('.modal-title').textContent = 'Add New Category';
            document.getElementById('categoryForm').reset();
            categoryModal.show();
        }

        function editCategory(id) {
            isEditMode = true;
            document.querySelector('.modal-title').textContent = 'Edit Category';
            
            fetch(`../controller/category_controller.php?action=get&id=${id}`)
                .then(response => response.json())
                .then(data => {
                    document.getElementById('category_id').value = data.Category_ID;
                    document.getElementById('category_name').value = data.Category_Name;
                    categoryModal.show();
                });
        }

        function saveCategory() {
            const form = document.getElementById('categoryForm');
            if (!form.checkValidity()) {
                form.reportValidity();
                return;
            }

            const formData = new FormData(form);
            const action = isEditMode ? 'update' : 'create';

            fetch(`../controller/category_controller.php?action=${action}`, {
                method: 'POST',
                body: formData
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    location.reload();
                } else {
                    alert(data.error || 'Error saving category');
                }
            });
        }

        function deleteCategory(id) {
            if (!confirm('Are you sure you want to delete this category?')) return;

            const formData = new FormData();
            formData.append('category_id', id);

            fetch('../controller/category_controller.php?action=delete', {
                method: 'POST',
                body: formData
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    location.reload();
                } else {
                    alert(data.error || 'Error deleting category');
                }
            });
        }
    </script>
</body>
</html>
