<?php
// logout.php
require_once __DIR__ . '/includes/config.php';

// Log logout jika ada session
if (isset($_SESSION['user_id'])) {
    logAktivitas('Logout dari sistem', $_SESSION['user_id']);
}

// Hapus semua session
session_unset();
session_destroy();

// Redirect ke halaman login
header('Location: public/login.php');
exit();
?>