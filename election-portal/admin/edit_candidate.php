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

// Get candidate ID from URL
$candidate_id = isset($_GET['id']) ? (int)$_GET['id'] : 0;

// Get candidate details
$stmt = $pdo->prepare("
    SELECT c.*, e.title as election_title, p.title as position_title 
    FROM elections_candidates c 
    JOIN elections e ON c.election_id = e.id 
    JOIN elections_positions p ON c.position_id = p.id 
    WHERE c.id = ?
");
$stmt->execute([$candidate_id]);
$candidate = $stmt->fetch();

if (!$candidate) {
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
    $photo_url = $candidate['photo_url'];
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
                // Delete old photo if exists
                if ($photo_url && file_exists('../' . $photo_url)) {
                    unlink('../' . $photo_url);
                }
                $photo_url = 'uploads/candidates/' . $new_filename;
            }
        }
    }
    
    // Update candidate
    $stmt = $pdo->prepare("
        UPDATE elections_candidates 
        SET name = ?, position_id = ?, photo_url = ?, short_description = ?, bio = ?
        WHERE id = ?
    ");
    
    if ($stmt->execute([$name, $position_id, $photo_url, $short_description, $bio, $candidate_id])) {
        echo json_encode(['success' => true, 'message' => 'Candidate updated successfully']);
        exit();
    } else {
        echo json_encode(['success' => false, 'message' => 'Failed to update candidate. Please try again.']);
        exit();
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>ACSES Election Portal - Edit Candidate</title>
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
        
        .preview-image {
            max-width: 200px;
            max-height: 200px;
            object-fit: cover;
            border-radius: 10px;
            border: 3px solid var(--primary-color);
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
                        <h5 class="mb-0">Edit Candidate</h5>
                    </div>
                    <div class="card-body">
                        <form id="editCandidateForm" class="needs-validation" novalidate>
                            <input type="hidden" name="csrf_token" value="<?php echo generateCSRFToken(); ?>">
                            
                            <div class="mb-3">
                                <label for="name" class="form-label">Candidate Name</label>
                                <input type="text" class="form-control" id="name" name="name" 
                                       value="<?php echo htmlspecialchars($candidate['name']); ?>" required>
                                <div class="invalid-feedback">Please enter the candidate's name.</div>
                            </div>
                            
                            <div class="mb-3">
                                <label for="position_id" class="form-label">Position</label>
                                <select class="form-control" id="position_id" name="position_id" required>
                                    <?php foreach ($positions as $position): ?>
                                        <option value="<?php echo $position['id']; ?>" 
                                                <?php echo $position['id'] === $candidate['position_id'] ? 'selected' : ''; ?>>
                                            <?php echo htmlspecialchars($position['title']); ?>
                                        </option>
                                    <?php endforeach; ?>
                                </select>
                                <div class="invalid-feedback">Please select a position.</div>
                            </div>
                            
                            <div class="mb-3">
                                <label for="photo" class="form-label">Photo</label>
                                <input type="file" class="form-control" id="photo" name="photo" accept="image/*">
                                <div class="invalid-feedback">Please select a valid image file.</div>
                                <?php if ($candidate['photo_url']): ?>
                                    <div class="mt-2">
                                        <img src="../<?php echo htmlspecialchars($candidate['photo_url']); ?>" 
                                             alt="Current photo" class="preview-image">
                                    </div>
                                <?php endif; ?>
                            </div>
                            
                            <div class="mb-3">
                                <label for="short_description" class="form-label">Short Description</label>
                                <textarea class="form-control" id="short_description" name="short_description" 
                                          rows="2" required><?php echo htmlspecialchars($candidate['short_description']); ?></textarea>
                                <div class="invalid-feedback">Please enter a short description.</div>
                            </div>
                            
                            <div class="mb-3">
                                <label for="bio" class="form-label">Bio</label>
                                <textarea class="form-control" id="bio" name="bio" 
                                          rows="4"><?php echo htmlspecialchars($candidate['bio']); ?></textarea>
                            </div>
                            
                            <div class="text-center">
                                <button type="submit" class="btn btn-primary">
                                    <i class="fas fa-save"></i> Update Candidate
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
            // Preview image before upload
            $('#photo').on('change', function() {
                const file = this.files[0];
                if (file) {
                    const reader = new FileReader();
                    reader.onload = function(e) {
                        $('.preview-image').attr('src', e.target.result).show();
                    }
                    reader.readAsDataURL(file);
                }
            });
            
            $('#editCandidateForm').on('submit', function(e) {
                e.preventDefault();
                
                if (!this.checkValidity()) {
                    e.stopPropagation();
                    $(this).addClass('was-validated');
                    return;
                }
                
                const formData = new FormData(this);
                
                $.ajax({
                    url: 'edit_candidate',
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