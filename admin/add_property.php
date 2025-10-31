<?php
// admin/add_property.php
require_once __DIR__ . '/../includes/header.php';
require_once __DIR__ . '/../includes/db_connect.php';

// Protect admin
if (empty($_SESSION['user_id']) || ($_SESSION['role'] ?? '') !== 'admin') {
    header('Location: ../login.php');
    exit;
}

$msg = '';
$errors = [];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $title = trim($_POST['title'] ?? '');
    $price = trim($_POST['price'] ?? '');
    $location = trim($_POST['location'] ?? '');
    $type = trim($_POST['property_type'] ?? '');
    $desc = trim($_POST['description'] ?? '');
    $image = null;

    if (!$title || !$price || !$location || !$type) {
        $errors[] = "Please fill all required fields.";
    }

    // Handle image upload
    if (!empty($_FILES['image']['name'])) {
        $targetDir = "../uploads/";
        $fileName = time() . "_" . basename($_FILES['image']['name']);
        $targetFile = $targetDir . $fileName;
        $fileType = strtolower(pathinfo($targetFile, PATHINFO_EXTENSION));

        $allowedTypes = ['jpg','jpeg','png','gif'];
        if (!in_array($fileType, $allowedTypes)) {
            $errors[] = "Only JPG, JPEG, PNG, or GIF files are allowed.";
        } elseif ($_FILES['image']['size'] > 2 * 1024 * 1024) {
            $errors[] = "Image size should be under 2MB.";
        } else {
            if (move_uploaded_file($_FILES['image']['tmp_name'], $targetFile)) {
                $image = $fileName;
            } else {
                $errors[] = "Error uploading image.";
            }
        }
    }

    if (empty($errors)) {
        $stmt = mysqli_prepare($conn, "INSERT INTO properties (title, price, location, property_type, description, image) VALUES (?, ?, ?, ?, ?, ?)");
        mysqli_stmt_bind_param($stmt, "sdssss", $title, $price, $location, $type, $desc, $image);
        if (mysqli_stmt_execute($stmt)) {
            $msg = "Property added successfully!";
        } else {
            $errors[] = "Failed to add property.";
        }
        mysqli_stmt_close($stmt);
    }
}
?>

<main class="admin-dashboard">
  <h1>Add Property</h1>
  <?php if ($msg): ?><p style="color:green;"><?= htmlspecialchars($msg) ?></p><?php endif; ?>
  <?php if ($errors): foreach($errors as $e) echo "<p style='color:red;'>".htmlspecialchars($e)."</p>"; endif; ?>

  <form method="post" enctype="multipart/form-data" class="form-card">
    <label>Title
      <input type="text" name="title" required>
    </label>
    <label>Price
      <input type="number" step="0.01" name="price" required>
    </label>
    <label>Location
      <input type="text" name="location" required>
    </label>
    <label>Property Type
      <select name="property_type" required>
        <option value="">-- Select Type --</option>
        <option value="Apartment">Apartment</option>
        <option value="House">House</option>
        <option value="Commercial">Commercial</option>
        <option value="Plot">Plot</option>
      </select>
    </label>
    <label>Description
      <textarea name="description" rows="4"></textarea>
    </label>
    <label>Image
      <input type="file" name="image" accept="image/*">
    </label>
    <button type="submit" class="btn-primary">Add Property</button>
  </form>

  <p><a href="dashboard.php" class="btn">← Back to Dashboard</a></p>
</main>

<?php include __DIR__ . '/../includes/footer.php'; ?>
