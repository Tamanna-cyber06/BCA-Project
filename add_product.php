<?php
// ============================================
// GlamCart — Add / Edit Product
// add_product.php
// ============================================

session_start();
require_once 'db.php';

// Admin-only access
if (!isset($_SESSION['user_id']) || $_SESSION['user_role'] !== 'admin') {
    header("Location: login.php");
    exit;
}

$error   = '';
$success = '';

// Check if we're editing an existing product
$editing    = false;
$edit_data  = null;
$edit_id    = 0;

if (isset($_GET['edit']) && is_numeric($_GET['edit'])) {
    $edit_id = (int)$_GET['edit'];
    $res     = mysqli_query($conn, "SELECT * FROM products WHERE id = $edit_id LIMIT 1");
    if (mysqli_num_rows($res) > 0) {
        $edit_data = mysqli_fetch_assoc($res);
        $editing   = true;
    }
}

// Fetch categories for dropdown
$categories = [];
$cat_result = mysqli_query($conn, "SELECT * FROM categories ORDER BY name");
while ($cat = mysqli_fetch_assoc($cat_result)) {
    $categories[] = $cat;
}

// ============================================
// HANDLE FORM SUBMISSION
// ============================================

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name        = sanitize($conn, $_POST['name']        ?? '');
    $description = sanitize($conn, $_POST['description'] ?? '');
    $price       = (float)($_POST['price']     ?? 0);
    $category_id = (int)($_POST['category_id'] ?? 0);
    $stock       = (int)($_POST['stock']       ?? 10);
    $image_path  = sanitize($conn, $_POST['image_path']  ?? '');
    $action      = $_POST['form_action'] ?? 'add';

    // Validation
    if (empty($name)) {
        $error = 'Product name is required.';
    } elseif ($price <= 0) {
        $error = 'Please enter a valid price.';
    } elseif ($category_id === 0) {
        $error = 'Please select a category.';
    } elseif (empty($image_path)) {
        $error = 'Please provide an image path.';
    } else {
        if ($action === 'edit' && isset($_POST['edit_id'])) {
            // Update existing product
            $eid = (int)$_POST['edit_id'];
            $sql = "
                UPDATE products SET
                    name        = '$name',
                    description = '$description',
                    price       = $price,
                    category_id = $category_id,
                    stock       = $stock,
                    image       = '$image_path'
                WHERE id = $eid
            ";
            if (mysqli_query($conn, $sql)) {
                $success = "Product updated successfully! ✅";
            } else {
                $error = "Failed to update product. " . mysqli_error($conn);
            }
        } else {
            // Insert new product
            $sql = "
                INSERT INTO products (name, description, price, image, category_id, stock)
                VALUES ('$name', '$description', $price, '$image_path', $category_id, $stock)
            ";
            if (mysqli_query($conn, $sql)) {
                $success = "Product added successfully! 🎉";
                $name = $description = $image_path = ''; // Clear fields
                $price = 0; $stock = 10; $category_id = 0;
            } else {
                $error = "Failed to add product. " . mysqli_error($conn);
            }
        }
    }
}

$page_title = $editing ? 'Edit Product' : 'Add New Product';
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo $page_title; ?> — GlamCart Admin</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>

<!-- TOP BAR -->
<div style="background:var(--dark);color:white;padding:12px 30px;display:flex;justify-content:space-between;align-items:center;">
    <span style="font-family:var(--font-head);font-size:20px;color:var(--pink);">💄 GlamCart Admin</span>
    <div style="display:flex;gap:20px;font-size:13px;">
        <a href="admin.php?tab=products" style="color:rgba(255,255,255,0.7);">← Back to Products</a>
        <a href="index.php" style="color:rgba(255,255,255,0.7);">🏪 View Store</a>
        <a href="logout.php" style="color:var(--pink);">Logout</a>
    </div>
</div>

<div class="section" style="max-width:760px;padding-top:40px;">

    <!-- Page Header -->
    <div style="margin-bottom:30px;">
        <h1 style="font-family:var(--font-head);font-size:34px;color:var(--dark);">
            <?php echo $editing ? '✏️ Edit Product' : '➕ Add New Product'; ?>
        </h1>
        <p style="color:var(--gray);margin-top:6px;">
            <?php echo $editing ? 'Update the product details below.' : 'Fill in the details to add a new product to the store.'; ?>
        </p>
    </div>

    <!-- Flash Messages -->
    <?php if (!empty($error)): ?>
        <div class="flash flash-error"><?php echo htmlspecialchars($error); ?></div>
    <?php endif; ?>
    <?php if (!empty($success)): ?>
        <div class="flash flash-success"><?php echo htmlspecialchars($success); ?></div>
    <?php endif; ?>

    <!-- FORM -->
    <div class="add-product-form">
        <form method="POST" action="add_product.php<?php echo $editing ? "?edit=$edit_id" : ''; ?>">
            <input type="hidden" name="form_action" value="<?php echo $editing ? 'edit' : 'add'; ?>">
            <?php if ($editing): ?>
                <input type="hidden" name="edit_id" value="<?php echo $edit_id; ?>">
            <?php endif; ?>

            <div class="form-group">
                <label>🏷️ Product Name *</label>
                <input type="text" name="name"
                       placeholder="e.g. Floral Summer Dress"
                       value="<?php echo $editing ? htmlspecialchars($edit_data['name']) : (isset($_POST['name']) ? htmlspecialchars($_POST['name']) : ''); ?>"
                       required>
            </div>

            <div class="form-group">
                <label>📝 Description</label>
                <textarea name="description" placeholder="Write a brief product description..." rows="4"><?php
                    echo $editing ? htmlspecialchars($edit_data['description']) : (isset($_POST['description']) ? htmlspecialchars($_POST['description']) : '');
                ?></textarea>
            </div>

            <div class="form-row">
                <div class="form-group">
                    <label>💰 Price (₹) *</label>
                    <input type="number" name="price" step="0.01" min="0"
                           placeholder="e.g. 1299.00"
                           value="<?php echo $editing ? $edit_data['price'] : (isset($_POST['price']) ? $_POST['price'] : ''); ?>"
                           required>
                </div>
                <div class="form-group">
                    <label>📦 Stock Quantity *</label>
                    <input type="number" name="stock" min="0"
                           placeholder="e.g. 20"
                           value="<?php echo $editing ? $edit_data['stock'] : (isset($_POST['stock']) ? $_POST['stock'] : '10'); ?>">
                </div>
            </div>

            <div class="form-group">
                <label>🗂️ Category *</label>
                <select name="category_id" required>
                    <option value="">-- Select a Category --</option>
                    <?php foreach ($categories as $cat): ?>
                        <option value="<?php echo $cat['id']; ?>"
                            <?php
                            $selected_cat = $editing ? $edit_data['category_id'] : (isset($_POST['category_id']) ? $_POST['category_id'] : 0);
                            echo ($selected_cat == $cat['id']) ? 'selected' : '';
                            ?>>
                            <?php echo htmlspecialchars($cat['name']); ?>
                        </option>
                    <?php endforeach; ?>
                </select>
            </div>

            <div class="form-group">
                <label>🖼️ Image Path *</label>
                <input type="text" name="image_path"
                       placeholder="e.g. images/dress1.jpg"
                       value="<?php echo $editing ? htmlspecialchars($edit_data['image']) : (isset($_POST['image_path']) ? htmlspecialchars($_POST['image_path']) : ''); ?>">
                <small style="color:var(--gray);font-size:12px;margin-top:6px;display:block;">
                    💡 Place your image file in the <strong>glamcart/images/</strong> folder, then enter the relative path here.
                    e.g. <code>images/dress1.jpg</code>
                </small>
            </div>

            <!-- Sample image paths hint -->
            <div style="background:var(--pink-pale);border:1px solid var(--border);border-radius:10px;padding:16px;margin-bottom:22px;">
                <strong style="font-size:13px;">💡 Sample Image Path Examples:</strong>
                <ul style="margin-top:8px;font-size:12px;color:var(--gray);list-style:disc;padding-left:18px;">
                    <li>images/dress1.jpg — images/dress2.jpg — images/dress3.jpg</li>
                    <li>images/shoes1.jpg — images/shoes2.jpg</li>
                    <li>images/bag1.jpg — images/bag2.jpg</li>
                    <li>images/makeup1.jpg — images/makeup2.jpg</li>
                </ul>
                <p style="font-size:12px;color:var(--gray);margin-top:8px;">
                    Or use placeholder: <code>https://placehold.co/400x400/FFB6C1/fff?text=Product</code>
                </p>
            </div>

            <div style="display:flex;gap:14px;">
                <button type="submit" class="btn btn-primary" style="flex:1;justify-content:center;">
                    <?php echo $editing ? '✏️ Update Product' : '➕ Add Product'; ?>
                </button>
                <a href="admin.php?tab=products" class="btn btn-outline" style="flex:0 0 auto;">Cancel</a>
            </div>
        </form>
    </div>

    <!-- Quick Tips -->
    <div style="margin-top:30px;background:white;border:1px solid var(--border);border-radius:14px;padding:26px;">
        <h3 style="font-family:var(--font-head);font-size:18px;margin-bottom:14px;">📸 Image Setup Tips</h3>
        <ol style="font-size:14px;color:var(--gray);line-height:2;padding-left:18px;">
            <li>Download free product images from <strong>Unsplash.com</strong> or <strong>Pexels.com</strong></li>
            <li>Place them in <code>htdocs/glamcart/images/</code> folder</li>
            <li>Name them like: <code>dress1.jpg</code>, <code>shoes1.jpg</code>, etc.</li>
            <li>Enter the path as: <code>images/dress1.jpg</code></li>
            <li>Recommended size: <strong>600×600px</strong> or larger</li>
        </ol>
    </div>
</div>

<!-- FOOTER -->
<footer>
    <div class="footer-bottom" style="max-width:100%;border-top:1px solid rgba(255,255,255,0.08);padding:20px 60px;">
        <p>© 2024 GlamCart Admin Panel. Made with 💕 by Tamanna Dadhwal & Simran.</p>
    </div>
</footer>

</body>
</html>
