<?php
require_once __DIR__ . '/../config/db.php';
require_admin();

$pageTitle = "Dashboard";
$headerTitle = "Store Overview";
$headerDesc = "Key sales indicators, recent orders, and customer activity";

// Metrics
$revenueStmt = $pdo->query("SELECT COALESCE(SUM(total_price), 0) FROM orders WHERE payment_status = 'paid'");
$totalRevenue = (float)$revenueStmt->fetchColumn();

$activeOrdersStmt = $pdo->query("SELECT COUNT(*) FROM orders WHERE status IN ('pending', 'processing')");
$activeOrders = (int)$activeOrdersStmt->fetchColumn();

$productsStmt = $pdo->query("SELECT COUNT(*) FROM products");
$totalProducts = (int)$productsStmt->fetchColumn();

$custStmt = $pdo->query("SELECT COUNT(*) FROM users WHERE role = 'customer'");
$totalCustomers = (int)$custStmt->fetchColumn();

// Recent Orders
$ordersStmt = $pdo->query("SELECT o.*, u.name as customer_name, u.email as customer_email 
                           FROM orders o 
                           LEFT JOIN users u ON o.user_id = u.id 
                           ORDER BY o.id DESC LIMIT 5");
$recentOrders = $ordersStmt->fetchAll();

// Recent Feedback
$fbStmt = $pdo->query("SELECT * FROM feedback ORDER BY id DESC LIMIT 5");
$recentFeedback = $fbStmt->fetchAll();

require_once __DIR__ . '/includes/admin_header.php';
?>

<!-- Metric Cards -->
<div class="grid-4" style="margin-bottom: 36px;">
    <div style="background: white; padding: 24px; border-radius: var(--radius); border: 1px solid var(--border); box-shadow: var(--shadow-sm);">
        <div style="font-size: 12px; font-weight: 700; color: var(--text-muted); text-transform: uppercase;">Total Paid Revenue</div>
        <div style="font-size: 28px; font-weight: 800; color: var(--success); margin-top: 6px;"><?= format_price($totalRevenue); ?></div>
    </div>

    <div style="background: white; padding: 24px; border-radius: var(--radius); border: 1px solid var(--border); box-shadow: var(--shadow-sm);">
        <div style="font-size: 12px; font-weight: 700; color: var(--text-muted); text-transform: uppercase;">Active Orders</div>
        <div style="font-size: 28px; font-weight: 800; color: var(--accent); margin-top: 6px;"><?= $activeOrders; ?></div>
    </div>

    <div style="background: white; padding: 24px; border-radius: var(--radius); border: 1px solid var(--border); box-shadow: var(--shadow-sm);">
        <div style="font-size: 12px; font-weight: 700; color: var(--text-muted); text-transform: uppercase;">Total Products</div>
        <div style="font-size: 28px; font-weight: 800; color: var(--primary); margin-top: 6px;"><?= $totalProducts; ?></div>
    </div>

    <div style="background: white; padding: 24px; border-radius: var(--radius); border: 1px solid var(--border); box-shadow: var(--shadow-sm);">
        <div style="font-size: 12px; font-weight: 700; color: var(--text-muted); text-transform: uppercase;">Registered Customers</div>
        <div style="font-size: 28px; font-weight: 800; color: var(--dark); margin-top: 6px;"><?= $totalCustomers; ?></div>
    </div>
</div>

<div style="display: grid; grid-template-columns: 2fr 1fr; gap: 30px;">
    <!-- Recent Orders -->
    <div class="table-wrapper">
        <div style="padding: 18px 24px; border-bottom: 1px solid var(--border); display: flex; justify-content: space-between; align-items: center;">
            <h3 style="font-size: 16px; font-weight: 800; color: var(--dark);">Recent Orders</h3>
            <a href="<?= base_url('admin/orders.php'); ?>" style="font-size: 12px; font-weight: 700; color: var(--primary);">View All &rarr;</a>
        </div>

        <?php if (empty($recentOrders)): ?>
            <div style="padding: 30px; text-align: center; color: var(--text-muted); font-size: 13px;">No orders yet.</div>
        <?php else: ?>
            <table class="table">
                <thead>
                    <tr>
                        <th>Order</th>
                        <th>Customer</th>
                        <th>Amount</th>
                        <th>Status</th>
                        <th>Date</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($recentOrders as $ro): ?>
                        <tr>
                            <td style="font-weight: 700;">#<?= $ro['id']; ?></td>
                            <td>
                                <strong style="display: block; color: var(--dark);"><?= htmlspecialchars($ro['customer_name'] ?? 'Guest'); ?></strong>
                                <span style="font-size: 11px; color: var(--text-muted);"><?= htmlspecialchars($ro['customer_email'] ?? ''); ?></span>
                            </td>
                            <td style="font-weight: 800; color: var(--primary);"><?= format_price($ro['total_price']); ?></td>
                            <td>
                                <span class="status-pill status-<?= htmlspecialchars($ro['status']); ?>">
                                    <?= ucfirst($ro['status']); ?>
                                </span>
                            </td>
                            <td style="font-size: 12px; color: var(--text-muted);"><?= date('M d, Y', strtotime($ro['created_at'])); ?></td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        <?php endif; ?>
    </div>

    <!-- Recent Feedback -->
    <div style="background: white; border-radius: var(--radius); border: 1px solid var(--border); box-shadow: var(--shadow-sm); padding: 24px;">
        <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 20px;">
            <h3 style="font-size: 16px; font-weight: 800; color: var(--dark);">Customer Feedback</h3>
            <a href="<?= base_url('admin/feedback.php'); ?>" style="font-size: 12px; font-weight: 700; color: var(--primary);">All &rarr;</a>
        </div>

        <?php if (empty($recentFeedback)): ?>
            <div style="padding: 30px; text-align: center; color: var(--text-muted); font-size: 13px;">No feedback received yet.</div>
        <?php else: ?>
            <div style="display: flex; flex-direction: column; gap: 16px;">
                <?php foreach ($recentFeedback as $fb): ?>
                    <div style="border-bottom: 1px solid #f1f5f9; padding-bottom: 12px;">
                        <div style="display: flex; justify-content: space-between; font-size: 13px; font-weight: 700; color: var(--dark);">
                            <span><?= htmlspecialchars($fb['name']); ?></span>
                            <span style="font-size: 11px; font-weight: normal; color: var(--text-muted);"><?= date('M d', strtotime($fb['created_at'])); ?></span>
                        </div>
                        <div style="font-size: 12px; font-weight: 600; color: var(--primary); margin: 2px 0;"><?= htmlspecialchars($fb['subject']); ?></div>
                        <div style="font-size: 12px; color: var(--text-muted);"><?= htmlspecialchars(substr($fb['message'], 0, 70)); ?>...</div>
                    </div>
                <?php endforeach; ?>
            </div>
        <?php endif; ?>
    </div>
</div>

<?php require_once __DIR__ . '/includes/admin_footer.php'; ?>
