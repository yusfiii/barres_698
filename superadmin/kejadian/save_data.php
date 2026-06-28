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
            // Jika tidak ada koordinat, set ke default 0
            $latitude = 0;
            $longitude = 0;
        }
    }
    
    // Proses upload foto
    $foto_name = isset($_POST['foto_lama']) ? $_POST['foto_lama'] : null;
    
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
    
    // Simpan data
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
                foto = ? 
            WHERE id = ?
        ");
        $stmt->bind_param(
            "ssdsssiiiiisi",
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
            $foto_name,
            $id
        );
        
        $message = "Data kejadian berhasil diupdate!";
    } else {
        // TAMBAH: Insert data baru
        $stmt = $conn->prepare("
            INSERT INTO kejadian_kebakaran 
            (waktu, latitude, longitude, alamat, kecamatan, kelurahan, 
             jumlah_bangunan, jumlah_KK, jumlah_individu, korban_luka, korban_jiwa, foto) 
            VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)
        ");
        $stmt->bind_param(
            "ssdsssiiiiis",
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
            $foto_name
        );
        
        $message = "Data kejadian berhasil ditambahkan!";
    }
    
    if ($stmt->execute()) {
        echo json_encode([
            'success' => true,
            'message' => $message
        ]);
    } else {
        echo json_encode([
            'success' => false,
            'message' => 'Gagal menyimpan data: ' . $stmt->error
        ]);
    }
    
    $stmt->close();
    $conn->close();
    
} catch (Exception $e) {
    echo json_encode([
        'success' => false,
        'message' => 'Terjadi kesalahan: ' . $e->getMessage()
    ]);
}
?>