<?php
// session_start();
include '../includes/db_connect.php';

// OPTIONAL: Check if admin is logged in
// if(!isset($_SESSION['admin_logged_in'])) { header('Location: login.php'); exit; }

// Delete inquiry if requested
if(isset($_GET['delete_id'])) {
    $delete_id = intval($_GET['delete_id']);
    $stmt = mysqli_prepare($conn, "DELETE FROM inquiries WHERE inquiry_id = ?");
    mysqli_stmt_bind_param($stmt, "i", $delete_id);
    mysqli_stmt_execute($stmt);
    mysqli_stmt_close($stmt);
    header('Location: manage_inquiries.php');
    exit;
}

// Fetch all inquiries with property titles
$query = "SELECT i.*, p.title AS property_title 
          FROM inquiries i 
          JOIN properties p ON i.property_id = p.property_id
          ORDER BY i.created_at DESC";
$result = mysqli_query($conn, $query);

include '../includes/header.php';
?>

<div class="admin-container">
    <h2>Manage Inquiries</h2>
    <table class="admin-table">
        <thead>
            <tr>
                <th>ID</th>
                <th>Property</th>
                <th>Name</th>
                <th>Email</th>
                <th>Message</th>
                <th>Date</th>
                <th>Action</th>
            </tr>
        </thead>
        <tbody>
            <?php if(mysqli_num_rows($result) > 0): ?>
                <?php while($row = mysqli_fetch_assoc($result)): ?>
                    <tr>
                        <td><?= $row['inquiry_id'] ?></td>
                        <td><?= htmlspecialchars($row['property_title']) ?></td>
                        <td><?= htmlspecialchars($row['name']) ?></td>
                        <td><?= htmlspecialchars($row['email']) ?></td>
                        <td><?= nl2br(htmlspecialchars($row['message'])) ?></td>
                        <td><?= $row['created_at'] ?></td>
                        <td>
                            <a href="manage_inquiries.php?delete_id=<?= $row['inquiry_id'] ?>" onclick="return confirm('Are you sure?');" class="btn-delete">Delete</a>
                        </td>
                    </tr>
                <?php endwhile; ?>
            <?php else: ?>
                <tr><td colspan="7">No inquiries yet.</td></tr>
            <?php endif; ?>
        </tbody>
    </table>
</div>

<?php include '../includes/footer.php'; ?>
