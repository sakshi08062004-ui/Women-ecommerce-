<?php
$pageTitle = "Home - Traditional & Contemporary Fashion";
require_once __DIR__ . '/includes/header.php';

// Fetch Categories
$catStmt = $pdo->query("SELECT c.*, COUNT(p.id) as product_count FROM categories c LEFT JOIN products p ON c.id = p.category_id GROUP BY c.id");
$categories = $catStmt->fetchAll();

// Fetch Trending / Featured Products
$prodStmt = $pdo->query("SELECT p.*, c.name as category_name FROM products p LEFT JOIN categories c ON p.category_id = c.id ORDER BY p.id DESC LIMIT 8");
$featuredProducts = $prodStmt->fetchAll();
?>

<!-- Hero Banner -->
<section class="hero">
    <h1>Grace, Elegance & Modern Heritage</h1>
    <p>Discover handwoven Kanjivaram silk sarees, majestic bridal lehengas, and everyday designer kurtis tailored for elegance.</p>
    <div>
        <a href="<?= base_url('shop.php'); ?>" class="btn btn-primary" style="background:#ffffff; color:#be185d; margin-right: 12px; font-weight:700;">Explore All Collection</a>
        <a href="<?= base_url('shop.php?category=sarees'); ?>" class="btn btn-outline" style="color:white; border-color:rgba(255,255,255,0.5);">Silk Sarees</a>
    </div>
</section>

<!-- Categories Section -->
<section style="margin-bottom: 60px;">
    <div class="section-header">
        <div>
            <div class="section-subtitle">Curated Styles</div>
            <h2 class="section-title">Shop by Category</h2>
        </div>
        <a href="<?= base_url('shop.php'); ?>" style="font-size: 13px; font-weight:700; color:var(--primary);">View Catalog &rarr;</a>
    </div>

    <div class="grid-4">
        <?php foreach ($categories as $cat): ?>
            <a href="<?= base_url('shop.php?category=' . urlencode($cat['slug'])); ?>" class="category-card">
                <img src="<?= htmlspecialchars($cat['image'] ?: 'https://images.unsplash.com/photo-1610189012906-40742d4a6de6?auto=format&fit=crop&w=500&q=80'); ?>" alt="<?= htmlspecialchars($cat['name']); ?>">
                <div class="category-overlay">
                    <h3><?= htmlspecialchars($cat['name']); ?></h3>
                    <span style="font-size: 12px; opacity: 0.9;"><?= $cat['product_count']; ?> Products</span>
                </div>
            </a>
        <?php endforeach; ?>
    </div>
</section>

<!-- Featured Products Section -->
<section>
    <div class="section-header">
        <div>
            <div class="section-subtitle">Trending Now</div>
            <h2 class="section-title">Featured Pieces</h2>
        </div>
        <a href="<?= base_url('shop.php'); ?>" style="font-size: 13px; font-weight:700; color:var(--primary);">All Products &rarr;</a>
    </div>

    <div class="grid-4">
        <?php foreach ($featuredProducts as $product): ?>
            <div class="product-card">
                <div class="product-img-box">
                    <img src="<?= htmlspecialchars($product['image']); ?>" alt="<?= htmlspecialchars($product['name']); ?>" onerror="this.src='https://images.unsplash.com/photo-1610189012906-40742d4a6de6?auto=format&fit=crop&w=500&q=80'">
                    <span class="product-badge"><?= htmlspecialchars($product['category_name'] ?? 'Collection'); ?></span>
                </div>
                <div class="product-content">
                    <a href="<?= base_url('product.php?id=' . $product['id']); ?>" class="product-title" title="<?= htmlspecialchars($product['name']); ?>">
                        <?= htmlspecialchars($product['name']); ?>
                    </a>
                    <p class="product-desc"><?= htmlspecialchars($product['description']); ?></p>
                    
                    <div class="product-footer">
                        <span class="price"><?= format_price($product['price']); ?></span>
                        <div style="display:flex; gap:6px;">
                            <?php if (is_logged_in()): ?>
                                <a href="<?= base_url('wishlist.php?action=add&id=' . $product['id']); ?>" class="btn btn-outline btn-sm" style="padding:6px 10px;" title="Save to Wishlist">❤️</a>
                            <?php endif; ?>
                            <a href="<?= base_url('cart.php?action=add&id=' . $product['id']); ?>" class="btn btn-primary btn-sm" title="Add to Bag">Add to Cart</a>
                        </div>
                    </div>
                </div>
            </div>
        <?php endforeach; ?>
    </div>
</section>

<?php require_once __DIR__ . '/includes/footer.php'; ?>
