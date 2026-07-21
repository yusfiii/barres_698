<?php
// superadmin/sarpras/index.php
require_once __DIR__ . '/../../includes/config.php';
require_once __DIR__ . '/../../includes/session.php';
require_once __DIR__ . '/../../includes/functions.php';

checkAuth();
checkRole(['super_admin']);

$user = getCurrentUser();
$conn = getConnection();

// Include sidebar dari folder includes
include __DIR__ . '/../../includes/sidebar.php';

// Ambil data BPK beserta data Sapras-nya
$query = "SELECT 
            b.id as bpk_id, 
            b.nomor_registrasi, 
            b.nama_bpk,
            COALESCE(s.mobil_tangki, 0) as mobil_tangki,
            COALESCE(s.mobil_portabel, 0) as mobil_portabel,
            COALESCE(s.mesin_pompa, 0) as mesin_pompa,
            COALESCE(s.selang_1_5_inc, 0) as selang_1_5_inc,
            COALESCE(s.selang_2_5_inc, 0) as selang_2_5_inc,
            COALESCE(s.selang_isap, 0) as selang_isap,
            COALESCE(s.nozle, 0) as nozle,
            COALESCE(s.helm_apd, 0) as helm_apd,
            COALESCE(s.baju_apd, 0) as baju_apd,
            COALESCE(s.celana_apd, 0) as celana_apd,
            COALESCE(s.sepatu_apd, 0) as sepatu_apd
          FROM bpk b 
          LEFT JOIN sapras_bpk s ON b.id = s.bpk_id 
          ORDER BY b.nama_bpk ASC";

$result = $conn->query($query);
$data_sapras = [];
if ($result) {
    while ($row = $result->fetch_assoc()) {
        $data_sapras[] = $row;
    }
}

$conn->close();

// Helper function dipindah ke luar looping agar tidak terjadi "Cannot redeclare"
if (!function_exists('formatValue')) {
    function formatValue($val) {
        return $val == 0 ? '<span class="badge-zero">-</span>' : '<span class="badge-value">'.$val.'</span>';
    }
}
?>

<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Data Sarpras BPK - BARRES 698</title>

    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
    
    <!-- DataTables CSS untuk fitur pencarian dan paginasi tabel -->
    <link rel="stylesheet" href="https://cdn.datatables.net/1.11.5/css/dataTables.bootstrap5.min.css">

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
            from { opacity: 0; transform: translateY(-10px); }
            to { opacity: 1; transform: translateY(0); }
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

        /* Card & Table */
        .card-custom {
            background: #FFFFFF;
            border: 1px solid rgba(0, 0, 0, 0.08);
            border-radius: 20px;
            overflow: hidden;
            margin-bottom: 28px;
            box-shadow: 0 4px 15px rgba(0,0,0,0.02);
        }

        .card-header-custom {
            padding: 18px 24px;
            display: flex;
            justify-content: space-between;
            align-items: center;
            background: #FFFFFF;
            border-bottom: 1px solid rgba(0, 0, 0, 0.08);
        }

        .card-header-custom h3 {
            font-size: 16px;
            font-weight: 600;
            margin: 0;
            display: flex;
            align-items: center;
            gap: 10px;
            color: #F7B801;
        }

        .table-custom {
            width: 100%;
            margin-bottom: 0;
            color: #1A1A1A;
            font-size: 13px;
        }

        .table-custom thead th {
            padding: 12px 10px;
            font-size: 12px;
            font-weight: 600;
            background: #F8F8F8;
            color: #1A1A1A;
            border-bottom: 1px solid #E0E0E0;
            border-right: 1px solid #E0E0E0;
        }

        .table-custom thead th:last-child {
            border-right: none;
        }

        /* Group Header Colors */
        .th-kendaraan { background-color: rgba(247, 184, 1, 0.1) !important; color: #B8860B !important; }
        .th-air { background-color: rgba(0, 123, 255, 0.08) !important; color: #0056b3 !important; }
        .th-apd { background-color: rgba(40, 167, 69, 0.08) !important; color: #1e7e34 !important; }

        .table-custom tbody td {
            padding: 10px;
            vertical-align: middle;
            border-bottom: 1px solid #E0E0E0;
            border-right: 1px solid #E0E0E0;
        }
        
        .table-custom tbody td:last-child {
            border-right: none;
        }

        .table-custom tbody tr:hover {
            background: rgba(247, 184, 1, 0.03);
        }

        .badge-zero {
            color: #adb5bd;
            font-weight: normal;
        }

        .badge-value {
            font-weight: 600;
            color: #1A1A1A;
        }

        /* DataTables Customization */
        .dataTables_wrapper .dataTables_length select {
            border-radius: 8px;
            border: 1px solid #E0E0E0;
            padding: 4px 24px 4px 8px;
        }
        .dataTables_wrapper .dataTables_filter input {
            border-radius: 8px;
            border: 1px solid #E0E0E0;
            padding: 6px 12px;
            margin-left: 8px;
        }
        .dataTables_wrapper .dataTables_filter input:focus {
            outline: none;
            border-color: #F7B801;
        }
        .page-item.active .page-link {
            background-color: #F7B801;
            border-color: #F7B801;
            color: #1A1A1A;
        }
        .page-link {
            color: #1A1A1A;
        }

        /* Responsive */
        @media (max-width: 768px) {
            .main-content {
                margin-left: 0;
                padding: 16px;
            }
        }
    </style>
</head>

<body>
    <!-- Main Content -->
    <div class="main-content">
        <div class="top-navbar">
            <div class="page-title">
                <h2>Data Sarana & Prasarana</h2>
                <p>Pantau ketersediaan Sarpras seluruh unit BPK</p>
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

        <!-- Tabel Data -->
        <div class="card-custom">
            <div class="card-header-custom">
                <h3><i class="fas fa-toolbox"></i> Rekapitulasi Sarpras BPK</h3>
            </div>
            <div class="card-body p-4">
                <div class="table-responsive">
                    <table class="table table-custom table-hover" id="sarprasTable">
                        <thead>
                            <tr>
                                <th rowspan="2" class="text-center align-middle" width="5%">No</th>
                                <th rowspan="2" class="align-middle" width="20%">Nama BPK</th>
                                <th colspan="2" class="text-center th-kendaraan"><i class="fas fa-truck-fire me-1"></i> Kendaraan</th>
                                <th colspan="5" class="text-center th-air"><i class="fas fa-water me-1"></i> Peralatan Air</th>
                                <th colspan="4" class="text-center th-apd"><i class="fas fa-user-shield me-1"></i> APD</th>
                            </tr>
                            <tr>
                                <!-- Kendaraan -->
                                <th class="text-center th-kendaraan">Tangki</th>
                                <th class="text-center th-kendaraan">Portabel</th>
                                <!-- Air -->
                                <th class="text-center th-air">Pompa</th>
                                <th class="text-center th-air">S. 1.5"</th>
                                <th class="text-center th-air">S. 2.5"</th>
                                <th class="text-center th-air">S. Isap</th>
                                <th class="text-center th-air">Nozle</th>
                                <!-- APD -->
                                <th class="text-center th-apd">Helm</th>
                                <th class="text-center th-apd">Baju</th>
                                <th class="text-center th-apd">Celana</th>
                                <th class="text-center th-apd">Sepatu</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php 
                            $no = 1;
                            foreach ($data_sapras as $row): 
                            ?>
                                <tr>
                                    <td class="text-center"><?= $no++ ?></td>
                                    <td>
                                        <strong><?= htmlspecialchars($row['nama_bpk']) ?></strong><br>
                                        <small class="text-muted">Reg: <?= htmlspecialchars($row['nomor_registrasi'] ?? '-') ?></small>
                                    </td>
                                    
                                    <!-- Kendaraan -->
                                    <td class="text-center" style="background: rgba(247, 184, 1, 0.02);"><?= formatValue($row['mobil_tangki']) ?></td>
                                    <td class="text-center" style="background: rgba(247, 184, 1, 0.02);"><?= formatValue($row['mobil_portabel']) ?></td>
                                    
                                    <!-- Air -->
                                    <td class="text-center" style="background: rgba(0, 123, 255, 0.02);"><?= formatValue($row['mesin_pompa']) ?></td>
                                    <td class="text-center" style="background: rgba(0, 123, 255, 0.02);"><?= formatValue($row['selang_1_5_inc']) ?></td>
                                    <td class="text-center" style="background: rgba(0, 123, 255, 0.02);"><?= formatValue($row['selang_2_5_inc']) ?></td>
                                    <td class="text-center" style="background: rgba(0, 123, 255, 0.02);"><?= formatValue($row['selang_isap']) ?></td>
                                    <td class="text-center" style="background: rgba(0, 123, 255, 0.02);"><?= formatValue($row['nozle']) ?></td>
                                    
                                    <!-- APD -->
                                    <td class="text-center" style="background: rgba(40, 167, 69, 0.02);"><?= formatValue($row['helm_apd']) ?></td>
                                    <td class="text-center" style="background: rgba(40, 167, 69, 0.02);"><?= formatValue($row['baju_apd']) ?></td>
                                    <td class="text-center" style="background: rgba(40, 167, 69, 0.02);"><?= formatValue($row['celana_apd']) ?></td>
                                    <td class="text-center" style="background: rgba(40, 167, 69, 0.02);"><?= formatValue($row['sepatu_apd']) ?></td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>

    <!-- Scripts -->
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://cdn.datatables.net/1.11.5/js/jquery.dataTables.min.js"></script>
    <script src="https://cdn.datatables.net/1.11.5/js/dataTables.bootstrap5.min.js"></script>

    <script>
        $(document).ready(function() {
            // Inisialisasi DataTables untuk fitur Search & Pagination
            $('#sarprasTable').DataTable({
                language: {
                    url: '//cdn.datatables.net/plug-ins/1.11.5/i18n/id.json'
                },
                pageLength: 25,
                ordering: false // Dinonaktifkan agar urutan default (Nama BPK) tetap rapi
            });

            // Dropdown Toggle
            $('#userAvatar').on('click', function(e) {
                e.stopPropagation();
                $('#dropdownMenu').toggleClass('show');
            });
            
            $(document).on('click', function() {
                $('#dropdownMenu').removeClass('show');
            });
        });
    </script>
</body>

</html>