<?php
require_once __DIR__ . '/config/db.php';

$cart = $_SESSION['cart'] ?? [];
if (empty($cart)) {
    set_flash('error', 'Your shopping bag is empty.');
    header('Location: ' . base_url('shop.php'));
    exit;
}

$subtotal = 0;
foreach ($cart as $item) {
    $subtotal += $item['price'] * $item['quantity'];
}

// Handle Order Submission
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $shippingAddress = trim($_POST['shipping_address'] ?? '');
    $paymentMethod = $_POST['payment_method'] ?? 'cod';
    $userId = $_SESSION['user_id'] ?? 2; // Default to Customer User (id: 2) if not logged in

    if (empty($shippingAddress)) {
        set_flash('error', 'Please provide a valid shipping address.');
    } else {
        try {
            $pdo->beginTransaction();

            // Insert into orders table
            $orderStmt = $pdo->prepare("INSERT INTO orders (user_id, total_price, status, payment_method, payment_status, shipping_address, created_at, updated_at) 
                                        VALUES (:user_id, :total_price, 'pending', :payment_method, :payment_status, :shipping_address, NOW(), NOW())");
            $paymentStatus = ($paymentMethod === 'cod') ? 'unpaid' : 'paid';
            $orderStmt->execute([
                ':user_id' => $userId,
                ':total_price' => $subtotal,
                ':payment_method' => $paymentMethod,
                ':payment_status' => $paymentStatus,
                ':shipping_address' => $shippingAddress
            ]);
            $orderId = $pdo->lastInsertId();

            // Insert into order_items table
            $itemStmt = $pdo->prepare("INSERT INTO order_items (order_id, product_id, quantity, price, created_at, updated_at) 
                                       VALUES (:order_id, :product_id, :quantity, :price, NOW(), NOW())");
            foreach ($cart as $item) {
                $itemStmt->execute([
                    ':order_id' => $orderId,
                    ':product_id' => $item['id'],
                    ':quantity' => $item['quantity'],
                    ':price' => $item['price']
                ]);
            }

            $pdo->commit();

            // Clear Cart
            unset($_SESSION['cart']);

            header('Location: ' . base_url('order_success.php?id=' . $orderId));
            exit;

        } catch (Exception $e) {
            $pdo->rollBack();
            set_flash('error', 'Failed to place order: ' . $e->getMessage());
        }
    }
}

$pageTitle = "Checkout";
require_once __DIR__ . '/includes/header.php';
?>

<div style="margin-bottom: 30px;">
    <h1 style="font-size: 32px; font-weight: 800; color: var(--dark);">Checkout</h1>
</div>

<form method="POST" action="<?= base_url('checkout.php'); ?>">
    <div style="display: grid; grid-template-columns: 1fr 380px; gap: 36px; align-items: start;">
        <!-- Left: Form -->
        <div style="background: white; padding: 32px; border-radius: var(--radius); border: 1px solid var(--border); box-shadow: var(--shadow-sm);">
            <h3 style="font-size: 18px; font-weight: 800; color: var(--dark); margin-bottom: 20px;">1. Shipping Information</h3>
            
            <div class="form-group">
                <label class="form-label">Full Delivery Address *</label>
                <textarea name="shipping_address" class="form-control" rows="4" required placeholder="Flat / House No., Apartment, Street, Landmark, City, State, PIN Code"></textarea>
            </div>

            <h3 style="font-size: 18px; font-weight: 800; color: var(--dark); margin: 30px 0 20px;">2. Payment Method</h3>
            
            <div style="display: flex; flex-direction: column; gap: 14px;">
                <label style="border: 1px solid var(--border); border-radius: 8px; padding: 16px; display: flex; align-items: center; gap: 14px; cursor: pointer;">
                    <input type="radio" name="payment_method" value="cod" checked>
                    <div>
                        <strong style="display: block; font-size: 14px; color: var(--dark);">Cash on Delivery (COD)</strong>
                        <span style="font-size: 12px; color: var(--text-muted);">Pay in cash upon doorstep arrival.</span>
                    </div>
                </label>

                <label style="border: 1px solid var(--border); border-radius: 8px; padding: 16px; display: flex; align-items: center; gap: 14px; cursor: pointer;">
                    <input type="radio" name="payment_method" value="card">
                    <div>
                        <strong style="display: block; font-size: 14px; color: var(--dark);">Credit / Debit Card (Online)</strong>
                        <span style="font-size: 12px; color: var(--text-muted);">Instant encrypted checkout.</span>
                    </div>
                </label>
            </div>
        </div>

        <!-- Right: Summary -->
        <div style="background: white; padding: 24px; border-radius: var(--radius); border: 1px solid var(--border); box-shadow: var(--shadow-sm); position: sticky; top: 100px;">
            <h3 style="font-size: 18px; font-weight: 800; color: var(--dark); margin-bottom: 20px;">Review Order</h3>
            
            <div style="max-height: 220px; overflow-y: auto; margin-bottom: 16px;">
                <?php foreach ($cart as $item): ?>
                    <div style="display: flex; justify-content: space-between; font-size: 13px; margin-bottom: 10px;">
                        <span><?= htmlspecialchars($item['name']); ?> &times; <?= $item['quantity']; ?></span>
                        <span style="font-weight: 600;"><?= format_price($item['price'] * $item['quantity']); ?></span>
                    </div>
                <?php endforeach; ?>
            </div>

            <div style="border-top: 1px solid var(--border); padding-top: 16px; margin-bottom: 20px;">
                <div style="display: flex; justify-content: space-between; font-size: 14px; margin-bottom: 8px; color: var(--text-muted);">
                    <span>Subtotal</span>
                    <span style="font-weight: 600; color: var(--dark);"><?= format_price($subtotal); ?></span>
                </div>
                <div style="display: flex; justify-content: space-between; font-size: 14px; margin-bottom: 14px; color: var(--text-muted);">
                    <span>Shipping</span>
                    <span style="font-weight: 700; color: var(--success);">FREE</span>
                </div>
                <div style="display: flex; justify-content: space-between; font-size: 18px; font-weight: 800; color: var(--dark); border-top: 1px solid #f1f5f9; padding-top: 12px;">
                    <span>Total Amount</span>
                    <span style="color: var(--primary);"><?= format_price($subtotal); ?></span>
                </div>
            </div>

            <button type="submit" class="btn btn-primary" style="width: 100%; padding: 14px; font-size: 15px;">
                Place Order
            </button>
        </div>
    </div>
</form>

<?php require_once __DIR__ . '/includes/footer.php'; ?>
