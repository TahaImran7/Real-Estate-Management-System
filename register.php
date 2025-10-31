<?php
// register.php
include 'includes/header.php';     // header starts session() already
require 'includes/db_connect.php';

$errors = [];
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = trim($_POST['username'] ?? '');
    $email    = trim($_POST['email'] ?? '');
    $password = $_POST['password'] ?? '';
    $confirm  = $_POST['confirm_password'] ?? '';

    if (!$username || !$email || !$password) {
        $errors[] = "All fields are required.";
    } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $errors[] = "Invalid email address.";
    } elseif ($password !== $confirm) {
        $errors[] = "Passwords do not match.";
    } else {
        // Check duplicate email
        $stmt = mysqli_prepare($conn, "SELECT user_id FROM users WHERE email = ?");
        mysqli_stmt_bind_param($stmt, "s", $email);
        mysqli_stmt_execute($stmt);
        mysqli_stmt_store_result($stmt);
        if (mysqli_stmt_num_rows($stmt) > 0) {
            $errors[] = "An account with this email already exists.";
        }
        mysqli_stmt_close($stmt);
    }

    if (empty($errors)) {
        $hash = password_hash($password, PASSWORD_DEFAULT);
        $role = 'user'; // default role for registered accounts

        $stmt = mysqli_prepare($conn, "INSERT INTO users (username, email, password, role) VALUES (?, ?, ?, ?)");
        mysqli_stmt_bind_param($stmt, "ssss", $username, $email, $hash, $role);
        $ok = mysqli_stmt_execute($stmt);
        mysqli_stmt_close($stmt);

        if ($ok) {
            // auto-login after register
            $user_id = mysqli_insert_id($conn);
            $_SESSION['user_id'] = $user_id;
            $_SESSION['username'] = $username;
            $_SESSION['role'] = $role;
            header('Location: index.php');
            exit;
        } else {
            $errors[] = "Registration failed. Try again later.";
        }
    }
}
?>

<main class="form-page">
  <section class="form-card">
    <h2>Create an account</h2>

    <?php if (!empty($errors)): ?>
      <div class="errors">
        <?php foreach ($errors as $e) echo "<p style='color:#c0392b;'>".htmlspecialchars($e)."</p>"; ?>
      </div>
    <?php endif; ?>

    <form method="post" action="register.php" class="form">
      <label>Username
        <input type="text" name="username" value="<?= htmlspecialchars($username ?? '') ?>" required>
      </label>

      <label>Email
        <input type="email" name="email" value="<?= htmlspecialchars($email ?? '') ?>" required>
      </label>

      <label>Password
        <input type="password" name="password" required>
      </label>

      <label>Confirm Password
        <input type="password" name="confirm_password" required>
      </label>

      <button type="submit" class="btn-primary">Register</button>
      <p>Already have an account? <a href="login.php">Login</a></p>
    </form>
  </section>
</main>

<?php include 'includes/footer.php'; ?>
