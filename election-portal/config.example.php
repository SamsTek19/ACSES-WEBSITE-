<?php
// Load local key/value settings when an .env file is present. Production
// deployments should inject environment variables through the host instead.
$localEnvFile = __DIR__ . '/.env';
if (is_file($localEnvFile)) {
    foreach (parse_ini_file($localEnvFile, false, INI_SCANNER_RAW) ?: [] as $key => $value) {
        if (getenv($key) === false) {
            putenv($key . '=' . $value);
        }
    }
}

// Set timezone
date_default_timezone_set('Africa/Accra');
ini_set('date.timezone', 'Africa/Accra');

// Session configuration
ini_set('session.cookie_httponly', 1);
ini_set('session.use_only_cookies', 1);
ini_set('session.cookie_samesite', 'Lax');
ini_set('session.gc_maxlifetime', 3600); // 1 hour
ini_set('session.cookie_lifetime', 3600); // 1 hour
$isHttps = (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off')
    || (!empty($_SERVER['HTTP_X_FORWARDED_PROTO']) && $_SERVER['HTTP_X_FORWARDED_PROTO'] === 'https')
    || (!empty($_SERVER['HTTP_X_FORWARDED_SSL']) && $_SERVER['HTTP_X_FORWARDED_SSL'] === 'on');
if ($isHttps) {
    ini_set('session.cookie_secure', 1);
}

// Start session if not already started
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Error reporting based on environment
$environment = getenv('APP_ENV') ?: 'production';
if ($environment === 'development') {
    error_reporting(E_ALL);
    ini_set('display_errors', 1);
} else {
    error_reporting(0);
    ini_set('display_errors', 0);
}

// Manual include of PHPMailer files
require_once __DIR__ . '/PHPMailer/src/PHPMailer.php';
require_once __DIR__ . '/PHPMailer/src/SMTP.php';
require_once __DIR__ . '/PHPMailer/src/Exception.php';

// Use PHPMailer classes with their namespaces
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\SMTP;
use PHPMailer\PHPMailer\Exception;

// Database configuration
define('DB_HOST', getenv('DB_HOST') ?: '127.0.0.1');
define('DB_NAME', getenv('DB_NAME') ?: 'acses_local');
define('DB_USER', getenv('DB_USER') ?: 'root');
define('DB_PASS', getenv('DB_PASS') ?: '');

// SMTP configuration
define('SMTP_HOST', getenv('SMTP_HOST') ?: '127.0.0.1');
define('SMTP_USERNAME', getenv('SMTP_USERNAME') ?: '');
define('SMTP_PASSWORD', getenv('SMTP_PASSWORD') ?: '');
define('SMTP_PORT', (int) (getenv('SMTP_PORT') ?: 1025));
define('SMTP_SECURE', getenv('SMTP_SECURE') ?: '');
define('SMTP_TIMEOUT', 15);
define('SMTP_DEBUG', $environment === 'development' ? 4 : 0);

// Application configuration
define('SITE_URL', getenv('SITE_URL') ?: 'http://127.0.0.1:8080/');
define('ADMIN_EMAIL', getenv('ADMIN_EMAIL') ?: 'developer@example.test');

// Theme colors
define('PRIMARY_COLOR', '#0056b3');
define('SECONDARY_COLOR', '#00b386');
define('WHITE_COLOR', '#ffffff');

// Session configuration
define('SESSION_LIFETIME', 3600); // 1 hour

// Create PDO instance with secure options
$pdoOptions = [
    PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
    PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
    PDO::ATTR_EMULATE_PREPARES => false,
    PDO::MYSQL_ATTR_INIT_COMMAND => "SET NAMES utf8mb4 COLLATE utf8mb4_unicode_ci"
];

try {
    $dsn = "mysql:host=" . DB_HOST . ";dbname=" . DB_NAME . ";charset=utf8mb4";
    error_log("Attempting database connection to: " . DB_HOST);
    $pdo = new PDO($dsn, DB_USER, DB_PASS, $pdoOptions);
    error_log("Database connection successful");
    
    // Set MySQL timezone to match PHP
    $pdo->exec("SET time_zone = '+00:00'");
} catch(PDOException $e) {
    error_log("Database connection failed: " . $e->getMessage());
    error_log("Connection details - Host: " . DB_HOST . ", Database: " . DB_NAME . ", User: " . DB_USER);
    if ($environment === 'development') {
        die("Connection failed: " . $e->getMessage());
    } else {
        die("Connection failed. Please try again later.");
    }
}

// Security functions
function generateCSRFToken() {
    if (empty($_SESSION['csrf_token'])) {
        $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
    }
    return $_SESSION['csrf_token'];
}

function validateCSRFToken($token) {
    return isset($_SESSION['csrf_token']) && hash_equals($_SESSION['csrf_token'], $token);
}

function generateAccessLink($email) {
    global $pdo;
    $token = bin2hex(random_bytes(32));
    $hash = bin2hex(random_bytes(32)); // Generate a random hash instead of using password_hash
    
    // Set expiry to 1 hour from now
    $now = new DateTime('now', new DateTimeZone('Africa/Accra'));
    $expiry = clone $now;
    $expiry->modify('+1 hour');
    
    error_log("Current time (Africa/Accra): " . $now->format('Y-m-d H:i:s'));
    error_log("Expiry time (Africa/Accra): " . $expiry->format('Y-m-d H:i:s'));
    
    $stmt = $pdo->prepare("INSERT INTO elections_access_links (email, token, hash, expiry) VALUES (?, ?, ?, ?)");
    $stmt->execute([$email, $token, $hash, $expiry->format('Y-m-d H:i:s')]);
    
    return [
        'token' => $token,
        'hash' => $hash,
        'url' => SITE_URL . 'access?token=' . $token . '&hash=' . $hash
    ];
}

function validateAccessLink($token, $hash) {
    global $pdo;
    $stmt = $pdo->prepare("SELECT * FROM elections_access_links WHERE token = ? AND hash = ? AND expiry > NOW() AND (used = 0 OR used IS NULL)");
    $stmt->execute([$token, $hash]);
    $link = $stmt->fetch();
    
    if ($link) {
        return $link;
    }
    return false;
}

function markAccessLinkAsUsed($token) {
    global $pdo;
    $stmt = $pdo->prepare("UPDATE elections_access_links SET used = 1 WHERE token = ?");
    $stmt->execute([$token]);
}

function createMailer() {
    $mail = new PHPMailer(true);
    
    try {
        // Server settings
        $mail->SMTPDebug = SMTP_DEBUG;
        $mail->isSMTP();
        $mail->Host = SMTP_HOST;
        $mail->SMTPAuth = SMTP_USERNAME !== '';
        $mail->Username = SMTP_USERNAME;
        $mail->Password = SMTP_PASSWORD;
        $mail->SMTPSecure = SMTP_SECURE;
        $mail->Port = SMTP_PORT;
        $mail->Timeout = defined('SMTP_TIMEOUT') ? SMTP_TIMEOUT : 15;
        
        // Secure SMTP options
        // Keep certificate verification enabled. For local email testing,
        // use Mailpit/MailHog on port 1025 without TLS.
        
        // Default settings
        $mail->isHTML(true);
        $mail->setFrom(SMTP_USERNAME, 'ACSES Election Portal');
        $mail->CharSet = 'UTF-8';
        
        return $mail;
    } catch (Exception $e) {
        error_log("Mailer creation failed: " . $e->getMessage());
        return false;
    }
}

function sendAccessLinkEmail($email, $accessLink) {
    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        error_log("Access-link email rejected because the address format was invalid");
        return false;
    }
    
    $mail = createMailer();
    if (!$mail) {
        return false;
    }
    
    try {
        $mail->addAddress($email);
        $mail->Subject = 'Your ACSES Election Portal Access Link';
        
        // Get email template
        ob_start();
        include __DIR__ . '/email_template.php';
        $mail->Body = $emailTemplate;
        $mail->AltBody = "Click the following link to access your account: " . $accessLink['url'];
        
        $mail->send();
        error_log("Election access-link email sent successfully");
        return true;
    } catch (Exception $e) {
        error_log("Email sending failed: " . $e->getMessage());
        return false;
    }
}

// Test SMTP connection
if (SMTP_DEBUG > 0) {
    try {
        $mail = createMailer();
        if ($mail) {
            error_log("SMTP connection test successful");
        }
    } catch (Exception $e) {
        error_log("SMTP connection test failed: " . $e->getMessage());
    }
} 
