<?php
// Pair B
// logout.php — kills session and redirects to index.php
session_start();
session_destroy();
header('Location: index.php');
exit();
