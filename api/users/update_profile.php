<?php
// api/users/update_profile.php
// Allows the currently logged-in user (admin) to update their own profile.
// Validates current_password before allowing a password change.
require_once '../../includes/auth.php';
require_once '../../includes/db.php';
require_once '../../includes/functions.php';
header('Content-Type: application/json');

$user_id         = $_SESSION['user_id'];
$name            = trim($_POST['name']             ?? '');
$email           = trim($_POST['email']            ?? '');
$current_pass    = trim($_POST['current_password'] ?? '');
$new_pass        = trim($_POST['new_password']     ?? '');

// Basic validation
if (!$name || !$email) {
    echo json_encode(['success' => false, 'message' => 'Name and email are required.']);
    exit();
}

if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
    echo json_encode(['success' => false, 'message' => 'Please enter a valid email address.']);
    exit();
}

try {
    // Fetch current user record
    $stmt = $pdo->prepare("SELECT * FROM users WHERE id = ?");
    $stmt->execute([$user_id]);
    $user = $stmt->fetch();

    if (!$user) {
        echo json_encode(['success' => false, 'message' => 'User account not found.']);
        exit();
    }

    // Check if email is already taken by another user
    $stmt = $pdo->prepare("SELECT id FROM users WHERE email = ? AND id != ?");
    $stmt->execute([$email, $user_id]);
    if ($stmt->fetch()) {
        echo json_encode(['success' => false, 'message' => 'That email address is already in use by another account.']);
        exit();
    }

    $updates = [];
    $params  = [];

    $updates[] = "name = ?";
    $params[]  = $name;

    $updates[] = "email = ?";
    $params[]  = $email;

    // Password change — only if new_password provided
    if ($new_pass !== '') {
        if (strlen($new_pass) < 8) {
            echo json_encode(['success' => false, 'message' => 'New password must be at least 8 characters long.']);
            exit();
        }
        if ($current_pass === '') {
            echo json_encode(['success' => false, 'message' => 'Please enter your current password to set a new one.']);
            exit();
        }
        if (!password_verify($current_pass, $user['password_hash'])) {
            echo json_encode(['success' => false, 'message' => 'Your current password is incorrect.']);
            exit();
        }
        $updates[] = "password_hash = ?";
        $params[]  = hashPassword($new_pass);
    }

    $params[] = $user_id;
    $sql = "UPDATE users SET " . implode(', ', $updates) . " WHERE id = ?";
    $stmt = $pdo->prepare($sql);
    $stmt->execute($params);

    // Refresh session
    $_SESSION['name']  = $name;
    $_SESSION['email'] = $email;

    $message = ($new_pass !== '')
        ? 'Profile and password updated successfully.'
        : 'Profile updated successfully.';

    echo json_encode(['success' => true, 'message' => $message]);

} catch (PDOException $e) {
    http_response_code(500);
    echo json_encode(['success' => false, 'message' => 'Failed to update profile. Please try again.']);
}
