<?php
include 'includes/db_connect.php';

// Get property ID from URL
$id = intval($_GET['id'] ?? 0);

if ($id <= 0) {
    header('Location: properties.php');
    exit;
}

// Fetch property from database
$stmt = mysqli_prepare($conn, "SELECT * FROM properties WHERE property_id = ? LIMIT 1");
mysqli_stmt_bind_param($stmt, "i", $id);
mysqli_stmt_execute($stmt);
$result = mysqli_stmt_get_result($stmt);
$property = mysqli_fetch_assoc($result);

if (!$property) {
    echo "Property not found.";
    exit;
}

include 'includes/header.php';
?>

<section class="property-detail">
  <div class="detail-container">
    <div class="detail-image">
      <?php if (!empty($property['image']) && file_exists('admin/uploads/' . $property['image'])): ?>
        <img src="admin/uploads/<?= htmlspecialchars($property['image']) ?>" alt="<?= htmlspecialchars($property['title']) ?>">
      <?php else: ?>
        <img src="assets/img/no-image.png" alt="No Image">
      <?php endif; ?>
    </div>

    <div class="detail-info">
      <h1><?= htmlspecialchars($property['title']) ?></h1>
      <p class="price">Price: Rs. <?= number_format($property['price']) ?></p>
      <p><strong>Location:</strong> <?= htmlspecialchars($property['location']) ?></p>
      <p><strong>Type:</strong> <?= htmlspecialchars($property['property_type']) ?></p>
      <p><strong>Description:</strong> <br><?= nl2br(htmlspecialchars($property['description'])) ?></p>

      <a href="contact.php?property_id=<?= $property['property_id'] ?>" class="btn-primary">Inquire About Property</a>
      <br><br>
      <a href="properties.php" class="btn">← Back to All Properties</a>
    </div>
  </div>
</section>

<?php include 'includes/footer.php'; ?>
