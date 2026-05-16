<?php
$error   = '';
$success = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
  $name    = trim($_POST['name']           ?? '');
  $email   = trim($_POST['email']          ?? '');
  $password = $_POST['password']           ?? '';
  $confirm  = $_POST['confirm_password']   ?? '';

  if ($name === '' || $email === '' || $password === '' || $confirm === '') {
    $error = 'Please fill in all fields.';
  } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
    $error = 'Please enter a valid email address.';
  } elseif (strlen($password) < 8) {
    $error = 'Password must be at least 8 characters.';
  } elseif ($password !== $confirm) {
    $error = 'Passwords do not match.';
  } else {
    // TODO: save user to database here
    $success = 'Account created! You can now sign in.';
  }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <title>WhoOwes — Sign Up</title>
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

  <!-- Content-Register -->
  <div class="card">
    <h2 style="text-align: center;">Create Account</h2>

    <?php if ($error):   ?><div class="alert alert-error"><?= htmlspecialchars($error) ?></div><?php endif; ?>
    <?php if ($success): ?><div class="alert alert-success"><?= htmlspecialchars($success) ?></div><?php endif; ?>

    <form method="POST" action="register.php" novalidate>

      <div class="field">
        <label for="name">Full Name</label>
        <input
          type="text"
          id="name"
          name="name"
          placeholder="Qiao Hui"
          value="<?= htmlspecialchars($_POST['name'] ?? '') ?>"
          autocomplete="name"
          required
        />
      </div>

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
          placeholder="At least 8 characters"
          autocomplete="new-password"
          required
        />
      </div>

      <div class="field">
        <label for="confirm_password">Confirm Password</label>
        <input
          type="password"
          id="confirm_password"
          name="confirm_password"
          placeholder="Re-enter your password"
          autocomplete="new-password"
          required
        />
      </div>

      <button type="submit" class="btn-primary" style="margin-top: 8px;">Sign Up Free</button>
    </form>

    <p class="signup-link">
      Already have an account? <a href="login.php">Sign in</a>
    </p>
  </div>

</div>

</body>
</html>
