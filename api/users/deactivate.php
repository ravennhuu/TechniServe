<?php
// Pair B
// deactivate.php — Disables user access
session_start();
require '../../includes/auth.php';
require '../../includes/db.php';
header('Content-Type: application/json');
