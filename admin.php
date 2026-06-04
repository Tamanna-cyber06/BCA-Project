<?php
// ============================================
// GlamCart — Admin Dashboard
// admin.php
// ============================================

session_start();
require_once 'db.php';

// Admin-only access
if (!isset($_SESSION['user_id']) || $_SESSION['user_role'] !== 'admin') {
    $_SESSION['msg']      = '⛔ Admin access required. Please login as admin.';
    $_SESSION['msg_type'] = 'error';
    header("Location: login.php");
    exit;
}

// ============================================
// FETCH STATS
// ============================================

// Total products
$total_products = mysqli_fetch_assoc(mysqli_query($conn, "SELECT COUNT(*) AS cnt FROM products"))['cnt'];

// Total users (non-admin)
$total_users = mysqli_fetch_assoc(mysqli_query($conn, "SELECT COUNT(*) AS cnt FROM users WHERE role='user'"))['cnt'];

// Total orders
$total_orders = mysqli_fetch_assoc(mysqli_query($conn, "SELECT COUNT(*) AS cnt FROM orders"))['cnt'];

// Total revenue
$revenue_row = mysqli_fetch_assoc(mysqli_query($conn, "SELECT SUM(total) AS rev FROM orders"));
$total_revenue = $revenue_row['rev'] ?? 0;

// Fetch latest orders
$orders_result = mysqli_query($conn, "
    SELECT o.*, u.name AS customer_name
    FROM orders o
    JOIN users u ON o.user_id = u.id
    ORDER BY o.created_at DESC
    LIMIT 10
");

// Fetch all products
$products_result = mysqli_query($conn, "
    SELECT p.*, c.name AS cat_name
    FROM products p
    LEFT JOIN categories c ON p.category_id = c.id
    ORDER BY p.id DESC
");

// Fetch all users
$users_result = mysqli_query($conn, "SELECT * FROM users ORDER BY created_at DESC");

// ============================================
// HANDLE PRODUCT DELETE
// ============================================
if (isset($_GET['delete_product'])) {
    $pid = (int)$_GET['delete_product'];
    mysqli_query($conn, "DELETE FROM products WHERE id = $pid");
    $_SESSION['msg']      = 'Product deleted successfully.';
    $_SESSION['msg_type'] = 'success';
    header("Location: admin.php");
    exit;
}

// Handle order status update
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['update_status'])) {
    $oid    = (int)$_POST['order_id'];
    $status = sanitize($conn, $_POST['status']);
    $allowed = ['pending','processing','shipped','delivered'];
    if (in_array($status, $allowed)) {
        mysqli_query($conn, "UPDATE orders SET status = '$status' WHERE id = $oid");
        $_SESSION['msg']      = "Order #$oid status updated.";
        $_SESSION['msg_type'] = 'success';
    }
    header("Location: admin.php");
    exit;
}

// Active tab
$tab = $_GET['tab'] ?? 'dashboard';
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Panel — GlamCart</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>

<!-- ADMIN TOP BAR -->
<div style="background:var(--dark);color:white;padding:12px 30px;display:flex;justify-content:space-between;align-items:center;">
    <span style="font-family:var(--font-head);font-size:20px;color:var(--pink);">💄 GlamCart Admin</span>
    <div style="display:flex;gap:20px;font-size:13px;">
        <span>👋 <?php echo htmlspecialchars($_SESSION['user_name']); ?></span>
        <a href="index.php" style="color:rgba(255,255,255,0.7);">🏪 View Store</a>
        <a href="logout.php" style="color:var(--pink);">Logout</a>
    </div>
</div>

<!-- FLASH -->
<?php if (isset($_SESSION['msg'])): ?>
    <div class="flash flash-<?php echo $_SESSION['msg_type'] ?? 'info'; ?>" style="margin:10px 20px;border-radius:8px;">
        <?php echo htmlspecialchars($_SESSION['msg']); unset($_SESSION['msg']); unset($_SESSION['msg_type']); ?>
    </div>
<?php endif; ?>

<div class="admin-layout">

    <!-- SIDEBAR -->
    <div class="admin-sidebar">
        <div class="admin-logo">🛍️ Admin Panel</div>
        <div class="admin-menu">
            <a href="admin.php?tab=dashboard" class="<?php echo $tab==='dashboard'?'active':''; ?>">
                📊 Dashboard
            </a>
            <a href="admin.php?tab=products" class="<?php echo $tab==='products'?'active':''; ?>">
                👗 Products
            </a>
            <a href="add_product.php">
                ➕ Add Product
            </a>
            <a href="admin.php?tab=orders" class="<?php echo $tab==='orders'?'active':''; ?>">
                📦 Orders
            </a>
            <a href="admin.php?tab=users" class="<?php echo $tab==='users'?'active':''; ?>">
                👥 Users
            </a>
            <a href="index.php">
                🏪 View Store
            </a>
            <a href="logout.php" style="color:#ff6b6b;">
                🚪 Logout
            </a>
        </div>
    </div>

    <!-- MAIN CONTENT -->
    <div class="admin-main">
        <div class="admin-topbar">
            <h2>
                <?php
                $titles = [
                    'dashboard' => '📊 Dashboard Overview',
                    'products'  => '👗 Product Management',
                    'orders'    => '📦 Order Management',
                    'users'     => '👥 User Management',
                ];
                echo $titles[$tab] ?? '📊 Dashboard';
                ?>
            </h2>
            <a href="add_product.php" class="btn btn-primary btn-sm">+ Add Product</a>
        </div>

        <div class="admin-content">

        <!-- ========================
             DASHBOARD TAB
             ======================== -->
        <?php if ($tab === 'dashboard'): ?>

            <!-- Stats Cards -->
            <div class="stats-grid">
                <div class="stat-card pink">
                    <div class="icon">👗</div>
                    <h3><?php echo $total_products; ?></h3>
                    <p>Total Products</p>
                </div>
                <div class="stat-card gold">
                    <div class="icon">👥</div>
                    <h3><?php echo $total_users; ?></h3>
                    <p>Registered Users</p>
                </div>
                <div class="stat-card green">
                    <div class="icon">📦</div>
                    <h3><?php echo $total_orders; ?></h3>
                    <p>Total Orders</p>
                </div>
                <div class="stat-card blue">
                    <div class="icon">💰</div>
                    <h3>₹<?php echo number_format($total_revenue, 0); ?></h3>
                    <p>Total Revenue</p>
                </div>
            </div>

            <!-- Recent Orders -->
            <div class="admin-table-box">
                <h3>📦 Recent Orders</h3>
                <?php
                // Re-run query for display
                $recent_orders = mysqli_query($conn, "
                    SELECT o.*, u.name AS customer_name
                    FROM orders o JOIN users u ON o.user_id = u.id
                    ORDER BY o.created_at DESC LIMIT 5
                ");
                ?>
                <table class="admin-table">
                    <thead>
                        <tr>
                            <th>Order #</th>
                            <th>Customer</th>
                            <th>Total</th>
                            <th>Status</th>
                            <th>Date</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php while ($order = mysqli_fetch_assoc($recent_orders)): ?>
                        <tr>
                            <td>#<?php echo $order['id']; ?></td>
                            <td><?php echo htmlspecialchars($order['customer_name']); ?></td>
                            <td>₹<?php echo number_format($order['total'], 2); ?></td>
                            <td>
                                <span class="status-badge status-<?php echo $order['status']; ?>">
                                    <?php echo ucfirst($order['status']); ?>
                                </span>
                            </td>
                            <td><?php echo date('d M Y', strtotime($order['created_at'])); ?></td>
                        </tr>
                        <?php endwhile; ?>
                    </tbody>
                </table>
                <div style="margin-top:14px;">
                    <a href="admin.php?tab=orders" class="btn btn-outline btn-sm">View All Orders →</a>
                </div>
            </div>

        <!-- ========================
             PRODUCTS TAB
             ======================== -->
        <?php elseif ($tab === 'products'): ?>
            <div class="admin-table-box">
                <h3>All Products <span style="font-size:14px;color:var(--gray);font-family:var(--font-body);font-weight:400;">(<?php echo $total_products; ?> total)</span></h3>
                <table class="admin-table">
                    <thead>
                        <tr>
                            <th>#</th>
                            <th>Image</th>
                            <th>Name</th>
                            <th>Category</th>
                            <th>Price</th>
                            <th>Stock</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php while ($p = mysqli_fetch_assoc($products_result)):
                            $cat_emoji = ['Clothing'=>'👗','Footwear'=>'👠','Accessories'=>'👜','Makeup'=>'💄'];
                            $emoji = $cat_emoji[$p['cat_name']] ?? '🛍️';
                        ?>
                        <tr>
                            <td><?php echo $p['id']; ?></td>
                            <td>
                                <?php if (file_exists($p['image'])): ?>
                                    <img src="<?php echo htmlspecialchars($p['image']); ?>"
                                         alt="<?php echo htmlspecialchars($p['name']); ?>">
                                <?php else: ?>
                                    <div style="width:50px;height:50px;background:var(--pink-pale);border-radius:8px;display:flex;align-items:center;justify-content:center;font-size:22px;">
                                        <?php echo $emoji; ?>
                                    </div>
                                <?php endif; ?>
                            </td>
                            <td>
                                <strong><?php echo htmlspecialchars($p['name']); ?></strong><br>
                                <small style="color:var(--gray);"><?php echo mb_substr(htmlspecialchars($p['description']), 0, 50); ?>...</small>
                            </td>
                            <td><?php echo htmlspecialchars($p['cat_name']); ?></td>
                            <td style="color:var(--pink);font-weight:700;">₹<?php echo number_format($p['price'], 2); ?></td>
                            <td><?php echo $p['stock']; ?></td>
                            <td>
                                <a href="add_product.php?edit=<?php echo $p['id']; ?>" class="btn btn-sm btn-outline">✏️ Edit</a>
                                <a href="admin.php?tab=products&delete_product=<?php echo $p['id']; ?>"
                                   class="btn btn-sm btn-danger"
                                   onclick="return confirm('Delete this product?')"
                                   style="margin-left:6px;">🗑️</a>
                            </td>
                        </tr>
                        <?php endwhile; ?>
                    </tbody>
                </table>
            </div>

        <!-- ========================
             ORDERS TAB
             ======================== -->
        <?php elseif ($tab === 'orders'): ?>
            <div class="admin-table-box">
                <h3>All Orders</h3>
                <table class="admin-table">
                    <thead>
                        <tr>
                            <th>Order #</th>
                            <th>Customer</th>
                            <th>Email</th>
                            <th>City</th>
                            <th>Total</th>
                            <th>Status</th>
                            <th>Date</th>
                            <th>Update Status</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php
                        $all_orders = mysqli_query($conn, "
                            SELECT o.*, u.name AS customer_name
                            FROM orders o JOIN users u ON o.user_id = u.id
                            ORDER BY o.created_at DESC
                        ");
                        while ($order = mysqli_fetch_assoc($all_orders)):
                        ?>
                        <tr>
                            <td>#<?php echo $order['id']; ?></td>
                            <td><?php echo htmlspecialchars($order['customer_name']); ?></td>
                            <td><?php echo htmlspecialchars($order['email']); ?></td>
                            <td><?php echo htmlspecialchars($order['city']); ?></td>
                            <td style="color:var(--pink);font-weight:700;">₹<?php echo number_format($order['total'], 2); ?></td>
                            <td>
                                <span class="status-badge status-<?php echo $order['status']; ?>">
                                    <?php echo ucfirst($order['status']); ?>
                                </span>
                            </td>
                            <td><?php echo date('d M Y', strtotime($order['created_at'])); ?></td>
                            <td>
                                <form method="POST" style="display:flex;gap:6px;align-items:center;">
                                    <input type="hidden" name="order_id" value="<?php echo $order['id']; ?>">
                                    <select name="status" style="padding:6px 10px;border:1px solid var(--border);border-radius:8px;font-size:12px;background:var(--pink-pale);outline:none;">
                                        <option value="pending"    <?php echo $order['status']==='pending'   ?'selected':''; ?>>Pending</option>
                                        <option value="processing" <?php echo $order['status']==='processing'?'selected':''; ?>>Processing</option>
                                        <option value="shipped"    <?php echo $order['status']==='shipped'   ?'selected':''; ?>>Shipped</option>
                                        <option value="delivered"  <?php echo $order['status']==='delivered' ?'selected':''; ?>>Delivered</option>
                                    </select>
                                    <button type="submit" name="update_status" class="btn btn-sm btn-success">✓</button>
                                </form>
                            </td>
                        </tr>
                        <?php endwhile; ?>
                    </tbody>
                </table>
            </div>

        <!-- ========================
             USERS TAB
             ======================== -->
        <?php elseif ($tab === 'users'): ?>
            <div class="admin-table-box">
                <h3>All Users</h3>
                <table class="admin-table">
                    <thead>
                        <tr>
                            <th>#</th>
                            <th>Name</th>
                            <th>Email</th>
                            <th>Role</th>
                            <th>Registered</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php while ($u = mysqli_fetch_assoc($users_result)): ?>
                        <tr>
                            <td><?php echo $u['id']; ?></td>
                            <td><?php echo htmlspecialchars($u['name']); ?></td>
                            <td><?php echo htmlspecialchars($u['email']); ?></td>
                            <td>
                                <span class="status-badge status-<?php echo $u['role']; ?>">
                                    <?php echo ucfirst($u['role']); ?>
                                </span>
                            </td>
                            <td><?php echo date('d M Y', strtotime($u['created_at'])); ?></td>
                        </tr>
                        <?php endwhile; ?>
                    </tbody>
                </table>
            </div>
        <?php endif; ?>

        </div><!-- /admin-content -->
    </div><!-- /admin-main -->
</div><!-- /admin-layout -->

</body>
</html>
