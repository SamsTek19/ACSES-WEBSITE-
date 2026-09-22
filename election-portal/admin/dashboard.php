<?php
require_once '../config.php';

// Debug logging
error_log("Admin dashboard access attempt - Session data: " . print_r($_SESSION, true));

// Check if user is logged in and is an admin
if (!isset($_SESSION['user_id']) || !isset($_SESSION['logged_in']) || $_SESSION['logged_in'] !== true || $_SESSION['role'] !== 'admin') {
    error_log("Admin session validation failed - Redirecting to access.php");
    header("Location: ../access");
    exit();
}

// Get user information
$stmt = $pdo->prepare("SELECT * FROM users WHERE user_id = ? AND role = 'admin'");
$stmt->execute([$_SESSION['user_id']]);
$admin = $stmt->fetch();

if (!$admin) {
    error_log("Admin user not found in database - Clearing session and redirecting");
    session_unset();
    session_destroy();
    header("Location: ../access");
    exit();
}

error_log("Admin authenticated successfully - Proceeding to admin dashboard");

// Get active elections
$stmt = $pdo->prepare("SELECT * FROM elections WHERE status = 'active'");
$stmt->execute();
$elections = $stmt->fetchAll();

// Get all positions
$stmt = $pdo->prepare("SELECT * FROM elections_positions ORDER BY title");
$stmt->execute();
$positions = $stmt->fetchAll();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>ACSES Election Portal - Admin</title>
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
        
        .table {
            margin-bottom: 0;
        }
        
        .table th {
            background-color: rgba(18, 72, 36, 0.1);
            color: var(--primary-color);
            font-weight: 600;
            border: none;
        }
        
        .table td {
            vertical-align: middle;
            border-color: rgba(18, 72, 36, 0.1);
        }
        
        .candidate-photo {
            width: 50px;
            height: 50px;
            object-fit: cover;
            border-radius: 50%;
            border: 2px solid var(--primary-color);
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
        
        .btn-action {
            padding: 0.5rem 1rem;
            border-radius: 8px;
            transition: all 0.3s ease;
        }
        
        .btn-action:hover {
            transform: translateY(-2px);
        }
        
        .btn-edit {
            background-color: var(--primary-color);
            color: var(--white);
        }
        
        .btn-delete {
            background-color: #dc3545;
            color: var(--white);
        }
        
        .btn-add {
            background-color: var(--secondary-color);
            color: var(--white);
        }
        
        .btn-add:hover {
            background-color: var(--primary-color);
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
                        <a class="nav-link" href="../dashboard">
                            <i class="fas fa-arrow-left"></i> Back to Portal
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
        <div class="row">
            <div class="col-md-12 mb-4">
                <div class="card">
                    <div class="card-body">
                        <h3 class="card-title">Manage Elections</h3>
                        <a href="create_election" class="btn btn-add">
                            <i class="fas fa-plus"></i> Create New Election
                        </a>
                    </div>
                </div>
            </div>
        </div>

        <?php if (empty($elections)): ?>
            <div class="alert alert-info">
                <i class="fas fa-info-circle"></i> No active elections found.
            </div>
        <?php else: ?>
            <?php foreach ($elections as $election): ?>
                <div class="card">
                    <div class="card-header d-flex justify-content-between align-items-center">
                        <h5 class="mb-0"><?php echo htmlspecialchars($election['title']); ?></h5>
                        <div>
                            <a href="add_candidate?election_id=<?php echo $election['id']; ?>" class="btn btn-add btn-action">
                                <i class="fas fa-user-plus"></i> Add Candidate
                            </a>
                        </div>
                    </div>
                    <div class="card-body">
                        <div class="table-responsive">
                            <table class="table">
                                <thead>
                                    <tr>
                                        <th>Photo</th>
                                        <th>Name</th>
                                        <th>Position</th>
                                        <th>Description</th>
                                        <th>Actions</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php
                                    $stmt = $pdo->prepare("
                                        SELECT c.*, p.title as position_title 
                                        FROM elections_candidates c 
                                        JOIN elections_positions p ON c.position_id = p.id 
                                        WHERE c.election_id = ?
                                    ");
                                    $stmt->execute([$election['id']]);
                                    $candidates = $stmt->fetchAll();
                                    
                                    foreach ($candidates as $candidate):
                                    ?>
                                        <tr>
                                            <td>
                                                <img src="<?php echo htmlspecialchars($candidate['photo_url'] ?? '../default-avatar.png'); ?>" 
                                                     alt="<?php echo htmlspecialchars($candidate['name']); ?>"
                                                     class="candidate-photo">
                                            </td>
                                            <td><?php echo htmlspecialchars($candidate['name']); ?></td>
                                            <td><?php echo htmlspecialchars($candidate['position_title']); ?></td>
                                            <td><?php echo htmlspecialchars($candidate['short_description']); ?></td>
                                            <td>
                                                <a href="edit_candidate?id=<?php echo $candidate['id']; ?>" class="btn btn-edit btn-action">
                                                    <i class="fas fa-edit"></i>
                                                </a>
                                                <button type="button" class="btn btn-delete btn-action" 
                                                        onclick="deleteCandidate(<?php echo $candidate['id']; ?>)">
                                                    <i class="fas fa-trash"></i>
                                                </button>
                                            </td>
                                        </tr>
                                    <?php endforeach; ?>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            <?php endforeach; ?>
        <?php endif; ?>

        <!-- Election Date & Time Setup -->
        <div class="card">
            <div class="card-header">
                <h5 class="mb-0">Election Date & Time Setup</h5>
            </div>
            <div class="card-body">
                <form method="POST" action="update_election_time" class="needs-validation" novalidate>
                    <input type="hidden" name="csrf_token" value="<?php echo generateCSRFToken(); ?>">
                    <div class="row">
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="start_date" class="form-label">Start Date & Time</label>
                                <input type="datetime-local" class="form-control" id="start_date" name="start_date" required>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="end_date" class="form-label">End Date & Time</label>
                                <input type="datetime-local" class="form-control" id="end_date" name="end_date" required>
                            </div>
                        </div>
                    </div>
                    <button type="submit" class="btn btn-primary">Save Election Time</button>
                </form>
            </div>
        </div>
    </div>

    <script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script>
        function deleteCandidate(candidateId) {
            Swal.fire({
                title: 'Are you sure?',
                text: "You won't be able to revert this!",
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#124824',
                cancelButtonColor: '#dc3545',
                confirmButtonText: 'Yes, delete it!'
            }).then((result) => {
                if (result.isConfirmed) {
                    // Add your delete logic here
                    Swal.fire(
                        'Deleted!',
                        'Candidate has been removed.',
                        'success'
                    );
                }
            });
        }

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
    </script>
</body>
</html> 