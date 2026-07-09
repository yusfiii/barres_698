<?php
// includes/config.php

// ============================================================
// SESSION MANAGEMENT
// ============================================================

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// ============================================================
// KONFIGURASI
// ============================================================

define('BASE_URL', 'http://localhost/barres_698/');
define('DB_HOST', 'localhost');
define('DB_USER', 'root');
define('DB_PASS', '');
define('DB_NAME', 'barres698_db');

// Timezone
date_default_timezone_set('Asia/Makassar');

// Error reporting
error_reporting(E_ALL);
ini_set('display_errors', 1);
ini_set('log_errors', 1);

// ============================================================
// KONEKSI DATABASE
// ============================================================

function getConnection() {
    $conn = new mysqli(DB_HOST, DB_USER, DB_PASS, DB_NAME);
    if ($conn->connect_error) {
        die("Connection failed: " . $conn->connect_error);
    }
    $conn->set_charset("utf8mb4");
    return $conn;
}

// ============================================================
// FUNGSI AUTHENTIKASI
// ============================================================

function checkAuth() {
    if (!isLoggedIn()) {
        header('Location: ' . BASE_URL . 'login.php');
        exit;
    }
}

function checkRole($roles) {
    if (!isLoggedIn()) {
        header('Location: ' . BASE_URL . 'login.php');
        exit;
    }
    
    $userRole = $_SESSION['role'] ?? $_SESSION['user']['role'] ?? '';
    
    if (!in_array($userRole, (array)$roles)) {
        die('Access denied. You do not have permission to access this page.');
    }
}

function isLoggedIn() {
    return isset($_SESSION['user_id']) || isset($_SESSION['user']);
}

function getCurrentUser() {
    if (isset($_SESSION['user'])) {
        return $_SESSION['user'];
    }
    
    if (isset($_SESSION['user_id'])) {
        $conn = getConnection();
        $stmt = $conn->prepare("SELECT * FROM users WHERE id = ?");
        $stmt->bind_param("i", $_SESSION['user_id']);
        $stmt->execute();
        $result = $stmt->get_result();
        $user = $result->fetch_assoc();
        $stmt->close();
        $conn->close();
        return $user;
    }
    
    return null;
}

// ============================================================
// FUNGSI LOGGING - HANYA DI SINI (CORE)
// ============================================================

/**
 * Mencatat aktivitas pengguna ke database
 */
function logAktivitas($aktivitas, $user_id = null) {
    if ($user_id === null && isset($_SESSION['user_id'])) {
        $user_id = $_SESSION['user_id'];
    }
    
    if (!$user_id) {
        return false;
    }
    
    $conn = getConnection();
    
    $stmt = $conn->prepare("SELECT username, role, nama FROM users WHERE id = ?");
    $stmt->bind_param("i", $user_id);
    $stmt->execute();
    $result = $stmt->get_result();
    $user = $result->fetch_assoc();
    $stmt->close();
    
    if (!$user) {
        $conn->close();
        return false;
    }
    
    $check = $conn->query("SHOW TABLES LIKE 'log_aktivitas'");
    if ($check->num_rows == 0) {
        $conn->close();
        return false;
    }
    
    $ip_address = $_SERVER['REMOTE_ADDR'] ?? null;
    $user_agent = $_SERVER['HTTP_USER_AGENT'] ?? null;
    
    $stmt = $conn->prepare("INSERT INTO log_aktivitas (user_id, username, role, nama, aktivitas, ip_address, user_agent) VALUES (?, ?, ?, ?, ?, ?, ?)");
    $stmt->bind_param("issssss", $user_id, $user['username'], $user['role'], $user['nama'], $aktivitas, $ip_address, $user_agent);
    
    $result = $stmt->execute();
    $stmt->close();
    $conn->close();
    
    return $result;
}

/**
 * Fungsi wrapper untuk logging dengan auto detect user
 */
function logActivity($aktivitas) {
    if (isset($_SESSION['user_id'])) {
        return logAktivitas($aktivitas, $_SESSION['user_id']);
    }
    return false;
}

/**
 * Mendapatkan riwayat log aktivitas
 */
function getLogAktivitas($limit = 100, $offset = 0, $role = null, $user_id = null, $start_date = null, $end_date = null) {
    $conn = getConnection();
    
    $check = $conn->query("SHOW TABLES LIKE 'log_aktivitas'");
    if ($check->num_rows == 0) {
        $conn->close();
        return [];
    }
    
    $query = "SELECT l.* FROM log_aktivitas l WHERE 1=1";
    $params = [];
    $types = "";
    
    if ($role) {
        $query .= " AND l.role = ?";
        $params[] = $role;
        $types .= "s";
    }
    
    if ($user_id) {
        $query .= " AND l.user_id = ?";
        $params[] = $user_id;
        $types .= "i";
    }
    
    if ($start_date) {
        $query .= " AND DATE(l.created_at) >= ?";
        $params[] = $start_date;
        $types .= "s";
    }
    
    if ($end_date) {
        $query .= " AND DATE(l.created_at) <= ?";
        $params[] = $end_date;
        $types .= "s";
    }
    
    $query .= " ORDER BY l.created_at DESC LIMIT ? OFFSET ?";
    $params[] = $limit;
    $params[] = $offset;
    $types .= "ii";
    
    $stmt = $conn->prepare($query);
    if (!empty($params)) {
        $stmt->bind_param($types, ...$params);
    }
    $stmt->execute();
    $result = $stmt->get_result();
    
    $data = [];
    while ($row = $result->fetch_assoc()) {
        $data[] = $row;
    }
    
    $stmt->close();
    $conn->close();
    
    return $data;
}

/**
 * Menghitung total log aktivitas
 */
function countLogAktivitas($role = null, $user_id = null, $start_date = null, $end_date = null) {
    $conn = getConnection();
    
    $check = $conn->query("SHOW TABLES LIKE 'log_aktivitas'");
    if ($check->num_rows == 0) {
        $conn->close();
        return 0;
    }
    
    $query = "SELECT COUNT(*) as total FROM log_aktivitas l WHERE 1=1";
    $params = [];
    $types = "";
    
    if ($role) {
        $query .= " AND l.role = ?";
        $params[] = $role;
        $types .= "s";
    }
    
    if ($user_id) {
        $query .= " AND l.user_id = ?";
        $params[] = $user_id;
        $types .= "i";
    }
    
    if ($start_date) {
        $query .= " AND DATE(l.created_at) >= ?";
        $params[] = $start_date;
        $types .= "s";
    }
    
    if ($end_date) {
        $query .= " AND DATE(l.created_at) <= ?";
        $params[] = $end_date;
        $types .= "s";
    }
    
    $stmt = $conn->prepare($query);
    if (!empty($params)) {
        $stmt->bind_param($types, ...$params);
    }
    $stmt->execute();
    $result = $stmt->get_result();
    $total = $result->fetch_assoc()['total'] ?? 0;
    
    $stmt->close();
    $conn->close();
    
    return $total;
}

// ============================================================
// FUNGSI KDE / HEATMAP
// ============================================================

function getHeatmapSettings() {
    $conn = getConnection();
    $result = $conn->query("SELECT * FROM heatmap_settings ORDER BY id DESC LIMIT 1");
    
    if ($result && $result->num_rows > 0) {
        $data = $result->fetch_assoc();
        $conn->close();
        return $data;
    }
    
    $conn->close();
    return [
        'radius' => 25, 
        'blur' => 15, 
        'intensity' => 70
    ];
}

// ============================================================
// FUNGSI UTILITY
// ============================================================

function generateNomorSurat($jenis = 'BARRES698', $bulan = null, $tahun = null) {
    if (!$bulan) $bulan = date('m');
    if (!$tahun) $tahun = date('Y');
    
    $conn = getConnection();
    $result = $conn->query("SELECT COUNT(*) as total FROM kejadian_kebakaran WHERE MONTH(waktu) = $bulan AND YEAR(waktu) = $tahun");
    $count = $result->fetch_assoc()['total'] + 1;
    $conn->close();
    
    return sprintf("%03d", $count) . '/' . $jenis . '/' . $bulan . '/' . $tahun;
}

function formatTanggal($date, $format = 'd F Y H:i') {
    $months = [
        1 => 'Januari', 2 => 'Februari', 3 => 'Maret', 
        4 => 'April', 5 => 'Mei', 6 => 'Juni',
        7 => 'Juli', 8 => 'Agustus', 9 => 'September',
        10 => 'Oktober', 11 => 'November', 12 => 'Desember'
    ];
    
    $timestamp = strtotime($date);
    $day = date('d', $timestamp);
    $month = (int)date('m', $timestamp);
    $year = date('Y', $timestamp);
    $time = date('H:i', $timestamp);
    
    return $day . ' ' . $months[$month] . ' ' . $year . ' ' . $time;
}

// ============================================================
// LOAD LOG HELPER (Fungsi Tambahan)
// ============================================================

if (file_exists(__DIR__ . '/log_helper.php')) {
    require_once __DIR__ . '/log_helper.php';
}
?>