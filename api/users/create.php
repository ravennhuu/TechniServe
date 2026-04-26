<?php
// api/users/create.php
require_once '../../includes/auth.php';
require_once '../../includes/db.php';
require_once '../../includes/functions.php';
header('Content-Type: application/json');

// Only admin can create accounts
if ($_SESSION['role'] !== 'admin') {
    http_response_code(403);
    echo json_encode(['success' => false, 'message' => 'Unauthorized.']);
    exit();
}

$name     = trim($_POST['name']     ?? '');
$email    = trim($_POST['email']    ?? '');
$password = trim($_POST['password'] ?? '');
$role     = trim($_POST['role']     ?? ''); // 'admin' or 'client'

if (!$name || !$email || !$password || !$role) {
    echo json_encode(['success' => false, 'message' => 'All fields are required.']);
    exit();
}

try {
    // Check if email exists
    $stmt = $pdo->prepare("SELECT id FROM users WHERE email = ?");
    $stmt->execute([$email]);
    if ($stmt->fetch()) {
        echo json_encode(['success' => false, 'message' => 'Email already registered.']);
        exit();
    }

    $hashed = hashPassword($password);

    $stmt = $pdo->prepare("
        INSERT INTO users (name, email, password_hash, role, is_active) 
        VALUES (?, ?, ?, ?, 1)
    ");
    $stmt->execute([$name, $email, $hashed, $role]);

    echo json_encode(['success' => true, 'message' => 'User account created successfully.']);
} catch (PDOException $e) {
    http_response_code(500);
    echo json_encode(['success' => false, 'message' => 'Failed to create user account.']);
}
