<?php
require_once __DIR__ . '/../../includes/config.php';
require_once __DIR__ . '/../../includes/session.php';
require_once __DIR__ . '/../../includes/functions.php';

checkAuth();
checkRole(['super_admin']);

$user = getCurrentUser();
$message = '';
$messageType = '';

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

// Filter parameters
$search = isset($_GET['search']) ? $_GET['search'] : '';
$filter_kecamatan = isset($_GET['kecamatan']) ? $_GET['kecamatan'] : '';
$filter_bulan = isset($_GET['bulan']) ? $_GET['bulan'] : '';

// Get total BPK untuk sidebar
$conn = getConnection();
$total_bpk = $conn->query("SELECT COUNT(*) as total FROM bpk")->fetch_assoc()['total'];

// Include sidebar
include __DIR__ . '/../../includes/sidebar.php';

// ============================================================
// HANDLE DELETE
// ============================================================
if (isset($_GET['delete'])) {
    $id = intval($_GET['delete']);

    // Ambil foto untuk dihapus
    $stmt = $conn->prepare("SELECT foto, alamat FROM kejadian_kebakaran WHERE id = ?");
    $stmt->bind_param("i", $id);
    $stmt->execute();
    $result = $stmt->get_result();
    $kejadian = $result->fetch_assoc();
    $stmt->close();

    if ($kejadian && $kejadian['foto']) {
        $fotoPath = '../../uploads/' . $kejadian['foto'];
        if (file_exists($fotoPath)) {
            unlink($fotoPath);
        }
    }

    // Hapus data
    $stmt = $conn->prepare("DELETE FROM kejadian_kebakaran WHERE id = ?");
    $stmt->bind_param("i", $id);
    
    if ($stmt->execute()) {
        catatLog($conn, $user, "Menghapus data kejadian kebakaran ID: $id (Alamat: " . ($kejadian['alamat'] ?? '') . ")");
        $message = "Data kejadian berhasil dihapus!";
        $messageType = "success";
    } else {
        $message = "Gagal menghapus data kejadian!";
        $messageType = "error";
    }
    $stmt->close();
}

// ============================================================
// HANDLE GEOCODING (Per Data)
// ============================================================
if (isset($_GET['geocode']) && isset($_GET['id'])) {
    $id = intval($_GET['id']);
    
    $stmt = $conn->prepare("SELECT alamat FROM kejadian_kebakaran WHERE id = ?");
    $stmt->bind_param("i", $id);
    $stmt->execute();
    $result = $stmt->get_result();
    $data = $result->fetch_assoc();
    $stmt->close();
    
    if ($data) {
        $coords = geocodeAddress($data['alamat']);
        if ($coords) {
            $stmt = $conn->prepare("UPDATE kejadian_kebakaran SET latitude = ?, longitude = ? WHERE id = ?");
            $stmt->bind_param("ddi", $coords['lat'], $coords['lng'], $id);
            if ($stmt->execute()) {
                catatLog($conn, $user, "Melakukan geocoding satuan pada kejadian ID: $id");
                $message = "Koordinat berhasil diperbarui! (Lat: " . $coords['lat'] . ", Lng: " . $coords['lng'] . ")";
                $messageType = "success";
            } else {
                $message = "Gagal memperbarui koordinat!";
                $messageType = "error";
            }
            $stmt->close();
        } else {
            $message = "Gagal mendapatkan koordinat dari alamat. Pastikan alamat lengkap dan benar!";
            $messageType = "error";
        }
    }
}

// ============================================================
// HANDLE BULK GEOCODING
// ============================================================
if (isset($_GET['bulk_geocode'])) {
    $query = "SELECT id, alamat FROM kejadian_kebakaran WHERE latitude IS NULL OR latitude = 0 OR longitude IS NULL OR longitude = 0";
    $result = $conn->query($query);
    $updated = 0;
    $failed = 0;
    
    while ($row = $result->fetch_assoc()) {
        $coords = geocodeAddress($row['alamat']);
        if ($coords) {
            $stmt = $conn->prepare("UPDATE kejadian_kebakaran SET latitude = ?, longitude = ? WHERE id = ?");
            $stmt->bind_param("ddi", $coords['lat'], $coords['lng'], $row['id']);
            if ($stmt->execute()) {
                $updated++;
            } else {
                $failed++;
            }
            $stmt->close();
        } else {
            $failed++;
        }
    }
    
    catatLog($conn, $user, "Melakukan Geocoding Massal. Berhasil: $updated data, Gagal: $failed data");
    
    $message = "Proses geocoding selesai! Berhasil: $updated, Gagal: $failed";
    $messageType = $updated > 0 ? "success" : "warning";
}

// ============================================================
// GET INCIDENTS DENGAN FILTER
// ============================================================
$query = "SELECT k.*, 
                 u1.username AS nama_penambah, 
                 u2.username AS nama_pengedit
          FROM kejadian_kebakaran k
          LEFT JOIN users u1 ON k.dibuat_oleh = u1.id
          LEFT JOIN users u2 ON k.diupdate_oleh = u2.id
          WHERE 1=1";
$params = [];
$types = "";

if (!empty($search)) {
    $query .= " AND (k.alamat LIKE ? OR k.kecamatan LIKE ? OR k.kelurahan LIKE ?)";
    $searchParam = "%$search%";
    $params[] = $searchParam;
    $params[] = $searchParam;
    $params[] = $searchParam;
    $types .= "sss";
}
if (!empty($filter_kecamatan)) {
    $query .= " AND k.kecamatan = ?";
    $params[] = $filter_kecamatan;
    $types .= "s";
}
if (!empty($filter_bulan)) {
    $query .= " AND DATE_FORMAT(k.waktu, '%Y-%m') = ?";
    $params[] = $filter_bulan;
    $types .= "s";
}

$query .= " ORDER BY k.waktu DESC";

$stmt = $conn->prepare($query);
if (!empty($params)) {
    $stmt->bind_param($types, ...$params);
}
$stmt->execute();
$result = $stmt->get_result();

$incidents = [];
$coordinates = [];
while ($row = $result->fetch_assoc()) {
    $incidents[] = $row;
    if (!empty($row['latitude']) && !empty($row['longitude'])) {
        $coordinates[] = [
            'lat' => (float)$row['latitude'],
            'lng' => (float)$row['longitude'],
            'alamat' => $row['alamat'],
            'waktu' => $row['waktu'],
            'id' => $row['id']
        ];
    }
}
$stmt->close();

// Statistik
$total_kejadian = count($incidents);
$total_luka = array_sum(array_column($incidents, 'korban_luka'));
$total_jiwa = array_sum(array_column($incidents, 'korban_jiwa'));
$total_bangunan = array_sum(array_column($incidents, 'jumlah_bangunan'));
$total_kk = array_sum(array_column($incidents, 'jumlah_KK'));

// Mengambil jumlah kejadian BULAN INI dari database (sebelum filter jika Anda ingin bulan ini secara global, atau berdasarkan hasil query)
$kejadian_bulan_ini = $conn->query("SELECT COUNT(*) as t FROM kejadian_kebakaran WHERE DATE_FORMAT(waktu, '%Y-%m') = DATE_FORMAT(NOW(), '%Y-%m')")->fetch_assoc()['t'];

// Data untuk dropdown kelurahan
$kelurahan_data = [
    'Banjarbaru Selatan' => ['Guntung Paikat', 'Kemuning', 'Loktabat Selatan', 'Sungai Besar'],
    'Banjarbaru Utara' => ['Komet', 'Loktabat Utara', 'Mentaos', 'Sungai Ulin'],
    'Cempaka' => ['Bangkal', 'Cempaka', 'Palam', 'Sungai Tiung'],
    'Landasan Ulin' => ['Guntung Manggis', 'Guntung Payung', 'Landasan Ulin Timur', 'Syamsudin Noor'],
    'Liang Anggang' => ['Landasan Ulin Barat', 'Landasan Ulin Selatan', 'Landasan Ulin Tengah', 'Landasan Ulin Utara']
];

// List kecamatan untuk filter
$kecamatan_list = $conn->query("SELECT DISTINCT kecamatan FROM kejadian_kebakaran WHERE kecamatan IS NOT NULL ORDER BY kecamatan");

$conn->close();
?>

<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Data Kejadian - Super Admin BARRES 698</title>

    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <link rel="stylesheet" href="https://cdn.datatables.net/1.13.4/css/dataTables.bootstrap5.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
    
    <!-- SweetAlert2 CSS -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/sweetalert2@11/dist/sweetalert2.min.css">
    
    <!-- Leaflet CSS & MarkerCluster -->
    <link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css" />
    <link rel="stylesheet" href="https://unpkg.com/leaflet.markercluster@1.4.1/dist/MarkerCluster.css" />
    <link rel="stylesheet" href="https://unpkg.com/leaflet.markercluster@1.4.1/dist/MarkerCluster.Default.css" />

    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; }
        body {
            font-family: 'Poppins', sans-serif;
            background: #D1D5DB;
            background: linear-gradient(135deg, #E5E7EB 0%, #D1D5DB 100%);
            min-height: 100vh;
        }
        .main-content { margin-left: 280px; padding: 24px 32px; min-height: 100vh; }
        
        /* Top Navbar */
        .top-navbar {
            background: #FFFFFF; border: 1px solid rgba(0,0,0,0.08); border-radius: 20px;
            padding: 12px 24px; margin-bottom: 28px; display: flex; justify-content: space-between; align-items: center;
        }
        .page-title h2 { font-size: 20px; font-weight: 600; margin: 0; color: #1A1A1A; }
        .page-title p { font-size: 13px; margin: 4px 0 0 0; color: #666; }
        .user-info { text-align: right; }
        .user-info .username { font-size: 14px; font-weight: 600; color: #1A1A1A; }
        .user-info .role { font-size: 11px; color: #F7B801; }
        .user-avatar {
            width: 44px; height: 44px; background: linear-gradient(135deg, #F7B801, #E5A800); border-radius: 14px;
            display: flex; align-items: center; justify-content: center; cursor: pointer; transition: transform 0.2s;
        }
        .user-avatar:hover { transform: scale(1.05); }
        .user-avatar i { font-size: 22px; color: #1A1A1A; }
        
        .dropdown-menu-custom {
            position: absolute; top: 80px; right: 32px; background: #FFFFFF; border: 1px solid rgba(0,0,0,0.1);
            box-shadow: 0 4px 12px rgba(0,0,0,0.08); border-radius: 16px; padding: 12px 0; min-width: 180px;
            display: none; z-index: 1000;
        }
        .dropdown-menu-custom.show { display: block; animation: fadeIn 0.2s ease; }
        @keyframes fadeIn { from { opacity: 0; transform: translateY(-10px); } to { opacity: 1; transform: translateY(0); } }
        .dropdown-menu-custom a {
            display: flex; align-items: center; gap: 12px; padding: 12px 20px; text-decoration: none;
            transition: all 0.2s; font-size: 13px; color: #333;
        }
        .dropdown-menu-custom a:hover { background: rgba(247,184,1,0.1); color: #F7B801; }
        .dropdown-divider { margin: 8px 0; border-color: #E0E0E0; }

        /* Filter Section */
        .filter-section {
            background: #FFFFFF; border: 1px solid rgba(0,0,0,0.08); border-radius: 20px;
            padding: 20px 24px; margin-bottom: 28px;
        }
        .form-label { font-size: 13px; font-weight: 600; margin-bottom: 8px; color: #1A1A1A; }
        .form-control, .form-select {
            background: #F8F8F8; border: 1px solid #E0E0E0; color: #1A1A1A;
            border-radius: 12px; padding: 10px 14px; font-size: 13px; font-family: 'Poppins', sans-serif;
        }
        .form-control:focus, .form-select:focus { border-color: #F7B801; box-shadow: 0 0 0 3px rgba(247,184,1,0.1); outline: none; }
        
        .btn-gold {
            background: linear-gradient(135deg, #F7B801, #E5A800); border: none; padding: 10px 20px; border-radius: 12px;
            font-weight: 600; font-size: 13px; color: #1A1A1A; transition: all 0.3s ease;
        }
        .btn-gold:hover { transform: translateY(-2px); box-shadow: 0 5px 15px rgba(247,184,1,0.3); color: #1A1A1A; }

        /* Stats Cards */
        .stat-card {
            background: #FFFFFF; border: 1px solid rgba(0,0,0,0.08); border-radius: 20px; padding: 20px;
            transition: all 0.3s ease; text-align: center;
        }
        .stat-card:hover { transform: translateY(-4px); border-color: #F7B801; box-shadow: 0 8px 20px rgba(0,0,0,0.06); }
        .stat-icon {
            width: 55px; height: 55px; background: rgba(247,184,1,0.1); border-radius: 16px;
            display: flex; align-items: center; justify-content: center; margin: 0 auto 12px;
        }
        .stat-icon i { font-size: 28px; color: #F7B801; }
        .stat-number { font-size: 32px; font-weight: 700; margin-bottom: 5px; color: #1A1A1A; }
        .stat-label { font-size: 13px; font-weight: 500; color: #666; }

        /* Buttons */
        .btn-tambah {
            background: linear-gradient(135deg, #F7B801, #E5A800); border: none; padding: 10px 20px; border-radius: 12px;
            font-weight: 600; font-size: 13px; color: #1A1A1A; transition: all 0.3s ease; display: flex; align-items: center; gap: 8px;
        }
        .btn-tambah:hover { transform: translateY(-2px); box-shadow: 0 5px 15px rgba(247,184,1,0.3); }

        .card-custom {
            background: #FFFFFF; border: 1px solid rgba(0,0,0,0.08); border-radius: 20px; overflow: hidden; margin-bottom: 28px;
        }
        .card-header-custom {
            padding: 18px 24px; display: flex; justify-content: space-between; align-items: center;
            background: #FFFFFF; border-bottom: 1px solid rgba(0,0,0,0.08);
        }
        .card-header-custom h3 { font-size: 16px; font-weight: 600; margin: 0; display: flex; align-items: center; gap: 10px; color: #F7B801; }

        .map-container-page { height: 400px; border-radius: 20px; overflow: hidden; margin-bottom: 20px; border: 1px solid rgba(0,0,0,0.08); }
        #mapPage { height: 100%; width: 100%; }

        .table-custom { width: 100%; margin-bottom: 0; color: #1A1A1A; }
        .table-custom thead th { padding: 14px 16px; font-size: 13px; font-weight: 600; background: #F8F8F8; color: #1A1A1A; border-bottom: 1px solid #E0E0E0; }
        .table-custom tbody td { padding: 12px 16px; font-size: 13px; vertical-align: middle; border-bottom: 1px solid #E0E0E0; }
        .table-custom tbody tr:hover { background: rgba(247,184,1,0.03); }

        .foto-thumb { width: 50px; height: 50px; object-fit: cover; border-radius: 10px; background: #F5F5F5; display: flex; align-items: center; justify-content: center; }
        .btn-action { background: transparent; border: none; padding: 6px 10px; border-radius: 10px; cursor: pointer; }
        .btn-action i { font-size: 14px; color: #999; }
        .btn-action:hover i { color: #F7B801; }
        .btn-action.danger:hover i { color: #dc3545; }

        .badge-status { background: rgba(247,184,1,0.15); color: #B8860B; padding: 4px 10px; border-radius: 20px; font-size: 11px; display: inline-block; }
        .badge-jiwa { background: rgba(220,53,69,0.1); color: #dc3545; padding: 4px 10px; border-radius: 20px; font-size: 11px; display: inline-block; }
        .badge-no-coord { background: rgba(255,193,7,0.15); color: #e6a000; padding: 2px 8px; border-radius: 12px; font-size: 10px; }

        /* Kerusakan Badges */
        .badge-success-custom { background: rgba(40,167,69,0.1); color: #1e7e34; padding: 4px 10px; border-radius: 20px; font-size: 11px; display: inline-block; font-weight: 600; }
        .badge-warning-custom { background: rgba(255,193,7,0.1); color: #d39e00; padding: 4px 10px; border-radius: 20px; font-size: 11px; display: inline-block; font-weight: 600; }
        .badge-orange-custom { background: rgba(253,126,20,0.1); color: #fd7e14; padding: 4px 10px; border-radius: 20px; font-size: 11px; display: inline-block; font-weight: 600; }
        .badge-danger-custom { background: rgba(220,53,69,0.1); color: #dc3545; padding: 4px 10px; border-radius: 20px; font-size: 11px; display: inline-block; font-weight: 600; }

        .btn-geocode { background: rgba(23,162,184,0.1); border: 1px solid rgba(23,162,184,0.3); padding: 4px 10px; border-radius: 8px; font-size: 11px; color: #17a2b8; transition: all 0.2s; text-decoration: none; cursor: pointer; }
        .btn-geocode:hover { background: rgba(23,162,184,0.2); color: #17a2b8; }
        .btn-geocode-bulk { background: rgba(40,167,69,0.1); border: 1px solid rgba(40,167,69,0.3); padding: 6px 14px; border-radius: 10px; font-size: 12px; color: #28a745; transition: all 0.2s; text-decoration: none; cursor: pointer; }
        .btn-geocode-bulk:hover { background: rgba(40,167,69,0.2); color: #28a745; }

        /* Modal Floating */
        .modal-floating { display: none; position: fixed; top: 0; left: 0; width: 100%; height: 100%; background: rgba(0,0,0,0.85); backdrop-filter: blur(8px); z-index: 2000; align-items: center; justify-content: center; }
        .modal-floating.show { display: flex; animation: modalFadeIn 0.3s ease; }
        @keyframes modalFadeIn { from { opacity: 0; } to { opacity: 1; } }
        .modal-floating-content { background: #FFFFFF; border-radius: 28px; width: 95%; max-width: 1200px; max-height: 90vh; overflow-y: auto; animation: modalFlyIn 0.4s cubic-bezier(0.34, 1.56, 0.64, 1); }
        @keyframes modalFlyIn { from { opacity: 0; transform: scale(0.95) translateY(-30px); } to { opacity: 1; transform: scale(1) translateY(0); } }
        .modal-floating-header { padding: 20px 28px; display: flex; justify-content: space-between; align-items: center; position: sticky; top: 0; z-index: 10; background: #FFFFFF; border-bottom: 1px solid rgba(0,0,0,0.08); }
        .modal-floating-header h4 { font-size: 20px; font-weight: 600; margin: 0; display: flex; align-items: center; gap: 10px; color: #F7B801; }
        .close-modal { background: transparent; border: none; width: 36px; height: 36px; border-radius: 12px; cursor: pointer; transition: all 0.2s; color: #666; }
        .close-modal:hover { background: rgba(247,184,1,0.1); color: #F7B801; }
        .modal-floating-body { padding: 28px; position: relative; }

        .form-group { margin-bottom: 20px; }
        .form-label { font-size: 13px; font-weight: 600; margin-bottom: 8px; display: block; color: #1A1A1A; }
        .form-label .required { color: #F7B801; }

        .map-container { height: 400px; border-radius: 16px; overflow: hidden; margin-bottom: 20px; }
        #map { height: 100%; width: 100%; }

        .coordinate-info { background: #F8F9FA; border-radius: 14px; padding: 15px; margin-bottom: 20px; }
        .coordinate-info p { margin: 0 0 10px 0; font-size: 13px; font-weight: 600; color: #F7B801; }
        .coordinate-input-group { display: flex; gap: 15px; margin-bottom: 15px; }
        .coordinate-input-group .form-control { flex: 1; }
        .btn-location { background: rgba(247,184,1,0.1); border: 1px solid rgba(247,184,1,0.3); padding: 10px 16px; border-radius: 14px; font-size: 13px; transition: all 0.2s; cursor: pointer; color: #F7B801; }
        .btn-location:hover { background: #F7B801; color: #1A1A1A; }

        .btn-submit { background: linear-gradient(135deg, #F7B801, #E5A800); border: none; padding: 12px 24px; border-radius: 14px; font-weight: 600; transition: all 0.2s; color: #1A1A1A; }
        .btn-submit:hover { transform: translateY(-2px); box-shadow: 0 5px 15px rgba(247,184,1,0.3); }
        .btn-cancel { background: transparent; padding: 12px 24px; border-radius: 14px; font-weight: 600; transition: all 0.2s; color: #666; border: 1px solid #ccc; }
        .btn-cancel:hover { background: rgba(0,0,0,0.05); }

        .image-preview { width: 100px; height: 100px; object-fit: cover; border-radius: 12px; margin-top: 10px; }
        .info-text { font-size: 12px; margin-top: 5px; color: #666; }

        /* DataTables overrides */
        .dataTables_wrapper .dataTables_length select, .dataTables_wrapper .dataTables_filter input { background: #F8F8F8; border: 1px solid #E0E0E0; color: #1A1A1A; border-radius: 10px; padding: 6px 12px; }
        .dataTables_wrapper .dataTables_info, .dataTables_wrapper .dataTables_paginate { color: #666; }
        .dataTables_wrapper .dataTables_paginate .paginate_button { background: #F8F8F8 !important; border-color: #E0E0E0 !important; color: #1A1A1A !important; }
        .dataTables_wrapper .dataTables_paginate .paginate_button.current { background: #F7B801 !important; color: #1A1A1A !important; }

        #penyebab_lainnya_container { display: none; margin-top: 8px; }
        
        /* Custom SweetAlert */
        .swal2-popup { font-family: 'Poppins', sans-serif !important; border-radius: 20px !important; }
        .swal2-confirm { border-radius: 12px !important; font-weight: 600 !important; }
        .swal2-cancel { border-radius: 12px !important; font-weight: 600 !important; }

        @media (max-width: 768px) {
            .main-content { margin-left: 0; padding: 16px; }
            .coordinate-input-group { flex-direction: column; gap: 10px; }
            .card-header-custom { flex-direction: column; gap: 12px; align-items: flex-start; }
            .map-container-page { height: 250px; }
            .filter-section .row { flex-direction: column; gap: 12px; }
        }
    </style>
</head>

<body>

    <div class="main-content">
        <div class="top-navbar">
            <div class="page-title">
                <h2>Data Kejadian Kebakaran</h2>
                <p>Kelola semua data kejadian kebakaran - Kota Banjarbaru, Kalimantan Selatan</p>
            </div>
            <div class="user-dropdown" style="display: flex; align-items: center; gap: 15px;">
                <div class="user-info">
                    <div class="username"><?= htmlspecialchars($user['username']) ?></div>
                    <div class="role">Super Administrator</div>
                </div>
                <div class="user-avatar" id="userAvatar">
                    <i class="fas fa-user"></i>
                </div>
            </div>
        </div>

        <div class="dropdown-menu-custom" id="dropdownMenu">
            <a href="../../logout.php">
                <i class="fas fa-sign-out-alt"></i>
                <span>Logout</span>
            </a>
        </div>

        <!-- FILTER SECTION -->
        <div class="filter-section">
            <form method="GET" action="" class="row g-3 align-items-end">
                <div class="col-md-4">
                    <label class="form-label"><i class="fas fa-search me-1"></i>Cari Kejadian</label>
                    <input type="text" name="search" class="form-control" placeholder="Alamat, Kecamatan, Kelurahan..." value="<?= htmlspecialchars($search) ?>">
                </div>
                <div class="col-md-3">
                    <label class="form-label"><i class="fas fa-map-marker-alt me-1"></i>Kecamatan</label>
                    <select name="kecamatan" class="form-select">
                        <option value="">Semua Kecamatan</option>
                        <?php
                        if ($kecamatan_list && $kecamatan_list->num_rows > 0):
                            mysqli_data_seek($kecamatan_list, 0);
                            while ($kec = $kecamatan_list->fetch_assoc()):
                        ?>
                            <option value="<?= htmlspecialchars($kec['kecamatan']) ?>" <?= $filter_kecamatan == $kec['kecamatan'] ? 'selected' : '' ?>>
                                <?= htmlspecialchars($kec['kecamatan']) ?>
                            </option>
                        <?php endwhile; endif; ?>
                    </select>
                </div>
                <div class="col-md-3">
                    <label class="form-label"><i class="fas fa-calendar me-1"></i>Periode Bulan</label>
                    <input type="month" name="bulan" class="form-control" value="<?= htmlspecialchars($filter_bulan) ?>">
                </div>
                <div class="col-md-2">
                    <button type="submit" class="btn-gold w-100">
                        <i class="fas fa-filter"></i> Tampilkan
                    </button>
                </div>
            </form>
        </div>

        <!-- STATISTIK CARD -->
        <div class="row g-4 mb-4">
            <div class="col-md-2 col-6">
                <div class="stat-card">
                    <div class="stat-icon"><i class="fas fa-fire"></i></div>
                    <div class="stat-number"><?= $total_kejadian ?></div>
                    <div class="stat-label">Total Kejadian</div>
                </div>
            </div>
            <!-- CARD BARU: KEJADIAN BULAN INI (Diletakkan setelah Total Kejadian) -->
            <div class="col-md-2 col-6">
                <div class="stat-card">
                    <div class="stat-icon"><i class="fas fa-calendar-alt"></i></div>
                    <div class="stat-number"><?= $kejadian_bulan_ini ?></div>
                    <div class="stat-label">Bulan Ini</div>
                </div>
            </div>
            <div class="col-md-2 col-6">
                <div class="stat-card">
                    <div class="stat-icon"><i class="fas fa-building"></i></div>
                    <div class="stat-number"><?= $total_bangunan ?></div>
                    <div class="stat-label">Bangunan</div>
                </div>
            </div>
            <div class="col-md-2 col-6">
                <div class="stat-card">
                    <div class="stat-icon"><i class="fas fa-users"></i></div>
                    <div class="stat-number"><?= $total_kk ?></div>
                    <div class="stat-label">KK Terdampak</div>
                </div>
            </div>
            <div class="col-md-2 col-6">
                <div class="stat-card">
                    <div class="stat-icon"><i class="fas fa-user-injured"></i></div>
                    <div class="stat-number"><?= $total_luka ?></div>
                    <div class="stat-label">Korban Luka</div>
                </div>
            </div>
            <div class="col-md-2 col-6">
                <div class="stat-card">
                    <div class="stat-icon"><i class="fas fa-skull"></i></div>
                    <div class="stat-number"><?= $total_jiwa ?></div>
                    <div class="stat-label">Korban Jiwa</div>
                </div>
            </div>
        </div>

        <!-- PETA SEBARAN -->
        <div class="card-custom">
            <div class="card-header-custom">
                <h3><i class="fas fa-map-marked-alt"></i> Peta Sebaran Kejadian</h3>
                <div>
                    <span class="badge-stats" style="background:rgba(247,184,1,0.1); color:#F7B801; padding:4px 12px; border-radius:20px; font-size:12px;">
                        <i class="fas fa-map-pin"></i> <?= count($coordinates) ?> Titik
                    </span>
                    <?php
                    $no_coord = count($incidents) - count($coordinates);
                    if ($no_coord > 0):
                    ?>
                        <span class="badge-stats" style="background:rgba(255,193,7,0.1); color:#e6a000; padding:4px 12px; border-radius:20px; font-size:12px;">
                            <i class="fas fa-exclamation-triangle"></i> <?= $no_coord ?> Tanpa Koordinat
                        </span>
                    <?php endif; ?>
                </div>
            </div>
            <div class="card-body" style="padding: 20px;">
                <?php if (count($coordinates) > 0): ?>
                    <div class="map-container-page">
                        <div id="mapPage"></div>
                    </div>
                <?php else: ?>
                    <div class="text-center py-4" style="color: #999;">
                        <i class="fas fa-map fa-3x mb-3 d-block" style="color: #ddd;"></i>
                        <p>Belum ada data kejadian dengan koordinat</p>
                        <p class="small">Tambahkan data atau gunakan fitur <strong>Geocoding</strong> untuk mengubah alamat menjadi koordinat</p>
                    </div>
                <?php endif; ?>
                
                <div class="d-flex gap-2 mt-3 flex-wrap">
                    <?php if ($no_coord > 0): ?>
                        <button type="button" class="btn-geocode-bulk" onclick="confirmBulkGeocode()">
                            <i class="fas fa-sync-alt me-1"></i> Geocoding Massal (<?= $no_coord ?> data)
                        </button>
                    <?php endif; ?>
                    <button class="btn-geocode-bulk" onclick="location.reload()" style="background:rgba(23,162,184,0.1); border-color:rgba(23,162,184,0.3); color:#17a2b8;">
                        <i class="fas fa-redo me-1"></i> Refresh Peta
                    </button>
                </div>
            </div>
        </div>

        <!-- TABEL DATA -->
        <div class="card-custom">
            <div class="card-header-custom">
                <h3><i class="fas fa-list"></i> Daftar Kejadian Kebakaran</h3>
                <div class="d-flex gap-2">
                    <button class="btn-tambah" id="btnTambah"><i class="fas fa-plus"></i> Tambah Data</button>
                </div>
            </div>
            <div class="table-responsive">
                <table class="table-custom table" id="dataTable">
                    <thead>
                        <tr>
                            <th>No</th>
                            <th>Foto</th>
                            <th>Waktu</th>
                            <th>Alamat</th>
                            <th>Kecamatan</th>
                            <th>Kelurahan</th>
                            <th>Koordinat</th>
                            <th>Penyebab</th>
                            <th>Kerusakan</th>
                            <th>Korban</th>
                            <th>Ditambahkan Oleh</th>
                            <th>Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($incidents as $index => $row): 
                            $hasCoord = !empty($row['latitude']) && !empty($row['longitude']);
                            $penyebab = $row['penyebab'] ?? '-';
                            if (!empty($row['penyebab_lainnya'])) {
                                $penyebab = $row['penyebab_lainnya'];
                            }
                            
                            $kerusakan = $row['kerusakan'] ?? '-';
                            $kerusakanClass = '';
                            if ($kerusakan == 'rusak ringan') $kerusakanClass = 'badge-success-custom';
                            elseif ($kerusakan == 'rusak sedang') $kerusakanClass = 'badge-warning-custom';
                            elseif ($kerusakan == 'rusak berat') $kerusakanClass = 'badge-orange-custom';
                            elseif ($kerusakan == 'rusak total') $kerusakanClass = 'badge-danger-custom';
                        ?>
                            <tr>
                                <td><?= $index + 1 ?></td>
                                <td>
                                    <?php if ($row['foto'] && file_exists('../../uploads/' . $row['foto'])): ?>
                                        <img src="../../uploads/<?= $row['foto'] ?>" class="foto-thumb">
                                    <?php else: ?>
                                        <div class="foto-thumb d-flex align-items-center justify-content-center"><i class="fas fa-image" style="color:#999;"></i></div>
                                    <?php endif; ?>
                                </td>
                                <td><?= date('d/m/Y H:i', strtotime($row['waktu'])) ?></td>
                                <td><?= htmlspecialchars(substr($row['alamat'], 0, 40)) ?>...</td>
                                <td><?= htmlspecialchars($row['kecamatan'] ?? '-') ?></td>
                                <td><?= htmlspecialchars($row['kelurahan'] ?? '-') ?></td>
                                <td>
                                    <?php if ($hasCoord): ?>
                                        <span style="font-size:11px;">
                                            <?= number_format($row['latitude'], 6) ?><br>
                                            <?= number_format($row['longitude'], 6) ?>
                                        </span>
                                    <?php else: ?>
                                        <span class="badge-no-coord"><i class="fas fa-exclamation-circle me-1"></i> Tidak ada</span>
                                        <br>
                                        <button type="button" class="btn-geocode mt-1 border-0" onclick="confirmGeocode(<?= $row['id'] ?>)">
                                            <i class="fas fa-sync-alt"></i> Geocode
                                        </button>
                                    <?php endif; ?>
                                </td>
                                <td><span style="font-size:12px;"><?= htmlspecialchars($penyebab) ?></span></td>
                                <td>
                                    <?php if ($kerusakanClass != ''): ?>
                                        <span class="<?= $kerusakanClass ?>"><i class="fas fa-house-damage"></i> <?= ucwords($kerusakan) ?></span>
                                    <?php else: ?>
                                        <span style="color:#999;">-</span>
                                    <?php endif; ?>
                                </td>
                                <td>
                                    <span class="badge-status">Luka: <?= $row['korban_luka'] ?></span>
                                    <span class="badge-jiwa">Jiwa: <?= $row['korban_jiwa'] ?></span>
                                </td>
                                <td>
                                    <span style="font-size:12px; font-weight:600;">
                                        <i class="fas fa-user-circle" style="color:#F7B801;"></i>
                                        <?= htmlspecialchars($row['nama_penambah'] ?? '-') ?>
                                    </span>
                                    <?php if (!empty($row['nama_pengedit'])): ?>
                                        <br><span style="font-size:10px; color:#999;">
                                            <i class="fas fa-pen"></i> diedit oleh <?= htmlspecialchars($row['nama_pengedit']) ?>
                                        </span>
                                    <?php endif; ?>
                                </td>
                                <td>
                                    <button class="btn-action btn-edit" data-id="<?= $row['id'] ?>"><i class="fas fa-edit"></i></button>
                                    <button type="button" class="btn-action danger" onclick="confirmDelete(<?= $row['id'] ?>)"><i class="fas fa-trash"></i></button>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <!-- MODAL FLOATING -->
    <div class="modal-floating" id="modalFloating">
        <div class="modal-floating-content">
            <div class="modal-floating-header">
                <h4><i class="fas fa-fire"></i> <span id="modalTitle">Tambah Data Kejadian</span></h4>
                <button class="close-modal" id="closeModal"><i class="fas fa-times"></i></button>
            </div>
            <div class="modal-floating-body" style="position: relative;">
                <form id="kejadianForm" enctype="multipart/form-data">
                    <input type="hidden" name="id" id="editId">

                    <!-- Baris 1: Waktu & Kecamatan -->
                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-group">
                                <label class="form-label"><i class="fas fa-calendar-alt me-1"></i> Waktu Kejadian <span class="required">*</span></label>
                                <input type="datetime-local" name="waktu" id="waktu" class="form-control" required>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group">
                                <label class="form-label"><i class="fas fa-map-marker-alt me-1"></i> Kecamatan <span class="required">*</span></label>
                                <select name="kecamatan" id="kecamatan" class="form-select" required onchange="updateKelurahan()">
                                    <option value="">Pilih Kecamatan</option>
                                    <option value="Banjarbaru Selatan">Banjarbaru Selatan</option>
                                    <option value="Banjarbaru Utara">Banjarbaru Utara</option>
                                    <option value="Cempaka">Cempaka</option>
                                    <option value="Landasan Ulin">Landasan Ulin</option>
                                    <option value="Liang Anggang">Liang Anggang</option>
                                </select>
                            </div>
                        </div>
                    </div>

                    <!-- Baris 2: Kelurahan & Kerusakan -->
                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-group">
                                <label class="form-label"><i class="fas fa-building"></i> Kelurahan/Desa <span class="required">*</span></label>
                                <select name="kelurahan" id="kelurahan" class="form-select" required>
                                    <option value="">Pilih Kecamatan terlebih dahulu</option>
                                </select>
                            </div>
                        </div>
                        <div class="col-md-6">
    <div class="form-group">
        <label class="form-label"><i class="fas fa-house-damage"></i> Tingkat Kerusakan</label>
        <select name="kerusakan" id="kerusakan" class="form-select">
            <option value="">Pilih Tingkat Kerusakan</option>
            <option value="rusak ringan">Rusak Ringan</option>
            <option value="rusak sedang">Rusak Sedang</option>
            <option value="rusak berat">Rusak Berat</option>
            <option value="rusak total">Rusak Total</option>
        </select>
    </div>
</div>
                    </div>

                    <!-- MAP -->
                    <div class="form-group">
                        <label class="form-label"><i class="fas fa-map me-1"></i> Pilih Lokasi di Peta</label>
                        <div class="map-container">
                            <div id="map"></div>
                        </div>
                        <div class="info-text"><i class="fas fa-info-circle"></i> Klik pada peta atau seret marker untuk memilih titik kejadian</div>
                    </div>

                    <!-- Koordinat Manual -->
                    <div class="coordinate-info">
                        <p><i class="fas fa-crosshairs"></i> Koordinat Lokasi</p>
                        <div class="coordinate-input-group">
                            <div class="form-group" style="flex:1; margin-bottom:0;">
                                <label class="form-label" style="font-size:12px;">Latitude</label>
                                <input type="number" step="any" name="latitude" id="latitude" class="form-control" placeholder="-3.4422" required>
                            </div>
                            <div class="form-group" style="flex:1; margin-bottom:0;">
                                <label class="form-label" style="font-size:12px;">Longitude</label>
                                <input type="number" step="any" name="longitude" id="longitude" class="form-control" placeholder="114.8325" required>
                            </div>
                            <div style="display: flex; align-items: flex-end;">
                                <button type="button" class="btn-location" id="applyCoordinates" style="margin-bottom:0;"><i class="fas fa-check"></i> Terapkan</button>
                            </div>
                        </div>
                        <div class="d-flex gap-2 mt-3">
                            <button type="button" class="btn-location" id="getCurrentLocation"><i class="fas fa-location-dot"></i> Gunakan Lokasi Saya</button>
                            <button type="button" class="btn-location" id="searchAddressBtn"><i class="fas fa-search"></i> Cari Alamat</button>
                            <button type="button" class="btn-location" id="geocodeFromAddress"><i class="fas fa-map-pin"></i> Dari Alamat</button>
                        </div>
                    </div>

                    <!-- Baris 3: Jumlah Bangunan & KK -->
                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-group">
                                <label class="form-label"><i class="fas fa-home"></i> Jumlah Bangunan Terdampak</label>
                                <input type="number" name="jumlah_bangunan" id="jumlah_bangunan" class="form-control" value="0">
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group">
                                <label class="form-label"><i class="fas fa-users"></i> Jumlah KK</label>
                                <input type="number" name="jumlah_KK" id="jumlah_KK" class="form-control" value="0">
                            </div>
                        </div>
                    </div>

                    <!-- Baris 4: Individu, Luka, Jiwa -->
                    <div class="row">
                        <div class="col-md-4">
                            <div class="form-group">
                                <label class="form-label"><i class="fas fa-user"></i> Jumlah Individu</label>
                                <input type="number" name="jumlah_individu" id="jumlah_individu" class="form-control" value="0">
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="form-group">
                                <label class="form-label"><i class="fas fa-user-injured"></i> Korban Luka</label>
                                <input type="number" name="korban_luka" id="korban_luka" class="form-control" value="0">
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="form-group">
                                <label class="form-label"><i class="fas fa-skull"></i> Korban Jiwa</label>
                                <input type="number" name="korban_jiwa" id="korban_jiwa" class="form-control" value="0">
                            </div>
                        </div>
                    </div>

                    <!-- PENYEBAB & KETERANGAN -->
                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-group">
                                <label class="form-label"><i class="fas fa-exclamation-triangle"></i> Penyebab Kebakaran <span class="required">*</span></label>
                                <select name="penyebab" id="penyebab" class="form-select" required onchange="togglePenyebabLainnya()">
                                    <option value="">Pilih Penyebab</option>
                                    <option value="Korsleting listrik">Korsleting listrik</option>
                                    <option value="Kompor menyala">Kompor menyala</option>
                                    <option value="Gas bocor">Gas bocor</option>
                                    <option value="Rokok">Rokok</option>
                                    <option value="Lilin">Lilin</option>
                                    <option value="Bermain api">Bermain api</option>
                                    <option value="Reaksi kimia">Reaksi kimia</option>
                                    <option value="Pembakaran sampah">Pembakaran sampah</option>
                                    <option value="Mesin bermasalah">Mesin bermasalah</option>
                                    <option value="Petir">Petir</option>
                                    <option value="Disengaja">Disengaja</option>
                                    <option value="Dalam penyelidikan">Dalam penyelidikan</option>
                                    <option value="Tidak diketahui">Tidak diketahui</option>
                                    <option value="Lainnya">Lainnya...</option>
                                </select>
                            </div>
                            <div id="penyebab_lainnya_container">
                                <div class="form-group">
                                    <label class="form-label">Sebutkan penyebab lainnya <span class="required">*</span></label>
                                    <input type="text" name="penyebab_lainnya" id="penyebab_lainnya" class="form-control" placeholder="Tulis penyebab kebakaran...">
                                </div>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group">
                                <label class="form-label"><i class="fas fa-sticky-note"></i> Keterangan</label>
                                <textarea name="keterangan" id="keterangan" class="form-control" rows="4" placeholder="Keterangan tambahan tentang kejadian..."></textarea>
                            </div>
                        </div>
                    </div>

                    <!-- FOTO -->
                    <div class="row">
                        <div class="col-md-12">
                            <div class="form-group">
                                <label class="form-label"><i class="fas fa-image"></i> Foto Kejadian</label>
                                <input type="file" name="foto" id="foto" class="form-control" accept="image/*">
                                <div id="fotoPreview" class="mt-2"></div>
                            </div>
                        </div>
                    </div>

                    <!-- ALAMAT -->
                    <div class="form-group">
                        <label class="form-label"><i class="fas fa-location-dot"></i> Alamat Lengkap <span class="required">*</span></label>
                        <textarea name="alamat" id="alamat" class="form-control" rows="3" placeholder="Alamat lengkap lokasi kejadian" required></textarea>
                    </div>

                    <div class="d-flex gap-3 mt-4">
                        <button type="submit" class="btn-submit" id="submitBtn"><i class="fas fa-save"></i> Simpan Data</button>
                        <button type="button" class="btn-cancel" id="cancelModal"><i class="fas fa-times"></i> Batal</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- MODAL CARI ALAMAT -->
    <div class="modal-floating" id="searchModal" style="z-index: 2100;">
        <div class="modal-floating-content" style="max-width: 500px;">
            <div class="modal-floating-header">
                <h4><i class="fas fa-search"></i> Cari Alamat</h4>
                <button class="close-modal" id="closeSearchModal"><i class="fas fa-times"></i></button>
            </div>
            <div class="modal-floating-body">
                <div class="form-group">
                    <label class="form-label">Masukkan nama jalan, kelurahan, atau kecamatan</label>
                    <input type="text" id="searchAddress" class="form-control" placeholder="Contoh: Landasan Ulin, Banjarbaru">
                </div>
                <div id="searchResults" class="mt-3" style="max-height: 300px; overflow-y: auto;"></div>
                <div class="d-flex gap-3 mt-4">
                    <button type="button" class="btn-submit" id="doSearch"><i class="fas fa-search"></i> Cari</button>
                    <button type="button" class="btn-cancel" id="cancelSearchModal">Batal</button>
                </div>
            </div>
        </div>
    </div>

    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://cdn.datatables.net/1.13.4/js/jquery.dataTables.min.js"></script>
    <script src="https://cdn.datatables.net/1.13.4/js/dataTables.bootstrap5.min.js"></script>
    <script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js"></script>
    <script src="https://unpkg.com/leaflet.markercluster@1.4.1/dist/leaflet.markercluster.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

    <script>
        // ============================================================
        // ALERT PHP DARI SERVER
        // ============================================================
        <?php if ($message): ?>
            Swal.fire({
                icon: '<?= $messageType == "success" ? "success" : "error" ?>',
                title: '<?= $messageType == "success" ? "Berhasil!" : "Gagal!" ?>',
                text: '<?= $message ?>',
                customClass: { popup: 'swal2-popup', confirmButton: 'swal2-confirm' }
            });
        <?php endif; ?>

        // ============================================================
        // FUNGSI SWEETALERT UNTUK TOMBOL AKSI
        // ============================================================
        function confirmDelete(id) {
            Swal.fire({
                title: 'Yakin hapus data?',
                text: "Data yang dihapus tidak dapat dikembalikan!",
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#d33',
                cancelButtonColor: '#6c757d',
                confirmButtonText: 'Ya, Hapus!',
                cancelButtonText: 'Batal',
                reverseButtons: true,
                customClass: { popup: 'swal2-popup', confirmButton: 'swal2-confirm', cancelButton: 'swal2-cancel' }
            }).then((result) => {
                if (result.isConfirmed) {
                    Swal.fire({ title: 'Menghapus...', allowOutsideClick: false, didOpen: () => { Swal.showLoading(); }});
                    window.location.href = '?delete=' + id;
                }
            });
        }

        function confirmGeocode(id) {
            Swal.fire({
                title: 'Cari Koordinat?',
                text: "Sistem akan mencari koordinat otomatis berdasarkan alamat yang tersimpan.",
                icon: 'question',
                showCancelButton: true,
                confirmButtonColor: '#F7B801',
                cancelButtonColor: '#6c757d',
                confirmButtonText: 'Ya, Cari!',
                cancelButtonText: 'Batal',
                reverseButtons: true,
                customClass: { popup: 'swal2-popup', confirmButton: 'swal2-confirm', cancelButton: 'swal2-cancel' }
            }).then((result) => {
                if (result.isConfirmed) {
                    Swal.fire({ title: 'Mencari Koordinat...', allowOutsideClick: false, didOpen: () => { Swal.showLoading(); }});
                    window.location.href = '?geocode=1&id=' + id;
                }
            });
        }

        function confirmBulkGeocode() {
            Swal.fire({
                title: 'Geocoding Massal?',
                text: "Sistem akan mencoba mencari koordinat untuk SEMUA data yang belum memilikinya. Proses ini mungkin memakan waktu.",
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#F7B801',
                cancelButtonColor: '#6c757d',
                confirmButtonText: 'Ya, Lanjutkan!',
                cancelButtonText: 'Batal',
                reverseButtons: true,
                customClass: { popup: 'swal2-popup', confirmButton: 'swal2-confirm', cancelButton: 'swal2-cancel' }
            }).then((result) => {
                if (result.isConfirmed) {
                    Swal.fire({ title: 'Memproses Geocoding...', allowOutsideClick: false, didOpen: () => { Swal.showLoading(); }});
                    window.location.href = '?bulk_geocode=1';
                }
            });
        }

        // ============================================================
        // DATA KELURAHAN
        // ============================================================
        const kelurahanData = <?= json_encode($kelurahan_data) ?>;

        function updateKelurahan() {
            const kecamatan = document.getElementById('kecamatan').value;
            const kelurahanSelect = document.getElementById('kelurahan');
            
            kelurahanSelect.innerHTML = '<option value="">Pilih Kelurahan</option>';
            
            if (kecamatan && kelurahanData[kecamatan]) {
                kelurahanData[kecamatan].forEach(function(kel) {
                    kelurahanSelect.innerHTML += '<option value="' + kel + '">' + kel + '</option>';
                });
            } else {
                kelurahanSelect.innerHTML = '<option value="">Pilih Kecamatan terlebih dahulu</option>';
            }
        }

        // ============================================================
        // TOGGLE PENYEBAB LAINNYA
        // ============================================================
        function togglePenyebabLainnya() {
            const penyebab = document.getElementById('penyebab').value;
            const container = document.getElementById('penyebab_lainnya_container');
            const input = document.getElementById('penyebab_lainnya');
            
            if (penyebab === 'Lainnya') {
                container.style.display = 'block';
                input.setAttribute('required', 'required');
            } else {
                container.style.display = 'none';
                input.removeAttribute('required');
                input.value = '';
            }
        }

        // ============================================================
        // DATA TABLES
        // ============================================================
        $(document).ready(function() {
            $('#dataTable').DataTable({
                language: {
                    url: '//cdn.datatables.net/plug-ins/1.13.4/i18n/id.json'
                },
                order: [[2, 'desc']]
            });
        });

        // ============================================================
        // PETA UTAMA (DEFAULT OSM)
        // ============================================================
        const coordinatesData = <?= json_encode($coordinates) ?>;
        let mapPage;

        function initMapPage() {
            if (mapPage) { mapPage.remove(); }
            if (coordinatesData.length === 0) { return; }
            
            const centerLat = coordinatesData.reduce((sum, c) => sum + c.lat, 0) / coordinatesData.length;
            const centerLng = coordinatesData.reduce((sum, c) => sum + c.lng, 0) / coordinatesData.length;
            
            mapPage = L.map('mapPage').setView([centerLat, centerLng], 12);
            
            L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
                attribution: '&copy; <a href="https://www.openstreetmap.org/copyright">OpenStreetMap</a> contributors',
                maxZoom: 19
            }).addTo(mapPage);
            
            const markers = L.markerClusterGroup();
            
            coordinatesData.forEach(data => {
                const marker = L.marker([data.lat, data.lng])
                    .bindPopup(`
                        <div style="font-family: 'Poppins', sans-serif; font-size: 12px;">
                            <strong style="color: #F7B801;">Kejadian Kebakaran</strong><br>
                            <small>${new Date(data.waktu).toLocaleString('id-ID')}</small><br>
                            ${data.alamat ? data.alamat.substring(0, 80) + (data.alamat.length > 80 ? '...' : '') : '-'}<br>
                            <a href="#" onclick="editKejadian(${data.id})" style="color: #F7B801; text-decoration: none;">
                                <i class="fas fa-edit"></i> Edit
                            </a>
                        </div>
                    `);
                markers.addLayer(marker);
            });
            
            mapPage.addLayer(markers);
            
            const bounds = coordinatesData.map(c => [c.lat, c.lng]);
            mapPage.fitBounds(bounds, { padding: [30, 30] });
        }

        function editKejadian(id) {
            document.querySelector(`.btn-edit[data-id="${id}"]`)?.click();
        }

        // ============================================================
        // MAP DI MODAL (DEFAULT OSM)
        // ============================================================
        let map, marker;
        const defaultLat = -3.4422;
        const defaultLng = 114.8325;

        function initMap(lat = defaultLat, lng = defaultLng) {
            if (map) map.remove();
            
            map = L.map('map').setView([lat, lng], 14);
            
            L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
                attribution: '&copy; <a href="https://www.openstreetmap.org/copyright">OpenStreetMap</a> contributors',
                maxZoom: 19
            }).addTo(map);

            marker = L.marker([lat, lng], { draggable: true }).addTo(map);
            document.getElementById('latitude').value = lat;
            document.getElementById('longitude').value = lng;

            marker.on('dragend', function(e) {
                const pos = e.target.getLatLng();
                updateCoordinatesFromLatLng(pos.lat, pos.lng);
                reverseGeocode(pos.lat, pos.lng);
            });

            map.on('click', function(e) {
                marker.setLatLng(e.latlng);
                updateCoordinatesFromLatLng(e.latlng.lat, e.latlng.lng);
                reverseGeocode(e.latlng.lat, e.latlng.lng);
            });
        }

        function updateCoordinatesFromLatLng(lat, lng) {
            document.getElementById('latitude').value = lat.toFixed(8);
            document.getElementById('longitude').value = lng.toFixed(8);
        }

        // ============================================================
        // GEOCODING FUNCTIONS
        // ============================================================
        document.getElementById('geocodeFromAddress')?.addEventListener('click', async function() {
            const address = document.getElementById('alamat').value;
            if (!address) {
                Swal.fire({icon: 'warning', text: 'Masukkan alamat terlebih dahulu!', customClass: {popup: 'swal2-popup', confirmButton: 'swal2-confirm'}});
                return;
            }
            
            Swal.fire({ title: 'Mencari Koordinat...', allowOutsideClick: false, didOpen: () => { Swal.showLoading(); }});
            
            try {
                const url = `https://nominatim.openstreetmap.org/search?format=json&q=${encodeURIComponent(address + ', Banjarbaru, Kalimantan Selatan, Indonesia')}&limit=1&accept-language=id`;
                const response = await fetch(url);
                const data = await response.json();
                
                if (data && data.length > 0) {
                    const lat = parseFloat(data[0].lat);
                    const lng = parseFloat(data[0].lon);
                    
                    map.setView([lat, lng], 16);
                    marker.setLatLng([lat, lng]);
                    updateCoordinatesFromLatLng(lat, lng);
                    
                    if (data[0].display_name) {
                        const parts = data[0].display_name.split(',');
                        for (let part of parts) {
                            part = part.trim();
                            for (let kec of ['Landasan Ulin', 'Cempaka', 'Banjarbaru Utara', 'Banjarbaru Selatan', 'Liang Anggang']) {
                                if (part.toLowerCase().includes(kec.toLowerCase())) {
                                    document.getElementById('kecamatan').value = kec;
                                    updateKelurahan();
                                    break;
                                }
                            }
                            for (let kel of ['Guntung Paikat', 'Kemuning', 'Loktabat Selatan', 'Sungai Besar', 'Komet', 'Loktabat Utara', 'Mentaos', 'Sungai Ulin', 'Bangkal', 'Cempaka', 'Palam', 'Sungai Tiung', 'Guntung Manggis', 'Guntung Payung', 'Landasan Ulin Timur', 'Syamsudin Noor', 'Landasan Ulin Barat', 'Landasan Ulin Selatan', 'Landasan Ulin Tengah', 'Landasan Ulin Utara']) {
                                if (part.toLowerCase().includes(kel.toLowerCase())) {
                                    document.getElementById('kelurahan').value = kel;
                                    break;
                                }
                            }
                        }
                    }
                    
                    Swal.fire({icon: 'success', title: 'Berhasil', text: 'Koordinat berhasil ditemukan!', timer: 2000, showConfirmButton: false, customClass: {popup: 'swal2-popup'}});
                } else {
                    Swal.fire({icon: 'error', title: 'Gagal', text: 'Alamat tidak ditemukan. Pastikan alamat lengkap dan benar!', customClass: {popup: 'swal2-popup', confirmButton: 'swal2-confirm'}});
                }
            } catch (err) {
                Swal.fire({icon: 'error', title: 'Error', text: 'Terjadi kesalahan: ' + err.message, customClass: {popup: 'swal2-popup', confirmButton: 'swal2-confirm'}});
            }
        });

        async function reverseGeocode(lat, lng) {
            Swal.fire({ title: 'Menerjemahkan Lokasi...', allowOutsideClick: false, didOpen: () => { Swal.showLoading(); }});
            const url = `https://nominatim.openstreetmap.org/reverse?format=json&lat=${lat}&lon=${lng}&accept-language=id&addressdetails=1`;

            try {
                const response = await fetch(url);
                const data = await response.json();
                if (data.display_name) {
                    document.getElementById('alamat').value = data.display_name;
                }
                Swal.close();
            } catch (err) {
                console.log('Geocoding error:', err);
                Swal.close();
            }
        }

        async function searchAddress(query) {
            const url = `https://nominatim.openstreetmap.org/search?format=json&q=${encodeURIComponent(query + ', Banjarbaru, Kalimantan Selatan')}&limit=10`;
            const response = await fetch(url);
            return response.json();
        }

        function applyManualCoordinates() {
            const lat = parseFloat(document.getElementById('latitude').value);
            const lng = parseFloat(document.getElementById('longitude').value);
            if (isNaN(lat) || isNaN(lng)) {
                Swal.fire({icon: 'warning', text: 'Masukkan latitude dan longitude yang valid!', customClass: {popup: 'swal2-popup', confirmButton: 'swal2-confirm'}});
                return;
            }
            map.setView([lat, lng], 16);
            marker.setLatLng([lat, lng]);
            reverseGeocode(lat, lng);
        }

        function getCurrentLocation() {
            if (navigator.geolocation) {
                Swal.fire({ title: 'Mencari Lokasi Anda...', allowOutsideClick: false, didOpen: () => { Swal.showLoading(); }});
                navigator.geolocation.getCurrentPosition(function(position) {
                    const lat = position.coords.latitude;
                    const lng = position.coords.longitude;
                    map.setView([lat, lng], 16);
                    marker.setLatLng([lat, lng]);
                    updateCoordinatesFromLatLng(lat, lng);
                    reverseGeocode(lat, lng);
                }, function(error) {
                    Swal.fire({icon: 'error', title: 'Gagal', text: 'Gagal mendapatkan lokasi: ' + error.message, customClass: {popup: 'swal2-popup', confirmButton: 'swal2-confirm'}});
                });
            } else {
                Swal.fire({icon: 'error', text: 'Browser tidak mendukung geolocation', customClass: {popup: 'swal2-popup', confirmButton: 'swal2-confirm'}});
            }
        }

        // ============================================================
        // MODAL HANDLERS
        // ============================================================
        const modal = document.getElementById('modalFloating');
        const searchModal = document.getElementById('searchModal');
        const btnTambah = document.getElementById('btnTambah');

        function openModal() {
            modal.classList.add('show');
            setTimeout(() => { map?.invalidateSize(); }, 200);
        }

        function closeModalFunc() {
            modal.classList.remove('show');
        }

        function openSearchModal() {
            searchModal.classList.add('show');
        }

        function closeSearchModal() {
            searchModal.classList.remove('show');
            document.getElementById('searchAddress').value = '';
            document.getElementById('searchResults').innerHTML = '';
        }

        btnTambah.addEventListener('click', function() {
            document.getElementById('kejadianForm').reset();
            document.getElementById('editId').value = '';
            document.getElementById('modalTitle').innerHTML = 'Tambah Data Kejadian';
            document.getElementById('fotoPreview').innerHTML = '';
            document.getElementById('penyebab_lainnya_container').style.display = 'none';
            document.getElementById('kelurahan').innerHTML = '<option value="">Pilih Kecamatan terlebih dahulu</option>';
            
            const now = new Date();
            now.setMinutes(now.getMinutes() - now.getTimezoneOffset());
            document.getElementById('waktu').value = now.toISOString().slice(0, 16);
            initMap(defaultLat, defaultLng);
            openModal();
        });

        document.getElementById('closeModal').addEventListener('click', closeModalFunc);
        document.getElementById('cancelModal').addEventListener('click', closeModalFunc);
        modal.addEventListener('click', function(e) {
            if (e.target === modal) closeModalFunc();
        });
        document.getElementById('applyCoordinates').addEventListener('click', applyManualCoordinates);
        document.getElementById('getCurrentLocation').addEventListener('click', getCurrentLocation);
        document.getElementById('searchAddressBtn').addEventListener('click', openSearchModal);
        document.getElementById('closeSearchModal').addEventListener('click', closeSearchModal);
        document.getElementById('cancelSearchModal').addEventListener('click', closeSearchModal);
        searchModal.addEventListener('click', function(e) {
            if (e.target === searchModal) closeSearchModal();
        });

        document.getElementById('doSearch').addEventListener('click', async function() {
            const query = document.getElementById('searchAddress').value;
            if (!query) {
                Swal.fire({icon: 'warning', text: 'Masukkan alamat yang ingin dicari!', customClass: {popup: 'swal2-popup', confirmButton: 'swal2-confirm'}});
                return;
            }
            
            Swal.fire({ title: 'Mencari Alamat...', allowOutsideClick: false, didOpen: () => { Swal.showLoading(); }});
            
            const results = await searchAddress(query);
            Swal.close();
            
            const resultsDiv = document.getElementById('searchResults');
            if (results.length === 0) {
                resultsDiv.innerHTML = '<div class="alert alert-danger">Tidak ditemukan hasil</div>';
                return;
            }
            let html = '<div class="list-group">';
            results.forEach(result => {
                html += `<button type="button" class="list-group-item list-group-item-action" style="background:#FFF; border:1px solid #E0E0E0; margin-bottom:8px; border-radius:12px; padding:12px; text-align:left; width:100%;" onclick="selectSearchResult(${result.lat}, ${result.lon}, '${result.display_name.replace(/'/g, "\\'")}')">
                            <strong>${result.display_name.substring(0, 80)}</strong><br>
                            <small>Lat: ${parseFloat(result.lat).toFixed(6)}, Lng: ${parseFloat(result.lon).toFixed(6)}</small>
                        </button>`;
            });
            html += '</div>';
            resultsDiv.innerHTML = html;
        });

        window.selectSearchResult = function(lat, lng, displayName) {
            map.setView([lat, lng], 17);
            marker.setLatLng([lat, lng]);
            updateCoordinatesFromLatLng(lat, lng);
            reverseGeocode(lat, lng);
            closeSearchModal();
        };

        // ============================================================
        // EDIT DATA
        // ============================================================
        document.querySelectorAll('.btn-edit').forEach(btn => {
            btn.addEventListener('click', function() {
                const id = this.dataset.id;
                $.ajax({
                    url: 'get_data.php',
                    type: 'GET',
                    data: { id: id },
                    dataType: 'json',
                    success: function(response) {
                        if (response.success) {
                            const d = response.data;
                            document.getElementById('editId').value = d.id;
                            document.getElementById('waktu').value = d.waktu.replace(' ', 'T').slice(0, 16);
                            document.getElementById('alamat').value = d.alamat;
                            document.getElementById('kecamatan').value = d.kecamatan || '';
                            updateKelurahan();
                            document.getElementById('kelurahan').value = d.kelurahan || '';
                            document.getElementById('jumlah_bangunan').value = d.jumlah_bangunan || 0;
                            document.getElementById('jumlah_KK').value = d.jumlah_KK || 0;
                            document.getElementById('jumlah_individu').value = d.jumlah_individu || 0;
                            document.getElementById('korban_luka').value = d.korban_luka || 0;
                            document.getElementById('korban_jiwa').value = d.korban_jiwa || 0;
                            document.getElementById('kerusakan').value = d.kerusakan || '';
                            document.getElementById('keterangan').value = d.keterangan || '';
                            
                            // Penyebab
                            const penyebabSelect = document.getElementById('penyebab');
                            const penyebabLainnya = document.getElementById('penyebab_lainnya');
                            const container = document.getElementById('penyebab_lainnya_container');
                            
                            const options = Array.from(penyebabSelect.options).map(o => o.value);
                            if (d.penyebab && options.includes(d.penyebab)) {
                                penyebabSelect.value = d.penyebab;
                                container.style.display = 'none';
                                penyebabLainnya.value = '';
                            } else if (d.penyebab) {
                                penyebabSelect.value = 'Lainnya';
                                container.style.display = 'block';
                                penyebabLainnya.value = d.penyebab;
                            } else {
                                penyebabSelect.value = '';
                                container.style.display = 'none';
                                penyebabLainnya.value = '';
                            }
                            
                            const lat = parseFloat(d.latitude) || defaultLat;
                            const lng = parseFloat(d.longitude) || defaultLng;
                            initMap(lat, lng);
                            updateCoordinatesFromLatLng(lat, lng);
                            
                            if (d.foto) {
                                document.getElementById('fotoPreview').innerHTML = '<img src="../../uploads/' + d.foto + '" class="image-preview"><p class="text-muted small mt-1">Foto saat ini</p>';
                            } else {
                                document.getElementById('fotoPreview').innerHTML = '';
                            }
                            
                            document.getElementById('modalTitle').innerHTML = 'Edit Data Kejadian';
                            document.getElementById('submitBtn').innerHTML = '<i class="fas fa-save"></i> Update Data';
                            openModal();
                        }
                    }
                });
            });
        });

        // ============================================================
        // SUBMIT FORM (SIMPAN / UPDATE DATA AJAX)
        // ============================================================
        $('#kejadianForm').on('submit', function(e) {
            e.preventDefault();
            
            // Validasi jika penyebab "Lainnya" tapi tidak diisi
            const penyebab = document.getElementById('penyebab').value;
            const penyebabLainnya = document.getElementById('penyebab_lainnya').value;
            if (penyebab === 'Lainnya' && !penyebabLainnya.trim()) {
                Swal.fire({icon: 'warning', text: 'Silakan tulis penyebab kebakaran pada kolom yang tersedia!', customClass: {popup: 'swal2-popup', confirmButton: 'swal2-confirm'}});
                document.getElementById('penyebab_lainnya').focus();
                return;
            }
            
            Swal.fire({ title: 'Menyimpan Data...', allowOutsideClick: false, didOpen: () => { Swal.showLoading(); }});
            
            const formData = new FormData(this);
            $.ajax({
                url: 'save_data.php',
                type: 'POST',
                data: formData,
                processData: false,
                contentType: false,
                dataType: 'json',
                success: function(response) {
                    if (response.success) {
                        Swal.fire({
                            icon: 'success',
                            title: 'Berhasil!',
                            text: 'Data telah tersimpan.',
                            showConfirmButton: false,
                            timer: 1500,
                            customClass: {popup: 'swal2-popup'}
                        }).then(() => location.reload());
                    } else {
                        Swal.fire({icon: 'error', title: 'Error', text: response.message, customClass: {popup: 'swal2-popup', confirmButton: 'swal2-confirm'}});
                    }
                },
                error: function() {
                    Swal.fire({icon: 'error', title: 'Terjadi Kesalahan', text: 'Gagal menghubungi server.', customClass: {popup: 'swal2-popup', confirmButton: 'swal2-confirm'}});
                }
            });
        });

        // ============================================================
        // PREVIEW FOTO
        // ============================================================
        document.getElementById('foto').addEventListener('change', function(e) {
            const preview = document.getElementById('fotoPreview');
            preview.innerHTML = '';
            if (this.files && this.files[0]) {
                const reader = new FileReader();
                reader.onload = function(e) {
                    preview.innerHTML = '<img src="' + e.target.result + '" class="image-preview">';
                };
                reader.readAsDataURL(this.files[0]);
            }
        });

        // ============================================================
        // DROPDOWN USER
        // ============================================================
        document.getElementById('userAvatar').addEventListener('click', function(e) {
            e.stopPropagation();
            document.getElementById('dropdownMenu').classList.toggle('show');
        });
        document.addEventListener('click', function() {
            document.getElementById('dropdownMenu').classList.remove('show');
        });

        // ============================================================
        // INITIALIZE
        // ============================================================
        document.addEventListener('DOMContentLoaded', function() {
            initMapPage();
            initMap(defaultLat, defaultLng);
            updateKelurahan();
        });
    </script>
</body>

</html>