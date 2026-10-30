<?php
require_once __DIR__ . '/config/db.php';

session_unset();
session_destroy();

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
set_flash('success', 'You have been logged out.');
header('Location: ' . base_url('login.php'));
exit;
