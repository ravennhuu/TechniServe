<?php
// Pair B
// auth.php — starts session, redirects to login.php if user is not authenticated
session_start();
if (!isset($_SESSION['user_id'])) {
    header('Location: ../login.php');
    exit();
}
