<?php
session_start();
require 'config/db.php';

// Security: Kick out unauthenticated users
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

$user_id = $_SESSION['user_id'];
$msg = "";
$msg_class = "";

// 1. Fetch current user data to display in the form
$stmt = $conn->prepare("SELECT full_name, email, profile_image FROM users WHERE id = ?");
$stmt->bind_param("i", $user_id);
$stmt->execute();
$stmt->bind_result($current_name, $current_email, $current_image);
$stmt->fetch();
$stmt->close();

// 2. Handle Form Submission
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $new_name = trim($_POST['name']);

    // Check if an image was actually uploaded
    if (isset($_FILES['profile_pic']) && $_FILES['profile_pic']['error'] === UPLOAD_ERR_OK) {
        
        // Validation Rules
        $allowed_types = ['image/jpeg', 'image/png', 'image/gif'];
        $max_size = 2 * 1024 * 1024; // 2MB maximum size
        
        $file_type = $_FILES['profile_pic']['type'];
        $file_size = $_FILES['profile_pic']['size'];

        // Validate Type and Size
        if (!in_array($file_type, $allowed_types)) {
            $msg = "Invalid file type. Only JPG, PNG, and GIF are allowed.";
            $msg_class = "alert-danger";
        } elseif ($file_size > $max_size) {
            $msg = "File is too large. Maximum size is 2MB.";
            $msg_class = "alert-danger";
        } else {
            // Generate a secure, unique filename to prevent overwriting
            $ext = pathinfo($_FILES['profile_pic']['name'], PATHINFO_EXTENSION);
            $new_filename = "user_" . $user_id . "_" . time() . "." . $ext;
            $upload_path = "uploads/" . $new_filename;

            // Move the file to your uploads folder
            if (move_uploaded_file($_FILES['profile_pic']['tmp_name'], $upload_path)) {
                // Update Database with Name AND Image
                $update_stmt = $conn->prepare("UPDATE users SET full_name = ?, profile_image = ? WHERE id = ?");
                $update_stmt->bind_param("ssi", $new_name, $new_filename, $user_id);
                $update_stmt->execute();
                $update_stmt->close();

                $_SESSION['user_name'] = $new_name; // Update session
                $current_name = $new_name;
                $current_image = $new_filename;
                $msg = "Profile and image updated successfully!";
                $msg_class = "alert-success";
            } else {
                $msg = "Failed to upload image to server.";
                $msg_class = "alert-danger";
            }
        }
    } else {
        // If no image was uploaded, just update the name
        $update_stmt = $conn->prepare("UPDATE users SET full_name = ? WHERE id = ?");
        $update_stmt->bind_param("si", $new_name, $user_id);
        $update_stmt->execute();
        $update_stmt->close();
        
        $_SESSION['user_name'] = $new_name;
        $current_name = $new_name;
        $msg = "Profile name updated successfully!";
        $msg_class = "alert-success";
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Edit Profile</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="assets/css/style.css" rel="stylesheet">
</head>
<body>
    <div class="container mt-5">
        <div class="row justify-content-center">
            <div class="col-md-6">
                <div class="card form-card">
                    <div class="card-body">
                        <h3 class="text-center mb-4">Edit Profile</h3>
                        
                        <?php if($msg): ?>
                            <div class="alert <?php echo $msg_class; ?>"><?php echo $msg; ?></div>
                        <?php endif; ?>

                        <form method="POST" action="edit_profile.php" enctype="multipart/form-data">
                            
                            <div class="text-center mb-4">
                                <img src="uploads/<?php echo htmlspecialchars($current_image); ?>" alt="Profile Picture" class="rounded-circle" style="width: 150px; height: 150px; object-fit: cover; border: 3px solid var(--color-purple);">
                            </div>

                            <div class="mb-3">
                                <label for="name" class="form-label">Full Name</label>
                                <input type="text" class="form-control" id="name" name="name" value="<?php echo htmlspecialchars($current_name); ?>" required>
                            </div>

                            <div class="mb-3">
                                <label for="email" class="form-label">Email (Read Only)</label>
                                <input type="email" class="form-control" id="email" value="<?php echo htmlspecialchars($current_email); ?>" disabled>
                            </div>

                            <div class="mb-3">
                                <label for="profile_pic" class="form-label">Update Profile Picture (Max 2MB)</label>
                                <input type="file" class="form-control" id="profile_pic" name="profile_pic" accept="image/jpeg, image/png, image/gif">
                            </div>

                            <div class="d-grid gap-2">
                                <button type="submit" class="btn btn-primary">Save Changes</button>
                                <a href="dashboard.php" class="btn btn-secondary">Back to Dashboard</a>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
</body>
</html>