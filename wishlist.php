<?php
require_once __DIR__ . '/config/db.php';
require_login();

$userId = $_SESSION['user_id'];
$action = $_GET['action'] ?? '';

if ($action === 'add') {
    $productId = (int)($_GET['id'] ?? 0);
    if ($productId > 0) {
        $check = $pdo->prepare("SELECT id FROM wishlists WHERE user_id = :uid AND product_id = :pid");
        $check->execute([':uid' => $userId, ':pid' => $productId]);
        if (!$check->fetch()) {
            $add = $pdo->prepare("INSERT INTO wishlists (user_id, product_id, created_at, updated_at) VALUES (:uid, :pid, NOW(), NOW())");
            $add->execute([':uid' => $userId, ':pid' => $productId]);
            set_flash('success', 'Item saved to your wishlist!');
        } else {
            set_flash('info', 'Item is already in your wishlist.');
        }
    }
    header('Location: ' . base_url('wishlist.php'));
    exit;
}

if ($action === 'remove') {
    $id = (int)($_GET['id'] ?? 0);
    $del = $pdo->prepare("DELETE FROM wishlists WHERE id = :id AND user_id = :uid");
    $del->execute([':id' => $id, ':uid' => $userId]);
    set_flash('success', 'Item removed from wishlist.');
    header('Location: ' . base_url('wishlist.php'));
    exit;
}

$stmt = $pdo->prepare("SELECT w.id as wishlist_id, p.*, c.name as category_name 
                      FROM wishlists w 
                      JOIN products p ON w.product_id = p.id 
                      LEFT JOIN categories c ON p.category_id = c.id 
                      WHERE w.user_id = :uid 
                      ORDER BY w.id DESC");
$stmt->execute([':uid' => $userId]);
$items = $stmt->fetchAll();

$pageTitle = "My Wishlist";
require_once __DIR__ . '/includes/header.php';
?>

<div style="margin-bottom: 30px;">
    <h1 style="font-size: 32px; font-weight: 800; color: var(--dark);">My Wishlist</h1>
</div>

<?php if (empty($items)): ?>
    <div style="background: white; padding: 60px; text-align: center; border-radius: var(--radius); border: 1px solid var(--border); max-width: 600px; margin: 40px auto;">
        <div style="font-size: 48px; margin-bottom: 12px;">❤️</div>
        <h3 style="font-size: 20px; font-weight: 700; color: var(--dark); margin-bottom: 8px;">Your wishlist is empty</h3>
        <p style="font-size: 14px; color: var(--text-muted); margin-bottom: 24px;">Save your favorite ethnic dresses to review later.</p>
        <a href="<?= base_url('shop.php'); ?>" class="btn btn-primary">Browse Catalog</a>
    </div>
<?php else: ?>
    <div class="grid-4">
        <?php foreach ($items as $item): ?>
            <div class="product-card">
                <div class="product-img-box">
                    <img src="<?= htmlspecialchars($item['image']); ?>" alt="<?= htmlspecialchars($item['name']); ?>" onerror="this.src='https://images.unsplash.com/photo-1610189012906-40742d4a6de6?auto=format&fit=crop&w=500&q=80'">
                    <span class="product-badge"><?= htmlspecialchars($item['category_name'] ?? 'Collection'); ?></span>
                </div>
                <div class="product-content">
                    <a href="<?= base_url('product.php?id=' . $item['id']); ?>" class="product-title"><?= htmlspecialchars($item['name']); ?></a>
                    <span class="price" style="margin-bottom: 16px;"><?= format_price($item['price']); ?></span>

                    <div style="display: flex; gap: 8px; margin-top: auto;">
                        <a href="<?= base_url('cart.php?action=add&id=' . $item['id']); ?>" class="btn btn-primary btn-sm" style="flex:1;">Move to Bag</a>
                        <a href="<?= base_url('wishlist.php?action=remove&id=' . $item['wishlist_id']); ?>" class="btn btn-outline btn-sm" style="color:var(--danger); border-color:var(--danger);" title="Remove">✕</a>
                    </div>
                </div>
            </div>
        <?php endforeach; ?>
    </div>
<?php endif; ?>

<?php require_once __DIR__ . '/includes/footer.php'; ?>
