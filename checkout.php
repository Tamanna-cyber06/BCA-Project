<?php
// ============================================
// GlamCart — Checkout Page
// checkout.php
// ============================================

session_start();
require_once 'db.php';

// Must be logged in
if (!isset($_SESSION['user_id'])) {
    $_SESSION['msg']      = 'Please login to checkout! 🛒';
    $_SESSION['msg_type'] = 'info';
    header("Location: login.php");
    exit;
}

$user_id = (int)$_SESSION['user_id'];

// Fetch cart items
$cart_result = mysqli_query($conn, "
    SELECT c.id AS cart_id, c.quantity, p.id AS product_id,
           p.name, p.price, p.image, cat.name AS category_name
    FROM cart c
    JOIN products p ON c.product_id = p.id
    LEFT JOIN categories cat ON p.category_id = cat.id
    WHERE c.user_id = $user_id
    ORDER BY c.id DESC
");

$items    = [];
$subtotal = 0;
while ($row = mysqli_fetch_assoc($cart_result)) {
    $row['line_total'] = $row['price'] * $row['quantity'];
    $subtotal += $row['line_total'];
    $items[] = $row;
}

// Redirect if cart is empty
if (count($items) === 0) {
    $_SESSION['msg']      = 'Your cart is empty! Add some items first. 🛍️';
    $_SESSION['msg_type'] = 'info';
    header("Location: cart.php");
    exit;
}

$shipping = ($subtotal >= 999) ? 0 : 99;
$total    = $subtotal + $shipping;

$order_placed = false;
$order_id     = null;
$error        = '';

// ============================================
// HANDLE ORDER PLACEMENT
// ============================================

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['place_order'])) {
    $name    = sanitize($conn, $_POST['name']    ?? '');
    $email   = sanitize($conn, $_POST['email']   ?? '');
    $phone   = sanitize($conn, $_POST['phone']   ?? '');
    $address = sanitize($conn, $_POST['address'] ?? '');
    $city    = sanitize($conn, $_POST['city']    ?? '');
    $zip     = sanitize($conn, $_POST['zip']     ?? '');

    if (empty($name) || empty($email) || empty($phone) || empty($address) || empty($city) || empty($zip)) {
        $error = 'Please fill in all required billing fields.';
    } else {
        // Insert order record
        $insert_order = "
            INSERT INTO orders (user_id, name, email, phone, address, city, zip, total, status)
            VALUES ($user_id, '$name', '$email', '$phone', '$address', '$city', '$zip', $total, 'pending')
        ";

        if (mysqli_query($conn, $insert_order)) {
            $order_id = mysqli_insert_id($conn);

            // Insert each cart item into order_items
            foreach ($items as $item) {
                $pid   = $item['product_id'];
                $qty   = $item['quantity'];
                $price = $item['price'];
                mysqli_query($conn, "
                    INSERT INTO order_items (order_id, product_id, quantity, price)
                    VALUES ($order_id, $pid, $qty, $price)
                ");
            }

            // Clear the user's cart
            mysqli_query($conn, "DELETE FROM cart WHERE user_id = $user_id");

            $order_placed = true;
        } else {
            $error = 'Failed to place order. Please try again.';
        }
    }
}

$cat_emoji = ['Clothing'=>'👗','Footwear'=>'👠','Accessories'=>'👜','Makeup'=>'💄'];
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Checkout — GlamCart</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>

<!-- HEADER -->
<header>
    <div class="navbar">
        <a href="index.php" class="logo">Glam<span>Cart</span></a>
        <div class="nav-actions">
            <a href="cart.php">← Back to Cart</a>
            <a href="logout.php">Logout</a>
        </div>
    </div>
</header>

<?php if ($order_placed): ?>
<!-- ===== ORDER SUCCESS ===== -->
<div class="section success-page">
    <div class="success-card">
        <div class="success-icon">🎉</div>
        <h2>Order Placed!</h2>
        <p>
            Thank you, <strong><?php echo htmlspecialchars($_POST['name']); ?></strong>!<br>
            Your order <strong>#<?php echo $order_id; ?></strong> has been successfully placed.
            We'll send you updates on your email. 💕
        </p>
        <div style="background:var(--pink-pale);border-radius:10px;padding:16px;margin-bottom:24px;font-size:14px;">
            <strong>Order Total:</strong> ₹<?php echo number_format($total, 2); ?><br>
            <strong>Status:</strong> <span class="status-badge status-pending">Pending</span>
        </div>
        <a href="index.php" class="btn btn-primary">Continue Shopping 🛍️</a>
    </div>
</div>

<?php else: ?>
<!-- ===== CHECKOUT FORM ===== -->
<div class="page-header">
    <h1>💳 Checkout</h1>
    <p>Fill in your details to complete your order</p>
    <div class="breadcrumb">
        <a href="index.php">Home</a> › <a href="cart.php">Cart</a> › Checkout
    </div>
</div>

<?php if (!empty($error)): ?>
    <div class="flash flash-error" style="margin:16px auto;max-width:1100px;">
        <?php echo htmlspecialchars($error); ?>
    </div>
<?php endif; ?>

<div class="section" style="padding-top:30px;">
    <div class="checkout-layout">

        <!-- BILLING FORM -->
        <div class="checkout-form-box">
            <h3>📦 Billing & Shipping Details</h3>
            <form method="POST" action="checkout.php">

                <div class="form-row">
                    <div class="form-group">
                        <label>Full Name *</label>
                        <input type="text" name="name"
                               value="<?php echo htmlspecialchars($_SESSION['user_name']); ?>"
                               placeholder="Your full name" required>
                    </div>
                    <div class="form-group">
                        <label>Phone Number *</label>
                        <input type="tel" name="phone" placeholder="+91 XXXXX XXXXX" required>
                    </div>
                </div>

                <div class="form-group">
                    <label>Email Address *</label>
                    <input type="email" name="email"
                           placeholder="your@email.com" required>
                </div>

                <div class="form-group">
                    <label>Full Address *</label>
                    <textarea name="address" placeholder="House No., Street, Locality..." required rows="3"></textarea>
                </div>

                <div class="form-row">
                    <div class="form-group">
                        <label>City *</label>
                        <input type="text" name="city" placeholder="City" required>
                    </div>
                    <div class="form-group">
                        <label>PIN Code *</label>
                        <input type="text" name="zip" placeholder="6-digit PIN" required maxlength="6">
                    </div>
                </div>

                <!-- Payment Method (Demo — no real payment) -->
                <div style="background:var(--pink-pale);border:1px solid var(--border);border-radius:12px;padding:20px;margin:20px 0;">
                    <h4 style="margin-bottom:14px;font-size:15px;">💳 Payment Method</h4>
                    <label style="display:flex;align-items:center;gap:10px;cursor:pointer;font-size:14px;margin-bottom:10px;">
                        <input type="radio" name="payment" value="cod" checked>
                        <span>💵 Cash on Delivery</span>
                    </label>
                    <label style="display:flex;align-items:center;gap:10px;cursor:pointer;font-size:14px;">
                        <input type="radio" name="payment" value="upi">
                        <span>📱 UPI / Online Payment (Demo)</span>
                    </label>
                </div>

                <button type="submit" name="place_order" class="btn btn-primary" style="width:100%;justify-content:center;font-size:16px;padding:16px;">
                    🎉 Place Order — ₹<?php echo number_format($total, 2); ?>
                </button>

                <p style="font-size:12px;color:var(--gray);text-align:center;margin-top:12px;">
                    🔒 Your information is safe and secure
                </p>
            </form>
        </div>

        <!-- ORDER SUMMARY -->
        <div class="checkout-summary">
            <h3 style="font-family:var(--font-head);font-size:20px;margin-bottom:20px;padding-bottom:14px;border-bottom:1px solid var(--border);">
                🧾 Order Summary
            </h3>

            <!-- Items list -->
            <?php foreach ($items as $item):
                $emoji = $cat_emoji[$item['category_name']] ?? '🛍️';
            ?>
            <div class="checkout-item">
                <?php if (file_exists($item['image'])): ?>
                    <img src="<?php echo htmlspecialchars($item['image']); ?>"
                         alt="<?php echo htmlspecialchars($item['name']); ?>">
                <?php else: ?>
                    <div style="width:60px;height:60px;background:var(--pink-pale);border-radius:8px;display:flex;align-items:center;justify-content:center;font-size:24px;flex-shrink:0;">
                        <?php echo $emoji; ?>
                    </div>
                <?php endif; ?>
                <div class="checkout-item-info">
                    <h4><?php echo htmlspecialchars($item['name']); ?></h4>
                    <p>Qty: <?php echo $item['quantity']; ?> × ₹<?php echo number_format($item['price'], 2); ?></p>
                    <p style="color:var(--pink);font-weight:600;">₹<?php echo number_format($item['line_total'], 2); ?></p>
                </div>
            </div>
            <?php endforeach; ?>

            <!-- Totals -->
            <div style="margin-top:16px;">
                <div class="summary-row">
                    <span>Subtotal</span>
                    <span>₹<?php echo number_format($subtotal, 2); ?></span>
                </div>
                <div class="summary-row">
                    <span>Shipping</span>
                    <span><?php echo $shipping == 0 ? '<span style="color:#2ed573;">FREE ✅</span>' : '₹99'; ?></span>
                </div>
                <div class="summary-row total" style="font-size:18px;font-weight:700;color:var(--pink);border-top:2px solid var(--border);margin-top:10px;padding-top:16px;">
                    <span>Total</span>
                    <span>₹<?php echo number_format($total, 2); ?></span>
                </div>
            </div>
        </div>
    </div>
</div>
<?php endif; ?>

<!-- FOOTER -->
<footer>
    <div class="footer-bottom" style="max-width:100%;border-top:1px solid rgba(255,255,255,0.08);padding:20px 60px;">
        <p>© 2024 GlamCart. Made with 💕 by Tamanna Dadhwal & Simran.</p>
    </div>
</footer>

</body>
</html>
