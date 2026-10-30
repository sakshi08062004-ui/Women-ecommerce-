<?php
require_once __DIR__ . '/../config/db.php';
require_admin();

if (isset($_GET['delete'])) {
    $id = (int)$_GET['delete'];
    $stmt = $pdo->prepare("DELETE FROM feedback WHERE id = :id");
    $stmt->execute([':id' => $id]);
    set_flash('success', 'Feedback entry deleted.');
    header('Location: ' . base_url('admin/feedback.php'));
    exit;
}

$pageTitle = "Customer Feedback";
$headerTitle = "Customer Feedback Inbox";
$headerDesc = "Messages, styling questions, and suggestions submitted by buyers";

$stmt = $pdo->query("SELECT * FROM feedback ORDER BY id DESC");
$feedbacks = $stmt->fetchAll();

require_once __DIR__ . '/includes/admin_header.php';
?>

<div class="table-wrapper">
    <table class="table">
        <thead>
            <tr>
                <th>Sender</th>
                <th>Subject</th>
                <th>Message</th>
                <th>Received</th>
                <th style="text-align: right;">Action</th>
            </tr>
        </thead>
        <tbody>
            <?php if (empty($feedbacks)): ?>
                <tr>
                    <td colspan="5" style="text-align: center; padding: 30px; color: var(--text-muted);">No feedback records found.</td>
                </tr>
            <?php else: ?>
                <?php foreach ($feedbacks as $fb): ?>
                    <tr>
                        <td>
                            <strong style="color: var(--dark); display: block;"><?= htmlspecialchars($fb['name']); ?></strong>
                            <span style="font-size: 11px; color: var(--text-muted);"><?= htmlspecialchars($fb['email']); ?></span>
                        </td>
                        <td style="font-weight: 700; color: var(--primary);"><?= htmlspecialchars($fb['subject']); ?></td>
                        <td style="max-width: 380px; font-size: 13px; color: var(--text-muted);"><?= nl2br(htmlspecialchars($fb['message'])); ?></td>
                        <td style="font-size: 12px; color: var(--text-muted);"><?= date('M d, Y h:i A', strtotime($fb['created_at'])); ?></td>
                        <td style="text-align: right;">
                            <a href="<?= base_url('admin/feedback.php?delete=' . $fb['id']); ?>" style="color: var(--danger); font-size: 13px; font-weight: 600;" onclick="return confirm('Delete this feedback?');">Delete</a>
                        </td>
                    </tr>
                <?php endforeach; ?>
            <?php endif; ?>
        </tbody>
    </table>
</div>

<?php require_once __DIR__ . '/includes/admin_footer.php'; ?>
