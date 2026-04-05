<?php
// Pair A
// login.php — Login page HTML form only. No PHP session logic here.
//             Form submits to api/auth/login.php via POST.
?>
<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="UTF-8">
  <title>Login — TechniServe</title>
  <link rel="stylesheet" href="public/css/bootstrap.min.css">
  <link rel="stylesheet" href="public/css/style.css">
</head>

<body>
  <form action="api/auth/login.php" method="POST">
    <!-- email, password, role fields go here -->
  </form>
  <script src="public/js/bootstrap.bundle.min.js"></script>
</body>

</html>