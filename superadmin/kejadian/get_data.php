<?php
/**
 * get_data.php - Ambil data kejadian kebakaran berdasarkan ID
 * Digunakan untuk modal edit di halaman Data Kejadian
 */

require_once '../../includes/config.php';
require_once '../../includes/session.php';
require_once '../../includes/functions.php';

// Cek autentikasi
checkAuth();
checkRole(['super_admin']);

// Set header JSON
header('Content-Type: application/json');

// Cek parameter ID
if (!isset($_GET['id']) || empty($_GET['id'])) {
    echo json_encode([
        'success' => false,
        'message' => 'ID tidak ditemukan'
    ]);
    exit();
}

$id = intval($_GET['id']);

if ($id <= 0) {
    echo json_encode([
        'success' => false,
        'message' => 'ID tidak valid'
    ]);
    exit();
}

try {
    $conn = getConnection();
    
    // Ambil data kejadian beserta nama akun penambah & pengedit
    $stmt = $conn->prepare("
        SELECT k.*, 
               u1.username AS dibuat_oleh_nama, 
               u2.username AS diupdate_oleh_nama
        FROM kejadian_kebakaran k
        LEFT JOIN users u1 ON k.dibuat_oleh = u1.id
        LEFT JOIN users u2 ON k.diupdate_oleh = u2.id
        WHERE k.id = ?
    ");
    $stmt->bind_param("i", $id);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($row = $result->fetch_assoc()) {
        // Format data untuk response (Sesuaikan parameter "skala" menjadi "kerusakan")
        $data = [
            'id' => $row['id'],
            'waktu' => $row['waktu'],
            'latitude' => $row['latitude'] ?? '',
            'longitude' => $row['longitude'] ?? '',
            'alamat' => $row['alamat'],
            'kecamatan' => $row['kecamatan'] ?? '',
            'kelurahan' => $row['kelurahan'] ?? '',
            'jumlah_bangunan' => $row['jumlah_bangunan'] ?? 0,
            'jumlah_KK' => $row['jumlah_KK'] ?? 0,
            'jumlah_individu' => $row['jumlah_individu'] ?? 0,
            'korban_luka' => $row['korban_luka'] ?? 0,
            'korban_jiwa' => $row['korban_jiwa'] ?? 0,
            'penyebab' => $row['penyebab'] ?? '',
            'kerusakan' => $row['kerusakan'] ?? '', // Update bagian ini
            'keterangan' => $row['keterangan'] ?? '',
            'foto' => $row['foto'] ?? null,
            'created_at' => $row['created_at'] ?? null,
            'dibuat_oleh' => $row['dibuat_oleh_nama'] ?? '-',
            'diupdate_oleh' => $row['diupdate_oleh_nama'] ?? '-'
        ];
        
        echo json_encode([
            'success' => true,
            'data' => $data
        ]);
    } else {
        echo json_encode([
            'success' => false,
            'message' => 'Data tidak ditemukan'
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