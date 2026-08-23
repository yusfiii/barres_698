<?php
// public/peta-statistik.php
require_once __DIR__ . '/../includes/config.php';
require_once __DIR__ . '/../includes/session.php';
require_once __DIR__ . '/../includes/functions.php';

$conn = getConnection();
$result = $conn->query("SELECT * FROM kejadian_kebakaran ORDER BY waktu DESC");
$incidents = [];
if ($result) { while ($row = $result->fetch_assoc()) { $incidents[] = $row; } }

$hydrant_result = $conn->query("SELECT * FROM hydrant ORDER BY id DESC");
$hydrants = [];
if ($hydrant_result) { while ($row = $hydrant_result->fetch_assoc()) { $hydrants[] = $row; } }

$bpk_result = $conn->query("
    SELECT bpk.*, 
    (SELECT COUNT(*) FROM anggota WHERE bpk_id = bpk.id) AS jumlah_anggota 
    FROM bpk 
    ORDER BY nama_bpk
");
$bpks = [];
if ($bpk_result) { while ($row = $bpk_result->fetch_assoc()) { $bpks[] = $row; } }

$heatmapSettings = $conn->query("SELECT * FROM heatmap_settings ORDER BY id DESC LIMIT 1");
$heatmap = $heatmapSettings->fetch_assoc();
if (!$heatmap) { $heatmap = ['radius' => 25, 'blur' => 15, 'intensity' => 70]; }

$conn->close();

// Data mentah dikirim ke Javascript agar bisa dikalkulasi ulang saat filter aktif
$incidentsJson = json_encode($incidents);
$hydrantsJson = json_encode($hydrants);
$bpksJson = json_encode($bpks);
$kdeSettingsJson = json_encode($heatmap);
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Peta & Statistik - BARRES 698</title>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700;800&family=DM+Mono:wght@400;500&display=swap" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
    <link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css" />
    <link rel="stylesheet" href="https://unpkg.com/leaflet.markercluster@1.4.1/dist/MarkerCluster.css" />
    <link rel="stylesheet" href="https://unpkg.com/leaflet.markercluster@1.4.1/dist/MarkerCluster.Default.css" />
    <script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js"></script>
    <script src="https://unpkg.com/leaflet.markercluster@1.4.1/dist/leaflet.markercluster.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/leaflet.heat@0.2.0/dist/leaflet-heat.js"></script>
    <style>
        :root { --jet-black: #0D0D0D; --dark-grey: #2A2A2A; --gold: #F7B801; --gold-dark: #E0A600; --off-white: #F5F5F5; }
        * { margin: 0; padding: 0; box-sizing: border-box; }
        body { font-family: 'Poppins', sans-serif; background: var(--off-white); color: var(--jet-black); overflow-x: hidden; }
        
        /* Navbar */
        .site-nav { position: fixed; top: 0; left: 0; right: 0; z-index: 1050; display: flex; align-items: center; justify-content: space-between; padding: 18px 40px; background: rgba(13, 13, 13, 0.96); backdrop-filter: blur(14px); border-bottom: 1px solid rgba(247, 184, 1, 0.25); transition: padding .3s; }
        .site-nav.compact { padding: 12px 40px; }
        .nav-logo { display: flex; align-items: center; gap: 12px; text-decoration: none; }
        .nav-logo-icon { width: 38px; height: 38px; border-radius: 10px; background: var(--jet-black); display: flex; align-items: center; justify-content: center; overflow: hidden; }
        .nav-logo-icon img { width: 100%; height: 100%; object-fit: cover; }
        .nav-logo-text { font-family: 'Poppins', sans-serif; font-weight: 700; font-size: 1.4rem; letter-spacing: 1px; color: #fff; line-height: 1; }
        .nav-logo-sub { font-size: .6rem; color: rgba(255, 255, 255, .45); letter-spacing: 3px; text-transform: uppercase; display: block; font-weight: 400; }
        .nav-links { display: flex; align-items: center; gap: 6px; }
        .nav-links a { font-size: .82rem; font-weight: 500; letter-spacing: .5px; color: rgba(255, 255, 255, .65); text-decoration: none; padding: 7px 14px; border-radius: 8px; transition: color .2s, background .2s; }
        .nav-links a:hover, .nav-links a.active { color: #fff; background: rgba(255, 255, 255, .07); }
        .nav-cta { background: linear-gradient(135deg, var(--gold), var(--gold-dark)) !important; color: var(--jet-black) !important; padding: 7px 18px !important; font-weight: 600 !important; }
        .nav-toggle { display: none; background: none; border: none; cursor: pointer; padding: 4px; }
        .nav-toggle span { display: block; width: 22px; height: 2px; background: #fff; margin: 5px 0; transition: all .3s; }
        
        /* Hero */
        .page-hero { background: var(--jet-black); padding: 140px 0 60px; position: relative; overflow: hidden; }
        .hero-badge { display: inline-flex; align-items: center; gap: 10px; font-family: 'DM Mono', monospace; font-size: .7rem; letter-spacing: 3px; text-transform: uppercase; color: var(--gold); margin-bottom: 24px; }
        .hero-badge::before { content: ''; display: block; width: 28px; height: 2px; background: var(--gold); }
        .page-hero h1 { font-family: 'Poppins', sans-serif; font-weight: 800; font-size: clamp(2.5rem, 5vw, 4rem); color: #fff; margin-bottom: 20px; }
        .page-hero .lead { color: rgba(255, 255, 255, .5); font-size: 1.1rem; max-width: 600px; }
        
        /* General Map & Layout */
        #map { height: 550px; width: 100%; border-radius: 20px; box-shadow: 0 8px 30px rgba(0, 0, 0, 0.2); }
        .map-container { position: relative; }
        .filter-section { background: var(--dark-grey); border-radius: 20px; padding: 24px; margin-bottom: 30px; border: 1px solid rgba(247, 184, 1, 0.12); }
        .filter-section label { color: var(--gold); font-size: .75rem; font-weight: 600; letter-spacing: 1px; margin-bottom: 8px; display: block; }
        .form-control-custom, .form-select-custom { background: rgba(255, 255, 255, 0.05); border: 1px solid rgba(247, 184, 1, 0.2); border-radius: 12px; padding: 12px 16px; color: #fff; width: 100%; }
        .form-control-custom option, .form-select-custom option { background: var(--dark-grey); color: #fff; }
        .form-check-custom { display: flex; align-items: center; gap: 10px; color: rgba(255, 255, 255, 0.7); font-size: .85rem; cursor: pointer; padding: 10px 0; }
        .btn-gold { background: linear-gradient(135deg, var(--gold), var(--gold-dark)); color: var(--jet-black); font-weight: 600; padding: 12px 24px; border-radius: 12px; border: none; transition: all .3s; }
        .btn-outline-gold { background: transparent; border: 2px solid var(--gold); color: var(--gold); padding: 10px 20px; border-radius: 12px; font-weight: 600; transition: all .3s; }
        .legend-card { background: rgba(13, 13, 13, 0.95); backdrop-filter: blur(10px); border-radius: 16px; padding: 16px 20px; border: 1px solid rgba(247, 184, 1, 0.2); margin-top: 16px; }
        .legend-card h6 { color: var(--gold); font-family: 'DM Mono', monospace; font-size: .7rem; letter-spacing: 2px; margin-bottom: 12px; }
        .legend-item { display: flex; align-items: center; gap: 12px; padding: 4px 0; font-size: .82rem; color: rgba(255, 255, 255, .8); }
        .legend-icon { width: 20px; height: 20px; border-radius: 50%; display: flex; align-items: center; justify-content: center; font-size: .7rem; flex-shrink: 0; }
        .legend-heatmap { display: flex; align-items: center; gap: 6px; padding: 4px 0; font-size: .75rem; color: rgba(255, 255, 255, .6); }
        .legend-heatmap .gradient-bar { width: 80px; height: 8px; border-radius: 4px; background: linear-gradient(to right, #00cc44, #ffcc00, #ff3300); }
        .stat-card-mini { background: var(--dark-grey); border-radius: 16px; padding: 20px; text-align: center; border: 1px solid rgba(247, 184, 1, 0.12); }
        .stat-card-mini .number { font-family: 'Poppins', sans-serif; font-weight: 800; font-size: 2rem; color: var(--gold); }
        .stat-card-mini .label { font-size: .7rem; text-transform: uppercase; color: rgba(255, 255, 255, .5); }
        .stat-card-mini .icon { font-size: 1.5rem; color: var(--gold); margin-bottom: 4px; }
        .chart-card { background: var(--dark-grey); border-radius: 20px; padding: 24px; border: 1px solid rgba(247, 184, 1, 0.12); height: 100%; }
        .chart-card .card-header-custom h5 { font-family: 'Poppins', sans-serif; font-weight: 600; color: var(--gold); margin: 0 0 16px 0; }
        .data-table-card { background: var(--dark-grey); border-radius: 20px; padding: 24px; border: 1px solid rgba(247, 184, 1, 0.12); margin-top: 30px; }
        .data-table-card h5 { font-family: 'Poppins', sans-serif; font-weight: 600; color: var(--gold); margin-bottom: 20px; }
        .table-custom { color: rgba(255, 255, 255, .8); }
        .table-custom thead th { background: rgba(0, 0, 0, 0.3); border-bottom: 1px solid rgba(247, 184, 1, 0.2); color: var(--gold); font-weight: 600; font-size: .75rem; letter-spacing: 1px; }
        .table-custom tbody td { border-bottom: 1px solid rgba(247, 184, 1, 0.08); vertical-align: middle; }
        .reveal { opacity: 0; transform: translateY(28px); transition: opacity .7s ease, transform .7s ease; }
        .reveal.visible { opacity: 1; transform: none; }
        .loading-overlay { position: absolute; inset: 0; background: rgba(13, 13, 13, 0.85); backdrop-filter: blur(8px); display: none; justify-content: center; align-items: center; z-index: 1000; border-radius: 20px; }
        .kde-info { position: absolute; bottom: 15px; left: 15px; background: rgba(13, 13, 13, 0.9); backdrop-filter: blur(8px); color: #fff; padding: 6px 14px; border-radius: 10px; font-size: 10px; font-family: 'DM Mono', monospace; z-index: 1000; border: 1px solid rgba(247, 184, 1, 0.3); }
        
        /* Popup Custom Styling */
        .leaflet-popup-content-wrapper { border-radius: 12px; box-shadow: 0 4px 15px rgba(0,0,0,0.2); }
        .leaflet-popup-content { margin: 15px; }

        /* FOOTER CSS */
        .site-footer { background: var(--jet-black); padding: 60px 0 32px; border-top: 1px solid rgba(247, 184, 1, 0.1); margin-top: 60px; }
        .footer-brand { display: flex; align-items: center; gap: 12px; margin-bottom: 20px; }
        .footer-brand-icon { width: 40px; height: 40px; border-radius: 12px; background: linear-gradient(135deg, var(--gold), var(--gold-dark)); display: flex; align-items: center; justify-content: center; }
        .footer-brand-icon i { color: var(--jet-black); font-size: 1.1rem; }
        .footer-brand-name { font-family: 'Poppins', sans-serif; font-weight: 700; font-size: 1.5rem; letter-spacing: 1px; color: #fff; line-height: 1; }
        .footer-brand-tagline { font-size: .65rem; color: rgba(255, 255, 255, .35); letter-spacing: 2px; text-transform: uppercase; }
        .footer-desc { font-size: .87rem; line-height: 1.75; max-width: 300px; color: rgba(255, 255, 255, .3); }
        .footer-heading { font-family: 'DM Mono', monospace; font-size: .68rem; letter-spacing: 3px; text-transform: uppercase; color: var(--gold); margin-bottom: 20px; }
        .footer-links { list-style: none; padding: 0; margin: 0; }
        .footer-links li { margin-bottom: 10px; }
        .footer-links a { font-size: .88rem; color: rgba(255, 255, 255, .4); text-decoration: none; transition: color .2s; }
        .footer-links a:hover { color: var(--gold); }
        .footer-contact-item { display: flex; align-items: center; gap: 12px; font-size: .85rem; color: rgba(255, 255, 255, .4); margin-bottom: 12px; }
        .footer-contact-item i { color: var(--gold); width: 16px; }
        .emergency-box { background: rgba(247, 184, 1, 0.08); border: 1px solid rgba(247, 184, 1, 0.2); border-radius: 16px; padding: 24px; text-align: center; }
        .emergency-box .label { font-size: .65rem; letter-spacing: 3px; text-transform: uppercase; color: rgba(255, 255, 255, .4); font-family: 'DM Mono', monospace; margin-bottom: 8px; }
        .emergency-box .number { font-family: 'Poppins', sans-serif; font-weight: 700; font-size: 3rem; color: var(--gold); line-height: 1; }
        .footer-divider { border: none; border-top: 1px solid rgba(255, 255, 255, .07); margin: 40px 0 24px; }
        .footer-bottom { display: flex; align-items: center; justify-content: space-between; font-size: .78rem; color: rgba(255, 255, 255, .3); }
        .footer-socials { display: flex; gap: 12px; }
        .footer-socials a { width: 34px; height: 34px; border-radius: 10px; background: rgba(255, 255, 255, .05); border: 1px solid rgba(255, 255, 255, .08); display: flex; align-items: center; justify-content: center; color: rgba(255, 255, 255, .4); text-decoration: none; transition: all .2s; }
        .footer-socials a:hover { background: var(--gold); border-color: var(--gold); color: var(--jet-black); }

        @media (max-width: 992px) { 
            .nav-links { display: none; } 
            .nav-toggle { display: block; } 
            .footer-bottom { flex-direction: column; gap: 16px; text-align: center; }
        }
    </style>
</head>
<body>
    <nav class="site-nav" id="siteNav">
        <a class="nav-logo" href="index.php">
            <div class="nav-logo-icon"><img src="../assets/barres2.png" alt="BARRES Logo"></div>
            <div><span class="nav-logo-text">BARRES 698</span><span class="nav-logo-sub">Banjarbaru Rescue</span></div>
        </a>
        <div class="nav-links" id="navLinks">
            <a href="index.php">Beranda</a><a href="profil.php">Profil</a><a href="peta-statistik.php" class="active">Peta & Statistik</a><a href="kontak.php">Kontak</a>
            <?php if (function_exists('isLoggedIn') && isLoggedIn()): ?>
                <a href="<?= $_SESSION['role'] == 'super_admin' ? '../superadmin/dashboard.php' : '../adminbpk/dashboard.php' ?>" class="nav-cta">Dashboard</a>
                <a href="../logout.php">Logout</a>
            <?php else: ?><a href="login.php" class="nav-cta">Login Admin</a><?php endif; ?>
        </div>
        <button class="nav-toggle" id="navToggle" aria-label="Toggle menu"><span></span><span></span><span></span></button>
    </nav>

    <section class="page-hero">
        <div class="container">
            <div class="hero-badge reveal">Visualisasi Data</div>
            <h1 class="reveal">Peta & <span style="color: var(--gold);">Statistik</span></h1>
            <p class="lead reveal">Visualisasi persebaran kejadian kebakaran, titik hydrant, dan unit BPK di Kota Banjarbaru</p>
        </div>
    </section>

    <div class="container py-4">
        <div class="filter-section reveal">
            <div class="row align-items-end">
                <div class="col-md-3 mb-3">
                    <label><i class="fas fa-map-marker-alt label-icon"></i> KECAMATAN</label>
                    <select id="filterKecamatan" class="form-select-custom">
                        <option value="">-- Semua Kecamatan --</option>
                        <option value="Banjarbaru Utara">Banjarbaru Utara</option>
                        <option value="Banjarbaru Selatan">Banjarbaru Selatan</option>
                        <option value="Cempaka">Cempaka</option>
                        <option value="Landasan Ulin">Landasan Ulin</option>
                        <option value="Liang Anggang">Liang Anggang</option>
                    </select>
                </div>
                <div class="col-md-3 mb-3">
                    <label><i class="fas fa-layer-group label-icon"></i> TIPE PENANDA</label>
                    <select id="filterTipe" class="form-select-custom">
                        <option value="all">-- Semua --</option>
                        <option value="kebakaran" selected>🔥 Titik Kebakaran</option>
                        <option value="hydrant">🚒 Titik Hydrant</option>
                        <option value="bpk">🏛️ Titik BPK</option>
                    </select>
                </div>
                <div class="col-md-2 mb-3"><label><i class="fas fa-calendar-alt label-icon"></i> DARI TANGGAL</label><input type="date" id="filterDateFrom" class="form-control-custom"></div>
                <div class="col-md-2 mb-3"><label><i class="fas fa-calendar-alt label-icon"></i> SAMPAI TANGGAL</label><input type="date" id="filterDateTo" class="form-control-custom"></div>
                <div class="col-md-2 mb-3"><button class="btn-gold w-100" onclick="applyFilter()"><i class="fas fa-search me-2"></i> Terapkan</button></div>
            </div>
            <div class="row mt-2">
                <div class="col-md-12">
                    <label class="form-check-custom">
                        <input type="checkbox" id="showHeatmap" checked>
                        <span><i class="fas fa-fire me-1" style="color: var(--gold);"></i> Tampilkan Heatmap KDE</span>
                    </label>
                </div>
            </div>
        </div>

        <div class="row">
            <div class="col-lg-9 mb-3">
                <div class="map-container reveal">
                    <div id="map"></div>
                    <div class="loading-overlay" id="loadingOverlay"><div class="text-center"><div class="spinner-border text-warning"></div></div></div>
                    <div class="kde-info" id="kdeInfo"><i class="fas fa-cog"></i> Menunggu data KDE...</div>
                </div>
            </div>
            <div class="col-lg-3 mb-3">
                <div class="legend-card reveal">
                    <h6><i class="fas fa-palette me-2"></i> LEGENDA</h6>
                    <div class="legend-item"><div class="legend-icon" style="background: #ff4444; width: 14px; height: 14px;"></div> Titik Kebakaran</div>
                    <div class="legend-item"><div class="legend-icon" style="background: #00ccff; width: 14px; height: 14px;"></div> Titik Hydrant</div>
                    <div class="legend-item"><div class="legend-icon" style="background: #ffaa00; width: 14px; height: 14px;"></div> Titik BPK</div>
                    <hr style="border-color: rgba(247, 184, 1, 0.2); margin: 12px 0;">
                    <div class="legend-heatmap">
                        <span>KDE Heatmap</span>
                        <div class="gradient-bar" style="background: linear-gradient(to right, #00cc44, #ffcc00, #ff3300); width: 100px;"></div>
                        <span style="font-size: .6rem;">Min → Max</span>
                    </div>
                    <hr style="border-color: rgba(247, 184, 1, 0.2); margin: 12px 0;">
                    <div class="legend-item" style="font-size: .7rem; color: rgba(255,255,255,.4);"><i class="fas fa-info-circle me-2"></i>Klik marker untuk detail</div>
                </div>
                <div class="mt-3"><button class="btn-outline-gold w-100" onclick="resetMap()"><i class="fas fa-home me-2"></i> Reset Peta</button></div>
            </div>
        </div>

        <div class="row mb-4 mt-4">
            <div class="col-md-3 mb-3 reveal"><div class="stat-card-mini"><div class="icon"><i class="fas fa-fire"></i></div><div class="number" id="statTotal">0</div><div class="label">Total Kejadian</div></div></div>
            <div class="col-md-3 mb-3 reveal"><div class="stat-card-mini"><div class="icon"><i class="fas fa-user-injured"></i></div><div class="number" id="statLuka">0</div><div class="label">Korban Luka</div></div></div>
            <div class="col-md-3 mb-3 reveal"><div class="stat-card-mini"><div class="icon"><i class="fas fa-skull"></i></div><div class="number" id="statJiwa">0</div><div class="label">Korban Jiwa</div></div></div>
            <div class="col-md-3 mb-3 reveal"><div class="stat-card-mini"><div class="icon"><i class="fas fa-building"></i></div><div class="number" id="statBangunan">0</div><div class="label">Bangunan Terdampak</div></div></div>
        </div>

        <div class="row mb-4">
            <div class="col-lg-4 mb-3 reveal"><div class="chart-card"><div class="card-header-custom"><h5><i class="fas fa-chart-bar me-2"></i> Per Kecamatan</h5></div><canvas id="kecamatanChart"></canvas></div></div>
            <div class="col-lg-4 mb-3 reveal"><div class="chart-card"><div class="card-header-custom"><h5><i class="fas fa-chart-line me-2"></i> Tren Kejadian</h5></div><canvas id="trendChart"></canvas></div></div>
            <div class="col-lg-4 mb-3 reveal"><div class="chart-card"><div class="card-header-custom"><h5><i class="fas fa-chart-pie me-2"></i> Penyebab</h5></div><canvas id="causeChart"></canvas></div></div>
        </div>

        <div class="data-table-card reveal">
            <h5><i class="fas fa-table me-2"></i> 5 Data Kejadian Kebakaran Terbaru</h5>
            <div class="table-responsive">
                <table class="table table-custom" id="incidentTable">
                    <thead><tr><th>No</th><th>Waktu</th><th>Lokasi</th><th>Kecamatan</th><th>Kelurahan</th><th>Luka</th><th>Jiwa</th><th>Bangunan</th><th>Penyebab</th></tr></thead>
                    <tbody id="tableBody">
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <!-- FOOTER LENGKAP -->
    <footer class="site-footer">
        <div class="container">
            <div class="row gy-5">
                <div class="col-lg-4">
                    <div class="footer-brand">
                        <div class="footer-brand-icon"><i class="fas fa-fire"></i></div>
                        <div>
                            <div class="footer-brand-name">BARRES 698</div>
                            <div class="footer-brand-tagline">Banjarbaru Rescue</div>
                        </div>
                    </div>
                    <p class="footer-desc">
                        Sistem Informasi Geografis pemetaan lokasi kebakaran berbasis web dengan metode Kernel Density Estimation (KDE).
                    </p>
                </div>

                <div class="col-6 col-lg-2">
                    <div class="footer-heading">Menu</div>
                    <ul class="footer-links">
                        <li><a href="index.php">Beranda</a></li>
                        <li><a href="profil.php">Profil</a></li>
                        <li><a href="peta-statistik.php">Peta & Statistik</a></li>
                        <li><a href="kontak.php">Kontak</a></li>
                    </ul>
                </div>

                <div class="col-6 col-lg-3">
                    <div class="footer-heading">Kontak</div>
                    <div class="footer-contact-item">
                        <i class="fas fa-map-marker-alt"></i>
                        <span>Kota Banjarbaru, Kalimantan Selatan</span>
                    </div>
                    <div class="footer-contact-item">
                        <i class="fas fa-phone"></i>
                        <span>(0851) 11315698</span>
                    </div>
                    <div class="footer-contact-item">
                        <i class="fas fa-envelope"></i>
                        <span>barres698.banjarbaru@gmail.com</span>
                    </div>
                </div>

                <div class="col-lg-3">
                    <div class="footer-heading">Darurat</div>
                    <div class="emergency-box">
                        <div class="label">Pemadam Kebakaran</div>
                        <div class="number">112</div>
                    </div>
                </div>
            </div>

            <hr class="footer-divider">

            <div class="footer-bottom">
                <span>&copy; <?= date('Y') ?> BARRES 698 — SIG Pemetaan Kebakaran Banjarbaru</span>
                <div class="footer-socials">
                    <a href="#" aria-label="Facebook"><i class="fab fa-facebook-f"></i></a>
                    <a href="#" aria-label="Instagram"><i class="fab fa-instagram"></i></a>
                    <a href="#" aria-label="Twitter"><i class="fab fa-twitter"></i></a>
                </div>
            </div>
        </div>
    </footer>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        const incidentsData = <?= $incidentsJson ?>;
        const hydrantsData = <?= $hydrantsJson ?>;
        const bpksData = <?= $bpksJson ?>;
        const kdeSettings = <?= $kdeSettingsJson ?>;

        const nav = document.getElementById('siteNav');
        window.addEventListener('scroll', () => { nav.classList.toggle('compact', window.scrollY > 60); });

        const reveals = document.querySelectorAll('.reveal');
        const observer = new IntersectionObserver((entries) => { entries.forEach((e, i) => { if (e.isIntersecting) { setTimeout(() => e.target.classList.add('visible'), i * 60); observer.unobserve(e.target); } }); }, { threshold: 0.12 });
        reveals.forEach(el => observer.observe(el));
        document.querySelectorAll('.page-hero .reveal').forEach((el, i) => { setTimeout(() => el.classList.add('visible'), 200 + i * 100); });

        // PERBAIKAN: Mengembalikan peta ke warna dasar (light_all)
        const map = L.map('map').setView([-3.468, 114.832], 12);
        L.tileLayer('https://{s}.basemaps.cartocdn.com/light_all/{z}/{x}/{y}{r}.png', { attribution: '&copy; OSM', maxZoom: 19 }).addTo(map);

        let markerCluster = null;
        let heatmapLayer = null;
        let fireMarkersGroup = null;
        let currentIncidents = incidentsData;
        
        // Deklarasi Chart Instances
        let kecamatanChartInstance = null;
        let trendChartInstance = null;
        let causeChartInstance = null;

        const hydrantIcon = L.divIcon({ html: '<i class="fas fa-tint" style="color:#00ccff;font-size:18px;"></i>', className: '', iconSize: [20, 20], iconAnchor: [10, 10] });
        const bpkIcon = L.divIcon({ html: '<i class="fas fa-fire-extinguisher" style="color:#ffaa00;font-size:18px;"></i>', className: '', iconSize: [20, 20], iconAnchor: [10, 10] });

        function renderHeatmapLayer(incidents = currentIncidents) {
            const showHeatmap = document.getElementById('showHeatmap');
            if (heatmapLayer) {
                map.removeLayer(heatmapLayer);
                heatmapLayer = null;
            }

            if (!showHeatmap.checked || incidents.length < 1) {
                document.getElementById('kdeInfo').innerHTML = `<i class="fas fa-cog"></i> KDE: Heatmap dinonaktifkan atau data kosong`;
                return;
            }

            const radius = parseInt(kdeSettings.radius) || 25;
            const blur = parseInt(kdeSettings.blur) || 15;
            const intensity = parseInt(kdeSettings.intensity) || 70;
            const maxVal = 2.0 - (intensity / 100); 

            const heatmapData = incidents.map(data => {
                const lat = parseFloat(data.latitude || data.lat);
                const lng = parseFloat(data.longitude || data.lng);
                return [lat, lng, 1];
            });

            heatmapLayer = L.heatLayer(heatmapData, {
                radius: radius,
                blur: blur,
                maxZoom: 15,
                max: maxVal,
                gradient: { 0.0: '#00cc44', 0.3: '#88cc00', 0.5: '#ffcc00', 0.7: '#ff8800', 0.8: '#ff4400', 1.0: '#ff0000' }
            }).addTo(map);

            document.getElementById('kdeInfo').innerHTML = `<i class="fas fa-cog"></i> KDE: Radius ${radius}px | Intensitas ${intensity}% | ${incidents.length} titik`;
        }

        function createMarkerCluster() {
            if (markerCluster) { map.removeLayer(markerCluster); markerCluster = null; }
            markerCluster = L.markerClusterGroup({
                maxClusterRadius: 50,
                iconCreateFunction: function(cluster) {
                    const count = cluster.getChildCount();
                    const size = count < 10 ? 40 : (count < 50 ? 50 : 60);
                    return L.divIcon({ html: `<div style="background:rgba(247,184,1,0.9);border-radius:50%;width:${size}px;height:${size}px;display:flex;align-items:center;justify-content:center;font-weight:700;font-size:${size < 50 ? 14 : 12}px;color:#0D0D0D;border:2px solid #F7B801;box-shadow:0 4px 15px rgba(247,184,1,0.3);">${count}</div>`, className: '', iconSize: [size, size], iconAnchor: [size/2, size/2] });
                }
            });
            return markerCluster;
        }

        function updateMarkers(incidents, hydrants, bpks) {
            if (markerCluster) { map.removeLayer(markerCluster); markerCluster = null; }
            if (fireMarkersGroup) { map.removeLayer(fireMarkersGroup); fireMarkersGroup = null; }
            markerCluster = createMarkerCluster();
            fireMarkersGroup = L.featureGroup();
            const selectedTipe = document.getElementById('filterTipe').value;

            if (selectedTipe === 'all' || selectedTipe === 'kebakaran') {
                incidents.forEach(incident => {
                    const lat = parseFloat(incident.latitude || incident.lat);
                    const lng = parseFloat(incident.longitude || incident.lng);
                    
                    const popupContent = `
                        <div style="min-width: 200px; font-family: 'Poppins', sans-serif;">
                            <h6 style="color: #d9534f; margin-bottom: 10px; font-weight: 700; border-bottom: 1px solid #eee; padding-bottom: 6px;">
                                <i class="fas fa-fire me-1"></i> Detail Kebakaran
                            </h6>
                            <div style="font-size: 12px; line-height: 1.6; color: #333;">
                                <strong>Waktu:</strong> ${new Date(incident.waktu).toLocaleString('id-ID')}<br>
                                <strong>Kecamatan:</strong> ${incident.kecamatan}<br>
                                <strong>Kelurahan:</strong> ${incident.kelurahan || '-'}<br>
                                <strong>Alamat:</strong> ${incident.alamat}<br>
                                <strong>Penyebab:</strong> ${incident.penyebab || 'Tidak diketahui'}<br>
                                <div style="display: flex; justify-content: space-between; margin-top: 10px; padding-top: 8px; border-top: 1px dashed #ccc; font-weight: 600;">
                                    <span style="color: #d9534f;" title="Korban Jiwa"><i class="fas fa-skull"></i> ${incident.korban_jiwa || 0}</span>
                                    <span style="color: #f0ad4e;" title="Korban Luka"><i class="fas fa-user-injured"></i> ${incident.korban_luka || 0}</span>
                                    <span style="color: #5bc0de;" title="Bangunan Terdampak"><i class="fas fa-building"></i> ${incident.jumlah_bangunan || 0}</span>
                                </div>
                            </div>
                        </div>
                    `;
                    const marker = L.marker([lat, lng], { icon: L.divIcon({ className: 'dot-marker', html: `<div style="background-color: #ff3300; width: 14px; height: 14px; border-radius: 50%; border: 2px solid #ffffff; box-shadow: 0 2px 4px rgba(0,0,0,0.4);"></div>`, iconSize: [14, 14], iconAnchor: [7, 7], popupAnchor: [0, -7] }) }).bindPopup(popupContent);
                    fireMarkersGroup.addLayer(marker);
                });
            }
            if (selectedTipe === 'all' || selectedTipe === 'hydrant') {
                hydrants.forEach(hydrant => { 
                    const lat = parseFloat(hydrant.latitude || hydrant.lat);
                    const lng = parseFloat(hydrant.longitude || hydrant.lng);
                    const statusColor = (hydrant.status && hydrant.status.toLowerCase() === 'berfungsi') ? '#5cb85c' : '#d9534f';
                    const popupHydrant = `
                        <div style="min-width: 200px; font-family: 'Poppins', sans-serif;">
                            <h6 style="color: #00ccff; margin-bottom: 10px; font-weight: 700; border-bottom: 1px solid #eee; padding-bottom: 6px;">
                                <i class="fas fa-tint me-1"></i> Detail Hydrant
                            </h6>
                            <div style="font-size: 12px; line-height: 1.6; color: #333;">
                                <strong>Alamat:</strong> ${hydrant.alamat || '-'}<br>
                                <strong>Kecamatan:</strong> ${hydrant.kecamatan || '-'}<br>
                                <strong>Kelurahan:</strong> ${hydrant.kelurahan || '-'}<br>
                                <strong>Status:</strong> <span style="color: ${statusColor}; font-weight: 600;">${hydrant.status || '-'}</span><br>
                                <strong>Tahun Pemasangan:</strong> ${hydrant.tahun_pemasangan || '-'}
                            </div>
                        </div>
                    `;
                    markerCluster.addLayer(L.marker([lat, lng], { icon: hydrantIcon }).bindPopup(popupHydrant)); 
                });
            }
            if (selectedTipe === 'all' || selectedTipe === 'bpk') {
                bpks.forEach(bpk => { 
                    if ((bpk.latitude || bpk.lat) && (bpk.longitude || bpk.lng)) { 
                        const lat = parseFloat(bpk.latitude || bpk.lat);
                        const lng = parseFloat(bpk.longitude || bpk.lng);
                        const popupBpk = `
                            <div style="min-width: 200px; font-family: 'Poppins', sans-serif;">
                                <h6 style="color: #ffaa00; margin-bottom: 10px; font-weight: 700; border-bottom: 1px solid #eee; padding-bottom: 6px;">
                                    <i class="fas fa-fire-extinguisher me-1"></i> BPK ${bpk.nama_bpk}
                                </h6>
                                <div style="font-size: 12px; line-height: 1.6; color: #333;">
                                    <strong>Alamat:</strong> ${bpk.alamat || '-'}<br>
                                    <strong>Kecamatan:</strong> ${bpk.kecamatan || '-'}<br>
                                    <strong>Kelurahan:</strong> ${bpk.kelurahan || '-'}<br>
                                    <div style="display: flex; justify-content: space-between; margin-top: 10px; padding-top: 8px; border-top: 1px dashed #ccc; font-weight: 600;">
                                        <span style="color: #5bc0de;" title="Tahun Berdiri"><i class="fas fa-calendar-alt"></i> ${bpk.tahun_berdiri || '-'}</span>
                                        <span style="color: #f0ad4e;" title="Jumlah Anggota"><i class="fas fa-users"></i> ${bpk.jumlah_anggota || 0} Anggota</span>
                                    </div>
                                </div>
                            </div>
                        `;
                        markerCluster.addLayer(L.marker([lat, lng], { icon: bpkIcon }).bindPopup(popupBpk)); 
                    } 
                });
            }

            map.addLayer(markerCluster);
            map.addLayer(fireMarkersGroup);
            const allLayers = [...markerCluster.getLayers(), ...fireMarkersGroup.getLayers()];
            if (allLayers.length > 0) { map.fitBounds(L.featureGroup(allLayers).getBounds().pad(0.15)); }
        }

        // PERBAIKAN: Fungsi untuk Update Card & Grafik secara Real-time berdasarkan data Filter
        function updateDashboardData(incidents) {
            let totalLuka = 0, totalJiwa = 0, totalBangunan = 0;
            let kecCount = {};
            let trendCount = {};
            let causeCount = {};

            incidents.forEach(inc => {
                // Kalkulasi Stats Card
                totalLuka += parseInt(inc.korban_luka) || 0;
                totalJiwa += parseInt(inc.korban_jiwa) || 0;
                totalBangunan += parseInt(inc.jumlah_bangunan) || 0;

                // Hitung per Kecamatan
                let kec = inc.kecamatan ? inc.kecamatan : 'Tidak Diketahui';
                kecCount[kec] = (kecCount[kec] || 0) + 1;

                // Hitung per Bulan (Tren YYYY-MM)
                if (inc.waktu) {
                    let date = new Date(inc.waktu);
                    let month = (date.getMonth() + 1).toString().padStart(2, '0');
                    let ym = date.getFullYear() + '-' + month;
                    trendCount[ym] = (trendCount[ym] || 0) + 1;
                }

                // Hitung per Penyebab (hindari null)
                let cause = inc.penyebab ? inc.penyebab : 'Dalam penyelidikan';
                causeCount[cause] = (causeCount[cause] || 0) + 1;
            });

            // Update Angka di Card
            document.getElementById('statTotal').textContent = incidents.length.toLocaleString('id-ID');
            document.getElementById('statLuka').textContent = totalLuka.toLocaleString('id-ID');
            document.getElementById('statJiwa').textContent = totalJiwa.toLocaleString('id-ID');
            document.getElementById('statBangunan').textContent = totalBangunan.toLocaleString('id-ID');

            // Update Grafik Kecamatan
            if (kecamatanChartInstance) {
                kecamatanChartInstance.data.labels = Object.keys(kecCount);
                kecamatanChartInstance.data.datasets[0].data = Object.values(kecCount);
                kecamatanChartInstance.update();
            }

            // Update Grafik Tren (Diurutkan berdasarkan waktu secara Ascending)
            if (trendChartInstance) {
                let trendLabels = Object.keys(trendCount).sort();
                let trendDataArr = trendLabels.map(l => trendCount[l]);
                trendChartInstance.data.labels = trendLabels;
                trendChartInstance.data.datasets[0].data = trendDataArr;
                trendChartInstance.update();
            }

            // Update Grafik Penyebab (Diurutkan Descending, maksimal 10 besar)
            if (causeChartInstance) {
                let causeEntries = Object.entries(causeCount).sort((a, b) => b[1] - a[1]).slice(0, 10);
                causeChartInstance.data.labels = causeEntries.map(e => e[0]);
                causeChartInstance.data.datasets[0].data = causeEntries.map(e => e[1]);
                causeChartInstance.update();
            }
        }

        function initCharts() {
            // Inisialisasi Chart kosong terlebih dahulu, data akan diisi oleh updateDashboardData
            kecamatanChartInstance = new Chart(document.getElementById('kecamatanChart').getContext('2d'), { 
                type: 'bar', 
                data: { labels: [], datasets: [{ data: [], backgroundColor: '#F7B801', borderRadius: 6 }] }, 
                options: { plugins: { legend: { display: false } }, scales: { y: { ticks: { color: '#fff', stepSize: 1 } }, x: { ticks: { color: '#fff' } } } } 
            });
            
            trendChartInstance = new Chart(document.getElementById('trendChart').getContext('2d'), { 
                type: 'line', 
                data: { labels: [], datasets: [{ data: [], borderColor: '#F7B801', backgroundColor: 'rgba(247, 184, 1, 0.1)', fill: true, tension: 0.3 }] }, 
                options: { plugins: { legend: { display: false } }, scales: { y: { ticks: { color: '#fff', stepSize: 1 } }, x: { ticks: { color: '#fff' } } } } 
            });
            
            causeChartInstance = new Chart(document.getElementById('causeChart').getContext('2d'), { 
                type: 'doughnut', 
                data: { labels: [], datasets: [{ data: [], backgroundColor: ['#F7B801', '#ff6b6b', '#ffaa00', '#00ccff', '#00cc88', '#e83e8c', '#6f42c1', '#20c997'] }] }, 
                options: { plugins: { legend: { position: 'bottom', labels: { color: 'rgba(255,255,255,.8)' } } }, cutout: '55%' } 
            });
        }

        function applyFilter() {
            const kecamatan = document.getElementById('filterKecamatan').value;
            const tipe = document.getElementById('filterTipe').value;
            const dateFrom = document.getElementById('filterDateFrom').value;
            const dateTo = document.getElementById('filterDateTo').value;
            document.getElementById('loadingOverlay').style.display = 'flex';

            let params = new URLSearchParams();
            if (kecamatan) params.append('kecamatan', kecamatan);
            if (tipe !== 'all') params.append('tipe', tipe);
            if (dateFrom) params.append('date_from', dateFrom);
            if (dateTo) params.append('date_to', dateTo);

            fetch(`../api/get-peta-data.php?${params.toString()}`)
                .then(r => r.json())
                .then(data => {
                    currentIncidents = data.kebakaran || [];
                    updateMarkers(data.kebakaran || [], data.hydrant || [], data.bpk || []);
                    renderHeatmapLayer(currentIncidents);
                    updateTable(data.kebakaran || []);
                    
                    // PERBAIKAN: Kalkulasi ulang card dan grafik sesuai filter baru
                    updateDashboardData(currentIncidents);
                    
                    document.getElementById('loadingOverlay').style.display = 'none';
                }).catch(e => { document.getElementById('loadingOverlay').style.display = 'none'; });
        }

        function updateTable(data) {
            const tbody = document.getElementById('tableBody'); tbody.innerHTML = '';
            if (data.length === 0) { tbody.innerHTML = '<tr><td colspan="9" class="text-center text-muted py-3">Tidak ada data</td></tr>'; return; }
            
            const displayData = data.slice(0, 5);
            
            displayData.forEach((incident, index) => {
                tbody.insertRow().innerHTML = `<td>${index + 1}</td><td>${new Date(incident.waktu).toLocaleString('id-ID')}</td><td>${(incident.alamat || '').substring(0, 35)}...</td><td>${incident.kecamatan || '-'}</td><td>${incident.kelurahan || '-'}</td><td><span class="badge" style="background: rgba(247, 184, 1, 0.15); color: #F7B801;">${incident.korban_luka || 0}</span></td><td><span class="badge" style="background: rgba(220, 53, 69, 0.15); color: #ff6b6b;">${incident.korban_jiwa || 0}</span></td><td>${incident.jumlah_bangunan || 0}</td><td>${incident.penyebab || '-'}</td>`;
            });
        }

        function resetMap() {
            document.getElementById('filterKecamatan').value = ''; 
            document.getElementById('filterTipe').value = 'kebakaran'; 
            document.getElementById('filterDateFrom').value = ''; 
            document.getElementById('filterDateTo').value = ''; 
            document.getElementById('showHeatmap').checked = true;
            
            currentIncidents = incidentsData; 
            updateMarkers(incidentsData, hydrantsData, bpksData); 
            renderHeatmapLayer(incidentsData); 
            updateTable(incidentsData);
            
            // PERBAIKAN: Kembalikan card & grafik ke data awal
            updateDashboardData(incidentsData);
        }

        document.addEventListener('DOMContentLoaded', function() {
            initCharts();
            
            // Panggil sekali saat pertama load halaman agar data terisi
            updateMarkers(incidentsData, hydrantsData, bpksData); 
            renderHeatmapLayer(incidentsData);
            updateDashboardData(incidentsData);
            updateTable(incidentsData);

            document.querySelectorAll('#filterKecamatan, #filterTipe, #filterDateFrom, #filterDateTo').forEach(el => { el.addEventListener('change', applyFilter); });
            document.getElementById('showHeatmap').addEventListener('change', function() { renderHeatmapLayer(currentIncidents); });
        });
    </script>
</body>
</html>