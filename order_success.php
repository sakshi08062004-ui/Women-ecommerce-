<?php
require_once __DIR__ . '/config/db.php';
$orderId = (int)($_GET['id'] ?? 0);

$pageTitle = "Order Placed Successfully";
require_once __DIR__ . '/includes/header.php';
?>

<div style="background: white; padding: 60px 30px; text-align: center; border-radius: var(--radius); border: 1px solid var(--border); max-width: 600px; margin: 40px auto; box-shadow: var(--shadow-sm);">
    <div style="font-size: 64px; color: var(--success); margin-bottom: 16px;">🎉</div>
    <h1 style="font-size: 28px; font-weight: 800; color: var(--dark); margin-bottom: 8px;">Order Placed Successfully!</h1>
    <p style="font-size: 14px; color: var(--text-muted); margin-bottom: 24px;">Thank you for shopping with SHE SHOP. Your beautiful attire is being carefully packaged.</p>

    <?php if ($orderId > 0): ?>
        <div style="background: #fdf2f8; padding: 14px; border-radius: 8px; display: inline-block; font-weight: 700; color: var(--primary); font-size: 15px; margin-bottom: 30px;">
            Order Reference: #<?= $orderId; ?>
        </div>
    <?php endif; ?>

    <div>
        <a href="<?= base_url('dashboard.php'); ?>" class="btn btn-primary" style="margin-right: 12px;">View in My Account</a>
        <a href="<?= base_url('shop.php'); ?>" class="btn btn-outline">Continue Shopping</a>
    </div>
</div>

<?php require_once __DIR__ . '/includes/footer.php'; ?>
