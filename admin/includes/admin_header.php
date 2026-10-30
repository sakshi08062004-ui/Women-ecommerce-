<?php
require_once __DIR__ . '/../../config/db.php';
require_admin();
$adminUser = current_user();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= isset($pageTitle) ? htmlspecialchars($pageTitle) . ' - Admin Control' : 'SHE SHOP - Admin Control Panel'; ?></title>
    <link rel="stylesheet" href="<?= base_url('assets/css/style.css'); ?>">
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
</head>
<body>
    <div class="admin-layout">
        <!-- Admin Sidebar -->
        <aside class="admin-sidebar">
            <div class="admin-brand">
                <div class="logo-icon" style="width:36px; height:36px; font-size:18px;">S</div>
                <div>
                    <span style="font-weight: 800; color: white; display: block; font-size: 16px;">SHE SHOP</span>
                    <span style="font-size: 10px; font-weight: 700; color: var(--primary); text-transform: uppercase;">Admin Control</span>
                </div>
            </div>

            <ul class="admin-menu">
                <li><a href="<?= base_url('admin/index.php'); ?>">📊 Dashboard</a></li>
                <li><a href="<?= base_url('admin/categories.php'); ?>">📁 Categories</a></li>
                <li><a href="<?= base_url('admin/products.php'); ?>">👗 Products Inventory</a></li>
                <li><a href="<?= base_url('admin/orders.php'); ?>">📦 Orders</a></li>
                <li><a href="<?= base_url('admin/customers.php'); ?>">👥 Customers</a></li>
                <li><a href="<?= base_url('admin/analytics.php'); ?>">📈 Sales Analytics</a></li>
                <li><a href="<?= base_url('admin/feedback.php'); ?>">💬 Customer Feedback</a></li>
                
                <li style="margin-top: 30px; border-top: 1px solid #1e293b; padding-top: 16px;">
                    <a href="<?= base_url('index.php'); ?>" target="_blank" style="color: #94a3b8;">🏪 View Storefront &nearr;</a>
                </li>
                <li>
                    <a href="<?= base_url('logout.php'); ?>" style="color: #ef4444;">🚪 Sign Out</a>
                </li>
            </ul>

            <div style="padding: 16px 20px; background: #020617; border-top: 1px solid #1e293b; font-size: 12px;">
                <div style="font-weight: 700; color: white;"><?= htmlspecialchars($adminUser['name']); ?></div>
                <div style="color: #64748b;"><?= htmlspecialchars($adminUser['email']); ?></div>
            </div>
        </aside>

        <!-- Main Content Area -->
        <main class="admin-content">
            <!-- Header Bar -->
            <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 30px; padding-bottom: 20px; border-bottom: 1px solid var(--border);">
                <div>
                    <h1 style="font-size: 26px; font-weight: 800; color: var(--dark);"><?= $headerTitle ?? 'Dashboard'; ?></h1>
                    <p style="font-size: 13px; color: var(--text-muted); margin-top: 4px;"><?= $headerDesc ?? 'Store performance overview & management'; ?></p>
                </div>
                <div>
                    <span class="status-pill status-completed">MySQL Live Connected</span>
                </div>
            </div>

            <!-- Flash Messages -->
            <?php if ($successMsg = get_flash('success')): ?>
                <div class="alert alert-success">
                    <span>✅</span> <?= htmlspecialchars($successMsg); ?>
                </div>
            <?php endif; ?>

            <?php if ($errorMsg = get_flash('error')): ?>
                <div class="alert alert-error">
                    <span>⚠️</span> <?= htmlspecialchars($errorMsg); ?>
                </div>
            <?php endif; ?>
