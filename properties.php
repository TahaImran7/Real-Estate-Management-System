<?php
include 'includes/db_connect.php';
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>All Properties - Real Estate Management System</title>
    <link rel="stylesheet" href="assets/css/style.css">
</head>
<body>
    <?php include 'includes/header.php'; ?>

    <section class="property-list">
        <h2>Available Properties</h2>
        <div class="property-container">
            <?php
            $query = "SELECT * FROM properties ORDER BY property_id DESC";
            $result = mysqli_query($conn, $query);

            if (mysqli_num_rows($result) > 0) {
                while ($row = mysqli_fetch_assoc($result)) {
                    echo '
                    <div class="property-card">
                        <img src="admin/uploads/' . $row['image'] . '" alt="' . $row['title'] . '">
                        <h3>' . $row['title'] . '</h3>
                        <p>Location: ' . $row['location'] . '</p>
                        <p class="price">Rs. ' . number_format($row['price']) . '</p>
                        <a href="property-detail.php?id=' . $row['id'] . '" class="btn">View Details</a>
                    </div>';
                }
            } else {
                echo "<p>No properties available yet.</p>";
            }
            ?>
        </div>
    </section>

    <?php include 'includes/footer.php'; ?>
</body>
</html>
