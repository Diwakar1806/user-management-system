<?php
session_start();
require 'config/db.php';

// Security Check: Kick out unauthenticated users OR regular users
if (!isset($_SESSION['user_id']) || $_SESSION['user_role'] !== 'admin') {
    header("Location: dashboard.php"); // Redirect non-admins to the regular dashboard
    exit();
}

// Fetch all users from the database (The 'R' in CRUD: Read)
$result = $conn->query("SELECT id, full_name, email, role, profile_image, created_at FROM users ORDER BY created_at DESC");
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Admin Dashboard</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="assets/css/style.css" rel="stylesheet">
</head>
<body>
    <div class="container mt-5">
        <div class="d-flex justify-content-between align-items-center mb-4">
            <h2 class="text-white">Admin Dashboard: Manage Users</h2>
            <div>
                <a href="dashboard.php" class="btn btn-secondary me-2">My Profile</a>
                <a href="logout.php" class="btn btn-danger">Logout</a>
            </div>
        </div>

        <div class="card form-card">
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table table-hover align-middle">
                        <thead class="table-dark">
                            <tr>
                                <th>ID</th>
                                <th>Profile</th>
                                <th>Name</th>
                                <th>Email</th>
                                <th>Role</th>
                                <th>Joined</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php if ($result->num_rows > 0): ?>
                                <?php while($row = $result->fetch_assoc()): ?>
                                    <tr>
                                        <td><?php echo $row['id']; ?></td>
                                        <td>
                                            <img src="uploads/<?php echo htmlspecialchars($row['profile_image']); ?>" alt="Profile" class="rounded-circle" style="width: 40px; height: 40px; object-fit: cover;">
                                        </td>
                                        <td><?php echo htmlspecialchars($row['full_name']); ?></td>
                                        <td><?php echo htmlspecialchars($row['email']); ?></td>
                                        <td>
                                            <span class="badge <?php echo ($row['role'] === 'admin') ? 'bg-danger' : 'bg-primary'; ?>">
                                                <?php echo ucfirst($row['role']); ?>
                                            </span>
                                        </td>
                                        <td><?php echo date('M j, Y', strtotime($row['created_at'])); ?></td>
                                        <td>
                                            <a href="admin_edit_user.php?id=<?php echo $row['id']; ?>" class="btn btn-sm btn-warning">Edit</a>
                                            
                                            <form action="admin_delete_user.php" method="POST" class="d-inline" onsubmit="return confirm('Are you sure you want to delete this user?');">
                                                <input type="hidden" name="user_id" value="<?php echo $row['id']; ?>">
                                                <button type="submit" class="btn btn-sm btn-danger">Delete</button>
                                            </form>
                                        </td>
                                    </tr>
                                <?php endwhile; ?>
                            <?php else: ?>
                                <tr>
                                    <td colspan="7" class="text-center">No users found.</td>
                                </tr>
                            <?php endif; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</body>
</html>