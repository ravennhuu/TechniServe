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
        $_SESSION['user_id'] = $user['id'];
        $_SESSION['role']    = $user['role'];
        $_SESSION['name']    = $user['name'];
        $_SESSION['email']   = $user['email'];

        // 3NF FIX 1: users.client_id removed. Derive client_id via clients.user_id.
        if ($user['role'] === 'client') {
            $cstmt = $pdo->prepare("SELECT id FROM clients WHERE user_id = ?");
            $cstmt->execute([$user['id']]);
            $client = $cstmt->fetch();
            $_SESSION['client_id'] = $client ? $client['id'] : null;
        } else {
            $_SESSION['client_id'] = null;
        }

        echo json_encode(['success' => true, 'redirect' => 'pages/dashboard.php']);
        exit();
    } else {
        echo json_encode(['success' => false, 'message' => 'Invalid email, password, or role selection.']);
        exit();
    }
} catch (PDOException $e) {
    echo json_encode(['success' => false, 'message' => 'System error. Please try again later.']);
    exit();
}

