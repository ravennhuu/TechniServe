<?php
// api/users/update.php
require_once '../../includes/auth.php';
require_once '../../includes/db.php';
require_once '../../includes/functions.php';
header('Content-Type: application/json');

$id = $_POST['id'] ?? $_SESSION['user_id']; // Default to own profile if ID not specified

// Ownership check: non-admins can only update themselves
if ($_SESSION['role'] !== 'admin' && $id != $_SESSION['user_id']) {
    http_response_code(403);
    echo json_encode(['success' => false, 'message' => 'Unauthorized.']);
    exit();
}

$name     = trim($_POST['name']     ?? '');
$email    = trim($_POST['email']    ?? '');
$password = trim($_POST['password'] ?? '');

try {
    $updates = [];
    $params  = [];

    if ($name) { $updates[] = "name = ?"; $params[] = $name; }
    if ($email) { $updates[] = "email = ?"; $params[] = $email; }
    if ($password) { 
        $updates[] = "password_hash = ?"; 
        $params[] = hashPassword($password); 
    }

    if (empty($updates)) {
        echo json_encode(['success' => false, 'message' => 'No fields provided for update.']);
        exit();
    }

    $params[] = $id;
    $sql = "UPDATE users SET " . implode(', ', $updates) . " WHERE id = ?";
    $stmt = $pdo->prepare($sql);
    $stmt->execute($params);

    // Update session name if updating own profile
    if ($id == $_SESSION['user_id'] && $name) {
        $_SESSION['name'] = $name;
    }

    echo json_encode(['success' => true, 'message' => 'Profile updated successfully.']);
} catch (PDOException $e) {
    http_response_code(500);
    echo json_encode(['success' => false, 'message' => 'Failed to update user profile.']);
}
