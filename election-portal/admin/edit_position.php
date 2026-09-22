<?php
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
    header("Location: ../access");
    exit();
}

// Get user information
$stmt = $pdo->prepare("SELECT * FROM users WHERE user_id = ? AND role = 'admin'");
$stmt->execute([$_SESSION['user_id']]);
$admin = $stmt->fetch();

if (!$admin) {
    header("Location: ../access");
    exit();
}

// Get position ID from URL
$position_id = isset($_GET['id']) ? (int)$_GET['id'] : 0;

// Get position details
$stmt = $pdo->prepare("
    SELECT p.*, e.title as election_title 
    FROM elections_positions p 
    JOIN elections e ON p.election_id = e.id 
    WHERE p.id = ?
");
$stmt->execute([$position_id]);
$position = $stmt->fetch();

if (!$position) {
    header("Location: dashboard");
    exit();
}

// Handle form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    header('Content-Type: application/json');
    if (!validateCSRFToken($_POST['csrf_token'])) {
        echo json_encode(['success' => false, 'message' => 'Invalid request']);
        exit();
    }

    $title = sanitizeInput($_POST['title']);
    $description = sanitizeInput($_POST['description']);
    $max_winners = (int)$_POST['max_winners'];
    
    // Update position
    $stmt = $pdo->prepare("
        UPDATE elections_positions 
        SET title = ?, description = ?, max_winners = ?
        WHERE id = ?
    ");
    
    if ($stmt->execute([$title, $description, $max_winners, $position_id])) {
        echo json_encode(['success' => true, 'message' => 'Position updated successfully']);
        exit();
    } else {
        echo json_encode(['success' => false, 'message' => 'Failed to update position. Please try again.']);
        exit();
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>ACSES Election Portal - Edit Position</title>
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
                        <h5 class="mb-0">Edit Position</h5>
                    </div>
                    <div class="card-body">
                        <form id="editPositionForm" class="needs-validation" novalidate>
                            <input type="hidden" name="csrf_token" value="<?php echo generateCSRFToken(); ?>">
                            
                            <div class="mb-3">
                                <label for="title" class="form-label">Position Title</label>
                                <input type="text" class="form-control" id="title" name="title" 
                                       value="<?php echo htmlspecialchars($position['title']); ?>" required>
                                <div class="invalid-feedback">Please enter a position title.</div>
                            </div>
                            
                            <div class="mb-3">
                                <label for="description" class="form-label">Description</label>
                                <textarea class="form-control" id="description" name="description" 
                                          rows="4" required><?php echo htmlspecialchars($position['description']); ?></textarea>
                                <div class="invalid-feedback">Please enter a position description.</div>
                            </div>
                            
                            <div class="mb-3">
                                <label for="max_winners" class="form-label">Maximum Winners</label>
                                <input type="number" class="form-control" id="max_winners" name="max_winners" 
                                       value="<?php echo (int)$position['max_winners']; ?>" min="1" required>
                                <div class="invalid-feedback">Please enter a valid number of maximum winners.</div>
                            </div>
                            
                            <div class="text-center">
                                <button type="submit" class="btn btn-primary">
                                    <i class="fas fa-save"></i> Update Position
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
            $('#editPositionForm').on('submit', function(e) {
                e.preventDefault();
                
                if (!this.checkValidity()) {
                    e.stopPropagation();
                    $(this).addClass('was-validated');
                    return;
                }
                
                const formData = new FormData(this);
                
                $.ajax({
                    url: 'edit_position',
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