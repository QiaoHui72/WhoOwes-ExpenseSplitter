<?php
session_start();
require_once '../database.php';

$error   = '';
$success = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
  $email    = trim($_POST['email']    ?? '');
  $password = $_POST['password']      ?? '';

  if ($email === '' || $password === '') {
    $error = 'Please fill in all fields.';
  } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
    $error = 'Please enter a valid email address.';
  } else {
    // Look up user by email
    $stmt = mysqli_prepare($connect, "SELECT id, name, password FROM users WHERE email = ? LIMIT 1");
    mysqli_stmt_bind_param($stmt, 's', $email);
    mysqli_stmt_execute($stmt);
    $result = mysqli_stmt_get_result($stmt);
    $user   = mysqli_fetch_assoc($result);
    mysqli_stmt_close($stmt);

    if ($user && password_verify($password, $user['password'])) {
      // Store user in session and go to dashboard
      $_SESSION['user_id']   = $user['id'];
      $_SESSION['user_name'] = $user['name'];
      header('Location: ../Content/dashboard.php');
      exit;
    } else {
      $error = 'Incorrect email or password.';
    }
  }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <title>WhoOwes — Sign In</title>
  <link rel="stylesheet" href="login.css">
</head>
<body>

<div class="wrapper">

  <!-- Header-WhoOwes -->
  <div class="brand">
    <div class="brand-logo">
      <svg viewBox="0 0 24 24">
        <rect x="2" y="5" width="20" height="14" rx="2"/>
        <path d="M2 10h20"/>
        <path d="M6 15h4"/>
      </svg>
    </div>
    <h1>WhoOwes</h1>
    <p>Smart expense splitting for everyone</p>
  </div>

  <!-- Content-SignIn -->
  <div class="card">
    <h2 style="text-align: center;">Sign In</h2>

    <?php if ($error):   ?><div class="alert alert-error"><?= htmlspecialchars($error) ?></div><?php endif; ?>
    <?php if ($success): ?><div class="alert alert-success"><?= htmlspecialchars($success) ?></div><?php endif; ?>

    <form method="POST" action="login.php" novalidate>

      <div class="field">
        <label for="email">Email address</label>
        <input
          type="email"
          id="email"
          name="email"
          placeholder="qiao.hui@email.com"
          value="<?= htmlspecialchars($_POST['email'] ?? '') ?>"
          autocomplete="email"
          required
        />
      </div>

      <div class="field">
        <label for="password">Password</label>
        <input
          type="password"
          id="password"
          name="password"
          placeholder="••••••••"
          autocomplete="current-password"
          required
        />
      </div>

      <div class="row">
        <label class="remember">
          <input type="checkbox" name="remember" <?= isset($_POST['remember']) ? 'checked' : '' ?> />
          Remember me
        </label>
        <a href="forgot-password.php" class="forgot">Forgot password?</a>
      </div>

      <button type="submit" class="btn-primary">Sign In</button>
    </form>

    <p class="signup-link">
      Don't have an account? <a href="register.php">Sign up free</a>
    </p>
  </div>

</div>

</body>
</html>
