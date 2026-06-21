<!doctype html>
<html lang="en">
<?php
session_start();
require 'config/db.php'; // Pull in your database connection

// If the user is already logged in, redirect them to the dashboard
if (isset($_SESSION['user_id'])) {
    header("Location: dashboard.php");
    exit();
}

$error_msg = "";

// Check if the form was submitted
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $email = trim($_POST['email']);
    $password = trim($_POST['password']);

    if (empty($email) || empty($password)) {
        $error_msg = "Please enter both email and password.";
    } else {
        // 1. Find the user by email
        $stmt = $conn->prepare("SELECT id, full_name, password, role FROM users WHERE email = ?");
        $stmt->bind_param("s", $email);
        $stmt->execute();
        $stmt->store_result();

        // 2. If user exists, verify the password
        if ($stmt->num_rows > 0) {
            $stmt->bind_result($id, $full_name, $hashed_password, $role);
            $stmt->fetch();

            // password_verify() checks the entered password against the scrambled hash
            if (password_verify($password, $hashed_password)) {
                // 3. Password is correct! Set session variables.
                $_SESSION['user_id'] = $id;
                $_SESSION['user_name'] = $full_name;
                $_SESSION['user_role'] = $role;

                // 4. Redirect to the secure dashboard
                header("Location: dashboard.php");
                exit();
            } else {
                $error_msg = "Invalid password.";
            }
        } else {
            $error_msg = "No account found with that email.";
        }
        $stmt->close();
    }
}
?>
  <head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Login — Diwakar.Dev</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;600;700&display=swap" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="assets/css/style.css" rel="stylesheet">
  </head>
  <body>
    <div id="navbar-placeholder"></div>

    <main class="py-5">
      <div class="container">
        <div class="row justify-content-center align-items-center">
          <div class="col-12 col-md-6 col-lg-5">
            <div class="card form-card mt-4">
              <div class="card-body">
                <h4 class="card-title mb-3 text-center">Welcome Back</h4>
                <?php if (!empty($error_msg)): ?>
                    <div class="alert alert-danger"><?php echo $error_msg; ?></div>
                <?php endif; ?>
                <form id="login-form" method="POST" action="login.php" novalidate>
                  <div class="mb-3">
                    <label for="login-email" class="form-label">Email</label>
                    <input type="email" class="form-control" id="login-email" name="email" required>
                    <div class="invalid-feedback">Please enter your email.</div>
                  </div>

                  <div class="mb-3">
                    <label for="login-password" class="form-label">Password</label>
                    <div class="input-group">
                      <input type="password" class="form-control" id="login-password" name="password" required>
                      <span class="input-group-text bg-transparent border-start-0">
                        <i class="fas fa-eye toggle-password" data-target="login-password" style="cursor:pointer"></i>
                      </span>
                      <div class="invalid-feedback">Please enter your password.</div>
                    </div>
                  </div>

                  <div class="d-grid">
                    <button type="submit" class="btn btn-primary btn-lg">Login</button>
                  </div>
                </form>

                <div class="mt-3 text-center small">
                  Don't have an account? <a href="register.html">Register here</a>
                </div>
              </div>
            </div>
          </div>
        </div>
      </div>
    </main>

    <div id="footer-placeholder"></div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
    <script src="assets/js/main.js"></script>
  </body>
</html>
