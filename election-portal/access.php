<?php
require_once 'config.php';

// Initialize error and success messages
$error = '';
$success = '';
$show_confirm = false;
$confirm_token = '';
$confirm_hash = '';
$confirm_email = '';

function completeAccessLinkLogin(array $accessLink): bool {
    global $pdo, $error;

    $stmt = $pdo->prepare("SELECT * FROM users WHERE email = ?");
    $stmt->execute([$accessLink['email']]);
    $user = $stmt->fetch();

    if (!$user) {
        $error = "Your account is not found in our system.";
        return false;
    }

    session_regenerate_id(true);

    $_SESSION['user_id'] = $user['user_id'];
    $_SESSION['role'] = $user['role'];
    $_SESSION['username'] = $user['username'];
    $_SESSION['fullname'] = $user['fullname'];
    $_SESSION['email'] = $user['email'];
    $_SESSION['logged_in'] = true;

    $stmt = $pdo->prepare("UPDATE elections_access_links SET used = 1 WHERE id = ? AND (used = 0 OR used IS NULL)");
    $stmt->execute([$accessLink['id']]);

    if ($stmt->rowCount() === 0) {
        $error = "This access link has already been used.";
        return false;
    }

    try {
        $stmt = $pdo->prepare("INSERT INTO elections_audit_log (user_id, action, details, ip_address, user_agent) VALUES (?, ?, ?, ?, ?)");
        $stmt->execute([
            $user['user_id'],
            'access_link_login',
            'Successful login via access link',
            $_SERVER['REMOTE_ADDR'] ?? '',
            $_SERVER['HTTP_USER_AGENT'] ?? ''
        ]);
    } catch (Throwable $e) {
        error_log("Audit log failed: " . $e->getMessage());
    }

    session_write_close();

    if ($user['role'] === 'admin') {
        header("Location: admin/dashboard");
    } else {
        header("Location: dashboard");
    }
    exit();
}

function fetchValidAccessLink(string $token, string $hash): ?array {
    global $pdo;

    $stmt = $pdo->prepare("SELECT * FROM elections_access_links WHERE token = ? AND hash = ? AND expiry > NOW() AND (used = 0 OR used IS NULL)");
    $stmt->execute([$token, $hash]);
    $accessLink = $stmt->fetch();

    return $accessLink ?: null;
}

// Confirm login via POST (prevents email prefetchers from consuming the link)
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['login_token'], $_POST['login_hash'])) {
    $accessLink = fetchValidAccessLink($_POST['login_token'], $_POST['login_hash']);

    if (!isset($_POST['csrf_token'], $_SESSION['csrf_token']) || !hash_equals($_SESSION['csrf_token'], $_POST['csrf_token'])) {
        $error = "Your session expired. Please click the button below to try again.";
        if ($accessLink) {
            $show_confirm = true;
            $confirm_token = $accessLink['token'];
            $confirm_hash = $accessLink['hash'];
            $confirm_email = $accessLink['email'];
            $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
        }
    } elseif ($accessLink) {
        completeAccessLinkLogin($accessLink);
    } else {
        $error = "Invalid, expired, or already used access link.";
    }
}

// Show confirmation page for magic link (do not auto-login on GET)
if ($_SERVER['REQUEST_METHOD'] === 'GET' && isset($_GET['token'], $_GET['hash'])) {
    $accessLink = fetchValidAccessLink($_GET['token'], $_GET['hash']);

    if ($accessLink) {
        $show_confirm = true;
        $confirm_token = $accessLink['token'];
        $confirm_hash = $accessLink['hash'];
        $confirm_email = $accessLink['email'];
    } else {
        $error = "Invalid, expired, or already used access link.";
    }
}

// Generate new CSRF token if not exists
if (!isset($_SESSION['csrf_token'])) {
    $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
}
$csrf_token = $_SESSION['csrf_token'];

if ($_SERVER['REQUEST_METHOD'] === 'POST' && !isset($_POST['login_token'])) {
    error_log("Access.php script started");
    error_log("POST data received: " . print_r($_POST, true));
    
    // Validate CSRF token
    if (!isset($_POST['csrf_token']) || !isset($_SESSION['csrf_token']) || 
        !hash_equals($_SESSION['csrf_token'], $_POST['csrf_token'])) {
        error_log("CSRF token validation failed");
        $error = "Invalid request. Please try again.";
        // Generate new token after failed attempt
        $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
        $csrf_token = $_SESSION['csrf_token'];
    } else {
        $email = filter_input(INPUT_POST, 'email', FILTER_VALIDATE_EMAIL);
        
        if (!$email) {
            $error = "Please enter a valid email address.";
        } else {
            // Check if email exists in users table
            $stmt = $pdo->prepare("SELECT user_id, fullname, email FROM users WHERE email = ?");
            $stmt->execute([$email]);
            $user = $stmt->fetch();
            
            if (!$user) {
                $error = "This email is not registered in our system.";
            } else {
                // Generate access link
                $accessLink = generateAccessLink($email);
                
                if ($accessLink && sendAccessLinkEmail($email, $accessLink)) {
                    $success = "Access link has been sent to your email.";
                    // Generate new token after successful request
                    $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
                    $csrf_token = $_SESSION['csrf_token'];
                } else {
                    $error = "Failed to send access link. Please try again later.";
                }
            }
        }
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Request Access - ACSES Election Portal</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
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
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 20px;
        }
        
        .access-container {
            background: var(--white);
            padding: 2.5rem;
            border-radius: 20px;
            box-shadow: 0 10px 30px rgba(0, 0, 0, 0.1);
            width: 100%;
            max-width: 450px;
            text-align: center;
        }
        
        .logo {
            max-width: 180px;
            height: auto;
            margin-bottom: 2rem;
        }
        
        .form-title {
            color: var(--primary-color);
            font-size: 1.75rem;
            font-weight: 600;
            margin-bottom: 1.5rem;
        }
        
        .form-control {
            border: 2px solid #eee;
            padding: 0.875rem 1rem;
            border-radius: 10px;
            font-size: 1rem;
            transition: all 0.3s ease;
        }
        
        .form-control:focus {
            border-color: var(--primary-color);
            box-shadow: 0 0 0 0.2rem rgba(18, 72, 36, 0.15);
        }
        
        .btn-primary {
            background-color: var(--primary-color);
            border: none;
            padding: 0.875rem 2rem;
            font-weight: 600;
            border-radius: 10px;
            transition: all 0.3s ease;
            width: 100%;
            margin-top: 1rem;
        }
        
        .btn-primary:hover {
            background-color: var(--secondary-color);
            transform: translateY(-2px);
        }
        
        .alert {
            border-radius: 10px;
            margin-bottom: 1.5rem;
            padding: 1rem;
        }
        
        .form-label {
            color: var(--text-color);
            font-weight: 500;
            margin-bottom: 0.5rem;
            text-align: left;
        }
        
        @media (max-width: 576px) {
            .access-container {
                padding: 2rem 1.5rem;
            }
            
            .logo {
                max-width: 150px;
            }
            
            .form-title {
                font-size: 1.5rem;
            }
        }
    </style>
</head>
<body>
    <div class="access-container">
        <img src="logo.png" alt="ACSES Logo" class="logo">
        <h2 class="form-title"><?php echo $show_confirm ? 'Confirm Sign In' : 'Request Access Link'; ?></h2>
        
        <?php if ($error): ?>
            <div class="alert alert-danger"><?php echo htmlspecialchars($error); ?></div>
        <?php endif; ?>
        
        <?php if ($success): ?>
            <div class="alert alert-success"><?php echo htmlspecialchars($success); ?></div>
        <?php endif; ?>

        <?php if ($show_confirm): ?>
            <p class="text-muted mb-4">Access link verified for <strong><?php echo htmlspecialchars($confirm_email); ?></strong>. Click below to sign in.</p>
            <form method="POST" action="">
                <input type="hidden" name="csrf_token" value="<?php echo htmlspecialchars($csrf_token); ?>">
                <input type="hidden" name="login_token" value="<?php echo htmlspecialchars($confirm_token); ?>">
                <input type="hidden" name="login_hash" value="<?php echo htmlspecialchars($confirm_hash); ?>">
                <button type="submit" class="btn btn-primary">Continue to Election Portal</button>
            </form>
        <?php else: ?>
        
        <form method="POST" action="">
            <input type="hidden" name="csrf_token" value="<?php echo htmlspecialchars($csrf_token); ?>">
            
            <div class="mb-4">
                <label for="email" class="form-label">Email Address</label>
                <input type="email" class="form-control" id="email" name="email" 
                       placeholder="Enter your registered email" required>
            </div>
            
            <button type="submit" class="btn btn-primary">Send Access Link</button>
        </form>
        <?php endif; ?>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html> 
