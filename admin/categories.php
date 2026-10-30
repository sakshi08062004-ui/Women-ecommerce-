<?php
require_once __DIR__ . '/../config/db.php';
require_admin();

// Handle Add Category
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['add_category'])) {
    $name = trim($_POST['name'] ?? '');
    $image = trim($_POST['image'] ?? '');
    $slug = strtolower(trim(preg_replace('/[^A-Za-z0-9-]+/', '-', $name), '-'));

    if (empty($name)) {
        set_flash('error', 'Category name is required.');
    } else {
        $stmt = $pdo->prepare("INSERT INTO categories (name, slug, image, created_at, updated_at) VALUES (:name, :slug, :image, NOW(), NOW())");
        $stmt->execute([':name' => $name, ':slug' => $slug, ':image' => $image]);
        set_flash('success', "Category '{$name}' created successfully.");
        header('Location: ' . base_url('admin/categories.php'));
        exit;
    }
}

// Handle Delete Category
if (isset($_GET['delete'])) {
    $id = (int)$_GET['delete'];
    $stmt = $pdo->prepare("DELETE FROM categories WHERE id = :id");
    $stmt->execute([':id' => $id]);
    set_flash('success', 'Category deleted.');
    header('Location: ' . base_url('admin/categories.php'));
    exit;
}

$pageTitle = "Manage Categories";
$headerTitle = "Product Categories";
$headerDesc = "Manage classifications and showcase images for ethnic wear";

$catStmt = $pdo->query("SELECT c.*, COUNT(p.id) as product_count FROM categories c LEFT JOIN products p ON c.id = p.category_id GROUP BY c.id ORDER BY c.name ASC");
$categories = $catStmt->fetchAll();

require_once __DIR__ . '/includes/admin_header.php';
?>

<div style="display: grid; grid-template-columns: 1fr 340px; gap: 30px; align-items: start;">
    <!-- Categories Table -->
    <div class="table-wrapper">
        <table class="table">
            <thead>
                <tr>
                    <th>Category</th>
                    <th>Slug</th>
                    <th>Products</th>
                    <th style="text-align: right;">Action</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($categories as $cat): ?>
                    <tr>
                        <td style="display: flex; align-items: center; gap: 12px;">
                            <img src="<?= htmlspecialchars($cat['image'] ?: 'https://images.unsplash.com/photo-1610189012906-40742d4a6de6?auto=format&fit=crop&w=100&q=80'); ?>" style="width: 44px; height: 44px; border-radius: 8px; object-fit: cover;">
                            <strong style="color: var(--dark);"><?= htmlspecialchars($cat['name']); ?></strong>
                        </td>
                        <td style="font-family: monospace; font-size: 12px; color: var(--text-muted);"><?= htmlspecialchars($cat['slug']); ?></td>
                        <td>
                            <span class="badge" style="position: static;"><?= $cat['product_count']; ?> items</span>
                        </td>
                        <td style="text-align: right;">
                            <a href="<?= base_url('admin/categories.php?delete=' . $cat['id']); ?>" style="color: var(--danger); font-size: 13px; font-weight: 600;" onclick="return confirm('Delete this category?');">Delete</a>
                        </td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>

    <!-- Add Category Form -->
    <div style="background: white; padding: 24px; border-radius: var(--radius); border: 1px solid var(--border); box-shadow: var(--shadow-sm);">
        <h3 style="font-size: 16px; font-weight: 800; color: var(--dark); margin-bottom: 20px;">+ Add New Category</h3>
        
        <form method="POST" action="<?= base_url('admin/categories.php'); ?>">
            <input type="hidden" name="add_category" value="1">
            <div class="form-group">
                <label class="form-label">Category Name *</label>
                <input type="text" name="name" required class="form-control" placeholder="e.g. Dupattas & Stoles">
            </div>

            <div class="form-group">
                <label class="form-label">Cover Image URL</label>
                <input type="text" name="image" class="form-control" placeholder="https://...">
            </div>

            <button type="submit" class="btn btn-primary" style="width: 100%; padding: 12px; border-radius: 8px;">
                Create Category
            </button>
        </form>
    </div>
</div>

<?php require_once __DIR__ . '/includes/admin_footer.php'; ?>
