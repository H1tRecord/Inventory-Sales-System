<div class="d-flex flex-column text-white h-100 position-fixed shadow" style="width: 250px; background: linear-gradient(to bottom, #1a237e, #283593);">
    <ul class="nav flex-column mt-4">
        <?php $current_page = basename($_SERVER['PHP_SELF']); ?>
        <li class="nav-item px-3 mb-2">
            <a href="../page/inventory_dashboard.php" class="nav-link text-white d-flex align-items-center <?php echo $current_page == 'inventory_dashboard.php' ? 'active bg-white bg-opacity-10 rounded' : ''; ?>" style="transition: all 0.3s;">
                <i class="bi bi-house-door me-3"></i> Home
            </a>
        </li>
        <li class="nav-item px-3 mb-2">
            <div class="nav-link text-white d-flex align-items-center justify-content-between cursor-pointer" 
                 onclick="toggleSubmenu('employeeSubmenu')" style="transition: all 0.3s; cursor: pointer;">
                <div>
                    <i class="bi bi-people me-3"></i> Employee
                </div>
                <i class="bi bi-chevron-down"></i>
            </div>
            <ul id="employeeSubmenu" class="nav flex-column ms-4 mt-2" style="display: <?php echo in_array($current_page, ['employee.php', 'jobs.php']) ? 'block' : 'none'; ?>;">
                <li class="nav-item">
                    <a href="../page/employee.php" class="nav-link text-white d-flex align-items-center <?php echo $current_page == 'employee.php' ? 'active bg-white bg-opacity-10 rounded' : ''; ?>" style="transition: all 0.3s;">
                        <i class="bi bi-person me-3"></i> Employee List
                    </a>
                </li>
                <li class="nav-item">
                    <a href="../page/jobs.php" class="nav-link text-white d-flex align-items-center <?php echo $current_page == 'jobs.php' ? 'active bg-white bg-opacity-10 rounded' : ''; ?>" style="transition: all 0.3s;">
                        <i class="bi bi-briefcase me-3"></i> Job Titles
                    </a>
                </li>
            </ul>
        </li>
        <li class="nav-item px-3 mb-2">
            <div class="nav-link text-white d-flex align-items-center justify-content-between cursor-pointer" 
                 onclick="toggleSubmenu('productSubmenu')" style="transition: all 0.3s; cursor: pointer;">
                <div>
                    <i class="bi bi-box-seam me-3"></i> Product
                </div>
                <i class="bi bi-chevron-down"></i>
            </div>
            <ul id="productSubmenu" class="nav flex-column ms-4 mt-2" style="display: <?php echo in_array($current_page, ['product.php', 'category.php']) ? 'block' : 'none'; ?>;">
                <li class="nav-item">
                    <a href="../page/product.php" class="nav-link text-white d-flex align-items-center <?php echo $current_page == 'product.php' ? 'active bg-white bg-opacity-10 rounded' : ''; ?>" style="transition: all 0.3s;">
                        <i class="bi bi-box me-3"></i> Product List
                    </a>
                </li>
                <li class="nav-item">
                    <a href="../page/category.php" class="nav-link text-white d-flex align-items-center <?php echo $current_page == 'category.php' ? 'active bg-white bg-opacity-10 rounded' : ''; ?>" style="transition: all 0.3s;">
                        <i class="bi bi-tag me-3"></i> Categories
                    </a>
                </li>
            </ul>
        </li>
        <li class="nav-item px-3 mb-2">
            <a href="../page/transaction.php" class="nav-link text-white d-flex align-items-center <?php echo $current_page == 'transaction.php' ? 'active bg-white bg-opacity-10 rounded' : ''; ?>" style="transition: all 0.3s;">
                <i class="bi bi-arrow-left-right me-3"></i> Transaction
            </a>
        </li>
        <li class="nav-item px-3 mb-2">
            <a href="../page/supplier.php" class="nav-link text-white d-flex align-items-center <?php echo $current_page == 'supplier.php' ? 'active bg-white bg-opacity-10 rounded' : ''; ?>" style="transition: all 0.3s;">
                <i class="bi bi-truck me-3"></i> Supplier
            </a>
        </li>
        <li class="nav-item px-3 mb-2">
            <a href="../page/accounts.php" class="nav-link text-white d-flex align-items-center <?php echo $current_page == 'accounts.php' ? 'active bg-white bg-opacity-10 rounded' : ''; ?>" style="transition: all 0.3s;">
                <i class="bi bi-person-circle me-3"></i> Accounts
            </a>
        </li>
    </ul>
</div>

<style>
.nav-link:hover {
    background-color: rgba(255, 255, 255, 0.1);
    transform: translateX(5px);
    border-radius: 5px;
}
.cursor-pointer {
    cursor: pointer;
}
</style>

<script>
function toggleSubmenu(id) {
    const submenu = document.getElementById(id);
    submenu.style.display = submenu.style.display === 'none' ? 'block' : 'none';
}
</script>