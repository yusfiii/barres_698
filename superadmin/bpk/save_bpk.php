<?php
/**
 * save_bpk.php - Proses tambah/edit/hapus BPK via AJAX
 * 
 * PERBAIKAN:
 * 1. Fix bind_param: "sssssi" → "ssssi" (6 karakter vs 5 placeholder)
 * 2. Output buffering + shutdown handler untuk memastikan response JSON valid
 * 3. Error reporting dipaksa mati meskipun config.php mengubahnya
 */

// ============================================================
// PAKSA MATIKAN SEMUA ERROR REPORTING DI AWAL
// ============================================================
error_reporting(0);
ini_set('display_errors', 0);
ini_set('log_errors', 1);

// ============================================================
// OUTPUT BUFFERING - Tangkap semua output agar tidak merusak JSON
// ============================================================
ob_start();

// ============================================================
// SHUTDOWN HANDLER - Pastikan response selalu JSON valid
// ============================================================
function shutdownHandler() {
    $error = error_get_last();
    if ($error !== null && in_array($error['type'], [E_ERROR, E_PARSE, E_CORE_ERROR, E_COMPILE_ERROR])) {
        // Bersihkan semua output buffer
        ob_clean();
        // Kirim response JSON error
        header('Content-Type: application/json');
        echo json_encode([
            'success' => false,
            'message' => 'Terjadi kesalahan pada server: ' . $error['message']
        ]);
        exit();
    }
}
register_shutdown_function('shutdownHandler');

// ============================================================
// LOAD KONFIGURASI
// ============================================================
require_once __DIR__ . '/../../includes/config.php';
require_once __DIR__ . '/../../includes/session.php';
require_once __DIR__ . '/../../includes/functions.php';

// PAKSA LAGI - config.php mungkin mengubah error_reporting
error_reporting(0);
ini_set('display_errors', 0);

// Cek autentikasi
checkAuth();
checkRole(['super_admin']);

// Set header JSON
header('Content-Type: application/json');

// Cek method POST
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    echo json_encode(['success' => false, 'message' => 'Method tidak diizinkan']);
    exit();
}

try {
    $conn = getConnection();
    $action = $_POST['action'] ?? '';

    if ($action == 'tambah' || $action == 'edit') {
        $nomor_registrasi = trim($_POST['nomor_registrasi'] ?? '');
        $nama_bpk = trim($_POST['nama_bpk'] ?? '');
        $alamat = trim($_POST['alamat'] ?? '');
        $kecamatan = $_POST['kecamatan'] ?? '';
        $kelurahan = $_POST['kelurahan'] ?? '';
        $latitude = trim($_POST['latitude'] ?? '');
        $longitude = trim($_POST['longitude'] ?? '');
        $tahun_berdiri = intval($_POST['tahun_berdiri'] ?? 0);

        // Validasi tahun berdiri
        if ($tahun_berdiri < 2000 || $tahun_berdiri > 2030) {
            echo json_encode(['success' => false, 'message' => 'Tahun berdiri harus antara 2000 - 2030!']);
            exit();
        }

        if (empty($nomor_registrasi) || empty($nama_bpk)) {
            echo json_encode(['success' => false, 'message' => 'Nomor registrasi dan nama BPK wajib diisi!']);
            exit();
        }

        // Cek nomor registrasi duplikat
        $cek_query = "SELECT id FROM bpk WHERE nomor_registrasi = '$nomor_registrasi'";
        if ($action == 'edit') {
            $edit_id = (int)$_POST['id'];
            $cek_query .= " AND id != $edit_id";
        }
        $cek = $conn->query($cek_query);
        if ($cek && $cek->num_rows > 0) {
            echo json_encode(['success' => false, 'message' => 'Nomor registrasi sudah digunakan!']);
            exit();
        }

        // Upload logo
        $logo_name = $_POST['logo_lama'] ?? null;
        if (isset($_FILES['logo']) && $_FILES['logo']['error'] == 0) {
            $uploadDir = '../../assets/img/uploads/logo/';
            if (!file_exists($uploadDir)) {
                mkdir($uploadDir, 0777, true);
            }
            $ext = pathinfo($_FILES['logo']['name'], PATHINFO_EXTENSION);
            $logo_name = time() . '_' . uniqid() . '.' . $ext;
            if (move_uploaded_file($_FILES['logo']['tmp_name'], $uploadDir . $logo_name)) {
                if ($_POST['logo_lama'] && file_exists($uploadDir . $_POST['logo_lama'])) {
                    unlink($uploadDir . $_POST['logo_lama']);
                }
            }
        }

        if ($action == 'tambah') {
            // Insert BPK
            $stmt = $conn->prepare("INSERT INTO bpk (nomor_registrasi, nama_bpk, alamat, kecamatan, kelurahan, logo, latitude, longitude, tahun_berdiri) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?)");
            $stmt->bind_param("ssssssddi", $nomor_registrasi, $nama_bpk, $alamat, $kecamatan, $kelurahan, $logo_name, $latitude, $longitude, $tahun_berdiri);
            
            if ($stmt->execute()) {
                $bpk_id = $conn->insert_id;
                
                // ============================================================
                // PERBAIKAN UTAMA: Buat akun Admin BPK otomatis
                // ============================================================
                $default_password = 'admin123';
                $hashed_password = password_hash($default_password, PASSWORD_DEFAULT);
                $username_bpk = 'admin_bpk_' . $nomor_registrasi;
                
                // Cek apakah username sudah ada
                $stmt_cek = $conn->prepare("SELECT id FROM users WHERE username = ?");
                $stmt_cek->bind_param("s", $username_bpk);
                $stmt_cek->execute();
                $cek_result = $stmt_cek->get_result();
                $stmt_cek->close();
                
                if ($cek_result->num_rows == 0) {
                    $no_hp_default = '0812345678' . rand(10, 99);
                    
                    // ============================================================
                    // FIX: 5 placeholder, 5 parameter → "ssssi" BUKAN "sssssi"
                    // ============================================================
                    $stmt_user = $conn->prepare("INSERT INTO users (username, nama, password, no_hp, role, bpk_id) VALUES (?, ?, ?, ?, 'admin_bpk', ?)");
                    $stmt_user->bind_param("ssssi", $username_bpk, $nama_bpk, $hashed_password, $no_hp_default, $bpk_id);
                    
                    if ($stmt_user->execute()) {
                        logAktivitas('Menambahkan BPK: ' . $nama_bpk . ' dan akun Admin BPK: ' . $username_bpk, $_SESSION['user_id']);
                        echo json_encode([
                            'success' => true, 
                            'message' => 'BPK berhasil ditambahkan!<br><small>Akun Admin BPK: <strong>' . $username_bpk . '</strong> (Password: <strong>admin123</strong>)</small>'
                        ]);
                    } else {
                        // Jika gagal membuat akun, hapus BPK yang sudah dibuat
                        $conn->query("DELETE FROM bpk WHERE id = $bpk_id");
                        echo json_encode(['success' => false, 'message' => 'Gagal membuat akun Admin BPK: ' . $stmt_user->error]);
                    }
                    $stmt_user->close();
                } else {
                    // Jika username sudah ada, hapus BPK
                    $conn->query("DELETE FROM bpk WHERE id = $bpk_id");
                    echo json_encode(['success' => false, 'message' => 'Username admin BPK sudah digunakan!']);
                }
            } else {
                echo json_encode(['success' => false, 'message' => 'Gagal menyimpan data BPK']);
            }
            $stmt->close();
            
        } else {
            // EDIT: Update data BPK
            $edit_id = (int)$_POST['id'];
            $stmt = $conn->prepare("UPDATE bpk SET nomor_registrasi=?, nama_bpk=?, alamat=?, kecamatan=?, kelurahan=?, logo=?, latitude=?, longitude=?, tahun_berdiri=? WHERE id=?");
            $stmt->bind_param("ssssssddii", $nomor_registrasi, $nama_bpk, $alamat, $kecamatan, $kelurahan, $logo_name, $latitude, $longitude, $tahun_berdiri, $edit_id);
            
            if ($stmt->execute()) {
                // Update username akun admin BPK jika berubah
                $username_bpk = 'admin_bpk_' . $nomor_registrasi;
                $conn->query("UPDATE users SET username = '$username_bpk', nama = '$nama_bpk' WHERE bpk_id = $edit_id AND role = 'admin_bpk'");
                
                logAktivitas('Mengedit data BPK: ' . $nama_bpk, $_SESSION['user_id']);
                echo json_encode(['success' => true, 'message' => 'Data BPK berhasil diupdate!']);
            } else {
                echo json_encode(['success' => false, 'message' => 'Gagal mengupdate data BPK']);
            }
            $stmt->close();
        }
        $conn->close();
        exit();
    }

    if ($action == 'hapus') {
        $hapus_id = (int)$_POST['id'];
        
        $cek = $conn->query("SELECT COUNT(*) as t FROM anggota WHERE bpk_id = $hapus_id")->fetch_assoc();
        if ($cek['t'] > 0) {
            echo json_encode(['success' => false, 'message' => 'BPK memiliki ' . $cek['t'] . ' anggota. Hapus anggota terlebih dahulu!']);
            exit();
        }
        
        // Hapus akun admin BPK terkait
        $conn->query("DELETE FROM users WHERE bpk_id = $hapus_id AND role = 'admin_bpk'");
        
        // Hapus logo
        $logo = $conn->query("SELECT logo FROM bpk WHERE id = $hapus_id")->fetch_assoc();
        if ($logo['logo'] && file_exists('../../assets/img/uploads/logo/' . $logo['logo'])) {
            unlink('../../assets/img/uploads/logo/' . $logo['logo']);
        }

        $stmt = $conn->prepare("DELETE FROM bpk WHERE id = ?");
        $stmt->bind_param("i", $hapus_id);
        if ($stmt->execute()) {
            logAktivitas('Menghapus BPK ID: ' . $hapus_id, $_SESSION['user_id']);
            echo json_encode(['success' => true, 'message' => 'BPK berhasil dihapus!']);
        } else {
            echo json_encode(['success' => false, 'message' => 'Gagal menghapus BPK!']);
        }
        $stmt->close();
        $conn->close();
        exit();
    }
    
    echo json_encode(['success' => false, 'message' => 'Aksi tidak valid']);
    exit();
    
} catch (Exception $e) {
    // Bersihkan buffer dan kirim JSON error
    ob_clean();
    echo json_encode(['success' => false, 'message' => 'Terjadi kesalahan: ' . $e->getMessage()]);
    exit();
}

// Bersihkan buffer jika tidak ada error
ob_end_flush();
?>