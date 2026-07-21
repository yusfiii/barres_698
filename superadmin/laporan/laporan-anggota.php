<?php
require_once __DIR__ . '/../../includes/config.php';
require_once __DIR__ . '/../../includes/session.php';
require_once __DIR__ . '/../../includes/functions.php';
require_once __DIR__ . '/../../includes/pdf-helper.php';

checkAuth();
checkRole(['super_admin']);

$user = getCurrentUser();

// Filter parameters
$filter_bpk = isset($_GET['bpk_id']) ? (int)$_GET['bpk_id'] : 0;

$conn = getConnection();

// List BPK untuk dropdown filter
$bpk_list = $conn->query("SELECT id, nama_bpk FROM bpk ORDER BY nama_bpk ASC");

// Dapatkan nama BPK jika difilter
$nama_bpk_filter = '';
if ($filter_bpk > 0) {
    $b = $conn->query("SELECT nama_bpk FROM bpk WHERE id = $filter_bpk")->fetch_assoc();
    $nama_bpk_filter = $b ? $b['nama_bpk'] : '';
}

// Ambil data Anggota berdasarkan filter BPK
$query = "SELECT a.*, b.nama_bpk 
          FROM anggota a
          LEFT JOIN bpk b ON a.bpk_id = b.id 
          WHERE 1=1";
$params = [];
$types = "";

if ($filter_bpk > 0) {
    $query .= " AND a.bpk_id = ?";
    $params[] = $filter_bpk;
    $types .= "i";
}

$query .= " ORDER BY b.nama_bpk ASC, a.nomor_anggota ASC";

$stmt = $conn->prepare($query);
if (!empty($params)) {
    $stmt->bind_param($types, ...$params);
}
$stmt->execute();
$result = $stmt->get_result();
$daftar_anggota = $result->fetch_all(MYSQLI_ASSOC);
$stmt->close();

$conn->close();

// Include sidebar
include __DIR__ . '/../../includes/sidebar.php';
?>

<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Laporan Anggota - BARRES 698</title>

    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">

    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: 'Poppins', sans-serif;
            background: #D1D5DB;
            background: linear-gradient(135deg, #E5E7EB 0%, #D1D5DB 100%);
            min-height: 100vh;
        }

        .main-content {
            margin-left: 280px;
            padding: 24px 32px;
            min-height: 100vh;
        }

        /* Top Navbar */
        .top-navbar {
            background: #FFFFFF;
            border: 1px solid rgba(0, 0, 0, 0.08);
            border-radius: 20px;
            padding: 12px 24px;
            margin-bottom: 28px;
            display: flex;
            justify-content: space-between;
            align-items: center;
        }

        .page-title h2 {
            font-size: 20px;
            font-weight: 600;
            margin: 0;
            color: #1A1A1A;
        }

        .page-title p {
            font-size: 13px;
            margin: 4px 0 0 0;
            color: #666;
        }

        .user-info {
            text-align: right;
        }

        .user-info .username {
            font-size: 14px;
            font-weight: 600;
            color: #1A1A1A;
        }

        .user-info .role {
            font-size: 11px;
            color: #F7B801;
        }

        .user-avatar {
            width: 44px;
            height: 44px;
            background: linear-gradient(135deg, #F7B801, #E5A800);
            border-radius: 14px;
            display: flex;
            align-items: center;
            justify-content: center;
            cursor: pointer;
            transition: transform 0.2s;
        }

        .user-avatar:hover {
            transform: scale(1.05);
        }

        .user-avatar i {
            font-size: 22px;
            color: #1A1A1A;
        }

        .dropdown-menu-custom {
            position: absolute;
            top: 80px;
            right: 32px;
            background: #FFFFFF;
            border: 1px solid rgba(0, 0, 0, 0.1);
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.08);
            border-radius: 16px;
            padding: 12px 0;
            min-width: 180px;
            display: none;
            z-index: 1000;
        }

        .dropdown-menu-custom.show {
            display: block;
            animation: fadeIn 0.2s ease;
        }

        @keyframes fadeIn {
            from {
                opacity: 0;
                transform: translateY(-10px);
            }
            to {
                opacity: 1;
                transform: translateY(0);
            }
        }

        .dropdown-menu-custom a {
            display: flex;
            align-items: center;
            gap: 12px;
            padding: 12px 20px;
            text-decoration: none;
            transition: all 0.2s;
            font-size: 13px;
            color: #333;
        }

        .dropdown-menu-custom a:hover {
            background: rgba(247, 184, 1, 0.1);
            color: #F7B801;
        }

        .dropdown-divider {
            margin: 8px 0;
            border-color: #E0E0E0;
        }

        /* Filter Section */
        .filter-section {
            background: #FFFFFF;
            border: 1px solid rgba(0, 0, 0, 0.08);
            border-radius: 20px;
            padding: 20px 24px;
            margin-bottom: 28px;
        }

        .form-label {
            font-size: 13px;
            font-weight: 600;
            margin-bottom: 8px;
            color: #1A1A1A;
        }

        .form-control,
        .form-select {
            background: #F8F8F8;
            border: 1px solid #E0E0E0;
            color: #1A1A1A;
            border-radius: 12px;
            padding: 10px 14px;
            font-size: 13px;
            font-family: 'Poppins', sans-serif;
        }

        .form-control:focus,
        .form-select:focus {
            border-color: #F7B801;
            box-shadow: 0 0 0 3px rgba(247, 184, 1, 0.1);
            outline: none;
        }

        .btn-gold {
            background: linear-gradient(135deg, #F7B801, #E5A800);
            border: none;
            padding: 10px 20px;
            border-radius: 12px;
            font-weight: 600;
            font-size: 13px;
            color: #1A1A1A;
            transition: all 0.3s ease;
        }

        .btn-gold:hover {
            transform: translateY(-2px);
            box-shadow: 0 5px 15px rgba(247, 184, 1, 0.3);
            color: #1A1A1A;
        }

        .btn-pdf-custom {
            background: rgba(220, 53, 69, 0.1);
            border: 1px solid rgba(220, 53, 69, 0.3);
            padding: 10px 20px;
            border-radius: 12px;
            font-weight: 600;
            font-size: 13px;
            color: #dc3545;
            transition: all 0.2s;
            text-decoration: none;
            display: inline-flex;
            align-items: center;
            gap: 6px;
        }

        .btn-pdf-custom:hover {
            background: rgba(220, 53, 69, 0.2);
            color: #dc3545;
        }

        /* Preview Container untuk A4 */
        .preview-container {
            background: #FFFFFF;
            border-radius: 20px;
            padding: 40px 50px;
            border: 1px solid rgba(0, 0, 0, 0.08);
            box-shadow: 0 4px 20px rgba(0, 0, 0, 0.02);
            max-width: 210mm;
            margin: 0 auto;
        }

        /* ==================== STYLE LAPORAN ==================== */
        .laporan {
            font-family: 'Arial', sans-serif !important;
            font-size: 10pt;
            color: #000000;
            line-height: 1.6;
        }

        .laporan table,
        .laporan td,
        .laporan div,
        .laporan p,
        .laporan span,
        .laporan h1,
        .laporan h2,
        .laporan h3,
        .laporan h4 {
            font-family: 'Arial', sans-serif !important;
        }

        /* No Data */
        .no-data {
            text-align: center;
            padding: 40px 20px;
            color: #999;
        }

        .no-data i {
            font-size: 48px;
            color: #ddd;
            display: block;
            margin-bottom: 15px;
        }

        .no-data p {
            font-size: 16px;
            color: #666;
        }

        /* Responsive */
        @media (max-width: 768px) {
            .main-content {
                margin-left: 0;
                padding: 16px;
            }
            .filter-section .row {
                flex-direction: column;
                gap: 12px;
            }
        }

        /* Print - A4 */
        @media print {
            .sidebar, .top-navbar, .dropdown-menu-custom, .user-avatar, .filter-section, .no-print {
                display: none !important;
            }
            .main-content {
                margin-left: 0 !important;
                padding: 0 !important;
            }
        }

        <?= pdfPreviewCss() ?>

        /* ==================== PERBAIKAN TAMPILAN KOP SURAT (PREVIEW WEB) ==================== */
        .preview-container .kop-surat {
            display: flex !important;
            flex-direction: row !important;
            align-items: center !important;
            justify-content: center !important;
            border-bottom: 3px solid #000 !important;
            padding-bottom: 15px !important;
            margin-bottom: 20px !important;
            position: relative !important;
        }

        .preview-container .kop-surat img {
            position: static !important;
            height: 75px !important;
            width: auto !important;
            margin-right: 20px !important;
            transform: none !important;
        }

        .preview-container .kop-surat .kop-text,
        .preview-container .kop-surat div {
            flex: 1 !important;
            text-align: center !important;
            padding-right: 95px !important; /* Margin imajiner agar teks seimbang ke tengah */
        }
        
        .preview-container .kop-surat h2,
        .preview-container .kop-surat .nama-organisasi {
            font-size: 16pt !important;
            font-weight: 800 !important;
            margin: 0 0 5px 0 !important;
            color: #000 !important;
            line-height: 1.2 !important;
        }

        .preview-container .kop-surat p,
        .preview-container .kop-surat .alamat-kop,
        .preview-container .kop-surat .kontak-kop {
            font-size: 9.5pt !important;
            color: #333 !important;
            margin: 2px 0 !important;
            line-height: 1.4 !important;
        }
    </style>
</head>

<body>

    <!-- Sidebar -->
    <div id="sidebarContainer">
        <?php include __DIR__ . '/../../includes/sidebar.php'; ?>
    </div>

    <!-- Main Content -->
    <div class="main-content">
        <!-- Top Navbar -->
        <div class="top-navbar no-print">
            <div class="page-title">
                <h2>Laporan Anggota</h2>
                <p>Preview dan cetak laporan daftar anggota BPK</p>
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

        <div class="dropdown-menu-custom no-print" id="dropdownMenu">
            <a href="../../logout.php">
                <i class="fas fa-sign-out-alt"></i>
                <span>Logout</span>
            </a>
        </div>

        <!-- Filter Section -->
        <div class="filter-section no-print">
            <form method="GET" action="" class="row g-3 align-items-end">
                <div class="col-md-5">
                    <label class="form-label"><i class="fas fa-building me-1"></i> Filter BPK</label>
                    <select name="bpk_id" class="form-select">
                        <option value="">Semua BPK</option>
                        <?php
                        if ($bpk_list && $bpk_list->num_rows > 0):
                            mysqli_data_seek($bpk_list, 0);
                            while ($bpk = $bpk_list->fetch_assoc()):
                        ?>
                                <option value="<?= $bpk['id'] ?>" <?= $filter_bpk == $bpk['id'] ? 'selected' : '' ?>>
                                    <?= htmlspecialchars($bpk['nama_bpk']) ?>
                                </option>
                        <?php endwhile;
                        endif; ?>
                    </select>
                </div>
                <div class="col-md-7">
                    <div class="d-flex gap-2">
                        <button type="submit" class="btn-gold">
                            <i class="fas fa-eye"></i> Preview
                        </button>
                        <a href="cetak-pdf-anggota.php?bpk_id=<?= $filter_bpk ?>" target="_blank" class="btn-pdf-custom">
                            <i class="fas fa-file-pdf"></i> Cetak PDF
                        </a>
                    </div>
                </div>
            </form>
        </div>

        <!-- Preview Laporan -->
        <?php if (count($daftar_anggota) > 0): ?>
            <?php
            ob_start();
            ?>
            <table class="data-table">
                <thead>
                    <tr>
                        <th style="width: 5%;">No</th>
                        <th style="width: 8%;">No. Reg</th>
                        <th style="width: 17%;">Nama</th>
                        <th style="width: 15%;">Tempat, Tgl Lahir</th>
                        <th class="center" style="width: 5%;">JK</th>
                        <th style="width: 18%;">Alamat</th>
                        <th style="width: 12%;">NIK</th>
                        <th style="width: 10%;">No. HP</th>
                        <th style="width: 10%;">Jabatan</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($daftar_anggota as $i => $anggota): 
                        $jk = ($anggota['jenis_kelamin'] == 'Laki-laki') ? 'L' : 'P';
                        $ttl = htmlspecialchars($anggota['tempat_lahir'] ?? '') . ', ' . ($anggota['tanggal_lahir'] ? date('d/m/Y', strtotime($anggota['tanggal_lahir'])) : '');
                        if($ttl == ', ') $ttl = '-';
                    ?>
                        <tr>
                            <td class="center"><?= $i + 1 ?></td>
                            <td class="center"><?= sprintf("%02d", $anggota['nomor_anggota']) ?></td>
                            <td>
                                <?= htmlspecialchars($anggota['nama'] ?? '-') ?><br>
                                <?php if ($filter_bpk == 0): ?>
                                    <small style="color:#666; font-size:8pt;">(<?= htmlspecialchars($anggota['nama_bpk'] ?? '-') ?>)</small>
                                <?php endif; ?>
                            </td>
                            <td><?= $ttl ?></td>
                            <td class="center"><?= $jk ?></td>
                            <td><?= htmlspecialchars($anggota['alamat'] ?? '-') ?></td>
                            <td><?= htmlspecialchars($anggota['nik'] ?? '-') ?></td>
                            <td><?= htmlspecialchars($anggota['no_hp'] ?? '-') ?></td>
                            <td><?= htmlspecialchars($anggota['jabatan'] ?? '-') ?></td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
            <p style="margin-top: 10px; font-size: 10pt;">
                Total Anggota terdaftar: <strong><?= count($daftar_anggota) ?> orang</strong>
            </p>
            <?php
            $isi_html = ob_get_clean();

            // MENYESUAIKAN JUDUL LAPORAN BERDASARKAN BPK YANG DIPILIH
            $judul_laporan = 'LAPORAN DATA ANGGOTA ' . (!empty($nama_bpk_filter) ? strtoupper($nama_bpk_filter) : 'BPK');

            echo pdfPreviewHtml([
                'judul'         => $judul_laporan,
                'nomor_urut'    => '024',
                'tanggal_acuan' => time(),
                'isi_html'      => $isi_html,
            ]);
            ?>
        <?php else: ?>
            <div class="laporan-preview">
                <?= pdfPreviewNoData('Tidak ada data Anggota untuk filter ini', 'Silakan ubah filter BPK atau pastikan data anggota telah ditambahkan.') ?>
            </div>
        <?php endif; ?>

    </div>

    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js"></script>

    <script>
        // Toggle dropdown
        document.getElementById('userAvatar').addEventListener('click', function(e) {
            e.stopPropagation();
            document.getElementById('dropdownMenu').classList.toggle('show');
        });

        document.addEventListener('click', function() {
            document.getElementById('dropdownMenu').classList.remove('show');
        });
    </script>
</body>

</html>