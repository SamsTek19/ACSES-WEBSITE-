<?php
// Enable error reporting
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

// Set error logging
ini_set('log_errors', 1);
ini_set('error_log', '../logs/php-error.log');

require_once '../config.php';
header('X-Content-Type-Options: nosniff');

// Function to sanitize input
function sanitizeInput($data) {
    $data = trim($data);
    $data = stripslashes($data);
    $data = htmlspecialchars($data);
    return $data;
}

// Check if user is logged in and is an admin
if (!isset($_SESSION['user_id']) || !isset($_SESSION['logged_in']) || $_SESSION['logged_in'] !== true || $_SESSION['role'] !== 'admin') {
    error_log("Access Denied - Session validation failed. Session data: " . print_r($_SESSION, true));
    header("Location: ../access");
    exit();
}

// Get user information
try {
    $stmt = $pdo->prepare("SELECT * FROM users WHERE user_id = ? AND role = 'admin'");
    $stmt->execute([$_SESSION['user_id']]);
    $admin = $stmt->fetch();

    if (!$admin) {
        error_log("Access Denied - Admin user not found for user_id: " . $_SESSION['user_id']);
        header("Location: ../access");
        exit();
    }
} catch (PDOException $e) {
    error_log("Database Error: " . $e->getMessage());
    die("A database error occurred. Please try again later.");
}

// Handle form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    try {
        header('Content-Type: application/json');
        if (!validateCSRFToken($_POST['csrf_token'])) {
            error_log("Invalid CSRF token");
            echo json_encode(['success' => false, 'message' => 'Invalid request']);
            exit();
        }

        $title = sanitizeInput($_POST['title']);
        $description = sanitizeInput($_POST['description']);
        $start_date = sanitizeInput($_POST['start_date']);
        $end_date = sanitizeInput($_POST['end_date']);
        $status = sanitizeInput($_POST['status']);

        // Validate dates
        if (strtotime($start_date) >= strtotime($end_date)) {
            echo json_encode(['success' => false, 'message' => 'End date must be after start date']);
            exit();
        }

        // Create election
        $stmt = $pdo->prepare("
            INSERT INTO elections (title, description, start_date, end_date, status)
            VALUES (?, ?, ?, ?, ?)
        ");
        
        if ($stmt->execute([$title, $description, $start_date, $end_date, $status])) {
            echo json_encode(['success' => true, 'message' => 'Election created successfully']);
            exit();
        } else {
            error_log("Failed to create election");
            echo json_encode(['success' => false, 'message' => 'Failed to create election. Please try again.']);
            exit();
        }
    } catch (Exception $e) {
        error_log("Form Submission Error: " . $e->getMessage());
        echo json_encode(['success' => false, 'message' => 'An error occurred. Please try again.']);
        exit();
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>ACSES Election Portal - Create Election</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
    <style>
        :root {
            --primary-color: #124824;
            --secondary-color: #f9a915;
            --text-color: #333;
            --white: #FFFFFF;
        }
        
        body {
            font-family: Arial, sans-serif;
            background: linear-gradient(135deg, rgba(18, 72, 36, 0.1) 0%, rgba(249, 169, 21, 0.1) 100%);
            min-height: 100vh;
        }
        
        .navbar {
            background-color: var(--primary-color);
            padding: 1rem 0;
            box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
        }
        
        .navbar-brand img {
            height: 40px;
            width: auto;
        }
        
        .nav-link {
            color: var(--white) !important;
            font-weight: 500;
            transition: all 0.3s ease;
        }
        
        .nav-link:hover {
            color: var(--secondary-color) !important;
        }
        
        .card {
            border: none;
            border-radius: 15px;
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
            transition: transform 0.3s ease;
            margin-bottom: 1.5rem;
        }
        
        .card:hover {
            transform: translateY(-5px);
        }
        
        .card-header {
            background-color: var(--primary-color);
            color: var(--white);
            border-radius: 15px 15px 0 0 !important;
            padding: 1rem;
        }
        
        .btn-primary {
            background-color: var(--primary-color);
            border: none;
            padding: 0.75rem 1.5rem;
            font-weight: 600;
            border-radius: 10px;
            transition: all 0.3s ease;
        }
        
        .btn-primary:hover {
            background-color: var(--secondary-color);
            transform: translateY(-2px);
        }
        
        .form-control {
            border: 2px solid rgba(18, 72, 36, 0.1);
            border-radius: 10px;
            padding: 0.75rem 1rem;
            transition: all 0.3s ease;
        }
        
        .form-control:focus {
            border-color: var(--primary-color);
            box-shadow: 0 0 0 0.2rem rgba(18, 72, 36, 0.15);
        }
        
        .form-label {
            color: var(--text-color);
            font-weight: 500;
            margin-bottom: 0.5rem;
        }
        
        .alert {
            border-radius: 10px;
            border: none;
        }
        
        .alert-success {
            background-color: rgba(18, 72, 36, 0.1);
            color: var(--primary-color);
        }
        
        .alert-info {
            background-color: rgba(249, 169, 21, 0.1);
            color: var(--secondary-color);
        }
    </style>
</head>
<body>
    <nav class="navbar navbar-expand-lg navbar-dark mb-4">
        <div class="container">
            <a class="navbar-brand" href="#">
                <img src="../logo.png" alt="ACSES Logo">
            </a>
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav">
                <span class="navbar-toggler-icon"></span>
            </button>
            <div class="collapse navbar-collapse" id="navbarNav">
                <ul class="navbar-nav ms-auto">
                    <li class="nav-item">
                        <a class="nav-link" href="dashboard">
                            <i class="fas fa-arrow-left"></i> Back to Dashboard
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="../logout">
                            <i class="fas fa-sign-out-alt"></i> Logout
                        </a>
                    </li>
                </ul>
            </div>
        </div>
    </nav>

    <div class="container">
        <div class="row justify-content-center">
            <div class="col-md-8">
                <div class="card">
                    <div class="card-header">
                        <h5 class="mb-0">Create New Election</h5>
                    </div>
                    <div class="card-body">
                        <form id="createElectionForm" class="needs-validation" novalidate>
                            <input type="hidden" name="csrf_token" value="<?php echo generateCSRFToken(); ?>">
                            
                            <div class="mb-3">
                                <label for="title" class="form-label">Election Title</label>
                                <input type="text" class="form-control" id="title" name="title" required>
                                <div class="invalid-feedback">Please enter an election title.</div>
                            </div>
                            
                            <div class="mb-3">
                                <label for="description" class="form-label">Description</label>
                                <textarea class="form-control" id="description" name="description" rows="4" required></textarea>
                                <div class="invalid-feedback">Please enter an election description.</div>
                            </div>
                            
                            <div class="row">
                                <div class="col-md-6">
                                    <div class="mb-3">
                                        <label for="start_date" class="form-label">Start Date & Time</label>
                                        <input type="datetime-local" class="form-control" id="start_date" name="start_date" required>
                                        <div class="invalid-feedback">Please select a start date and time.</div>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="mb-3">
                                        <label for="end_date" class="form-label">End Date & Time</label>
                                        <input type="datetime-local" class="form-control" id="end_date" name="end_date" required>
                                        <div class="invalid-feedback">Please select an end date and time.</div>
                                    </div>
                                </div>
                            </div>
                            
                            <div class="text-center">
                                <button type="submit" class="btn btn-primary">
                                    <i class="fas fa-plus"></i> Create Election
                                </button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script>
        $(document).ready(function() {
            $('#createElectionForm').on('submit', function(e) {
                e.preventDefault();
                
                if (!this.checkValidity()) {
                    e.stopPropagation();
                    $(this).addClass('was-validated');
                    return;
                }
                
                const formData = new FormData(this);
                
                $.ajax({
                    url: 'create_election',
                    type: 'POST',
                    data: formData,
                    processData: false,
                    contentType: false,
                    success: function(response) {
                        if (response.success) {
                            Swal.fire({
                                title: 'Success!',
                                text: response.message,
                                icon: 'success',
                                confirmButtonColor: '#124824'
                            }).then((result) => {
                                if (result.isConfirmed) {
                                    window.location.href = 'dashboard';
                                }
                            });
                        } else {
                            Swal.fire({
                                title: 'Error!',
                                text: response.message,
                                icon: 'error',
                                confirmButtonColor: '#124824'
                            });
                        }
                    },
                    error: function() {
                        Swal.fire({
                            title: 'Error!',
                            text: 'An error occurred. Please try again.',
                            icon: 'error',
                            confirmButtonColor: '#124824'
                        });
                    }
                });
            });
        });
    </script>
</body>
</html> 