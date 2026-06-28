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
    
    // Ambil data kejadian
    $stmt = $conn->prepare("SELECT * FROM kejadian_kebakaran WHERE id = ?");
    $stmt->bind_param("i", $id);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($row = $result->fetch_assoc()) {
        // Format data untuk response
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
            'foto' => $row['foto'] ?? null,
            'created_at' => $row['created_at'] ?? null
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