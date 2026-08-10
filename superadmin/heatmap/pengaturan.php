<?php
// superadmin/heatmap/pengaturan.php
require_once __DIR__ . '/../../includes/config.php';
require_once __DIR__ . '/../../includes/session.php';
require_once __DIR__ . '/../../includes/functions.php';

checkAuth();
checkRole(['super_admin']);

$user = getCurrentUser();

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

$heatmapSettings = getHeatmapSettings();
$conn = getConnection();
$total_bpk = $conn->query("SELECT COUNT(*) as total FROM bpk")->fetch_assoc()['total'];

// Filter Preview
$filterKecamatan = $_GET['kecamatan'] ?? '';
$filterStartDate = $_GET['start_date'] ?? '';
$filterEndDate = $_GET['end_date'] ?? '';

$sql_kejadian = "SELECT * FROM kejadian_kebakaran WHERE latitude IS NOT NULL AND longitude IS NOT NULL";
if (!empty($filterKecamatan)) $sql_kejadian .= " AND kecamatan = '" . $conn->real_escape_string($filterKecamatan) . "'";
if (!empty($filterStartDate) && !empty($filterEndDate)) $sql_kejadian .= " AND DATE(waktu) BETWEEN '" . $conn->real_escape_string($filterStartDate) . "' AND '" . $conn->real_escape_string($filterEndDate) . "'";
$sql_kejadian .= " ORDER BY waktu DESC";

$kejadian = $conn->query($sql_kejadian);
$totalKejadian = $kejadian->num_rows;

$data_kejadian = [];
while ($row = $kejadian->fetch_assoc()) {
    $data_kejadian[] = [
        'lat' => (float)$row['latitude'], 'lng' => (float)$row['longitude'],
        'waktu' => $row['waktu'], 'alamat' => $row['alamat'], 'kecamatan' => $row['kecamatan']
    ];
}
$kejadianJson = json_encode($data_kejadian);

$kecamatanStats = $conn->query("
    SELECT kecamatan, COUNT(*) as total, AVG(latitude) as avg_lat, AVG(longitude) as avg_lng
    FROM kejadian_kebakaran WHERE latitude IS NOT NULL AND longitude IS NOT NULL GROUP BY kecamatan
");

$success = ''; $error = '';
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $radius = (int)$_POST['radius'];
    $blur = (int)$_POST['blur'];
    $intensity = (int)$_POST['intensity'];

    if ($radius < 10 || $radius > 50) { $error = 'Radius harus antara 10-50'; } 
    elseif ($blur < 5 || $blur > 30) { $error = 'Blur harus antara 5-30'; } 
    elseif ($intensity < 10 || $intensity > 100) { $error = 'Intensitas harus antara 10-100%'; } 
    else {
        $stmt = $conn->prepare("INSERT INTO heatmap_settings (radius, blur, intensity) VALUES (?, ?, ?)");
        $stmt->bind_param("iii", $radius, $blur, $intensity);
        if ($stmt->execute()) {
            catatLog($conn, $user, "Memperbarui konfigurasi Heatmap (Radius: $radius, Blur: $blur, Intensitas: $intensity%)");
            $success = 'Pengaturan heatmap berhasil disimpan!';
            $heatmapSettings = getHeatmapSettings();
        } else { $error = 'Gagal menyimpan pengaturan'; }
        $stmt->close();
    }
}
$conn->close();
include __DIR__ . '/../../includes/sidebar.php';
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Pengaturan Heatmap - BARRES 698</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/sweetalert2@11/dist/sweetalert2.min.css">
    <link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css" />
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; }
        body { font-family: 'Poppins', sans-serif; background: linear-gradient(135deg, #E5E7EB 0%, #D1D5DB 100%); min-height: 100vh; }
        .main-content { margin-left: 280px; padding: 24px 32px; min-height: 100vh; }
        .top-navbar { background: #FFFFFF; border: 1px solid rgba(0,0,0,0.08); border-radius: 20px; padding: 12px 24px; margin-bottom: 28px; display: flex; justify-content: space-between; align-items: center; }
        .page-title h2 { font-size: 20px; font-weight: 600; margin: 0; color: #1A1A1A; }
        .page-title p { font-size: 13px; margin: 4px 0 0 0; color: #666; }
        .user-info { text-align: right; }
        .user-info .username { font-size: 14px; font-weight: 600; color: #1A1A1A; }
        .user-info .role { font-size: 11px; color: #F7B801; }
        .user-avatar { width: 44px; height: 44px; background: linear-gradient(135deg, #F7B801, #E5A800); border-radius: 14px; display: flex; align-items: center; justify-content: center; cursor: pointer; }
        .user-avatar i { font-size: 22px; color: #1A1A1A; }
        .stat-card { background: #FFFFFF; border: 1px solid rgba(0,0,0,0.08); border-radius: 20px; padding: 20px; transition: all 0.3s ease; }
        .stat-icon { width: 55px; height: 55px; background: rgba(247,184,1,0.1); border-radius: 16px; display: flex; align-items: center; justify-content: center; }
        .stat-icon i { font-size: 28px; color: #F7B801; }
        .stat-number { font-size: 32px; font-weight: 700; margin-bottom: 5px; color: #1A1A1A; }
        .stat-label { font-size: 13px; font-weight: 500; color: #666; }
        .card-custom { background: #FFFFFF; border: 1px solid rgba(0,0,0,0.08); border-radius: 20px; overflow: hidden; margin-bottom: 24px; }
        .card-header-custom { padding: 18px 24px; display: flex; justify-content: space-between; align-items: center; border-bottom: 1px solid rgba(0,0,0,0.08); }
        .card-header-custom h3 { font-size: 16px; font-weight: 600; margin: 0; color: #F7B801; }
        .btn-gold { background: linear-gradient(135deg, #F7B801, #E5A800); border: none; padding: 10px 20px; border-radius: 14px; font-weight: 600; font-size: 13px; color: #1A1A1A; transition: all 0.3s ease; }
        .btn-outline-gold { background: transparent; border: 1px solid rgba(247,184,1,0.4); padding: 10px 20px; border-radius: 14px; font-weight: 600; font-size: 13px; color: #F7B801; transition: all 0.2s; }
        .map-container { height: 500px; width: 100%; border-radius: 16px; overflow: hidden; position: relative; }
        #heatmapPreview { height: 100%; width: 100%; }
        .slider-group { padding: 18px; border-radius: 14px; margin-bottom: 20px; background: #F8F8F8; border: 1px solid #E0E0E0; }
        .slider-group label { font-weight: 600; font-size: 13px; color: #1A1A1A; }
        .slider-value { background: linear-gradient(135deg, #F7B801, #E5A800); color: #1A1A1A; padding: 4px 12px; border-radius: 20px; font-size: 12px; font-weight: bold; }
        .range-slider { width: 100%; height: 6px; border-radius: 5px; appearance: none; background: #E0E0E0; }
        .range-slider::-webkit-slider-thumb { appearance: none; width: 20px; height: 20px; border-radius: 50%; background: #F7B801; cursor: pointer; border: 2px solid #FFFFFF; }
        .info-box { border-radius: 14px; padding: 15px; margin-bottom: 15px; background: #F8F9FA; border-left: 4px solid #F7B801; }
        .info-box h6 { margin-bottom: 8px; font-weight: 600; font-size: 14px; color: #1A1A1A; }
        .info-box p { font-size: 12px; line-height: 1.5; color: #666; margin-bottom:0; }
        .gradient-preview { height: 8px; border-radius: 4px; margin: 12px 0; background: linear-gradient(to right, #00cc44, #ffcc00, #ff3300); }
        .legend-item { display: flex; align-items: center; gap: 10px; font-size: 12px; color: #666; font-weight:600; }
        .legend-color { width: 16px; height: 16px; border-radius: 4px; }
    </style>
</head>
<body>
    <div class="main-content">
        <div class="top-navbar">
            <div class="page-title">
                <h2>Pengaturan Heatmap</h2>
                <p>Konfigurasi visualisasi Kepadatan Titik Kebakaran</p>
            </div>
            <div class="user-dropdown" style="display: flex; align-items: center; gap: 15px;">
                <div class="user-info">
                    <div class="username"><?= htmlspecialchars($user['username']) ?></div>
                    <div class="role">Super Administrator</div>
                </div>
                <div class="user-avatar"><i class="fas fa-user"></i></div>
            </div>
        </div>

        <div class="row g-4 mb-4">
            <div class="col-md-3 col-sm-6"><div class="stat-card"><div class="d-flex justify-content-between align-items-start"><div><div class="stat-number"><?= $totalKejadian ?></div><div class="stat-label">Kejadian Tampil</div></div><div class="stat-icon"><i class="fas fa-fire"></i></div></div></div></div>
            <div class="col-md-3 col-sm-6"><div class="stat-card"><div class="d-flex justify-content-between align-items-start"><div><div class="stat-number" id="cardRadius"><?= $heatmapSettings['radius'] ?? 25 ?></div><div class="stat-label">Radius (px)</div></div><div class="stat-icon"><i class="fas fa-expand-alt"></i></div></div></div></div>
            <div class="col-md-3 col-sm-6"><div class="stat-card"><div class="d-flex justify-content-between align-items-start"><div><div class="stat-number" id="cardBlur"><?= $heatmapSettings['blur'] ?? 15 ?></div><div class="stat-label">Tingkat Blur</div></div><div class="stat-icon"><i class="fas fa-brush"></i></div></div></div></div>
            <div class="col-md-3 col-sm-6"><div class="stat-card"><div class="d-flex justify-content-between align-items-start"><div><div class="stat-number" id="cardIntensity"><?= $heatmapSettings['intensity'] ?? 70 ?>%</div><div class="stat-label">Intensitas</div></div><div class="stat-icon"><i class="fas fa-chart-line"></i></div></div></div></div>
        </div>

        <div class="card-custom mb-4">
            <div class="card-body-custom p-4">
                <form method="GET" class="row align-items-end g-3">
                    <div class="col-md-3">
                        <label class="form-label" style="font-size: 13px; font-weight: 600;"><i class="fas fa-map-marker-alt me-1 text-warning"></i> Kecamatan</label>
                        <select name="kecamatan" class="form-select" style="font-size: 13px;">
                            <option value="">-- Semua Kecamatan --</option>
                            <option value="Banjarbaru Utara" <?= $filterKecamatan == 'Banjarbaru Utara' ? 'selected' : '' ?>>Banjarbaru Utara</option>
                            <option value="Banjarbaru Selatan" <?= $filterKecamatan == 'Banjarbaru Selatan' ? 'selected' : '' ?>>Banjarbaru Selatan</option>
                            <option value="Cempaka" <?= $filterKecamatan == 'Cempaka' ? 'selected' : '' ?>>Cempaka</option>
                            <option value="Landasan Ulin" <?= $filterKecamatan == 'Landasan Ulin' ? 'selected' : '' ?>>Landasan Ulin</option>
                            <option value="Liang Anggang" <?= $filterKecamatan == 'Liang Anggang' ? 'selected' : '' ?>>Liang Anggang</option>
                        </select>
                    </div>
                    <div class="col-md-3"><label class="form-label" style="font-size: 13px; font-weight: 600;"><i class="fas fa-calendar-alt me-1 text-warning"></i> Dari Tanggal</label><input type="date" name="start_date" class="form-control" style="font-size: 13px;" value="<?= htmlspecialchars($filterStartDate) ?>"></div>
                    <div class="col-md-3"><label class="form-label" style="font-size: 13px; font-weight: 600;"><i class="fas fa-calendar-alt me-1 text-warning"></i> Sampai Tanggal</label><input type="date" name="end_date" class="form-control" style="font-size: 13px;" value="<?= htmlspecialchars($filterEndDate) ?>"></div>
                    <div class="col-md-3"><button type="submit" class="btn-gold w-100"><i class="fas fa-search me-2"></i> Terapkan Filter</button></div>
                </form>
            </div>
        </div>

        <div class="row">
            <!-- MAP PREVIEW -->
            <div class="col-lg-8">
                <div class="card-custom">
                    <div class="card-header-custom"><h3><i class="fas fa-map"></i> Preview Heatmap</h3><div><button class="btn-outline-gold" onclick="resetMapView()"><i class="fas fa-sync-alt"></i> Center Map</button></div></div>
                    <div class="card-body-custom" style="padding: 20px;">
                        <div class="map-container">
                            <div id="heatmapPreview"></div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- SETTINGS PANEL -->
            <div class="col-lg-4">
                <div class="card-custom">
                    <div class="card-header-custom"><h3><i class="fas fa-sliders-h"></i> Konfigurasi KDE</h3></div>
                    <div class="card-body-custom" style="padding: 20px;">
                        <form method="POST" id="heatmapForm">
                            <div class="slider-group"><div class="d-flex justify-content-between align-items-center mb-2"><label>Radius</label><span class="slider-value" id="radiusDisplay"><?= $heatmapSettings['radius'] ?? 25 ?></span></div><input type="range" class="range-slider" id="radiusSlider" name="radius" min="10" max="50" value="<?= $heatmapSettings['radius'] ?? 25 ?>" oninput="updateSliderDisplay()"></div>
                            <div class="slider-group"><div class="d-flex justify-content-between align-items-center mb-2"><label>Blur</label><span class="slider-value" id="blurDisplay"><?= $heatmapSettings['blur'] ?? 15 ?></span></div><input type="range" class="range-slider" id="blurSlider" name="blur" min="5" max="30" value="<?= $heatmapSettings['blur'] ?? 15 ?>" oninput="updateSliderDisplay()"></div>
                            <div class="slider-group"><div class="d-flex justify-content-between align-items-center mb-2"><label>Intensitas</label><span class="slider-value" id="intensityDisplay"><?= $heatmapSettings['intensity'] ?? 70 ?>%</span></div><input type="range" class="range-slider" id="intensitySlider" name="intensity" min="10" max="100" value="<?= $heatmapSettings['intensity'] ?? 70 ?>" oninput="updateSliderDisplay()"></div>
                            <button type="submit" class="btn-gold w-100 mt-2 mb-4" id="btnSimpan"><i class="fas fa-save me-1"></i> Simpan Konfigurasi</button>
                            
                            <h6 class="mb-2" style="font-weight: 600; font-size:14px; color: #1A1A1A;"><i class="fas fa-palette me-1" style="color: #F7B801;"></i> Legenda Warna</h6>
                            <div class="gradient-preview" style="background: linear-gradient(to right, #00cc44, #ffcc00, #ff3300);"></div>
                            <div class="d-flex justify-content-between mt-2 mb-4">
                                <div class="legend-item"><div class="legend-color" style="background: #00cc44;"></div> Rendah</div>
                                <div class="legend-item"><div class="legend-color" style="background: #ffcc00;"></div> Sedang</div>
                                <div class="legend-item"><div class="legend-color" style="background: #ff3300;"></div> Tinggi</div>
                            </div>
                        </form>
                        <hr style="border-color: #E0E0E0; margin: 15px 0 20px;">
                        <h6 class="mb-3" style="font-weight: 600; font-size:14px; color: #1A1A1A;"><i class="fas fa-info-circle me-1" style="color: #F7B801;"></i> Penjelasan Parameter</h6>
                        <div class="info-box"><h6><i class="fas fa-circle me-1"></i> Radius</h6><p>Jarak pengaruh titik. Semakin besar radius, semakin luas persebarannya.</p></div>
                        <div class="info-box"><h6><i class="fas fa-brush me-1"></i> Blur</h6><p>Tingkat kehalusan gradasi pinggiran kepadatan.</p></div>
                        <div class="info-box" style="margin-bottom: 0;"><h6><i class="fas fa-sun me-1"></i> Intensitas</h6><p>Tingkat kepekaan warna merah pada titik yang bertumpuk.</p></div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- SCRIPTS -->
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/leaflet.heat@0.2.0/dist/leaflet-heat.js"></script>

    <script>
        <?php if ($success): ?>Swal.fire({ icon: 'success', title: 'Berhasil!', text: '<?= $success ?>', showConfirmButton: false, timer: 2000, customClass: { popup: 'swal2-popup' } });<?php endif; ?>
        <?php if ($error): ?>Swal.fire({ icon: 'error', title: 'Gagal!', text: '<?= $error ?>', customClass: { popup: 'swal2-popup', confirmButton: 'swal2-confirm' } });<?php endif; ?>

        const map = L.map('heatmapPreview').setView([-3.468, 114.832], 12);
        L.tileLayer('https://{s}.basemaps.cartocdn.com/light_all/{z}/{x}/{y}{r}.png', { attribution: '&copy; OSM', maxZoom: 19 }).addTo(map);

        let kdeHeatmapLayer = null;
        let markers = [];
        
        // Menggunakan data mentah yang sudah diekstrak oleh PHP
        const kejadianData = <?= $kejadianJson ?>;

        // Fungsi menggambar titik merah aslinya
        function drawMarkers() {
            markers.forEach(m => map.removeLayer(m));
            markers = [];
            kejadianData.forEach(data => {
                const marker = L.circleMarker([data.lat, data.lng], { 
                    radius: 4, fillColor: '#ff3300', color: '#FFFFFF', weight: 1.5, fillOpacity: 0.9 
                }).addTo(map);
                markers.push(marker);
            });
        }

        // Fungsi Heatmap Dinamis murni dari Frontend Canvas
        function renderHeatmapLayer() {
            if (kejadianData.length < 1) return;

            const radius = parseInt(document.getElementById('radiusSlider').value);
            const blur = parseInt(document.getElementById('blurSlider').value);
            const intensity = parseInt(document.getElementById('intensitySlider').value);
            
            // Konversi persentase intensitas menjadi sensitivitas maksimum.
            // Semakin kecil max, semakin cepat warnanya memerah.
            const maxVal = 2.0 - (intensity / 100); 

            // Susun data untuk L.heatLayer: [lat, lng, bobot]
            const heatmapData = kejadianData.map(data => [data.lat, data.lng, 1]);

            if (kdeHeatmapLayer) map.removeLayer(kdeHeatmapLayer);
            
            kdeHeatmapLayer = L.heatLayer(heatmapData, {
                radius: radius,
                blur: blur,
                maxZoom: 15, // Leaflet.heat akan menyesuaikan kerapatan saat melewati batas zoom ini
                max: maxVal,
                gradient: { 0.0: '#00cc44', 0.3: '#88cc00', 0.5: '#ffcc00', 0.7: '#ff8800', 0.8: '#ff4400', 1.0: '#ff0000' }
            }).addTo(map);
        }

        function updateSliderDisplay() {
            document.getElementById('radiusDisplay').textContent = document.getElementById('radiusSlider').value;
            document.getElementById('blurDisplay').textContent = document.getElementById('blurSlider').value;
            document.getElementById('intensityDisplay').textContent = document.getElementById('intensitySlider').value + '%';
            document.getElementById('cardRadius').textContent = document.getElementById('radiusSlider').value;
            document.getElementById('cardBlur').textContent = document.getElementById('blurSlider').value;
            document.getElementById('cardIntensity').textContent = document.getElementById('intensitySlider').value + '%';
        }

        function resetMapView() {
            if (kejadianData.length > 0) { 
                const latLngs = kejadianData.map(d => [d.lat, d.lng]); 
                map.fitBounds(latLngs, { padding: [50, 50] }); 
            } else { 
                map.setView([-3.468, 114.832], 12); 
            }
        }

        // Event listener slider untuk rendering real-time
        document.querySelectorAll('.range-slider').forEach(slider => {
            slider.addEventListener('input', function() {
                updateSliderDisplay();
                renderHeatmapLayer(); // Tidak perlu timeout lagi karena prosesnya sangat ringan
            });
        });

        // Inisialisasi saat halaman dimuat
        document.addEventListener('DOMContentLoaded', function() {
            if (kejadianData.length > 0) { 
                drawMarkers();
                renderHeatmapLayer();
                resetMapView();
            }
        });
    </script>
</body>
</html>