<?php
// admin/manage_properties.php
require_once __DIR__ . '/../includes/header.php';
require_once __DIR__ . '/../includes/db_connect.php';

// protect route: only admin
if (empty($_SESSION['user_id']) || ($_SESSION['role'] ?? '') !== 'admin') {
    header('Location: ../login.php');
    exit;
}

// fetch properties
$res = mysqli_query($conn, "SELECT * FROM properties ORDER BY created_at DESC");
?>
<main class="admin-dashboard">
  <h1>Manage Properties</h1>
  <p><a href="add_property.php" class="btn">+ Add New Property</a> &nbsp; <a href="dashboard.php" class="btn">Dashboard</a></p>

  <table style="width:100%; border-collapse: collapse; margin-top:20px;">
    <thead>
      <tr style="background:#f4f4f4;">
        <th style="padding:8px; border:1px solid #ddd;">ID</th>
        <th style="padding:8px; border:1px solid #ddd;">Title</th>
        <th style="padding:8px; border:1px solid #ddd;">Price</th>
        <th style="padding:8px; border:1px solid #ddd;">Location</th>
        <th style="padding:8px; border:1px solid #ddd;">Type</th>
        <th style="padding:8px; border:1px solid #ddd;">Image</th>
        <th style="padding:8px; border:1px solid #ddd;">Actions</th>
      </tr>
    </thead>
    <tbody>
      <?php while($row = mysqli_fetch_assoc($res)): ?>
        <tr>
          <td style="padding:8px; border:1px solid #ddd; text-align:center;"><?= $row['property_id'] ?></td>
          <td style="padding:8px; border:1px solid #ddd;"><?= htmlspecialchars($row['title']) ?></td>
          <td style="padding:8px; border:1px solid #ddd;">PKR <?= number_format($row['price'],2) ?></td>
          <td style="padding:8px; border:1px solid #ddd;"><?= htmlspecialchars($row['location']) ?></td>
          <td style="padding:8px; border:1px solid #ddd;"><?= htmlspecialchars($row['property_type']) ?></td>
          <td style="padding:8px; border:1px solid #ddd; text-align:center;">
            <?php if (!empty($row['image']) && file_exists(__DIR__ . '/../uploads/' . $row['image'])): ?>
              <img src="../uploads/<?= htmlspecialchars($row['image']) ?>" alt="" style="width:80px;height:60px;object-fit:cover;border-radius:4px;">
            <?php else: ?>
              <span style="color:#888">No image</span>
            <?php endif; ?>
          </td>
          <td style="padding:8px; border:1px solid #ddd; text-align:center;">
            <a href="edit_property.php?id=<?= $row['property_id'] ?>" class="btn" style="margin-right:6px;">Edit</a>
            <a href="delete_property.php?id=<?= $row['property_id'] ?>" class="btn" onclick="return confirm('Delete this property?')">Delete</a>
          </td>
        </tr>
      <?php endwhile; ?>
    </tbody>
  </table>

</main>
<?php include __DIR__ . '/../includes/footer.php'; ?>
