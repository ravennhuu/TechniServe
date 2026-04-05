<?php
// Pair B
// fetch.php — Returns maintenance calendar events
session_start();
require '../../includes/auth.php';
require '../../includes/db.php';
header('Content-Type: application/json');
