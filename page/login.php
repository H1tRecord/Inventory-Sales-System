<?php
require_once '../controller/login_controller.php';
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login - Inventory & Sales System</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons/font/bootstrap-icons.css" rel="stylesheet">
    <style>
        @keyframes slideIn {
            from { 
                transform: translateX(-30px); 
                opacity: 0; 
            }
            to { 
                transform: translateX(0); 
                opacity: 1; 
            }
        }
        .animate-slideIn {
            animation: slideIn 1.2s cubic-bezier(0.165, 0.84, 0.44, 1) forwards;
            animation-iteration-count: 1;
        }
        .bg-gradient-primary {
            background: linear-gradient(135deg, #0d6efd 0%, #0a4cd7 100%);
        }
    </style>
</head>
<body class="min-vh-100">
    <div class="container-fluid min-vh-100">
        <div class="row min-vh-100">
            <!-- Left side - Title Card -->
            <div class="col-lg-6 d-none d-lg-flex bg-gradient-primary text-white p-5">
                <div class="my-auto mx-auto animate-slideIn" style="max-width: 32rem;">
                    <h1 class="display-4 fw-bold mb-4">Inventory & Sales System</h1>
                    <p class="lead mb-5">
                        Streamline your business operations with our comprehensive inventory management 
                        and point-of-sale solution. Track stock levels, monitor sales, and make informed 
                        decisions with real-time analytics.
                    </p>
                </div>
            </div>

            <!-- Right side - Login Form -->
            <div class="col-lg-6 d-flex align-items-center justify-content-center p-5">
                <div class="w-100" style="max-width: 28rem;">
                    <div class="text-center mb-5">
                        <h2 class="h1 fw-bold text-dark">Welcome Back</h2>
                        <p class="text-muted">Please sign in to continue</p>
                    </div>

                    <?php if (isset($error)): ?>
                        <div class="alert alert-danger d-flex align-items-center" role="alert">
                            <i class="bi bi-exclamation-triangle-fill me-2"></i>
                            <div><?= $error ?></div>
                        </div>
                    <?php endif; ?>

                    <form method="POST" action="" class="needs-validation" novalidate>
                        <div class="mb-4">
                            <label for="username" class="form-label">Username</label>
                            <div class="input-group">
                                <span class="input-group-text"><i class="bi bi-person"></i></span>
                                <input type="text" class="form-control" id="username" name="username" 
                                       placeholder="Enter your username" required>
                            </div>
                        </div>

                        <div class="mb-4">
                            <label for="password" class="form-label">Password</label>
                            <div class="input-group">
                                <span class="input-group-text"><i class="bi bi-lock"></i></span>
                                <input type="password" class="form-control" id="password" name="password" 
                                       placeholder="Enter your password" required>
                            </div>
                        </div>

                        <button type="submit" class="btn btn-primary w-100 py-2 mb-4 fw-bold">
                            Sign In
                        </button>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
