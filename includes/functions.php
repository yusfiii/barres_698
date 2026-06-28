<?php
// includes/functions.php
require_once __DIR__ . '/config.php';

// ============================================================
// SANITASI & VALIDASI
// ============================================================

/**
 * SANITASI - Hanya di sini (hapus dari config.php)
 */
function sanitize($data) {
    if (is_array($data)) {
        return array_map('sanitize', $data);
    }
    $data = trim($data);
    $data = stripslashes($data);
    $data = htmlspecialchars($data, ENT_QUOTES, 'UTF-8');
    return $data;
}

/**
 * Validasi email
 */
function validateEmail($email) {
    return filter_var($email, FILTER_VALIDATE_EMAIL);
}

/**
 * Validasi nomor HP Indonesia
 */
function validatePhoneNumber($phone) {
    $phone = preg_replace('/[^0-9]/', '', $phone);
    return preg_match('/^(08|8)[0-9]{8,12}$/', $phone);
}

// ============================================================
// SESSION & FLASH MESSAGE
// ============================================================

/**
 * Cek login
 */
// function isLoggedIn() {
//     return isset($_SESSION['user_id']);
// }

/**
 * Cek role
 */
// function checkAuth() {
//     if (!isLoggedIn()) {
//         header('Location: ../login.php');
//         exit();
//     }
// }

// function checkRole($roles) {
//     if (!is_array($roles)) {
//         $roles = [$roles];
//     }
//     if (!in_array($_SESSION['role'], $roles)) {
//         header('Location: ../unauthorized.php');
//         exit();
//     }
// }

/**
 * Get current user
 */
// function getCurrentUser() {
//     if (!isLoggedIn()) {
//         return null;
//     }
//     $conn = getConnection();
//     $stmt = $conn->prepare("SELECT * FROM users WHERE id = ?");
//     $stmt->bind_param("i", $_SESSION['user_id']);
//     $stmt->execute();
//     $result = $stmt->get_result();
//     $user = $result->fetch_assoc();
//     $stmt->close();
//     $conn->close();
//     return $user;
// }

/**
 * Flash Message
 */
function setFlashMessage($type, $message) {
    $_SESSION['flash'] = [
        'type' => $type,
        'message' => $message
    ];
}

function getFlashMessage() {
    if (isset($_SESSION['flash'])) {
        $flash = $_SESSION['flash'];
        unset($_SESSION['flash']);
        return $flash;
    }
    return null;
}

function displayFlashMessage() {
    $flash = getFlashMessage();
    if ($flash) {
        $type = $flash['type'];
        $message = $flash['message'];
        $class = $type === 'success' ? 'alert-success' : ($type === 'error' ? 'alert-danger' : 'alert-info');
        echo "<div class='alert $class alert-dismissible fade show' role='alert'>
                $message
                <button type='button' class='btn-close' data-bs-dismiss='alert'></button>
              </div>";
    }
}

// ============================================================
// FILE UPLOAD
// ============================================================

/**
 * Upload File
 */
function uploadFile($file, $target_dir = "assets/img/uploads/") {
    // Buat direktori jika belum ada
    if (!is_dir($target_dir)) {
        mkdir($target_dir, 0777, true);
    }
    
    $target_file = $target_dir . basename($file["name"]);
    $imageFileType = strtolower(pathinfo($target_file, PATHINFO_EXTENSION));
    
    // Check if image file is actual image
    $check = getimagesize($file["tmp_name"]);
    if($check === false) {
        return ['success' => false, 'message' => 'File bukan gambar'];
    }
    
    // Check file size (max 5MB)
    if ($file["size"] > 5000000) {
        return ['success' => false, 'message' => 'Ukuran file terlalu besar (maks 5MB)'];
    }
    
    // Allow certain file formats
    $allowed = ['jpg', 'jpeg', 'png', 'gif', 'webp'];
    if (!in_array($imageFileType, $allowed)) {
        return ['success' => false, 'message' => 'Hanya file ' . implode(', ', $allowed) . ' yang diperbolehkan'];
    }
    
    // Generate unique filename
    $new_filename = date('Ymd_His') . '_' . uniqid() . '.' . $imageFileType;
    $target_file = $target_dir . $new_filename;
    
    if (move_uploaded_file($file["tmp_name"], $target_file)) {
        return ['success' => true, 'filename' => $new_filename, 'path' => $target_file];
    } else {
        return ['success' => false, 'message' => 'Gagal mengupload file'];
    }
}

/**
 * Delete file
 */
function deleteFile($filename, $target_dir = "assets/img/uploads/") {
    $filepath = $target_dir . $filename;
    if (file_exists($filepath)) {
        return unlink($filepath);
    }
    return false;
}

// ============================================================
// GEOCODING (Nominatim - OpenStreetMap)
// ============================================================

/**
 * Geocoding - Ubah alamat menjadi koordinat menggunakan Nominatim (OpenStreetMap)
 * @param string $address Alamat lengkap
 * @return array|null ['lat' => float, 'lng' => float, 'display_name' => string] atau null jika gagal
 */
function geocodeAddress($address) {
    $url = 'https://nominatim.openstreetmap.org/search?format=json&q=' . urlencode($address . ', Banjarbaru, Kalimantan Selatan, Indonesia') . '&limit=1&accept-language=id';
    
    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, $url);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_USERAGENT, 'BARRES698/1.0 (admin@barres698.com)');
    curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
    curl_setopt($ch, CURLOPT_TIMEOUT, 10);
    $response = curl_exec($ch);
    curl_close($ch);
    
    $data = json_decode($response, true);
    
    if (!empty($data) && isset($data[0]['lat']) && isset($data[0]['lon'])) {
        return [
            'lat' => (float)$data[0]['lat'],
            'lng' => (float)$data[0]['lon'],
            'display_name' => $data[0]['display_name'] ?? ''
        ];
    }
    
    return null;
}

/**
 * Reverse Geocoding - Ubah koordinat menjadi alamat
 * @param float $lat Latitude
 * @param float $lng Longitude
 * @return string|null Alamat lengkap atau null jika gagal
 */
function reverseGeocode($lat, $lng) {
    $url = 'https://nominatim.openstreetmap.org/reverse?format=json&lat=' . $lat . '&lon=' . $lng . '&accept-language=id&zoom=18';
    
    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, $url);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_USERAGENT, 'BARRES698/1.0 (admin@barres698.com)');
    curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
    curl_setopt($ch, CURLOPT_TIMEOUT, 10);
    $response = curl_exec($ch);
    curl_close($ch);
    
    $data = json_decode($response, true);
    
    if (!empty($data) && isset($data['display_name'])) {
        return $data['display_name'];
    }
    
    return null;
}

/**
 * Konversi koordinat dari format DMS ke Decimal Degrees
 * @param string $dms Koordinat dalam format DMS (contoh: "S 03° 27' 21\"" atau "03° 27' 21\" S")
 * @return float|null Koordinat dalam Decimal Degrees
 */
function dmsToDecimal($dms) {
    // Hapus spasi berlebih
    $dms = trim($dms);
    
    // Deteksi arah (N/S/E/W)
    $direction = null;
    if (preg_match('/[NSEW]/i', $dms, $matches)) {
        $direction = strtoupper($matches[0]);
        $dms = str_replace($matches[0], '', $dms);
    }
    
    // Cari pola derajat, menit, detik
    preg_match('/(\d+)°\s*(\d+)\'\s*(\d+\.?\d*)(?:"|′|″)?/', $dms, $matches);
    
    if (count($matches) >= 4) {
        $degrees = (int)$matches[1];
        $minutes = (int)$matches[2];
        $seconds = (float)$matches[3];
        
        // Konversi ke decimal
        $decimal = $degrees + ($minutes / 60) + ($seconds / 3600);
        
        // Negatif untuk Selatan (S) atau Barat (W)
        if ($direction == 'S' || $direction == 'W') {
            $decimal = -$decimal;
        }
        
        return round($decimal, 8);
    }
    
    return null;
}

/**
 * Konversi koordinat dari Decimal Degrees ke DMS (untuk tampilan)
 * @param float $decimal Koordinat dalam Decimal Degrees
 * @param string $type 'lat' atau 'lng'
 * @return string Format DMS
 */
function decimalToDms($decimal, $type = 'lat') {
    $abs = abs($decimal);
    $degrees = floor($abs);
    $minutes = floor(($abs - $degrees) * 60);
    $seconds = round(($abs - $degrees - $minutes/60) * 3600, 0);
    
    $direction = '';
    if ($type == 'lat') {
        $direction = $decimal >= 0 ? 'N' : 'S';
    } else {
        $direction = $decimal >= 0 ? 'E' : 'W';
    }
    
    return sprintf("%02d° %02d' %02d\" %s", $degrees, $minutes, $seconds, $direction);
}

// ============================================================
// HEATMAP SETTINGS
// ============================================================

/**
 * Get heatmap settings
 */
// function getHeatmapSettings() {
//     $conn = getConnection();
//     $result = $conn->query("SELECT * FROM heatmap_settings ORDER BY id DESC LIMIT 1");
//     $settings = $result->fetch_assoc();
//     $conn->close();
    
//     if (!$settings) {
//         return [
//             'radius' => 25,
//             'blur' => 15,
//             'intensity' => 70
//         ];
//     }
    
//     return $settings;
// }

// ============================================================
// STATISTICS
// ============================================================

/**
 * Get Statistics
 */
function getStatistics() {
    $conn = getConnection();
    
    $stats = [];
    
    // Total kejadian
    $result = $conn->query("SELECT COUNT(*) as total FROM kejadian_kebakaran");
    $stats['total_kejadian'] = $result->fetch_assoc()['total'] ?? 0;
    
    // Total korban
    $result = $conn->query("SELECT SUM(korban_luka) as luka, SUM(korban_jiwa) as jiwa FROM kejadian_kebakaran");
    $korban = $result->fetch_assoc();
    $stats['total_luka'] = $korban['luka'] ?? 0;
    $stats['total_jiwa'] = $korban['jiwa'] ?? 0;
    
    // Total bangunan
    $result = $conn->query("SELECT SUM(jumlah_bangunan) as total FROM kejadian_kebakaran");
    $stats['total_bangunan'] = $result->fetch_assoc()['total'] ?? 0;
    
    // Total KK
    $result = $conn->query("SELECT SUM(jumlah_KK) as total FROM kejadian_kebakaran");
    $stats['total_kk'] = $result->fetch_assoc()['total'] ?? 0;
    
    // Total individu
    $result = $conn->query("SELECT SUM(jumlah_individu) as total FROM kejadian_kebakaran");
    $stats['total_individu'] = $result->fetch_assoc()['total'] ?? 0;
    
    // Statistik per kecamatan
    $result = $conn->query("
        SELECT kecamatan, COUNT(*) as total 
        FROM kejadian_kebakaran 
        WHERE kecamatan IS NOT NULL 
        GROUP BY kecamatan 
        ORDER BY total DESC
    ");
    $stats['per_kecamatan'] = [];
    while($row = $result->fetch_assoc()) {
        $stats['per_kecamatan'][] = $row;
    }
    
    // Data bulanan (12 bulan terakhir)
    $result = $conn->query("
        SELECT DATE_FORMAT(waktu, '%Y-%m') as bulan, COUNT(*) as total 
        FROM kejadian_kebakaran 
        WHERE waktu >= DATE_SUB(NOW(), INTERVAL 12 MONTH)
        GROUP BY DATE_FORMAT(waktu, '%Y-%m')
        ORDER BY bulan
    ");
    $stats['bulanan'] = [];
    while($row = $result->fetch_assoc()) {
        $stats['bulanan'][] = $row;
    }
    
    $conn->close();
    return $stats;
}

/**
 * Get statistik per kecamatan untuk periode tertentu
 */
function getKecamatanStats($bulan = null) {
    $conn = getConnection();
    
    $query = "
        SELECT 
            kecamatan,
            COUNT(*) as total,
            COALESCE(SUM(jumlah_bangunan), 0) as bangunan,
            COALESCE(SUM(korban_luka), 0) as luka,
            COALESCE(SUM(korban_jiwa), 0) as jiwa
        FROM kejadian_kebakaran 
        WHERE 1=1
    ";
    
    if ($bulan) {
        $query .= " AND DATE_FORMAT(waktu, '%Y-%m') = '" . $conn->real_escape_string($bulan) . "'";
    }
    
    $query .= " GROUP BY kecamatan ORDER BY total DESC";
    
    $result = $conn->query($query);
    $data = [];
    while ($row = $result->fetch_assoc()) {
        $data[] = $row;
    }
    $conn->close();
    return $data;
}

// ============================================================
// FORMAT TANGGAL & WAKTU
// ============================================================

/**
 * Format tanggal Indonesia
 */
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

/**
 * Format tanggal untuk laporan
 */
function formatTanggalLaporan($date) {
    return date('d F Y', strtotime($date));
}

/**
 * Format waktu untuk laporan
 */
function formatWaktuLaporan($date) {
    return date('H:i', strtotime($date));
}

/**
 * Get array bulan dalam bahasa Indonesia
 */
function getBulanIndonesia() {
    return [
        1 => 'Januari', 2 => 'Februari', 3 => 'Maret',
        4 => 'April', 5 => 'Mei', 6 => 'Juni',
        7 => 'Juli', 8 => 'Agustus', 9 => 'September',
        10 => 'Oktober', 11 => 'November', 12 => 'Desember'
    ];
}

// ============================================================
// SLUG & STRING HELPER
// ============================================================

/**
 * Generate slug dari string
 */
function createSlug($string) {
    $string = strtolower($string);
    $string = preg_replace('/[^a-z0-9-]/', '-', $string);
    $string = preg_replace('/-+/', '-', $string);
    return trim($string, '-');
}

/**
 * Truncate text dengan batas karakter
 */
function truncateText($text, $limit = 100, $suffix = '...') {
    if (strlen($text) <= $limit) {
        return $text;
    }
    return substr($text, 0, $limit) . $suffix;
}

// ============================================================
// DROPDOWN & SELECT
// ============================================================

/**
 * Get data untuk dropdown
 */
function getDropdownData($table, $valueField, $textField, $where = '') {
    $conn = getConnection();
    $sql = "SELECT $valueField, $textField FROM $table";
    if ($where) {
        $sql .= " WHERE $where";
    }
    $sql .= " ORDER BY $textField";
    
    $result = $conn->query($sql);
    $data = [];
    while ($row = $result->fetch_assoc()) {
        $data[] = $row;
    }
    $conn->close();
    return $data;
}

/**
 * Get kecamatan list
 */
function getKecamatanList() {
    return [
        'Banjarbaru Utara',
        'Banjarbaru Selatan',
        'Cempaka',
        'Landasan Ulin',
        'Liang Anggang'
    ];
}

/**
 * Get kelurahan by kecamatan
 */
function getKelurahanByKecamatan($kecamatan) {
    $data = [
        'Banjarbaru Utara' => ['Loktabat Utara', 'Mentaos', 'Sungai Ulin', 'Guntung Manggis', 'Guntung Payung', 'Komet'],
        'Banjarbaru Selatan' => ['Sungai Besar', 'Loktabat Selatan', 'Guntung Paikat', 'Kemuning'],
        'Cempaka' => ['Cempaka', 'Palam', 'Bangkal', 'Sungai Tiung'],
        'Landasan Ulin' => ['Landasan Ulin Timur', 'Landasan Ulin Barat', 'Syamsudin Noor'],
        'Liang Anggang' => ['Liang Anggang', 'Landasan Ulin Tengah', 'Pangeran', 'Basirih', 'Landasan Ulin Selatan']
    ];
    
    return $data[$kecamatan] ?? [];
}

// ============================================================
// NUMBER FORMATTING
// ============================================================

/**
 * Format angka ke rupiah
 */
function formatRupiah($number) {
    return 'Rp ' . number_format($number, 0, ',', '.');
}

/**
 * Format angka dengan pemisah ribuan
 */
function formatNumber($number) {
    return number_format($number, 0, ',', '.');
}

/**
 * Format persentase
 */
function formatPersen($value, $total) {
    if ($total == 0) return '0%';
    return round(($value / $total) * 100) . '%';
}

// ============================================================
// FILE UPLOAD HELPER
// ============================================================

/**
 * Upload foto dengan nama unik
 */
function uploadFoto($file, $folder = 'foto/', $maxSize = 5) {
    $allowed = ['jpg', 'jpeg', 'png', 'gif', 'webp'];
    $maxSizeBytes = $maxSize * 1024 * 1024;
    
    // Cek ekstensi
    $ext = strtolower(pathinfo($file['name'], PATHINFO_EXTENSION));
    if (!in_array($ext, $allowed)) {
        return ['success' => false, 'message' => 'Format file tidak didukung. Gunakan: ' . implode(', ', $allowed)];
    }
    
    // Cek ukuran
    if ($file['size'] > $maxSizeBytes) {
        return ['success' => false, 'message' => 'Ukuran file terlalu besar. Maksimal ' . $maxSize . 'MB'];
    }
    
    // Buat nama unik
    $filename = date('Ymd_His') . '_' . uniqid() . '.' . $ext;
    $path = 'assets/img/uploads/' . $folder;
    
    if (!is_dir($path)) {
        mkdir($path, 0777, true);
    }
    
    if (move_uploaded_file($file['tmp_name'], $path . $filename)) {
        return ['success' => true, 'filename' => $filename];
    }
    
    return ['success' => false, 'message' => 'Gagal mengupload file'];
}

// ============================================================
// LAPORAN HELPER
// ============================================================

/**
 * Generate nomor surat otomatis
 */
function generateNomorSurat($jenis = 'BARRES698', $bulan = null, $tahun = null) {
    if (!$bulan) $bulan = date('m');
    if (!$tahun) $tahun = date('Y');
    
    $conn = getConnection();
    $result = $conn->query("SELECT COUNT(*) as total FROM kejadian_kebakaran WHERE MONTH(waktu) = $bulan AND YEAR(waktu) = $tahun");
    $count = $result->fetch_assoc()['total'] + 1;
    $conn->close();
    
    return sprintf("%03d", $count) . '/' . $jenis . '/' . $bulan . '/' . $tahun;
}

/**
 * Get data untuk laporan kejadian
 */
function getLaporanKejadian($bulan = null, $kecamatan = null) {
    $conn = getConnection();
    
    $query = "SELECT * FROM kejadian_kebakaran WHERE 1=1";
    
    if ($bulan) {
        $query .= " AND DATE_FORMAT(waktu, '%Y-%m') = '" . $conn->real_escape_string($bulan) . "'";
    }
    if ($kecamatan) {
        $query .= " AND kecamatan = '" . $conn->real_escape_string($kecamatan) . "'";
    }
    
    $query .= " ORDER BY waktu DESC";
    
    $result = $conn->query($query);
    $data = [];
    while ($row = $result->fetch_assoc()) {
        $data[] = $row;
    }
    $conn->close();
    return $data;
}

// ============================================================
// SIDEBAR HELPER
// ============================================================

/**
 * Get menu aktif berdasarkan halaman
 */
function isActiveMenu($menu, $currentPage) {
    if ($menu == $currentPage) return 'active';
    return '';
}

/**
 * Check if user has permission
 */
// function hasPermission($permission) {
//     // Implementasi permission jika diperlukan
//     return true;
// }

// ============================================================
// SECURITY
// ============================================================

/**
 * Generate CSRF token
 */
function generateCSRFToken() {
    if (empty($_SESSION['csrf_token'])) {
        $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
    }
    return $_SESSION['csrf_token'];
}

/**
 * Verify CSRF token
 */
function verifyCSRFToken($token) {
    if (empty($_SESSION['csrf_token']) || $token !== $_SESSION['csrf_token']) {
        return false;
    }
    return true;
}

/**
 * Hash password dengan lebih aman
 */
function hashPassword($password) {
    return password_hash($password, PASSWORD_DEFAULT);
}

/**
 * Verify password
 */
function verifyPassword($password, $hash) {
    return password_verify($password, $hash);
}

// ============================================================
// DEBUG (Hanya untuk development)
// ============================================================

/**
 * Debug function (hanya untuk development)
 */
function debug($data, $die = false) {
    echo '<pre style="background: #f4f4f4; padding: 15px; border-radius: 8px; margin: 10px 0; border: 1px solid #ddd; font-size: 13px; max-height: 500px; overflow: auto;">';
    print_r($data);
    echo '</pre>';
    if ($die) die();
}

// ============================================================
// CURL HELPER (Alternatif jika curl tidak tersedia)
// ============================================================

/**
 * HTTP Request menggunakan file_get_contents (fallback jika curl tidak tersedia)
 */
function httpRequest($url) {
    if (function_exists('curl_init')) {
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_USERAGENT, 'BARRES698/1.0');
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
        curl_setopt($ch, CURLOPT_TIMEOUT, 10);
        $response = curl_exec($ch);
        curl_close($ch);
        return $response;
    } else {
        $context = stream_context_create([
            'http' => [
                'header' => "User-Agent: BARRES698/1.0\r\n",
                'timeout' => 10
            ]
        ]);
        return file_get_contents($url, false, $context);
    }
}

// ============================================================
// PAGINATION HELPER
// ============================================================

/**
 * Generate pagination links
 */
function paginate($total, $perPage = 10, $currentPage = 1, $baseUrl = '') {
    $totalPages = ceil($total / $perPage);
    $links = [];
    
    if ($totalPages <= 1) return '';
    
    $links[] = '<ul class="pagination">';
    
    // Previous
    if ($currentPage > 1) {
        $links[] = '<li class="page-item"><a class="page-link" href="' . $baseUrl . '&page=' . ($currentPage - 1) . '">«</a></li>';
    }
    
    // Pages
    for ($i = 1; $i <= $totalPages; $i++) {
        $active = ($i == $currentPage) ? 'active' : '';
        $links[] = '<li class="page-item ' . $active . '"><a class="page-link" href="' . $baseUrl . '&page=' . $i . '">' . $i . '</a></li>';
    }
    
    // Next
    if ($currentPage < $totalPages) {
        $links[] = '<li class="page-item"><a class="page-link" href="' . $baseUrl . '&page=' . ($currentPage + 1) . '">»</a></li>';
    }
    
    $links[] = '</ul>';
    
    return implode("\n", $links);
}

/**
 * Get Kecamatan list with total kejadian untuk dashboard
 */
function getKecamatanStatsDashboard() {
    $conn = getConnection();
    $result = $conn->query("
        SELECT kecamatan, COUNT(*) as total 
        FROM kejadian_kebakaran 
        WHERE kecamatan IS NOT NULL 
        GROUP BY kecamatan 
        ORDER BY total DESC
        LIMIT 5
    ");
    $data = [];
    while ($row = $result->fetch_assoc()) {
        $data[] = $row;
    }
    $conn->close();
    return $data;
}

/**
 * Get bulan list dengan total kejadian untuk chart
 */
function getBulanStats($tahun = null) {
    if (!$tahun) $tahun = date('Y');
    
    $conn = getConnection();
    $result = $conn->query("
        SELECT 
            DATE_FORMAT(waktu, '%m') as bulan,
            DATE_FORMAT(waktu, '%M') as nama_bulan,
            COUNT(*) as total
        FROM kejadian_kebakaran 
        WHERE YEAR(waktu) = $tahun
        GROUP BY DATE_FORMAT(waktu, '%m')
        ORDER BY bulan
    ");
    
    $data = [];
    while ($row = $result->fetch_assoc()) {
        $data[] = $row;
    }
    $conn->close();
    return $data;
}

?>