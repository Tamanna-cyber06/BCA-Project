<?php
// ============================================
// GlamCart — About Us Page
// about.php
// ============================================

session_start();
require_once 'db.php';

// Cart count for nav
$cart_count = 0;
if (isset($_SESSION['user_id'])) {
    $uid = (int)$_SESSION['user_id'];
    $cart_q = mysqli_query($conn, "SELECT SUM(quantity) as total FROM cart WHERE user_id = $uid");
    $cart_row = mysqli_fetch_assoc($cart_q);
    $cart_count = $cart_row['total'] ?? 0;
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>About Us — GlamCart</title>
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
            <a href="about.php" class="active">About</a>
            <a href="contact.php">Contact</a>
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

<!-- HERO -->
<div class="about-hero">
    <h1>About <span>GlamCart</span> ✨</h1>
    <p>We're on a mission to make every girl feel confident, beautiful, and unstoppable — one outfit at a time.</p>
</div>

<!-- OUR STORY -->
<div class="section">
    <div style="display:grid;grid-template-columns:1fr 1fr;gap:60px;align-items:center;max-width:1000px;margin:0 auto;">
        <div>
            <span class="section-label">Our Story</span>
            <h2 style="font-family:var(--font-head);font-size:36px;margin-bottom:20px;line-height:1.3;">
                Fashion That Speaks Your Language 💕
            </h2>
            <p style="color:var(--gray);font-size:15px;line-height:1.9;margin-bottom:16px;">
                GlamCart was born from a simple idea: every girl deserves to look and feel amazing without
                breaking the bank. We curate the latest trends in clothing, footwear, accessories, and makeup —
                all in one beautiful place.
            </p>
            <p style="color:var(--gray);font-size:15px;line-height:1.9;margin-bottom:24px;">
                From boho maxi dresses to killer heels, quilted mini bags to glam eyeshadow palettes —
                we have everything you need to express your unique personality and style.
            </p>
            <div style="display:flex;gap:30px;">
                <div>
                    <div style="font-family:var(--font-head);font-size:32px;color:var(--pink);font-weight:700;">500+</div>
                    <div style="font-size:13px;color:var(--gray);">Products</div>
                </div>
                <div>
                    <div style="font-family:var(--font-head);font-size:32px;color:var(--pink);font-weight:700;">10K+</div>
                    <div style="font-size:13px;color:var(--gray);">Happy Customers</div>
                </div>
                <div>
                    <div style="font-family:var(--font-head);font-size:32px;color:var(--pink);font-weight:700;">4</div>
                    <div style="font-size:13px;color:var(--gray);">Categories</div>
                </div>
            </div>
        </div>
        <div style="background:linear-gradient(145deg,#FFB6C1,#FF69B4);border-radius:20px;height:380px;display:flex;align-items:center;justify-content:center;font-size:100px;box-shadow:var(--shadow-lg);">
            💄
        </div>
    </div>
</div>

<!-- OUR VALUES -->
<div style="background:var(--pink-pale);border-top:1px solid var(--border);border-bottom:1px solid var(--border);">
    <div class="section">
        <div class="section-header">
            <span class="section-label">What We Stand For</span>
            <h2>Our Values</h2>
            <div class="divider"></div>
        </div>
        <div style="display:grid;grid-template-columns:repeat(3,1fr);gap:28px;">
            <div class="feature-card" style="text-align:left;padding:30px;">
                <div class="icon">💫</div>
                <h4 style="font-size:18px;margin-bottom:10px;">Trendy & Affordable</h4>
                <p>We believe fashion should be accessible to all. Get the latest looks without the luxury price tags.</p>
            </div>
            <div class="feature-card" style="text-align:left;padding:30px;">
                <div class="icon">🌸</div>
                <h4 style="font-size:18px;margin-bottom:10px;">Quality First</h4>
                <p>Every product is carefully selected for quality and comfort. We only sell what we'd wear ourselves.</p>
            </div>
            <div class="feature-card" style="text-align:left;padding:30px;">
                <div class="icon">💪</div>
                <h4 style="font-size:18px;margin-bottom:10px;">Girl Power</h4>
                <p>We celebrate every body type and style. GlamCart is for every girl — bold, soft, edgy, or classic.</p>
            </div>
        </div>
    </div>
</div>

<!-- MEET THE TEAM -->
<div class="section">
    <div class="section-header">
        <span class="section-label">The Creators</span>
        <h2>Meet Our Team 💕</h2>
        <p>The passionate girls behind GlamCart who make the magic happen</p>
        <div class="divider"></div>
    </div>

    <div class="team-grid">

        <!-- Team Member 1 — Tamanna Dadhwal -->
        <div class="team-card">
            <div class="team-avatar">👩‍💼</div>
            <h3>Tamanna Dadhwal</h3>
            <div class="role">Co-Founder & CEO</div>
            <p>
                Tamanna is the creative visionary behind GlamCart. With a passion for fashion and a sharp
                eye for trends, she curates every collection with love and dedication. When she's not
                styling outfits, she's building the future of online fashion! 💄
            </p>
            <div style="margin-top:18px;display:flex;justify-content:center;gap:12px;">
                <a href="#" style="width:36px;height:36px;background:var(--pink-pale);border-radius:50%;display:flex;align-items:center;justify-content:center;border:1px solid var(--border);">📘</a>
                <a href="#" style="width:36px;height:36px;background:var(--pink-pale);border-radius:50%;display:flex;align-items:center;justify-content:center;border:1px solid var(--border);">📸</a>
                <a href="#" style="width:36px;height:36px;background:var(--pink-pale);border-radius:50%;display:flex;align-items:center;justify-content:center;border:1px solid var(--border);">💼</a>
            </div>
        </div>

        <!-- Team Member 2 — Simran -->
        <div class="team-card">
            <div class="team-avatar">👩‍🎨</div>
            <h3>Simran</h3>
            <div class="role">Co-Founder & Creative Director</div>
            <p>
                Simran is the design genius of the team. She brings the GlamCart aesthetic to life with
                her impeccable taste and creative flair. From website design to product photography,
                Simran ensures everything looks absolutely stunning! ✨
            </p>
            <div style="margin-top:18px;display:flex;justify-content:center;gap:12px;">
                <a href="#" style="width:36px;height:36px;background:var(--pink-pale);border-radius:50%;display:flex;align-items:center;justify-content:center;border:1px solid var(--border);">📘</a>
                <a href="#" style="width:36px;height:36px;background:var(--pink-pale);border-radius:50%;display:flex;align-items:center;justify-content:center;border:1px solid var(--border);">📸</a>
                <a href="#" style="width:36px;height:36px;background:var(--pink-pale);border-radius:50%;display:flex;align-items:center;justify-content:center;border:1px solid var(--border);">💼</a>
            </div>
        </div>

    </div>
</div>

<!-- CTA SECTION -->
<div class="promo-strip">
    <h2>💕 Ready to Glam Up?</h2>
    <p>Explore our collection and find your perfect style today</p>
    <a href="index.php" class="btn btn-primary">Shop Now 🛍️</a>
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
            <h4>Contact Us</h4>
            <ul>
                <li><a href="mailto:tamanna@glamcart.com">📧 tamanna@glamcart.com</a></li>
                <li><a href="mailto:simran@glamcart.com">📧 simran@glamcart.com</a></li>
                <li><a href="contact.php">💬 Send a Message</a></li>
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
