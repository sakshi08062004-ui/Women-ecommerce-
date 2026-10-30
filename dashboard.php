<?php
require_once __DIR__ . '/config/db.php';
require_login();

$userId = $_SESSION['user_id'];
$user = current_user();

// Fetch orders with order_items and product
$orderStmt = $pdo->prepare("SELECT o.*, COUNT(oi.id) as item_count 
                            FROM orders o 
                            LEFT JOIN order_items oi ON o.id = oi.order_id 
                            WHERE o.user_id = :uid 
                            GROUP BY o.id 
                            ORDER BY o.id DESC");
$orderStmt->execute([':uid' => $userId]);
$orders = $orderStmt->fetchAll();

// Wishlist count
$wStmt = $pdo->prepare("SELECT COUNT(*) FROM wishlists WHERE user_id = :uid");
$wStmt->execute([':uid' => $userId]);
$wishlistCount = $wStmt->fetchColumn();

$pageTitle = "My Account Dashboard";
require_once __DIR__ . '/includes/header.php';
?>

<div style="margin-bottom: 30px;">
    <h1 style="font-size: 32px; font-weight: 800; color: var(--dark);">Welcome back, <?= htmlspecialchars($user['name']); ?>!</h1>
    <p style="font-size: 14px; color: var(--text-muted); margin-top: 4px;">Manage your orders, saved attire, and account credentials.</p>
</div>

<!-- Overview Cards -->
<div class="grid-3" style="margin-bottom: 40px;">
    <div style="background: white; padding: 24px; border-radius: var(--radius); border: 1px solid var(--border); box-shadow: var(--shadow-sm); display:flex; align-items:center; gap:16px;">
        <div style="width: 50px; height: 50px; border-radius: 50%; background: #fdf2f8; color: var(--primary); display:flex; align-items:center; justify-content:center; font-size:24px;">
            🛍️
        </div>
        <div>
            <div style="font-size: 12px; font-weight:700; color: var(--text-muted); text-transform: uppercase;">Lifetime Orders</div>
            <div style="font-size: 26px; font-weight:800; color: var(--dark);"><?= count($orders); ?></div>
        </div>
    </div>

    <div style="background: white; padding: 24px; border-radius: var(--radius); border: 1px solid var(--border); box-shadow: var(--shadow-sm); display:flex; align-items:center; gap:16px;">
        <div style="width: 50px; height: 50px; border-radius: 50%; background: #fdf2f8; color: var(--primary); display:flex; align-items:center; justify-content:center; font-size:24px;">
            ❤️
        </div>
        <div>
            <div style="font-size: 12px; font-weight:700; color: var(--text-muted); text-transform: uppercase;">Saved to Wishlist</div>
            <div style="font-size: 26px; font-weight:800; color: var(--dark);"><?= $wishlistCount; ?> items</div>
        </div>
    </div>

    <div style="background: white; padding: 24px; border-radius: var(--radius); border: 1px solid var(--border); box-shadow: var(--shadow-sm); display:flex; align-items:center; gap:16px;">
        <div style="width: 50px; height: 50px; border-radius: 50%; background: #f1f5f9; color: var(--dark-muted); display:flex; align-items:center; justify-content:center; font-size:24px;">
            👤
        </div>
        <div>
            <div style="font-size: 12px; font-weight:700; color: var(--text-muted); text-transform: uppercase;">Customer Profile</div>
            <div style="font-size: 14px; font-weight:700; color: var(--dark);"><?= htmlspecialchars($user['email']); ?></div>
        </div>
    </div>
</div>

<!-- Order History -->
<div class="table-wrapper">
    <div style="padding: 20px 24px; border-bottom: 1px solid var(--border); display:flex; justify-content:space-between; align-items:center;">
        <h3 style="font-size: 18px; font-weight: 800; color: var(--dark);">My Order History</h3>
        <a href="<?= base_url('shop.php'); ?>" class="btn btn-outline btn-sm">+ Shop More</a>
    </div>

    <?php if (empty($orders)): ?>
        <div style="padding: 40px; text-align: center; color: var(--text-muted); font-size: 14px;">
            You have not placed any orders yet. <a href="<?= base_url('shop.php'); ?>" style="color:var(--primary); font-weight:700;">Start exploring now!</a>
        </div>
    <?php else: ?>
        <table class="table">
            <thead>
                <tr>
                    <th>Order #</th>
                    <th>Placed Date</th>
                    <th>Total Price</th>
                    <th>Status</th>
                    <th>Payment</th>
                    <th>Shipping Address</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($orders as $order): ?>
                    <tr>
                        <td style="font-weight: 700; color: var(--dark);">#<?= $order['id']; ?></td>
                        <td><?= date('M d, Y h:i A', strtotime($order['created_at'])); ?></td>
                        <td style="font-weight: 800; color: var(--primary);"><?= format_price($order['total_price']); ?></td>
                        <td>
                            <span class="status-pill status-<?= htmlspecialchars($order['status']); ?>">
                                <?= ucfirst($order['status']); ?>
                            </span>
                        </td>
                        <td>
                            <span style="font-size: 12px; font-weight:600; text-transform:uppercase;">
                                <?= htmlspecialchars($order['payment_method']); ?> (<?= ucfirst($order['payment_status']); ?>)
                            </span>
                        </td>
                        <td style="max-width: 250px; font-size: 13px; color: var(--text-muted);"><?= htmlspecialchars($order['shipping_address']); ?></td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    <?php endif; ?>
</div>

<?php require_once __DIR__ . '/includes/footer.php'; ?>
