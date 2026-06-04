<?php
// ============================================
// GlamCart — Database Connection
// includes/db.php
// ============================================

// Database credentials — update if needed
define('DB_HOST', 'localhost');
define('DB_USER', 'root');       // Default XAMPP username
define('DB_PASS', '');           // Default XAMPP password (empty)
define('DB_NAME', 'glamcart');

// Create connection using MySQLi
$conn = mysqli_connect(DB_HOST, DB_USER, DB_PASS, DB_NAME);

// Check connection
if (!$conn) {
    die("<div style='font-family:sans-serif;color:red;padding:20px;'>
        <h2>Database Connection Failed</h2>
        <p>Error: " . mysqli_connect_error() . "</p>
        <p>Please make sure XAMPP is running and you have imported <b>schema.sql</b>.</p>
    </div>");
}

// Set charset to UTF-8
mysqli_set_charset($conn, "utf8mb4");

// Helper: sanitize user input to prevent SQL injection
function sanitize($conn, $data) {
    return mysqli_real_escape_string($conn, trim($data));
}
?>
