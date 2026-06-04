<?php
// ============================================
// GlamCart — Contact Page
// contact.php
// ============================================

session_start();
require_once 'db.php';

$success = '';
$error   = '';

// Cart count for nav
$cart_count = 0;
if (isset($_SESSION['user_id'])) {
    $uid = (int)$_SESSION['user_id'];
    $cart_q = mysqli_query($conn, "SELECT SUM(quantity) as total FROM cart WHERE user_id = $uid");
    $cart_row = mysqli_fetch_assoc($cart_q);
    $cart_count = $cart_row['total'] ?? 0;
}

// Handle contact form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name    = sanitize($conn, $_POST['name']    ?? '');
    $email   = sanitize($conn, $_POST['email']   ?? '');
    $subject = sanitize($conn, $_POST['subject'] ?? '');
    $message = sanitize($conn, $_POST['message'] ?? '');

    if (empty($name) || empty($email) || empty($message)) {
        $error = 'Please fill in all required fields.';
    } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $error = 'Please enter a valid email address.';
    } else {
        // Save message to database
        $sql = "INSERT INTO contacts (name, email, subject, message)
                VALUES ('$name', '$email', '$subject', '$message')";

        if (mysqli_query($conn, $sql)) {
            $success = "Thank you, $name! Your message has been sent. We'll get back to you soon! 💕";
        } else {
            $error = 'Failed to send message. Please try again later.';
        }
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Contact Us — GlamCart</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>

<!-- HEADER -->
<header>
    <div class="nav-top">✨ Free Shipping on orders above ₹999 | New Arrivals Every Week ✨</div>
    <div class="navbar">
        <a href="index.php" class="logo">Glam<span>Cart</span></a>
        <nav class="nav-links">
            <a href="index.php">Home</a>
            <a href="index.php?cat=1">Clothing</a>
            <a href="index.php?cat=2">Footwear</a>
            <a href="index.php?cat=3">Accessories</a>
            <a href="index.php?cat=4">Makeup</a>
            <a href="about.php">About</a>
            <a href="contact.php" class="active">Contact</a>
        </nav>
        <div class="nav-actions">
            <?php if (isset($_SESSION['user_id'])): ?>
                <a href="#">👋 <?php echo htmlspecialchars($_SESSION['user_name']); ?></a>
                <a href="logout.php">Logout</a>
            <?php else: ?>
                <a href="login.php">Login</a>
                <a href="signup.php">Sign Up</a>
            <?php endif; ?>
            <a href="cart.php">
                🛒 Cart
                <?php if ($cart_count > 0): ?>
                    <span class="cart-badge"><?php echo $cart_count; ?></span>
                <?php endif; ?>
            </a>
        </div>
    </div>
</header>

<!-- PAGE HEADER -->
<div class="page-header">
    <h1>📬 Contact Us</h1>
    <p>We'd love to hear from you! Reach out anytime.</p>
    <div class="breadcrumb">
        <a href="index.php">Home</a> › Contact
    </div>
</div>

<div class="section" style="padding-top:50px;">
    <div class="contact-layout">

        <!-- LEFT: Contact Info -->
        <div class="contact-info-box">
            <span class="section-label">Get In Touch</span>
            <h2>We're Here to Help You Shine! ✨</h2>
            <p>
                Have a question about your order, a product, or just want to say hi?
                Our team is always happy to help you. Drop us a message and we'll
                get back to you within 24 hours!
            </p>

            <!-- Tamanna's Contact -->
            <div class="contact-detail">
                <div class="contact-detail-icon">👩‍💼</div>
                <div>
                    <h4>Tamanna Dadhwal — CEO</h4>
                    <p>tamanna@glamcart.com</p>
                    <p>+91 98765 43210</p>
                </div>
            </div>

            <!-- Simran's Contact -->
            <div class="contact-detail">
                <div class="contact-detail-icon">👩‍🎨</div>
                <div>
                    <h4>Simran — Creative Director</h4>
                    <p>simran@glamcart.com</p>
                    <p>+91 98765 43211</p>
                </div>
            </div>

            <!-- General Info -->
            <div class="contact-detail">
                <div class="contact-detail-icon">📍</div>
                <div>
                    <h4>Our Location</h4>
                    <p>GlamCart HQ, Fashion Street,</p>
                    <p>Chandigarh, India — 160001</p>
                </div>
            </div>

            <div class="contact-detail">
                <div class="contact-detail-icon">🕐</div>
                <div>
                    <h4>Business Hours</h4>
                    <p>Monday – Saturday: 10AM – 7PM</p>
                    <p>Sunday: 11AM – 5PM</p>
                </div>
            </div>

            <!-- Social Media -->
            <div style="margin-top:30px;">
                <h4 style="font-size:14px;font-weight:600;margin-bottom:14px;">Follow Us 📱</h4>
                <div style="display:flex;gap:12px;">
                    <a href="#" style="width:42px;height:42px;background:var(--pink-pale);border:1px solid var(--border);border-radius:50%;display:flex;align-items:center;justify-content:center;font-size:18px;transition:var(--transition);" onmouseover="this.style.background='var(--pink)'" onmouseout="this.style.background='var(--pink-pale)'">📘</a>
                    <a href="#" style="width:42px;height:42px;background:var(--pink-pale);border:1px solid var(--border);border-radius:50%;display:flex;align-items:center;justify-content:center;font-size:18px;transition:var(--transition);">📸</a>
                    <a href="#" style="width:42px;height:42px;background:var(--pink-pale);border:1px solid var(--border);border-radius:50%;display:flex;align-items:center;justify-content:center;font-size:18px;">🐦</a>
                    <a href="#" style="width:42px;height:42px;background:var(--pink-pale);border:1px solid var(--border);border-radius:50%;display:flex;align-items:center;justify-content:center;font-size:18px;">▶️</a>
                </div>
            </div>
        </div>

        <!-- RIGHT: Contact Form -->
        <div class="contact-form-box">
            <h3 style="font-family:var(--font-head);font-size:24px;margin-bottom:6px;">Send Us a Message 💌</h3>
            <p style="color:var(--gray);font-size:14px;margin-bottom:26px;">Fill in the form below and we'll get back to you soon!</p>

            <?php if (!empty($error)): ?>
                <div class="flash flash-error"><?php echo htmlspecialchars($error); ?></div>
            <?php endif; ?>

            <?php if (!empty($success)): ?>
                <div class="flash flash-success"><?php echo htmlspecialchars($success); ?></div>
            <?php endif; ?>

            <form method="POST" action="contact.php">
                <div class="form-row">
                    <div class="form-group">
                        <label>👤 Your Name *</label>
                        <input type="text" name="name"
                               placeholder="Your full name"
                               value="<?php echo isset($_POST['name']) ? htmlspecialchars($_POST['name']) : (isset($_SESSION['user_name']) ? htmlspecialchars($_SESSION['user_name']) : ''); ?>"
                               required>
                    </div>
                    <div class="form-group">
                        <label>📧 Your Email *</label>
                        <input type="email" name="email"
                               placeholder="your@email.com"
                               value="<?php echo isset($_POST['email']) ? htmlspecialchars($_POST['email']) : ''; ?>"
                               required>
                    </div>
                </div>

                <div class="form-group">
                    <label>📌 Subject</label>
                    <select name="subject">
                        <option value="">-- Select a Subject --</option>
                        <option value="Order Query"     <?php echo (isset($_POST['subject']) && $_POST['subject']==='Order Query')    ?'selected':''; ?>>📦 Order Query</option>
                        <option value="Product Info"    <?php echo (isset($_POST['subject']) && $_POST['subject']==='Product Info')   ?'selected':''; ?>>👗 Product Information</option>
                        <option value="Return/Refund"   <?php echo (isset($_POST['subject']) && $_POST['subject']==='Return/Refund') ?'selected':''; ?>>↩️ Return / Refund</option>
                        <option value="General Enquiry" <?php echo (isset($_POST['subject']) && $_POST['subject']==='General Enquiry')?'selected':''; ?>>💬 General Enquiry</option>
                        <option value="Feedback"        <?php echo (isset($_POST['subject']) && $_POST['subject']==='Feedback')       ?'selected':''; ?>>⭐ Feedback</option>
                        <option value="Other"           <?php echo (isset($_POST['subject']) && $_POST['subject']==='Other')          ?'selected':''; ?>>🔖 Other</option>
                    </select>
                </div>

                <div class="form-group">
                    <label>💬 Your Message *</label>
                    <textarea name="message" placeholder="Write your message here..." required rows="5"><?php echo isset($_POST['message']) ? htmlspecialchars($_POST['message']) : ''; ?></textarea>
                </div>

                <button type="submit" class="btn btn-primary" style="width:100%;justify-content:center;">
                    📨 Send Message
                </button>
            </form>
        </div>
    </div>
</div>

<!-- FOOTER -->
<footer>
    <div class="footer-main">
        <div class="footer-brand">
            <span class="logo">Glam<span>Cart</span></span>
            <p>Your one-stop fashion destination for everything glam.</p>
            <div class="social-links">
                <a href="#">📘</a><a href="#">📸</a><a href="#">🐦</a><a href="#">▶️</a>
            </div>
        </div>
        <div class="footer-col">
            <h4>Quick Links</h4>
            <ul>
                <li><a href="index.php">Home</a></li>
                <li><a href="about.php">About</a></li>
                <li><a href="contact.php">Contact</a></li>
                <li><a href="cart.php">Cart</a></li>
            </ul>
        </div>
        <div class="footer-col">
            <h4>Categories</h4>
            <ul>
                <li><a href="index.php?cat=1">👗 Clothing</a></li>
                <li><a href="index.php?cat=2">👠 Footwear</a></li>
                <li><a href="index.php?cat=3">👜 Accessories</a></li>
                <li><a href="index.php?cat=4">💄 Makeup</a></li>
            </ul>
        </div>
        <div class="footer-col">
            <h4>Our Team</h4>
            <ul>
                <li><a href="about.php">👩‍💼 Tamanna Dadhwal</a></li>
                <li><a href="about.php">👩‍🎨 Simran</a></li>
                <li><a href="contact.php">📬 Contact Us</a></li>
            </ul>
        </div>
    </div>
    <div class="footer-bottom">
        <p>© 2024 GlamCart. Made with 💕 by Tamanna Dadhwal & Simran.</p>
        <p>Privacy Policy · Terms of Service</p>
    </div>
</footer>

</body>
</html>
