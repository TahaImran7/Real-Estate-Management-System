<?php
// admin/delete_property.php
require_once __DIR__ . '/../includes/header.php';
require_once __DIR__ . '/../includes/db_connect.php';

// protect
if (empty($_SESSION['user_id']) || ($_SESSION['role'] ?? '') !== 'admin') {
    header('Location: ../login.php');
    exit;
}

$id = intval($_GET['id'] ?? 0);
if ($id <= 0) {
    header('Location: manage_properties.php');
    exit;
}

// fetch property to get image filename
$stmt = mysqli_prepare($conn, "SELECT image FROM properties WHERE property_id = ? LIMIT 1");
mysqli_stmt_bind_param($stmt, "i", $id);
mysqli_stmt_execute($stmt);
mysqli_stmt_bind_result($stmt, $imageName);
mysqli_stmt_fetch($stmt);
mysqli_stmt_close($stmt);

// delete DB row
$stmt = mysqli_prepare($conn, "DELETE FROM properties WHERE property_id = ? LIMIT 1");
mysqli_stmt_bind_param($stmt, "i", $id);
$ok = mysqli_stmt_execute($stmt);
mysqli_stmt_close($stmt);

if ($ok) {
    // remove image file if exists
    if (!empty($imageName)) {
        $filePath = __DIR__ . '/../uploads/' . $imageName;
        if (file_exists($filePath)) {
            @unlink($filePath);
        }
    }
    $_SESSION['flash_success'] = "Property deleted.";
} else {
    $_SESSION['flash_error'] = "Could not delete property.";
}

header('Location: manage_properties.php');
exit;
