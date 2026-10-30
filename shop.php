<?php
$pageTitle = "Shop Collection";
require_once __DIR__ . '/includes/header.php';

// Fetch Categories for Sidebar
$catStmt = $pdo->query("SELECT * FROM categories ORDER BY name ASC");
$categories = $catStmt->fetchAll();

// Build Query
$sql = "SELECT p.*, c.name as category_name, c.slug as category_slug 
        FROM products p 
        LEFT JOIN categories c ON p.category_id = c.id 
        WHERE 1=1";
$params = [];

// Filters
$categorySlug = $_GET['category'] ?? '';
if (!empty($categorySlug)) {
    $sql .= " AND c.slug = :category";
    $params[':category'] = $categorySlug;
}

$search = trim($_GET['search'] ?? '');
if (!empty($search)) {
    $sql .= " AND (p.name LIKE :search OR p.description LIKE :search)";
    $params[':search'] = "%$search%";
}

$minPrice = $_GET['min_price'] ?? '';
if (is_numeric($minPrice)) {
    $sql .= " AND p.price >= :min_price";
    $params[':min_price'] = (float)$minPrice;
}

$maxPrice = $_GET['max_price'] ?? '';
if (is_numeric($maxPrice)) {
    $sql .= " AND p.price <= :max_price";
    $params[':max_price'] = (float)$maxPrice;
}

// Sorting
$sort = $_GET['sort'] ?? 'newest';
switch ($sort) {
    case 'price_asc':
        $sql .= " ORDER BY p.price ASC";
        break;
    case 'price_desc':
        $sql .= " ORDER BY p.price DESC";
        break;
    default:
        $sql .= " ORDER BY p.id DESC";
        break;
}

$stmt = $pdo->prepare($sql);
$stmt->execute($params);
$products = $stmt->fetchAll();
?>

<div style="margin-bottom: 30px;">
    <h1 style="font-size: 32px; font-weight: 800; color: var(--dark);">Women's Traditional Catalog</h1>
    <p style="font-size: 14px; color: var(--text-muted); margin-top: 4px;">Explore <?= count($products); ?> handcrafted outfits, sarees, and ensembles.</p>
</div>

<div style="display: grid; grid-template-columns: 280px 1fr; gap: 36px; align-items: start;">
    <!-- Filter Sidebar -->
    <aside style="background: white; padding: 24px; border-radius: var(--radius); border: 1px solid var(--border); box-shadow: var(--shadow-sm);">
        <form method="GET" action="<?= base_url('shop.php'); ?>">
            <!-- Search -->
            <div class="form-group">
                <label class="form-label">Search Keyword</label>
                <input type="text" name="search" class="form-control" placeholder="e.g. Saree, Silk, Red..." value="<?= htmlspecialchars($search); ?>">
            </div>

            <!-- Category -->
            <div class="form-group">
                <label class="form-label">Category</label>
                <div style="display: flex; flex-direction: column; gap: 8px;">
                    <label style="font-size: 13px; display:flex; align-items:center; gap:8px;">
                        <input type="radio" name="category" value="" <?= empty($categorySlug) ? 'checked' : ''; ?>>
                        All Categories
                    </label>
                    <?php foreach ($categories as $cat): ?>
                        <label style="font-size: 13px; display:flex; align-items:center; gap:8px;">
                            <input type="radio" name="category" value="<?= htmlspecialchars($cat['slug']); ?>" <?= $categorySlug === $cat['slug'] ? 'checked' : ''; ?>>
                            <?= htmlspecialchars($cat['name']); ?>
                        </label>
                    <?php endforeach; ?>
                </div>
            </div>

            <!-- Price Range -->
            <div class="form-group">
                <label class="form-label">Price Range (₹)</label>
                <div style="display: flex; gap: 8px; align-items:center;">
                    <input type="number" name="min_price" class="form-control" placeholder="Min" value="<?= htmlspecialchars($minPrice); ?>">
                    <span>-</span>
                    <input type="number" name="max_price" class="form-control" placeholder="Max" value="<?= htmlspecialchars($maxPrice); ?>">
                </div>
            </div>

            <!-- Sort -->
            <div class="form-group">
                <label class="form-label">Sort By</label>
                <select name="sort" class="form-control">
                    <option value="newest" <?= $sort === 'newest' ? 'selected' : ''; ?>>Newest Arrivals</option>
                    <option value="price_asc" <?= $sort === 'price_asc' ? 'selected' : ''; ?>>Price: Low to High</option>
                    <option value="price_desc" <?= $sort === 'price_desc' ? 'selected' : ''; ?>>Price: High to Low</option>
                </select>
            </div>

            <button type="submit" class="btn btn-primary" style="width: 100%; border-radius: 8px;">Apply Filters</button>
            <?php if (!empty($categorySlug) || !empty($search) || !empty($minPrice) || !empty($maxPrice)): ?>
                <a href="<?= base_url('shop.php'); ?>" style="display:block; text-align:center; margin-top:12px; font-size:12px; color:var(--text-muted);">Clear Filters</a>
            <?php endif; ?>
        </form>
    </aside>

    <!-- Product Grid -->
    <div>
        <?php if (empty($products)): ?>
            <div style="background: white; padding: 60px; text-align: center; border-radius: var(--radius); border: 1px solid var(--border);">
                <div style="font-size: 40px; margin-bottom: 12px;">🛍️</div>
                <h3 style="font-size: 18px; font-weight: 700; color: var(--dark);">No matching products found</h3>
                <p style="font-size: 13px; color: var(--text-muted); margin: 8px 0 20px;">Try adjusting your search criteria or price boundaries.</p>
                <a href="<?= base_url('shop.php'); ?>" class="btn btn-primary btn-sm">Reset Catalog</a>
            </div>
        <?php else: ?>
            <div class="grid-3">
                <?php foreach ($products as $product): ?>
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
                                        <a href="<?= base_url('wishlist.php?action=add&id=' . $product['id']); ?>" class="btn btn-outline btn-sm" style="padding:6px 10px;" title="Wishlist">❤️</a>
                                    <?php endif; ?>
                                    <a href="<?= base_url('cart.php?action=add&id=' . $product['id']); ?>" class="btn btn-primary btn-sm">Add to Bag</a>
                                </div>
                            </div>
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>
        <?php endif; ?>
    </div>
</div>

<?php require_once __DIR__ . '/includes/footer.php'; ?>
