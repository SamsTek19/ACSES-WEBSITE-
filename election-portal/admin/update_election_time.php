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

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    header('Content-Type: application/json');
    if (!validateCSRFToken($_POST['csrf_token'])) {
        echo json_encode(['success' => false, 'message' => 'Invalid request']);
        exit();
    }

    $start_date = sanitizeInput($_POST['start_date']);
    $end_date = sanitizeInput($_POST['end_date']);

    // Validate dates
    if (strtotime($start_date) >= strtotime($end_date)) {
        echo json_encode(['success' => false, 'message' => 'End date must be after start date']);
        exit();
    }

    // Update election time in the database
    $stmt = $pdo->prepare("UPDATE elections SET start_date = ?, end_date = ? WHERE id = ?");
    if ($stmt->execute([$start_date, $end_date, $election_id])) {
        echo json_encode(['success' => true, 'message' => 'Election time updated successfully']);
        exit();
    } else {
        echo json_encode(['success' => false, 'message' => 'Failed to update election time. Please try again.']);
        exit();
    }
}
?> 