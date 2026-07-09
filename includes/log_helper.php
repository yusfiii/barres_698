<?php
// includes/log_helper.php
// File ini berisi fungsi tambahan untuk logging
// Fungsi dasar logAktivitas() dan logActivity() sudah ada di config.php

// ============================================================
// FUNGSI LOGGING TAMBAHAN
// ============================================================

/**
 * Log aktivitas spesifik untuk CRUD
 * Gunakan logActivity() yang sudah ada di config.php
 */
function logCrud($action, $table, $id = null, $detail = null) {
    $messages = [
        'create' => 'Menambahkan data ' . $table,
        'update' => 'Mengubah data ' . $table,
        'delete' => 'Menghapus data ' . $table,
        'view' => 'Melihat data ' . $table,
    ];
    
    $message = $messages[$action] ?? 'Melakukan aksi ' . $action . ' pada ' . $table;
    if ($id) {
        $message .= ' ID: ' . $id;
    }
    if ($detail) {
        $message .= ' (' . $detail . ')';
    }
    
    // Gunakan logActivity dari config.php
    if (function_exists('logActivity')) {
        return logActivity($message);
    }
    return false;
}

/**
 * Log login user
 */
function logLogin($user_id = null) {
    if ($user_id === null && isset($_SESSION['user_id'])) {
        $user_id = $_SESSION['user_id'];
    }
    if ($user_id) {
        return logAktivitas('Login ke sistem', $user_id);
    }
    return false;
}

/**
 * Log logout user
 */
function logLogout($user_id = null) {
    if ($user_id === null && isset($_SESSION['user_id'])) {
        $user_id = $_SESSION['user_id'];
    }
    if ($user_id) {
        return logAktivitas('Logout dari sistem', $user_id);
    }
    return false;
}

/**
 * Log error
 */
function logError($message, $user_id = null) {
    if ($user_id === null && isset($_SESSION['user_id'])) {
        $user_id = $_SESSION['user_id'];
    }
    if ($user_id) {
        return logAktivitas('ERROR: ' . $message, $user_id);
    }
    return false;
}

/**
 * Membersihkan log lama (lebih dari N hari)
 */
function cleanOldLogs($days = 90) {
    $conn = getConnection();
    
    // Cek apakah tabel log_aktivitas ada
    $check = $conn->query("SHOW TABLES LIKE 'log_aktivitas'");
    if ($check->num_rows == 0) {
        $conn->close();
        return false;
    }
    
    $stmt = $conn->prepare("DELETE FROM log_aktivitas WHERE created_at < DATE_SUB(NOW(), INTERVAL ? DAY)");
    $stmt->bind_param("i", $days);
    $result = $stmt->execute();
    $deleted = $stmt->affected_rows;
    $stmt->close();
    $conn->close();
    
    return ['success' => $result, 'deleted' => $deleted];
}

/**
 * Get statistik log
 */
function getLogStats() {
    $conn = getConnection();
    
    $check = $conn->query("SHOW TABLES LIKE 'log_aktivitas'");
    if ($check->num_rows == 0) {
        $conn->close();
        return [
            'total' => 0,
            'today' => 0,
            'this_month' => 0,
            'by_role' => [],
            'by_user' => []
        ];
    }
    
    $stats = [];
    
    // Total
    $result = $conn->query("SELECT COUNT(*) as total FROM log_aktivitas");
    $stats['total'] = $result->fetch_assoc()['total'] ?? 0;
    
    // Hari ini
    $result = $conn->query("SELECT COUNT(*) as total FROM log_aktivitas WHERE DATE(created_at) = CURDATE()");
    $stats['today'] = $result->fetch_assoc()['total'] ?? 0;
    
    // Bulan ini
    $result = $conn->query("SELECT COUNT(*) as total FROM log_aktivitas WHERE MONTH(created_at) = MONTH(CURDATE()) AND YEAR(created_at) = YEAR(CURDATE())");
    $stats['this_month'] = $result->fetch_assoc()['total'] ?? 0;
    
    // Per role
    $result = $conn->query("SELECT role, COUNT(*) as total FROM log_aktivitas GROUP BY role");
    $stats['by_role'] = [];
    while ($row = $result->fetch_assoc()) {
        $stats['by_role'][$row['role']] = $row['total'];
    }
    
    // Per user (top 10)
    $result = $conn->query("SELECT username, COUNT(*) as total FROM log_aktivitas GROUP BY username ORDER BY total DESC LIMIT 10");
    $stats['by_user'] = [];
    while ($row = $result->fetch_assoc()) {
        $stats['by_user'][] = $row;
    }
    
    $conn->close();
    return $stats;
}
?>