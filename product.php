<?php
require_once __DIR__ . '/config/db.php';

$id = (int)($_GET['id'] ?? 0);
$stmt = $pdo->prepare("SELECT p.*, c.name as category_name, c.slug as category_slug FROM products p LEFT JOIN categories c ON p.category_id = c.id WHERE p.id = :id");
$stmt->execute([':id' => $id]);
$product = $stmt->fetch();

if (!$product) {
    set_flash('error', 'Product not found.');
    header('Location: ' . base_url('shop.php'));
    exit;
}

$pageTitle = $product['name'];
require_once __DIR__ . '/includes/header.php';

// Fetch Related Products
$relStmt = $pdo->prepare("SELECT * FROM products WHERE category_id = :cat_id AND id != :id LIMIT 4");
$relStmt->execute([':cat_id' => $product['category_id'], ':id' => $id]);
$related = $relStmt->fetchAll();
?>

<div style="display: grid; grid-template-columns: 1fr 1fr; gap: 48px; background: white; padding: 40px; border-radius: var(--radius); border: 1px solid var(--border); box-shadow: var(--shadow-sm); margin-bottom: 60px;">
    <!-- Product Image -->
    <div style="border-radius: var(--radius); overflow: hidden; background: #f8fafc; height: 500px;">
        <img src="<?= htmlspecialchars($product['image']); ?>" alt="<?= htmlspecialchars($product['name']); ?>" style="width: 100%; height: 100%; object-fit: cover;" onerror="this.src='https://images.unsplash.com/photo-1610189012906-40742d4a6de6?auto=format&fit=crop&w=800&q=80'">
    </div>

    <!-- Details -->
    <div style="display: flex; flex-direction: column;">
        <span style="font-size: 12px; font-weight: 700; color: var(--primary); text-transform: uppercase; letter-spacing: 1px; margin-bottom: 8px;">
            <?= htmlspecialchars($product['category_name'] ?? 'Collection'); ?>
        </span>

        <h1 style="font-size: 32px; font-weight: 800; color: var(--dark); line-height: 1.2; margin-bottom: 12px;">
            <?= htmlspecialchars($product['name']); ?>
        </h1>

        <div style="display: flex; align-items: center; gap: 16px; margin-bottom: 24px;">
            <span style="font-size: 28px; font-weight: 800; color: var(--primary);"><?= format_price($product['price']); ?></span>
            <span class="status-pill status-completed">In Stock (<?= $product['stock']; ?> Available)</span>
        </div>

        <div style="border-top: 1px solid var(--border); border-bottom: 1px solid var(--border); padding: 20px 0; margin-bottom: 24px;">
            <h4 style="font-size: 13px; font-weight: 700; color: var(--dark); text-transform: uppercase; margin-bottom: 8px;">Description</h4>
            <p style="font-size: 14px; color: var(--text-muted); line-height: 1.7;"><?= nl2br(htmlspecialchars($product['description'])); ?></p>
        </div>

        <!-- Add to Cart Form -->
        <form method="POST" action="<?= base_url('cart.php?action=add'); ?>" style="display: flex; gap: 16px; align-items: center; margin-bottom: 24px;">
            <input type="hidden" name="product_id" value="<?= $product['id']; ?>">
            
            <div style="display: flex; align-items: center; gap: 8px;">
                <label style="font-size: 13px; font-weight: 600; color: var(--dark);">Qty:</label>
                <input type="number" name="quantity" value="1" min="1" max="<?= max(1, $product['stock']); ?>" class="form-control" style="width: 70px; padding: 8px 10px; text-align: center;">
            </div>

            <button type="submit" class="btn btn-primary" style="flex: 1; padding: 12px;">
                Add to Shopping Bag
            </button>

            <?php if (is_logged_in()): ?>
                <a href="<?= base_url('wishlist.php?action=add&id=' . $product['id']); ?>" class="btn btn-outline" style="padding: 12px 16px;" title="Wishlist">❤️</a>
            <?php endif; ?>
        </form>

        <!-- Perks / Guarantees -->
        <div style="margin-top: auto; padding-top: 20px; border-top: 1px solid #f1f5f9; display: grid; grid-template-columns: 1fr 1fr; gap: 12px; font-size: 13px; color: var(--text-muted);">
            <div>✨ 100% Authentic Handloom Silk</div>
            <div>🚚 Free Delivery above ₹1,999</div>
            <div>🔄 Easy 7-Day Exchange</div>
            <div>💳 Cash on Delivery & Card</div>
        </div>
    </div>
</div>

<!-- Related Products -->
<?php if (!empty($related)): ?>
    <section>
        <div class="section-header">
            <div>
                <div class="section-subtitle">Similar Styles</div>
                <h2 class="section-title">You May Also Like</h2>
            </div>
        </div>

        <div class="grid-4">
            <?php foreach ($related as $rel): ?>
                <div class="product-card">
                    <div class="product-img-box" style="height: 240px;">
                        <img src="<?= htmlspecialchars($rel['image']); ?>" alt="<?= htmlspecialchars($rel['name']); ?>" onerror="this.src='https://images.unsplash.com/photo-1610189012906-40742d4a6de6?auto=format&fit=crop&w=500&q=80'">
                    </div>
                    <div class="product-content">
                        <a href="<?= base_url('product.php?id=' . $rel['id']); ?>" class="product-title"><?= htmlspecialchars($rel['name']); ?></a>
                        <div class="product-footer" style="padding-top: 8px;">
                            <span class="price"><?= format_price($rel['price']); ?></span>
                            <a href="<?= base_url('product.php?id=' . $rel['id']); ?>" class="btn btn-outline btn-sm">View</a>
                        </div>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>
    </section>
<?php endif; ?>

<?php require_once __DIR__ . '/includes/footer.php'; ?>
