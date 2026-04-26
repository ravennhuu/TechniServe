<?php
// api/auth/login.php
session_start();
require_once '../../includes/db.php';
header('Content-Type: application/json');

// Get form data
$email    = trim($_POST['email']    ?? '');
$password = trim($_POST['password'] ?? '');
$role     = trim($_POST['role']     ?? '');

// Basic validation
if (!$email || !$password || !$role) {
    echo json_encode(['success' => false, 'message' => 'All fields are required.']);
    exit();
}

try {
    // Find user in the database by email and role
    $stmt = $pdo->prepare("SELECT * FROM users WHERE email = ? AND role = ? AND is_active = 1");
    $stmt->execute([$email, $role]);
    $user = $stmt->fetch();

    // Check if user exists AND password matches
    if ($user && password_verify($password, $user['password_hash'])) {
        // Save user info in session
        $_SESSION['user_id']   = $user['id'];
        $_SESSION['role']      = $user['role'];
        $_SESSION['name']      = $user['name'];
        $_SESSION['email']     = $user['email'];
        $_SESSION['client_id'] = $user['client_id']; // Essential for client-side filtering

        // If AJAX request, return JSON. If standard form POST, redirect.
        if (!empty($_SERVER['HTTP_X_REQUESTED_WITH']) && strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) == 'xmlhttprequest') {
            echo json_encode(['success' => true, 'redirect' => '../../pages/dashboard.php']);
        } else {
header('Location: ../../pages/dashboard.php');
        }
        exit();
    } else {
        if (!empty($_SERVER['HTTP_X_REQUESTED_WITH']) && strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) == 'xmlhttprequest') {
            echo json_encode(['success' => false, 'message' => 'Invalid email, password, or role selection.']);
        } else {
            header('Location: ../../login.php?error=1');
        }
        exit();
    }
} catch (PDOException $e) {
    echo json_encode(['success' => false, 'message' => 'System error. Please try again later.']);
exit();
}
