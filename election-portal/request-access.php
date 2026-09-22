<?php
require_once 'config.php';
header('X-Content-Type-Options: nosniff');

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    header('Content-Type: application/json');
    if (!validateCSRFToken($_POST['csrf_token'])) {
        echo json_encode(['success' => false, 'message' => 'Invalid request']);
        exit();
    }
    $email = filter_var($_POST['email'], FILTER_SANITIZE_EMAIL);
    
    if (filter_var($email, FILTER_VALIDATE_EMAIL)) {
        // Check if email exists in users table
        $stmt = $pdo->prepare("SELECT * FROM users WHERE email = ?");
        $stmt->execute([$email]);
        $user = $stmt->fetch();
        
        if ($user) {
            // Generate access link
            $accessLink = generateAccessLink($email);
            
            // Send email
            if (sendAccessLinkEmail($email, $accessLink)) {
                echo json_encode(['success' => true, 'message' => 'Access link sent to your email']);
                exit();
            } else {
                echo json_encode(['success' => false, 'message' => 'Failed to send access link. Please try again.']);
                exit();
            }
        } else {
            echo json_encode(['success' => false, 'message' => 'No account found with this email address.']);
            exit();
        }
    } else {
        echo json_encode(['success' => false, 'message' => 'Please enter a valid email address.']);
        exit();
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>ACSES Election Portal - Request Access</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
    <script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script src="assets/js/acses-theme.js"></script>
    <script src="assets/js/acses.js"></script>
    <style>
        body {
            background-color: #f8f9fa;
        }
        .navbar {
            background: linear-gradient(135deg, <?php echo PRIMARY_COLOR; ?> 0%, <?php echo SECONDARY_COLOR; ?> 100%);
        }
        .navbar-brand img {
            height: 40px;
            margin-right: 10px;
        }
        .card {
            border: none;
            border-radius: 15px;
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
        }
        .btn-primary {
            background-color: <?php echo SECONDARY_COLOR; ?>;
            border-color: <?php echo SECONDARY_COLOR; ?>;
            color: <?php echo WHITE_COLOR; ?>;
        }
        .btn-primary:hover {
            background-color: <?php echo PRIMARY_COLOR; ?>;
            border-color: <?php echo PRIMARY_COLOR; ?>;
        }
    </style>
</head>
<body>
    <nav class="navbar navbar-expand-lg navbar-dark mb-4">
        <div class="container">
            <a class="navbar-brand" href="#">
                <img src="logo.png" alt="ACSES Logo">
                ACSES Election Portal
            </a>
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav">
                <span class="navbar-toggler-icon"></span>
            </button>
            <div class="collapse navbar-collapse" id="navbarNav">
                <ul class="navbar-nav ms-auto">
                    <li class="nav-item">
                        <a class="nav-link" href="access">
                            <i class="fas fa-sign-in-alt"></i> Login
                        </a>
                    </li>
                </ul>
            </div>
        </div>
    </nav>

    <div class="container">
        <div class="row justify-content-center">
            <div class="col-md-6">
                <div class="card">
                    <div class="card-body">
                        <h3 class="card-title mb-4">Request Access Link</h3>
                        <form method="POST" action="" class="needs-validation" novalidate>
                            <input type="hidden" name="csrf_token" value="<?php echo generateCSRFToken(); ?>">
                            <div class="mb-3">
                                <label for="email" class="form-label">Email Address</label>
                                <input type="email" class="form-control" id="email" name="email" required>
                            </div>
                            <div class="text-center">
                                <button type="submit" class="btn btn-primary">
                                    <i class="fas fa-paper-plane"></i> Send Access Link
                                </button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        $(function() {
            acsesAjaxForm($("form.needs-validation"), function(resp) {
                setTimeout(function() { window.location.href = 'access'; }, 1500);
            });
        });
    </script>
</body>
</html> 