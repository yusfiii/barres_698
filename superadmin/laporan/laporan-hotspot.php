<?php
// superadmin/laporan/laporan-hotspot.php
require_once __DIR__ . '/../../includes/config.php';
require_once __DIR__ . '/../../includes/session.php';
require_once __DIR__ . '/../../includes/functions.php';

checkAuth();
checkRole(['super_admin']);

$user = getCurrentUser();
$conn = getConnection();

// Ambil pengaturan heatmap
$heatmapSettings = getHeatmapSettings();

// Ambil data kejadian untuk marker & bounds
$query = "SELECT latitude, longitude, alamat, waktu, kecamatan 
          FROM kejadian_kebakaran 
          WHERE latitude IS NOT NULL 
          AND longitude IS NOT NULL";
$result = $conn->query($query);
$kejadian = [];
while ($row = $result->fetch_assoc()) {
    $kejadian[] = [
        'lat' => (float)$row['latitude'],
        'lng' => (float)$row['longitude'],
        'waktu' => $row['waktu'],
        'alamat' => $row['alamat'],
        'kecamatan' => $row['kecamatan']
    ];
}

// Statistik Kepadatan per Kecamatan
$kecamatanStats = $conn->query("
    SELECT kecamatan, COUNT(*) as total
    FROM kejadian_kebakaran 
    WHERE latitude IS NOT NULL AND longitude IS NOT NULL
    GROUP BY kecamatan
    ORDER BY total DESC
");

$conn->close();

// Include sidebar
include __DIR__ . '/../../includes/sidebar.php';
?>

<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Laporan Peta Hotspot - BARRES 698</title>

    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
    
    <!-- Leaflet CSS -->
    <link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css" />

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
            padding: 20px 24px; margin-bottom: 28px; display: flex; justify-content: space-between; align-items: center;
        }
        .btn-pdf-custom {
            background: rgba(220, 53, 69, 0.1); border: 1px solid rgba(220, 53, 69, 0.3); padding: 10px 20px;
            border-radius: 12px; font-weight: 600; font-size: 13px; color: #dc3545; transition: all 0.2s;
            text-decoration: none; display: inline-flex; align-items: center; gap: 6px; cursor: pointer;
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

        /* Map Container Laporan */
        .map-wrapper { width: 100%; border: 2px solid #000; border-radius: 8px; overflow: hidden; margin-bottom: 20px; position: relative; }
        #mapReport { height: 450px; width: 100%; }
        
        /* Loading Overlay Map */
        .map-loading {
            position: absolute; top: 0; left: 0; right: 0; bottom: 0; background: rgba(255,255,255,0.8);
            display: flex; justify-content: center; align-items: center; z-index: 1000; flex-direction: column;
        }
        
        /* Table Detail */
        .laporan .data-table { width: 100%; border-collapse: collapse; margin-bottom: 20px; }
        .laporan .data-table th, .laporan .data-table td { border: 1px solid #666; padding: 8px 10px; font-size: 10pt; }
        .laporan .data-table th { background: #f0f0f0; text-align: center; font-weight: bold; }
        
        /* Legenda Peta */
        .legend-container { display: flex; align-items: center; justify-content: center; gap: 20px; margin-bottom: 20px; padding: 10px; border: 1px solid #ddd; border-radius: 8px; background: #fafafa; }
        .legend-item { display: flex; align-items: center; gap: 8px; font-size: 10pt; font-weight: 600; }
        .legend-color { width: 20px; height: 20px; border-radius: 4px; }

        /* Tanda Tangan */
        .laporan .ttd-section { margin-top: 40px; text-align: right; }
        .laporan .ttd-section .ttd-place { font-size: 11pt; margin-bottom: 60px; }
        .laporan .ttd-section .ttd-name { font-weight: 700; font-size: 12pt; text-decoration: underline; }
        .laporan .ttd-section .ttd-title { font-size: 11pt; }

        /* Print - A4 */
        @media print {
            body { background: white !important; }
            .sidebar, .top-navbar, .dropdown-menu-custom, .user-avatar, .action-section, .no-print { display: none !important; }
            .main-content { margin: 0 !important; padding: 0 !important; }
            .preview-container {
                border: none !important; box-shadow: none !important; padding: 0 !important;
                max-width: 100% !important; width: 100% !important;
            }
            .map-wrapper { page-break-inside: avoid; }
            /* Memastikan Leaflet memuat warna dengan benar di print */
            * { -webkit-print-color-adjust: exact !important; print-color-adjust: exact !important; }
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
                <h2>Laporan Peta Hotspot</h2>
                <p>Preview dan cetak visualisasi analisis KDE kepadatan kebakaran</p>
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

        <!-- Action Section -->
        <div class="action-section no-print">
            <div>
                <p class="mb-0 text-muted" style="font-size: 13px;">
                    <i class="fas fa-info-circle me-1" style="color: #F7B801;"></i> 
                    Peta dirender menggunakan pengaturan KDE terakhir yang disimpan.
                </p>
            </div>
            <div>
                <button type="button" onclick="window.print()" class="btn-pdf-custom">
                    <i class="fas fa-print"></i> Cetak Laporan Peta
                </button>
            </div>
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
                    <span class="label">Nomor</span> : 025/BARRES698/<?= date('m/Y') ?><br>
                    <span class="label">Perihal</span> : Laporan Analisis Titik Rawan Kebakaran (KDE)
                </div>
                <div class="right">
                    Banjarbaru, <?= date('d F Y') ?>
                </div>
            </div>

            <div class="judul">PETA HOTSPOT KEPADATAN KEJADIAN KEBAKARAN</div>

            <!-- Legenda -->
            <div class="legend-container">
                <div class="legend-item">
                    <div class="legend-color" style="background: #00cc44;"></div> Rendah
                </div>
                <div class="legend-item">
                    <div class="legend-color" style="background: #ffcc00;"></div> Sedang
                </div>
                <div class="legend-item">
                    <div class="legend-color" style="background: #ff3300;"></div> Tinggi
                </div>
            </div>

            <!-- Map Area -->
            <div class="map-wrapper">
                <div id="mapReport"></div>
                <div class="map-loading" id="mapLoading">
                    <div class="spinner-border text-warning" role="status"></div>
                    <p class="mt-2" style="font-family: 'Poppins', sans-serif;">Memuat Peta KDE...</p>
                </div>
            </div>

            <p style="text-align: justify; font-size: 10pt; margin-bottom: 15px;">
                Berdasarkan hasil analisis spasial menggunakan metode <em>Kernel Density Estimation</em> (KDE) dengan parameter visualisasi 
                Radius: <strong><?= $heatmapSettings['radius'] ?? 25 ?>px</strong>, 
                Blur: <strong><?= $heatmapSettings['blur'] ?? 15 ?></strong>, 
                serta Intensitas <strong><?= $heatmapSettings['intensity'] ?? 70 ?>%</strong>, berikut adalah rekapitulasi kepadatan kejadian kebakaran per kecamatan di Kota Banjarbaru:
            </p>

            <!-- Tabel Statistik -->
            <table class="data-table">
                <thead>
                    <tr>
                        <th style="width: 10%;">No</th>
                        <th style="width: 40%;">Kecamatan</th>
                        <th style="width: 25%;">Total Kejadian</th>
                        <th style="width: 25%;">Status Kerawanan</th>
                    </tr>
                </thead>
                <tbody>
                    <?php 
                    if ($kecamatanStats && $kecamatanStats->num_rows > 0): 
                        // 1. Simpan data ke array dan cari nilai tertinggi untuk acuan dinamis
                        $dataKec = [];
                        $maxTotal = 0;
                        while ($row = $kecamatanStats->fetch_assoc()) {
                            $dataKec[] = $row;
                            if ($row['total'] > $maxTotal) $maxTotal = $row['total'];
                        }

                        $no = 1;
                        foreach ($dataKec as $row):
                            // 2. Klasifikasi dinamis berdasarkan persentase dari nilai tertinggi
                            $persentase = ($maxTotal > 0) ? (($row['total'] / $maxTotal) * 100) : 0;
                            
                            $kepadatan = 'Rendah';
                            $warna = '#28a745'; // Hijau
                            
                            if ($persentase >= 75) {
                                $kepadatan = 'Tinggi';
                                $warna = '#dc3545'; // Merah
                            } elseif ($persentase >= 40) {
                                $kepadatan = 'Sedang';
                                $warna = '#fd7e14'; // Orange
                            }
                    ?>
                        <tr>
                            <td class="center" style="text-align: center;"><?= $no++ ?></td>
                            <td><?= htmlspecialchars($row['kecamatan']) ?></td>
                            <td class="center" style="text-align: center;"><?= $row['total'] ?> titik</td>
                            <td class="center" style="text-align: center; font-weight: bold; color: <?= $warna ?>;">
                                <?= $kepadatan ?>
                            </td>
                        </tr>
                    <?php endforeach; else: ?>
                        <tr>
                            <td colspan="4" style="text-align: center;">Data kejadian kosong atau tidak memiliki koordinat spasial.</td>
                        </tr>
                    <?php endif; ?>
                </tbody>
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
    
    <!-- Leaflet & Heatmap JS -->
    <script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/leaflet.heat@0.2.0/dist/leaflet-heat.js"></script>

    <script>
        // Toggle user dropdown
        document.getElementById('userAvatar').addEventListener('click', function(e) {
            e.stopPropagation();
            document.getElementById('dropdownMenu').classList.toggle('show');
        });
        document.addEventListener('click', function() {
            document.getElementById('dropdownMenu').classList.remove('show');
        });

        // Initialize Map
        const map = L.map('mapReport', {
            zoomControl: false, 
            dragging: false, 
            scrollWheelZoom: false,
            doubleClickZoom: false
        }).setView([-3.468, 114.832], 12);

        L.tileLayer('https://{s}.basemaps.cartocdn.com/light_all/{z}/{x}/{y}{r}.png', {
            attribution: '&copy; OpenStreetMap contributors'
        }).addTo(map);

        const kejadianData = <?= json_encode($kejadian) ?>;
        const radiusVal = <?= $heatmapSettings['radius'] ?? 25 ?>;
        const blurVal = <?= $heatmapSettings['blur'] ?? 15 ?>;
        const intensityVal = <?= $heatmapSettings['intensity'] ?? 70 ?>;

        function loadKDE() {
            if (kejadianData.length < 1) {
                document.getElementById('mapLoading').innerHTML = '<p>Data tidak cukup untuk menampilkan Heatmap</p>';
                return;
            }

            // Sensitivitas maksimum mengikuti rumus yang sama
            const maxVal = 2.0 - (intensityVal / 100);

            // Pemetaan data mentah (tanpa fetch AJAX ke backend grid)
            const heatmapData = kejadianData.map(data => [data.lat, data.lng, 1]);

            // Render Heatmap Layer
            L.heatLayer(heatmapData, {
                radius: radiusVal,
                blur: blurVal,
                maxZoom: 15,
                max: maxVal,
                gradient: { 
                    0.0: '#00cc44', 
                    0.3: '#88cc00', 
                    0.5: '#ffcc00', 
                    0.7: '#ff8800', 
                    0.8: '#ff4400', 
                    1.0: '#ff0000' 
                }
            }).addTo(map);

            // Menyesuaikan tampilan peta (fit bounds) dengan koordinat
            const latLngs = kejadianData.map(d => [d.lat, d.lng]);
            map.fitBounds(latLngs, { padding: [50, 50] });

            // Hilangkan animasi loading
            document.getElementById('mapLoading').style.display = 'none';
        }

        document.addEventListener('DOMContentLoaded', loadKDE);
    </script>
</body>

</html>