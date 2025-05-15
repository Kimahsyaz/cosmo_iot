<?php
session_start();
if (!isset($_SESSION['username'])) {
    header('Location: login.php');
    exit;
}
?>

<!DOCTYPE html>
<html>
<head>
  <title>Dashboard</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="p-4 bg-light">

<div class="container">
  <div class="card shadow-sm p-4">
    <h1 class="mb-3">Welcome, <?= htmlspecialchars($_SESSION['username']) ?>!</h1>
    <p class="mb-4">You are now logged in.</p>
    <div class="d-flex gap-2">
      <a href="index.php" class="btn btn-primary">Go to Sensor Dashboard</a>
      <a href="logout.php" class="btn btn-outline-danger">Logout</a>
    </div>
  </div>
</div>

</body>
</html>
