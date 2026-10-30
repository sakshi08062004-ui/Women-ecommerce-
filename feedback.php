<?php
require_once __DIR__ . '/config/db.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name = trim($_POST['name'] ?? '');
    $email = trim($_POST['email'] ?? '');
    $subject = trim($_POST['subject'] ?? '');
    $message = trim($_POST['message'] ?? '');
    $userId = $_SESSION['user_id'] ?? null;

    if (empty($name) || empty($email) || empty($subject) || empty($message)) {
        set_flash('error', 'Please fill in all required fields.');
    } else {
        $stmt = $pdo->prepare("INSERT INTO feedback (user_id, name, email, subject, message, created_at, updated_at) 
                               VALUES (:uid, :name, :email, :subject, :message, NOW(), NOW())");
        $stmt->execute([
            ':uid' => $userId,
            ':name' => $name,
            ':email' => $email,
            ':subject' => $subject,
            ':message' => $message
        ]);
        set_flash('success', 'Thank you! Your feedback has been received.');
        header('Location: ' . base_url('feedback.php'));
        exit;
    }
}

$pageTitle = "Feedback & Contact";
require_once __DIR__ . '/includes/header.php';
$currUser = current_user();
?>

<div style="max-width: 650px; margin: 0 auto; background: white; padding: 40px; border-radius: var(--radius); border: 1px solid var(--border); box-shadow: var(--shadow-sm);">
    <div style="text-align: center; margin-bottom: 30px;">
        <span style="font-size: 12px; font-weight: 700; color: var(--primary); text-transform: uppercase;">We Value Your Voice</span>
        <h1 style="font-size: 28px; font-weight: 800; color: var(--dark); margin-top: 6px;">Send Us Your Feedback</h1>
        <p style="font-size: 14px; color: var(--text-muted); margin-top: 6px;">Questions regarding tailoring, saree fabrics, or order status? Let us know!</p>
    </div>

    <form method="POST" action="<?= base_url('feedback.php'); ?>">
        <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 16px;">
            <div class="form-group">
                <label class="form-label">Your Name *</label>
                <input type="text" name="name" required class="form-control" value="<?= htmlspecialchars($currUser['name'] ?? ''); ?>">
            </div>

            <div class="form-group">
                <label class="form-label">Email Address *</label>
                <input type="email" name="email" required class="form-control" value="<?= htmlspecialchars($currUser['email'] ?? ''); ?>">
            </div>
        </div>

        <div class="form-group">
            <label class="form-label">Subject *</label>
            <input type="text" name="subject" required class="form-control" placeholder="e.g. Fabric inquiry / Order question">
        </div>

        <div class="form-group">
            <label class="form-label">Message *</label>
            <textarea name="message" rows="5" required class="form-control" placeholder="Write your comments or questions here..."></textarea>
        </div>

        <button type="submit" class="btn btn-primary" style="width: 100%; padding: 13px; font-size: 15px;">
            Submit Feedback
        </button>
    </form>
</div>

<?php require_once __DIR__ . '/includes/footer.php'; ?>
