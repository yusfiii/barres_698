<?php
require_once __DIR__ . '/../../includes/config.php';
require_once __DIR__ . '/../../includes/session.php';
require_once __DIR__ . '/../../includes/functions.php';

checkAuth();
checkRole(['super_admin']);

$user = getCurrentUser();
$conn = getConnection();

// Filter Tahun (Default tahun saat ini)
$current_year = date('Y');
$filter_tahun = isset($_GET['tahun']) ? (int)$_GET['tahun'] : $current_year;

// Ambil daftar tahun yang tersedia di database untuk dropdown
$tahun_list = [];
$thn_result = $conn->query("SELECT DISTINCT YEAR(waktu) as tahun FROM kejadian_kebakaran ORDER BY tahun DESC");
if ($thn_result) {
    while ($r = $thn_result->fetch_assoc()) {
        if ($r['tahun']) $tahun_list[] = $r['tahun'];
    }
}
if (!in_array($current_year, $tahun_list)) {
    array_unshift($tahun_list, $current_year);
}

// 1. Data Grafik (Total Kejadian per Kecamatan)
$query_kecamatan = "
    SELECT kecamatan, COUNT(*) as total 
    FROM kejadian_kebakaran 
    WHERE YEAR(waktu) = ? AND kecamatan IS NOT NULL AND kecamatan != ''
    GROUP BY kecamatan 
    ORDER BY total DESC
";
$stmt = $conn->prepare($query_kecamatan);
$stmt->bind_param("i", $filter_tahun);
$stmt->execute();
$res_kecamatan = $stmt->get_result();

$data_kecamatan = [];
while ($row = $res_kecamatan->fetch_assoc()) {
    $data_kecamatan[] = $row;
}
$stmt->close();

// 2. Data Tabel (Detail Kelurahan per Kecamatan)
$query_kelurahan = "
    SELECT kecamatan, kelurahan, COUNT(*) as total 
    FROM kejadian_kebakaran 
    WHERE YEAR(waktu) = ? AND kecamatan IS NOT NULL AND kecamatan != ''
    GROUP BY kecamatan, kelurahan 
    ORDER BY kecamatan ASC, total DESC
";
$stmt = $conn->prepare($query_kelurahan);
$stmt->bind_param("i", $filter_tahun);
$stmt->execute();
$res_kelurahan = $stmt->get_result();

$data_kelurahan = [];
$total_semua_kejadian = 0;
while ($row = $res_kelurahan->fetch_assoc()) {
    $data_kelurahan[] = $row;
    $total_semua_kejadian += $row['total'];
}
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
    <title>Laporan Wilayah - BARRES 698</title>

    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
    
    <!-- Chart.js -->
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>

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

        /* Actions Section */
        .action-section {
            background: #FFFFFF; border: 1px solid rgba(0, 0, 0, 0.08); border-radius: 20px;
            padding: 20px 24px; margin-bottom: 28px;
        }
        
        .form-label { font-size: 13px; font-weight: 600; margin-bottom: 8px; color: #1A1A1A; }
        .form-select {
            background: #F8F8F8; border: 1px solid #E0E0E0; color: #1A1A1A;
            border-radius: 12px; padding: 10px 14px; font-size: 13px; font-family: 'Poppins', sans-serif;
        }
        .form-select:focus { border-color: #F7B801; box-shadow: 0 0 0 3px rgba(247,184,1,0.1); outline: none; }
        
        .btn-gold {
            background: linear-gradient(135deg, #F7B801, #E5A800); border: none; padding: 10px 20px; border-radius: 12px;
            font-weight: 600; font-size: 13px; color: #1A1A1A; transition: all 0.3s ease; height: 100%;
        }
        .btn-gold:hover { transform: translateY(-2px); box-shadow: 0 5px 15px rgba(247,184,1,0.3); }

        .btn-pdf-custom {
            background: rgba(220, 53, 69, 0.1); border: 1px solid rgba(220, 53, 69, 0.3); padding: 10px 20px;
            border-radius: 12px; font-weight: 600; font-size: 13px; color: #dc3545; transition: all 0.2s;
            text-decoration: none; display: inline-flex; align-items: center; justify-content: center; gap: 6px; cursor: pointer; height: 100%; width: 100%;
        }
        .btn-pdf-custom:hover { background: rgba(220, 53, 69, 0.2); color: #dc3545; }

        /* Preview Container untuk A4 */
        .preview-container {
            background: #FFFFFF; border-radius: 20px; padding: 40px 50px;
            border: 1px solid rgba(0, 0, 0, 0.08); box-shadow: 0 4px 20px rgba(0, 0, 0, 0.02);
            max-width: 210mm; margin: 0 auto;
        }

        /* ==================== STYLE LAPORAN ==================== */
        .laporan { font-family: 'Arial', sans-serif !important; font-size: 10pt; color: #000000; line-height: 1.6; }
        
        .laporan .kop-surat {
            display: flex; flex-direction: row; align-items: center; justify-content: center;
            border-bottom: 3px solid #000; padding-bottom: 15px; margin-bottom: 20px; position: relative;
        }
        .laporan .kop-surat img { height: 75px; width: auto; margin-right: 20px; }
        .laporan .kop-surat .kop-text { flex: 1; text-align: center; padding-right: 95px; }
        .laporan .kop-surat .nama-organisasi { font-size: 16pt; font-weight: 800; margin: 0 0 5px 0; color: #000; line-height: 1.2; }
        .laporan .kop-surat p { font-size: 9.5pt; color: #333; margin: 2px 0; line-height: 1.4; }

        .laporan .surat-info { display: flex; justify-content: space-between; margin: 12px 0 15px 0; font-size: 11pt; }
        .laporan .surat-info .label { font-weight: 700; }
        
        .laporan .judul { text-align: center; margin: 15px 0 20px 0; font-weight: 700; font-size: 14pt; text-transform: uppercase; }

        /* Charts Container */
        .chart-wrapper { border: 1px solid #E0E0E0; border-radius: 12px; padding: 15px; margin-bottom: 20px; background: #FAFAFA; page-break-inside: avoid; }
        .chart-title { text-align: center; font-weight: bold; font-size: 11pt; margin-bottom: 10px; color: #333; }
        
        /* Table Detail */
        .laporan .data-table { width: 100%; border-collapse: collapse; margin-bottom: 20px; page-break-inside: auto; }
        .laporan .data-table th, .laporan .data-table td { border: 1px solid #666; padding: 8px 10px; font-size: 10pt; }
        .laporan .data-table th { background: #ECECEC; text-align: center; font-weight: bold; }
        .laporan .data-table tr { page-break-inside: avoid; page-break-after: auto; }
        
        /* Tanda Tangan */
        .laporan .ttd-section { margin-top: 40px; text-align: right; page-break-inside: avoid; }
        .laporan .ttd-section .ttd-place { font-size: 11pt; margin-bottom: 60px; }
        .laporan .ttd-section .ttd-name { font-weight: 700; font-size: 12pt; text-decoration: underline; }
        .laporan .ttd-section .ttd-title { font-size: 11pt; }

        /* Print - A4 */
        @media print {
            @page { 
                size: A4 portrait; 
                margin: 15mm; 
            }
            
            html, body { 
                background: white !important; 
                height: auto !important; 
                min-height: auto !important; 
                margin: 0 !important; 
                padding: 0 !important;
            }
            
            .sidebar, .top-navbar, .dropdown-menu-custom, .user-avatar, .action-section, .no-print { 
                display: none !important; 
            }
            
            .main-content { 
                margin: 0 !important; 
                padding: 0 !important; 
                min-height: auto !important;
            }
            
            .preview-container {
                border: none !important; 
                box-shadow: none !important; 
                padding: 0 !important;
                max-width: 100% !important; 
                width: 100% !important;
                margin: 0 !important;
            }
            
            .chart-wrapper { 
                border: 1px solid #ddd !important; 
                background: transparent !important; 
                page-break-inside: avoid !important; 
                margin-bottom: 15px !important;
            }
            
            * { 
                -webkit-print-color-adjust: exact !important; 
                print-color-adjust: exact !important; 
            }
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
                <h2>Laporan Kejadian per Wilayah</h2>
                <p>Analisis visual kepadatan dan jumlah kejadian per kecamatan/kelurahan</p>
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

        <!-- Action & Filter Section -->
        <div class="action-section no-print">
            <form method="GET" action="" class="row w-100 g-3 align-items-end m-0">
                <div class="col-md-5">
                    <label class="form-label"><i class="fas fa-calendar-alt me-1"></i> Pilih Tahun Analisis</label>
                    <select name="tahun" class="form-select">
                        <?php foreach ($tahun_list as $thn): ?>
                            <option value="<?= $thn ?>" <?= $filter_tahun == $thn ? 'selected' : '' ?>><?= $thn ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>
                <div class="col-md-3">
                    <button type="submit" class="btn-gold w-100">
                        <i class="fas fa-chart-bar"></i> Proses Data
                    </button>
                </div>
                <div class="col-md-4">
                    <button type="button" onclick="window.print()" class="btn-pdf-custom">
                        <i class="fas fa-print"></i> Cetak Laporan PDF
                    </button>
                </div>
            </form>
        </div>

        <!-- Preview Laporan -->
        <div class="preview-container laporan" id="laporanPreview">
            
            <!-- KOP SURAT -->
            <div class="kop-surat">
                <img src="../../assets/barres2.png" alt="BARRES 698">
                <div class="kop-text">
                    <h2 class="nama-organisasi">BANJARBARU RESCUE "BARRES 698"</h2>
                    <p class="alamat-kop">Jl. Zafri Zamzam II Komplek H. KA Ganie No. 06 RT. 013 RW. 003, Kel.</p>
                    <p class="alamat-kop">Kemuning Kec. Banjarbaru Selatan, Kota Banjarbaru.</p>
                    <p class="kontak-kop">WhatsApp : 0851 868 14698 / Freq : 15.698.0 Mhz</p>
                    <p class="kontak-kop">E-mail : barres698.banjarbaru@gmail.com</p>
                </div>
            </div>

            <!-- Surat Info -->
            <div class="surat-info">
                <div class="left">
                    <span class="label">Nomor</span> : 027/BARRES698/<?= date('m/Y') ?><br>
                    <span class="label">Perihal</span> : Laporan Analisis Wilayah Rawan Kebakaran Tahun <?= $filter_tahun ?>
                </div>
                <div class="right">
                    Banjarbaru, <?= date('d F Y') ?>
                </div>
            </div>

            <div class="judul">ANALISIS KEJADIAN PER WILAYAH TAHUN <?= $filter_tahun ?></div>

            <p style="text-align: justify; margin-bottom: 15px;">
                Berdasarkan rekapitulasi data dari sistem pemetaan Banjarbaru Rescue 698, total kejadian kebakaran yang tercatat dan terverifikasi wilayahnya pada tahun <strong><?= $filter_tahun ?></strong> adalah sebanyak <strong><?= $total_semua_kejadian ?></strong> kejadian. Berikut adalah visualisasi penyebaran kejadian per kecamatan:
            </p>

            <!-- Grafik Tren Kecamatan (Bar Chart) -->
            <div class="chart-wrapper">
                <div class="chart-title">Grafik Perbandingan Jumlah Kejadian antar Kecamatan</div>
                <!-- Kontainer Absolut untuk menjaga rasio grafik saat diprint -->
                <div style="position: relative; height: 350px; width: 100%;">
                    <canvas id="wilayahChart"></canvas>
                </div>
            </div>

            <p style="text-align: justify; margin-bottom: 10px;">
                <strong>Rincian Rekapitulasi per Kecamatan dan Kelurahan:</strong>
            </p>

            <!-- Tabel Detail Wilayah -->
            <table class="data-table">
                <thead>
                    <tr>
                        <th style="width: 10%;">No</th>
                        <th style="width: 35%;">Kecamatan</th>
                        <th style="width: 35%;">Kelurahan/Desa</th>
                        <th style="width: 20%;">Jumlah Kejadian</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (count($data_kelurahan) > 0): 
                        $no = 1;
                        $current_kecamatan = '';
                        foreach ($data_kelurahan as $row): 
                            // Untuk rowspan (menggabungkan sel kecamatan yang sama) - ini versi simple tanpa rowspan agar lebih aman saat terpotong page
                    ?>
                        <tr>
                            <td style="text-align: center;"><?= $no++ ?></td>
                            <td><?= htmlspecialchars($row['kecamatan']) ?></td>
                            <td><?= htmlspecialchars($row['kelurahan'] ?? 'Tidak Diketahui') ?></td>
                            <td style="text-align: center; font-weight: bold;"><?= $row['total'] ?> titik</td>
                        </tr>
                    <?php endforeach; else: ?>
                        <tr>
                            <td colspan="4" style="text-align: center; color: #999;">Tidak ada data kejadian pada tahun ini.</td>
                        </tr>
                    <?php endif; ?>
                </tbody>
                <tfoot>
                    <tr style="background: #ECECEC; font-weight: bold;">
                        <td colspan="3" style="text-align: right;">TOTAL KEJADIAN KESELURUHAN</td>
                        <td style="text-align: center;"><?= $total_semua_kejadian ?> titik</td>
                    </tr>
                </tfoot>
            </table>

            <!-- TTD -->
            <div class="ttd-section">
                <div class="ttd-place">Banjarbaru, <?= date('d F Y') ?></div>
                <div class="ttd-name">Kemas Akhmad Rudi Indrajaya</div>
                <div class="ttd-title">KETUA UMUM BARRES 698</div>
            </div>

        </div>
    </div>

    <!-- Scripts -->
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js"></script>

    <script>
        // Toggle user dropdown
        document.getElementById('userAvatar').addEventListener('click', function(e) {
            e.stopPropagation();
            document.getElementById('dropdownMenu').classList.toggle('show');
        });
        document.addEventListener('click', function() {
            document.getElementById('dropdownMenu').classList.remove('show');
        });

        // Data Kecamatan untuk Bar Chart
        const dataKecamatan = <?= json_encode($data_kecamatan) ?>;
        const labelsKecamatan = dataKecamatan.map(d => d.kecamatan);
        const valuesKecamatan = dataKecamatan.map(d => d.total);
        
        // Palette warna untuk Bar
        const barColors = ['#F7B801', '#dc3545', '#17a2b8', '#28a745', '#6c757d', '#fd7e14'];

        // Inisialisasi Bar Chart
        if (dataKecamatan.length > 0) {
            const ctxWilayah = document.getElementById('wilayahChart').getContext('2d');
            new Chart(ctxWilayah, {
                type: 'bar',
                data: {
                    labels: labelsKecamatan,
                    datasets: [{
                        label: 'Total Kejadian',
                        data: valuesKecamatan,
                        backgroundColor: barColors.slice(0, dataKecamatan.length),
                        borderColor: '#1A1A1A',
                        borderWidth: 1,
                        borderRadius: 4
                    }]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    plugins: {
                        legend: { display: false },
                        tooltip: {
                            callbacks: {
                                label: function(context) {
                                    return context.parsed.y + ' Kejadian';
                                }
                            }
                        }
                    },
                    scales: {
                        y: {
                            beginAtZero: true,
                            ticks: { stepSize: 1 }
                        }
                    }
                }
            });
        } else {
            document.getElementById('wilayahChart').parentElement.innerHTML = '<p class="text-center text-muted mt-5">Belum ada data untuk dirender ke dalam grafik</p>';
        }
    </script>
</body>

</html>