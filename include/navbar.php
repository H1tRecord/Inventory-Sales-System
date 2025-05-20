<?php session_start(); ?>

<nav class="navbar navbar-expand-lg shadow-sm sticky-top" style="background: linear-gradient(to right, #1a237e, #283593);">
    <div class="container-fluid px-4">
        <a class="navbar-brand text-white d-flex align-items-center" href="#">
            <i class="bi bi-building me-2"></i>
            <span class="fw-bold">Inventory & Sales System</span>
        </a>

        <!-- Add system switcher for admin -->
        <?php if (isset($_SESSION['user_role']) && $_SESSION['user_role'] === 'Admin'): ?>
        <div class="btn-group mx-3">
            <a href="../page/inventory_dashboard.php" class="btn btn-sm <?= strpos($_SERVER['PHP_SELF'], 'cashier_dashboard.php') ? 'btn-outline-light' : 'btn-light' ?>">
                <i class="bi bi-box-seam me-1"></i>Inventory
            </a>
            <a href="../page/cashier_dashboard.php" class="btn btn-sm <?= strpos($_SERVER['PHP_SELF'], 'cashier_dashboard.php') ? 'btn-light' : 'btn-outline-light' ?>">
                <i class="bi bi-cart3 me-1"></i>POS
            </a>
        </div>
        <?php endif; ?>

        <div class="ms-auto">
            <div class="dropdown">
                <button class="btn btn-link text-white text-decoration-none dropdown-toggle d-flex align-items-center" 
                        type="button" id="userMenuButton" data-bs-toggle="dropdown" aria-expanded="false">
                    <div class="rounded-circle bg-white bg-opacity-25 p-2 me-2">
                        <i class="bi bi-person-circle fs-5"></i>
                    </div>
                    <span><?php echo isset($_SESSION['username']) ? $_SESSION['username'] : 'User'; ?></span>
                </button>
                <ul class="dropdown-menu dropdown-menu-end border-0 shadow mt-2" aria-labelledby="userMenuButton">
                    <li>
                        <a class="dropdown-item py-2 px-4" href="../controller/logout.php">
                            <i class="bi bi-box-arrow-right me-2"></i>Sign Out
                        </a>
                    </li>
                </ul>
            </div>
        </div>
    </div>
</nav>

<style>
.navbar {
    padding: 0.75rem 0;
}
.dropdown-menu {
    border-radius: 0.5rem;
}
.dropdown-item:hover {
    background-color: #f8f9fa;
}
.btn-outline-light {
    border-color: rgba(255,255,255,0.5);
}
.btn-outline-light:hover {
    background-color: rgba(255,255,255,0.1);
}
</style>
