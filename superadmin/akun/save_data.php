<?php
require_once __DIR__ . '/../../includes/config.php';
require_once __DIR__ . '/../../includes/session.php';
require_once __DIR__ . '/../../includes/functions.php';

checkAuth();
checkRole(['super_admin']);

header('Content-Type: application/json');

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    echo json_encode(['success' => false, 'message' => 'Method tidak diizinkan']);
    exit();
}

$user = getCurrentUser();
$conn = getConnection();

// ============================================================
// FUNGSI PENCATATAN LOG AKTIVITAS (Sesuai Struktur Database)
// ============================================================
if (!function_exists('catatLog')) {
    function catatLog($conn, $user, $aktivitas) {
        $ip_address = $_SERVER['REMOTE_ADDR'] ?? null;
        $user_agent = $_SERVER['HTTP_USER_AGENT'] ?? null;
        $nama = isset($user['nama']) ? $user['nama'] : $user['username'];
        
        $stmt = $conn->prepare("INSERT INTO log_aktivitas (user_id, username, role, nama, aktivitas, ip_address, user_agent) VALUES (?, ?, ?, ?, ?, ?, ?)");
        $stmt->bind_param("issssss", $user['id'], $user['username'], $user['role'], $nama, $aktivitas, $ip_address, $user_agent);
        $stmt->execute();
        $stmt->close();
    }
}

$action = isset($_POST['action']) ? $_POST['action'] : '';
$nama = trim($_POST['nama'] ?? '');
$username = trim($_POST['username'] ?? '');
$password = trim($_POST['password'] ?? '');
$no_hp = trim($_POST['no_hp'] ?? '');
$role = $_POST['role'] ?? '';
$bpk_id = isset($_POST['bpk_id']) && !empty($_POST['bpk_id']) ? intval($_POST['bpk_id']) : null;

// Validasi
if (empty($nama) || empty($username) || empty($role)) {
    echo json_encode(['success' => false, 'message' => 'Nama, Username, dan Role wajib diisi!']);
    exit();
}

if ($action == 'tambah') {
    // Validasi username unik
    $cek = $conn->query("SELECT id FROM users WHERE username = '$username'");
    if ($cek->num_rows > 0) {
        echo json_encode(['success' => false, 'message' => 'Username sudah digunakan!']);
        exit();
    }
    
    if (empty($password)) {
        echo json_encode(['success' => false, 'message' => 'Password wajib diisi!']);
        exit();
    }
    
    if (strlen($password) < 6) {
        echo json_encode(['success' => false, 'message' => 'Password minimal 6 karakter!']);
        exit();
    }
    
    $hashed = password_hash($password, PASSWORD_DEFAULT);
    
    $stmt = $conn->prepare("INSERT INTO users (nama, username, password, no_hp, role, bpk_id) VALUES (?, ?, ?, ?, ?, ?)");
    $stmt->bind_param("sssssi", $nama, $username, $hashed, $no_hp, $role, $bpk_id);
    
    if ($stmt->execute()) {
        catatLog($conn, $user, "Menambahkan akun baru: " . $username . " (" . str_replace('_', ' ', strtoupper($role)) . ")");
        echo json_encode(['success' => true, 'message' => 'Akun berhasil ditambahkan!']);
    } else {
        echo json_encode(['success' => false, 'message' => 'Gagal menambahkan akun: ' . $stmt->error]);
    }
    $stmt->close();
    
} elseif ($action == 'edit') {
    $id = intval($_POST['id'] ?? 0);
    if ($id <= 0) {
        echo json_encode(['success' => false, 'message' => 'ID tidak valid']);
        exit();
    }
    
    // Validasi username unik (kecuali dirinya sendiri)
    $cek = $conn->query("SELECT id FROM users WHERE username = '$username' AND id != $id");
    if ($cek->num_rows > 0) {
        echo json_encode(['success' => false, 'message' => 'Username sudah digunakan oleh akun lain!']);
        exit();
    }
    
    // Cek role admin_bpk harus punya bpk_id
    if ($role == 'admin_bpk' && empty($bpk_id)) {
        echo json_encode(['success' => false, 'message' => 'Admin BPK harus memilih organisasi BPK-nya!']);
        exit();
    }
    
    if (!empty($password)) {
        if (strlen($password) < 6) {
            echo json_encode(['success' => false, 'message' => 'Password minimal 6 karakter!']);
            exit();
        }
        $hashed = password_hash($password, PASSWORD_DEFAULT);
        $stmt = $conn->prepare("UPDATE users SET nama = ?, username = ?, password = ?, no_hp = ?, role = ?, bpk_id = ? WHERE id = ?");
        $stmt->bind_param("sssssii", $nama, $username, $hashed, $no_hp, $role, $bpk_id, $id);
    } else {
        $stmt = $conn->prepare("UPDATE users SET nama = ?, username = ?, no_hp = ?, role = ?, bpk_id = ? WHERE id = ?");
        $stmt->bind_param("ssssii", $nama, $username, $no_hp, $role, $bpk_id, $id);
    }
    
    if ($stmt->execute()) {
        catatLog($conn, $user, "Memperbarui data akun: " . $username);
        echo json_encode(['success' => true, 'message' => 'Akun berhasil diupdate!']);
    } else {
        echo json_encode(['success' => false, 'message' => 'Gagal mengupdate akun: ' . $stmt->error]);
    }
    $stmt->close();
    
} else {
    echo json_encode(['success' => false, 'message' => 'Aksi tidak valid']);
}

$conn->close();
?>