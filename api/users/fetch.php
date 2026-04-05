<?php
// Pair B
// fetch.php — Returns user list filtered by role
session_start();
require '../../includes/auth.php';
require '../../includes/db.php';
header('Content-Type: application/json');
