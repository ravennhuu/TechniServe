<?php
// Pair B
// logout.php — Destroys session and returns status
session_start();
require '../../includes/auth.php';
require '../../includes/db.php';
header('Content-Type: application/json');
