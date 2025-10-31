<?php
// admin/dashboard.php
require_once __DIR__ . '/../includes/header.php';
require_once __DIR__ . '/../includes/db_connect.php';

// Protect route: only admin
if (empty($_SESSION['user_id']) || ($_SESSION['role'] ?? '') !== 'admin') {
    header('Location: ../login.php');
    exit;
}

// Count properties
$countProperties = 0;
$res = mysqli_query($conn, "SELECT COUNT(*) AS c FROM properties");
if ($res) {
    $row = mysqli_fetch_assoc($res);
    $countProperties = $row['c'] ?? 0;
}

// Count inquiries
$countInquiries = 0;
$res = mysqli_query($conn, "SELECT COUNT(*) AS c FROM inquiries");
if ($res) {
    $row = mysqli_fetch_assoc($res);
    $countInquiries = $row['c'] ?? 0;
}
?>

<main class="admin-dashboard">
  <h1>Admin Dashboard</h1>
  <p>Welcome, <?= htmlspecialchars($_SESSION['username']) ?></p>

  <div class="stats">
    <div class="stat-card">
      <h3>Total Properties</h3>
      <p><?= $countProperties ?></p>
      <a href="manage_property.php" class="btn-small">Manage Properties</a>
    </div>

    <div class="stat-card">
      <h3>Total Inquiries</h3>
      <p><?= $countInquiries ?></p>
      <a href="manage_inquiries.php" class="btn-small">View Inquiries</a>
    </div>
  </div>

  <div class="admin-actions">
    <a href="add_property.php" class="btn">Add New Property</a>
    <a href="../logout.php" class="btn">Logout</a>
  </div>
</main>

<?php include __DIR__ . '/../includes/footer.php'; ?>
