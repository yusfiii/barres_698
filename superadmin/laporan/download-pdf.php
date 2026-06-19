<?php
require_once __DIR__ . '/../../includes/config.php';
require_once __DIR__ . '/../../includes/session.php';
require_once __DIR__ . '/../../includes/functions.php';

checkAuth();
checkRole(['super_admin']);

// Filter parameters
$filter_bulan = isset($_GET['bulan']) ? $_GET['bulan'] : date('Y-m');
$filter_kecamatan = isset($_GET['kecamatan']) ? $_GET['kecamatan'] : '';
$filter_id = isset($_GET['id']) ? (int)$_GET['id'] : 0;

$conn = getConnection();

// Ambil data kejadian
$query = "SELECT * FROM kejadian_kebakaran WHERE 1=1";
$params = [];
$types = "";

if ($filter_id > 0) {
    $query .= " AND id = ?";
    $params[] = $filter_id;
    $types .= "i";
} else {
    if (!empty($filter_bulan)) {
        $query .= " AND DATE_FORMAT(waktu, '%Y-%m') = ?";
        $params[] = $filter_bulan;
        $types .= "s";
    }
    if (!empty($filter_kecamatan)) {
        $query .= " AND kecamatan = ?";
        $params[] = $filter_kecamatan;
        $types .= "s";
    }
}

$query .= " ORDER BY waktu DESC";
$query .= " LIMIT 1";

$stmt = $conn->prepare($query);
if (!empty($params)) {
    $stmt->bind_param($types, ...$params);
}
$stmt->execute();
$result = $stmt->get_result();
$kejadian = $result->fetch_assoc();
$stmt->close();
$conn->close();

if (!$kejadian) {
    die("Tidak ada data kejadian untuk periode ini");
}

// Fungsi untuk menampilkan detail
function showDetail($label, $value) {
    echo '<tr>';
    echo '<td style="font-weight: 600; width: 180px; padding: 6px 10px; border-bottom: 1px dashed #e8e8e8;">' . $label . '</td>';
    echo '<td style="padding: 6px 10px; border-bottom: 1px dashed #e8e8e8;">: ' . ($value ?: '-') . '</td>';
    echo '</tr>';
}

header('Content-Type: text/html; charset=utf-8');
?>
<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Laporan Kejadian - BARRES 698</title>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; }
        body { 
            font-family: 'Poppins', sans-serif; 
            background: white; 
            color: #1A1A1A;
            padding: 20px 40px;
            font-size: 12px;
        }
        .container { max-width: 1000px; margin: 0 auto; }

        /* Kop Surat */
        .kop-surat {
            border-bottom: 3px double #F7B801;
            padding-bottom: 15px;
            margin-bottom: 20px;
            text-align: center;
        }
        .kop-surat .logo-container {
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 15px;
            margin-bottom: 5px;
        }
        .kop-surat .logo-placeholder {
            width: 65px;
            height: 65px;
            background: #F7B801;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 28px;
            color: white;
            font-weight: 800;
            flex-shrink: 0;
        }
        .kop-surat .kop-text { text-align: left; }
        .kop-surat h1 {
            font-size: 16px;
            font-weight: 800;
            color: #1A1A1A;
            margin: 0;
            letter-spacing: 1px;
            line-height: 1.3;
        }
        .kop-surat .subtitle { font-size: 11px; color: #444; font-weight: 600; }
        .kop-surat .address, .kop-surat .contact {
            font-size: 10px;
            color: #666;
            margin: 1px 0;
        }

        /* Surat Info */
        .surat-info {
            display: flex;
            justify-content: space-between;
            margin: 15px 0 20px 0;
            font-size: 12px;
        }
        .surat-info .label { font-weight: 600; }

        /* Judul */
        .judul {
            text-align: center;
            margin: 20px 0;
            font-weight: 700;
            font-size: 18px;
            color: #1A1A1A;
            text-transform: uppercase;
            letter-spacing: 1px;
        }

        /* Detail Table */
        .detail-table {
            width: 100%;
            border-collapse: collapse;
            margin: 10px 0;
        }
        .detail-table td {
            padding: 6px 10px;
            border-bottom: 1px dashed #e8e8e8;
            font-size: 12px;
        }
        .detail-table .label {
            font-weight: 600;
            width: 180px;
        }

        /* Foto */
        .foto-section {
            margin: 20px 0;
            padding: 15px;
            border: 1px dashed #ddd;
            border-radius: 8px;
            text-align: center;
            min-height: 120px;
            background: #FAFAFA;
        }
        .foto-section img { max-width: 100%; max-height: 250px; border-radius: 8px; }
        .foto-section .no-foto { color: #999; }
        .foto-section .no-foto i { font-size: 36px; display: block; margin-bottom: 8px; color: #ddd; }

        /* TTD */
        .ttd-section {
            margin-top: 40px;
            text-align: right;
            padding-top: 15px;
        }
        .ttd-section .ttd-place { font-size: 12px; color: #555; }
        .ttd-section .ttd-name {
            font-weight: 600;
            font-size: 14px;
            color: #1A1A1A;
            margin-top: 35px;
        }
        .ttd-section .ttd-title { font-size: 12px; color: #666; }
        .ttd-section .ttd-stamp {
            margin-top: 3px;
            font-size: 10px;
            color: #999;
            font-style: italic;
        }

        .footer-report {
            margin-top: 20px;
            text-align: center;
            font-size: 10px;
            color: #999;
            border-top: 1px solid #E8E8E8;
            padding-top: 12px;
        }

        @media print {
            body { padding: 0; margin: 0; }
            .no-print { display: none; }
        }

        @media (max-width: 768px) {
            body { padding: 10px; }
            .surat-info { flex-direction: column; gap: 5px; }
            .kop-surat .logo-container { flex-direction: column; text-align: center; }
            .kop-surat .kop-text { text-align: center; }
        }
    </style>
</head>
<body>

<div class="container">

    <!-- KOP SURAT -->
    <div class="kop-surat">
        <div class="logo-container">
            <div class="logo-placeholder">B</div>
            <div class="kop-text">
                <h1>SEKRETARIAT BANJARBARU RESCUE "BARRES 698"</h1>
                <div class="subtitle">KOTA BANJARBARU</div>
                <div class="address">Nomor AHU-0006775.AH.01.07. Tahun 2025</div>
                <div class="address">Jl. Zafri Zamzam II Komplek H. KA Ganie No. 06 RT. 013 RW. 003</div>
                <div class="address">Kel. Kemuning Kec. Banjarbaru Selatan, Kota Banjarbaru</div>
                <div class="contact">WhatsApp : 0851 868 14698 | Freq : 15.698.0 Mhz</div>
                <div class="contact">E-mail : barres698.banjarbaru@gmail.com</div>
            </div>
        </div>
    </div>

    <!-- Surat Info -->
    <div class="surat-info">
        <div class="left">
            <span class="label">Nomor</span> : 022/BARRES698/<?= date('m/Y', strtotime($kejadian['waktu'])) ?><br>
            <span class="label">Lampiran</span> : 1 (satu) berkas
        </div>
        <div class="right">
            Banjarbaru, <?= date('d F Y', strtotime($kejadian['waktu'])) ?>
        </div>
    </div>

    <!-- Judul -->
    <div class="judul">LAPORAN KEJADIAN KEBAKARAN</div>

    <!-- Detail -->
    <table class="detail-table">
        <?php
        showDetail('Waktu Kejadian', date('d/m/Y H:i', strtotime($kejadian['waktu'])));
        showDetail('Titik Koordinat', number_format($kejadian['latitude'] ?? 0, 6) . ' , ' . number_format($kejadian['longitude'] ?? 0, 6));
        showDetail('Alamat', $kejadian['alamat']);
        showDetail('Kecamatan', $kejadian['kecamatan'] ?? '-');
        showDetail('Kelurahan', $kejadian['kelurahan'] ?? '-');
        showDetail('Bangunan Terdampak', ($kejadian['jumlah_bangunan'] ?? 0) . ' unit');
        showDetail('Jumlah KK', $kejadian['jumlah_KK'] ?? 0);
        showDetail('Jumlah Individu', $kejadian['jumlah_individu'] ?? 0);
        showDetail('Korban Luka/Cedera', ($kejadian['korban_luka'] ?? 0) . ' orang');
        showDetail('Korban Jiwa', ($kejadian['korban_jiwa'] ?? 0) . ' orang');
        ?>
    </table>

    <!-- Foto -->
    <div class="foto-section">
        <?php if ($kejadian['foto'] && file_exists('../../uploads/' . $kejadian['foto'])): ?>
            <img src="../../uploads/<?= $kejadian['foto'] ?>" alt="Foto Kejadian">
        <?php else: ?>
            <div class="no-foto">
                <i class="fas fa-camera"></i>
                <span>Foto Jika Ada</span>
            </div>
        <?php endif; ?>
    </div>

    <!-- TTD -->
    <div class="ttd-section">
        <div class="ttd-place">Banjarbaru, <?= date('d F Y', strtotime($kejadian['waktu'])) ?></div>
        <div class="ttd-name">Kemas Akhmad Rudi Indrajaya</div>
        <div class="ttd-title">KETUA UMUM BARRES 698</div>
        <div class="ttd-stamp">*Laporan ini dicetak secara elektronik dan sah tanpa tanda tangan basah</div>
    </div>

    <!-- Footer -->
    <div class="footer-report">
        Laporan Resmi BARRES 698 - Dicetak pada <?= date('d/m/Y H:i') ?>
    </div>

</div>

<script>
    // Auto print
    window.onload = function() {
        setTimeout(function() {
            window.print();
        }, 500);
    }
</script>

</body>
</html>