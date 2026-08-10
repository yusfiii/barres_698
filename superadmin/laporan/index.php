<?php
require_once __DIR__ . '/../../includes/config.php';
require_once __DIR__ . '/../../includes/session.php';
require_once __DIR__ . '/../../includes/functions.php';

checkAuth();
checkRole(['super_admin']);

$user = getCurrentUser();

// Total BPK untuk sidebar
$conn = getConnection();
$total_bpk = $conn->query("SELECT COUNT(*) as total FROM bpk")->fetch_assoc()['total'];
$conn->close();

// Include sidebar dari folder includes
include __DIR__ . '/../../includes/sidebar.php';
?>

<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Laporan - BARRES 698</title>

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

        /* Report Cards Grid */
        .report-grid {
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(280px, 1fr));
            gap: 24px;
            margin-top: 20px;
        }

        .report-card {
            background: #FFFFFF;
            border: 1px solid rgba(0, 0, 0, 0.08);
            border-radius: 20px;
            padding: 28px 24px;
            text-align: center;
            transition: all 0.3s ease;
            text-decoration: none;
            color: #1A1A1A;
            display: block;
            position: relative;
            overflow: hidden;
        }

        .report-card:hover {
            transform: translateY(-6px);
            border-color: #F7B801;
            box-shadow: 0 12px 30px rgba(0, 0, 0, 0.08);
            color: #1A1A1A;
        }

        .report-card .icon-wrapper {
            width: 72px;
            height: 72px;
            background: rgba(247, 184, 1, 0.1);
            border-radius: 20px;
            display: flex;
            align-items: center;
            justify-content: center;
            margin: 0 auto 16px;
            transition: all 0.3s ease;
        }

        .report-card:hover .icon-wrapper {
            background: rgba(247, 184, 1, 0.2);
            transform: scale(1.05);
        }

        .report-card .icon-wrapper i {
            font-size: 32px;
            color: #F7B801;
        }

        .report-card h5 {
            font-size: 15px;
            font-weight: 600;
            margin-bottom: 8px;
            color: #1A1A1A;
        }

        .report-card p {
            font-size: 12.5px;
            color: #666;
            margin-bottom: 0;
            line-height: 1.5;
        }

        .report-card .badge-count {
            position: absolute;
            top: 16px;
            right: 16px;
            background: rgba(247, 184, 1, 0.15);
            color: #F7B801;
            font-size: 11px;
            font-weight: 600;
            padding: 4px 12px;
            border-radius: 20px;
        }

        .report-card .arrow-icon {
            margin-top: 16px;
            opacity: 0;
            transition: all 0.3s ease;
            color: #F7B801;
        }

        .report-card:hover .arrow-icon {
            opacity: 1;
            transform: translateX(4px);
        }

        /* Section Title */
        .section-title {
            display: flex;
            align-items: center;
            gap: 12px;
            margin-bottom: 8px;
        }

        .section-title h3 {
            font-size: 18px;
            font-weight: 600;
            color: #1A1A1A;
            margin: 0;
        }

        .section-title .line {
            flex: 1;
            height: 2px;
            background: linear-gradient(to right, rgba(247, 184, 1, 0.3), transparent);
        }

        .card-custom {
            background: #FFFFFF;
            border: 1px solid rgba(0, 0, 0, 0.08);
            border-radius: 20px;
            overflow: hidden;
        }

        /* Responsive */
        @media (max-width: 768px) {
            .main-content {
                margin-left: 0;
                padding: 16px;
            }

            .report-grid {
                grid-template-columns: 1fr 1fr;
                gap: 16px;
            }

            .report-card {
                padding: 20px 16px;
            }

            .report-card .icon-wrapper {
                width: 56px;
                height: 56px;
            }

            .report-card .icon-wrapper i {
                font-size: 24px;
            }

            .report-card h5 {
                font-size: 14px;
            }

            .report-card p {
                font-size: 12px;
            }
        }

        @media (max-width: 480px) {
            .report-grid {
                grid-template-columns: 1fr;
            }
        }

        @media print {
            .sidebar, .top-navbar, .dropdown-menu-custom, .user-avatar, .no-print {
                display: none !important;
            }
            .main-content {
                margin-left: 0 !important;
                padding: 20px !important;
            }
        }
    </style>
</head>

<body>

    <!-- Main Content -->
    <div class="main-content">
        <!-- Top Navbar -->
        <div class="top-navbar">
            <div class="page-title">
                <h2>Laporan</h2>
                <p>Pilih jenis laporan yang ingin Anda cetak atau ekspor</p>
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

        <!-- Report Cards Section -->
        <div class="section-title">
            <h3><i class="fas fa-file-alt" style="color: #F7B801;"></i> Daftar Laporan</h3>
            <span class="line"></span>
        </div>
        <p style="color: #666; font-size: 14px; margin-bottom: 24px;">
            Klik pada salah satu kartu di bawah untuk membuka preview dan mencetak dokumen laporan
        </p>

        <div class="report-grid">
            <!-- 1. Laporan Kejadian -->
            <a href="laporan-kejadian.php" class="report-card" target="_blank">
                <span class="badge-count"><i class="fas fa-file-pdf me-1"></i> PDF</span>
                <div class="icon-wrapper">
                    <i class="fas fa-fire"></i>
                </div>
                <h5>Laporan Kejadian</h5>
                <p>Detail Rekapitulasi Kejadian Kebakaran</p>
                <div class="arrow-icon"><i class="fas fa-arrow-right"></i></div>
            </a>

            <!-- 2. Laporan BPK -->
            <a href="laporan-bpk.php" class="report-card" target="_blank">
                <span class="badge-count"><i class="fas fa-file-pdf me-1"></i> PDF</span>
                <div class="icon-wrapper">
                    <i class="fas fa-building"></i>
                </div>
                <h5>Laporan BPK</h5>
                <p>Daftar BPK/PMK Terdaftar Sekota Banjarbaru</p>
                <div class="arrow-icon"><i class="fas fa-arrow-right"></i></div>
            </a>

            <!-- 3. Laporan Anggota -->
            <a href="laporan-anggota.php" class="report-card" target="_blank">
                <span class="badge-count"><i class="fas fa-file-pdf me-1"></i> PDF</span>
                <div class="icon-wrapper">
                    <i class="fas fa-users"></i>
                </div>
                <h5>Laporan Anggota</h5>
                <p>Daftar dan Jumlah Anggota BPK</p>
                <div class="arrow-icon"><i class="fas fa-arrow-right"></i></div>
            </a>

            <!-- 4. Laporan Sarpras BPK -->
            <a href="laporan-sarpras.php" class="report-card" target="_blank">
                <span class="badge-count"><i class="fas fa-file-pdf me-1"></i> PDF</span>
                <div class="icon-wrapper">
                    <i class="fas fa-truck-monster"></i>
                </div>
                <h5>Laporan Sarpras BPK</h5>
                <p>Inventaris Peralatan & Armada BPK</p>
                <div class="arrow-icon"><i class="fas fa-arrow-right"></i></div>
            </a>

            <!-- 5. Laporan Hydrant -->
            <a href="laporan-hydrant.php" class="report-card" target="_blank">
                <span class="badge-count"><i class="fas fa-file-pdf me-1"></i> PDF</span>
                <div class="icon-wrapper">
                    <i class="fas fa-faucet"></i>
                </div>
                <h5>Laporan Data Hydrant</h5>
                <p>Fasilitas & Lokasi Hydrant Umum</p>
                <div class="arrow-icon"><i class="fas fa-arrow-right"></i></div>
            </a>

            <!-- 6. Laporan Peta Hotspot -->
            <a href="laporan-hotspot.php" class="report-card" target="_blank">
                <span class="badge-count"><i class="fas fa-file-pdf me-1"></i> PDF</span>
                <div class="icon-wrapper">
                    <i class="fas fa-map-marked-alt"></i>
                </div>
                <h5>Laporan Peta Hotspot</h5>
                <p>Visualisasi Pemetaan Analisis KDE</p>
                <div class="arrow-icon"><i class="fas fa-arrow-right"></i></div>
            </a>

            <!-- 7. Laporan Bulanan (Yang diubah) -->
            <a href="laporan-tren.php" class="report-card" target="_blank">
                <span class="badge-count"><i class="fas fa-file-pdf me-1"></i> PDF</span>
                <div class="icon-wrapper">
                    <i class="fas fa-calendar-alt"></i>
                </div>
                <h5>Laporan Bulanan</h5>
                <p>Detail dan analisis kejadian kebakaran per bulan</p>
                <div class="arrow-icon"><i class="fas fa-arrow-right"></i></div>
            </a>

            <!-- 8. Laporan per Wilayah -->
            <a href="laporan-wilayah.php" class="report-card" target="_blank">
                <span class="badge-count"><i class="fas fa-file-pdf me-1"></i> PDF</span>
                <div class="icon-wrapper">
                    <i class="fas fa-map-marker-alt"></i>
                </div>
                <h5>Laporan Kejadian per Wilayah</h5>
                <p>Grafik Kepadatan Kecamatan & Kelurahan</p>
                <div class="arrow-icon"><i class="fas fa-arrow-right"></i></div>
            </a>

            <!-- 9. Laporan Korban & Dampak -->
            <a href="laporan-korban.php" class="report-card" target="_blank">
                <span class="badge-count"><i class="fas fa-file-pdf me-1"></i> PDF</span>
                <div class="icon-wrapper">
                    <i class="fas fa-user-injured"></i>
                </div>
                <h5>Laporan Korban & Dampak</h5>
                <p>Statistik Bangunan & Korban Terdampak</p>
                <div class="arrow-icon"><i class="fas fa-arrow-right"></i></div>
            </a>

            <!-- 10. Laporan Keaktifan Relawan -->
            <a href="laporan-keaktifan.php" class="report-card" target="_blank">
                <span class="badge-count"><i class="fas fa-file-pdf me-1"></i> PDF</span>
                <div class="icon-wrapper">
                    <i class="fas fa-user-check"></i>
                </div>
                <h5>Laporan Keaktifan Relawan</h5>
                <p>Rekapitulasi Status Keaktifan Anggota</p>
                <div class="arrow-icon"><i class="fas fa-arrow-right"></i></div>
            </a>
        </div>

        <!-- Informasi Tambahan -->
        <div class="card-custom mt-4" style="padding: 20px 24px;">
            <div class="row align-items-center">
                <div class="col-md-8">
                    <h6 style="font-weight: 600; color: #F7B801;"><i class="fas fa-info-circle me-2"></i>Informasi Standar Laporan</h6>
                    <p style="color: #666; font-size: 13px; margin: 0;">
                        Setiap laporan akan dicetak secara otomatis dengan format resmi yang mencakup Kop Surat resmi Banjarbaru Rescue 698, logo, nomor urut surat, dan kolom tanda tangan pengesahan.
                    </p>
                </div>
                <div class="col-md-4 text-end">
                    <span style="font-size: 12px; color: #999;">
                        <i class="fas fa-file-pdf text-danger me-1"></i> 10 Modul Laporan Ready
                    </span>
                </div>
            </div>
        </div>
    </div>

    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js"></script>

    <script>
        // Toggle dropdown navbar
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