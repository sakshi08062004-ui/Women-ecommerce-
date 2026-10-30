<?php
require_once __DIR__ . '/../config/db.php';
require_admin();

if (isset($_GET['delete'])) {
    $id = (int)$_GET['delete'];
    $stmt = $pdo->prepare("DELETE FROM users WHERE id = :id AND role = 'customer'");
    $stmt->execute([':id' => $id]);
    set_flash('success', 'Customer account deleted.');
    header('Location: ' . base_url('admin/customers.php'));
    exit;
}

$pageTitle = "Customers";
$headerTitle = "Registered Customers";
$headerDesc = "View customer activity, order history, and account status";

$stmt = $pdo->query("SELECT u.*, COUNT(o.id) as order_count 
                     FROM users u 
                     LEFT JOIN orders o ON u.id = o.user_id 
                     WHERE u.role = 'customer' 
                     GROUP BY u.id 
                     ORDER BY u.id DESC");
$customers = $stmt->fetchAll();

require_once __DIR__ . '/includes/admin_header.php';
?>

<div class="table-wrapper">
    <table class="table">
        <thead>
            <tr>
                <th>Customer</th>
                <th>Email</th>
                <th>Orders Placed</th>
                <th>Joined Date</th>
                <th style="text-align: right;">Action</th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($customers as $cust): ?>
                <tr>
                    <td style="display: flex; align-items: center; gap: 12px;">
                        <div class="logo-icon" style="width: 32px; height: 32px; font-size: 14px;">
                            <?= strtoupper(substr($cust['name'], 0, 1)); ?>
                        </div>
                        <strong style="color: var(--dark);"><?= htmlspecialchars($cust['name']); ?></strong>
                    </td>
                    <td><?= htmlspecialchars($cust['email']); ?></td>
                    <td>
                        <span class="badge" style="position: static; background: #6366f1;"><?= $cust['order_count']; ?> Orders</span>
                    </td>
                    <td style="font-size: 12px; color: var(--text-muted);"><?= date('M d, Y', strtotime($cust['created_at'])); ?></td>
                    <td style="text-align: right;">
                        <a href="<?= base_url('admin/customers.php?delete=' . $cust['id']); ?>" style="color: var(--danger); font-size: 13px; font-weight: 600;" onclick="return confirm('Delete this customer?');">Delete</a>
                    </td>
                </tr>
            <?php endforeach; ?>
        </tbody>
    </table>
</div>

<?php require_once __DIR__ . '/includes/admin_footer.php'; ?>
