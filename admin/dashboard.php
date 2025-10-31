<?php
// admin/dashboard.php
require_once __DIR__ . '/../includes/header.php';
require_once __DIR__ . '/../includes/db_connect.php';

// Protect route: only admin
if (empty($_SESSION['user_id']) || ($_SESSION['role'] ?? '') !== 'admin') {
    header('Location: ../login.php');
    exit;
}

// Sample stats (count properties, messages)
$countProperties = 0;
$countMessages = 0;

$res = mysqli_query($conn, "SELECT COUNT(*) AS c FROM properties");
if ($res) {
    $row = mysqli_fetch_assoc($res);
    $countProperties = $row['c'] ?? 0;
}

$res = mysqli_query($conn, "SELECT COUNT(*) AS c FROM messages");
if ($res) {
    $row = mysqli_fetch_assoc($res);
    $countMessages = $row['c'] ?? 0;
}
?>

<main class="admin-dashboard">
  <h1>Admin Dashboard</h1>
  <p>Welcome, <?= htmlspecialchars($_SESSION['username']) ?></p>

  <div class="stats">
    <div class="stat-card">
      <h3>Total Properties</h3>
      <p><?= $countProperties ?></p>
    </div>

    <div class="stat-card">
      <h3>Messages / Inquiries</h3>
      <p><?= $countMessages ?></p>
    </div>
  </div>

  <p><a href="add_property.php" class="btn">Add Property</a> &nbsp; <a href="../logout.php" class="btn">Logout</a></p>
</main>

<?php include __DIR__ . '/../includes/footer.php'; ?>
