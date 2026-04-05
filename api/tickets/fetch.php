<?php
// Pair B
// fetch.php — Returns filtered ticket collection
session_start();
require '../../includes/auth.php';
require '../../includes/db.php';
header('Content-Type: application/json');
