<?php
// ============================================
// GlamCart — Login Page
// login.php
// ============================================

session_start();
require_once 'db.php';

// If already logged in, redirect to home
if (isset($_SESSION['user_id'])) {
    header("Location: index.php");
    exit;
}

$error = '';

// Handle login form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email    = sanitize($conn, $_POST['email'] ?? '');
    $password = $_POST['password'] ?? '';

    if (empty($email) || empty($password)) {
        $error = 'Please fill in all fields.';
    } else {
        // Fetch user by email
        $query  = "SELECT * FROM users WHERE email = '$email' LIMIT 1";
        $result = mysqli_query($conn, $query);

        if (mysqli_num_rows($result) === 1) {
            $user = mysqli_fetch_assoc($result);

            // Special case: admin@gmail.com with password admin123
            // (since we stored a demo hash, we check manually too)
            $valid = false;

            if ($email === 'admin@gmail.com' && $password === 'admin123') {
                $valid = true;
            } elseif (password_verify($password, $user['password'])) {
                $valid = true;
            }

            if ($valid) {
                // Set session variables
                $_SESSION['user_id']   = $user['id'];
                $_SESSION['user_name'] = $user['name'];
                $_SESSION['user_role'] = $user['role'];

                $_SESSION['msg']      = "Welcome back, " . $user['name'] . "! 💕";
                $_SESSION['msg_type'] = 'success';

                // Redirect based on role
                if ($user['role'] === 'admin') {
                    header("Location: admin.php");
                } else {
                    header("Location: index.php");
                }
                exit;
            } else {
                $error = 'Incorrect password. Please try again.';
            }
        } else {
            $error = 'No account found with this email.';
        }
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login — GlamCart</title>
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
        <h2>Welcome Back!</h2>
        <p class="subtitle">Sign in to continue shopping 🛍️</p>

        <?php if (!empty($error)): ?>
            <div class="flash flash-error"><?php echo htmlspecialchars($error); ?></div>
        <?php endif; ?>

        <form method="POST" action="login.php">
            <div class="form-group">
                <label for="email">📧 Email Address</label>
                <input type="email" id="email" name="email"
                       placeholder="Enter your email"
                       value="<?php echo isset($_POST['email']) ? htmlspecialchars($_POST['email']) : ''; ?>"
                       required>
            </div>
            <div class="form-group">
                <label for="password">🔒 Password</label>
                <input type="password" id="password" name="password"
                       placeholder="Enter your password" required>
            </div>
            <button type="submit" class="btn btn-primary form-full">
                Login →
            </button>
        </form>

        <!-- Demo credentials hint -->
        <div style="background:var(--pink-pale);border:1px solid var(--border);border-radius:10px;padding:14px;margin-top:20px;font-size:13px;">
            <strong>🔑 Demo Credentials:</strong><br>
            Admin: admin@gmail.com / admin123<br>
            User: tamanna@gmail.com / password
        </div>

        <div class="auth-footer">
            Don't have an account? <a href="signup.php">Sign Up here</a>
        </div>
    </div>
</div>

</body>
</html>
