<?php
// api/users/update.php
// Admin: update any user's profile. Non-admin: update own profile only.
require_once '../../includes/auth.php';
require_once '../../includes/db.php';
require_once '../../includes/functions.php';
header('Content-Type: application/json');

// Accept 'user_id' (from user_form.php hidden field) OR 'id', fallback to own session
$id = $_POST['user_id'] ?? $_POST['id'] ?? $_SESSION['user_id'];

// Non-admins may only update themselves
if ($_SESSION['role'] !== 'admin' && $id != $_SESSION['user_id']) {
    http_response_code(403);
    echo json_encode(['success' => false, 'message' => 'Unauthorized.']);
    exit();
}

$name      = trim($_POST['name']      ?? '');
$email     = trim($_POST['email']     ?? '');
$password  = trim($_POST['password']  ?? '');
$role      = trim($_POST['role']      ?? '');
$is_active = isset($_POST['is_active']) ? (int)$_POST['is_active'] : null;

if (!$name || !$email) {
    echo json_encode(['success' => false, 'message' => 'Name and email are required.']);
    exit();
}

if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
    echo json_encode(['success' => false, 'message' => 'Please enter a valid email address.']);
    exit();
}

try {
    // Check if email is taken by a different user
    $stmt = $pdo->prepare("SELECT id FROM users WHERE email = ? AND id != ?");
    $stmt->execute([$email, $id]);
    if ($stmt->fetch()) {
        echo json_encode(['success' => false, 'message' => 'That email address is already used by another account.']);
        exit();
    }

    $updates = [];
    $params  = [];

    $updates[] = "name = ?";  $params[] = $name;
    $updates[] = "email = ?"; $params[] = $email;

    if ($password !== '') {
        if (strlen($password) < 8) {
            echo json_encode(['success' => false, 'message' => 'Password must be at least 8 characters.']);
            exit();
        }
        $updates[] = "password_hash = ?";
        $params[]  = hashPassword($password);
    }

    // Admin-only fields
    if ($_SESSION['role'] === 'admin') {
        if ($role && in_array($role, ['admin', 'client'])) {
            $updates[] = "role = ?";
            $params[]  = $role;
        }
        if ($is_active !== null) {
            $updates[] = "is_active = ?";
            $params[]  = $is_active;
        }
        // 3NF FIX 1: users.client_id removed — link is managed via clients.user_id
    }

    $params[] = $id;
    $sql = "UPDATE users SET " . implode(', ', $updates) . " WHERE id = ?";
    $stmt = $pdo->prepare($sql);
    $stmt->execute($params);

    // Refresh session if updating own profile
    if ($id == $_SESSION['user_id']) {
        $_SESSION['name']  = $name;
        $_SESSION['email'] = $email;
    }

    echo json_encode(['success' => true, 'message' => 'User account updated successfully.']);
} catch (PDOException $e) {
    http_response_code(500);
    echo json_encode(['success' => false, 'message' => 'Failed to update user account.']);
}
