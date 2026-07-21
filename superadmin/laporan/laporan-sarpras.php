<?php
require_once __DIR__ . '/../../includes/config.php';
require_once __DIR__ . '/../../includes/session.php';
require_once __DIR__ . '/../../includes/functions.php';
require_once __DIR__ . '/../../includes/pdf-helper.php';

checkAuth();
checkRole(['super_admin']);

$user = getCurrentUser();

// Filter parameters (BPK)
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

// Ambil data BPK dan Sarprasnya (Left Join agar BPK yang belum punya data tetap tampil)
$query = "SELECT b.id as bpk_id, b.nama_bpk, 
                 s.mobil_tangki, s.mobil_portabel, s.mesin_pompa, 
                 s.selang_1_5_inc, s.selang_2_5_inc, s.selang_isap, 
                 s.nozle, s.helm_apd, s.baju_apd, s.celana_apd, s.sepatu_apd 
          FROM bpk b
          LEFT JOIN sapras_bpk s ON b.id = s.bpk_id 
          WHERE 1=1";
$params = [];
$types = "";

if ($filter_bpk > 0) {
    $query .= " AND b.id = ?";
    $params[] = $filter_bpk;
    $types .= "i";
}

$query .= " ORDER BY b.nama_bpk ASC";

$stmt = $conn->prepare($query);

// Pengecekan error SQL
if (!$stmt) {
    die("Terjadi kesalahan pada Query SQL: " . $conn->error);
}

if (!empty($params)) {
    $stmt->bind_param($types, ...$params);
}
$stmt->execute();
$result = $stmt->get_result();
$daftar_sarpras = $result->fetch_all(MYSQLI_ASSOC);
$stmt->close();

$conn->close();

// Mapping kolom database ke nama tampilan dan satuan
$items_map = [
    'mobil_tangki'   => ['label' => 'Mobil Tangki', 'satuan' => 'Unit'],
    'mobil_portabel' => ['label' => 'Mobil Portabel', 'satuan' => 'Unit'],
    'mesin_pompa'    => ['label' => 'Mesin Pompa', 'satuan' => 'Unit'],
    'selang_1_5_inc' => ['label' => 'Selang 1.5"', 'satuan' => 'Roll'],
    'selang_2_5_inc' => ['label' => 'Selang 2.5"', 'satuan' => 'Roll'],
    'selang_isap'    => ['label' => 'Selang Isap', 'satuan' => 'Roll'],
    'nozle'          => ['label' => 'Nozle', 'satuan' => 'Pcs'],
    'helm_apd'       => ['label' => 'Helm APD', 'satuan' => 'Pcs'],
    'baju_apd'       => ['label' => 'Baju APD', 'satuan' => 'Pcs'],
    'celana_apd'     => ['label' => 'Celana APD', 'satuan' => 'Pcs'],
    'sepatu_apd'     => ['label' => 'Sepatu APD', 'satuan' => 'Pcs']
];

// Include sidebar
include __DIR__ . '/../../includes/sidebar.php';
?>

<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Laporan Sarpras - BARRES 698</title>

    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">

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
            border-radius: 14px; display: flex; align-items: center; justify-content: center; cursor: pointer;
        }
        .user-avatar i { font-size: 22px; color: #1A1A1A; }

        .dropdown-menu-custom {
            position: absolute; top: 80px; right: 32px; background: #FFFFFF; border: 1px solid rgba(0, 0, 0, 0.1);
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.08); border-radius: 16px; padding: 12px 0; min-width: 180px; display: none; z-index: 1000;
        }
        .dropdown-menu-custom.show { display: block; }
        .dropdown-menu-custom a { display: flex; align-items: center; gap: 12px; padding: 12px 20px; text-decoration: none; font-size: 13px; color: #333; }
        .dropdown-menu-custom a:hover { background: rgba(247, 184, 1, 0.1); color: #F7B801; }

        /* Filter Section */
        .filter-section {
            background: #FFFFFF; border: 1px solid rgba(0, 0, 0, 0.08); border-radius: 20px;
            padding: 20px 24px; margin-bottom: 28px;
        }
        .form-label { font-size: 13px; font-weight: 600; margin-bottom: 8px; color: #1A1A1A; }
        .form-control, .form-select {
            background: #F8F8F8; border: 1px solid #E0E0E0; color: #1A1A1A;
            border-radius: 12px; padding: 10px 14px; font-size: 13px; font-family: 'Poppins', sans-serif;
        }
        .form-control:focus, .form-select:focus { border-color: #F7B801; box-shadow: 0 0 0 3px rgba(247, 184, 1, 0.1); outline: none; }
        
        .btn-gold {
            background: linear-gradient(135deg, #F7B801, #E5A800); border: none; padding: 10px 20px; border-radius: 12px;
            font-weight: 600; font-size: 13px; color: #1A1A1A; transition: all 0.3s ease;
        }
        .btn-gold:hover { transform: translateY(-2px); box-shadow: 0 5px 15px rgba(247, 184, 1, 0.3); }
        .btn-pdf-custom {
            background: rgba(220, 53, 69, 0.1); border: 1px solid rgba(220, 53, 69, 0.3); padding: 10px 20px;
            border-radius: 12px; font-weight: 600; font-size: 13px; color: #dc3545; text-decoration: none; display: inline-flex; align-items: center; gap: 6px;
        }

        /* Preview Container */
        .preview-container {
            background: #FFFFFF; border-radius: 20px; padding: 40px 50px;
            border: 1px solid rgba(0, 0, 0, 0.08); box-shadow: 0 4px 20px rgba(0, 0, 0, 0.02);
            max-width: 210mm; margin: 0 auto;
        }

        @media print {
            .sidebar, .top-navbar, .dropdown-menu-custom, .user-avatar, .filter-section, .no-print { display: none !important; }
            .main-content { margin-left: 0 !important; padding: 0 !important; }
        }

        /* Panggil Helper CSS Web */
        <?= pdfPreviewCss() ?>
        
        /* Kop Surat Override */
        .preview-container .kop-surat { display: flex !important; flex-direction: row !important; align-items: center !important; justify-content: center !important; border-bottom: 3px solid #000 !important; padding-bottom: 15px !important; margin-bottom: 20px !important; position: relative !important; }
        .preview-container .kop-surat img { position: static !important; height: 75px !important; width: auto !important; margin-right: 20px !important; transform: none !important; }
        .preview-container .kop-surat .kop-text, .preview-container .kop-surat div { flex: 1 !important; text-align: center !important; padding-right: 95px !important; }
        .preview-container .kop-surat h2, .preview-container .kop-surat .nama-organisasi { font-size: 16pt !important; font-weight: 800 !important; margin: 0 0 5px 0 !important; color: #000 !important; line-height: 1.2 !important; }
        .preview-container .kop-surat p, .preview-container .kop-surat .alamat-kop, .preview-container .kop-surat .kontak-kop { font-size: 9.5pt !important; color: #333 !important; margin: 2px 0 !important; line-height: 1.4 !important; }
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
                <h2>Laporan Sarpras BPK</h2>
                <p>Preview dan cetak laporan inventaris peralatan BPK</p>
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
                        <button type="submit" class="btn-gold"><i class="fas fa-eye"></i> Preview</button>
                        <a href="cetak-pdf-sarpras.php?bpk_id=<?= $filter_bpk ?>" target="_blank" class="btn-pdf-custom">
                            <i class="fas fa-file-pdf"></i> Cetak PDF
                        </a>
                    </div>
                </div>
            </form>
        </div>

        <!-- Preview Laporan -->
        <?php if (count($daftar_sarpras) > 0): ?>
            <?php
            ob_start();
            $total_seluruh_barang = 0;
            ?>
            <table class="data-table">
                <thead>
                    <tr>
                        <th style="width: 5%;">No</th>
                        <th style="width: 35%;">Nama BPK/PMK</th>
                        <th style="width: 45%;">Rincian Sarana & Prasarana</th>
                        <th class="center" style="width: 15%;">Total Item</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($daftar_sarpras as $i => $s): 
                        $rincian_html = [];
                        $total_per_bpk = 0;
                        $has_record = isset($s['mobil_tangki']); // Cek apakah data sarpras ada untuk BPK ini

                        if ($has_record) {
                            foreach ($items_map as $db_col => $info) {
                                $qty = (int)$s[$db_col];
                                if ($qty > 0) {
                                    $rincian_html[] = "<li>{$info['label']}: <strong>{$qty} {$info['satuan']}</strong></li>";
                                    $total_per_bpk += $qty;
                                }
                            }
                        }
                        $total_seluruh_barang += $total_per_bpk;
                    ?>
                        <tr>
                            <td class="center" style="vertical-align: top; padding-top: 10px;"><?= $i + 1 ?></td>
                            
                            <td style="vertical-align: top; padding-top: 10px;">
                                <strong><?= htmlspecialchars($s['nama_bpk']) ?></strong>
                            </td>
                            
                            <td style="vertical-align: top; padding-top: 10px;">
                                <?php if (!$has_record): ?>
                                    <em style="color: #dc3545;">Belum melakukan pendataan sarpras.</em>
                                <?php elseif (empty($rincian_html)): ?>
                                    <em style="color: #999;">Tidak memiliki inventaris alat.</em>
                                <?php else: ?>
                                    <ul style="margin: 0; padding-left: 15px; line-height: 1.6;">
                                        <?= implode('', $rincian_html) ?>
                                    </ul>
                                <?php endif; ?>
                            </td>
                            
                            <td class="center" style="vertical-align: top; padding-top: 10px; font-weight: bold; font-size: 12pt;">
                                <?= $total_per_bpk ?>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
            <p style="margin-top: 10px; font-size: 10pt;">
                Total Jumlah Seluruh Inventaris Terdata: <strong><?= $total_seluruh_barang ?> unit/pcs/roll</strong>
            </p>
            <?php
            $isi_html = ob_get_clean();
            $judul_laporan = 'LAPORAN REKAPITULASI SARANA & PRASARANA ' . (!empty($nama_bpk_filter) ? strtoupper($nama_bpk_filter) : 'BPK');

            echo pdfPreviewHtml([
                'judul'         => $judul_laporan,
                'nomor_urut'    => '031',
                'tanggal_acuan' => time(),
                'isi_html'      => $isi_html,
            ]);
            ?>
        <?php else: ?>
            <div class="laporan-preview">
                <?= pdfPreviewNoData('Tidak ada data BPK/Sarpras', 'Silakan tambahkan data terlebih dahulu.') ?>
            </div>
        <?php endif; ?>

    </div>
</body>
</html>