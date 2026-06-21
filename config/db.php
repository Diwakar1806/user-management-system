<?php
// Database configuration variables
$host = "localhost";       // XAMPP default host
$db_user = "root";         // XAMPP default database username
$db_pass = "";             // XAMPP default database password (blank)
$db_name = "user_management_system"; // The database you just created

// Create connection using MySQLi
$conn = mysqli_connect($host, $db_user, $db_pass, $db_name);

// Check if the connection was successful
if (!$conn) {
    die("Database connection failed: " . mysqli_connect_error());
}
?>