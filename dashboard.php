<?php
session_start();
require 'config/db.php';

if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

// Fetch the latest profile image dynamically
$user_id = $_SESSION['user_id'];
$stmt = $conn->prepare("SELECT profile_image FROM users WHERE id = ?");
$stmt->bind_param("i", $user_id);
$stmt->execute();
$stmt->bind_result($profile_image);
$stmt->fetch();
$stmt->close();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Dashboard</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="assets/css/style.css" rel="stylesheet">
</head>
<body>
    <div class="container mt-5">
        <div class="row justify-content-center">
            <div class="col-md-8">
                <div class="card form-card">
                    <div class="card-body text-center">
                        <img src="uploads/<?php echo htmlspecialchars($profile_image); ?>" alt="Profile" class="rounded-circle mb-3" style="width: 120px; height: 120px; object-fit: cover; border: 3px solid var(--color-purple);">
                        
                        <h2>Welcome, <?php echo htmlspecialchars($_SESSION['user_name']); ?>!</h2>
                        <p class="text-muted">Role: <strong><?php echo htmlspecialchars($_SESSION['user_role']); ?></strong></p>
                        
                        <div class="mt-4">
                            <a href="edit_profile.php" class="btn btn-primary mx-2">Edit Profile</a>
                            <a href="logout.php" class="btn btn-danger mx-2">Logout</a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</body>
</html>