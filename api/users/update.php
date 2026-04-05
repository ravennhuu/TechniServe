<?php
// Pair B
// update.php — Updates user profile or permissions
session_start();
require '../../includes/auth.php';
require '../../includes/db.php';
header('Content-Type: application/json');
