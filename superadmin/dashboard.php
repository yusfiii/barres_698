<?php
require_once __DIR__ . '/../includes/config.php';
require_once __DIR__ . '/../includes/session.php';
require_once __DIR__ . '/../includes/functions.php';

checkAuth();
checkRole(['super_admin']);

$user = getCurrentUser();
$stats = getStatistics();

// Get total BPK untuk sidebar
$conn = getConnection();
$total_bpk = $conn->query("SELECT COUNT(*) as total FROM bpk")->fetch_assoc()['total'];
$total_hydrant = $conn->query("SELECT COUNT(*) as total FROM hydrant")->fetch_assoc()['total'];

// Include sidebar
include __DIR__ . '/../includes/sidebar.php';

// Get recent incidents (LIMIT 5)
$recentIncidents = $conn->query("
    SELECT k.*, 
           DATE_FORMAT(waktu, '%d/%m/%Y %H:%i') as formatted_time
    FROM kejadian_kebakaran k 
    ORDER BY waktu DESC 
    LIMIT 5
");

// Get monthly data for chart
$monthlyData = $conn->query("
    SELECT DATE_FORMAT(waktu, '%M %Y') as bulan, 
           DATE_FORMAT(waktu, '%m') as bulan_index,
           COUNT(*) as total,
           SUM(korban_luka) as luka,
           SUM(korban_jiwa) as jiwa
    FROM kejadian_kebakaran 
    WHERE waktu >= DATE_SUB(NOW(), INTERVAL 12 MONTH)
    GROUP BY DATE_FORMAT(waktu, '%Y-%m')
    ORDER BY DATE_FORMAT(waktu, '%Y-%m')
");

// Get data untuk grafik penyebab
$penyebabData = $conn->query("
    SELECT 
        CASE 
            WHEN penyebab IS NULL OR penyebab = '' THEN 'Tidak diketahui'
            WHEN penyebab = 'Lainnya' THEN penyebab_lainnya
            ELSE penyebab 
        END as penyebab,
        COUNT(*) as total
    FROM kejadian_kebakaran 
    WHERE 1=1
    GROUP BY 
        CASE 
            WHEN penyebab IS NULL OR penyebab = '' THEN 'Tidak diketahui'
            WHEN penyebab = 'Lainnya' THEN penyebab_lainnya
            ELSE penyebab 
        END
    ORDER BY total DESC
    LIMIT 10
");

// Get kejadian per bulan (batang)
$bulanStats = $conn->query("
    SELECT 
        DATE_FORMAT(waktu, '%M %Y') as bulan,
        DATE_FORMAT(waktu, '%m') as bulan_index,
        COUNT(*) as total
    FROM kejadian_kebakaran 
    WHERE waktu >= DATE_SUB(NOW(), INTERVAL 12 MONTH)
    GROUP BY DATE_FORMAT(waktu, '%Y-%m')
    ORDER BY DATE_FORMAT(waktu, '%Y-%m')
");

// Kejadian bulan ini
$bulan_ini = date('Y-m');
$kejadian_bulan_ini = $conn->query("SELECT COUNT(*) as total FROM kejadian_kebakaran WHERE DATE_FORMAT(waktu, '%Y-%m') = '$bulan_ini'")->fetch_assoc()['total'];

$conn->close();
?>

<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard Super Admin - BARRES 698</title>

    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Material+Symbols+Outlined:opsz,wght,FILL,GRAD@20,400,0,0" rel="stylesheet" />
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>

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

        /* Stats Cards - 5 cards rata */
        .stats-row {
            display: grid;
            grid-template-columns: repeat(5, 1fr);
            gap: 16px;
            margin-bottom: 28px;
        }

        .stat-card {
            background: #FFFFFF;
            border: 1px solid rgba(0, 0, 0, 0.08);
            border-radius: 20px;
            padding: 20px 16px;
            transition: all 0.3s ease;
            text-align: center;
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: center;
            min-height: 130px;
        }

        .stat-card:hover {
            transform: translateY(-4px);
            border-color: #F7B801;
            box-shadow: 0 8px 20px rgba(0, 0, 0, 0.06);
        }

        .stat-icon {
            width: 50px;
            height: 50px;
            background: rgba(247, 184, 1, 0.1);
            border-radius: 14px;
            display: flex;
            align-items: center;
            justify-content: center;
            margin: 0 auto 8px;
        }

        .stat-icon i {
            font-size: 24px;
            color: #F7B801;
        }

        .stat-icon .material-symbols-outlined {
            font-size: 28px;
            color: #F7B801;
        }

        .stat-number {
            font-size: 28px;
            font-weight: 700;
            color: #1A1A1A;
            line-height: 1.2;
        }

        .stat-label {
            font-size: 12px;
            font-weight: 500;
            color: #666;
            margin-top: 2px;
        }

        /* Cards */
        .card-custom {
            background: #FFFFFF;
            border: 1px solid rgba(0, 0, 0, 0.08);
            border-radius: 20px;
            overflow: hidden;
            margin-bottom: 28px;
        }

        .card-header-custom {
            padding: 16px 24px;
            display: flex;
            justify-content: space-between;
            align-items: center;
            background: #FFFFFF;
            border-bottom: 1px solid rgba(0, 0, 0, 0.08);
        }

        .card-header-custom h3 {
            font-size: 15px;
            font-weight: 600;
            margin: 0;
            display: flex;
            align-items: center;
            gap: 10px;
            color: #F7B801;
        }

        .btn-view-all {
            background: transparent;
            border: 1px solid rgba(247, 184, 1, 0.4);
            padding: 6px 16px;
            border-radius: 10px;
            font-size: 12px;
            font-weight: 600;
            transition: all 0.2s;
            text-decoration: none;
            display: inline-flex;
            align-items: center;
            gap: 8px;
            color: #F7B801;
        }

        .btn-view-all:hover {
            background: rgba(247, 184, 1, 0.1);
            color: #F7B801;
            transform: translateY(-2px);
        }

        .card-body-custom {
            padding: 16px 20px;
        }

        .chart-container {
            position: relative;
            height: 260px;
        }

        .chart-container-sm {
            position: relative;
            height: 230px;
        }

        /* Grid charts */
        .chart-grid {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 24px;
        }

        /* Table */
        .table-custom {
            width: 100%;
            margin-bottom: 0;
            color: #1A1A1A;
            font-size: 13px;
        }

        .table-custom thead th {
            padding: 12px 16px;
            font-size: 12px;
            font-weight: 600;
            background: #F8F8F8;
            color: #1A1A1A;
            border-bottom: 1px solid #E0E0E0;
        }

        .table-custom tbody td {
            padding: 12px 16px;
            vertical-align: middle;
            border-bottom: 1px solid #E0E0E0;
        }

        .table-custom tbody tr:hover {
            background: rgba(247, 184, 1, 0.03);
        }

        .badge-luka {
            background: rgba(247, 184, 1, 0.15);
            color: #B8860B;
            padding: 3px 10px;
            border-radius: 20px;
            font-size: 11px;
            font-weight: 600;
            display: inline-block;
        }

        .badge-jiwa {
            background: rgba(220, 53, 69, 0.1);
            color: #DC3545;
            padding: 3px 10px;
            border-radius: 20px;
            font-size: 11px;
            font-weight: 600;
            display: inline-block;
        }

        .badge-penyebab {
            background: rgba(108, 117, 125, 0.1);
            color: #6c757d;
            padding: 3px 10px;
            border-radius: 20px;
            font-size: 11px;
            font-weight: 500;
            display: inline-block;
        }

        .table-footer {
            padding: 12px 24px;
            border-top: 1px solid rgba(0, 0, 0, 0.08);
            text-align: center;
            background: #FFFFFF;
        }

        /* Responsive */
        @media (max-width: 992px) {
            .stats-row {
                grid-template-columns: repeat(3, 1fr);
            }
        }

        @media (max-width: 768px) {
            .main-content {
                margin-left: 0;
                padding: 16px;
            }

            .stats-row {
                grid-template-columns: repeat(2, 1fr);
                gap: 12px;
            }

            .stat-card {
                min-height: 100px;
                padding: 16px 12px;
            }

            .stat-number {
                font-size: 22px;
            }

            .stat-icon {
                width: 40px;
                height: 40px;
            }

            .stat-icon i,
            .stat-icon .material-symbols-outlined {
                font-size: 20px;
            }

            .card-header-custom {
                flex-direction: column;
                gap: 10px;
                align-items: flex-start;
            }

            .chart-container,
            .chart-container-sm {
                height: 200px;
            }

            .chart-grid {
                grid-template-columns: 1fr;
                gap: 16px;
            }
        }

        @media (max-width: 480px) {
            .stats-row {
                grid-template-columns: 1fr 1fr;
                gap: 10px;
            }

            .stat-number {
                font-size: 20px;
            }

            .stat-label {
                font-size: 11px;
            }
        }
    </style>
</head>

<body>

    <!-- Sidebar sudah di-include -->
    <div class="main-content">
        <!-- Top Navbar -->
        <div class="top-navbar">
            <div class="page-title">
                <h2>Dashboard</h2>
                <p>Selamat datang kembali, <?= htmlspecialchars($user['username']) ?></p>
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
            <a href="../logout.php">
                <i class="fas fa-sign-out-alt"></i>
                <span>Logout</span>
            </a>
        </div>

        <!-- Stats Cards - 5 Cards Rata -->
        <div class="stats-row">
            <div class="stat-card">
                <div class="stat-icon"><i class="fas fa-fire"></i></div>
                <div class="stat-number"><?= number_format($stats['total_kejadian']) ?></div>
                <div class="stat-label">Total Kejadian</div>
            </div>
            <div class="stat-card">
                <div class="stat-icon"><i class="fas fa-calendar-alt"></i></div>
                <div class="stat-number"><?= number_format($kejadian_bulan_ini) ?></div>
                <div class="stat-label">Kejadian Bulan Ini</div>
            </div>
            <div class="stat-card">
                <div class="stat-icon"><i class="fas fa-building"></i></div>
                <div class="stat-number"><?= number_format($total_bpk) ?></div>
                <div class="stat-label">Total BPK</div>
            </div>
            <div class="stat-card">
                <div class="stat-icon">
                    <span class="material-symbols-outlined">fire_hydrant</span>
                </div>
                <div class="stat-number"><?= number_format($total_hydrant) ?></div>
                <div class="stat-label">Total Hydrant</div>
            </div>
            <div class="stat-card">
                <div class="stat-icon"><i class="fas fa-map-marker-alt"></i></div>
                <div class="stat-number"><?= count($stats['per_kecamatan']) ?></div>
                <div class="stat-label">Kecamatan Terdampak</div>
            </div>
        </div>

        <!-- Charts Grid -->
        <div class="chart-grid">
            <!-- Chart 1: Statistik Kejadian per Bulan -->
            <div class="card-custom">
                <div class="card-header-custom">
                    <h3><i class="fas fa-chart-line"></i> Statistik Kejadian per Bulan</h3>
                </div>
                <div class="card-body-custom">
                    <div class="chart-container">
                        <canvas id="monthlyChart"></canvas>
                    </div>
                </div>
            </div>

            <!-- Chart 2: Distribusi per Kecamatan -->
            <div class="card-custom">
                <div class="card-header-custom">
                    <h3><i class="fas fa-chart-pie"></i> Distribusi per Kecamatan</h3>
                </div>
                <div class="card-body-custom">
                    <div class="chart-container">
                        <canvas id="kecamatanPieChart"></canvas>
                    </div>
                </div>
            </div>

            <!-- Chart 3: Tren Waktu Kejadian -->
            <div class="card-custom">
                <div class="card-header-custom">
                    <h3><i class="fas fa-chart-bar"></i> Tren Waktu Kejadian</h3>
                </div>
                <div class="card-body-custom">
                    <div class="chart-container">
                        <canvas id="trendBarChart"></canvas>
                    </div>
                </div>
            </div>

            <!-- Chart 4: Penyebab Kebakaran -->
            <div class="card-custom">
                <div class="card-header-custom">
                    <h3><i class="fas fa-exclamation-triangle"></i> Penyebab Kebakaran</h3>
                </div>
                <div class="card-body-custom">
                    <div class="chart-container">
                        <canvas id="penyebabChart"></canvas>
                    </div>
                </div>
            </div>
        </div>

        <!-- Recent Incidents Table -->
        <div class="card-custom">
            <div class="card-header-custom">
                <h3><i class="fas fa-list"></i> Kejadian Terbaru</h3>
                <a href="kejadian/index.php" class="btn-view-all">
                    <i class="fas fa-eye"></i> Lihat Semua Kejadian
                </a>
            </div>
            <div class="card-body-custom p-0">
                <div class="table-responsive">
                    <table class="table-custom">
                        <thead>
                            <tr>
                                <th>No</th>
                                <th>Waktu</th>
                                <th>Alamat</th>
                                <th>Kecamatan</th>
                                <th>Penyebab</th>
                                <th>Korban Luka</th>
                                <th>Korban Jiwa</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php
                            $no = 1;
                            $incidentsData = [];
                            while ($row = $recentIncidents->fetch_assoc()):
                                $incidentsData[] = $row;
                            endwhile;

                            if (count($incidentsData) > 0):
                                foreach ($incidentsData as $row):
                                    $penyebab = $row['penyebab'] ?? '-';
                                    if (!empty($row['penyebab_lainnya'])) {
                                        $penyebab = $row['penyebab_lainnya'];
                                    }
                            ?>
                                    <tr>
                                        <td><?= $no++ ?></td>
                                        <td><?= $row['formatted_time'] ?></td>
                                        <td><?= htmlspecialchars(substr($row['alamat'], 0, 35)) ?><?= strlen($row['alamat']) > 35 ? '...' : '' ?></td>
                                        <td><?= htmlspecialchars($row['kecamatan'] ?? '-') ?></td>
                                        <td><span class="badge-penyebab"><?= htmlspecialchars($penyebab) ?></span></td>
                                        <td><span class="badge-luka"><?= $row['korban_luka'] ?></span></td>
                                        <td><span class="badge-jiwa"><?= $row['korban_jiwa'] ?></span></td>
                                    </tr>
                                <?php
                                endforeach;
                            else:
                                ?>
                                <tr>
                                    <td colspan="7" class="text-center py-4">Belum ada data kejadian</td>
                                </tr>
                            <?php endif; ?>
                        </tbody>
                    </table>
                </div>
                <?php if (count($incidentsData) > 0): ?>
                    <div class="table-footer">
                        <a href="kejadian/index.php" class="btn-view-all">
                            <i class="fas fa-arrow-right"></i> Kelola Semua Data Kejadian
                        </a>
                    </div>
                <?php endif; ?>
            </div>
        </div>
    </div>

    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>

    <script>
        let monthlyChart, kecamatanChart, trendBarChart, penyebabChart;

        // Dropdown
        document.getElementById('userAvatar').addEventListener('click', function(e) {
            e.stopPropagation();
            document.getElementById('dropdownMenu').classList.toggle('show');
        });

        document.addEventListener('click', function() {
            document.getElementById('dropdownMenu').classList.remove('show');
        });

        // Initialize Charts
        document.addEventListener('DOMContentLoaded', function() {
            // ============================================================
            // CHART 1: Statistik Kejadian per Bulan (Line)
            // ============================================================
            const monthlyData = <?php
                                $labels = [];
                                $totals = [];
                                $luka = [];
                                $jiwa = [];
                                if ($monthlyData) {
                                    $monthlyData->data_seek(0);
                                    while ($row = $monthlyData->fetch_assoc()) {
                                        $labels[] = $row['bulan'];
                                        $totals[] = (int)$row['total'];
                                        $luka[] = (int)$row['luka'];
                                        $jiwa[] = (int)$row['jiwa'];
                                    }
                                }
                                echo json_encode(['labels' => $labels, 'totals' => $totals, 'luka' => $luka, 'jiwa' => $jiwa]);
                                ?>;

            const ctx1 = document.getElementById('monthlyChart').getContext('2d');
            if (monthlyData.labels.length > 0) {
                monthlyChart = new Chart(ctx1, {
                    type: 'line',
                    data: {
                        labels: monthlyData.labels,
                        datasets: [{
                            label: 'Total Kejadian',
                            data: monthlyData.totals,
                            borderColor: '#F7B801',
                            backgroundColor: 'rgba(247, 184, 1, 0.1)',
                            borderWidth: 3,
                            pointBackgroundColor: '#F7B801',
                            pointBorderColor: '#FFFFFF',
                            pointRadius: 4,
                            pointHoverRadius: 6,
                            tension: 0.3,
                            fill: true
                        }, {
                            label: 'Korban Luka',
                            data: monthlyData.luka,
                            borderColor: '#ffc107',
                            backgroundColor: 'rgba(255, 193, 7, 0.05)',
                            borderWidth: 2,
                            pointBackgroundColor: '#ffc107',
                            pointBorderColor: '#FFFFFF',
                            pointRadius: 3,
                            pointHoverRadius: 5,
                            tension: 0.3,
                            fill: true,
                            borderDash: [5, 5]
                        }, {
                            label: 'Korban Jiwa',
                            data: monthlyData.jiwa,
                            borderColor: '#dc3545',
                            backgroundColor: 'rgba(220, 53, 69, 0.05)',
                            borderWidth: 2,
                            pointBackgroundColor: '#dc3545',
                            pointBorderColor: '#FFFFFF',
                            pointRadius: 3,
                            pointHoverRadius: 5,
                            tension: 0.3,
                            fill: true,
                            borderDash: [5, 5]
                        }]
                    },
                    options: {
                        responsive: true,
                        maintainAspectRatio: false,
                        plugins: {
                            legend: {
                                position: 'bottom',
                                labels: {
                                    color: '#666',
                                    usePointStyle: true,
                                    boxWidth: 10,
                                    padding: 12,
                                    font: { size: 10 }
                                }
                            }
                        },
                        scales: {
                            y: {
                                beginAtZero: true,
                                ticks: {
                                    stepSize: 1,
                                    color: '#666',
                                    font: { size: 10 }
                                },
                                grid: {
                                    color: 'rgba(0,0,0,0.05)'
                                }
                            },
                            x: {
                                ticks: {
                                    color: '#666',
                                    font: { size: 10 },
                                    maxRotation: 45,
                                    minRotation: 30
                                },
                                grid: {
                                    color: 'rgba(0,0,0,0.05)'
                                }
                            }
                        }
                    }
                });
            } else {
                ctx1.font = '14px Poppins';
                ctx1.fillStyle = '#999';
                ctx1.textAlign = 'center';
                ctx1.fillText('Belum ada data', ctx1.canvas.width / 2, ctx1.canvas.height / 2);
            }

            // ============================================================
            // CHART 2: Distribusi per Kecamatan (Pie)
            // ============================================================
            const kecamatanData = <?= json_encode($stats['per_kecamatan']) ?>;
            const ctx2 = document.getElementById('kecamatanPieChart').getContext('2d');
            const pieColors = ['#F7B801', '#E5A800', '#D49A00', '#C38B00', '#B27C00', '#A16D00', '#906000', '#7F5300'];

            if (kecamatanData.length > 0) {
                kecamatanChart = new Chart(ctx2, {
                    type: 'doughnut',
                    data: {
                        labels: kecamatanData.map(d => d.kecamatan),
                        datasets: [{
                            data: kecamatanData.map(d => d.total),
                            backgroundColor: pieColors.slice(0, kecamatanData.length),
                            borderWidth: 2,
                            borderColor: '#FFFFFF'
                        }]
                    },
                    options: {
                        responsive: true,
                        maintainAspectRatio: false,
                        plugins: {
                            legend: {
                                position: 'bottom',
                                labels: {
                                    color: '#666',
                                    usePointStyle: true,
                                    padding: 10,
                                    font: { size: 10 }
                                }
                            }
                        }
                    }
                });
            } else {
                ctx2.font = '14px Poppins';
                ctx2.fillStyle = '#999';
                ctx2.textAlign = 'center';
                ctx2.fillText('Belum ada data', ctx2.canvas.width / 2, ctx2.canvas.height / 2);
            }

            // ============================================================
            // CHART 3: Tren Waktu Kejadian (Bar)
            // ============================================================
            const bulanStats = <?php
                                $bulan_labels = [];
                                $bulan_totals = [];
                                if ($bulanStats) {
                                    $bulanStats->data_seek(0);
                                    while ($row = $bulanStats->fetch_assoc()) {
                                        $bulan_labels[] = $row['bulan'];
                                        $bulan_totals[] = (int)$row['total'];
                                    }
                                }
                                echo json_encode(['labels' => $bulan_labels, 'totals' => $bulan_totals]);
                                ?>;

            const ctx3 = document.getElementById('trendBarChart').getContext('2d');
            if (bulanStats.labels.length > 0) {
                trendBarChart = new Chart(ctx3, {
                    type: 'bar',
                    data: {
                        labels: bulanStats.labels,
                        datasets: [{
                            label: 'Jumlah Kejadian',
                            data: bulanStats.totals,
                            backgroundColor: 'rgba(247, 184, 1, 0.7)',
                            borderColor: '#F7B801',
                            borderWidth: 2,
                            borderRadius: 4,
                            barPercentage: 0.6
                        }]
                    },
                    options: {
                        responsive: true,
                        maintainAspectRatio: false,
                        plugins: {
                            legend: {
                                display: false
                            }
                        },
                        scales: {
                            y: {
                                beginAtZero: true,
                                ticks: {
                                    stepSize: 1,
                                    color: '#666',
                                    font: { size: 10 }
                                },
                                grid: {
                                    color: 'rgba(0,0,0,0.05)'
                                }
                            },
                            x: {
                                ticks: {
                                    color: '#666',
                                    font: { size: 10 },
                                    maxRotation: 45,
                                    minRotation: 30
                                },
                                grid: {
                                    color: 'rgba(0,0,0,0.05)'
                                }
                            }
                        }
                    }
                });
            } else {
                ctx3.font = '14px Poppins';
                ctx3.fillStyle = '#999';
                ctx3.textAlign = 'center';
                ctx3.fillText('Belum ada data', ctx3.canvas.width / 2, ctx3.canvas.height / 2);
            }

            // ============================================================
            // CHART 4: Penyebab Kebakaran (Pie)
            // ============================================================
            const penyebabData = <?php
                                $penyebab_labels = [];
                                $penyebab_totals = [];
                                if ($penyebabData) {
                                    $penyebabData->data_seek(0);
                                    while ($row = $penyebabData->fetch_assoc()) {
                                        $penyebab_labels[] = $row['penyebab'];
                                        $penyebab_totals[] = (int)$row['total'];
                                    }
                                }
                                echo json_encode(['labels' => $penyebab_labels, 'totals' => $penyebab_totals]);
                                ?>;

            const ctx4 = document.getElementById('penyebabChart').getContext('2d');
            const penyebabColors = ['#F7B801', '#E5A800', '#D49A00', '#C38B00', '#B27C00', '#A16D00', '#906000', '#7F5300', '#6E4600',
                '#5D3900'
            ];

            if (penyebabData.labels.length > 0) {
                penyebabChart = new Chart(ctx4, {
                    type: 'pie',
                    data: {
                        labels: penyebabData.labels,
                        datasets: [{
                            data: penyebabData.totals,
                            backgroundColor: penyebabColors.slice(0, penyebabData.labels.length),
                            borderWidth: 2,
                            borderColor: '#FFFFFF'
                        }]
                    },
                    options: {
                        responsive: true,
                        maintainAspectRatio: false,
                        plugins: {
                            legend: {
                                position: 'bottom',
                                labels: {
                                    color: '#666',
                                    usePointStyle: true,
                                    padding: 10,
                                    font: { size: 10 }
                                }
                            }
                        }
                    }
                });
            } else {
                ctx4.font = '14px Poppins';
                ctx4.fillStyle = '#999';
                ctx4.textAlign = 'center';
                ctx4.fillText('Belum ada data', ctx4.canvas.width / 2, ctx4.canvas.height / 2);
            }
        });
    </script>
</body>

</html>