<?php
// adminbpk/bpk/index.php
require_once __DIR__ . '/../../includes/config.php';
require_once __DIR__ . '/../../includes/session.php';
require_once __DIR__ . '/../../includes/functions.php';

checkAuth();
checkRole(['admin_bpk']);

$bpk_id = $_SESSION['bpk_id'];
$user = getCurrentUser(); // Mengambil info user yang sedang login

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

// Ambil data BPK saat ini
$query_bpk = "SELECT * FROM bpk WHERE id = $bpk_id";
$result_bpk = $conn->query($query_bpk);
$bpk = $result_bpk->fetch_assoc();

if (!$bpk) {
    die("Data BPK tidak ditemukan");
}

// ============================================================
// PROSES AJAX UNTUK UPDATE PROFIL & SAPRAS
// ============================================================
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['ajax_action'])) {
    header('Content-Type: application/json');
    $response = ['success' => false, 'message' => 'Terjadi kesalahan sistem'];
    
    if ($_POST['ajax_action'] == 'update_profil') {
        $alamat = $conn->real_escape_string($_POST['alamat']);
        $kecamatan = $conn->real_escape_string($_POST['kecamatan']);
        $kelurahan = $conn->real_escape_string($_POST['kelurahan']);
        $latitude = $conn->real_escape_string($_POST['latitude']);
        $longitude = $conn->real_escape_string($_POST['longitude']);
        $tahun_berdiri = (int)$_POST['tahun_berdiri'];

        // Upload logo baru jika ada
        $logo_name = $bpk['logo'];
        if (isset($_FILES['logo']) && $_FILES['logo']['error'] == 0) {
            $upload = uploadFile($_FILES['logo'], '../../assets/img/uploads/logo/');
            if ($upload['success']) {
                if ($logo_name && file_exists('../../assets/img/uploads/logo/' . $logo_name)) {
                    unlink('../../assets/img/uploads/logo/' . $logo_name);
                }
                $logo_name = $upload['filename'];
            }
        }

        $update = "UPDATE bpk SET 
            alamat = '$alamat',
            kecamatan = '$kecamatan',
            kelurahan = '$kelurahan',
            latitude = '$latitude',
            longitude = '$longitude',
            tahun_berdiri = $tahun_berdiri,
            logo = '$logo_name'
            WHERE id = $bpk_id";

        if ($conn->query($update)) {
            catatLog($conn, $user, "Memperbarui Profil Organisasi BPK: " . $bpk['nama_bpk']);
            $response = ['success' => true, 'message' => 'Profil BPK berhasil diperbarui!'];
        } else {
            $response = ['success' => false, 'message' => 'Gagal memperbarui profil: ' . $conn->error];
        }
        echo json_encode($response);
        exit;
    }
    
    if ($_POST['ajax_action'] == 'update_sapras') {
        $fields = [
            'mobil_tangki', 'mobil_portabel', 'mesin_pompa', 
            'selang_1_5_inc', 'selang_2_5_inc', 'selang_isap', 
            'nozle', 'helm_apd', 'baju_apd', 'celana_apd', 'sepatu_apd'
        ];
        
        $vals = [];
        foreach($fields as $f) {
            $vals[$f] = (int)$_POST[$f];
        }
        
        $check = $conn->query("SELECT id FROM sapras_bpk WHERE bpk_id = $bpk_id");
        
        if($check->num_rows > 0) {
            $update_str = [];
            foreach($vals as $k => $v) { $update_str[] = "$k = $v"; }
            $sql = "UPDATE sapras_bpk SET " . implode(', ', $update_str) . " WHERE bpk_id = $bpk_id";
        } else {
            $sql = "INSERT INTO sapras_bpk (bpk_id, " . implode(', ', $fields) . ") 
                    VALUES ($bpk_id, " . implode(', ', $vals) . ")";
        }
        
        if ($conn->query($sql)) {
            catatLog($conn, $user, "Memperbarui data inventaris SAPRAS milik BPK: " . $bpk['nama_bpk']);
            $response = ['success' => true, 'message' => 'Data Sarana & Prasarana berhasil diperbarui!'];
        } else {
            $response = ['success' => false, 'message' => 'Gagal memperbarui SAPRAS: ' . $conn->error];
        }
        echo json_encode($response);
        exit;
    }
}

// Ambil data Sarana dan Prasarana terbaru
$query_sapras = "SELECT * FROM sapras_bpk WHERE bpk_id = $bpk_id";
$result_sapras = $conn->query($query_sapras);
$sapras = $result_sapras->fetch_assoc();

if (!$sapras) {
    $sapras = [
        'mobil_tangki' => 0, 'mobil_portabel' => 0, 'mesin_pompa' => 0,
        'selang_1_5_inc' => 0, 'selang_2_5_inc' => 0, 'selang_isap' => 0,
        'nozle' => 0, 'helm_apd' => 0, 'baju_apd' => 0, 'celana_apd' => 0, 'sepatu_apd' => 0
    ];
}

$conn->close();

// Include sidebar dari folder includes
include __DIR__ . '/../../includes/sidebar.php';
?>

<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Profil BPK - BARRES 698</title>

    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
    
    <!-- SweetAlert2 CSS -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/sweetalert2@11/dist/sweetalert2.min.css">

    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; }
        body {
            font-family: 'Poppins', sans-serif; background: #D1D5DB;
            background: linear-gradient(135deg, #E5E7EB 0%, #D1D5DB 100%); min-height: 100vh;
        }
        .main-content { margin-left: 280px; padding: 24px 32px; min-height: 100vh; }

        /* Top Navbar */
        .top-navbar {
            background: #FFFFFF; border: 1px solid rgba(0, 0, 0, 0.08); border-radius: 20px;
            padding: 12px 24px; margin-bottom: 28px; display: flex; justify-content: space-between; align-items: center;
        }
        .page-title h2 { font-size: 20px; font-weight: 600; margin: 0; color: #1A1A1A; }
        .page-title p { font-size: 13px; margin: 4px 0 0 0; color: #666; }
        .user-info { text-align: right; }
        .user-info .username { font-size: 14px; font-weight: 600; color: #1A1A1A; }
        .user-info .role { font-size: 11px; color: #F7B801; }
        .user-avatar {
            width: 44px; height: 44px; background: linear-gradient(135deg, #F7B801, #E5A800);
            border-radius: 14px; display: flex; align-items: center; justify-content: center;
            cursor: pointer; transition: transform 0.2s;
        }
        .user-avatar:hover { transform: scale(1.05); }
        .user-avatar i { font-size: 22px; color: #1A1A1A; }
        .dropdown-menu-custom {
            position: absolute; top: 80px; right: 32px; background: #FFFFFF; border: 1px solid rgba(0, 0, 0, 0.1);
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.08); border-radius: 16px; padding: 12px 0; min-width: 180px;
            display: none; z-index: 1000;
        }
        .dropdown-menu-custom.show { display: block; animation: fadeIn 0.2s ease; }
        @keyframes fadeIn { from { opacity: 0; transform: translateY(-10px); } to { opacity: 1; transform: translateY(0); } }
        .dropdown-menu-custom a {
            display: flex; align-items: center; gap: 12px; padding: 12px 20px;
            text-decoration: none; transition: all 0.2s; font-size: 13px; color: #333;
        }
        .dropdown-menu-custom a:hover { background: rgba(247, 184, 1, 0.1); color: #F7B801; }
        .dropdown-divider { margin: 8px 0; border-color: #E0E0E0; }

        /* Cards */
        .card-custom {
            background: #FFFFFF; border: 1px solid rgba(0, 0, 0, 0.08);
            border-radius: 20px; overflow: hidden; margin-bottom: 28px;
        }
        .card-header-custom {
            padding: 18px 24px; display: flex; justify-content: space-between; align-items: center;
            background: #FFFFFF; border-bottom: 1px solid rgba(0, 0, 0, 0.08);
        }
        .card-header-custom h3 {
            font-size: 16px; font-weight: 600; margin: 0; display: flex; align-items: center; gap: 10px; color: #F7B801;
        }

        /* Buttons */
        .btn-gold {
            background: linear-gradient(135deg, #F7B801, #E5A800); border: none; padding: 10px 20px;
            border-radius: 12px; font-weight: 600; font-size: 13px; color: #1A1A1A; transition: all 0.3s ease;
        }
        .btn-gold:hover { transform: translateY(-2px); box-shadow: 0 5px 15px rgba(247, 184, 1, 0.3); color: #1A1A1A; }
        .btn-outline-gold {
            background: transparent; border: 1px solid rgba(247, 184, 1, 0.4); padding: 8px 16px;
            border-radius: 12px; font-weight: 600; font-size: 12px; color: #F7B801; transition: all 0.2s;
        }
        .btn-outline-gold:hover { background: rgba(247, 184, 1, 0.1); color: #F7B801; }

        /* Form */
        .form-label { color: #1A1A1A; font-weight: 500; font-size: 13px; margin-bottom: 8px; }
        .form-control, .form-select {
            background: #F8F8F8; border: 1px solid #E0E0E0; color: #1A1A1A; border-radius: 12px;
            padding: 10px 14px; font-size: 13px;
        }
        .form-control:focus, .form-select:focus { border-color: #F7B801; box-shadow: 0 0 0 3px rgba(247, 184, 1, 0.1); outline: none; }
        .form-control:disabled { background: #F0F0F0; color: #888; cursor: not-allowed; }

        /* Info Box */
        .info-box { background: #FEF9E6; border-radius: 12px; padding: 15px; border-left: 4px solid #F7B801; color: #1A1A1A; }

        /* Profile Image */
        .profile-logo { width: 150px; height: 150px; object-fit: cover; border-radius: 16px; border: 2px solid #F7B801; background: #FFFFFF; }
        .profile-logo-placeholder {
            width: 150px; height: 150px; background: rgba(247, 184, 1, 0.1); border-radius: 16px;
            display: flex; align-items: center; justify-content: center; font-size: 48px; color: #F7B801;
        }

        /* Modal */
        .modal-content { border-radius: 20px; overflow: hidden; border: none; }
        .modal-header-gradient { background: linear-gradient(135deg, #F7B801, #E5A800); color: #1A1A1A; border: none; }
        .modal-header-gradient .btn-close { filter: brightness(0); }
        .required:after { content: ' *'; color: #F7B801; }
        .preview-img { max-width: 100px; border-radius: 8px; margin-top: 8px; border: 1px solid #ddd; }

        /* Sapras Item Grid */
        .sapras-item {
            background: #F8F9FA; border: 1px solid #E9ECEF; border-radius: 12px; padding: 16px;
            display: flex; align-items: center; gap: 15px; height: 100%; transition: all 0.3s;
        }
        .sapras-item:hover { border-color: #F7B801; transform: translateY(-2px); box-shadow: 0 4px 10px rgba(0,0,0,0.05); }
        .sapras-icon {
            width: 48px; height: 48px; background: rgba(247, 184, 1, 0.15); color: #F7B801;
            border-radius: 12px; display: flex; align-items: center; justify-content: center; font-size: 20px; flex-shrink: 0;
        }
        .sapras-details h6 { margin: 0; font-size: 13px; font-weight: 600; color: #6c757d; }
        .sapras-details .jumlah { margin: 0; font-size: 20px; font-weight: 700; color: #1A1A1A; }

        /* Custom SweetAlert */
        .swal2-popup { font-family: 'Poppins', sans-serif !important; border-radius: 20px !important; }
        .swal2-confirm { border-radius: 12px !important; font-weight: 600 !important; }

        @media (max-width: 768px) {
            .main-content { margin-left: 0; padding: 16px; }
        }
    </style>
</head>

<body>
    <!-- Main Content -->
    <div class="main-content">
        <div class="top-navbar">
            <div class="page-title">
                <h2>Profil BPK</h2>
                <p>Kelola data organisasi dan inventaris Sarana Prasarana (SAPRAS)</p>
            </div>
            <div class="user-dropdown" style="display: flex; align-items: center; gap: 15px;">
                <div class="user-info">
                    <div class="username"><?= htmlspecialchars($_SESSION['username']) ?></div>
                    <div class="role">Admin BPK</div>
                </div>
                <div class="user-avatar" id="userAvatar">
                    <i class="fas fa-user"></i>
                </div>
            </div>
        </div>

        <div class="dropdown-menu-custom" id="dropdownMenu">
            <a href="../dashboard.php">
                <i class="fas fa-tachometer-alt"></i>
                <span>Dashboard</span>
            </a>
            <div class="dropdown-divider"></div>
            <a href="../../logout.php">
                <i class="fas fa-sign-out-alt"></i>
                <span>Logout</span>
            </a>
        </div>

        <!-- Info Peringatan -->
        <div class="info-box mb-4">
            <i class="fas fa-info-circle me-2" style="color: #F7B801;"></i>
            <strong>Informasi:</strong> Nomor Registrasi dan Nama BPK tidak dapat diubah. Jika ada perubahan identitas, silakan hubungi Super Admin.
        </div>

        <!-- Profil BPK -->
        <div class="card-custom">
            <div class="card-header-custom">
                <h3><i class="fas fa-building"></i> Data Organisasi</h3>
                <button type="button" class="btn-gold" data-bs-toggle="modal" data-bs-target="#modalEditProfil">
                    <i class="fas fa-pen"></i> Edit Profil
                </button>
            </div>
            <div class="card-body p-4">
                <div class="row">
                    <div class="col-md-3 text-center">
                        <?php
                        $logo_path = '../../assets/img/uploads/logo/' . $bpk['logo'];
                        if ($bpk['logo'] && file_exists($logo_path)):
                        ?>
                            <img src="../../assets/img/uploads/logo/<?= $bpk['logo'] ?>" class="profile-logo">
                        <?php else: ?>
                            <div class="profile-logo-placeholder">
                                <i class="fas fa-fire-extinguisher"></i>
                            </div>
                        <?php endif; ?>
                    </div>
                    <div class="col-md-9">
                        <div class="row mt-3 mt-md-0">
                            <div class="col-md-6 mb-3">
                                <strong style="color: #6c757d; font-size: 12px;">Nomor Registrasi</strong><br>
                                <span style="font-size: 15px; font-weight: 600;"><?= htmlspecialchars($bpk['nomor_registrasi'] ?? '-') ?></span>
                            </div>
                            <div class="col-md-6 mb-3">
                                <strong style="color: #6c757d; font-size: 12px;">Nama BPK</strong><br>
                                <span style="font-size: 15px; font-weight: 600;"><?= htmlspecialchars($bpk['nama_bpk']) ?></span>
                            </div>
                            <div class="col-md-12 mb-3">
                                <strong style="color: #6c757d; font-size: 12px;">Alamat Lengkap</strong><br>
                                <span style="font-size: 15px; font-weight: 500;"><?= htmlspecialchars($bpk['alamat'] ?? '-') ?></span>
                            </div>
                            <div class="col-md-6 mb-3">
                                <strong style="color: #6c757d; font-size: 12px;">Kecamatan / Kelurahan</strong><br>
                                <span style="font-size: 15px; font-weight: 500;"><?= htmlspecialchars($bpk['kecamatan'] ?? '-') ?> / <?= htmlspecialchars($bpk['kelurahan'] ?? '-') ?></span>
                            </div>
                            <div class="col-md-6 mb-3">
                                <strong style="color: #6c757d; font-size: 12px;">Koordinat Peta (Lat, Lng)</strong><br>
                                <span style="font-size: 15px; font-family: monospace;"><?= $bpk['latitude'] ?? '-' ?>, <?= $bpk['longitude'] ?? '-' ?></span>
                            </div>
                            <div class="col-md-6 mb-3">
                                <strong style="color: #6c757d; font-size: 12px;">Tahun Berdiri</strong><br>
                                <span style="font-size: 15px; font-weight: 500;"><?= $bpk['tahun_berdiri'] ?? '-' ?></span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Sarana dan Prasarana Section -->
        <div class="card-custom">
            <div class="card-header-custom">
                <h3><i class="fas fa-toolbox"></i> Inventaris Sarana & Prasarana</h3>
                <button type="button" class="btn-gold" data-bs-toggle="modal" data-bs-target="#modalEditSapras">
                    <i class="fas fa-pen"></i> Update SAPRAS
                </button>
            </div>
            <div class="card-body p-4">
                
                <h6 class="mb-3" style="color: #F7B801; font-weight: 700;"><i class="fas fa-truck-fire me-2"></i>Kendaraan Pemadam</h6>
                <div class="row g-3 mb-4">
                    <div class="col-md-3 col-sm-6">
                        <div class="sapras-item">
                            <div class="sapras-icon"><i class="fas fa-truck-front"></i></div>
                            <div class="sapras-details">
                                <h6>Mobil Tangki</h6>
                                <p class="jumlah"><?= $sapras['mobil_tangki'] ?></p>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-3 col-sm-6">
                        <div class="sapras-item">
                            <div class="sapras-icon"><i class="fas fa-truck-pickup"></i></div>
                            <div class="sapras-details">
                                <h6>Mobil Portabel</h6>
                                <p class="jumlah"><?= $sapras['mobil_portabel'] ?></p>
                            </div>
                        </div>
                    </div>
                </div>

                <h6 class="mb-3" style="color: #0056b3; font-weight: 700;"><i class="fas fa-water me-2"></i>Peralatan Air & Penyemprotan</h6>
                <div class="row g-3 mb-4">
                    <div class="col-md-3 col-sm-6">
                        <div class="sapras-item">
                            <div class="sapras-icon" style="color: #0056b3; background: rgba(0, 123, 255, 0.15);"><i class="fas fa-fan"></i></div>
                            <div class="sapras-details">
                                <h6>Mesin Pompa</h6>
                                <p class="jumlah"><?= $sapras['mesin_pompa'] ?></p>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-3 col-sm-6">
                        <div class="sapras-item">
                            <div class="sapras-icon" style="color: #0056b3; background: rgba(0, 123, 255, 0.15);"><i class="fas fa-bacon"></i></div>
                            <div class="sapras-details">
                                <h6>Selang 1.5 Inch</h6>
                                <p class="jumlah"><?= $sapras['selang_1_5_inc'] ?></p>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-3 col-sm-6">
                        <div class="sapras-item">
                            <div class="sapras-icon" style="color: #0056b3; background: rgba(0, 123, 255, 0.15);"><i class="fas fa-bacon"></i></div>
                            <div class="sapras-details">
                                <h6>Selang 2.5 Inch</h6>
                                <p class="jumlah"><?= $sapras['selang_2_5_inc'] ?></p>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-3 col-sm-6">
                        <div class="sapras-item">
                            <div class="sapras-icon" style="color: #0056b3; background: rgba(0, 123, 255, 0.15);"><i class="fas fa-circle-notch"></i></div>
                            <div class="sapras-details">
                                <h6>Selang Isap</h6>
                                <p class="jumlah"><?= $sapras['selang_isap'] ?></p>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-3 col-sm-6">
                        <div class="sapras-item">
                            <div class="sapras-icon" style="color: #0056b3; background: rgba(0, 123, 255, 0.15);"><i class="fas fa-spray-can"></i></div>
                            <div class="sapras-details">
                                <h6>Nozle</h6>
                                <p class="jumlah"><?= $sapras['nozle'] ?></p>
                            </div>
                        </div>
                    </div>
                </div>

                <h6 class="mb-3" style="color: #1e7e34; font-weight: 700;"><i class="fas fa-user-shield me-2"></i>Alat Pelindung Diri (APD)</h6>
                <div class="row g-3">
                    <div class="col-md-3 col-sm-6">
                        <div class="sapras-item">
                            <div class="sapras-icon" style="color: #1e7e34; background: rgba(40, 167, 69, 0.15);"><i class="fas fa-hard-hat"></i></div>
                            <div class="sapras-details">
                                <h6>Helm APD</h6>
                                <p class="jumlah"><?= $sapras['helm_apd'] ?></p>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-3 col-sm-6">
                        <div class="sapras-item">
                            <div class="sapras-icon" style="color: #1e7e34; background: rgba(40, 167, 69, 0.15);"><i class="fas fa-shirt"></i></div>
                            <div class="sapras-details">
                                <h6>Baju APD</h6>
                                <p class="jumlah"><?= $sapras['baju_apd'] ?></p>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-3 col-sm-6">
                        <div class="sapras-item">
                            <div class="sapras-icon" style="color: #1e7e34; background: rgba(40, 167, 69, 0.15);"><i class="fas fa-socks"></i></div>
                            <div class="sapras-details">
                                <h6>Celana APD</h6>
                                <p class="jumlah"><?= $sapras['celana_apd'] ?></p>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-3 col-sm-6">
                        <div class="sapras-item">
                            <div class="sapras-icon" style="color: #1e7e34; background: rgba(40, 167, 69, 0.15);"><i class="fas fa-shoe-prints"></i></div>
                            <div class="sapras-details">
                                <h6>Sepatu APD</h6>
                                <p class="jumlah"><?= $sapras['sepatu_apd'] ?></p>
                            </div>
                        </div>
                    </div>
                </div>

            </div>
        </div>
    </div>

    <!-- Modal Edit Profil BPK -->
    <div class="modal fade" id="modalEditProfil" tabindex="-1">
        <div class="modal-dialog modal-lg modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header modal-header-gradient">
                    <h5 class="modal-title"><i class="fas fa-edit"></i> Edit Profil Organisasi</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <!-- Gunakan ID form khusus ajax -->
                <form id="formProfilAjax">
                    <input type="hidden" name="ajax_action" value="update_profil">
                    <div class="modal-body">
                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label class="form-label">Nomor Registrasi</label>
                                <input type="text" class="form-control" value="<?= htmlspecialchars($bpk['nomor_registrasi'] ?? '-') ?>" disabled>
                            </div>
                            <div class="col-md-6 mb-3">
                                <label class="form-label">Nama BPK</label>
                                <input type="text" class="form-control" value="<?= htmlspecialchars($bpk['nama_bpk']) ?>" disabled>
                            </div>
                            <div class="col-md-12 mb-3">
                                <label class="form-label">Alamat Lengkap</label>
                                <input type="text" name="alamat" class="form-control" value="<?= htmlspecialchars($bpk['alamat'] ?? '') ?>">
                            </div>
                            <div class="col-md-6 mb-3">
                                <label class="form-label">Kecamatan</label>
                                <input type="text" name="kecamatan" class="form-control" value="<?= htmlspecialchars($bpk['kecamatan'] ?? '') ?>">
                            </div>
                            <div class="col-md-6 mb-3">
                                <label class="form-label">Kelurahan</label>
                                <input type="text" name="kelurahan" class="form-control" value="<?= htmlspecialchars($bpk['kelurahan'] ?? '') ?>">
                            </div>
                            <div class="col-md-6 mb-3">
                                <label class="form-label">Latitude</label>
                                <input type="text" name="latitude" class="form-control" value="<?= htmlspecialchars($bpk['latitude'] ?? '') ?>" placeholder="-3.45236090">
                            </div>
                            <div class="col-md-6 mb-3">
                                <label class="form-label">Longitude</label>
                                <input type="text" name="longitude" class="form-control" value="<?= htmlspecialchars($bpk['longitude'] ?? '') ?>" placeholder="114.84423233">
                            </div>
                            <div class="col-md-6 mb-3">
                                <label class="form-label">Tahun Berdiri</label>
                                <input type="number" name="tahun_berdiri" class="form-control" value="<?= $bpk['tahun_berdiri'] ?? '' ?>">
                            </div>
                            <div class="col-md-6 mb-3">
                                <label class="form-label">Logo Organisasi</label>
                                <input type="file" name="logo" class="form-control" accept="image/*">
                                <small class="text-muted">Kosongkan jika tidak ingin mengganti logo</small>
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn-outline-gold" data-bs-dismiss="modal">Batal</button>
                        <button type="submit" class="btn-gold"><i class="fas fa-save"></i> Simpan Perubahan</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- Modal Edit SAPRAS -->
    <div class="modal fade" id="modalEditSapras" tabindex="-1">
        <div class="modal-dialog modal-lg modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header modal-header-gradient">
                    <h5 class="modal-title"><i class="fas fa-toolbox"></i> Edit Inventaris Sarana & Prasarana</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <form id="formSaprasAjax">
                    <input type="hidden" name="ajax_action" value="update_sapras">
                    <div class="modal-body p-4">
                        <div class="alert alert-info" style="border-radius: 12px; font-size: 13px;">
                            <i class="fas fa-info-circle me-2"></i> Silakan isi dengan jumlah unit (angka) yang tersedia saat ini.
                        </div>

                        <h6 class="mt-4 mb-3" style="color: #F7B801; font-weight: 600;">1. Kendaraan</h6>
                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label class="form-label">Mobil Tangki</label>
                                <input type="number" name="mobil_tangki" class="form-control" min="0" value="<?= $sapras['mobil_tangki'] ?>">
                            </div>
                            <div class="col-md-6 mb-3">
                                <label class="form-label">Mobil Portabel</label>
                                <input type="number" name="mobil_portabel" class="form-control" min="0" value="<?= $sapras['mobil_portabel'] ?>">
                            </div>
                        </div>
                        <hr>

                        <h6 class="mt-4 mb-3" style="color: #0056b3; font-weight: 600;">2. Peralatan Air</h6>
                        <div class="row">
                            <div class="col-md-4 mb-3">
                                <label class="form-label">Mesin Pompa</label>
                                <input type="number" name="mesin_pompa" class="form-control" min="0" value="<?= $sapras['mesin_pompa'] ?>">
                            </div>
                            <div class="col-md-4 mb-3">
                                <label class="form-label">Selang 1.5 Inch</label>
                                <input type="number" name="selang_1_5_inc" class="form-control" min="0" value="<?= $sapras['selang_1_5_inc'] ?>">
                            </div>
                            <div class="col-md-4 mb-3">
                                <label class="form-label">Selang 2.5 Inch</label>
                                <input type="number" name="selang_2_5_inc" class="form-control" min="0" value="<?= $sapras['selang_2_5_inc'] ?>">
                            </div>
                            <div class="col-md-6 mb-3">
                                <label class="form-label">Selang Isap</label>
                                <input type="number" name="selang_isap" class="form-control" min="0" value="<?= $sapras['selang_isap'] ?>">
                            </div>
                            <div class="col-md-6 mb-3">
                                <label class="form-label">Nozle</label>
                                <input type="number" name="nozle" class="form-control" min="0" value="<?= $sapras['nozle'] ?>">
                            </div>
                        </div>
                        <hr>

                        <h6 class="mt-4 mb-3" style="color: #1e7e34; font-weight: 600;">3. Alat Pelindung Diri (APD)</h6>
                        <div class="row">
                            <div class="col-md-3 mb-3">
                                <label class="form-label">Helm APD</label>
                                <input type="number" name="helm_apd" class="form-control" min="0" value="<?= $sapras['helm_apd'] ?>">
                            </div>
                            <div class="col-md-3 mb-3">
                                <label class="form-label">Baju APD</label>
                                <input type="number" name="baju_apd" class="form-control" min="0" value="<?= $sapras['baju_apd'] ?>">
                            </div>
                            <div class="col-md-3 mb-3">
                                <label class="form-label">Celana APD</label>
                                <input type="number" name="celana_apd" class="form-control" min="0" value="<?= $sapras['celana_apd'] ?>">
                            </div>
                            <div class="col-md-3 mb-3">
                                <label class="form-label">Sepatu APD</label>
                                <input type="number" name="sepatu_apd" class="form-control" min="0" value="<?= $sapras['sepatu_apd'] ?>">
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn-outline-gold" data-bs-dismiss="modal">Batal</button>
                        <button type="submit" class="btn-gold"><i class="fas fa-save"></i> Simpan Data</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

    <script>
        // Dropdown Toggle
        document.getElementById('userAvatar').addEventListener('click', function(e) {
            e.stopPropagation();
            document.getElementById('dropdownMenu').classList.toggle('show');
        });
        document.addEventListener('click', function() {
            document.getElementById('dropdownMenu').classList.remove('show');
        });

        // ============================================================
        // SUBMIT AJAX UNTUK PROFIL ORGANISASI
        // ============================================================
        $('#formProfilAjax').on('submit', function(e) {
            e.preventDefault();
            const formData = new FormData(this);
            
            Swal.fire({
                title: 'Menyimpan Profil...',
                text: 'Mohon tunggu sebentar',
                allowOutsideClick: false,
                didOpen: () => { Swal.showLoading(); },
                customClass: { popup: 'swal2-popup' }
            });
            
            $.ajax({
                url: '', // Submit ke file ini sendiri
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
                            text: response.message,
                            timer: 2000,
                            showConfirmButton: false,
                            customClass: { popup: 'swal2-popup' }
                        }).then(() => location.reload());
                    } else {
                        Swal.fire({icon: 'error', title: 'Gagal', text: response.message, customClass: { popup: 'swal2-popup', confirmButton: 'swal2-confirm' }});
                    }
                },
                error: function() {
                    Swal.fire({icon: 'error', title: 'Error', text: 'Terjadi kesalahan jaringan!', customClass: { popup: 'swal2-popup', confirmButton: 'swal2-confirm' }});
                }
            });
        });

        // ============================================================
        // SUBMIT AJAX UNTUK SAPRAS
        // ============================================================
        $('#formSaprasAjax').on('submit', function(e) {
            e.preventDefault();
            const formData = new FormData(this);
            
            Swal.fire({
                title: 'Menyimpan Inventaris SAPRAS...',
                text: 'Mohon tunggu sebentar',
                allowOutsideClick: false,
                didOpen: () => { Swal.showLoading(); },
                customClass: { popup: 'swal2-popup' }
            });
            
            $.ajax({
                url: '', // Submit ke file ini sendiri
                type: 'POST',
                data: formData,
                processData: false,
                contentType: false,
                dataType: 'json',
                success: function(response) {
                    if (response.success) {
                        Swal.fire({
                            icon: 'success',
                            title: 'Tersimpan!',
                            text: response.message,
                            timer: 2000,
                            showConfirmButton: false,
                            customClass: { popup: 'swal2-popup' }
                        }).then(() => location.reload());
                    } else {
                        Swal.fire({icon: 'error', title: 'Gagal', text: response.message, customClass: { popup: 'swal2-popup', confirmButton: 'swal2-confirm' }});
                    }
                },
                error: function() {
                    Swal.fire({icon: 'error', title: 'Error', text: 'Terjadi kesalahan server saat menyimpan sapras.', customClass: { popup: 'swal2-popup', confirmButton: 'swal2-confirm' }});
                }
            });
        });
    </script>
</body>

</html>