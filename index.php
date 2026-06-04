<?php
// ============================================
// GlamCart — Home Page
// index.php
// ============================================

session_start();
require_once 'db.php';

// Fetch all categories
$cat_result = mysqli_query($conn, "SELECT * FROM categories");

// Fetch all products (limit to 8 for homepage)
$products_result = mysqli_query($conn, "
    SELECT p.*, c.name AS category_name
    FROM products p
    LEFT JOIN categories c ON p.category_id = c.id
    ORDER BY p.created_at DESC
    LIMIT 8
");

// Count cart items for logged-in user
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
    <title>GlamCart — Girls' Fashion Store</title>
    <link rel="stylesheet" href="style.css">
    <link rel="icon" href="data:image/svg+xml,<svg xmlns='http://www.w3.org/2000/svg' viewBox='0 0 100 100'><text y='.9em' font-size='90'>💄</text></svg>">
</head>
<body>

<!-- ===== HEADER ===== -->
<header>
    <div class="nav-top">✨ Free Shipping on orders above ₹999 | New Arrivals Every Week ✨</div>
    <div class="navbar">
        <a href="index.php" class="logo">Glam<span>Cart</span></a>
        <nav class="nav-links">
            <a href="index.php" class="active">Home</a>
            <a href="index.php?cat=1">Clothing</a>
            <a href="index.php?cat=2">Footwear</a>
            <a href="index.php?cat=3">Accessories</a>
            <a href="index.php?cat=4">Makeup</a>
            <a href="about.php">About</a>
            <a href="contact.php">Contact</a>
        </nav>
        <div class="nav-actions">
            <?php if (isset($_SESSION['user_id'])): ?>
                <a href="#">👋 <?php echo htmlspecialchars($_SESSION['user_name']); ?></a>
                <?php if ($_SESSION['user_role'] === 'admin'): ?>
                    <a href="admin.php">🛠 Admin</a>
                <?php endif; ?>
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

<!-- ===== FLASH MESSAGE ===== -->
<?php if (isset($_SESSION['msg'])): ?>
    <div class="flash flash-<?php echo $_SESSION['msg_type'] ?? 'info'; ?>" style="margin:16px auto;max-width:900px;">
        <?php echo htmlspecialchars($_SESSION['msg']); unset($_SESSION['msg']); unset($_SESSION['msg_type']); ?>
    </div>
<?php endif; ?>

<!-- ===== HERO SECTION ===== -->
<section class="hero">
    <div class="hero-content">
        <span class="hero-badge">✨ New Season Collection</span>
        <h1>Your Style,<br>Your <span>Story</span>.</h1>
        <p>Discover the latest trends in girls' fashion — from chic clothing to glam makeup, all in one place. Express yourself with GlamCart.</p>
        <div class="hero-btns">
            <a href="#products" class="btn btn-primary">Shop Now 🛍️</a>
            <a href="about.php" class="btn btn-outline">Our Story</a>
        </div>
        <div class="hero-stats">
            <div class="hero-stat">
                <h3>500+</h3>
                <p>Products</p>
            </div>
            <div class="hero-stat">
                <h3>10K+</h3>
                <p>Happy Girls</p>
            </div>
            <div class="hero-stat">
                <h3>4.9★</h3>
                <p>Rating</p>
            </div>
        </div>
    </div>
    <div class="hero-image">
        <!-- Replace with your own hero image -->
        <div style="width:400px;height:440px;background:linear-gradient(145deg,#FFB6C1,#FF69B4,#FF1493);border-radius:20px;display:flex;align-items:center;justify-content:center;font-size:120px;box-shadow:0 20px 60px rgba(255,105,180,0.3);">
            👗
        </div>
    </div>
</section>

<!-- ===== FEATURES STRIP ===== -->
<div style="background:var(--pink-pale);border-top:1px solid var(--border);border-bottom:1px solid var(--border);">
    <div class="section" style="padding-top:30px;padding-bottom:30px;">
        <div class="features-grid">
            <div class="feature-card">
                <div class="icon">🚚</div>
                <h4>Free Shipping</h4>
                <p>On all orders above ₹999</p>
            </div>
            <div class="feature-card">
                <div class="icon">↩️</div>
                <h4>Easy Returns</h4>
                <p>7-day hassle-free returns</p>
            </div>
            <div class="feature-card">
                <div class="icon">🔒</div>
                <h4>Secure Payment</h4>
                <p>100% secure transactions</p>
            </div>
            <div class="feature-card">
                <div class="icon">💬</div>
                <h4>24/7 Support</h4>
                <p>Always here to help you</p>
            </div>
        </div>
    </div>
</div>

<!-- ===== CATEGORIES ===== -->
<div class="section">
    <div class="section-header">
        <span class="section-label">Browse By</span>
        <h2>Shop By Category</h2>
        <p>Find everything you need for your perfect look</p>
        <div class="divider"></div>
    </div>
    <div class="categories-grid">
        <a href="index.php?cat=1" class="category-card">
            <span class="category-icon">👗</span>
            <h3>Clothing</h3>
            <p>Dresses, Tops, Skirts & more</p>
        </a>
        <a href="index.php?cat=2" class="category-card">
            <span class="category-icon">👠</span>
            <h3>Footwear</h3>
            <p>Heels, Sandals, Sneakers</p>
        </a>
        <a href="index.php?cat=3" class="category-card">
            <span class="category-icon">👜</span>
            <h3>Accessories</h3>
            <p>Bags, Jewelry & more</p>
        </a>
        <a href="index.php?cat=4" class="category-card">
            <span class="category-icon">💄</span>
            <h3>Makeup</h3>
            <p>Lipstick, Eyeshadow & more</p>
        </a>
    </div>
</div>

<!-- ===== PRODUCTS SECTION ===== -->
<div class="section" id="products">
    <div class="section-header">
        <span class="section-label">Our Collection</span>
        <h2>Trending Products</h2>
        <p>Hand-picked styles that are flying off the shelves</p>
        <div class="divider"></div>
    </div>

    <?php
    // If category filter is set, refetch with filter
    if (isset($_GET['cat']) && is_numeric($_GET['cat'])) {
        $cat_id = (int)$_GET['cat'];
        $products_result = mysqli_query($conn, "
            SELECT p.*, c.name AS category_name
            FROM products p
            LEFT JOIN categories c ON p.category_id = c.id
            WHERE p.category_id = $cat_id
            ORDER BY p.created_at DESC
        ");
    }
    ?>

    <div class="products-grid">
        <?php if (mysqli_num_rows($products_result) > 0):
            while ($product = mysqli_fetch_assoc($products_result)):
                // Emoji fallback based on category
                $cat_emoji = ['1'=>'👗','2'=>'👠','3'=>'👜','4'=>'💄'];
                $emoji = $cat_emoji[$product['category_id']] ?? '🛍️';
        ?>
        <div class="product-card">
            <div class="product-img-wrap">
                <?php if (file_exists($product['image'])): ?>
                    <img src="<?php echo htmlspecialchars($product['image']); ?>"
                         alt="<?php echo htmlspecialchars($product['name']); ?>">
                <?php else: ?>
                    <div class="placeholder-img"><?php echo $emoji; ?></div>
                <?php endif; ?>
                <span class="product-badge">New ✨</span>
                <button class="product-wishlist">🤍</button>
            </div>
            <div class="product-info">
                <div class="product-category"><?php echo htmlspecialchars($product['category_name']); ?></div>
                <div class="product-name"><?php echo htmlspecialchars($product['name']); ?></div>
                <div class="product-price">
                    ₹<?php echo number_format($product['price'], 2); ?>
                </div>
                <form method="POST" action="cart.php">
                    <input type="hidden" name="action" value="add">
                    <input type="hidden" name="product_id" value="<?php echo $product['id']; ?>">
                    <button type="submit" class="add-to-cart-btn">
                        🛒 Add to Cart
                    </button>
                </form>
            </div>
        </div>
        <?php endwhile;
        else: ?>
        <div style="grid-column:1/-1;text-align:center;padding:60px 20px;">
            <div style="font-size:60px;margin-bottom:16px;">🛍️</div>
            <h3 style="font-family:var(--font-head);font-size:24px;margin-bottom:10px;">No products found</h3>
            <p style="color:var(--gray);">Check back soon for new arrivals!</p>
        </div>
        <?php endif; ?>
    </div>
</div>

<!-- ===== PROMO BANNER ===== -->
<div class="promo-strip">
    <h2>💅 Glow Up Sale — Up to 40% OFF!</h2>
    <p>Limited time offer on selected products. Don't miss out!</p>
    <a href="#products" class="btn btn-primary">Shop the Sale</a>
</div>

<!-- ===== FOOTER ===== -->
<footer>
    <div class="footer-main">
        <div class="footer-brand">
            <span class="logo">Glam<span>Cart</span></span>
            <p>Your one-stop fashion destination for everything glam. Express your unique style with our curated collection of clothing, footwear, accessories, and makeup.</p>
            <div class="social-links">
                <a href="#">📘</a>
                <a href="#">📸</a>
                <a href="#">🐦</a>
                <a href="#">▶️</a>
            </div>
        </div>
        <div class="footer-col">
            <h4>Quick Links</h4>
            <ul>
                <li><a href="index.php">Home</a></li>
                <li><a href="about.php">About Us</a></li>
                <li><a href="contact.php">Contact</a></li>
                <li><a href="cart.php">Cart</a></li>
                <li><a href="checkout.php">Checkout</a></li>
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
        <div class="footer-col footer-newsletter">
            <h4>Newsletter</h4>
            <p>Subscribe to get the latest deals and fashion tips straight to your inbox.</p>
            <div class="newsletter-form">
                <input type="email" placeholder="Your email address">
                <button>→</button>
            </div>
        </div>
    </div>
    <div class="footer-bottom">
        <p>© 2024 GlamCart. Made with 💕 by Tamanna Dadhwal & Simran.</p>
        <p>Privacy Policy · Terms of Service</p>
    </div>
</footer>

</body>
</html>
