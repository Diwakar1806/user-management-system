<?php
session_start();
require 'config/db.php'; // Pull in your database connection

$error_msg = "";
$success_msg = "";

// Check if the form was submitted
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Capture and sanitize the input data
    $name = trim($_POST['name']);
    $email = trim($_POST['email']);
    $password = $_POST['password'];
    $confirm_password = $_POST['confirm_password'];

    // 1. Backend Validation
    if (empty($name) || empty($email) || empty($password)) {
        $error_msg = "All fields are required.";
    } elseif ($password !== $confirm_password) {
        $error_msg = "Passwords do not match.";
    } else {
        // 2. Check if the email is already in the database
        $check_stmt = $conn->prepare("SELECT id FROM users WHERE email = ?");
        $check_stmt->bind_param("s", $email); // 's' means string
        $check_stmt->execute();
        $check_stmt->store_result();

        if ($check_stmt->num_rows > 0) {
            $error_msg = "This email is already registered.";
        } else {
            // 3. Hash the password securely (Task-3 Requirement)
            $hashed_password = password_hash($password, PASSWORD_DEFAULT);

            // 4. Insert the new user into the database
            $insert_stmt = $conn->prepare("INSERT INTO users (full_name, email, password) VALUES (?, ?, ?)");
            $insert_stmt->bind_param("sss", $name, $email, $hashed_password);

            if ($insert_stmt->execute()) {
                $success_msg = "Registration successful! You can now login.";
            } else {
                $error_msg = "Something went wrong. Please try again.";
            }
            $insert_stmt->close();
        }
        $check_stmt->close();
    }
}
?>
<!doctype html>
<html lang="en">
  <head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Register — Diwakar.Dev</title>
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
                <h4 class="card-title mb-3 text-center">Create Account</h4>
                <?php if (!empty($error_msg)): ?>
                    <div class="alert alert-danger"><?php echo $error_msg; ?></div>
                <?php endif; ?>

                <?php if (!empty($success_msg)): ?>
                    <div class="alert alert-success"><?php echo $success_msg; ?></div>
                <?php endif; ?>
                <form id="register-form" method="POST" action="register.php" novalidate>
                  <div class="mb-3">
                    <label for="register-name" class="form-label">Full Name</label>
                    <input type="text" class="form-control" id="register-name" name="name" required>
                    <div class="invalid-feedback">Please enter your full name.</div>
                  </div>

                  <div class="mb-3">
                    <label for="register-email" class="form-label">Email</label>
                    <input type="email" class="form-control" id="register-email" name="email" required>
                    <div id="email-taken-msg" class="form-text text-danger d-none">This email is already taken.</div>
                    <div class="invalid-feedback">Please enter a valid email.</div>
                  </div>

                  <div class="mb-3">
                    <label for="register-password" class="form-label">Password</label>
                    <div class="input-group">
                      <input type="password" class="form-control" id="register-password" name="password" required>
                      <span class="input-group-text bg-transparent border-start-0">
                        <i class="fas fa-eye toggle-password" data-target="register-password" style="cursor:pointer"></i>
                      </span>
                      <div class="invalid-feedback">Please enter a password.</div>
                    </div>
                  </div>

                  <div class="mb-3">
                    <label for="register-password-confirm" class="form-label">Confirm Password</label>
                    <div class="input-group">
                      <input type="password" class="form-control" id="register-password-confirm" name="confirm_password" required>
                      <span class="input-group-text bg-transparent border-start-0">
                        <i class="fas fa-eye toggle-password" data-target="register-password-confirm" style="cursor:pointer"></i>
                      </span>
                      <div id="password-match-msg" class="form-text text-danger d-none">Passwords do not match.</div>
                      <div class="invalid-feedback">Please confirm your password.</div>
                    </div>
                  </div>

                  <div class="d-grid">
                    <button type="submit" class="btn btn-primary btn-lg">Register</button>
                  </div>
                </form>

                <div class="mt-3 text-center small">
                  Already have an account? <a href="login.html">Login here</a>
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
