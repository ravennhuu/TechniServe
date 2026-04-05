<?php
// Pair B
// fetch.php — Returns active SLA agreements
session_start();
require '../../includes/auth.php';
require '../../includes/db.php';
header('Content-Type: application/json');
