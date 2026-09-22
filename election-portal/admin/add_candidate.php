<?php
require_once '../config.php';
header('X-Content-Type-Options: nosniff');

// Check if user is logged in and is an admin
if (!isset($_SESSION['user_id'])) {
    header("Location: ../access");
    exit();
}

$stmt = $pdo->prepare("SELECT * FROM admin_users WHERE user_id = ?");
$stmt->execute([$_SESSION['user_id']]);
$admin = $stmt->fetch();

if (!$admin) {
    header("Location: ../dashboard");
    exit();
}

// Get election ID from URL
$election_id = isset($_GET['election_id']) ? (int)$_GET['election_id'] : 0;

// Verify election exists and is active
$stmt = $pdo->prepare("SELECT * FROM elections WHERE id = ? AND status = 'active'");
$stmt->execute([$election_id]);
$election = $stmt->fetch();

if (!$election) {
    header("Location: dashboard");
    exit();
}

// Get all positions
$stmt = $pdo->prepare("SELECT * FROM elections_positions ORDER BY title");
$stmt->execute();
$positions = $stmt->fetchAll();

// Handle form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    header('Content-Type: application/json');
    if (!validateCSRFToken($_POST['csrf_token'])) {
        echo json_encode(['success' => false, 'message' => 'Invalid request']);
        exit();
    }

    $name = sanitizeInput($_POST['name']);
    $position_id = (int)$_POST['position_id'];
    $short_description = sanitizeInput($_POST['short_description']);
    $bio = sanitizeInput($_POST['bio']);
    
    // Handle file upload
    $photo_url = null;
    if (isset($_FILES['photo']) && $_FILES['photo']['error'] === UPLOAD_ERR_OK) {
        $upload_dir = '../uploads/candidates/';
        if (!file_exists($upload_dir)) {
            mkdir($upload_dir, 0777, true);
        }
        
        $file_extension = strtolower(pathinfo($_FILES['photo']['name'], PATHINFO_EXTENSION));
        $allowed_extensions = ['jpg', 'jpeg', 'png'];
        
        if (in_array($file_extension, $allowed_extensions)) {
            $new_filename = uniqid() . '.' . $file_extension;
            $upload_path = $upload_dir . $new_filename;
            
            if (move_uploaded_file($_FILES['photo']['tmp_name'], $upload_path)) {
                $photo_url = 'uploads/candidates/' . $new_filename;
            }
        }
    }
    
    // Insert candidate
    $stmt = $pdo->prepare("
        INSERT INTO elections_candidates 
        (election_id, position_id, name, photo_url, short_description, bio) 
        VALUES (?, ?, ?, ?, ?, ?)
    ");
    
    if ($stmt->execute([$election_id, $position_id, $name, $photo_url, $short_description, $bio])) {
        echo json_encode(['success' => true, 'message' => 'Candidate added successfully']);
        exit();
    } else {
        echo json_encode(['success' => false, 'message' => 'Failed to add candidate. Please try again.']);
        exit();
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>ACSES Election Portal - Add Candidate</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
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
        .preview-image {
            max-width: 200px;
            max-height: 200px;
            object-fit: cover;
            border-radius: 10px;
            display: none;
        }
    </style>
    <script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script src="../assets/js/acses-theme.js.php"></script>
    <script src="../assets/js/acses.js"></script>
</head>
<body>
    <nav class="navbar navbar-expand-lg navbar-dark mb-4">
        <div class="container">
            <a class="navbar-brand" href="#">
                <img src="../logo.png" alt="ACSES Logo">
                ACSES Election Portal - Admin
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
                </ul>
            </div>
        </div>
    </nav>

    <div class="container">
        <div class="row justify-content-center">
            <div class="col-md-8">
                <div class="card">
                    <div class="card-body">
                        <h3 class="card-title mb-4">Add New Candidate</h3>
                        
                        <?php if (isset($error)): ?>
                            <div class="alert alert-danger" role="alert">
                                <i class="fas fa-exclamation-circle"></i> <?php echo $error; ?>
                            </div>
                        <?php endif; ?>

                        <form method="POST" action="" enctype="multipart/form-data" class="needs-validation" novalidate>
                            <input type="hidden" name="csrf_token" value="<?php echo generateCSRFToken(); ?>">
                            
                            <div class="mb-3">
                                <label for="name" class="form-label">Full Name</label>
                                <input type="text" class="form-control" id="name" name="name" required>
                            </div>

                            <div class="mb-3">
                                <label for="position_id" class="form-label">Position</label>
                                <select class="form-select" id="position_id" name="position_id" required>
                                    <option value="">Select Position</option>
                                    <?php foreach ($positions as $position): ?>
                                        <option value="<?php echo $position['id']; ?>">
                                            <?php echo htmlspecialchars($position['title']); ?>
                                        </option>
                                    <?php endforeach; ?>
                                </select>
                            </div>

                            <div class="mb-3">
                                <label for="photo" class="form-label">Profile Photo</label>
                                <input type="file" class="form-control" id="photo" name="photo" accept="image/*" required>
                                <div class="mt-2">
                                    <img id="preview" class="preview-image" alt="Preview">
                                </div>
                            </div>

                            <div class="mb-3">
                                <label for="short_description" class="form-label">Short Description</label>
                                <textarea class="form-control" id="short_description" name="short_description" rows="2" required></textarea>
                                <div class="form-text">A brief description that will be shown on the voting page.</div>
                            </div>

                            <div class="mb-3">
                                <label for="bio" class="form-label">Full Biography</label>
                                <textarea class="form-control" id="bio" name="bio" rows="4"></textarea>
                                <div class="form-text">Detailed biography of the candidate.</div>
                            </div>

                            <div class="text-center">
                                <button type="submit" class="btn btn-primary">
                                    <i class="fas fa-save"></i> Add Candidate
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
        // Image preview
        document.getElementById('photo').addEventListener('change', function(e) {
            const preview = document.getElementById('preview');
            const file = e.target.files[0];
            
            if (file) {
                const reader = new FileReader();
                reader.onload = function(e) {
                    preview.src = e.target.result;
                    preview.style.display = 'block';
                }
                reader.readAsDataURL(file);
            }
        });

        // Form validation
        (function() {
            'use strict';
            const forms = document.querySelectorAll('.needs-validation');
            Array.from(forms).forEach(form => {
                form.addEventListener('submit', event => {
                    if (!form.checkValidity()) {
                        event.preventDefault();
                        event.stopPropagation();
                    }
                    form.classList.add('was-validated');
                }, false);
            });
        })();

        $(function() {
            acsesAjaxForm($("form.needs-validation"), function(resp) {
                setTimeout(function() { window.location.href = 'dashboard'; }, 1500);
            });
        });
    </script>
</body>
</html> 