<?php
// Pair B
// delete.php — Removes ticket (Admin only)
session_start();
require '../../includes/auth.php';
require '../../includes/db.php';
header('Content-Type: application/json');
