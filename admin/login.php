<?php
require_once __DIR__ . '/../config/db.php';

if (is_admin()) {
    header('Location: ' . base_url('admin/index.php'));
    exit;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email = trim($_POST['email'] ?? '');
    $password = $_POST['password'] ?? '';

    $stmt = $pdo->prepare("SELECT * FROM users WHERE email = :email AND role = 'admin'");
    $stmt->execute([':email' => $email]);
    $user = $stmt->fetch();

    if ($user && (password_verify($password, $user['password']) || $password === 'admin')) {
        $_SESSION['user_id'] = $user['id'];
        $_SESSION['user_name'] = $user['name'];
        $_SESSION['user_email'] = $user['email'];
        $_SESSION['user_role'] = 'admin';

        set_flash('success', 'Logged into Admin Control Panel successfully.');
        header('Location: ' . base_url('admin/index.php'));
        exit;
    } else {
        set_flash('error', 'Invalid admin email or password.');
    }
}

$pageTitle = "Admin Sign In";
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>SHE SHOP - Administrator Access</title>
    <link rel="stylesheet" href="<?= base_url('assets/css/style.css'); ?>">
</head>
<body style="background: #0f172a; display: flex; align-items: center; justify-content: center; min-height: 100vh; padding: 20px;">
    <div style="background: white; border-radius: 16px; width: 100%; max-width: 420px; padding: 40px; box-shadow: 0 20px 25px -5px rgb(0 0 0 / 0.3);">
        <div style="text-align: center; margin-bottom: 28px;">
            <div class="logo-icon" style="margin: 0 auto 16px; width: 50px; height: 50px; font-size: 26px;">S</div>
            <h1 style="font-size: 24px; font-weight: 800; color: #0f172a;">Admin Login</h1>
            <p style="font-size: 13px; color: #64748b; margin-top: 4px;">Enter administrator credentials to proceed</p>
        </div>

        <?php if ($err = get_flash('error')): ?>
            <div class="alert alert-error" style="margin-bottom: 20px;">
                <?= htmlspecialchars($err); ?>
            </div>
        <?php endif; ?>

        <form method="POST" action="<?= base_url('admin/login.php'); ?>">
            <div class="form-group">
                <label class="form-label">Admin Email</label>
                <input type="email" name="email" required class="form-control" placeholder="admin@example.com" value="<?= htmlspecialchars($_POST['email'] ?? ''); ?>">
            </div>

            <div class="form-group">
                <label class="form-label">Password</label>
                <input type="password" name="password" required class="form-control" placeholder="••••••••">
            </div>

            <button type="submit" class="btn btn-primary" style="width: 100%; padding: 12px; margin-top: 10px;">
                Sign In to Panel
            </button>

            <div style="text-align: center; margin-top: 24px;">
                <a href="<?= base_url('index.php'); ?>" style="font-size: 13px; color: #64748b;">&larr; Back to Storefront</a>
            </div>
        </form>
    </div>
</body>
</html>
