<?php
// ============================================
// GlamCart — Sign Up Page
// signup.php
// ============================================

session_start();
require_once 'db.php';

// If already logged in, redirect to home
if (isset($_SESSION['user_id'])) {
    header("Location: index.php");
    exit;
}

$error   = '';
$success = '';

// Handle registration form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name     = sanitize($conn, $_POST['name'] ?? '');
    $email    = sanitize($conn, $_POST['email'] ?? '');
    $password = $_POST['password'] ?? '';
    $confirm  = $_POST['confirm_password'] ?? '';

    // Validation
    if (empty($name) || empty($email) || empty($password) || empty($confirm)) {
        $error = 'Please fill in all fields.';
    } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $error = 'Please enter a valid email address.';
    } elseif (strlen($password) < 6) {
        $error = 'Password must be at least 6 characters long.';
    } elseif ($password !== $confirm) {
        $error = 'Passwords do not match.';
    } else {
        // Check if email already exists
        $check = mysqli_query($conn, "SELECT id FROM users WHERE email = '$email' LIMIT 1");
        if (mysqli_num_rows($check) > 0) {
            $error = 'An account with this email already exists.';
        } else {
            // Hash the password securely
            $hashed = password_hash($password, PASSWORD_DEFAULT);

            // Insert new user
            $insert = "INSERT INTO users (name, email, password, role)
                       VALUES ('$name', '$email', '$hashed', 'user')";

            if (mysqli_query($conn, $insert)) {
                $success = 'Account created successfully! You can now login. 🎉';
            } else {
                $error = 'Something went wrong. Please try again.';
            }
        }
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Sign Up — GlamCart</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>

<header>
    <div class="navbar">
        <a href="index.php" class="logo">Glam<span>Cart</span></a>
        <div class="nav-actions">
            <a href="index.php">← Back to Store</a>
        </div>
    </div>
</header>

<div class="auth-page">
    <div class="auth-card">
        <div class="auth-logo">💄 GlamCart</div>
        <h2>Create Account</h2>
        <p class="subtitle">Join thousands of fashion lovers 💕</p>

        <?php if (!empty($error)): ?>
            <div class="flash flash-error"><?php echo htmlspecialchars($error); ?></div>
        <?php endif; ?>

        <?php if (!empty($success)): ?>
            <div class="flash flash-success"><?php echo htmlspecialchars($success); ?></div>
            <div style="text-align:center;margin-top:14px;">
                <a href="login.php" class="btn btn-primary">Go to Login →</a>
            </div>
        <?php else: ?>

        <form method="POST" action="signup.php">
            <div class="form-group">
                <label for="name">👤 Full Name</label>
                <input type="text" id="name" name="name"
                       placeholder="Enter your full name"
                       value="<?php echo isset($_POST['name']) ? htmlspecialchars($_POST['name']) : ''; ?>"
                       required>
            </div>
            <div class="form-group">
                <label for="email">📧 Email Address</label>
                <input type="email" id="email" name="email"
                       placeholder="Enter your email address"
                       value="<?php echo isset($_POST['email']) ? htmlspecialchars($_POST['email']) : ''; ?>"
                       required>
            </div>
            <div class="form-group">
                <label for="password">🔒 Password</label>
                <input type="password" id="password" name="password"
                       placeholder="At least 6 characters" required>
            </div>
            <div class="form-group">
                <label for="confirm_password">🔒 Confirm Password</label>
                <input type="password" id="confirm_password" name="confirm_password"
                       placeholder="Re-enter your password" required>
            </div>
            <button type="submit" class="btn btn-primary form-full">
                Create Account 🎉
            </button>
        </form>

        <?php endif; ?>

        <div class="auth-footer">
            Already have an account? <a href="login.php">Login here</a>
        </div>
    </div>
</div>

</body>
</html>
