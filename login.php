<?php
// login.php
include 'includes/header.php';
require 'includes/db_connect.php';

$errors = [];
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email = trim($_POST['email'] ?? '');
    $password = $_POST['password'] ?? '';

    if (!$email || !$password) {
        $errors[] = "Email and password are required.";
    } else {
        $stmt = mysqli_prepare($conn, "SELECT user_id, username, password, role FROM users WHERE email = ? LIMIT 1");
        mysqli_stmt_bind_param($stmt, "s", $email);
        mysqli_stmt_execute($stmt);
        mysqli_stmt_bind_result($stmt, $user_id, $username_db, $hash_db, $role_db);
        if (mysqli_stmt_fetch($stmt)) {
            if (password_verify($password, $hash_db)) {
                // login success
                $_SESSION['user_id'] = $user_id;
                $_SESSION['username'] = $username_db;
                $_SESSION['role'] = $role_db;
                mysqli_stmt_close($stmt);

                // Redirect admin to admin dashboard, users to homepage
                if ($role_db === 'admin') {
                    header('Location: admin/dashboard.php');
                } else {
                    header('Location: index.php');
                }
                exit;
            } else {
                $errors[] = "Invalid credentials.";
            }
        } else {
            $errors[] = "Invalid credentials.";
        }
        mysqli_stmt_close($stmt);
    }
}
?>

<main class="form-page">
  <section class="form-card">
    <h2>Login</h2>

    <?php if (!empty($errors)): ?>
      <div class="errors">
        <?php foreach ($errors as $e) echo "<p style='color:#c0392b;'>".htmlspecialchars($e)."</p>"; ?>
      </div>
    <?php endif; ?>

    <form method="post" action="login.php" class="form">
      <label>Email
        <input type="email" name="email" required>
      </label>

      <label>Password
        <input type="password" name="password" required>
      </label>

      <button type="submit" class="btn-primary">Login</button>
      <p>Don't have an account? <a href="register.php">Register</a></p>
    </form>
  </section>
</main>

<?php include 'includes/footer.php'; ?>
