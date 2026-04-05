<?php
// Leader
// update.php — Updates lead status (e.g. converted)
session_start();
require '../../includes/auth.php';
require '../../includes/db.php';
header('Content-Type: application/json');
