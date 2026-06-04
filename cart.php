<?php
// ============================================
// GlamCart — Cart Page
// cart.php
// ============================================

session_start();
require_once 'db.php';

// Must be logged in to use cart
if (!isset($_SESSION['user_id'])) {
    $_SESSION['msg']      = 'Please login to view your cart! 🛒';
    $_SESSION['msg_type'] = 'info';
    header("Location: login.php");
    exit;
}

$user_id = (int)$_SESSION['user_id'];

// ============================================
// HANDLE CART ACTIONS
// ============================================

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $action = $_POST['action'] ?? '';

    // --- ADD TO CART ---
    if ($action === 'add') {
        $product_id = (int)($_POST['product_id'] ?? 0);
        $qty        = (int)($_POST['quantity']   ?? 1);

        if ($product_id > 0 && $qty > 0) {
            // Check if product already in cart
            $check = mysqli_query($conn, "
                SELECT id, quantity FROM cart
                WHERE user_id = $user_id AND product_id = $product_id
                LIMIT 1
            ");

            if (mysqli_num_rows($check) > 0) {
                // Update quantity
                $row     = mysqli_fetch_assoc($check);
                $new_qty = $row['quantity'] + $qty;
                mysqli_query($conn, "
                    UPDATE cart SET quantity = $new_qty
                    WHERE user_id = $user_id AND product_id = $product_id
                ");
            } else {
                // Insert new cart item
                mysqli_query($conn, "
                    INSERT INTO cart (user_id, product_id, quantity)
                    VALUES ($user_id, $product_id, $qty)
                ");
            }

            $_SESSION['msg']      = 'Item added to cart! 🛒';
            $_SESSION['msg_type'] = 'success';
        }
        header("Location: cart.php");
        exit;
    }

    // --- UPDATE QUANTITY ---
    if ($action === 'update') {
        $cart_id = (int)($_POST['cart_id']  ?? 0);
        $qty     = (int)($_POST['quantity'] ?? 1);

        if ($cart_id > 0 && $qty > 0) {
            mysqli_query($conn, "
                UPDATE cart SET quantity = $qty
                WHERE id = $cart_id AND user_id = $user_id
            ");
            $_SESSION['msg']      = 'Cart updated! ✅';
            $_SESSION['msg_type'] = 'success';
        }
        header("Location: cart.php");
        exit;
    }

    // --- REMOVE ITEM ---
    if ($action === 'remove') {
        $cart_id = (int)($_POST['cart_id'] ?? 0);
        if ($cart_id > 0) {
            mysqli_query($conn, "
                DELETE FROM cart WHERE id = $cart_id AND user_id = $user_id
            ");
            $_SESSION['msg']      = 'Item removed from cart.';
            $_SESSION['msg_type'] = 'info';
        }
        header("Location: cart.php");
        exit;
    }

    // --- CLEAR CART ---
    if ($action === 'clear') {
        mysqli_query($conn, "DELETE FROM cart WHERE user_id = $user_id");
        $_SESSION['msg']      = 'Cart cleared.';
        $_SESSION['msg_type'] = 'info';
        header("Location: cart.php");
        exit;
    }
}

// ============================================
// FETCH CART ITEMS
// ============================================

$cart_result = mysqli_query($conn, "
    SELECT c.id AS cart_id, c.quantity, p.id AS product_id,
           p.name, p.price, p.image, cat.name AS category_name
    FROM cart c
    JOIN products p ON c.product_id = p.id
    LEFT JOIN categories cat ON p.category_id = cat.id
    WHERE c.user_id = $user_id
    ORDER BY c.id DESC
");

// Calculate totals
$items     = [];
$subtotal  = 0;
while ($row = mysqli_fetch_assoc($cart_result)) {
    $row['line_total'] = $row['price'] * $row['quantity'];
    $subtotal += $row['line_total'];
    $items[] = $row;
}

$shipping = ($subtotal >= 999) ? 0 : 99;
$total    = $subtotal + $shipping;

// Cart count for nav
$cart_count = count($items);

// Emoji fallback
$cat_emoji = ['Clothing'=>'👗','Footwear'=>'👠','Accessories'=>'👜','Makeup'=>'💄'];
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>My Cart — GlamCart</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>

<!-- HEADER -->
<header>
    <div class="nav-top">✨ Free Shipping on orders above ₹999 ✨</div>
    <div class="navbar">
        <a href="index.php" class="logo">Glam<span>Cart</span></a>
        <nav class="nav-links">
            <a href="index.php">Home</a>
            <a href="index.php?cat=1">Clothing</a>
            <a href="index.php?cat=2">Footwear</a>
            <a href="index.php?cat=3">Accessories</a>
            <a href="index.php?cat=4">Makeup</a>
            <a href="about.php">About</a>
            <a href="contact.php">Contact</a>
        </nav>
        <div class="nav-actions">
            <a href="#">👋 <?php echo htmlspecialchars($_SESSION['user_name']); ?></a>
            <a href="logout.php">Logout</a>
            <a href="cart.php" class="active">
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
    <h1>🛒 Shopping Cart</h1>
    <p>Review your selected items</p>
    <div class="breadcrumb">
        <a href="index.php">Home</a> › Cart
    </div>
</div>

<!-- FLASH -->
<?php if (isset($_SESSION['msg'])): ?>
    <div class="flash flash-<?php echo $_SESSION['msg_type'] ?? 'info'; ?>" style="margin:16px auto;max-width:1100px;">
        <?php echo htmlspecialchars($_SESSION['msg']); unset($_SESSION['msg']); unset($_SESSION['msg_type']); ?>
    </div>
<?php endif; ?>

<div class="section" style="padding-top:30px;">

<?php if (count($items) === 0): ?>
    <!-- EMPTY CART -->
    <div class="empty-cart">
        <div class="icon">🛒</div>
        <h2>Your cart is empty!</h2>
        <p>Looks like you haven't added anything yet. Start shopping to fill it up! 💕</p>
        <a href="index.php" class="btn btn-primary">Continue Shopping →</a>
    </div>

<?php else: ?>

    <div class="cart-layout">
        <!-- CART ITEMS TABLE -->
        <div>
            <div class="cart-table">
                <table>
                    <thead>
                        <tr>
                            <th>Product</th>
                            <th>Name</th>
                            <th>Price</th>
                            <th>Quantity</th>
                            <th>Total</th>
                            <th>Action</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($items as $item):
                            $emoji = $cat_emoji[$item['category_name']] ?? '🛍️';
                        ?>
                        <tr>
                            <!-- Product Image -->
                            <td>
                                <?php if (file_exists($item['image'])): ?>
                                    <img src="<?php echo htmlspecialchars($item['image']); ?>"
                                         alt="<?php echo htmlspecialchars($item['name']); ?>"
                                         class="cart-product-img">
                                <?php else: ?>
                                    <div style="width:70px;height:70px;background:var(--pink-pale);border-radius:10px;display:flex;align-items:center;justify-content:center;font-size:28px;">
                                        <?php echo $emoji; ?>
                                    </div>
                                <?php endif; ?>
                            </td>

                            <!-- Product Name -->
                            <td>
                                <strong><?php echo htmlspecialchars($item['name']); ?></strong><br>
                                <small style="color:var(--gray);"><?php echo htmlspecialchars($item['category_name']); ?></small>
                            </td>

                            <!-- Price -->
                            <td>₹<?php echo number_format($item['price'], 2); ?></td>

                            <!-- Quantity Update -->
                            <td>
                                <form method="POST" action="cart.php" class="cart-qty-form">
                                    <input type="hidden" name="action"  value="update">
                                    <input type="hidden" name="cart_id" value="<?php echo $item['cart_id']; ?>">
                                    <input type="number" name="quantity"
                                           value="<?php echo $item['quantity']; ?>"
                                           min="1" max="99">
                                    <button type="submit" class="btn btn-sm btn-outline" style="padding:7px 12px;">
                                        Update
                                    </button>
                                </form>
                            </td>

                            <!-- Line Total -->
                            <td style="font-weight:700;color:var(--pink);">
                                ₹<?php echo number_format($item['line_total'], 2); ?>
                            </td>

                            <!-- Remove -->
                            <td>
                                <form method="POST" action="cart.php">
                                    <input type="hidden" name="action"  value="remove">
                                    <input type="hidden" name="cart_id" value="<?php echo $item['cart_id']; ?>">
                                    <button type="submit" class="btn btn-sm btn-danger"
                                            onclick="return confirm('Remove this item?')">
                                        🗑️ Remove
                                    </button>
                                </form>
                            </td>
                        </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>

            <!-- CART ACTIONS -->
            <div style="display:flex;justify-content:space-between;margin-top:18px;flex-wrap:wrap;gap:12px;">
                <a href="index.php" class="btn btn-outline">← Continue Shopping</a>
                <form method="POST" action="cart.php" style="display:inline;">
                    <input type="hidden" name="action" value="clear">
                    <button type="submit" class="btn btn-danger"
                            onclick="return confirm('Clear entire cart?')">
                        🗑️ Clear Cart
                    </button>
                </form>
            </div>
        </div>

        <!-- CART SUMMARY -->
        <div class="cart-summary">
            <h3>🧾 Order Summary</h3>

            <div class="summary-row">
                <span>Subtotal (<?php echo count($items); ?> items)</span>
                <span>₹<?php echo number_format($subtotal, 2); ?></span>
            </div>
            <div class="summary-row">
                <span>Shipping</span>
                <span><?php echo $shipping == 0 ? '<span style="color:#2ed573;">FREE ✅</span>' : '₹'.$shipping; ?></span>
            </div>
            <?php if ($subtotal < 999): ?>
            <div style="font-size:12px;color:var(--gray);padding:8px 0;text-align:center;">
                Add ₹<?php echo number_format(999 - $subtotal, 2); ?> more for free shipping! 🚚
            </div>
            <?php endif; ?>
            <div class="summary-row total">
                <span>Total</span>
                <span>₹<?php echo number_format($total, 2); ?></span>
            </div>

            <a href="checkout.php" class="btn btn-primary" style="width:100%;justify-content:center;margin-top:22px;">
                Proceed to Checkout →
            </a>

            <div style="margin-top:20px;text-align:center;">
                <p style="font-size:12px;color:var(--gray);">🔒 Secure Checkout · 🚚 Fast Delivery</p>
            </div>
        </div>
    </div>

<?php endif; ?>

</div>

<!-- FOOTER -->
<footer>
    <div class="footer-bottom" style="max-width:100%;border-top:1px solid rgba(255,255,255,0.08);padding:20px 60px;">
        <p>© 2024 GlamCart. Made with 💕 by Tamanna Dadhwal & Simran.</p>
        <p>Privacy Policy · Terms of Service</p>
    </div>
</footer>

</body>
</html>
