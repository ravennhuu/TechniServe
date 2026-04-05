<?php
// Leader
// fetch.php — Returns historical report data
session_start();
require '../../includes/auth.php';
require '../../includes/db.php';
header('Content-Type: application/json');
