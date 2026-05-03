<?php
// api/users/create.php
// Admin only — create a new user account (admin or client role).
require_once '../../includes/auth.php';
require_once '../../includes/db.php';
require_once '../../includes/functions.php';
header('Content-Type: application/json');

if ($_SESSION['role'] !== 'admin') {
    http_response_code(403);
    echo json_encode(['success' => false, 'message' => 'Unauthorized.']);
    exit();
}

$name      = trim($_POST['name']      ?? '');
$email     = trim($_POST['email']     ?? '');
$password  = trim($_POST['password']  ?? '');
$role      = trim($_POST['role']      ?? '');
$is_active = isset($_POST['is_active']) ? (int)$_POST['is_active'] : 1;

if (!$name || !$email || !$password || !$role) {
    echo json_encode(['success' => false, 'message' => 'Name, email, password, and role are all required.']);
    exit();
}

if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
    echo json_encode(['success' => false, 'message' => 'Please enter a valid email address.']);
    exit();
}

if (!in_array($role, ['admin', 'client'])) {
    echo json_encode(['success' => false, 'message' => 'Role must be either Admin or Client.']);
    exit();
}

if (strlen($password) < 8) {
    echo json_encode(['success' => false, 'message' => 'Password must be at least 8 characters long.']);
    exit();
}

try {
    // Check if email already exists
    $stmt = $pdo->prepare("SELECT id FROM users WHERE email = ?");
    $stmt->execute([$email]);
    if ($stmt->fetch()) {
        echo json_encode(['success' => false, 'message' => 'That email address is already registered in the system.']);
        exit();
    }

    // If client role, no extra validation needed — link is via clients.user_id (3NF FIX 1)

    $hashed = hashPassword($password);

    $stmt = $pdo->prepare("
        INSERT INTO users (name, email, password_hash, role, is_active)
        VALUES (?, ?, ?, ?, ?)
    ");
    $stmt->execute([
        $name,
        $email,
        $hashed,
        $role,
        $is_active
    ]);

    $userId = $pdo->lastInsertId();

    // If role is client, create a corresponding placeholder client profile to ensure data integrity
    if ($role === 'client') {
        $cstmt = $pdo->prepare("
            INSERT INTO clients (user_id, company_name, contact_person, contact_email) 
            VALUES (?, ?, ?, ?)
        ");
        // Use user's name as placeholder for company and contact
        $cstmt->execute([$userId, $name . "'s Company", $name, $email]);
    }

    echo json_encode([
        'success' => true, 
        'message' => 'User account created successfully.',
        'data' => [
            'id'        => $userId,
            'name'      => $name,
            'email'     => $email,
            'role'      => $role,
            'is_active' => $is_active,
            'created_at'=> date('Y-m-d H:i:s'),
            'company_name' => 'N/A'
        ]
    ]);
} catch (PDOException $e) {
    http_response_code(500);
    echo json_encode(['success' => false, 'message' => 'Failed to create user account. Please try again.']);
}
