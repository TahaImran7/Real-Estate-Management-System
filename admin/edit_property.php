<?php
// admin/edit_property.php
require_once __DIR__ . '/../includes/header.php';
require_once __DIR__ . '/../includes/db_connect.php';

// protect
if (empty($_SESSION['user_id']) || ($_SESSION['role'] ?? '') !== 'admin') {
    header('Location: ../login.php');
    exit;
}

$id = intval($_GET['id'] ?? 0);
if ($id <= 0) {
    header('Location: manage_property.php');
    exit;
}

$errors = [];
$msg = '';

// fetch existing
$stmt = mysqli_prepare($conn, "SELECT title, price, location, property_type, description, image FROM properties WHERE property_id = ? LIMIT 1");
mysqli_stmt_bind_param($stmt, "i", $id);
mysqli_stmt_execute($stmt);
mysqli_stmt_bind_result($stmt, $title, $price, $location, $ptype, $description, $image);
mysqli_stmt_fetch($stmt);
mysqli_stmt_close($stmt);

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $title_new = trim($_POST['title'] ?? '');
    $price_new = trim($_POST['price'] ?? '');
    $location_new = trim($_POST['location'] ?? '');
    $ptype_new = trim($_POST['property_type'] ?? '');
    $desc_new = trim($_POST['description'] ?? '');
    $image_new_name = $image; // default keep existing

    if (!$title_new || !$price_new || !$location_new || !$ptype_new) {
        $errors[] = "Please fill the required fields.";
    }

    // handle image replacement
    if (!empty($_FILES['image']['name'])) {
        
        $fileName = time() . "_" . basename($_FILES['image']['name']);
        $targetDir = "uploads/$fileame";
        $targetFile = $targetDir . $fileName;
        $fileType = strtolower(pathinfo($targetFile, PATHINFO_EXTENSION));
        $allowed = ['jpg','jpeg','png','gif'];

        if (!in_array($fileType, $allowed)) {
            $errors[] = "Only JPG, PNG, GIF allowed.";
        } elseif ($_FILES['image']['size'] > 2 * 1024 * 1024) {
            $errors[] = "Image must be under 2MB.";
        } else {
            if (move_uploaded_file($_FILES['image']['tmp_name'], $targetFile)) {
                // delete old image
                if (!empty($image) && file_exists(__DIR__ . '/../uploads/' . $image)) {
                    @unlink(__DIR__ . '/../uploads/' . $image);
                }
                $image_new_name = $fileName;
            } else {
                $errors[] = "Image upload failed.";
            }
        }
    }

    if (empty($errors)) {
        $stmt = mysqli_prepare($conn, "UPDATE properties SET title = ?, price = ?, location = ?, property_type = ?, description = ?, image = ? WHERE property_id = ? LIMIT 1");
        mysqli_stmt_bind_param($stmt, "sdssssi", $title_new, $price_new, $location_new, $ptype_new, $desc_new, $image_new_name, $id);
        $ok = mysqli_stmt_execute($stmt);
        mysqli_stmt_close($stmt);

        if ($ok) {
            $msg = "Property updated.";
            // update current vars for showing in form
            $title = $title_new; $price = $price_new; $location = $location_new; $ptype = $ptype_new; $description = $desc_new; $image = $image_new_name;
        } else {
            $errors[] = "Failed to update property.";
        }
    }
}
?>
<main class="admin-dashboard">
  <h1>Edit Property</h1>
  <?php if ($msg): ?><p style="color:green;"><?= htmlspecialchars($msg) ?></p><?php endif; ?>
  <?php foreach($errors as $e) echo "<p style='color:red;'>".htmlspecialchars($e)."</p>"; ?>

  <form method="post" enctype="multipart/form-data" class="form-card">
    <label>Title
      <input type="text" name="title" value="<?= htmlspecialchars($title) ?>" required>
    </label>
    <label>Price
      <input type="number" step="0.01" name="price" value="<?= htmlspecialchars($price) ?>" required>
    </label>
    <label>Location
      <input type="text" name="location" value="<?= htmlspecialchars($location) ?>" required>
    </label>
    <label>Property Type
      <select name="property_type" required>
        <option value="">-- Select Type --</option>
        <?php
          $types = ['Apartments','Villas','Plots','Offices'];
          foreach ($types as $t) {
              $sel = ($t === $ptype) ? 'selected' : '';
              echo "<option value=\"$t\" $sel>$t</option>";
          }
        ?>
      </select>
    </label>
    <label>Description
      <textarea name="description" rows="4"><?= htmlspecialchars($description) ?></textarea>
    </label>

    <label>Current Image
      <div style="margin:6px 0;">
        <?php if (!empty($image) && file_exists(__DIR__ . '/../uploads/' . $image)): ?>
          <img src="../uploads/<?= htmlspecialchars($image) ?>" alt="" style="width:140px;height:90px;object-fit:cover;border-radius:6px;">
        <?php else: ?>
          <span style="color:#888">No image</span>
        <?php endif; ?>
      </div>
    </label>

    <label>Replace Image (optional)
      <input type="file" name="image" accept="image/*">
    </label>

    <button type="submit" class="btn-primary">Save Changes</button>
  </form>

  <p><a href="manage_property.php" class="btn">← Back to Manage Properties</a></p>
</main>

<?php include __DIR__ . '/../includes/footer.php'; ?>
