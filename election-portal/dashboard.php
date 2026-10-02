<?php
require_once 'config.php';
header('X-Content-Type-Options: nosniff');

// Check if user is logged in
if (!isset($_SESSION['user_id']) || !isset($_SESSION['logged_in']) || $_SESSION['logged_in'] !== true) {
    header("Location: access");
    exit();
}

// Get user information
$stmt = $pdo->prepare("SELECT * FROM users WHERE user_id = ?");
$stmt->execute([$_SESSION['user_id']]);
$user = $stmt->fetch();

if (!$user || $user['role'] !== 'student') {
    session_unset();
    session_destroy();
    header("Location: access");
    exit();
}

// Get active elections
$stmt = $pdo->prepare("
    SELECT e.*, 
           (SELECT COUNT(*) FROM elections_votes WHERE election_id = e.id AND user_id = ?) as has_voted
    FROM elections e 
    WHERE e.status = 'active' 
    AND e.start_date <= NOW() 
    AND e.end_date >= NOW()
");
$stmt->execute([$_SESSION['user_id']]);
$elections = $stmt->fetchAll();

// Handle vote submission
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    header('Content-Type: application/json');
    if (!isset($_POST['csrf_token']) || !validateCSRFToken($_POST['csrf_token'])) {
        echo json_encode(['success' => false, 'message' => 'Invalid request']);
        exit();
    }

    $election_id = filter_input(INPUT_POST, 'election_id', FILTER_VALIDATE_INT);
    $candidate_id = filter_input(INPUT_POST, 'candidate_id', FILTER_VALIDATE_INT);
    if (!$election_id || !$candidate_id) {
        http_response_code(422);
        echo json_encode(['success' => false, 'message' => 'Invalid vote selection']);
        exit();
    }

    $stmt = $pdo->prepare("SELECT id FROM elections WHERE id = ? AND status = 'active' AND start_date <= NOW() AND end_date >= NOW()");
    $stmt->execute([$election_id]);
    if (!$stmt->fetch()) {
        http_response_code(409);
        echo json_encode(['success' => false, 'message' => 'This election is not open for voting']);
        exit();
    }

    $stmt = $pdo->prepare('SELECT id FROM elections_candidates WHERE id = ? AND election_id = ?');
    $stmt->execute([$candidate_id, $election_id]);
    if (!$stmt->fetch()) {
        http_response_code(422);
        echo json_encode(['success' => false, 'message' => 'Candidate does not belong to this election']);
        exit();
    }
    
    // Check if user has already voted
    $stmt = $pdo->prepare("SELECT COUNT(*) FROM elections_votes WHERE election_id = ? AND user_id = ?");
    $stmt->execute([$election_id, $_SESSION['user_id']]);
    if ($stmt->fetchColumn() > 0) {
        echo json_encode(['success' => false, 'message' => 'You have already voted in this election']);
        exit();
    } else {
        // Record the vote
        $stmt = $pdo->prepare("INSERT INTO elections_votes (election_id, user_id, candidate_id, voted_at, ip_address) VALUES (?, ?, ?, NOW(), ?)");
        if ($stmt->execute([$election_id, $_SESSION['user_id'], $candidate_id, $_SERVER['REMOTE_ADDR'] ?? ''])) {
            echo json_encode(['success' => true, 'message' => 'Your vote has been recorded successfully']);
            exit();
        } else {
            echo json_encode(['success' => false, 'message' => 'Failed to record your vote. Please try again.']);
            exit();
        }
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>ACSES Election Portal</title>
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
        
        .candidate-card {
            cursor: pointer;
            border: 2px solid transparent;
            transition: all 0.3s ease;
        }
        
        .candidate-card:hover {
            border-color: var(--primary-color);
        }
        
        .candidate-card.selected {
            border-color: var(--secondary-color);
            background-color: rgba(249, 169, 21, 0.1);
        }
        
        .candidate-card img {
            width: 120px;
            height: 120px;
            object-fit: cover;
            border-radius: 50%;
            margin-bottom: 1rem;
            border: 3px solid var(--primary-color);
        }
        
        .progress {
            height: 10px;
            border-radius: 5px;
            background-color: rgba(18, 72, 36, 0.1);
        }
        
        .progress-bar {
            background-color: var(--primary-color);
            border-radius: 5px;
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
        
        .btn-vote {
            background-color: var(--primary-color);
            color: var(--white);
            border: none;
            padding: 0.75rem 2rem;
            font-weight: 600;
            border-radius: 10px;
            transition: all 0.3s ease;
        }
        
        .btn-vote:hover:not(:disabled) {
            background-color: var(--secondary-color);
            transform: translateY(-2px);
        }
        
        .btn-vote:disabled {
            background-color: #ccc;
            cursor: not-allowed;
        }
        
        #countdown {
            font-weight: 600;
            color: var(--primary-color);
        }
        
        .countdown-warning {
            color: var(--secondary-color) !important;
        }
    </style>
    <script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script src="assets/js/acses-theme.js.php"></script>
    <script src="assets/js/acses.js"></script>
</head>
<body>
    <nav class="navbar navbar-expand-lg navbar-dark mb-4">
        <div class="container">
            <a class="navbar-brand" href="#">
                <img src="logo.png" alt="ACSES Logo">
            </a>
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav">
                <span class="navbar-toggler-icon"></span>
            </button>
            <div class="collapse navbar-collapse" id="navbarNav">
                <ul class="navbar-nav ms-auto">
                    <li class="nav-item">
                        <span class="nav-link">
                            <i class="fas fa-user"></i> <?php echo htmlspecialchars($user['fullname']); ?>
                        </span>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="logout">
                            <i class="fas fa-sign-out-alt"></i> Logout
                        </a>
                    </li>
                </ul>
            </div>
        </div>
    </nav>

    <div class="container">
        <?php if (isset($error)): ?>
            <div class="alert alert-danger alert-dismissible fade show" role="alert">
                <i class="fas fa-exclamation-circle"></i> <?php echo $error; ?>
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        <?php endif; ?>

        <?php if (isset($success)): ?>
            <div class="alert alert-success alert-dismissible fade show" role="alert">
                <i class="fas fa-check-circle"></i> <?php echo $success; ?>
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        <?php endif; ?>

        <h2 class="mb-4">Active Elections</h2>

        <?php if (empty($elections)): ?>
            <div class="alert alert-info">
                <i class="fas fa-info-circle"></i> There are no active elections at the moment.
            </div>
        <?php else: ?>
            <?php foreach ($elections as $election): ?>
                <div class="card mb-4">
                    <div class="card-body">
                        <h3 class="card-title"><?php echo htmlspecialchars($election['title']); ?></h3>
                        <p class="card-text"><?php echo htmlspecialchars($election['description']); ?></p>
                        
                        <?php if ($election['has_voted']): ?>
                            <div class="alert alert-success">
                                <i class="fas fa-check-circle"></i> You have already voted in this election
                            </div>
                        <?php else: ?>
                            <form method="POST" class="needs-validation" novalidate>
                                <input type="hidden" name="csrf_token" value="<?php echo generateCSRFToken(); ?>">
                                <input type="hidden" name="election_id" value="<?php echo $election['id']; ?>">
                                
                                <div class="row">
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
                                        <div class="col-md-4 mb-3">
                                            <div class="card candidate-card" data-candidate-id="<?php echo $candidate['id']; ?>" onclick="selectCandidate(this, <?php echo $candidate['id']; ?>)">
                                                <div class="card-body text-center">
                                                    <img src="<?php echo htmlspecialchars($candidate['photo_url'] ?? 'default-avatar.png'); ?>" 
                                                         alt="<?php echo htmlspecialchars($candidate['name']); ?>" 
                                                         class="mb-3">
                                                    <h5 class="card-title"><?php echo htmlspecialchars($candidate['name']); ?></h5>
                                                    <p class="card-text"><?php echo htmlspecialchars($candidate['position_title']); ?></p>
                                                </div>
                                            </div>
                                        </div>
                                    <?php endforeach; ?>
                                </div>

                                <input type="hidden" name="candidate_id" id="selected_candidate" required>
                                <div class="text-center mt-4">
                                    <button type="submit" name="vote" class="btn btn-vote" disabled>
                                        <i class="fas fa-vote-yea"></i> Submit Vote
                                    </button>
                                </div>
                            </form>
                        <?php endif; ?>
                    </div>
                </div>

                <div class="card mb-4">
                    <div class="card-header">
                        <h5 class="mb-0">Election Status</h5>
                    </div>
                    <div class="card-body">
                        <?php
                        $stmt = $pdo->prepare("SELECT start_date, end_date FROM elections WHERE id = ?");
                        $stmt->execute([$election['id']]);
                        $election = $stmt->fetch();

                        $now = new DateTime();
                        $start = new DateTime($election['start_date']);
                        $end = new DateTime($election['end_date']);

                        if ($now < $start) {
                            echo '<h6>Coming Soon</h6>';
                            echo '<p>Election starts in: <span id="countdown"></span></p>';
                            echo '<div class="progress"><div id="countdown-progress" class="progress-bar" role="progressbar" style="width: 0%"></div></div>';
                        } elseif ($now >= $start && $now <= $end) {
                            echo '<h6>In Progress</h6>';
                            echo '<p>Election ends in: <span id="countdown"></span></p>';
                            echo '<div class="progress"><div id="countdown-progress" class="progress-bar" role="progressbar" style="width: 0%"></div></div>';
                        } else {
                            echo '<h6>Election Ended</h6>';
                        }
                        ?>
                    </div>
                </div>
            <?php endforeach; ?>
        <?php endif; ?>
    </div>

    <div class="modal fade" id="candidateModal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="candidateModalTitle"></h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <div class="row">
                        <div class="col-md-4">
                            <img id="candidateModalImage" class="img-fluid rounded" src="" alt="Candidate Photo">
                        </div>
                        <div class="col-md-8">
                            <h6>Position</h6>
                            <p id="candidateModalPosition"></p>
                            <h6>Short Description</h6>
                            <p id="candidateModalShortDesc"></p>
                            <h6>Biography</h6>
                            <p id="candidateModalBio"></p>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                </div>
            </div>
        </div>
    </div>

    <div class="modal fade" id="resultsModal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Election Results</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <div id="resultsContent"></div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                </div>
            </div>
        </div>
    </div>

    <div class="text-center mb-4">
        <button class="btn btn-primary" onclick="showResults()">
            <i class="fas fa-chart-bar"></i> View Election Results
        </button>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        function selectCandidate(card, candidateId) {
            // Remove selected class from all cards
            document.querySelectorAll('.candidate-card').forEach(c => c.classList.remove('selected'));
            
            // Add selected class to clicked card
            card.classList.add('selected');
            
            // Update hidden input
            document.getElementById('selected_candidate').value = candidateId;
            
            // Enable submit button
            document.querySelector('button[name="vote"]').disabled = false;
        }

        $(function() {
            acsesAjaxForm($("form.needs-validation"), function(resp) {
                setTimeout(function() { window.location.reload(); }, 1500);
            });
        });

        document.querySelectorAll('.candidate-card').forEach(card => {
            card.addEventListener('click', function() {
                const candidateId = this.dataset.candidateId;
                const candidateName = this.querySelector('h5').textContent;
                const candidatePosition = this.querySelector('.position').textContent;
                const candidateShortDesc = this.querySelector('.short-desc').textContent;
                const candidateBio = this.querySelector('.bio').textContent;
                const candidateImage = this.querySelector('img').src;

                document.getElementById('candidateModalTitle').textContent = candidateName;
                document.getElementById('candidateModalPosition').textContent = candidatePosition;
                document.getElementById('candidateModalShortDesc').textContent = candidateShortDesc;
                document.getElementById('candidateModalBio').textContent = candidateBio;
                document.getElementById('candidateModalImage').src = candidateImage;

                new bootstrap.Modal(document.getElementById('candidateModal')).show();
            });
        });

        document.querySelector('button[name="vote"]').addEventListener('click', function(e) {
            e.preventDefault();
            const candidateName = document.querySelector('.candidate-card.selected h5').textContent;
            acsesConfirm('Confirm Vote', `Are you sure you want to vote for ${candidateName}?`, function() {
                document.querySelector('form.needs-validation').submit();
            });
        });

        function showResults() {
            fetch('get_results')
                .then(response => response.json())
                .then(data => {
                    let resultsHtml = '<div class="table-responsive"><table class="table table-striped"><thead><tr><th>Candidate</th><th>Position</th><th>Votes</th></tr></thead><tbody>';
                    data.forEach(candidate => {
                        resultsHtml += `<tr><td>${candidate.name}</td><td>${candidate.position}</td><td>${candidate.votes}</td></tr>`;
                    });
                    resultsHtml += '</tbody></table></div>';
                    document.getElementById('resultsContent').innerHTML = resultsHtml;
                    new bootstrap.Modal(document.getElementById('resultsModal')).show();
                })
                .catch(error => {
                    console.error('Error fetching results:', error);
                    acsesAlert('error', 'Error', 'Failed to load election results.');
                });
        }

        function updateCountdown() {
            const now = new Date();
            const start = new Date('<?php echo $election['start_date']; ?>');
            const end = new Date('<?php echo $election['end_date']; ?>');
            let target;

            if (now < start) {
                target = start;
            } else if (now >= start && now <= end) {
                target = end;
            } else {
                document.getElementById('countdown').innerHTML = 'Election has ended';
                document.getElementById('countdown-progress').style.width = '100%';
                document.getElementById('countdown-progress').className = 'progress-bar bg-danger';
                return;
            }

            const diff = target - now;
            const days = Math.floor(diff / (1000 * 60 * 60 * 24));
            const hours = Math.floor((diff % (1000 * 60 * 60 * 24)) / (1000 * 60 * 60));
            const minutes = Math.floor((diff % (1000 * 60 * 60)) / (1000 * 60));
            const seconds = Math.floor((diff % (1000 * 60)) / 1000);

            document.getElementById('countdown').innerHTML = `${days}d ${hours}h ${minutes}m ${seconds}s`;

            // Update progress bar
            const total = target - start;
            const progress = ((total - diff) / total) * 100;
            document.getElementById('countdown-progress').style.width = `${progress}%`;

            // Change color based on remaining time
            if (progress > 50) {
                document.getElementById('countdown-progress').className = 'progress-bar bg-success';
            } else if (progress > 25) {
                document.getElementById('countdown-progress').className = 'progress-bar bg-warning';
            } else {
                document.getElementById('countdown-progress').className = 'progress-bar bg-danger';
            }
        }

        setInterval(updateCountdown, 1000);
        updateCountdown();
    </script>
</body>
</html> 