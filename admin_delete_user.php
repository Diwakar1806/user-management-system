<?php
session_start();
require 'config/db.php';

// Security Check
if (!isset($_SESSION['user_id']) || $_SESSION['user_role'] !== 'admin') {
    header("Location: dashboard.php");
    exit();
}

if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['user_id'])) {
    $target_user_id = $_POST['user_id'];

    // Prevent the admin from accidentally deleting themselves!
    if ($target_user_id == $_SESSION['user_id']) {
        echo "<script>alert('You cannot delete your own admin account.'); window.location.href='admin_dashboard.php';</script>";
        exit();
    }

    // Execute the deletion using Prepared Statements
    $stmt = $conn->prepare("DELETE FROM users WHERE id = ?");
    $stmt->bind_param("i", $target_user_id);
    
    if ($stmt->execute()) {
        header("Location: admin_dashboard.php?msg=UserDeleted");
    } else {
        echo "<script>alert('Error deleting user.'); window.location.href='admin_dashboard.php';</script>";
    }
    $stmt->close();
} else {
    header("Location: admin_dashboard.php");
}
?>