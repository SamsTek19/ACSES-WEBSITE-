<?php
require_once '../config.php';
header('X-Content-Type-Options: nosniff');

// Check if user is logged in and is an admin
if (!isset($_SESSION['user_id']) || !isset($_SESSION['logged_in']) || $_SESSION['logged_in'] !== true || ($_SESSION['role'] ?? null) !== 'admin') {
    header("Location: ../access");
    exit();
}

$stmt = $pdo->prepare("SELECT user_id FROM users WHERE user_id = ? AND role = 'admin'");
$stmt->execute([$_SESSION['user_id']]);
$admin = $stmt->fetch();

if (!$admin) {
    header("Location: ../dashboard");
    exit();
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    header('Content-Type: application/json');
    if (!isset($_POST['csrf_token']) || !validateCSRFToken($_POST['csrf_token'])) {
        echo json_encode(['success' => false, 'message' => 'Invalid request']);
        exit();
    }

    $election_id = filter_input(INPUT_POST, 'election_id', FILTER_VALIDATE_INT);
    $start_date = $_POST['start_date'] ?? '';
    $end_date = $_POST['end_date'] ?? '';
    $start = DateTimeImmutable::createFromFormat('Y-m-d\\TH:i', $start_date);
    $end = DateTimeImmutable::createFromFormat('Y-m-d\\TH:i', $end_date);

    // Validate dates
    if (!$election_id || !$start || !$end || $start >= $end) {
        http_response_code(422);
        echo json_encode(['success' => false, 'message' => 'End date must be after start date']);
        exit();
    }

    // Update election time in the database
    $stmt = $pdo->prepare("UPDATE elections SET start_date = ?, end_date = ? WHERE id = ?");
    if ($stmt->execute([$start->format('Y-m-d H:i:s'), $end->format('Y-m-d H:i:s'), $election_id]) && $stmt->rowCount() > 0) {
        echo json_encode(['success' => true, 'message' => 'Election time updated successfully']);
        exit();
    } else {
        echo json_encode(['success' => false, 'message' => 'Failed to update election time. Please try again.']);
        exit();
    }
}
?> 