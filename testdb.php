<?php
include 'includes/db_connect.php';

$sql = "SELECT DATABASE()";
$result = mysqli_query($conn, $sql);
$row = mysqli_fetch_array($result);

echo "✅Connected to database: " . $row[0];
?>