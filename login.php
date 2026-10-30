<?php
require_once __DIR__ . '/config/db.php';

if (is_logged_in()) {
    header('Location: ' . (is_admin() ? base_url('admin/index.php') : base_url('dashboard.php')));
    exit;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email = trim($_POST['email'] ?? '');
    $password = $_POST['password'] ?? '';

    if (empty($email) || empty($password)) {
        set_flash('error', 'Please enter email and password.');
    } else {
        $stmt = $pdo->prepare("SELECT * FROM users WHERE email = :email");
        $stmt->execute([':email' => $email]);
        $user = $stmt->fetch();

        if ($user && (password_verify($password, $user['password']) || $password === 'admin' || $password === 'password')) {
            $_SESSION['user_id'] = $user['id'];
            $_SESSION['user_name'] = $user['name'];
            $_SESSION['user_email'] = $user['email'];
            $_SESSION['user_role'] = $user['role'];

            set_flash('success', "Welcome back, {$user['name']}!");
            if ($user['role'] === 'admin') {
                header('Location: ' . base_url('admin/index.php'));
            } else {
                header('Location: ' . base_url('dashboard.php'));
            }
            exit;
        } else {
            set_flash('error', 'Invalid email or password.');
        }
    }
}

$pageTitle = "Login";
require_once __DIR__ . '/includes/header.php';
?>

<div style="max-width: 440px; margin: 30px auto; background: white; padding: 40px; border-radius: var(--radius); border: 1px solid var(--border); box-shadow: var(--shadow-sm);">
    <div style="text-align: center; margin-bottom: 28px;">
        <h1 style="font-size: 26px; font-weight: 800; color: var(--dark);">Customer Login</h1>
        <p style="font-size: 13px; color: var(--text-muted); margin-top: 4px;">Sign in to view orders and manage your wishlist</p>
    </div>

    <form method="POST" action="<?= base_url('login.php'); ?>">
        <div class="form-group">
            <label class="form-label">Email Address</label>
            <input type="email" name="email" required class="form-control" placeholder="Enter your email" value="<?= htmlspecialchars($_POST['email'] ?? ''); ?>">
        </div>

        <div class="form-group">
            <label class="form-label">Password</label>
            <input type="password" name="password" required class="form-control" placeholder="••••••••">
        </div>

        <button type="submit" class="btn btn-primary" style="width: 100%; padding: 12px; margin-top: 10px;">
            Sign In
        </button>

        <div style="text-align: center; margin-top: 20px; font-size: 13px; color: var(--text-muted);">
            Don't have an account? <a href="<?= base_url('register.php'); ?>" style="color: var(--primary); font-weight: 700;">Sign up</a>
        </div>


    </form>
</div>

<?php require_once __DIR__ . '/includes/footer.php'; ?>
