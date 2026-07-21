<?php
require_once __DIR__ . '/../../includes/config.php';
require_once __DIR__ . '/../../includes/session.php';
require_once __DIR__ . '/../../includes/functions.php';

checkAuth();
checkRole(['super_admin']);

$user = getCurrentUser();
$search = isset($_GET['search']) ? $_GET['search'] : '';
$kecamatan_filter = isset($_GET['kecamatan']) ? $_GET['kecamatan'] : '';

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

// Catat log jika user melakukan pencarian atau pemfilteran
if (!empty($search)) {
    catatLog($conn, $user, "Mencari data BPK dengan kata kunci: " . $search);
} elseif (!empty($kecamatan_filter)) {
    catatLog($conn, $user, "Memfilter data BPK berdasarkan kecamatan: " . $kecamatan_filter);
}

// Query BPK
$query = "SELECT b.*, 
          (SELECT COUNT(*) FROM anggota WHERE bpk_id = b.id) as total_anggota,
          (SELECT COUNT(*) FROM anggota WHERE bpk_id = b.id AND status = 'aktif') as anggota_aktif
          FROM bpk b WHERE 1=1";

if (!empty($search)) {
    $query .= " AND (b.nama_bpk LIKE '%" . $conn->real_escape_string($search) . "%' 
                 OR b.nomor_registrasi LIKE '%" . $conn->real_escape_string($search) . "%'
                 OR b.kecamatan LIKE '%" . $conn->real_escape_string($search) . "%'
                 OR b.kelurahan LIKE '%" . $conn->real_escape_string($search) . "%')";
}
if (!empty($kecamatan_filter)) {
    $query .= " AND b.kecamatan = '" . $conn->real_escape_string($kecamatan_filter) . "'";
}
$query .= " ORDER BY b.nomor_registrasi ASC";

$bpk_list = $conn->query($query);
$total_bpk = $bpk_list->num_rows;
$total_anggota_all = $conn->query("SELECT COUNT(*) as t FROM anggota")->fetch_assoc()['t'];

// DATA KECAMATAN DAN KELURAHAN
$kecamatan_list = [
    'Banjarbaru Selatan',
    'Banjarbaru Utara',
    'Cempaka',
    'Landasan Ulin',
    'Liang Anggang'
];

$kelurahan_list = [
    'Banjarbaru Selatan' => ['Guntung Paikat', 'Kemuning', 'Loktabat Selatan', 'Sungai Besar'],
    'Banjarbaru Utara' => ['Komet', 'Loktabat Utara', 'Mentaos', 'Sungai Ulin'],
    'Cempaka' => ['Bangkal', 'Cempaka', 'Palam', 'Sungai Tiung'],
    'Landasan Ulin' => ['Guntung Manggis', 'Guntung Payung', 'Landasan Ulin Timur', 'Syamsudin Noor'],
    'Liang Anggang' => ['Landasan Ulin Barat', 'Landasan Ulin Selatan', 'Landasan Ulin Tengah', 'Landasan Ulin Utara']
];

// Include sidebar
include __DIR__ . '/../../includes/sidebar.php';

$all_bpk_data = [];
if ($bpk_list->num_rows > 0) {
    mysqli_data_seek($bpk_list, 0);
    while ($row = $bpk_list->fetch_assoc()) {
        $all_bpk_data[$row['id']] = $row;
    }
}
$conn->close();

if (!file_exists('../../assets/img/uploads/logo/')) {
    mkdir('../../assets/img/uploads/logo/', 0777, true);
}
?>

<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Data BPK - BARRES 698</title>

    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    
    <!-- Library SweetAlert2 -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/sweetalert2@11/dist/sweetalert2.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
    
    <!-- Penambahan Library Marker Clustering CSS & JS -->
    <link rel="stylesheet" href="https://unpkg.com/leaflet@1.7.1/dist/leaflet.css" />
    <link rel="stylesheet" href="https://unpkg.com/leaflet.markercluster@1.4.1/dist/MarkerCluster.css" />
    <link rel="stylesheet" href="https://unpkg.com/leaflet.markercluster@1.4.1/dist/MarkerCluster.Default.css" />
    
    <script src="https://unpkg.com/leaflet@1.7.1/dist/leaflet.js"></script>
    <script src="https://unpkg.com/leaflet.markercluster@1.4.1/dist/leaflet.markercluster.js"></script>

    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; }
        body {
            font-family: 'Poppins', sans-serif;
            background: #D1D5DB;
            background: linear-gradient(135deg, #E5E7EB 0%, #D1D5DB 100%);
            min-height: 100vh;
        }
        .main-content { margin-left: 280px; padding: 24px 32px; min-height: 100vh; }
        
        .top-navbar {
            background: #FFFFFF;
            border: 1px solid rgba(0,0,0,0.08);
            border-radius: 20px;
            padding: 12px 24px;
            margin-bottom: 28px;
            display: flex;
            justify-content: space-between;
            align-items: center;
        }
        .page-title h2 { font-size: 20px; font-weight: 600; margin: 0; color: #1A1A1A; }
        .page-title p { font-size: 13px; margin: 4px 0 0 0; color: #666; }
        .user-info { text-align: right; }
        .user-info .username { font-size: 14px; font-weight: 600; color: #1A1A1A; }
        .user-info .role { font-size: 11px; color: #F7B801; }
        .user-avatar {
            width: 44px; height: 44px;
            background: linear-gradient(135deg, #F7B801, #E5A800);
            border-radius: 14px;
            display: flex; align-items: center; justify-content: center;
            cursor: pointer; transition: transform 0.2s;
        }
        .user-avatar:hover { transform: scale(1.05); }
        .user-avatar i { font-size: 22px; color: #1A1A1A; }
        
        .dropdown-menu-custom {
            position: absolute; top: 80px; right: 32px;
            background: #FFFFFF; border: 1px solid rgba(0,0,0,0.1);
            box-shadow: 0 4px 12px rgba(0,0,0,0.08);
            border-radius: 16px; padding: 12px 0; min-width: 180px;
            display: none; z-index: 1000;
        }
        .dropdown-menu-custom.show { display: block; animation: fadeIn 0.2s ease; }
        @keyframes fadeIn { from { opacity: 0; transform: translateY(-10px); } to { opacity: 1; transform: translateY(0); } }
        .dropdown-menu-custom a {
            display: flex; align-items: center; gap: 12px;
            padding: 12px 20px; text-decoration: none;
            transition: all 0.2s; font-size: 13px; color: #333;
        }
        .dropdown-menu-custom a:hover { background: rgba(247,184,1,0.1); color: #F7B801; }
        .dropdown-divider { margin: 8px 0; border-color: #E0E0E0; }

        .stat-card {
            background: #FFFFFF;
            border: 1px solid rgba(0,0,0,0.08);
            border-radius: 20px;
            padding: 20px;
            transition: all 0.3s ease;
            text-align: center;
        }
        .stat-card:hover { transform: translateY(-4px); border-color: #F7B801; box-shadow: 0 8px 20px rgba(0,0,0,0.06); }
        .stat-icon {
            width: 55px; height: 55px;
            background: rgba(247,184,1,0.1);
            border-radius: 16px;
            display: flex; align-items: center; justify-content: center;
            margin: 0 auto 12px;
        }
        .stat-icon i { font-size: 28px; color: #F7B801; }
        .stat-number { font-size: 32px; font-weight: 700; margin-bottom: 5px; color: #1A1A1A; }
        .stat-label { font-size: 13px; font-weight: 500; color: #666; }

        .filter-section {
            background: #FFFFFF;
            border: 1px solid rgba(0,0,0,0.08);
            border-radius: 20px;
            padding: 20px 24px;
            margin-bottom: 28px;
        }
        .form-label { color: #1A1A1A; font-weight: 500; font-size: 13px; margin-bottom: 8px; }
        .form-control, .form-select {
            background: #F8F8F8; border: 1px solid #E0E0E0; color: #1A1A1A;
            border-radius: 12px; padding: 10px 14px; font-size: 13px; font-family: 'Poppins', sans-serif;
        }
        .form-control:focus, .form-select:focus { border-color: #F7B801; box-shadow: 0 0 0 3px rgba(247,184,1,0.1); outline: none; }

        .btn-gold {
            background: linear-gradient(135deg, #F7B801, #E5A800);
            border: none; padding: 10px 20px; border-radius: 12px;
            font-weight: 600; font-size: 13px; color: #1A1A1A;
            transition: all 0.3s ease;
        }
        .btn-gold:hover { transform: translateY(-2px); box-shadow: 0 5px 15px rgba(247,184,1,0.3); }
        .btn-outline-gold {
            background: transparent; border: 1px solid rgba(247,184,1,0.4);
            padding: 10px 20px; border-radius: 12px;
            font-weight: 600; font-size: 13px; color: #F7B801;
            transition: all 0.2s;
        }
        .btn-outline-gold:hover { background: rgba(247,184,1,0.1); }

        .card-custom {
            background: #FFFFFF; border: 1px solid rgba(0,0,0,0.08);
            border-radius: 20px; overflow: hidden; margin-bottom: 28px;
        }
        .card-header-custom {
            padding: 18px 24px; display: flex; justify-content: space-between;
            align-items: center; background: #FFFFFF;
            border-bottom: 1px solid rgba(0,0,0,0.08);
        }
        .card-header-custom h3 {
            font-size: 16px; font-weight: 600; margin: 0;
            display: flex; align-items: center; gap: 10px; color: #F7B801;
        }
        .badge-stats { background: rgba(247,184,1,0.1); padding: 5px 12px; border-radius: 20px; font-size: 12px; font-weight: 600; color: #F7B801; }

        /* ===== PETA OSM DI ATAS DAFTAR BPK ===== */
        .map-osm-container {
            height: 400px;
            border-radius: 16px;
            overflow: hidden;
            border: 1px solid rgba(0,0,0,0.1);
        }
        #mapOSM {
            height: 100%;
            width: 100%;
        }

        .table-custom { width: 100%; margin-bottom: 0; color: #1A1A1A; }
        .table-custom thead th {
            padding: 14px 16px; font-size: 13px; font-weight: 600;
            background: #F8F8F8; color: #1A1A1A; border-bottom: 1px solid #E0E0E0;
        }
        .table-custom tbody td {
            padding: 12px 16px; font-size: 13px; vertical-align: middle;
            border-bottom: 1px solid #E0E0E0;
        }
        .table-custom tbody tr:hover { background: rgba(247,184,1,0.03); }

        .reg-badge {
            display: inline-block;
            background: rgba(247,184,1,0.15);
            color: #B8860B;
            padding: 4px 10px;
            border-radius: 20px;
            font-weight: 700;
            font-size: 12px;
        }
        .logo-thumb {
            width: 45px; height: 45px;
            border-radius: 12px;
            object-fit: cover;
            border: 2px solid rgba(247,184,1,0.3);
        }
        .logo-placeholder {
            width: 45px; height: 45px;
            border-radius: 12px;
            background: rgba(247,184,1,0.1);
            display: flex; align-items: center; justify-content: center;
            color: #F7B801; font-weight: bold; font-size: 18px;
        }
        .badge-anggota {
            background: rgba(40,167,69,0.1);
            color: #1e7e34;
            padding: 4px 10px;
            border-radius: 20px;
            font-size: 11px;
        }
        .btn-action {
            background: transparent;
            border: none;
            padding: 6px 10px;
            border-radius: 10px;
            transition: all 0.2s;
        }
        .btn-action i { font-size: 14px; color: #999; }
        .btn-action:hover i { color: #F7B801; }
        .btn-action.danger:hover i { color: #dc3545; }

        .modal-content { border-radius: 20px; overflow: hidden; border: none; }
        .modal-header { padding: 18px 24px; }
        .modal-header .modal-title { font-size: 18px; font-weight: 600; display: flex; align-items: center; gap: 10px; }
        .modal-header-gradient, .modal-header-gradient-edit {
            background: linear-gradient(135deg, #F7B801, #E5A800);
            color: #1A1A1A; border: none;
        }
        .modal-header-gradient .btn-close, .modal-header-gradient-edit .btn-close { filter: brightness(0); }
        .modal-header-gradient-detail {
            background: linear-gradient(135deg, #0D3B4F, #0A2A38);
            color: #F7B801;
        }
        .modal-header-gradient-detail .modal-title { color: #F7B801; }
        .modal-header-gradient-detail .btn-close { filter: brightness(0) invert(1); }

        .logo-preview {
            width: 100px; height: 100px;
            object-fit: contain;
            border-radius: 16px;
            border: 2px solid rgba(247,184,1,0.3);
            background: #F8F8F8;
        }
        .logo-upload-area {
            border: 2px dashed rgba(247,184,1,0.3);
            border-radius: 16px;
            padding: 20px;
            text-align: center;
            cursor: pointer;
            transition: all 0.3s;
        }
        .logo-upload-area:hover { border-color: #F7B801; background: rgba(247,184,1,0.05); }

        .map-container {
            height: 350px;
            border-radius: 16px;
            border: 1px solid rgba(0,0,0,0.1);
            cursor: crosshair;
        }
        .coordinate-input { background: #FFFFFF; font-weight: 600; cursor: text; }
        .detail-label {
            font-weight: 600; font-size: 12px; text-transform: uppercase;
            letter-spacing: 0.5px; color: #F7B801;
        }
        .required:after { content: ' *'; color: #F7B801; }

        .swal2-popup {
            font-family: 'Poppins', sans-serif !important;
            border-radius: 20px !important;
        }
        .swal2-confirm {
            background: linear-gradient(135deg, #F7B801, #E5A800) !important;
            color: #1A1A1A !important;
            border-radius: 12px !important;
            font-weight: 600 !important;
        }
        .swal2-timer-progress-bar {
            background: #F7B801 !important;
        }

        @media (max-width: 768px) {
            .main-content { margin-left: 0; padding: 16px; }
            .card-header-custom { flex-direction: column; gap: 12px; align-items: flex-start; }
            .filter-section .row { flex-direction: column; gap: 12px; }
            .map-osm-container { height: 250px; }
        }
    </style>
</head>

<body>

    <!-- Sidebar sudah di-include -->
    <div class="main-content">
        <div class="top-navbar">
            <div class="page-title">
                <h2>Data BPK</h2>
                <p>Kelola data Badan Penanggulangan Kebakaran</p>
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

        <!-- Stats Cards -->
        <div class="row g-4 mb-4">
            <div class="col-md-3 col-6">
                <div class="stat-card">
                    <div class="stat-icon"><i class="fas fa-building"></i></div>
                    <div class="stat-number"><?= $total_bpk ?></div>
                    <div class="stat-label">Total BPK</div>
                </div>
            </div>
            <div class="col-md-3 col-6">
                <div class="stat-card">
                    <div class="stat-icon"><i class="fas fa-users"></i></div>
                    <div class="stat-number"><?= $total_anggota_all ?></div>
                    <div class="stat-label">Total Anggota</div>
                </div>
            </div>
            <div class="col-md-3 col-6">
                <div class="stat-card">
                    <div class="stat-icon"><i class="fas fa-map-marker-alt"></i></div>
                    <div class="stat-number"><?= count($kecamatan_list) ?></div>
                    <div class="stat-label">Kecamatan</div>
                </div>
            </div>
            <div class="col-md-3 col-6">
                <button class="btn-gold w-100 py-3" onclick="bukaModalTambah()">
                    <i class="fas fa-plus"></i> Tambah BPK
                </button>
            </div>
        </div>

        <!-- Filter Section -->
        <div class="filter-section">
            <form method="GET" action="" class="row g-3 align-items-end">
                <div class="col-md-5">
                    <label class="form-label"><i class="fas fa-search me-1"></i>Cari BPK</label>
                    <input type="text" name="search" class="form-control" placeholder="Nama BPK, Nomor Registrasi, Kecamatan..." value="<?= htmlspecialchars($search) ?>">
                </div>
                <div class="col-md-5">
                    <label class="form-label"><i class="fas fa-filter me-1"></i>Kecamatan</label>
                    <select name="kecamatan" class="form-select">
                        <option value="">Semua Kecamatan</option>
                        <?php foreach ($kecamatan_list as $kec): ?>
                            <option value="<?= $kec ?>" <?= $kecamatan_filter == $kec ? 'selected' : '' ?>><?= $kec ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>
                <div class="col-md-2">
                    <button type="submit" class="btn-gold w-100">
                        <i class="fas fa-filter"></i> Tampilkan
                    </button>
                </div>
            </form>
        </div>

        <!-- ===== PETA OSM DI ATAS DAFTAR BPK ===== -->
        <div class="card-custom">
            <div class="card-header-custom">
                <h3><i class="fas fa-map-marked-alt"></i> Peta Lokasi BPK</h3>
                <span class="badge-stats"><i class="fas fa-map-pin"></i> <?= count($all_bpk_data) ?> BPK</span>
            </div>
            <div class="card-body" style="padding: 20px;">
                <div class="map-osm-container">
                    <div id="mapOSM"></div>
                </div>
                <div class="mt-2 text-muted">
                    <small><i class="fas fa-info-circle"></i> Klik marker untuk melihat detail BPK</small>
                </div>
            </div>
        </div>

        <!-- Table Card -->
        <div class="card-custom">
            <div class="card-header-custom">
                <h3><i class="fas fa-list"></i> Daftar BPK</h3>
                <span class="badge-stats"><i class="fas fa-building"></i> <?= $total_bpk ?> BPK</span>
            </div>
            <div class="card-body-custom p-0">
                <div class="table-responsive">
                    <table class="table-custom">
                        <thead>
                            <tr>
                                <th>No</th>
                                <th>Logo</th>
                                <th>Registrasi</th>
                                <th>Nama BPK</th>
                                <th>Kecamatan</th>
                                <th>Kelurahan</th>
                                <th>Anggota</th>
                                <th>Tahun</th>
                                <th>Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php if (!empty($all_bpk_data)): $no = 1; foreach ($all_bpk_data as $row): ?>
                                <tr id="row-<?= $row['id'] ?>">
                                    <td><?= $no++ ?></td>
                                    <td>
                                        <?php if ($row['logo'] && file_exists('../../assets/img/uploads/logo/' . $row['logo'])): ?>
                                            <img src="../../assets/img/uploads/logo/<?= $row['logo'] ?>" class="logo-thumb">
                                        <?php else: ?>
                                            <div class="logo-placeholder"><?= strtoupper(substr($row['nama_bpk'], 0, 1)) ?></div>
                                        <?php endif; ?>
                                    </td>
                                    <td><span class="reg-badge"><?= $row['nomor_registrasi'] ?></span></td>
                                    <td><strong><?= htmlspecialchars($row['nama_bpk']) ?></strong></td>
                                    <td><?= htmlspecialchars($row['kecamatan']) ?></td>
                                    <td><?= htmlspecialchars($row['kelurahan']) ?></td>
                                    <td><span class="badge-anggota"><?= $row['anggota_aktif'] ?>/<?= $row['total_anggota'] ?></span></td>
                                    <td><?= $row['tahun_berdiri'] ?></td>
                                    <td>
                                        <div class="btn-group">
                                            <button class="btn-action" title="Detail" onclick="bukaModalDetail(<?= $row['id'] ?>)">
                                                <i class="fas fa-eye"></i>
                                            </button>
                                            <button class="btn-action" title="Edit" onclick="bukaModalEdit(<?= $row['id'] ?>)">
                                                <i class="fas fa-edit"></i>
                                            </button>
                                            <button class="btn-action danger" title="Hapus" onclick="hapusBPK(<?= $row['id'] ?>, '<?= htmlspecialchars($row['nama_bpk'], ENT_QUOTES) ?>')">
                                                <i class="fas fa-trash"></i>
                                            </button>
                                        </div>
                                    </td>
                                </tr>
                            <?php endforeach; else: ?>
                                <tr>
                                    <td colspan="9" class="text-center py-5">
                                        <i class="fas fa-building fa-3x mb-3 d-block" style="color: #999;"></i>
                                        <p class="mb-0">Belum ada data BPK</p>
                                        <button class="btn-gold mt-3" onclick="bukaModalTambah()" style="display: inline-flex;">
                                            <i class="fas fa-plus"></i> Tambah BPK Pertama
                                        </button>
                                    </td>
                                </tr>
                            <?php endif; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>

    <!-- MODAL TAMBAH -->
    <div class="modal fade" id="modalTambah" tabindex="-1" data-bs-backdrop="static">
        <div class="modal-dialog modal-xl modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header modal-header-gradient">
                    <h5 class="modal-title"><i class="fas fa-plus-circle"></i>Tambah BPK</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <form id="formTambah" enctype="multipart/form-data" action="save_bpk.php" method="POST">
                    <div class="modal-body">
                        <div class="alert alert-info alert-dismissible fade show" role="alert" style="border-radius: 12px; border-left: 4px solid #F7B801;">
                            <i class="fas fa-info-circle me-2" style="color: #F7B801;"></i>
                            <strong>Informasi:</strong> Setelah BPK ditambahkan, akan otomatis dibuatkan akun Admin BPK dengan:
                            <br>
                            <strong>Username:</strong> <code>admin_bpk_[nomor_registrasi]</code> &nbsp;|&nbsp; 
                            <strong>Password default:</strong> <code>admin123</code>
                            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                        </div>
                        <div class="row">
                            <div class="col-md-6">
                                <div class="row">
                                    <div class="col-md-4 mb-3">
                                        <label class="form-label required">No Registrasi</label>
                                        <input type="text" name="nomor_registrasi" class="form-control" placeholder="001" maxlength="3" oninput="formatRegistrasi(this)" required>
                                        <small class="text-muted">3 digit (001, 002, dst)</small>
                                    </div>
                                    <div class="col-md-8 mb-3">
                                        <label class="form-label required">Nama BPK</label>
                                        <input type="text" name="nama_bpk" class="form-control" placeholder="Nama BPK" required>
                                    </div>
                                </div>
                                <div class="mb-3">
                                    <label class="form-label">Alamat Lengkap</label>
                                    <textarea name="alamat" class="form-control" rows="2" placeholder="Alamat lengkap"></textarea>
                                </div>
                                <div class="row">
                                    <div class="col-md-6 mb-3">
                                        <label class="form-label required">Kecamatan</label>
                                        <select name="kecamatan" class="form-select" id="kecamatanTambah" required onchange="updateKelurahan('kecamatanTambah', 'kelurahanTambah')">
                                            <option value="">Pilih Kecamatan</option>
                                            <?php foreach ($kecamatan_list as $kec): ?>
                                                <option value="<?= $kec ?>"><?= $kec ?></option>
                                            <?php endforeach; ?>
                                        </select>
                                    </div>
                                    <div class="col-md-6 mb-3">
                                        <label class="form-label required">Kelurahan</label>
                                        <select name="kelurahan" class="form-select" id="kelurahanTambah" required>
                                            <option value="">Pilih Kecamatan dulu</option>
                                        </select>
                                    </div>
                                </div>
                                <div class="row">
                                    <div class="col-md-6 mb-3">
                                        <label class="form-label required">Latitude</label>
                                        <!-- readonly DIHAPUS, DITAMBAHKAN ONINPUT -->
                                        <input type="text" name="latitude" class="form-control coordinate-input" id="latTambah" placeholder="Ketik atau klik peta..." oninput="updateMarkerTambah()" required>
                                    </div>
                                    <div class="col-md-6 mb-3">
                                        <label class="form-label required">Longitude</label>
                                        <!-- readonly DIHAPUS, DITAMBAHKAN ONINPUT -->
                                        <input type="text" name="longitude" class="form-control coordinate-input" id="lngTambah" placeholder="Ketik atau klik peta..." oninput="updateMarkerTambah()" required>
                                    </div>
                                </div>
                                <div class="row">
                                    <div class="col-md-6 mb-3">
                                        <label class="form-label">Tahun Berdiri <span style="color: #F7B801;">(2000-2030)</span></label>
                                        <input type="number" name="tahun_berdiri" class="form-control" placeholder="2000" min="2000" max="2030" value="2000">
                                    </div>
                                    <div class="col-md-6 mb-3">
                                        <label class="form-label">Logo BPK</label>
                                        <div class="logo-upload-area" onclick="document.getElementById('logoTambah').click()">
                                            <i class="fas fa-upload fa-2x text-muted d-block mb-1"></i>
                                            <small>Klik untuk upload logo</small>
                                            <input type="file" name="logo" id="logoTambah" accept="image/*" style="display:none;" onchange="previewLogo(this, 'previewLogoTambah')">
                                        </div>
                                        <img id="previewLogoTambah" class="logo-preview mt-2 d-block mx-auto" style="display:none;">
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label"><i class="fas fa-map-marker-alt me-1"></i>Pilih lokasi BPK</label>
                                <div id="mapTambah" class="map-container"></div>
                                <small class="text-muted mt-1 d-block">
                                    <i class="fas fa-info-circle"></i> Anda bisa mengetik koordinat secara manual atau mengklik langsung pada peta.
                                </small>
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn-outline-gold" data-bs-dismiss="modal">Batal</button>
                        <button type="submit" class="btn-gold" id="submitTambah"><i class="fas fa-save me-1"></i> Simpan BPK</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- MODAL EDIT -->
    <div class="modal fade" id="modalEdit" tabindex="-1" data-bs-backdrop="static">
        <div class="modal-dialog modal-xl modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header modal-header-gradient-edit">
                    <h5 class="modal-title"><i class="fas fa-edit"></i>Edit BPK</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <form id="formEdit" enctype="multipart/form-data" action="save_bpk.php" method="POST">
                    <input type="hidden" name="id" id="edit_id">
                    <input type="hidden" name="logo_lama" id="edit_logo_lama">
                    <div class="modal-body" id="editContent"></div>
                    <div class="modal-footer">
                        <button type="button" class="btn-outline-gold" data-bs-dismiss="modal">Batal</button>
                        <button type="submit" class="btn-gold" id="submitEdit"><i class="fas fa-save me-1"></i> Update BPK</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- MODAL DETAIL -->
    <div class="modal fade" id="modalDetail" tabindex="-1">
        <div class="modal-dialog modal-lg modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header modal-header-gradient-detail">
                    <h5 class="modal-title"><i class="fas fa-building"></i>Detail BPK</h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body" id="detailContent"></div>
                <div class="modal-footer">
                    <button type="button" class="btn-outline-gold" data-bs-dismiss="modal">Tutup</button>
                </div>
            </div>
        </div>
    </div>

    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

    <script>
        // Data dari PHP
        const bpkData = <?= json_encode($all_bpk_data) ?>;
        const kelurahanData = <?= json_encode($kelurahan_list) ?>;

        let mapTambah, markerTambah;
        let mapEdit, markerEdit;

        // ============================================================
        // PETA OSM DI ATAS DAFTAR BPK
        // ============================================================
        function initMapOSM() {
            const mapOSM = L.map('mapOSM').setView([-3.468, 114.832], 12);
            
            L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
                attribution: '&copy; <a href="https://www.openstreetmap.org/copyright">OpenStreetMap</a> contributors',
                maxZoom: 19
            }).addTo(mapOSM);
            
            // Inisialisasi Marker Cluster Group
            const markersCluster = L.markerClusterGroup({
                maxClusterRadius: 50,
                iconCreateFunction: function(cluster) {
                    const count = cluster.getChildCount();
                    const size = count < 10 ? 40 : (count < 50 ? 50 : 60);
                    return L.divIcon({
                        html: `<div style="background:rgba(247,184,1,0.9);border-radius:50%;width:${size}px;height:${size}px;display:flex;align-items:center;justify-content:center;font-weight:700;font-size:${size < 50 ? 14 : 12}px;color:#0D0D0D;border:2px solid #F7B801;box-shadow:0 4px 15px rgba(247,184,1,0.3);">${count}</div>`,
                        className: '',
                        iconSize: [size, size],
                        iconAnchor: [size/2, size/2]
                    });
                }
            });
            
            const bpkArray = Object.values(bpkData);
            let hasMarker = false;
            
            bpkArray.forEach(function(bpk) {
                if (bpk.latitude && bpk.longitude && bpk.latitude != 0 && bpk.longitude != 0) {
                    hasMarker = true;
                    const lat = parseFloat(bpk.latitude);
                    const lng = parseFloat(bpk.longitude);
                    
                    const marker = L.marker([lat, lng], {
                        icon: L.divIcon({
                            className: 'custom-marker',
                            html: `<div style="background: #F7B801; color: #1A1A1A; border-radius: 50%; width: 32px; height: 32px; display: flex; align-items: center; justify-content: center; font-weight: bold; font-size: 12px; border: 2px solid white; box-shadow: 0 2px 8px rgba(0,0,0,0.3);">${bpk.nomor_registrasi}</div>`,
                            iconSize: [32, 32],
                            iconAnchor: [16, 16]
                        })
                    });
                    
                    marker.bindPopup(`
                        <div style="font-family: 'Poppins', sans-serif; font-size: 13px;">
                            <strong style="color: #F7B801;">${escapeHtml(bpk.nama_bpk)}</strong><br>
                            <small>Reg: ${bpk.nomor_registrasi}</small><br>
                            ${escapeHtml(bpk.kecamatan || '')} - ${escapeHtml(bpk.kelurahan || '')}<br>
                            <a href="#" onclick="bukaModalDetail(${bpk.id})" style="color: #F7B801; text-decoration: none;">
                                <i class="fas fa-eye"></i> Lihat Detail
                            </a>
                        </div>
                    `);
                    
                    // Tambahkan marker ke dalam cluster
                    markersCluster.addLayer(marker);
                }
            });
            
            // Tambahkan cluster group ke map
            mapOSM.addLayer(markersCluster);
            
            if (hasMarker) {
                // Fit bounds ke semua marker
                const bounds = [];
                bpkArray.forEach(function(bpk) {
                    if (bpk.latitude && bpk.longitude && bpk.latitude != 0 && bpk.longitude != 0) {
                        bounds.push([parseFloat(bpk.latitude), parseFloat(bpk.longitude)]);
                    }
                });
                if (bounds.length > 0) {
                    mapOSM.fitBounds(bounds, { padding: [50, 50] });
                }
            } else {
                // Tampilkan pesan jika tidak ada marker
                mapOSM.setView([-3.468, 114.832], 12);
            }
        }

        // ============================================================
        // FUNGSI UTILITY
        // ============================================================
        function formatRegistrasi(input) {
            input.value = input.value.replace(/[^0-9]/g, '').substring(0, 3);
        }

        function updateKelurahan(kecId, kelId) {
            const kec = document.getElementById(kecId).value;
            const kelSelect = document.getElementById(kelId);
            kelSelect.innerHTML = '<option value="">Pilih Kelurahan</option>';
            if (kec && kelurahanData[kec]) {
                kelurahanData[kec].forEach(kel => {
                    kelSelect.innerHTML += `<option value="${kel}">${kel}</option>`;
                });
            }
        }

        function previewLogo(input, previewId) {
            const preview = document.getElementById(previewId);
            if (input.files && input.files[0]) {
                const reader = new FileReader();
                reader.onload = function(e) {
                    preview.src = e.target.result;
                    preview.style.display = 'block';
                }
                reader.readAsDataURL(input.files[0]);
            }
        }

        function escapeHtml(str) {
            if (!str) return '';
            return str.replace(/[&<>]/g, function(m) {
                if (m === '&') return '&amp;';
                if (m === '<') return '&lt;';
                if (m === '>') return '&gt;';
                return m;
            });
        }

        // ============================================================
        // MAP FUNCTIONS (Modal)
        // ============================================================
        function initMapTambah() {
            if (mapTambah) {
                mapTambah.invalidateSize();
                return;
            }

            mapTambah = L.map('mapTambah').setView([-3.468, 114.832], 13);
            L.tileLayer('https://{s}.basemaps.cartocdn.com/light_all/{z}/{x}/{y}{r}.png', {
                attribution: '© OpenStreetMap',
                maxZoom: 19
            }).addTo(mapTambah);

            mapTambah.on('click', function(e) {
                const lat = e.latlng.lat.toFixed(8);
                const lng = e.latlng.lng.toFixed(8);

                document.getElementById('latTambah').value = lat;
                document.getElementById('lngTambah').value = lng;

                if (markerTambah) {
                    markerTambah.setLatLng(e.latlng);
                } else {
                    markerTambah = L.marker(e.latlng, { draggable: true }).addTo(mapTambah);
                    markerTambah.on('dragend', function() {
                        const pos = markerTambah.getLatLng();
                        document.getElementById('latTambah').value = pos.lat.toFixed(8);
                        document.getElementById('lngTambah').value = pos.lng.toFixed(8);
                    });
                }
            });
        }
        
        // Update Marker jika diketik manual (Tambah)
        function updateMarkerTambah() {
            if (!mapTambah) return;
            const lat = parseFloat(document.getElementById('latTambah').value);
            const lng = parseFloat(document.getElementById('lngTambah').value);
            
            if (!isNaN(lat) && !isNaN(lng)) {
                const newLatLng = new L.LatLng(lat, lng);
                if (markerTambah) {
                    markerTambah.setLatLng(newLatLng);
                } else {
                    markerTambah = L.marker(newLatLng, { draggable: true }).addTo(mapTambah);
                    markerTambah.on('dragend', function() {
                        const pos = markerTambah.getLatLng();
                        document.getElementById('latTambah').value = pos.lat.toFixed(8);
                        document.getElementById('lngTambah').value = pos.lng.toFixed(8);
                    });
                }
                mapTambah.panTo(newLatLng);
            }
        }
        
        // Update Marker jika diketik manual (Edit)
        function updateMarkerEdit() {
            if (!mapEdit) return;
            const lat = parseFloat(document.getElementById('latEdit').value);
            const lng = parseFloat(document.getElementById('lngEdit').value);
            
            if (!isNaN(lat) && !isNaN(lng)) {
                const newLatLng = new L.LatLng(lat, lng);
                if (markerEdit) {
                    markerEdit.setLatLng(newLatLng);
                } else {
                    markerEdit = L.marker(newLatLng, { draggable: true }).addTo(mapEdit);
                    markerEdit.on('dragend', function() {
                        const pos = markerEdit.getLatLng();
                        document.getElementById('latEdit').value = pos.lat.toFixed(8);
                        document.getElementById('lngEdit').value = pos.lng.toFixed(8);
                    });
                }
                mapEdit.panTo(newLatLng);
            }
        }

        // ============================================================
        // MODAL OPEN FUNCTIONS
        // ============================================================
        function bukaModalTambah() {
            document.getElementById('formTambah').reset();
            document.getElementById('kelurahanTambah').innerHTML = '<option value="">Pilih Kecamatan dulu</option>';
            document.getElementById('latTambah').value = '';
            document.getElementById('lngTambah').value = '';
            document.getElementById('previewLogoTambah').style.display = 'none';

            const modal = new bootstrap.Modal(document.getElementById('modalTambah'));
            modal.show();

            document.getElementById('modalTambah').addEventListener('shown.bs.modal', function() {
                setTimeout(() => {
                    initMapTambah();
                    if (markerTambah) {
                        mapTambah.removeLayer(markerTambah);
                        markerTambah = null;
                    }
                }, 200);
            }, { once: true });
        }

        function bukaModalEdit(id) {
            const data = bpkData[id];
            if (!data) return;

            document.getElementById('edit_id').value = id;
            document.getElementById('edit_logo_lama').value = data.logo || '';

            let kelOptions = '';
            if (data.kecamatan && kelurahanData[data.kecamatan]) {
                kelurahanData[data.kecamatan].forEach(kel => {
                    kelOptions += `<option value="${kel}" ${data.kelurahan == kel ? 'selected' : ''}>${kel}</option>`;
                });
            }

            // Pada template form edit ini readonly DIHAPUS, ONINPUT DITAMBAHKAN
            document.getElementById('editContent').innerHTML = `
                <div class="row">
                    <div class="col-md-6">
                        <div class="row">
                            <div class="col-md-4 mb-3">
                                <label class="form-label required">No Registrasi</label>
                                <input type="text" name="nomor_registrasi" class="form-control" value="${data.nomor_registrasi}" required oninput="formatRegistrasi(this)">
                            </div>
                            <div class="col-md-8 mb-3">
                                <label class="form-label required">Nama BPK</label>
                                <input type="text" name="nama_bpk" class="form-control" value="${escapeHtml(data.nama_bpk)}" required>
                            </div>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Alamat</label>
                            <textarea name="alamat" class="form-control" rows="2">${escapeHtml(data.alamat || '')}</textarea>
                        </div>
                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label class="form-label required">Kecamatan</label>
                                <select name="kecamatan" class="form-select" id="kecamatanEdit" required onchange="updateKelurahan('kecamatanEdit', 'kelurahanEdit')">
                                    <option value="">Pilih</option>
                                    <?php foreach ($kecamatan_list as $kec): ?>
                                        <option value="<?= $kec ?>"><?= $kec ?></option>
                                    <?php endforeach; ?>
                                </select>
                            </div>
                            <div class="col-md-6 mb-3">
                                <label class="form-label required">Kelurahan</label>
                                <select name="kelurahan" class="form-select" id="kelurahanEdit" required>
                                    ${kelOptions}
                                </select>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label class="form-label required">Latitude</label>
                                <input type="text" name="latitude" class="form-control coordinate-input" id="latEdit" value="${data.latitude || ''}" oninput="updateMarkerEdit()" required>
                            </div>
                            <div class="col-md-6 mb-3">
                                <label class="form-label required">Longitude</label>
                                <input type="text" name="longitude" class="form-control coordinate-input" id="lngEdit" value="${data.longitude || ''}" oninput="updateMarkerEdit()" required>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label class="form-label">Tahun Berdiri <span style="color: #F7B801;">(2000-2030)</span></label>
                                <input type="number" name="tahun_berdiri" class="form-control" value="${data.tahun_berdiri || 2000}" min="2000" max="2030">
                            </div>
                            <div class="col-md-6 mb-3">
                                <label class="form-label">Logo BPK</label>
                                <div class="logo-upload-area" onclick="document.getElementById('logoEdit').click()">
                                    <i class="fas fa-upload fa-2x text-muted d-block mb-1"></i>
                                    <small>Ganti logo</small>
                                    <input type="file" name="logo" id="logoEdit" accept="image/*" style="display:none;" onchange="previewLogo(this, 'previewLogoEdit')">
                                </div>
                                ${data.logo ? `<img src="../../assets/img/uploads/logo/${data.logo}" class="logo-preview mt-2 d-block mx-auto">` : ''}
                                <img id="previewLogoEdit" class="logo-preview mt-2 d-block mx-auto" style="display:none;">
                            </div>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <label class="form-label">Klik peta untuk update koordinat</label>
                        <div id="mapEdit" class="map-container"></div>
                        <small class="text-muted mt-1 d-block">Marker bisa di-drag atau koordinat bisa diketik manual.</small>
                    </div>
                </div>
            `;

            const modal = new bootstrap.Modal(document.getElementById('modalEdit'));
            modal.show();

            document.getElementById('modalEdit').addEventListener('shown.bs.modal', function() {
                setTimeout(() => {
                    document.getElementById('kecamatanEdit').value = data.kecamatan || '';
                    const lat = parseFloat(data.latitude) || -3.468;
                    const lng = parseFloat(data.longitude) || 114.832;

                    if (mapEdit) mapEdit.remove();
                    mapEdit = L.map('mapEdit').setView([lat, lng], 14);
                    L.tileLayer('https://{s}.basemaps.cartocdn.com/light_all/{z}/{x}/{y}{r}.png', {
                        maxZoom: 19
                    }).addTo(mapEdit);

                    if (data.latitude && data.longitude) {
                        markerEdit = L.marker([lat, lng], { draggable: true }).addTo(mapEdit);
                        markerEdit.on('dragend', function() {
                            const pos = markerEdit.getLatLng();
                            document.getElementById('latEdit').value = pos.lat.toFixed(8);
                            document.getElementById('lngEdit').value = pos.lng.toFixed(8);
                        });
                    }

                    mapEdit.on('click', function(e) {
                        const lat = e.latlng.lat.toFixed(8);
                        const lng = e.latlng.lng.toFixed(8);
                        document.getElementById('latEdit').value = lat;
                        document.getElementById('lngEdit').value = lng;
                        if (markerEdit) {
                            markerEdit.setLatLng(e.latlng);
                        } else {
                            markerEdit = L.marker(e.latlng, { draggable: true }).addTo(mapEdit);
                            markerEdit.on('dragend', function() {
                                const pos = markerEdit.getLatLng();
                                document.getElementById('latEdit').value = pos.lat.toFixed(8);
                                document.getElementById('lngEdit').value = pos.lng.toFixed(8);
                            });
                        }
                    });
                    mapEdit.invalidateSize();
                }, 300);
            }, { once: true });
        }

        function bukaModalDetail(id) {
            const data = bpkData[id];
            if (!data) return;

            document.getElementById('detailContent').innerHTML = `
                <div class="row">
                    <div class="col-md-5 text-center">
                        ${data.logo ? 
                            `<img src="../../assets/img/uploads/logo/${data.logo}" class="logo-preview mb-3" style="width:120px;height:120px;">` : 
                            `<div class="logo-placeholder mx-auto mb-3" style="width:120px;height:120px;font-size:48px;">${data.nama_bpk.charAt(0).toUpperCase()}</div>`
                        }
                        <div class="reg-badge mb-2" style="font-size:20px;padding:8px 18px;">${data.nomor_registrasi}</div>
                        <h5 class="fw-bold" style="color:#F7B801;">${escapeHtml(data.nama_bpk)}</h5>
                        <hr>
                        <table class="table table-borderless table-sm text-start">
                            <tr><td class="detail-label">Alamat</td><td class="detail-value">: ${escapeHtml(data.alamat || '-')}</td></tr>
                            <tr><td class="detail-label">Kecamatan</td><td class="detail-value">: ${escapeHtml(data.kecamatan || '-')}</td></tr>
                            <tr><td class="detail-label">Kelurahan</td><td class="detail-value">: ${escapeHtml(data.kelurahan || '-')}</td></tr>
                            <tr><td class="detail-label">Koordinat</td><td class="detail-value">: ${data.latitude}, ${data.longitude}</td></tr>
                            <tr><td class="detail-label">Tahun Berdiri</td><td class="detail-value">: ${data.tahun_berdiri || '-'}</td></tr>
                            <tr><td class="detail-label">Anggota Aktif</td><td class="detail-value">: <span class="badge-anggota">${data.anggota_aktif || 0}</span></td></tr>
                            <tr><td class="detail-label">Total Anggota</td><td class="detail-value">: <span style="color:#28a745;">${data.total_anggota || 0}</span></td></tr>
                        </table>
                    </div>
                    <div class="col-md-7">
                        <label class="form-label"><i class="fas fa-map-marker-alt me-1"></i> Lokasi pada Peta</label>
                        <div id="mapDetail" class="map-container"></div>
                    </div>
                </div>
            `;

            const modal = new bootstrap.Modal(document.getElementById('modalDetail'));
            modal.show();

            document.getElementById('modalDetail').addEventListener('shown.bs.modal', function() {
                setTimeout(() => {
                    const lat = parseFloat(data.latitude) || -3.468;
                    const lng = parseFloat(data.longitude) || 114.832;
                    const mapDetail = L.map('mapDetail').setView([lat, lng], 15);
                    L.tileLayer('https://{s}.basemaps.cartocdn.com/light_all/{z}/{x}/{y}{r}.png', {
                        maxZoom: 19
                    }).addTo(mapDetail);
                    if (data.latitude && data.longitude) {
                        L.marker([lat, lng]).addTo(mapDetail).bindPopup(`<strong>${escapeHtml(data.nama_bpk)}</strong>`).openPopup();
                    }
                    mapDetail.invalidateSize();
                }, 300);
            }, { once: true });
        }

        // ============================================================
        // HAPUS BPK
        // ============================================================
        function hapusBPK(id, nama) {
            Swal.fire({
                title: 'Yakin hapus?',
                html: `<strong>${escapeHtml(nama)}</strong> akan dihapus permanen!<br>Akun Admin BPK juga akan ikut terhapus.`,
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#F7B801',
                cancelButtonColor: '#6c757d',
                confirmButtonText: 'Ya, Hapus!',
                cancelButtonText: 'Batal',
                reverseButtons: true,
                customClass: {
                    popup: 'swal2-popup',
                    confirmButton: 'swal2-confirm'
                }
            }).then((result) => {
                if (result.isConfirmed) {
                    Swal.fire({
                        title: 'Menghapus...',
                        allowOutsideClick: false,
                        didOpen: () => { Swal.showLoading(); }
                    });
                    
                    const formData = new FormData();
                    formData.append('action', 'hapus');
                    formData.append('id', id);
                    
                    fetch('save_bpk.php', {
                        method: 'POST',
                        body: formData
                    })
                    .then(res => {
                        const contentType = res.headers.get('content-type');
                        if (!contentType || !contentType.includes('application/json')) {
                            throw new Error('Server error: ' + res.status);
                        }
                        return res.json();
                    })
                    .then(data => {
                        if (data.success) {
                            Swal.fire({
                                icon: 'success',
                                title: 'Terhapus!',
                                text: data.message,
                                timer: 2000,
                                timerProgressBar: true,
                                showConfirmButton: true,
                                customClass: {
                                    popup: 'swal2-popup',
                                    confirmButton: 'swal2-confirm'
                                }
                            }).then(() => location.reload());
                        } else {
                            Swal.fire({
                                icon: 'error',
                                title: 'Gagal!',
                                text: data.message,
                                customClass: {
                                    popup: 'swal2-popup',
                                    confirmButton: 'swal2-confirm'
                                }
                            });
                        }
                    })
                    .catch(error => {
                        Swal.fire({
                            icon: 'error',
                            title: 'Error!',
                            text: 'Terjadi kesalahan: ' + error.message,
                            customClass: {
                                popup: 'swal2-popup',
                                confirmButton: 'swal2-confirm'
                            }
                        });
                    });
                }
            });
        }

        // ============================================================
        // SUBMIT FORM TAMBAH
        // ============================================================
        document.getElementById('formTambah').addEventListener('submit', function(e) {
            e.preventDefault();
            
            const lat = document.getElementById('latTambah').value;
            const lng = document.getElementById('lngTambah').value;
            if (!lat || !lng) {
                Swal.fire({
                    icon: 'warning',
                    title: 'Perhatian!',
                    text: 'Silakan isi koordinat secara manual atau klik pada peta!',
                    customClass: {
                        popup: 'swal2-popup',
                        confirmButton: 'swal2-confirm'
                    }
                });
                return;
            }
            
            // Validasi tahun berdiri
            const tahun = parseInt(document.querySelector('input[name="tahun_berdiri"]').value);
            if (isNaN(tahun) || tahun < 2000 || tahun > 2030) {
                Swal.fire({
                    icon: 'warning',
                    title: 'Perhatian!',
                    text: 'Tahun berdiri harus antara 2000 - 2030!',
                    customClass: {
                        popup: 'swal2-popup',
                        confirmButton: 'swal2-confirm'
                    }
                });
                return;
            }
            
            Swal.fire({
                title: 'Menyimpan Data...',
                text: 'Mohon tunggu sebentar',
                allowOutsideClick: false,
                didOpen: () => { Swal.showLoading(); },
                customClass: {
                    popup: 'swal2-popup'
                }
            });
            
            const formData = new FormData(this);
            formData.append('action', 'tambah');
            
            fetch('save_bpk.php', {
                method: 'POST',
                body: formData
            })
            .then(res => {
                const contentType = res.headers.get('content-type');
                if (!contentType || !contentType.includes('application/json')) {
                    throw new Error('Server error: ' + res.status);
                }
                return res.json();
            })
            .then(data => {
                if (data.success) {
                    Swal.fire({
                        icon: 'success',
                        title: 'Sukses!',
                        html: data.message,
                        timer: 3000,
                        timerProgressBar: true,
                        showConfirmButton: true,
                        customClass: {
                            popup: 'swal2-popup',
                            confirmButton: 'swal2-confirm'
                        }
                    }).then(() => location.reload());
                } else {
                    Swal.fire({
                        icon: 'error',
                        title: 'Error!',
                        text: data.message,
                        customClass: {
                            popup: 'swal2-popup',
                            confirmButton: 'swal2-confirm'
                        }
                    });
                }
            })
            .catch(error => {
                console.error('Error:', error);
                Swal.fire({
                    icon: 'error',
                    title: 'Error!',
                    text: error.message || 'Terjadi kesalahan pada server. Silakan coba lagi.',
                    customClass: {
                        popup: 'swal2-popup',
                        confirmButton: 'swal2-confirm'
                    }
                });
            });
        });

        // ============================================================
        // SUBMIT FORM EDIT
        // ============================================================
        document.getElementById('formEdit').addEventListener('submit', function(e) {
            e.preventDefault();
            
            const lat = document.getElementById('latEdit').value;
            const lng = document.getElementById('lngEdit').value;
            if (!lat || !lng) {
                Swal.fire({
                    icon: 'warning',
                    title: 'Perhatian!',
                    text: 'Silakan isi koordinat secara manual atau klik pada peta!',
                    customClass: {
                        popup: 'swal2-popup',
                        confirmButton: 'swal2-confirm'
                    }
                });
                return;
            }
            
            // Validasi tahun berdiri
            const tahun = parseInt(document.querySelector('input[name="tahun_berdiri"]').value);
            if (isNaN(tahun) || tahun < 2000 || tahun > 2030) {
                Swal.fire({
                    icon: 'warning',
                    title: 'Perhatian!',
                    text: 'Tahun berdiri harus antara 2000 - 2030!',
                    customClass: {
                        popup: 'swal2-popup',
                        confirmButton: 'swal2-confirm'
                    }
                });
                return;
            }
            
            Swal.fire({
                title: 'Mengupdate Data...',
                text: 'Mohon tunggu sebentar',
                allowOutsideClick: false,
                didOpen: () => { Swal.showLoading(); },
                customClass: {
                    popup: 'swal2-popup'
                }
            });
            
            const formData = new FormData(this);
            formData.append('action', 'edit');
            
            fetch('save_bpk.php', {
                method: 'POST',
                body: formData
            })
            .then(res => {
                const contentType = res.headers.get('content-type');
                if (!contentType || !contentType.includes('application/json')) {
                    throw new Error('Server error: ' + res.status);
                }
                return res.json();
            })
            .then(data => {
                if (data.success) {
                    Swal.fire({
                        icon: 'success',
                        title: 'Sukses!',
                        text: data.message,
                        timer: 2000,
                        timerProgressBar: true,
                        showConfirmButton: true,
                        customClass: {
                            popup: 'swal2-popup',
                            confirmButton: 'swal2-confirm'
                        }
                    }).then(() => location.reload());
                } else {
                    Swal.fire({
                        icon: 'error',
                        title: 'Error!',
                        text: data.message,
                        customClass: {
                            popup: 'swal2-popup',
                            confirmButton: 'swal2-confirm'
                        }
                    });
                }
            })
            .catch(error => {
                console.error('Error:', error);
                Swal.fire({
                    icon: 'error',
                    title: 'Error!',
                    text: error.message || 'Terjadi kesalahan pada server. Silakan coba lagi.',
                    customClass: {
                        popup: 'swal2-popup',
                        confirmButton: 'swal2-confirm'
                    }
                });
            });
        });

        // ============================================================
        // DROPDOWN
        // ============================================================
        document.getElementById('userAvatar').addEventListener('click', function(e) {
            e.stopPropagation();
            document.getElementById('dropdownMenu').classList.toggle('show');
        });
        document.addEventListener('click', function() {
            document.getElementById('dropdownMenu').classList.remove('show');
        });

        // ============================================================
        // INITIALIZE - Peta OSM di atas daftar BPK
        // ============================================================
        document.addEventListener('DOMContentLoaded', function() {
            setTimeout(function() {
                initMapOSM();
            }, 300);
        });
    </script>
</body>

</html>