<?php
// Pair B
// deactivate.php — Disables active SLA agreement
session_start();
require '../../includes/auth.php';
require '../../includes/db.php';
header('Content-Type: application/json');
