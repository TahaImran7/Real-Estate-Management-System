<?php
include 'includes/db_connect.php';

$property_id = intval($_GET['property_id'] ?? 0);

if ($property_id <= 0) {
    header('Location: properties.php');
    exit;
}

// fetch property title (optional)
$stmt = mysqli_prepare($conn, "SELECT title FROM properties WHERE property_id = ? LIMIT 1");
mysqli_stmt_bind_param($stmt, "i", $property_id);
mysqli_stmt_execute($stmt);
$result = mysqli_stmt_get_result($stmt);
$property = mysqli_fetch_assoc($result);

if (!$property) {
    echo "Property not found.";
    exit;
}

$msg = '';
$errors = [];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name = trim($_POST['name'] ?? '');
    $email = trim($_POST['email'] ?? '');
    $message = trim($_POST['message'] ?? '');

    if (!$name || !$email || !$message) {
        $errors[] = "All fields are required.";
    } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $errors[] = "Invalid email address.";
    }

    if (empty($errors)) {
        $stmt = mysqli_prepare($conn, "INSERT INTO inquiries (property_id, name, email, message) VALUES (?, ?, ?, ?)");
        mysqli_stmt_bind_param($stmt, "isss", $property_id, $name, $email, $message);
        $ok = mysqli_stmt_execute($stmt);
        mysqli_stmt_close($stmt);

        if ($ok) {
            $msg = "Your inquiry has been sent successfully.";
        } else {
            $errors[] = "Failed to submit inquiry. Please try again.";
        }
    }
}

include 'includes/header.php';
?>

<section class="contact-form">
  <h2>Inquire About: <?= htmlspecialchars($property['title']) ?></h2>

  <?php if ($msg): ?>
    <p style="color:green;"><?= $msg ?></p>
  <?php endif; ?>

  <?php foreach($errors as $e) echo "<p style='color:red;'>".htmlspecialchars($e)."</p>"; ?>

  <form method="post" class="form-card">
    <label>Name:
      <input type="text" name="name" required>
    </label>

    <label>Email:
      <input type="email" name="email" required>
    </label>

    <label>Message:
      <textarea name="message" rows="5" required></textarea>
    </label>

    <button type="submit" class="btn-primary">Send Inquiry</button>
  </form>

  <p><a href="property_details.php?id=<?= $property_id ?>" class="btn">← Back to Property</a></p>
</section>

<?php include 'includes/footer.php'; ?>
