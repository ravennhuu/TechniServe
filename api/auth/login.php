<?php
// Mock Login endpoint for frontend UI preview
session_start();

// Set dummy session data to bypass auth.php
$_SESSION['user_id'] = 1;
$_SESSION['name'] = 'Demo User';

// Use the role selected in the login form (defaults to admin)
$_SESSION['role'] = $_POST['role'] ?? 'admin';

// Redirect to the dashboard
header('Location: ../../pages/dashboard.php');
exit();
