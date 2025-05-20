<?php
require_once '../include/navbar.php';
require_once '../controller/validation_controller.php';
require_once '../config/db_connection.php';

validateAccess(['Admin', 'User']); // Update to accept both roles

// Add session check at the top
if (!isset($_SESSION['employee_id']) || !isset($_SESSION['job_id'])) {
    header("Location: ../page/login.php");
    exit();
}

// Fetch categories
$categories = $conn->query("SELECT * FROM category ORDER BY Category_Name");

// Fetch products
$products = $conn->query("SELECT p.*, c.Category_Name 
                         FROM product p 
                         LEFT JOIN category c ON p.Category_ID = c.Category_ID 
                         ORDER BY p.Product_Name");
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>POS System</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons/font/bootstrap-icons.css" rel="stylesheet">
    <style>
        .product-grid {
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(200px, 1fr));
            gap: 1rem;
            padding: 1rem;
        }
        .product-card {
            cursor: pointer;
            transition: transform 0.2s;
        }
        .product-card:hover {
            transform: translateY(-5px);
        }
        .cart-container {
            height: calc(100vh - 100px);
            display: flex;
            flex-direction: column;
            position: sticky;
            top: 80px;
        }
        .cart-items {
            flex-grow: 1;
            overflow-y: auto;
        }
        .category-pills {
            overflow-x: scroll;
            white-space: nowrap;
            padding: 1rem;
            scrollbar-width: thin;
            scrollbar-color: #6c757d #f8f9fa;
        }
        .category-pills::-webkit-scrollbar {
            height: 8px;
            display: block;
        }
        .category-pills::-webkit-scrollbar-track {
            background: #f8f9fa;
            border-radius: 4px;
        }
        .category-pills::-webkit-scrollbar-thumb {
            background-color: #6c757d;
            border-radius: 4px;
        }
        .category-pills::-webkit-scrollbar-thumb:hover {
            background-color: #495057;
        }
        .category-pill {
            cursor: pointer;
            user-select: none;
        }
        .search-container {
            position: relative;
            margin-bottom: 1rem;
        }

        .search-icon {
            position: absolute;
            left: 1rem;
            top: 50%;
            transform: translateY(-50%);
            color: #6c757d;
            font-size: 1.2rem;
            pointer-events: none;
        }

        #search {
            padding-left: 3rem;
            padding-right: 1rem;
            height: 3rem;
            width: 100%;
            border-radius: 1.5rem;
            border: 2px solid #dee2e6;
            font-size: 1.1rem;
            transition: all 0.3s ease;
            background-color: #fff;
        }
        #search:focus {
            border-color: #0d6efd;
            box-shadow: 0 0 0 0.25rem rgba(13, 110, 253, 0.25);
        }
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
        .modal-body .form-control {
            margin-bottom: 1rem;
        }
    </style>
</head>
<body class="bg-light">
    <div class="container-fluid">
        <div class="row g-3 my-3">
            <!-- Products Section -->
            <div class="col-lg-8">
                <div class="card shadow-sm">
                    <div class="card-body p-0">
                        <!-- Search and Categories -->
                        <div class="p-3 border-bottom">
                            <div class="search-container">
                                <i class="bi bi-search search-icon"></i>
                                <input type="text" id="search" class="form-control form-control-lg" placeholder="Search products...">
                            </div>
                            <div class="category-pills">
                                <button class="btn btn-outline-primary me-2 category-pill active" data-category="all">
                                    All Products
                                </button>
                                <?php while($category = $categories->fetch_assoc()): ?>
                                    <button class="btn btn-outline-primary me-2 category-pill" 
                                            data-category="<?= $category['Category_ID'] ?>">
                                        <?= $category['Category_Name'] ?>
                                    </button>
                                <?php endwhile; ?>
                            </div>
                        </div>
                        <!-- Products Grid -->
                        <div class="product-grid">
                            <?php while($product = $products->fetch_assoc()): ?>
                                <div class="card product-card" 
                                     data-category="<?= $product['Category_ID'] ?>"
                                     data-name="<?= strtolower($product['Product_Name']) ?>"
                                     onclick="addToCart(<?= htmlspecialchars(json_encode($product)) ?>)">
                                    <div class="card-body">
                                        <h6 class="card-title mb-1"><?= $product['Product_Name'] ?></h6>
                                        <p class="card-text text-muted small mb-0"><?= $product['Category_Name'] ?></p>
                                        <p class="card-text text-primary fw-bold mt-2">₱<?= number_format($product['Selling_Price'], 2) ?></p>
                                        <span class="badge bg-<?= $product['In_Stock'] > 0 ? 'success' : 'danger' ?>">
                                            <?= $product['In_Stock'] > 0 ? 'In Stock: ' . $product['In_Stock'] : 'Out of Stock' ?>
                                        </span>
                                    </div>
                                </div>
                            <?php endwhile; ?>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Cart Section -->
            <div class="col-lg-4">
                <div class="card shadow-sm cart-container">
                    <div class="card-header bg-primary text-white py-3">
                        <h5 class="card-title mb-0"><i class="bi bi-cart3 me-2"></i>Shopping Cart</h5>
                    </div>
                    <div class="cart-items p-3">
                        <!-- Cart items will be dynamically added here -->
                    </div>
                    <div class="card-footer border-top">
                        <div class="d-flex justify-content-between align-items-center mb-3">
                            <h5 class="mb-0">Total:</h5>
                            <h5 class="mb-0" id="cartTotal">₱0.00</h5>
                        </div>
                        <button class="btn btn-primary w-100" onclick="checkout()">
                            <i class="bi bi-credit-card me-2"></i>Proceed to Checkout
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Checkout Modal -->
    <div class="modal fade" id="checkoutModal" tabindex="-1">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header bg-primary text-white">
                    <h5 class="modal-title">Checkout</h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <form id="checkoutForm" class="row g-3">
                        <div class="col-12">
                            <label class="form-label">Customer Full Name</label>
                            <input type="text" class="form-control" id="customerName" required>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Email</label>
                            <input type="email" class="form-control" id="customerEmail">
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Phone Number</label>
                            <input type="tel" class="form-control" id="customerPhone">
                        </div>
                        <div class="col-12">
                            <h6 class="mb-3">Order Summary</h6>
                            <div class="table-responsive">
                                <table class="table table-bordered">
                                    <thead>
                                        <tr>
                                            <th>Product</th>
                                            <th>Quantity</th>
                                            <th>Price</th>
                                            <th>Subtotal</th>
                                        </tr>
                                    </thead>
                                    <tbody id="checkoutItems"></tbody>
                                    <tfoot>
                                        <tr>
                                            <td colspan="3" class="text-end fw-bold">Total:</td>
                                            <td class="fw-bold" id="checkoutTotal"></td>
                                        </tr>
                                    </tfoot>
                                </table>
                            </div>
                        </div>
                    </form>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="button" class="btn btn-primary" onclick="processTransaction()">
                        <i class="bi bi-check-circle me-2"></i>Complete Transaction
                    </button>
                </div>
            </div>
        </div>
    </div>

    <script src="https://code.jquery.com/jquery-3.7.0.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha3/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        // Store shopping cart data
        let cart = [];

        // Validate email format
        function validateEmail(email) {
            if (!email) return true; // Email is optional
            const emailRegex = /^[a-zA-Z0-9._-]+@[a-zA-Z0-9.-]+\.[a-zA-Z]{2,}$/;
            return emailRegex.test(email);
        }

        // Validate phone number length
        function validatePhone(phone) {
            if (!phone) return true; // Phone is optional
            return phone.length === 11;
        }

        // Refresh product catalog after transaction
        function refreshCatalog() {
            fetch('cashier_dashboard.php')
                .then(response => response.text())
                .then(html => {
                    const parser = new DOMParser();
                    const doc = parser.parseFromString(html, 'text/html');
                    const newProductGrid = doc.querySelector('.product-grid');
                    document.querySelector('.product-grid').innerHTML = newProductGrid.innerHTML;
                });
        }

        $(document).ready(function() {
            // Initialize search and category filtering
            $('#search').on('input', function() {
                const searchTerm = $(this).val().toLowerCase();
                $('.product-card').each(function() {
                    const productName = $(this).data('name');
                    $(this).toggle(productName.includes(searchTerm));
                });
            });

            // Handle category filter clicks
            $('.category-pill').click(function() {
                $('.category-pill').removeClass('active');
                $(this).addClass('active');
                
                const category = $(this).data('category');
                $('.product-card').each(function() {
                    if (category === 'all') {
                        $(this).show();
                    } else {
                        $(this).toggle($(this).data('category') == category);
                    }
                });
            });
        });

        function addToCart(product) {
            // Check stock and add/update product in cart
            if (product.In_Stock <= 0) {
                alert('Product is out of stock!');
                return;
            }

            const existingItem = cart.find(item => item.Product_ID === product.Product_ID);
            if (existingItem) {
                if (existingItem.quantity >= product.In_Stock) {
                    alert('Cannot exceed available stock!');
                    return;
                }
                existingItem.quantity++;
            } else {
                cart.push({...product, quantity: 1});
            }
            updateCartDisplay();
        }

        function removeFromCart(index) {
            // Remove item from cart
            cart.splice(index, 1);
            updateCartDisplay();
        }

        function updateQuantity(index, change) {
            // Update item quantity and check stock limits
            const item = cart[index];
            const newQuantity = item.quantity + change;
            
            if (newQuantity > item.In_Stock) {
                alert('Cannot exceed available stock!');
                return;
            }
            
            if (newQuantity > 0) {
                item.quantity = newQuantity;
            } else {
                cart.splice(index, 1);
            }
            updateCartDisplay();
        }

        function updateCartDisplay() {
            // Refresh cart UI and calculate total
            const cartContainer = $('.cart-items');
            cartContainer.empty();
            
            let total = 0;
            
            cart.forEach((item, index) => {
                const subtotal = item.quantity * item.Selling_Price;
                total += subtotal;
                
                cartContainer.append(`
                    <div class="card mb-2">
                        <div class="card-body p-2">
                            <div class="d-flex justify-content-between align-items-center">
                                <div>
                                    <h6 class="mb-0">${item.Product_Name}</h6>
                                    <small class="text-muted">₱${parseFloat(item.Selling_Price).toFixed(2)}</small>
                                </div>
                                <div class="d-flex align-items-center">
                                    <button class="btn btn-sm btn-outline-secondary" onclick="updateQuantity(${index}, -1)">-</button>
                                    <span class="mx-2">${item.quantity}</span>
                                    <button class="btn btn-sm btn-outline-secondary" onclick="updateQuantity(${index}, 1)">+</button>
                                    <button class="btn btn-sm btn-outline-danger ms-2" onclick="removeFromCart(${index})">
                                        <i class="bi bi-trash"></i>
                                    </button>
                                </div>
                            </div>
                        </div>
                    </div>
                `);
            });

            $('#cartTotal').text(`₱${total.toFixed(2)}`);
        }

        const checkoutModal = new bootstrap.Modal(document.getElementById('checkoutModal'));

        function checkout() {
            // Prepare and show checkout modal
            if (cart.length === 0) {
                alert('Cart is empty!');
                return;
            }

            const checkoutItems = document.getElementById('checkoutItems');
            checkoutItems.innerHTML = '';
            let total = 0;

            cart.forEach(item => {
                const subtotal = item.quantity * item.Selling_Price;
                total += subtotal;
                checkoutItems.innerHTML += `
                    <tr>
                        <td>${item.Product_Name}</td>
                        <td>${item.quantity}</td>
                        <td>₱${parseFloat(item.Selling_Price).toFixed(2)}</td>
                        <td>₱${subtotal.toFixed(2)}</td>
                    </tr>
                `;
            });

            document.getElementById('checkoutTotal').textContent = `₱${total.toFixed(2)}`;
            checkoutModal.show();
        }

        function processTransaction() {
            // Validate customer details
            const customerName = document.getElementById('customerName').value.trim();
            const customerEmail = document.getElementById('customerEmail').value.trim();
            const customerPhone = document.getElementById('customerPhone').value.trim();

            // Basic validation checks
            if (!customerName) {
                alert('Please enter customer name');
                return;
            }

            // Email validation if provided
            if (customerEmail && !validateEmail(customerEmail)) {
                alert('Please enter a valid email address');
                return;
            }

            // Phone validation if provided
            if (customerPhone && !validatePhone(customerPhone)) {
                alert('Phone number must be 11 digits');
                return;
            }

            // Prepare transaction data
            const totalItems = cart.reduce((sum, item) => sum + item.quantity, 0);
            const employeeId = <?= (int)$_SESSION['employee_id'] ?>;
            const jobId = <?= (int)$_SESSION['job_id'] ?>;

            const transactionData = {
                action: 'create',
                customerName: customerName,
                customerEmail: customerEmail || null,
                customerPhone: customerPhone || null,
                totalItems: totalItems,
                items: cart.map(item => ({
                    Product_ID: item.Product_ID,
                    quantity: item.quantity,
                    Selling_Price: parseFloat(item.Selling_Price)
                })),
                employeeId: employeeId,
                jobId: jobId
            };

            // Process transaction with loading state
            const checkoutButton = document.querySelector('.modal-footer .btn-primary');
            checkoutButton.disabled = true;
            checkoutButton.innerHTML = '<span class="spinner-border spinner-border-sm me-2"></span>Processing...';

            // Send transaction to server
            fetch('../controller/transaction_controller.php', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                },
                body: JSON.stringify(transactionData)
            })
            .then(response => response.json())
            .then(data => {
                // Handle successful transaction
                if (data.success) {
                    alert('Transaction completed successfully!');
                    cart = [];
                    updateCartDisplay();
                    checkoutModal.hide();
                    document.getElementById('checkoutForm').reset();
                    refreshCatalog(); // Add this line to refresh the catalog
                } else {
                    throw new Error(data.error || 'Unknown error occurred');
                }
            })
            .catch(error => {
                // Handle transaction errors
                console.error('Error:', error);
                alert('Error processing transaction: ' + error.message);
            })
            .finally(() => {
                // Reset checkout button state
                checkoutButton.disabled = false;
                checkoutButton.innerHTML = '<i class="bi bi-check-circle me-2"></i>Complete Transaction';
            });
        }
    </script>
</body>
</html>
