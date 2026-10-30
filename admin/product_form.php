<?php
require_once __DIR__ . '/../config/db.php';
require_admin();

$id = (int)($_GET['id'] ?? 0);
$product = null;

if ($id > 0) {
    $stmt = $pdo->prepare("SELECT * FROM products WHERE id = :id");
    $stmt->execute([':id' => $id]);
    $product = $stmt->fetch();
}

// Handle Form Submission
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name = trim($_POST['name'] ?? '');
    $categoryId = (int)($_POST['category_id'] ?? 0);
    $price = (float)($_POST['price'] ?? 0);
    $stock = (int)($_POST['stock'] ?? 0);
    $image = trim($_POST['image'] ?? '');
    $description = trim($_POST['description'] ?? '');

    if (empty($name) || $categoryId <= 0 || $price <= 0) {
        set_flash('error', 'Name, category, and price are required.');
    } else {
        $slug = strtolower(trim(preg_replace('/[^A-Za-z0-9-]+/', '-', $name), '-')) . '-' . substr(uniqid(), -4);

        if ($product) {
            // Update
            $uStmt = $pdo->prepare("UPDATE products SET category_id = :cat_id, name = :name, price = :price, stock = :stock, image = :image, description = :description, updated_at = NOW() WHERE id = :id");
            $uStmt->execute([
                ':cat_id' => $categoryId,
                ':name' => $name,
                ':price' => $price,
                ':stock' => $stock,
                ':image' => $image,
                ':description' => $description,
                ':id' => $id
            ]);
            set_flash('success', 'Product updated successfully.');
        } else {
            // Insert
            $iStmt = $pdo->prepare("INSERT INTO products (category_id, name, slug, price, stock, image, description, created_at, updated_at) 
                                   VALUES (:cat_id, :name, :slug, :price, :stock, :image, :description, NOW(), NOW())");
            $iStmt->execute([
                ':cat_id' => $categoryId,
                ':name' => $name,
                ':slug' => $slug,
                ':price' => $price,
                ':stock' => $stock,
                ':image' => $image,
                ':description' => $description
            ]);
            set_flash('success', 'Product added to catalog.');
        }
        header('Location: ' . base_url('admin/products.php'));
        exit;
    }
}

// Fetch Categories for Dropdown
$catStmt = $pdo->query("SELECT * FROM categories ORDER BY name ASC");
$categories = $catStmt->fetchAll();

$pageTitle = $product ? "Edit Product" : "Create Product";
$headerTitle = $product ? "Edit Product #{$product['id']}" : "Add New Product";
$headerDesc = "Fill in product specifications, pricing, and high-res imagery";

require_once __DIR__ . '/includes/admin_header.php';
?>

<div style="max-width: 680px; background: white; padding: 36px; border-radius: var(--radius); border: 1px solid var(--border); box-shadow: var(--shadow-sm);">
    <form method="POST" action="<?= base_url('admin/product_form.php' . ($product ? '?id=' . $product['id'] : '')); ?>">
        <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 20px;">
            <div class="form-group">
                <label class="form-label">Product Name *</label>
                <input type="text" name="name" required class="form-control" placeholder="e.g. Royal Blue Silk Saree" value="<?= htmlspecialchars($product['name'] ?? ''); ?>">
            </div>

            <div class="form-group">
                <label class="form-label">Category *</label>
                <select name="category_id" required class="form-control">
                    <option value="">Select Category</option>
                    <?php foreach ($categories as $cat): ?>
                        <option value="<?= $cat['id']; ?>" <?= ($product && $product['category_id'] == $cat['id']) ? 'selected' : ''; ?>>
                            <?= htmlspecialchars($cat['name']); ?>
                        </option>
                    <?php endforeach; ?>
                </select>
            </div>
        </div>

        <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 20px;">
            <div class="form-group">
                <label class="form-label">Price (₹) *</label>
                <input type="number" step="0.01" name="price" required class="form-control" placeholder="1999.00" value="<?= htmlspecialchars($product['price'] ?? ''); ?>">
            </div>

            <div class="form-group">
                <label class="form-label">Stock Quantity *</label>
                <input type="number" name="stock" required class="form-control" placeholder="15" value="<?= htmlspecialchars($product['stock'] ?? '10'); ?>">
            </div>
        </div>

        <div class="form-group">
            <label class="form-label">Product Image URL</label>
            <input type="text" name="image" class="form-control" placeholder="https://images.unsplash.com/... or relative path" value="<?= htmlspecialchars($product['image'] ?? ''); ?>">
        </div>

        <div class="form-group">
            <label class="form-label">Detailed Description</label>
            <textarea name="description" rows="5" class="form-control" placeholder="Fabric details, zari work, embroidery style..."><?= htmlspecialchars($product['description'] ?? ''); ?></textarea>
        </div>

        <div style="display: flex; gap: 14px; margin-top: 10px;">
            <button type="submit" class="btn btn-primary" style="padding: 12px 28px; border-radius: 8px;">
                <?= $product ? 'Save Changes' : 'Publish Product'; ?>
            </button>
            <a href="<?= base_url('admin/products.php'); ?>" class="btn btn-outline" style="border-radius: 8px; color: var(--text-muted); border-color: var(--border);">Cancel</a>
        </div>
    </form>
</div>

<?php require_once __DIR__ . '/includes/admin_footer.php'; ?>
