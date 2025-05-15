<?php
require 'config.php';

$message = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = trim($_POST['username'] ?? '');
    $password = $_POST['password'] ?? '';

    if ($username && $password) {
        $hashedPassword = password_hash($password, PASSWORD_DEFAULT);

        try {
            $stmt = $pdo->prepare("INSERT INTO users (username, password) VALUES (:username, :password)");
            $stmt->execute(['username' => $username, 'password' => $hashedPassword]);
            $message = 'Account created successfully. <a href="login.php">Login here</a>.';
        } catch (PDOException $e) {
            // Assuming unique username constraint violated
            $message = 'Error: Username might already be taken.';
        }
    } else {
        $message = 'Please fill in all fields.';
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <title>Sign Up</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet" />
</head>
<body class="bg-light d-flex align-items-center" style="height: 100vh;">
  <div class="container">
    <div class="row justify-content-center">
      <div class="col-md-4">
        <div class="card shadow">
          <div class="card-body">
            <h3 class="card-title text-center mb-4">Sign Up</h3>
            <?php if ($message): ?>
              <div class="alert alert-info"><?= $message ?></div>
            <?php endif; ?>
            <form method="POST" novalidate>
              <div class="mb-3">
                <label for="username" class="form-label">Username</label>
                <input id="username" type="text" name="username" class="form-control" required autofocus />
              </div>
              <div class="mb-3">
                <label for="password" class="form-label">Password</label>
                <input id="password" type="password" name="password" class="form-control" required />
              </div>
              <button type="submit" class="btn btn-success w-100">Sign Up</button>
              <a href="login.php" class="btn btn-link d-block text-center mt-2">Already have an account? Login</a>
            </form>
          </div>
        </div>
      </div>
    </div>
  </div>
</body>
</html>
