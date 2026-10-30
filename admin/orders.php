<?php
require_once __DIR__ . '/../config/db.php';
require_admin();

// Handle Order Status Update
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['update_order'])) {
    $orderId = (int)$_POST['order_id'];
    $status = $_POST['status'] ?? 'pending';
    $paymentStatus = $_POST['payment_status'] ?? 'unpaid';

    $stmt = $pdo->prepare("UPDATE orders SET status = :status, payment_status = :payment_status, updated_at = NOW() WHERE id = :id");
    $stmt->execute([
        ':status' => $status,
        ':payment_status' => $paymentStatus,
        ':id' => $orderId
    ]);

    set_flash('success', "Order #{$orderId} updated.");
    header('Location: ' . base_url('admin/orders.php'));
    exit;
}

$pageTitle = "Orders Management";
$headerTitle = "Customer Orders";
$headerDesc = "View orders, change dispatch status, and manage payments";

$stmt = $pdo->query("SELECT o.*, u.name as customer_name, u.email as customer_email 
                     FROM orders o 
                     LEFT JOIN users u ON o.user_id = u.id 
                     ORDER BY o.id DESC");
$orders = $stmt->fetchAll();

require_once __DIR__ . '/includes/admin_header.php';
?>

<div class="table-wrapper">
    <table class="table">
        <thead>
            <tr>
                <th>Order</th>
                <th>Customer</th>
                <th>Amount</th>
                <th>Status</th>
                <th>Payment</th>
                <th>Date</th>
                <th>Quick Actions</th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($orders as $order): ?>
                <tr>
                    <td style="font-weight: 700; color: var(--dark);">#<?= $order['id']; ?></td>
                    <td>
                        <strong style="display: block; color: var(--dark);"><?= htmlspecialchars($order['customer_name'] ?? 'Guest'); ?></strong>
                        <span style="font-size: 11px; color: var(--text-muted);"><?= htmlspecialchars($order['customer_email'] ?? ''); ?></span>
                    </td>
                    <td style="font-weight: 800; color: var(--primary);"><?= format_price($order['total_price']); ?></td>
                    <td>
                        <span class="status-pill status-<?= htmlspecialchars($order['status']); ?>">
                            <?= ucfirst($order['status']); ?>
                        </span>
                    </td>
                    <td>
                        <span style="font-size: 11px; font-weight:700; text-transform:uppercase; color: <?= $order['payment_status'] === 'paid' ? 'var(--success)' : 'var(--danger)'; ?>;">
                            <?= htmlspecialchars($order['payment_method']); ?> - <?= ucfirst($order['payment_status']); ?>
                        </span>
                    </td>
                    <td style="font-size: 12px; color: var(--text-muted);"><?= date('M d, Y', strtotime($order['created_at'])); ?></td>
                    <td>
                        <!-- Inline Update Form -->
                        <form method="POST" action="<?= base_url('admin/orders.php'); ?>" style="display: flex; gap: 6px; align-items: center;">
                            <input type="hidden" name="update_order" value="1">
                            <input type="hidden" name="order_id" value="<?= $order['id']; ?>">
                            
                            <select name="status" class="form-control" style="width: auto; padding: 4px 8px; font-size: 12px;">
                                <option value="pending" <?= $order['status'] === 'pending' ? 'selected' : ''; ?>>Pending</option>
                                <option value="processing" <?= $order['status'] === 'processing' ? 'selected' : ''; ?>>Processing</option>
                                <option value="completed" <?= $order['status'] === 'completed' ? 'selected' : ''; ?>>Completed</option>
                                <option value="cancelled" <?= $order['status'] === 'cancelled' ? 'selected' : ''; ?>>Cancelled</option>
                            </select>

                            <select name="payment_status" class="form-control" style="width: auto; padding: 4px 8px; font-size: 12px;">
                                <option value="unpaid" <?= $order['payment_status'] === 'unpaid' ? 'selected' : ''; ?>>Unpaid</option>
                                <option value="paid" <?= $order['payment_status'] === 'paid' ? 'selected' : ''; ?>>Paid</option>
                            </select>

                            <button type="submit" class="btn btn-outline btn-sm" style="padding: 4px 10px;">Save</button>
                        </form>
                    </td>
                </tr>
            <?php endforeach; ?>
        </tbody>
    </table>
</div>

<?php require_once __DIR__ . '/includes/admin_footer.php'; ?>
