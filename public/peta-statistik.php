<?php
// public/peta-statistik.php
require_once __DIR__ . '/../includes/config.php';
require_once __DIR__ . '/../includes/session.php';
require_once __DIR__ . '/../includes/functions.php';

// Get all fire incidents
$conn = getConnection();
$result = $conn->query("SELECT * FROM kejadian_kebakaran ORDER BY waktu DESC");
$incidents = [];
if ($result) {
    while ($row = $result->fetch_assoc()) {
        $incidents[] = $row;
    }
}

// Get all hydrants
$hydrant_result = $conn->query("SELECT * FROM hydrant ORDER BY id DESC");
$hydrants = [];
if ($hydrant_result) {
    while ($row = $hydrant_result->fetch_assoc()) {
        $hydrants[] = $row;
    }
}

// Get all BPK
$bpk_result = $conn->query("SELECT * FROM bpk ORDER BY nama_bpk");
$bpks = [];
if ($bpk_result) {
    while ($row = $bpk_result->fetch_assoc()) {
        $bpks[] = $row;
    }
}

// Get per-kecamatan statistics
$kecamatanStats = $conn->query("
    SELECT 
        kecamatan,
        COUNT(*) as total,
        SUM(korban_luka) as total_luka,
        SUM(korban_jiwa) as total_jiwa,
        SUM(jumlah_bangunan) as total_bangunan
    FROM kejadian_kebakaran
    GROUP BY kecamatan
    ORDER BY total DESC
");

$kecamatanData = [];
while ($row = $kecamatanStats->fetch_assoc()) {
    $kecamatanData[] = $row;
}

// Get monthly trend
$monthlyTrend = $conn->query("
    SELECT 
        DATE_FORMAT(waktu, '%Y-%m') as bulan,
        COUNT(*) as total
    FROM kejadian_kebakaran
    WHERE waktu >= DATE_SUB(NOW(), INTERVAL 12 MONTH)
    GROUP BY DATE_FORMAT(waktu, '%Y-%m')
    ORDER BY bulan ASC
");

$trendData = [];
while ($row = $monthlyTrend->fetch_assoc()) {
    $trendData[] = $row;
}

// Get cause statistics
$causeStats = $conn->query("
    SELECT 
        penyebab,
        COUNT(*) as total
    FROM kejadian_kebakaran
    GROUP BY penyebab
    ORDER BY total DESC
    LIMIT 10
");

$causeData = [];
while ($row = $causeStats->fetch_assoc()) {
    $causeData[] = $row;
}

// ============ AMBIL SETTINGAN KDE DARI DATABASE (SUPER ADMIN) ============
$heatmapSettings = $conn->query("SELECT * FROM heatmap_settings ORDER BY id DESC LIMIT 1");
$heatmap = $heatmapSettings->fetch_assoc();
if (!$heatmap) {
    $heatmap = ['radius' => 25, 'blur' => 15, 'intensity' => 70];
}

$conn->close();

$stats = getStatistics();

// Encode data untuk JavaScript
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
    <link rel="preconnect" href="https://fonts.googleapis.com">
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
        :root {
            --jet-black: #0D0D0D;
            --dark-grey: #2A2A2A;
            --gold: #F7B801;
            --gold-dark: #E0A600;
            --off-white: #F5F5F5;
            --off-white-dim: #E8E5DF;
        }

        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: 'Poppins', sans-serif;
            background: var(--off-white);
            color: var(--jet-black);
            overflow-x: hidden;
        }

        /* Navbar */
        .site-nav {
            position: fixed;
            top: 0;
            left: 0;
            right: 0;
            z-index: 1050;
            display: flex;
            align-items: center;
            justify-content: space-between;
            padding: 18px 40px;
            background: rgba(13, 13, 13, 0.96);
            backdrop-filter: blur(14px);
            border-bottom: 1px solid rgba(247, 184, 1, 0.25);
            transition: padding .3s;
        }

        .site-nav.compact {
            padding: 12px 40px;
        }

        .nav-logo {
            display: flex;
            align-items: center;
            gap: 12px;
            text-decoration: none;
        }

        .nav-logo-icon {
            width: 38px;
            height: 38px;
            border-radius: 10px;
            background: var(--jet-black);
            display: flex;
            align-items: center;
            justify-content: center;
            overflow: hidden;
        }

        .nav-logo-icon img {
            width: 100%;
            height: 100%;
            object-fit: cover;
        }

        .nav-logo-text {
            font-family: 'Poppins', sans-serif;
            font-weight: 700;
            font-size: 1.4rem;
            letter-spacing: 1px;
            color: #fff;
            line-height: 1;
        }

        .nav-logo-sub {
            font-size: .6rem;
            color: rgba(255, 255, 255, .45);
            letter-spacing: 3px;
            text-transform: uppercase;
            display: block;
            font-weight: 400;
        }

        .nav-links {
            display: flex;
            align-items: center;
            gap: 6px;
        }

        .nav-links a {
            font-size: .82rem;
            font-weight: 500;
            letter-spacing: .5px;
            color: rgba(255, 255, 255, .65);
            text-decoration: none;
            padding: 7px 14px;
            border-radius: 8px;
            transition: color .2s, background .2s;
        }

        .nav-links a:hover,
        .nav-links a.active {
            color: #fff;
            background: rgba(255, 255, 255, .07);
        }

        .nav-cta {
            background: linear-gradient(135deg, var(--gold), var(--gold-dark)) !important;
            color: var(--jet-black) !important;
            padding: 7px 18px !important;
            font-weight: 600 !important;
        }

        .nav-cta:hover {
            background: linear-gradient(135deg, var(--gold-dark), var(--gold)) !important;
        }

        .nav-toggle {
            display: none;
            background: none;
            border: none;
            cursor: pointer;
            padding: 4px;
        }

        .nav-toggle span {
            display: block;
            width: 22px;
            height: 2px;
            background: #fff;
            margin: 5px 0;
            transition: all .3s;
        }

        /* Page Hero */
        .page-hero {
            background: var(--jet-black);
            padding: 140px 0 60px;
            position: relative;
            overflow: hidden;
        }

        .page-hero::before {
            content: '';
            position: absolute;
            inset: 0;
            background-image: url("data:image/svg+xml,%3Csvg viewBox='0 0 256 256' xmlns='http://www.w3.org/2000/svg'%3E%3Cfilter id='n'%3E%3CfeTurbulence type='fractalNoise' baseFrequency='.9' numOctaves='4'/%3E%3C/filter%3E%3Crect width='100%25' height='100%25' filter='url(%23n)' opacity='.04'/%3E%3C/svg%3E");
            pointer-events: none;
        }

        .page-hero::after {
            content: '';
            position: absolute;
            top: -20%;
            right: -10%;
            width: 55%;
            height: 140%;
            background: linear-gradient(160deg, rgba(247, 184, 1, 0.08) 0%, rgba(247, 184, 1, 0.02) 60%, transparent 100%);
            transform: skewX(-8deg);
            z-index: 0;
        }

        .page-hero .container {
            position: relative;
            z-index: 1;
        }

        .hero-badge {
            display: inline-flex;
            align-items: center;
            gap: 10px;
            font-family: 'DM Mono', monospace;
            font-size: .7rem;
            letter-spacing: 3px;
            text-transform: uppercase;
            color: var(--gold);
            margin-bottom: 24px;
        }

        .hero-badge::before {
            content: '';
            display: block;
            width: 28px;
            height: 2px;
            background: var(--gold);
        }

        .page-hero h1 {
            font-family: 'Poppins', sans-serif;
            font-weight: 800;
            font-size: clamp(2.5rem, 5vw, 4rem);
            color: #fff;
            margin-bottom: 20px;
        }

        .page-hero .lead {
            color: rgba(255, 255, 255, .5);
            font-size: 1.1rem;
            max-width: 600px;
        }

        /* Map Styles */
        #map {
            height: 550px;
            width: 100%;
            border-radius: 20px;
            box-shadow: 0 8px 30px rgba(0, 0, 0, 0.2);
        }

        .map-container {
            position: relative;
        }

        /* Filter Section */
        .filter-section {
            background: var(--dark-grey);
            border-radius: 20px;
            padding: 24px;
            margin-bottom: 30px;
            border: 1px solid rgba(247, 184, 1, 0.12);
        }

        .filter-section label {
            color: var(--gold);
            font-size: .75rem;
            font-weight: 600;
            letter-spacing: 1px;
            margin-bottom: 8px;
            display: block;
        }

        .filter-section label .label-icon {
            margin-right: 6px;
        }

        .form-control-custom,
        .form-select-custom {
            background: rgba(255, 255, 255, 0.05);
            border: 1px solid rgba(247, 184, 1, 0.2);
            border-radius: 12px;
            padding: 12px 16px;
            color: #fff;
            font-family: 'Poppins', sans-serif;
            width: 100%;
        }

        .form-control-custom option,
        .form-select-custom option {
            background: var(--dark-grey);
            color: #fff;
        }

        .form-control-custom:focus,
        .form-select-custom:focus {
            background: rgba(255, 255, 255, 0.08);
            border-color: var(--gold);
            outline: none;
            color: #fff;
        }

        .form-control-custom::placeholder {
            color: rgba(255, 255, 255, 0.3);
        }

        .form-control-custom[type="date"] {
            color-scheme: dark;
        }

        .form-check-custom {
            display: flex;
            align-items: center;
            gap: 10px;
            color: rgba(255, 255, 255, 0.7);
            font-size: .85rem;
            cursor: pointer;
            padding: 10px 0;
        }

        .form-check-custom input[type="checkbox"] {
            width: 18px;
            height: 18px;
            accent-color: var(--gold);
            cursor: pointer;
            border-radius: 4px;
        }

        .btn-gold {
            background: linear-gradient(135deg, var(--gold), var(--gold-dark));
            color: var(--jet-black);
            font-weight: 600;
            padding: 12px 24px;
            border-radius: 12px;
            border: none;
            transition: all .3s;
        }

        .btn-gold:hover {
            transform: translateY(-2px);
            box-shadow: 0 8px 24px rgba(247, 184, 1, 0.35);
        }

        .btn-outline-gold {
            background: transparent;
            border: 2px solid var(--gold);
            color: var(--gold);
            padding: 10px 20px;
            border-radius: 12px;
            font-weight: 600;
            transition: all .3s;
        }

        .btn-outline-gold:hover {
            background: var(--gold);
            color: var(--jet-black);
        }

        /* Legend Card */
        .legend-card {
            background: rgba(13, 13, 13, 0.95);
            backdrop-filter: blur(10px);
            border-radius: 16px;
            padding: 16px 20px;
            border: 1px solid rgba(247, 184, 1, 0.2);
            margin-top: 16px;
        }

        .legend-card h6 {
            color: var(--gold);
            font-family: 'DM Mono', monospace;
            font-size: .7rem;
            letter-spacing: 2px;
            margin-bottom: 12px;
        }

        .legend-item {
            display: flex;
            align-items: center;
            gap: 12px;
            padding: 4px 0;
            font-size: .82rem;
            color: rgba(255, 255, 255, .8);
        }

        .legend-icon {
            width: 20px;
            height: 20px;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: .7rem;
            flex-shrink: 0;
        }

        .legend-heatmap {
            display: flex;
            align-items: center;
            gap: 6px;
            padding: 4px 0;
            font-size: .75rem;
            color: rgba(255, 255, 255, .6);
        }

        .legend-heatmap .gradient-bar {
            width: 80px;
            height: 8px;
            border-radius: 4px;
            background: linear-gradient(to right, #00cc44, #ffcc00, #ff3300);
        }

        /* Stats Cards */
        .stat-card-mini {
            background: var(--dark-grey);
            border-radius: 16px;
            padding: 20px;
            text-align: center;
            border: 1px solid rgba(247, 184, 1, 0.12);
            transition: all .3s;
        }

        .stat-card-mini:hover {
            transform: translateY(-4px);
            border-color: rgba(247, 184, 1, 0.3);
        }

        .stat-card-mini .number {
            font-family: 'Poppins', sans-serif;
            font-weight: 800;
            font-size: 2rem;
            color: var(--gold);
        }

        .stat-card-mini .label {
            font-size: .7rem;
            text-transform: uppercase;
            letter-spacing: 1px;
            color: rgba(255, 255, 255, .5);
        }

        .stat-card-mini .icon {
            font-size: 1.5rem;
            color: var(--gold);
            margin-bottom: 4px;
        }

        /* Chart Cards */
        .chart-card {
            background: var(--dark-grey);
            border-radius: 20px;
            padding: 24px;
            border: 1px solid rgba(247, 184, 1, 0.12);
            height: 100%;
        }

        .chart-card .card-header-custom {
            border-bottom: 1px solid rgba(247, 184, 1, 0.15);
            padding-bottom: 16px;
            margin-bottom: 20px;
        }

        .chart-card .card-header-custom h5 {
            font-family: 'Poppins', sans-serif;
            font-weight: 600;
            color: var(--gold);
            margin: 0;
        }

        .chart-card canvas {
            max-height: 280px;
        }

        /* Data Table */
        .data-table-card {
            background: var(--dark-grey);
            border-radius: 20px;
            padding: 24px;
            border: 1px solid rgba(247, 184, 1, 0.12);
            margin-top: 30px;
        }

        .data-table-card h5 {
            font-family: 'Poppins', sans-serif;
            font-weight: 600;
            color: var(--gold);
            margin-bottom: 20px;
        }

        .table-custom {
            color: rgba(255, 255, 255, .8);
        }

        .table-custom thead th {
            background: rgba(0, 0, 0, 0.3);
            border-bottom: 1px solid rgba(247, 184, 1, 0.2);
            color: var(--gold);
            font-weight: 600;
            font-size: .75rem;
            letter-spacing: 1px;
        }

        .table-custom tbody td {
            border-bottom: 1px solid rgba(247, 184, 1, 0.08);
            vertical-align: middle;
        }

        .table-custom tbody tr:hover {
            background: rgba(247, 184, 1, 0.05);
        }

        /* Footer */
        .site-footer {
            background: var(--jet-black);
            padding: 60px 0 32px;
            border-top: 1px solid rgba(247, 184, 1, 0.1);
            margin-top: 60px;
        }

        .footer-brand {
            display: flex;
            align-items: center;
            gap: 12px;
            margin-bottom: 20px;
        }

        .footer-brand-icon {
            width: 40px;
            height: 40px;
            border-radius: 12px;
            background: linear-gradient(135deg, var(--gold), var(--gold-dark));
            display: flex;
            align-items: center;
            justify-content: center;
        }

        .footer-brand-icon i {
            color: var(--jet-black);
            font-size: 1.1rem;
        }

        .footer-brand-name {
            font-family: 'Poppins', sans-serif;
            font-weight: 700;
            font-size: 1.5rem;
            letter-spacing: 1px;
            color: #fff;
            line-height: 1;
        }

        .footer-brand-tagline {
            font-size: .65rem;
            color: rgba(255, 255, 255, .35);
            letter-spacing: 2px;
            text-transform: uppercase;
        }

        .footer-desc {
            font-size: .87rem;
            line-height: 1.75;
            max-width: 300px;
            color: rgba(255, 255, 255, .3);
        }

        .footer-heading {
            font-family: 'DM Mono', monospace;
            font-size: .68rem;
            letter-spacing: 3px;
            text-transform: uppercase;
            color: var(--gold);
            margin-bottom: 20px;
        }

        .footer-links {
            list-style: none;
            padding: 0;
        }

        .footer-links li {
            margin-bottom: 10px;
        }

        .footer-links a {
            font-size: .88rem;
            color: rgba(255, 255, 255, .4);
            text-decoration: none;
            transition: color .2s;
        }

        .footer-links a:hover {
            color: var(--gold);
        }

        .footer-contact-item {
            display: flex;
            align-items: center;
            gap: 12px;
            font-size: .85rem;
            color: rgba(255, 255, 255, .4);
            margin-bottom: 12px;
        }

        .footer-contact-item i {
            color: var(--gold);
            width: 16px;
        }

        .emergency-box {
            background: rgba(247, 184, 1, 0.08);
            border: 1px solid rgba(247, 184, 1, 0.2);
            border-radius: 16px;
            padding: 24px;
            text-align: center;
        }

        .emergency-box .label {
            font-size: .65rem;
            letter-spacing: 3px;
            text-transform: uppercase;
            color: rgba(255, 255, 255, .4);
            font-family: 'DM Mono', monospace;
            margin-bottom: 8px;
        }

        .emergency-box .number {
            font-family: 'Poppins', sans-serif;
            font-weight: 700;
            font-size: 3rem;
            color: var(--gold);
            line-height: 1;
        }

        .footer-divider {
            border: none;
            border-top: 1px solid rgba(255, 255, 255, .07);
            margin: 40px 0 24px;
        }

        .footer-bottom {
            display: flex;
            align-items: center;
            justify-content: space-between;
            font-size: .78rem;
            color: rgba(255, 255, 255, .3);
        }

        .footer-socials {
            display: flex;
            gap: 12px;
        }

        .footer-socials a {
            width: 34px;
            height: 34px;
            border-radius: 10px;
            background: rgba(255, 255, 255, .05);
            border: 1px solid rgba(255, 255, 255, .08);
            display: flex;
            align-items: center;
            justify-content: center;
            color: rgba(255, 255, 255, .4);
            text-decoration: none;
            transition: all .2s;
        }

        .footer-socials a:hover {
            background: var(--gold);
            border-color: var(--gold);
            color: var(--jet-black);
        }

        .reveal {
            opacity: 0;
            transform: translateY(28px);
            transition: opacity .7s ease, transform .7s ease;
        }

        .reveal.visible {
            opacity: 1;
            transform: none;
        }

        .loading-overlay {
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            bottom: 0;
            background: rgba(13, 13, 13, 0.85);
            backdrop-filter: blur(8px);
            display: none;
            justify-content: center;
            align-items: center;
            z-index: 1000;
            border-radius: 20px;
        }

        .loading-overlay .spinner {
            width: 50px;
            height: 50px;
            border: 4px solid rgba(247, 184, 1, 0.2);
            border-top-color: var(--gold);
            border-radius: 50%;
            animation: spin 0.8s linear infinite;
        }

        @keyframes spin {
            to {
                transform: rotate(360deg);
            }
        }

        .kde-info {
            position: absolute;
            bottom: 15px;
            left: 15px;
            background: rgba(13, 13, 13, 0.9);
            backdrop-filter: blur(8px);
            color: #fff;
            padding: 6px 14px;
            border-radius: 10px;
            font-size: 10px;
            font-family: 'DM Mono', monospace;
            z-index: 1000;
            border: 1px solid rgba(247, 184, 1, 0.3);
        }

        @media (max-width: 992px) {
            .site-nav {
                padding: 16px 24px;
            }
            .nav-links {
                display: none;
            }
            .nav-toggle {
                display: block;
            }
            .nav-links.open {
                display: flex;
                flex-direction: column;
                gap: 4px;
                position: fixed;
                top: 72px;
                left: 0;
                right: 0;
                background: rgba(13, 13, 13, 0.98);
                padding: 20px 24px;
                border-bottom: 1px solid rgba(247, 184, 1, 0.15);
            }
            .footer-bottom {
                flex-direction: column;
                gap: 16px;
                text-align: center;
            }
            .filter-section .btn-gold {
                width: 100%;
                margin-top: 8px;
            }
        }

        @media (max-width: 576px) {
            #map {
                height: 350px;
            }
            .stat-card-mini .number {
                font-size: 1.5rem;
            }
            .chart-card canvas {
                max-height: 200px;
            }
        }
    </style>
</head>

<body>

    <!-- NAVBAR -->
    <nav class="site-nav" id="siteNav">
        <!-- ... kode HTML navbar, hero, map, filter, dan tabel sama persis seperti sebelumnya ... -->
        <a class="nav-logo" href="index.php">
            <div class="nav-logo-icon">
                <img src="../assets/barres2.png" alt="BARRES Logo">
            </div>
            <div>
                <span class="nav-logo-text">BARRES 698</span>
                <span class="nav-logo-sub">Banjarbaru Rescue</span>
            </div>
        </a>
        <div class="nav-links" id="navLinks">
            <a href="index.php">Beranda</a>
            <a href="profil.php">Profil</a>
            <a href="peta-statistik.php" class="active">Peta & Statistik</a>
            <a href="kontak.php">Kontak</a>
            <?php if (function_exists('isLoggedIn') && isLoggedIn()): ?>
                <?php if ($_SESSION['role'] == 'super_admin'): ?>
                    <a href="../superadmin/dashboard.php" class="nav-cta">Dashboard</a>
                <?php else: ?>
                    <a href="../adminbpk/dashboard.php" class="nav-cta">Dashboard</a>
                <?php endif; ?>
                <a href="../logout.php">Logout</a>
            <?php else: ?>
                <a href="login.php" class="nav-cta">Login Admin</a>
            <?php endif; ?>
        </div>
        <button class="nav-toggle" id="navToggle" aria-label="Toggle menu">
            <span></span><span></span><span></span>
        </button>
    </nav>

    <!-- PAGE HERO -->
    <section class="page-hero">
        <div class="container">
            <div class="hero-badge reveal">Visualisasi Data</div>
            <h1 class="reveal">Peta & <span style="color: var(--gold);">Statistik</span></h1>
            <p class="lead reveal">Visualisasi persebaran kejadian kebakaran, titik hydrant, dan unit BPK di Kota Banjarbaru</p>
        </div>
    </section>

    <div class="container py-4">
        <!-- Filter Section -->
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
                <div class="col-md-2 mb-3">
                    <label><i class="fas fa-calendar-alt label-icon"></i> DARI TANGGAL</label>
                    <input type="date" id="filterDateFrom" class="form-control-custom">
                </div>
                <div class="col-md-2 mb-3">
                    <label><i class="fas fa-calendar-alt label-icon"></i> SAMPAI TANGGAL</label>
                    <input type="date" id="filterDateTo" class="form-control-custom">
                </div>
                <div class="col-md-2 mb-3">
                    <label>&nbsp;</label>
                    <button class="btn-gold w-100" onclick="applyFilter()">
                        <i class="fas fa-search me-2"></i> Terapkan
                    </button>
                </div>
            </div>
            <div class="row mt-2">
                <div class="col-md-12">
                    <label class="form-check-custom">
                        <input type="checkbox" id="showHeatmap" checked>
                        <span><i class="fas fa-fire me-1" style="color: var(--gold);"></i> Tampilkan Heatmap KDE (Kernel Density Estimation)</span>
                        <span style="font-size: .65rem; color: rgba(255,255,255,.3); margin-left: 8px;">— menunjukkan zona kepadatan kebakaran</span>
                    </label>
                </div>
            </div>
        </div>

        <!-- Map & Legend -->
        <div class="row">
            <div class="col-lg-9 mb-3">
                <div class="map-container reveal">
                    <div id="map"></div>
                    <div class="loading-overlay" id="loadingOverlay">
                        <div class="text-center">
                            <div class="spinner"></div>
                            <p class="mt-3" style="color: var(--gold); font-size: .9rem;">Memproses data...</p>
                        </div>
                    </div>
                    <div class="kde-info" id="kdeInfo">
                        <i class="fas fa-cog"></i> Menunggu data KDE...
                    </div>
                </div>
            </div>
            <div class="col-lg-3 mb-3">
                <div class="legend-card reveal">
                    <h6><i class="fas fa-palette me-2"></i> LEGENDA</h6>
                    <div class="legend-item">
                        <div class="legend-icon" style="background: #ff4444; width: 14px; height: 14px; border-radius: 50%;"></div>
                        <span>Titik Kebakaran</span>
                    </div>
                    <div class="legend-item">
                        <div class="legend-icon" style="background: #00ccff; width: 14px; height: 14px; border-radius: 50%;"></div>
                        <span>Titik Hydrant</span>
                    </div>
                    <div class="legend-item">
                        <div class="legend-icon" style="background: #ffaa00; width: 14px; height: 14px; border-radius: 50%;"></div>
                        <span>Titik BPK</span>
                    </div>
                    <hr style="border-color: rgba(247, 184, 1, 0.2); margin: 12px 0;">
                    <div class="legend-heatmap">
                        <span>KDE Heatmap</span>
                        <div class="gradient-bar"></div>
                        <span style="font-size: .6rem;">Rendah → Tinggi</span>
                    </div>
                    <hr style="border-color: rgba(247, 184, 1, 0.2); margin: 12px 0;">
                    <div class="legend-item" style="font-size: .7rem; color: rgba(255,255,255,.4);">
                        <i class="fas fa-info-circle me-2"></i>
                        <span>Klik marker untuk detail</span>
                    </div>
                    <div class="legend-item" style="font-size: .7rem; color: rgba(255,255,255,.3);">
                        <i class="fas fa-layer-group me-2"></i>
                        <span>Marker Clustering aktif</span>
                    </div>
                </div>
                <div class="mt-3">
                    <button class="btn-outline-gold w-100" onclick="resetMap()">
                        <i class="fas fa-home me-2"></i> Reset Peta
                    </button>
                </div>
            </div>
        </div>

        <!-- Stats Cards -->
        <div class="row mb-4 mt-4">
            <div class="col-md-3 mb-3 reveal">
                <div class="stat-card-mini">
                    <div class="icon"><i class="fas fa-fire"></i></div>
                    <div class="number" id="statTotal"><?= number_format($stats['total_kejadian'] ?? 0, 0, ',', '.') ?></div>
                    <div class="label">Total Kejadian</div>
                </div>
            </div>
            <div class="col-md-3 mb-3 reveal">
                <div class="stat-card-mini">
                    <div class="icon"><i class="fas fa-user-injured"></i></div>
                    <div class="number" id="statLuka"><?= number_format($stats['total_luka'] ?? 0, 0, ',', '.') ?></div>
                    <div class="label">Korban Luka</div>
                </div>
            </div>
            <div class="col-md-3 mb-3 reveal">
                <div class="stat-card-mini">
                    <div class="icon"><i class="fas fa-skull"></i></div>
                    <div class="number" id="statJiwa"><?= number_format($stats['total_jiwa'] ?? 0, 0, ',', '.') ?></div>
                    <div class="label">Korban Jiwa</div>
                </div>
            </div>
            <div class="col-md-3 mb-3 reveal">
                <div class="stat-card-mini">
                    <div class="icon"><i class="fas fa-building"></i></div>
                    <div class="number" id="statBangunan"><?= number_format($stats['total_bangunan'] ?? 0, 0, ',', '.') ?></div>
                    <div class="label">Bangunan Terdampak</div>
                </div>
            </div>
        </div>

        <!-- Charts Section -->
        <div class="row mb-4">
            <div class="col-lg-4 mb-3 reveal">
                <div class="chart-card">
                    <div class="card-header-custom">
                        <h5><i class="fas fa-chart-bar me-2"></i> Per Kecamatan</h5>
                    </div>
                    <canvas id="kecamatanChart"></canvas>
                </div>
            </div>
            <div class="col-lg-4 mb-3 reveal">
                <div class="chart-card">
                    <div class="card-header-custom">
                        <h5><i class="fas fa-chart-line me-2"></i> Tren 12 Bulan</h5>
                    </div>
                    <canvas id="trendChart"></canvas>
                </div>
            </div>
            <div class="col-lg-4 mb-3 reveal">
                <div class="chart-card">
                    <div class="card-header-custom">
                        <h5><i class="fas fa-chart-pie me-2"></i> Penyebab</h5>
                    </div>
                    <canvas id="causeChart"></canvas>
                </div>
            </div>
        </div>

        <!-- Data Table -->
        <div class="data-table-card reveal">
            <h5><i class="fas fa-table me-2"></i> Data Kejadian Kebakaran</h5>
            <div class="table-responsive">
                <table class="table table-custom" id="incidentTable">
                    <thead>
                        <tr>
                            <th>No</th>
                            <th>Waktu</th>
                            <th>Lokasi</th>
                            <th>Kecamatan</th>
                            <th>Kelurahan</th>
                            <th>Luka</th>
                            <th>Jiwa</th>
                            <th>Bangunan</th>
                            <th>Penyebab</th>
                        </tr>
                    </thead>
                    <tbody id="tableBody">
                        <?php foreach ($incidents as $index => $incident): ?>
                            <tr>
                                <td><?= $index + 1 ?></td>
                                <td><?= date('d/m/Y H:i', strtotime($incident['waktu'])) ?></td>
                                <td><?= htmlspecialchars(substr($incident['alamat'], 0, 35)) ?>...</td>
                                <td><?= htmlspecialchars($incident['kecamatan']) ?></td>
                                <td><?= htmlspecialchars($incident['kelurahan'] ?? '-') ?></td>
                                <td><span class="badge" style="background: rgba(247, 184, 1, 0.15); color: var(--gold);"><?= $incident['korban_luka'] ?></span></td>
                                <td><span class="badge" style="background: rgba(220, 53, 69, 0.15); color: #ff6b6b;"><?= $incident['korban_jiwa'] ?></span></td>
                                <td><?= $incident['jumlah_bangunan'] ?></td>
                                <td><?= htmlspecialchars($incident['penyebab'] ?? '-') ?></td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <!-- FOOTER -->
    <footer class="site-footer">
        <!-- ... Footer sama persis seperti sebelumnya ... -->
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
                        Sistem Informasi Geografis pemetaan lokasi kebakaran berbasis web untuk Kota Banjarbaru.
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
                        <span>(0511) 123456</span>
                    </div>
                    <div class="footer-contact-item">
                        <i class="fas fa-envelope"></i>
                        <span>info@barres698.id</span>
                    </div>
                </div>
                <div class="col-lg-3">
                    <div class="footer-heading">Darurat</div>
                    <div class="emergency-box">
                        <div class="label">Pemadam Kebakaran</div>
                        <div class="number">113</div>
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
        // ============================================
        // DATA DARI PHP (diencode ke JSON)
        // ============================================
        const incidentsData = <?= $incidentsJson ?>;
        const hydrantsData = <?= $hydrantsJson ?>;
        const bpksData = <?= $bpksJson ?>;
        
        // ============ SETTINGAN KDE DARI SUPER ADMIN ============
        const kdeSettings = <?= $kdeSettingsJson ?>;

        // ============================================
        // NAVBAR & REVEAL ANIMATIONS
        // ============================================
        const nav = document.getElementById('siteNav');
        window.addEventListener('scroll', () => {
            nav.classList.toggle('compact', window.scrollY > 60);
        });

        const toggle = document.getElementById('navToggle');
        const links = document.getElementById('navLinks');
        if (toggle && links) {
            toggle.addEventListener('click', () => links.classList.toggle('open'));
        }

        const reveals = document.querySelectorAll('.reveal');
        const observer = new IntersectionObserver((entries) => {
            entries.forEach((e, i) => {
                if (e.isIntersecting) {
                    setTimeout(() => e.target.classList.add('visible'), i * 60);
                    observer.unobserve(e.target);
                }
            });
        }, { threshold: 0.12 });
        reveals.forEach(el => observer.observe(el));
        
        document.querySelectorAll('.page-hero .reveal').forEach((el, i) => {
            setTimeout(() => el.classList.add('visible'), 200 + i * 100);
        });

        // ============================================
        // MAP INITIALIZATION
        // ============================================
        const map = L.map('map').setView([-3.468, 114.832], 12);
        L.tileLayer('https://{s}.basemaps.cartocdn.com/light_all/{z}/{x}/{y}{r}.png', {
            attribution: '&copy; <a href="https://www.openstreetmap.org/copyright">OSM</a>',
            subdomains: 'abcd',
            maxZoom: 19
        }).addTo(map);

        let markerCluster = null;
        let heatmapLayer = null;
        let currentIncidents = incidentsData;

        // ============================================
        // ICON DEFINITIONS
        // ============================================
        const fireIcon = L.divIcon({
            html: '<i class="fas fa-fire" style="color:#ff4444;font-size:20px;text-shadow:0 0 10px rgba(255,68,68,0.5);"></i>',
            className: '',
            iconSize: [24, 24],
            iconAnchor: [12, 12]
        });

        const hydrantIcon = L.divIcon({
            html: '<i class="fas fa-tint" style="color:#00ccff;font-size:18px;text-shadow:0 0 10px rgba(0,204,255,0.5);"></i>',
            className: '',
            iconSize: [20, 20],
            iconAnchor: [10, 10]
        });

        const bpkIcon = L.divIcon({
            html: '<i class="fas fa-fire-extinguisher" style="color:#ffaa00;font-size:18px;text-shadow:0 0 10px rgba(255,170,0,0.5);"></i>',
            className: '',
            iconSize: [20, 20],
            iconAnchor: [10, 10]
        });

        // ============================================
        // KDE HEATMAP (Fetch from API Backend)
        // ============================================
        async function updateHeatmap(incidents) {
            if (heatmapLayer) {
                map.removeLayer(heatmapLayer);
                heatmapLayer = null;
            }

            const showHeatmap = document.getElementById('showHeatmap').checked;
            if (!showHeatmap || incidents.length < 2) {
                document.getElementById('kdeInfo').innerHTML = `<i class="fas fa-cog"></i> KDE: Tidak ada data / nonaktif`;
                return;
            }

            // Kumpulkan titik koordinat
            const lats = [];
            const lngs = [];
            incidents.forEach(inc => {
                if (inc.latitude && inc.longitude) {
                    lats.push(parseFloat(inc.latitude));
                    lngs.push(parseFloat(inc.longitude));
                }
            });

            if (lats.length < 2) return;

            // Tentukan Bounds
            const padding = 0.02;
            const bounds = {
                minLat: Math.min(...lats) - padding,
                maxLat: Math.max(...lats) + padding,
                minLng: Math.min(...lngs) - padding,
                maxLng: Math.max(...lngs) + padding
            };

            const params = new URLSearchParams({
                minLat: bounds.minLat,
                maxLat: bounds.maxLat,
                minLng: bounds.minLng,
                maxLng: bounds.maxLng,
                gridSize: 50
            });

            try {
                // Fetch data dari API Backend
                const response = await fetch(`../superadmin/heatmap/kde_heatmap.php?${params.toString()}`);
                const data = await response.json();

                if (data.status === 'success') {
                    const grid = data.grid_data;
                    const gridSize = data.grid_size;
                    const stepLat = (bounds.maxLat - bounds.minLat) / gridSize;
                    const stepLng = (bounds.maxLng - bounds.minLng) / gridSize;

                    // Terapkan pengaturan dari database
                    const radius = parseInt(kdeSettings.radius) || 25;
                    const blur = parseInt(kdeSettings.blur) || 15;
                    const intensity = parseInt(kdeSettings.intensity) || 70;
                    const intensityMultiplier = intensity / 100;

                    const heatmapData = [];
                    let currentLat = bounds.minLat;

                    for (let i = 0; i < gridSize; i++) {
                        let currentLng = bounds.minLng;
                        for (let j = 0; j < gridSize; j++) {
                            const value = grid[i][j];
                            if (value > 0.01) {
                                heatmapData.push([currentLat, currentLng, value * intensityMultiplier]);
                            }
                            currentLng += stepLng;
                        }
                        currentLat += stepLat;
                    }

                    heatmapLayer = L.heatLayer(heatmapData, {
                        radius: radius,
                        blur: blur,
                        maxZoom: 18,
                        max: 1.0,
                        gradient: {
                            0.0: '#00cc44', 0.2: '#88cc00', 0.4: '#ffcc00', 
                            0.6: '#ff8800', 0.8: '#ff4400', 1.0: '#ff0000'
                        }
                    }).addTo(map);

                    document.getElementById('kdeInfo').innerHTML = 
                        `<i class="fas fa-cog"></i> KDE: Radius ${radius}px | Blur ${blur} | ${data.total_points} titik | Bandwidth ${data.bandwidth.toFixed(5)}°`;
                } else {
                    console.error('API Error:', data.message);
                }
            } catch (error) {
                console.error('Gagal fetch KDE dari server:', error);
            }
        }

        // ============================================
        // MARKER CLUSTERING
        // ============================================
        function createMarkerCluster() {
            if (markerCluster) {
                map.removeLayer(markerCluster);
                markerCluster = null;
            }

            markerCluster = L.markerClusterGroup({
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

            return markerCluster;
        }

        // Tambahkan variabel baru untuk grup kebakaran (di bawah let markerCluster = null;)
        let fireMarkersGroup = null;

        function updateMarkers(incidents, hydrants, bpks) {
            // Hapus cluster lama jika ada
            if (markerCluster) {
                map.removeLayer(markerCluster);
                markerCluster = null;
            }
            // Hapus grup kebakaran lama jika ada
            if (fireMarkersGroup) {
                map.removeLayer(fireMarkersGroup);
                fireMarkersGroup = null;
            }

            markerCluster = createMarkerCluster();
            fireMarkersGroup = L.featureGroup(); // Grup baru KHUSUS kebakaran (tanpa cluster)

            const selectedTipe = document.getElementById('filterTipe').value;

            // 1. Add fire markers (LANGSUNG KE FIRE GROUP, BUKAN KE CLUSTER)
            if (selectedTipe === 'all' || selectedTipe === 'kebakaran') {
                incidents.forEach(incident => {
                    const popupContent = `
                        <strong>🔥 Kejadian Kebakaran</strong><br>
                        Waktu: ${new Date(incident.waktu).toLocaleString('id-ID')}<br>
                        Alamat: ${incident.alamat}<br>
                        Kecamatan: ${incident.kecamatan}<br>
                        Kelurahan: ${incident.kelurahan || '-'}<br>
                        Korban Luka: ${incident.korban_luka}<br>
                        Korban Jiwa: ${incident.korban_jiwa}<br>
                        Bangunan: ${incident.jumlah_bangunan}<br>
                        Penyebab: ${incident.penyebab || '-'}
                    `;
                    const marker = L.marker([parseFloat(incident.latitude), parseFloat(incident.longitude)], {
                        icon: fireIcon
                    }).bindPopup(popupContent);
                    
                    fireMarkersGroup.addLayer(marker); // Masukkan ke layer biasa
                });
            }

            // 2. Add hydrant markers (TETAP DI CLUSTER)
            if (selectedTipe === 'all' || selectedTipe === 'hydrant') {
                hydrants.forEach(hydrant => {
                    const statusColor = hydrant.status === 'berfungsi' ? '#00cc88' : '#ff4444';
                    const popupContent = `
                        <strong>🚒 Hydrant</strong><br>
                        Alamat: ${hydrant.alamat}<br>
                        Kecamatan: ${hydrant.kecamatan}<br>
                        Kelurahan: ${hydrant.kelurahan || '-'}<br>
                        Status: <span style="color:${statusColor};font-weight:bold;">${hydrant.status}</span><br>
                        Tahun Pemasangan: ${hydrant.tahun_pemasangan || '-'}
                    `;
                    const marker = L.marker([parseFloat(hydrant.latitude), parseFloat(hydrant.longitude)], {
                        icon: hydrantIcon
                    }).bindPopup(popupContent);
                    
                    markerCluster.addLayer(marker);
                });
            }

            // 3. Add BPK markers (TETAP DI CLUSTER)
            if (selectedTipe === 'all' || selectedTipe === 'bpk') {
                bpks.forEach(bpk => {
                    if (bpk.latitude && bpk.longitude) {
                        const popupContent = `
                            <strong>🏛️ BPK ${bpk.nama_bpk}</strong><br>
                            Alamat: ${bpk.alamat || '-'}<br>
                            Kecamatan: ${bpk.kecamatan || '-'}<br>
                            Kelurahan: ${bpk.kelurahan || '-'}<br>
                            Tahun Berdiri: ${bpk.tahun_berdiri || '-'}<br>
                            Anggota: ${bpk.jumlah_anggota || 0}
                        `;
                        const marker = L.marker([parseFloat(bpk.latitude), parseFloat(bpk.longitude)], {
                            icon: bpkIcon
                        }).bindPopup(popupContent);
                        
                        markerCluster.addLayer(marker);
                    }
                });
            }

            // Tampilkan ke peta
            map.addLayer(markerCluster);
            map.addLayer(fireMarkersGroup);

            // Sesuaikan tampilan (Fit Bounds) agar semua titik terlihat
            const allLayers = [
                ...markerCluster.getLayers(),
                ...fireMarkersGroup.getLayers()
            ];
            
            if (allLayers.length > 0) {
                const group = L.featureGroup(allLayers);
                map.fitBounds(group.getBounds().pad(0.15));
            }
        }

        // ============================================
        // INIT MAP
        // ============================================
        function initMap() {
            updateMarkers(incidentsData, hydrantsData, bpksData);
            updateHeatmap(incidentsData);
        }

        // ============================================
        // FILTER FUNCTIONS
        // ============================================
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

            const url = `../api/get-peta-data.php?${params.toString()}`;

            fetch(url)
                .then(response => {
                    if (!response.ok) throw new Error('Network response was not ok: ' + response.status);
                    return response.json();
                })
                .then(data => {
                    const kebakaranData = data.kebakaran || [];
                    const hydrantData = data.hydrant || [];
                    const bpkData = data.bpk || [];

                    currentIncidents = kebakaranData;

                    updateMarkers(kebakaranData, hydrantData, bpkData);
                    updateHeatmap(kebakaranData);
                    updateTable(kebakaranData);

                    if (data.stats) {
                        document.getElementById('statTotal').textContent = (data.stats.total_kejadian || 0).toLocaleString('id-ID');
                        document.getElementById('statLuka').textContent = (data.stats.total_luka || 0).toLocaleString('id-ID');
                        document.getElementById('statJiwa').textContent = (data.stats.total_jiwa || 0).toLocaleString('id-ID');
                        document.getElementById('statBangunan').textContent = (data.stats.total_bangunan || 0).toLocaleString('id-ID');
                    }

                    document.getElementById('loadingOverlay').style.display = 'none';
                })
                .catch(error => {
                    console.error('Error applying filter:', error);
                    document.getElementById('loadingOverlay').style.display = 'none';
                    alert('Gagal memuat data. Silakan coba lagi.');
                });
        }

        function updateTable(data) {
            const tbody = document.getElementById('tableBody');
            tbody.innerHTML = '';
            if (data.length === 0) {
                tbody.innerHTML = '<tr><td colspan="9" class="text-center text-muted py-3">Tidak ada data</td></tr>';
                return;
            }
            data.forEach((incident, index) => {
                const row = tbody.insertRow();
                row.innerHTML = `
                    <td>${index + 1}</td>
                    <td>${new Date(incident.waktu).toLocaleString('id-ID')}</td>
                    <td>${(incident.alamat || '').substring(0, 35)}...</td>
                    <td>${incident.kecamatan || '-'}</td>
                    <td>${incident.kelurahan || '-'}</td>
                    <td><span class="badge" style="background: rgba(247, 184, 1, 0.15); color: #F7B801;">${incident.korban_luka || 0}</span></td>
                    <td><span class="badge" style="background: rgba(220, 53, 69, 0.15); color: #ff6b6b;">${incident.korban_jiwa || 0}</span></td>
                    <td>${incident.jumlah_bangunan || 0}</td>
                    <td>${incident.penyebab || '-'}</td>
                `;
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
            updateHeatmap(incidentsData);
            updateTable(incidentsData);
        }

        // ============================================
        // CHARTS
        // ============================================
        function initCharts() {
            const kecamatanData = <?= json_encode($kecamatanData) ?>;
            const ctx1 = document.getElementById('kecamatanChart').getContext('2d');
            new Chart(ctx1, {
                type: 'bar',
                data: {
                    labels: kecamatanData.map(d => d.kecamatan),
                    datasets: [{
                        label: 'Kejadian',
                        data: kecamatanData.map(d => d.total),
                        backgroundColor: '#F7B801',
                        borderRadius: 6,
                        barPercentage: 0.7
                    }]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: true,
                    plugins: { legend: { display: false } },
                    scales: {
                        y: {
                            beginAtZero: true,
                            ticks: { stepSize: 1, color: '#fff' },
                            grid: { color: 'rgba(255,255,255,0.1)' }
                        },
                        x: {
                            ticks: { color: '#fff', font: { size: 10 } },
                            grid: { display: false }
                        }
                    }
                }
            });

            const trendData = <?= json_encode($trendData) ?>;
            const ctx2 = document.getElementById('trendChart').getContext('2d');
            new Chart(ctx2, {
                type: 'line',
                data: {
                    labels: trendData.map(d => d.bulan),
                    datasets: [{
                        label: 'Kejadian',
                        data: trendData.map(d => d.total),
                        borderColor: '#F7B801',
                        backgroundColor: 'rgba(247, 184, 1, 0.1)',
                        tension: 0.4,
                        fill: true,
                        pointBackgroundColor: '#F7B801',
                        pointBorderColor: '#0D0D0D',
                        pointRadius: 3
                    }]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: true,
                    plugins: { legend: { display: false } },
                    scales: {
                        y: {
                            beginAtZero: true,
                            ticks: { stepSize: 1, color: '#fff' },
                            grid: { color: 'rgba(255,255,255,0.1)' }
                        },
                        x: {
                            ticks: { color: '#fff', font: { size: 9 }, maxRotation: 45, minRotation: 30 },
                            grid: { display: false }
                        }
                    }
                }
            });

            const causeData = <?= json_encode($causeData) ?>;
            const colors = ['#F7B801', '#ff6b6b', '#ffaa00', '#00ccff', '#00cc88', '#ff8a80', '#b388ff', '#82b1ff', '#ffd54f', '#4dd0e1'];
            const ctx3 = document.getElementById('causeChart').getContext('2d');
            new Chart(ctx3, {
                type: 'doughnut',
                data: {
                    labels: causeData.map(d => d.penyebab || 'Tidak Diketahui'),
                    datasets: [{
                        data: causeData.map(d => d.total),
                        backgroundColor: colors.slice(0, causeData.length),
                        borderColor: '#0D0D0D',
                        borderWidth: 2
                    }]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: true,
                    plugins: {
                        legend: {
                            position: 'bottom',
                            labels: {
                                color: 'rgba(255,255,255,.8)',
                                font: { size: 10 },
                                padding: 8,
                                boxWidth: 12
                            }
                        }
                    },
                    cutout: '55%'
                }
            });
        }

        // ============================================
        // INIT ON DOM READY
        // ============================================
        document.addEventListener('DOMContentLoaded', function() {
            initMap();
            initCharts();

            // Auto apply filter on change
            document.querySelectorAll('#filterKecamatan, #filterTipe, #filterDateFrom, #filterDateTo').forEach(el => {
                el.addEventListener('change', applyFilter);
            });

            // Heatmap checkbox toggle
            document.getElementById('showHeatmap').addEventListener('change', function() {
                const incidents = currentIncidents.length > 0 ? currentIncidents : incidentsData;
                updateHeatmap(incidents);
            });
        });
    </script>
</body>
</html>