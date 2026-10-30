<?php
require_once __DIR__ . '/../config/db.php';
require_admin();

$pageTitle = "Sales Analytics";
$headerTitle = "Sales Analytics & Reports";
$headerDesc = "Visual reporting of revenue, orders distribution, and monthly performance";

// Monthly Sales Query (MySQL DATE_FORMAT)
$monthlyStmt = $pdo->query("SELECT DATE_FORMAT(created_at, '%Y-%m') as month, COUNT(*) as total_orders, SUM(total_price) as total_revenue 
                            FROM orders 
                            GROUP BY month 
                            ORDER BY month DESC LIMIT 12");
$monthlySales = $monthlyStmt->fetchAll();

// Order Status Distribution
$statusStmt = $pdo->query("SELECT status, COUNT(*) as count FROM orders GROUP BY status");
$statusBreakdown = $statusStmt->fetchAll(PDO::FETCH_KEY_PAIR);

require_once __DIR__ . '/includes/admin_header.php';
?>

<div style="display: grid; grid-template-columns: 1fr 1fr; gap: 30px; margin-bottom: 36px;">
    <!-- Revenue Bar Chart -->
    <div style="background: white; padding: 24px; border-radius: var(--radius); border: 1px solid var(--border); box-shadow: var(--shadow-sm);">
        <h3 style="font-size: 16px; font-weight: 800; color: var(--dark); margin-bottom: 16px;">Monthly Revenue (₹)</h3>
        <canvas id="revChart" height="200"></canvas>
    </div>

    <!-- Status Doughnut Chart -->
    <div style="background: white; padding: 24px; border-radius: var(--radius); border: 1px solid var(--border); box-shadow: var(--shadow-sm);">
        <h3 style="font-size: 16px; font-weight: 800; color: var(--dark); margin-bottom: 16px;">Order Status Distribution</h3>
        <div style="max-width: 260px; margin: 0 auto;">
            <canvas id="pieChart" height="200"></canvas>
        </div>
    </div>
</div>

<!-- Monthly Performance Table -->
<div class="table-wrapper">
    <div style="padding: 18px 24px; border-bottom: 1px solid var(--border);">
        <h3 style="font-size: 16px; font-weight: 800; color: var(--dark);">Monthly Financial Overview</h3>
    </div>
    <table class="table">
        <thead>
            <tr>
                <th>Billing Month</th>
                <th>Orders Processed</th>
                <th>Total Revenue Generated</th>
            </tr>
        </thead>
        <tbody>
            <?php if (empty($monthlySales)): ?>
                <tr>
                    <td colspan="3" style="text-align: center; padding: 30px; color: var(--text-muted);">No orders recorded yet.</td>
                </tr>
            <?php else: ?>
                <?php foreach ($monthlySales as $row): ?>
                    <tr>
                        <td style="font-weight: 700; color: var(--dark);"><?= htmlspecialchars($row['month']); ?></td>
                        <td><?= $row['total_orders']; ?> orders</td>
                        <td style="font-weight: 800; color: var(--primary);"><?= format_price($row['total_revenue']); ?></td>
                    </tr>
                <?php endforeach; ?>
            <?php endif; ?>
        </tbody>
    </table>
</div>

<script>
    const mData = <?= json_encode(array_reverse($monthlySales)); ?>;
    const sData = <?= json_encode($statusBreakdown); ?>;

    // Bar Chart
    new Chart(document.getElementById('revChart'), {
        type: 'bar',
        data: {
            labels: mData.map(d => d.month),
            datasets: [{
                label: 'Revenue (₹)',
                data: mData.map(d => d.total_revenue),
                backgroundColor: 'rgba(219, 39, 119, 0.75)',
                borderColor: '#db2777',
                borderWidth: 1,
                borderRadius: 6
            }]
        },
        options: {
            responsive: true,
            scales: { y: { beginAtZero: true } }
        }
    });

    // Pie Chart
    new Chart(document.getElementById('pieChart'), {
        type: 'doughnut',
        data: {
            labels: Object.keys(sData).map(k => k.charAt(0).toUpperCase() + k.slice(1)),
            datasets: [{
                data: Object.values(sData),
                backgroundColor: ['#f59e0b', '#3b82f6', '#10b981', '#ef4444']
            }]
        },
        options: { responsive: true }
    });
</script>

<?php require_once __DIR__ . '/includes/admin_footer.php'; ?>
