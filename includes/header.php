<?php
require_once __DIR__ . '/../config/db.php';
$cartCount = cart_count();
$user = current_user();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= isset($pageTitle) ? htmlspecialchars($pageTitle) . ' - SHE SHOP' : 'SHE SHOP - Women\'s Traditional & Modern Fashion'; ?></title>
    <link rel="stylesheet" href="<?= base_url('assets/css/style.css'); ?>">
</head>
<body>
    <!-- Main Navigation Header -->
    <header class="main-header">
        <div class="nav-container">
            <a href="<?= base_url('index.php'); ?>" class="logo-area">
                <div class="logo-icon">S</div>
                <div>
                    <span class="logo-text">SHE SHOP</span>
                    <span class="logo-tag">Ethnic & Modern</span>
                </div>
            </a>

            <ul class="nav-links">
                <li><a href="<?= base_url('index.php'); ?>">Home</a></li>
                <li><a href="<?= base_url('shop.php'); ?>">Shop All</a></li>
                <li><a href="<?= base_url('shop.php?category=sarees'); ?>">Sarees</a></li>
                <li><a href="<?= base_url('shop.php?category=lehengas'); ?>">Lehengas</a></li>
                <li><a href="<?= base_url('feedback.php'); ?>">Feedback</a></li>
            </ul>

            <div class="nav-actions">
                <?php if ($user): ?>
                    <a href="<?= base_url('wishlist.php'); ?>" class="action-icon" title="Wishlist">
                        ❤️
                    </a>
                <?php endif; ?>

                <a href="<?= base_url('cart.php'); ?>" class="action-icon" title="Shopping Cart">
                    🛍️
                    <?php if ($cartCount > 0): ?>
                        <span class="badge"><?= $cartCount; ?></span>
                    <?php endif; ?>
                </a>

                <?php if ($user): ?>
                    <a href="<?= base_url('dashboard.php'); ?>" class="btn btn-outline btn-sm">My Account</a>
                    <a href="<?= base_url('logout.php'); ?>" class="btn btn-outline btn-sm" style="color:#ef4444; border-color:#ef4444;">Logout</a>
                <?php else: ?>
                    <a href="<?= base_url('login.php'); ?>" class="btn btn-outline btn-sm">Login</a>
                    <a href="<?= base_url('register.php'); ?>" class="btn btn-primary btn-sm">Sign Up</a>
                <?php endif; ?>
            </div>
        </div>
    </header>

    <!-- Global Container Starts -->
    <div class="container">
        <!-- Display Flash Messages -->
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
