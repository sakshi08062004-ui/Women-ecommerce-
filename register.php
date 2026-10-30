<?php
require_once __DIR__ . '/config/db.php';

if (is_logged_in()) {
    header('Location: ' . base_url('dashboard.php'));
    exit;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name = trim($_POST['name'] ?? '');
    $email = trim($_POST['email'] ?? '');
    $password = $_POST['password'] ?? '';
    $confirmPassword = $_POST['password_confirmation'] ?? '';

    if (empty($name) || empty($email) || empty($password)) {
        set_flash('error', 'All fields are required.');
    } elseif ($password !== $confirmPassword) {
        set_flash('error', 'Passwords do not match.');
    } else {
        $check = $pdo->prepare("SELECT id FROM users WHERE email = :email");
        $check->execute([':email' => $email]);
        if ($check->fetch()) {
            set_flash('error', 'An account with this email already exists.');
        } else {
            $hashed = password_hash($password, PASSWORD_BCRYPT);
            $stmt = $pdo->prepare("INSERT INTO users (name, email, password, role, created_at, updated_at) 
                                   VALUES (:name, :email, :password, 'customer', NOW(), NOW())");
            $stmt->execute([
                ':name' => $name,
                ':email' => $email,
                ':password' => $hashed
            ]);
            $newId = $pdo->lastInsertId();

            $_SESSION['user_id'] = $newId;
            $_SESSION['user_name'] = $name;
            $_SESSION['user_email'] = $email;
            $_SESSION['user_role'] = 'customer';

            set_flash('success', "Welcome to SHE SHOP, {$name}!");
            header('Location: ' . base_url('dashboard.php'));
            exit;
        }
    }
}

$pageTitle = "Register";
require_once __DIR__ . '/includes/header.php';
?>

<div style="max-width: 460px; margin: 30px auto; background: white; padding: 40px; border-radius: var(--radius); border: 1px solid var(--border); box-shadow: var(--shadow-sm);">
    <div style="text-align: center; margin-bottom: 28px;">
        <h1 style="font-size: 26px; font-weight: 800; color: var(--dark);">Create Customer Account</h1>
        <p style="font-size: 13px; color: var(--text-muted); margin-top: 4px;">Join SHE SHOP to track orders and save your favorites</p>
    </div>

    <form method="POST" action="<?= base_url('register.php'); ?>">
        <div class="form-group">
            <label class="form-label">Full Name</label>
            <input type="text" name="name" required class="form-control" placeholder="Priya Sharma" value="<?= htmlspecialchars($_POST['name'] ?? ''); ?>">
        </div>

        <div class="form-group">
            <label class="form-label">Email Address</label>
            <input type="email" name="email" required class="form-control" placeholder="priya@example.com" value="<?= htmlspecialchars($_POST['email'] ?? ''); ?>">
        </div>

        <div class="form-group">
            <label class="form-label">Password</label>
            <input type="password" name="password" required class="form-control" placeholder="••••••••">
        </div>

        <div class="form-group">
            <label class="form-label">Confirm Password</label>
            <input type="password" name="password_confirmation" required class="form-control" placeholder="••••••••">
        </div>

        <button type="submit" class="btn btn-primary" style="width: 100%; padding: 12px; margin-top: 10px;">
            Create Account
        </button>

        <div style="text-align: center; margin-top: 20px; font-size: 13px; color: var(--text-muted);">
            Already have an account? <a href="<?= base_url('login.php'); ?>" style="color: var(--primary); font-weight: 700;">Sign in</a>
        </div>
    </form>
</div>

<?php require_once __DIR__ . '/includes/footer.php'; ?>
