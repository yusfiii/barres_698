<?php
/**
 * save_data.php - Simpan data kejadian kebakaran (Tambah & Edit)
 * Menangani form submission dari modal floating
 */

require_once '../../includes/config.php';
require_once '../../includes/session.php';
require_once '../../includes/functions.php';

// Cek autentikasi
checkAuth();
checkRole(['super_admin']);

// Set header JSON
header('Content-Type: application/json');

// Cek method POST
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    echo json_encode([
        'success' => false,
        'message' => 'Method tidak diizinkan'
    ]);
    exit();
}

try {
    $conn = getConnection();
    
    // Ambil data dari POST
    $id = isset($_POST['id']) ? intval($_POST['id']) : 0;
    $waktu = $_POST['waktu'] ?? '';
    $latitude = $_POST['latitude'] ?? '';
    $longitude = $_POST['longitude'] ?? '';
    $alamat = trim($_POST['alamat'] ?? '');
    $kecamatan = $_POST['kecamatan'] ?? '';
    $kelurahan = $_POST['kelurahan'] ?? '';
    $jumlah_bangunan = intval($_POST['jumlah_bangunan'] ?? 0);
    $jumlah_KK = intval($_POST['jumlah_KK'] ?? 0);
    $jumlah_individu = intval($_POST['jumlah_individu'] ?? 0);
    $korban_luka = intval($_POST['korban_luka'] ?? 0);
    $korban_jiwa = intval($_POST['korban_jiwa'] ?? 0);
    $penyebab = $_POST['penyebab'] ?? '';
    $penyebab_lainnya = trim($_POST['penyebab_lainnya'] ?? '');
    $skala = $_POST['skala'] ?? '';
    $keterangan = trim($_POST['keterangan'] ?? '');
    $current_user_id = $_SESSION['user_id']; // Akun yang sedang login (penambah/pengedit)
    
    // Jika penyebab "Lainnya", gunakan nilai dari penyebab_lainnya
    if ($penyebab === 'Lainnya' && !empty($penyebab_lainnya)) {
        $penyebab = $penyebab_lainnya;
    }
    
    // Validasi data wajib
    if (empty($waktu) || empty($alamat) || empty($kecamatan) || empty($kelurahan)) {
        echo json_encode([
            'success' => false,
            'message' => 'Waktu, Alamat, Kecamatan, dan Kelurahan wajib diisi!'
        ]);
        exit();
    }
    
    // Validasi koordinat
    if (empty($latitude) || empty($longitude)) {
        // Coba cari koordinat dari alamat
        $coords = geocodeAddress($alamat);
        if ($coords) {
            $latitude = $coords['lat'];
            $longitude = $coords['lng'];
        } else {
            // Jika tidak ada koordinat, set ke 0
            $latitude = 0;
            $longitude = 0;
        }
    }
    
    // Proses upload foto
    $foto_name = isset($_POST['foto_lama']) && !empty($_POST['foto_lama']) ? $_POST['foto_lama'] : null;
    
    if (isset($_FILES['foto']) && $_FILES['foto']['error'] == 0) {
        $upload = uploadFile($_FILES['foto'], '../../uploads/');
        if ($upload['success']) {
            // Hapus foto lama jika ada
            if ($foto_name && file_exists('../../uploads/' . $foto_name)) {
                unlink('../../uploads/' . $foto_name);
            }
            $foto_name = $upload['filename'];
        }
    }
    
    // ============================================================
    // SIMPAN DATA
    // ============================================================
    if ($id > 0) {
        // EDIT: Update data
        $stmt = $conn->prepare("
            UPDATE kejadian_kebakaran 
            SET waktu = ?, 
                latitude = ?, 
                longitude = ?, 
                alamat = ?, 
                kecamatan = ?, 
                kelurahan = ?, 
                jumlah_bangunan = ?, 
                jumlah_KK = ?, 
                jumlah_individu = ?, 
                korban_luka = ?, 
                korban_jiwa = ?, 
                penyebab = ?, 
                skala = ?, 
                keterangan = ?, 
                foto = ?,
                diupdate_oleh = ?
            WHERE id = ?
        ");
        $stmt->bind_param(
            "ssdsssiiiiissssii",
            $waktu,
            $latitude,
            $longitude,
            $alamat,
            $kecamatan,
            $kelurahan,
            $jumlah_bangunan,
            $jumlah_KK,
            $jumlah_individu,
            $korban_luka,
            $korban_jiwa,
            $penyebab,
            $skala,
            $keterangan,
            $foto_name,
            $current_user_id,
            $id
        );
        
        if ($stmt->execute()) {
            // LOG AKTIVITAS - EDIT
            logAktivitas('Mengedit data kejadian kebakaran ID: ' . $id, $_SESSION['user_id']);
            echo json_encode([
                'success' => true,
                'message' => 'Data kejadian berhasil diupdate!'
            ]);
        } else {
            echo json_encode([
                'success' => false,
                'message' => 'Gagal mengupdate data: ' . $stmt->error
            ]);
        }
        $stmt->close();
        
    } else {
        // TAMBAH: Insert data baru
        $stmt = $conn->prepare("
            INSERT INTO kejadian_kebakaran 
            (waktu, latitude, longitude, alamat, kecamatan, kelurahan, 
             jumlah_bangunan, jumlah_KK, jumlah_individu, korban_luka, korban_jiwa, 
             penyebab, skala, keterangan, foto, dibuat_oleh) 
            VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)
        ");
        $stmt->bind_param(
            "ssdsssiiiiissssi",
            $waktu,
            $latitude,
            $longitude,
            $alamat,
            $kecamatan,
            $kelurahan,
            $jumlah_bangunan,
            $jumlah_KK,
            $jumlah_individu,
            $korban_luka,
            $korban_jiwa,
            $penyebab,
            $skala,
            $keterangan,
            $foto_name,
            $current_user_id
        );
        
        if ($stmt->execute()) {
            $new_id = $conn->insert_id;
            // LOG AKTIVITAS - TAMBAH
            logAktivitas('Menambahkan data kejadian kebakaran ID: ' . $new_id, $_SESSION['user_id']);
            echo json_encode([
                'success' => true,
                'message' => 'Data kejadian berhasil ditambahkan!'
            ]);
        } else {
            echo json_encode([
                'success' => false,
                'message' => 'Gagal menambahkan data: ' . $stmt->error
            ]);
        }
        $stmt->close();
    }
    
    $conn->close();
    
} catch (Exception $e) {
    echo json_encode([
        'success' => false,
        'message' => 'Terjadi kesalahan: ' . $e->getMessage()
    ]);
}
?>