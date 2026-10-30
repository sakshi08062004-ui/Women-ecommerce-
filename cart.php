<?php
require_once __DIR__ . '/config/db.php';

// Handle Cart Actions
$action = $_GET['action'] ?? $_POST['action'] ?? '';

if ($action === 'add') {
    $productId = (int)($_POST['product_id'] ?? $_GET['id'] ?? 0);
    $qty = max(1, (int)($_POST['quantity'] ?? 1));

    if ($productId > 0) {
        $stmt = $pdo->prepare("SELECT id, name, price, image, stock FROM products WHERE id = :id");
        $stmt->execute([':id' => $productId]);
        $prod = $stmt->fetch();

        if ($prod) {
            if (!isset($_SESSION['cart'])) {
                $_SESSION['cart'] = [];
            }

            if (isset($_SESSION['cart'][$productId])) {
                $_SESSION['cart'][$productId]['quantity'] += $qty;
            } else {
                $_SESSION['cart'][$productId] = [
                    'id' => $prod['id'],
                    'name' => $prod['name'],
                    'price' => (float)$prod['price'],
                    'image' => $prod['image'],
                    'quantity' => $qty
                ];
            }
            set_flash('success', "{$prod['name']} added to your shopping bag!");
        }
    }
    header('Location: ' . base_url('cart.php'));
    exit;
}

if ($action === 'update' && $_SERVER['REQUEST_METHOD'] === 'POST') {
    $productId = (int)($_POST['product_id'] ?? 0);
    $qty = (int)($_POST['quantity'] ?? 1);

    if (isset($_SESSION['cart'][$productId])) {
        if ($qty > 0) {
            $_SESSION['cart'][$productId]['quantity'] = $qty;
            set_flash('success', 'Bag updated.');
        } else {
            unset($_SESSION['cart'][$productId]);
            set_flash('success', 'Item removed from bag.');
        }
    }
    header('Location: ' . base_url('cart.php'));
    exit;
}

if ($action === 'remove') {
    $productId = (int)($_GET['id'] ?? 0);
    if (isset($_SESSION['cart'][$productId])) {
        unset($_SESSION['cart'][$productId]);
        set_flash('success', 'Item removed from shopping bag.');
    }
    header('Location: ' . base_url('cart.php'));
    exit;
}

if ($action === 'clear') {
    unset($_SESSION['cart']);
    set_flash('success', 'Shopping bag cleared.');
    header('Location: ' . base_url('cart.php'));
    exit;
}

$pageTitle = "Shopping Cart";
require_once __DIR__ . '/includes/header.php';

$cart = $_SESSION['cart'] ?? [];
$subtotal = 0;
foreach ($cart as $item) {
    $subtotal += $item['price'] * $item['quantity'];
}
?>

<div style="margin-bottom: 30px;">
    <h1 style="font-size: 32px; font-weight: 800; color: var(--dark);">Your Shopping Bag</h1>
</div>

<?php if (empty($cart)): ?>
    <div style="background: white; padding: 60px; text-align: center; border-radius: var(--radius); border: 1px solid var(--border); max-width: 600px; margin: 40px auto;">
        <div style="font-size: 48px; margin-bottom: 12px;">🛍️</div>
        <h3 style="font-size: 20px; font-weight: 700; color: var(--dark); margin-bottom: 8px;">Your shopping bag is empty</h3>
        <p style="font-size: 14px; color: var(--text-muted); margin-bottom: 24px;">Explore our designer lehengas and sarees to find the perfect match.</p>
        <a href="<?= base_url('shop.php'); ?>" class="btn btn-primary">Start Shopping</a>
    </div>
<?php else: ?>
    <div style="display: grid; grid-template-columns: 1fr 340px; gap: 36px; align-items: start;">
        <!-- Cart Table -->
        <div class="table-wrapper">
            <table class="table">
                <thead>
                    <tr>
                        <th>Item</th>
                        <th>Price</th>
                        <th>Quantity</th>
                        <th>Total</th>
                        <th></th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($cart as $id => $item): ?>
                        <tr>
                            <td style="display: flex; align-items: center; gap: 16px;">
                                <img src="<?= htmlspecialchars($item['image']); ?>" alt="<?= htmlspecialchars($item['name']); ?>" style="width: 60px; height: 75px; object-fit: cover; border-radius: 8px;" onerror="this.src='https://images.unsplash.com/photo-1610189012906-40742d4a6de6?auto=format&fit=crop&w=200&q=80'">
                                <div>
                                    <a href="<?= base_url('product.php?id=' . $id); ?>" style="font-weight: 700; color: var(--dark); display: block;">
                                        <?= htmlspecialchars($item['name']); ?>
                                    </a>
                                </div>
                            </td>
                            <td style="font-weight: 600;"><?= format_price($item['price']); ?></td>
                            <td>
                                <form method="POST" action="<?= base_url('cart.php?action=update'); ?>" style="display: flex; align-items: center; gap: 6px;">
                                    <input type="hidden" name="product_id" value="<?= $id; ?>">
                                    <input type="number" name="quantity" value="<?= $item['quantity']; ?>" min="1" max="20" class="form-control" style="width: 60px; padding: 6px; text-align: center;" onchange="this.form.submit()">
                                </form>
                            </td>
                            <td style="font-weight: 800; color: var(--primary);">
                                <?= format_price($item['price'] * $item['quantity']); ?>
                            </td>
                            <td style="text-align: right;">
                                <a href="<?= base_url('cart.php?action=remove&id=' . $id); ?>" style="color: var(--danger); font-size: 13px; font-weight: 600;">Remove</a>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>

        <!-- Summary Card -->
        <div style="background: white; padding: 24px; border-radius: var(--radius); border: 1px solid var(--border); box-shadow: var(--shadow-sm); position: sticky; top: 100px;">
            <h3 style="font-size: 18px; font-weight: 800; color: var(--dark); margin-bottom: 20px;">Order Summary</h3>
            
            <div style="display: flex; justify-content: space-between; font-size: 14px; margin-bottom: 12px; color: var(--text-muted);">
                <span>Subtotal</span>
                <span style="font-weight: 600; color: var(--dark);"><?= format_price($subtotal); ?></span>
            </div>

            <div style="display: flex; justify-content: space-between; font-size: 14px; margin-bottom: 20px; color: var(--text-muted);">
                <span>Estimated Shipping</span>
                <span style="font-weight: 700; color: var(--success);">FREE</span>
            </div>

            <div style="display: flex; justify-content: space-between; font-size: 18px; font-weight: 800; color: var(--dark); border-top: 1px solid var(--border); padding-top: 16px; margin-bottom: 24px;">
                <span>Total</span>
                <span style="color: var(--primary);"><?= format_price($subtotal); ?></span>
            </div>

            <a href="<?= base_url('checkout.php'); ?>" class="btn btn-primary" style="width: 100%; padding: 14px; font-size: 15px;">
                Proceed to Checkout
            </a>

            <div style="text-align: center; margin-top: 16px;">
                <a href="<?= base_url('shop.php'); ?>" style="font-size: 12px; color: var(--text-muted);">&larr; Continue Shopping</a>
            </div>
        </div>
    </div>
<?php endif; ?>

<?php require_once __DIR__ . '/includes/footer.php'; ?>
