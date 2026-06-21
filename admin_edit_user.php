<?php
session_start();
require 'config/db.php';

// Security Check: Only Admins allowed 
if (!isset($_SESSION['user_id']) || $_SESSION['user_role'] !== 'admin') {
    header("Location: dashboard.php");
    exit();
}

$msg = "";
$msg_class = "";

// Check if an ID was passed in the URL
if (!isset($_GET['id']) || empty($_GET['id'])) {
    header("Location: admin_dashboard.php");
    exit();
}

$target_user_id = $_GET['id'];

// Handle the form submission (The 'U' in CRUD: Update) [cite: 394]
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $new_name = trim($_POST['full_name']);
    $new_role = $_POST['role'];

    // Prevent the admin from demoting themselves by accident!
    if ($target_user_id == $_SESSION['user_id'] && $new_role == 'user') {
        $msg = "You cannot remove your own Admin privileges.";
        $msg_class = "alert-danger";
    } else {
        // Update the user using Prepared Statements [cite: 405]
        $update_stmt = $conn->prepare("UPDATE users SET full_name = ?, role = ? WHERE id = ?");
        $update_stmt->bind_param("ssi", $new_name, $new_role, $target_user_id);
        
        if ($update_stmt->execute()) {
            $msg = "User updated successfully!";
            $msg_class = "alert-success";
        } else {
            $msg = "Error updating user.";
            $msg_class = "alert-danger";
        }
        $update_stmt->close();
    }
}

// Fetch the target user's current data to fill the form [cite: 393]
$stmt = $conn->prepare("SELECT full_name, email, role FROM users WHERE id = ?");
$stmt->bind_param("i", $target_user_id);
$stmt->execute();
$stmt->bind_result($user_name, $user_email, $user_role);
$stmt->fetch();
$stmt->close();

// If user doesn't exist, kick back to dashboard
if (empty($user_email)) {
    header("Location: admin_dashboard.php");
    exit();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Edit User — Admin</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="assets/css/style.css" rel="stylesheet">
</head>
<body>
    <div class="container mt-5">
        <div class="row justify-content-center">
            <div class="col-md-6">
                <div class="card form-card">
                    <div class="card-body">
                        <h3 class="text-center mb-4">Edit User Account</h3>
                        
                        <?php if($msg): ?>
                            <div class="alert <?php echo $msg_class; ?>"><?php echo $msg; ?></div>
                        <?php endif; ?>

                        <form method="POST" action="">
                            <div class="mb-3">
                                <label for="email" class="form-label">Email Address (Cannot be changed)</label>
                                <input type="email" class="form-control" id="email" value="<?php echo htmlspecialchars($user_email); ?>" disabled>
                            </div>

                            <div class="mb-3">
                                <label for="full_name" class="form-label">Full Name</label>
                                <input type="text" class="form-control" id="full_name" name="full_name" value="<?php echo htmlspecialchars($user_name); ?>" required>
                            </div>

                            <div class="mb-4">
                                <label for="role" class="form-label">Account Role</label>
                                <select class="form-select" id="role" name="role" required>
                                    <option value="user" <?php echo ($user_role === 'user') ? 'selected' : ''; ?>>Standard User</option>
                                    <option value="admin" <?php echo ($user_role === 'admin') ? 'selected' : ''; ?>>Administrator</option>
                                </select>
                            </div>

                            <div class="d-grid gap-2">
                                <button type="submit" class="btn btn-warning text-dark fw-bold">Update User</button>
                                <a href="admin_dashboard.php" class="btn btn-secondary">Back to User List</a>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
</body>
</html>