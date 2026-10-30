<?php
require_once __DIR__ . '/../config/db.php';
require_admin();

// Handle Delete Product
if (isset($_GET['delete'])) {
    $id = (int)$_GET['delete'];
    $stmt = $pdo->prepare("DELETE FROM products WHERE id = :id");
    $stmt->execute([':id' => $id]);
    set_flash('success', 'Product deleted from inventory.');
    header('Location: ' . base_url('admin/products.php'));
    exit;
}

$pageTitle = "Products Inventory";
$headerTitle = "Products Catalog";
$headerDesc = "Manage dresses, sarees, lehengas, pricing, and stock";

$stmt = $pdo->query("SELECT p.*, c.name as category_name 
                     FROM products p 
                     LEFT JOIN categories c ON p.category_id = c.id 
                     ORDER BY p.id DESC");
$products = $stmt->fetchAll();

require_once __DIR__ . '/includes/admin_header.php';
?>

<div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 24px;">
    <span style="font-size: 14px; color: var(--text-muted);"><?= count($products); ?> total apparel items in database</span>
    <a href="<?= base_url('admin/product_form.php'); ?>" class="btn btn-primary" style="border-radius: 8px;">+ Add New Product</a>
</div>

<div class="table-wrapper">
    <table class="table">
        <thead>
            <tr>
                <th>Product</th>
                <th>Category</th>
                <th>Price</th>
                <th>Stock</th>
                <th style="text-align: right;">Actions</th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($products as $p): ?>
                <tr>
                    <td style="display: flex; align-items: center; gap: 14px;">
                        <img src="<?= htmlspecialchars($p['image']); ?>" alt="" style="width: 48px; height: 60px; object-fit: cover; border-radius: 6px;" onerror="this.src='https://images.unsplash.com/photo-1610189012906-40742d4a6de6?auto=format&fit=crop&w=150&q=80'">
                        <div>
                            <strong style="color: var(--dark); display:block;"><?= htmlspecialchars($p['name']); ?></strong>
                            <span style="font-size: 11px; color: var(--text-muted); font-family:monospace;"><?= htmlspecialchars($p['slug']); ?></span>
                        </div>
                    </td>
                    <td><?= htmlspecialchars($p['category_name'] ?? 'None'); ?></td>
                    <td style="font-weight: 800; color: var(--dark);"><?= format_price($p['price']); ?></td>
                    <td>
                        <span class="status-pill <?= $p['stock'] > 0 ? 'status-completed' : 'status-cancelled'; ?>">
                            <?= $p['stock']; ?> In Stock
                        </span>
                    </td>
                    <td style="text-align: right;">
                        <a href="<?= base_url('admin/product_form.php?id=' . $p['id']); ?>" style="color: var(--primary); font-size: 13px; font-weight: 700; margin-right: 12px;">Edit</a>
                        <a href="<?= base_url('admin/products.php?delete=' . $p['id']); ?>" style="color: var(--danger); font-size: 13px; font-weight: 600;" onclick="return confirm('Delete this product?');">Delete</a>
                    </td>
                </tr>
            <?php endforeach; ?>
        </tbody>
    </table>
</div>

<?php require_once __DIR__ . '/includes/admin_footer.php'; ?>
