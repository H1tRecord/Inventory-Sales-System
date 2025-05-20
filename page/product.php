<?php
require_once '../include/navbar.php';
require_once '../include/sidebar.php';
require_once '../controller/validation_controller.php';
require_once '../controller/product_controller.php';

validateAccess('Admin');

$productController = new ProductController($conn);
$products = $productController->getAllProducts();
$categories = $productController->getAllCategories();
$suppliers = $productController->getAllSuppliers();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Product Management</title>
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

        textarea.form-control {
            min-height: 100px;
        }
    </style>
    <script src="https://code.jquery.com/jquery-3.7.0.js"></script>
    <script src="https://cdn.datatables.net/1.13.7/js/jquery.dataTables.min.js"></script>
    <script src="https://cdn.datatables.net/1.13.7/js/dataTables.bootstrap5.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha3/dist/js/bootstrap.bundle.min.js"></script>
</head>
<body class="bg-light">
    <!-- Add/Edit Product Modal -->
    <div class="modal fade" id="productModal">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title"></h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <form id="productForm">
                        <input type="hidden" id="product_id" name="product_id">
                        <div class="mb-3">
                            <label class="form-label">Product Code</label>
                            <input type="text" class="form-control" id="product_code" name="Product_Code" required>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Product Name</label>
                            <input type="text" class="form-control" id="product_name" name="Product_Name" required>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Description</label>
                            <textarea class="form-control" id="description" name="Description" rows="3"></textarea>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Category</label>
                            <select class="form-select" id="category_id" name="Category_ID" required>
                                <?php foreach ($categories as $category): ?>
                                    <option value="<?= $category['Category_ID'] ?>"><?= $category['Category_Name'] ?></option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">In Stock</label>
                            <input type="number" class="form-control" id="in_stock" name="In_Stock" required min="0">
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Selling Price</label>
                            <input type="number" class="form-control" id="selling_price" name="Selling_Price" required min="0" step="0.01">
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Supplier</label>
                            <select class="form-select" id="supplier_id" name="Supplier_ID" required>
                                <?php foreach ($suppliers as $supplier): ?>
                                    <option value="<?= $supplier['Supplier_ID'] ?>"><?= $supplier['Supplier_Name'] ?></option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                    </form>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                    <button type="button" class="btn btn-primary" onclick="saveProduct()">Save</button>
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
                <h1 class="fs-3 fw-bold text-primary">Product Management</h1>
                <button class="btn btn-primary" onclick="showAddModal()">
                    <i class="bi bi-plus-circle me-2"></i>Add Product
                </button>
            </div>
            
            <table id="productTable" class="table table-bordered table-striped table-hover">
                <thead class="table-dark">
                    <tr>
                        <th>Product Code</th>
                        <th>Product Name</th>
                        <th>Description</th>
                        <th>Category</th>
                        <th data-type="num">In Stock</th>
                        <th data-type="num">Selling Price</th>
                        <th>Product Added</th>
                        <th>Supplier</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (empty($products)): ?>
                    <tr>
                        <td colspan="9" class="text-center py-4">
                            <div class="text-muted">
                                <i class="bi bi-box-seam fs-1 mb-3 d-block"></i>
                                <h5>No Products Found</h5>
                                <p>No products have been added to the inventory yet.</p>
                            </div>
                        </td>
                    </tr>
                    <?php else: ?>
                        <?php foreach ($products as $product): ?>
                        <tr class="<?php 
                            if ($product['In_Stock'] == 0) {
                                echo 'table-danger';
                            } elseif ($product['In_Stock'] <= 10) {
                                echo 'table-warning';
                            }
                        ?>">
                            <td><?= $product['Product_Code'] ?></td>
                            <td><?= $product['Product_Name'] ?></td>
                            <td><?= $product['Description'] ?></td>
                            <td><?= $product['Category_Name'] ?></td>
                            <td class="<?= $product['In_Stock'] <= 10 ? 'fw-bold' : '' ?>" 
                                data-order="<?= $product['In_Stock'] ?>">
                                <?php if ($product['In_Stock'] == 0): ?>
                                    <span class="badge bg-danger">Out of Stock</span>
                                <?php elseif ($product['In_Stock'] <= 10): ?>
                                    <span class="badge bg-warning text-dark"><?= $product['In_Stock'] ?></span>
                                <?php else: ?>
                                    <?= $product['In_Stock'] ?>
                                <?php endif; ?>
                            </td>
                            <td>₱<?= number_format($product['Selling_Price'], 2) ?></td>
                            <td><?= $product['Product_Added'] ?></td>
                            <td><?= $product['Supplier_Name'] ?></td>
                            <td>
                                <button class="btn btn-primary btn-sm" onclick="editProduct(<?= $product['Product_ID'] ?>)">
                                    <i class="bi bi-pencil"></i>
                                </button>
                                <button class="btn btn-danger btn-sm" onclick="deleteProduct(<?= $product['Product_ID'] ?>)">
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
        // Initialize DataTable with custom sorting for numeric columns
        $(document).ready(function() {
            if ($('#productTable tbody tr').length > 1) {
                $('#productTable').DataTable({
                    "order": [[0, "asc"]],
                    "columnDefs": [
                        { "orderable": false, "targets": 8 },
                        { 
                            "targets": 4,
                            "type": "num",
                            "orderData": 4
                        },
                        {
                            "targets": 5,
                            "type": "num",
                            "render": function(data, type, row) {
                                if (type === 'sort') {
                                    return parseFloat(data.replace('₱', '').replace(',', ''));
                                }
                                return data;
                            }
                        }
                    ]
                });
            }
        });

        // Modal management
        const productModal = new bootstrap.Modal(document.getElementById('productModal'));
        let isEditMode = false;

        // Modal display functions
        function showAddModal() {
            isEditMode = false;
            document.querySelector('.modal-title').textContent = 'Add New Product';
            document.getElementById('productForm').reset();
            productModal.show();
        }

        // Product data loading and form population
        function editProduct(id) {
            isEditMode = true;
            document.querySelector('.modal-title').textContent = 'Edit Product';
            
            fetch(`../controller/product_controller.php?action=get&id=${id}`)
                .then(response => response.json())
                .then(data => {
                    document.getElementById('product_id').value = data.Product_ID;
                    document.getElementById('product_code').value = data.Product_Code;
                    document.getElementById('product_name').value = data.Product_Name;
                    document.getElementById('description').value = data.Description;
                    document.getElementById('category_id').value = data.Category_ID;
                    document.getElementById('in_stock').value = data.In_Stock;
                    document.getElementById('selling_price').value = data.Selling_Price;
                    document.getElementById('supplier_id').value = data.Supplier_ID;
                    productModal.show();
                });
        }

        // Form submission with validation
        function saveProduct() {
            const form = document.getElementById('productForm');
            if (!form.checkValidity()) {
                form.reportValidity();
                return;
            }

            const formData = new FormData(form);
            const action = isEditMode ? 'update' : 'create';

            fetch(`../controller/product_controller.php?action=${action}`, {
                method: 'POST',
                body: formData
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    location.reload();
                } else {
                    alert(data.error || 'Error saving product');
                }
            });
        }

        // Delete product with confirmation
        function deleteProduct(id) {
            if (!confirm('Are you sure you want to delete this product?')) return;

            const formData = new FormData();
            formData.append('product_id', id);

            fetch('../controller/product_controller.php?action=delete', {
                method: 'POST',
                body: formData
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    location.reload();
                } else {
                    alert('Error deleting product');
                }
            });
        }
    </script>
</body>
</html>
