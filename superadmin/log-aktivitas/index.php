<?php
require_once __DIR__ . '/../../includes/config.php';
require_once __DIR__ . '/../../includes/session.php';
require_once __DIR__ . '/../../includes/functions.php';
require_once __DIR__ . '/../../includes/log_helper.php';

checkAuth();
checkRole(['super_admin']);

$user = getCurrentUser();
$message = '';
$messageType = '';

// Filter
$filter_role = isset($_GET['role']) ? $_GET['role'] : '';
$filter_user = isset($_GET['user_id']) ? $_GET['user_id'] : '';
$filter_start = isset($_GET['start_date']) ? $_GET['start_date'] : '';
$filter_end = isset($_GET['end_date']) ? $_GET['end_date'] : '';
$limit = isset($_GET['limit']) ? (int)$_GET['limit'] : 100;
$offset = isset($_GET['offset']) ? (int)$_GET['offset'] : 0;
$page = isset($_GET['page']) ? (int)$_GET['page'] : 1;
$offset = ($page - 1) * $limit;

// Get total BPK untuk sidebar
$conn = getConnection();
$total_bpk = $conn->query("SELECT COUNT(*) as total FROM bpk")->fetch_assoc()['total'];
$conn->close();

// Include sidebar
include __DIR__ . '/../../includes/sidebar.php';

// Ambil data log
$logs = getLogAktivitas($limit, $offset, $filter_role, $filter_user, $filter_start, $filter_end);
$total_logs = countLogAktivitas($filter_role, $filter_user, $filter_start, $filter_end);
$total_pages = ceil($total_logs / $limit);

// Ambil daftar user untuk filter
$conn = getConnection();
$users_list = $conn->query("SELECT id, username, nama, role FROM users ORDER BY username");
$conn->close();

// Statistik log
$conn = getConnection();

// Total log hari ini
$today = date('Y-m-d');
$today_logs = $conn->query("SELECT COUNT(*) as total FROM log_aktivitas WHERE DATE(created_at) = '$today'")->fetch_assoc()['total'];

// Total log bulan ini
$this_month = date('Y-m');
$month_logs = $conn->query("SELECT COUNT(*) as total FROM log_aktivitas WHERE DATE_FORMAT(created_at, '%Y-%m') = '$this_month'")->fetch_assoc()['total'];

// Aktivitas terakhir per role
$last_super = $conn->query("SELECT username, created_at FROM log_aktivitas WHERE role = 'super_admin' ORDER BY created_at DESC LIMIT 1")->fetch_assoc();
$last_bpk = $conn->query("SELECT username, created_at FROM log_aktivitas WHERE role = 'admin_bpk' ORDER BY created_at DESC LIMIT 1")->fetch_assoc();

$conn->close();
?>

<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Log Aktivitas - BARRES 698</title>

    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <link rel="stylesheet" href="https://cdn.datatables.net/1.13.4/css/dataTables.bootstrap5.min.css">
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
        
        .top-navbar {
            background: #FFFFFF;
            border: 1px solid rgba(0,0,0,0.08);
            border-radius: 20px;
            padding: 12px 24px;
            margin-bottom: 28px;
            display: flex;
            justify-content: space-between;
            align-items: center;
        }
        .page-title h2 { font-size: 20px; font-weight: 600; margin: 0; color: #1A1A1A; }
        .page-title p { font-size: 13px; margin: 4px 0 0 0; color: #666; }
        .user-info { text-align: right; }
        .user-info .username { font-size: 14px; font-weight: 600; color: #1A1A1A; }
        .user-info .role { font-size: 11px; color: #F7B801; }
        .user-avatar {
            width: 44px; height: 44px;
            background: linear-gradient(135deg, #F7B801, #E5A800);
            border-radius: 14px;
            display: flex; align-items: center; justify-content: center;
            cursor: pointer; transition: transform 0.2s;
        }
        .user-avatar:hover { transform: scale(1.05); }
        .user-avatar i { font-size: 22px; color: #1A1A1A; }
        
        .dropdown-menu-custom {
            position: absolute; top: 80px; right: 32px;
            background: #FFFFFF; border: 1px solid rgba(0,0,0,0.1);
            box-shadow: 0 4px 12px rgba(0,0,0,0.08);
            border-radius: 16px; padding: 12px 0; min-width: 180px;
            display: none; z-index: 1000;
        }
        .dropdown-menu-custom.show { display: block; animation: fadeIn 0.2s ease; }
        @keyframes fadeIn { from { opacity: 0; transform: translateY(-10px); } to { opacity: 1; transform: translateY(0); } }
        .dropdown-menu-custom a {
            display: flex; align-items: center; gap: 12px;
            padding: 12px 20px; text-decoration: none;
            transition: all 0.2s; font-size: 13px; color: #333;
        }
        .dropdown-menu-custom a:hover { background: rgba(247,184,1,0.1); color: #F7B801; }
        .dropdown-divider { margin: 8px 0; border-color: #E0E0E0; }

        /* Stats Cards */
        .stat-card {
            background: #FFFFFF;
            border: 1px solid rgba(0,0,0,0.08);
            border-radius: 20px;
            padding: 20px;
            transition: all 0.3s ease;
            text-align: center;
        }
        .stat-card:hover { transform: translateY(-4px); border-color: #F7B801; box-shadow: 0 8px 20px rgba(0,0,0,0.06); }
        .stat-icon {
            width: 55px; height: 55px;
            background: rgba(247,184,1,0.1);
            border-radius: 16px;
            display: flex; align-items: center; justify-content: center;
            margin: 0 auto 12px;
        }
        .stat-icon i { font-size: 28px; color: #F7B801; }
        .stat-number { font-size: 32px; font-weight: 700; margin-bottom: 5px; color: #1A1A1A; }
        .stat-label { font-size: 13px; font-weight: 500; color: #666; }

        .card-custom {
            background: #FFFFFF; border: 1px solid rgba(0,0,0,0.08);
            border-radius: 20px; overflow: hidden; margin-bottom: 28px;
        }
        .card-header-custom {
            padding: 18px 24px; display: flex; justify-content: space-between;
            align-items: center; background: #FFFFFF;
            border-bottom: 1px solid rgba(0,0,0,0.08);
        }
        .card-header-custom h3 {
            font-size: 16px; font-weight: 600; margin: 0;
            display: flex; align-items: center; gap: 10px; color: #F7B801;
        }

        /* Filter Section */
        .filter-section {
            background: #FFFFFF;
            border: 1px solid rgba(0,0,0,0.08);
            border-radius: 20px;
            padding: 20px 24px;
            margin-bottom: 28px;
        }
        .form-label {
            font-size: 13px; font-weight: 600; margin-bottom: 8px; color: #1A1A1A;
        }
        .form-control, .form-select {
            background: #F8F8F8; border: 1px solid #E0E0E0; color: #1A1A1A;
            border-radius: 12px; padding: 10px 14px; font-size: 13px; font-family: 'Poppins', sans-serif;
        }
        .form-control:focus, .form-select:focus {
            border-color: #F7B801; box-shadow: 0 0 0 3px rgba(247,184,1,0.1); outline: none;
        }
        .btn-gold {
            background: linear-gradient(135deg, #F7B801, #E5A800);
            border: none; padding: 10px 20px; border-radius: 12px;
            font-weight: 600; font-size: 13px; color: #1A1A1A;
            transition: all 0.3s ease;
        }
        .btn-gold:hover { transform: translateY(-2px); box-shadow: 0 5px 15px rgba(247,184,1,0.3); color: #1A1A1A; }
        .btn-outline-gold {
            background: transparent; border: 1px solid rgba(247,184,1,0.4);
            padding: 10px 20px; border-radius: 12px;
            font-weight: 600; font-size: 13px; color: #F7B801;
            transition: all 0.2s;
        }
        .btn-outline-gold:hover { background: rgba(247,184,1,0.1); color: #F7B801; }

        /* Tabel */
        .table-custom { width: 100%; margin-bottom: 0; color: #1A1A1A; }
        .table-custom thead th {
            padding: 14px 16px; font-size: 13px; font-weight: 600;
            background: #F8F8F8; color: #1A1A1A; border-bottom: 1px solid #E0E0E0;
        }
        .table-custom tbody td {
            padding: 12px 16px; font-size: 13px; vertical-align: middle;
            border-bottom: 1px solid #E0E0E0;
        }
        .table-custom tbody tr:hover { background: rgba(247,184,1,0.03); }

        .badge-role-super { background: rgba(247,184,1,0.15); color: #B8860B; padding: 4px 12px; border-radius: 20px; font-size: 11px; display: inline-block; }
        .badge-role-bpk { background: rgba(23,162,184,0.1); color: #17a2b8; padding: 4px 12px; border-radius: 20px; font-size: 11px; display: inline-block; }

        .badge-login { background: rgba(40,167,69,0.1); color: #28a745; padding: 4px 10px; border-radius: 20px; font-size: 11px; display: inline-block; }
        .badge-create { background: rgba(23,162,184,0.1); color: #17a2b8; padding: 4px 10px; border-radius: 20px; font-size: 11px; display: inline-block; }
        .badge-update { background: rgba(255,193,7,0.1); color: #e6a000; padding: 4px 10px; border-radius: 20px; font-size: 11px; display: inline-block; }
        .badge-delete { background: rgba(220,53,69,0.1); color: #dc3545; padding: 4px 10px; border-radius: 20px; font-size: 11px; display: inline-block; }
        .badge-other { background: rgba(108,117,125,0.1); color: #6c757d; padding: 4px 10px; border-radius: 20px; font-size: 11px; display: inline-block; }

        .dataTables_wrapper .dataTables_length select, .dataTables_wrapper .dataTables_filter input {
            background: #F8F8F8; border: 1px solid #E0E0E0; color: #1A1A1A; border-radius: 10px; padding: 6px 12px;
        }
        .dataTables_wrapper .dataTables_info, .dataTables_wrapper .dataTables_paginate { color: #666; }
        .dataTables_wrapper .dataTables_paginate .paginate_button {
            background: #F8F8F8 !important; border-color: #E0E0E0 !important; color: #1A1A1A !important;
        }
        .dataTables_wrapper .dataTables_paginate .paginate_button.current {
            background: #F7B801 !important; color: #1A1A1A !important;
        }

        @media (max-width: 768px) {
            .main-content { margin-left: 0; padding: 16px; }
            .card-header-custom { flex-direction: column; gap: 12px; align-items: flex-start; }
            .filter-section .row { flex-direction: column; gap: 12px; }
        }
    </style>
</head>

<body>

    <!-- Sidebar sudah di-include -->
    <div class="main-content">
        <div class="top-navbar">
            <div class="page-title">
                <h2>Log Aktivitas</h2>
                <p>Riwayat aktivitas semua pengguna sistem</p>
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

        <!-- Statistik -->
        <div class="row g-4 mb-4">
            <div class="col-md-3 col-6">
                <div class="stat-card">
                    <div class="stat-icon"><i class="fas fa-clock"></i></div>
                    <div class="stat-number"><?= number_format($total_logs) ?></div>
                    <div class="stat-label">Total Aktivitas</div>
                </div>
            </div>
            <div class="col-md-3 col-6">
                <div class="stat-card">
                    <div class="stat-icon"><i class="fas fa-calendar-day"></i></div>
                    <div class="stat-number"><?= number_format($today_logs) ?></div>
                    <div class="stat-label">Hari Ini</div>
                </div>
            </div>
            <div class="col-md-3 col-6">
                <div class="stat-card">
                    <div class="stat-icon"><i class="fas fa-calendar-alt"></i></div>
                    <div class="stat-number"><?= number_format($month_logs) ?></div>
                    <div class="stat-label">Bulan Ini</div>
                </div>
            </div>
            <div class="col-md-3 col-6">
                <div class="stat-card">
                    <div class="stat-icon"><i class="fas fa-user-clock"></i></div>
                    <div class="stat-number">
                        <?php 
                        $last_activity = !empty($logs) ? date('H:i', strtotime($logs[0]['created_at'])) : '-';
                        echo $last_activity;
                        ?>
                    </div>
                    <div class="stat-label">Aktivitas Terakhir</div>
                </div>
            </div>
        </div>

        <!-- Informasi Aktivitas Terakhir per Role -->
        <div class="row g-4 mb-4">
            <div class="col-md-6">
                <div class="card-custom">
                    <div class="card-body" style="padding: 15px 20px;">
                        <div class="d-flex align-items-center">
                            <div class="stat-icon" style="width:40px; height:40px; margin:0 15px 0 0; background:rgba(247,184,1,0.15);">
                                <i class="fas fa-crown" style="color:#F7B801; font-size:18px;"></i>
                            </div>
                            <div>
                                <div style="font-size:13px; color:#666;">Super Admin terakhir</div>
                                <div style="font-weight:600;">
                                    <?php if ($last_super): ?>
                                        <?= htmlspecialchars($last_super['username']) ?> - <?= date('d/m/Y H:i', strtotime($last_super['created_at'])) ?>
                                    <?php else: ?>
                                        <span class="text-muted">Belum ada aktivitas</span>
                                    <?php endif; ?>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-md-6">
                <div class="card-custom">
                    <div class="card-body" style="padding: 15px 20px;">
                        <div class="d-flex align-items-center">
                            <div class="stat-icon" style="width:40px; height:40px; margin:0 15px 0 0; background:rgba(23,162,184,0.15);">
                                <i class="fas fa-user-tie" style="color:#17a2b8; font-size:18px;"></i>
                            </div>
                            <div>
                                <div style="font-size:13px; color:#666;">Admin BPK terakhir</div>
                                <div style="font-weight:600;">
                                    <?php if ($last_bpk): ?>
                                        <?= htmlspecialchars($last_bpk['username']) ?> - <?= date('d/m/Y H:i', strtotime($last_bpk['created_at'])) ?>
                                    <?php else: ?>
                                        <span class="text-muted">Belum ada aktivitas</span>
                                    <?php endif; ?>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Filter Section -->
        <div class="filter-section">
            <form method="GET" action="" class="row g-3 align-items-end">
                <div class="col-md-3">
                    <label class="form-label"><i class="fas fa-user me-1"></i> Pengguna</label>
                    <select name="user_id" class="form-select">
                        <option value="">Semua Pengguna</option>
                        <?php if ($users_list && $users_list->num_rows > 0):
                            while ($u = $users_list->fetch_assoc()): ?>
                                <option value="<?= $u['id'] ?>" <?= $filter_user == $u['id'] ? 'selected' : '' ?>>
                                    <?= htmlspecialchars($u['username']) ?> <?= $u['nama'] ? '('.htmlspecialchars($u['nama']).')' : '' ?>
                                    (<?= $u['role'] == 'super_admin' ? 'Super Admin' : 'Admin BPK' ?>)
                                </option>
                        <?php endwhile; endif; ?>
                    </select>
                </div>
                <div class="col-md-2">
                    <label class="form-label"><i class="fas fa-tag me-1"></i> Role</label>
                    <select name="role" class="form-select">
                        <option value="">Semua Role</option>
                        <option value="super_admin" <?= $filter_role == 'super_admin' ? 'selected' : '' ?>>Super Admin</option>
                        <option value="admin_bpk" <?= $filter_role == 'admin_bpk' ? 'selected' : '' ?>>Admin BPK</option>
                    </select>
                </div>
                <div class="col-md-2">
                    <label class="form-label"><i class="fas fa-calendar-start me-1"></i> Mulai</label>
                    <input type="date" name="start_date" class="form-control" value="<?= htmlspecialchars($filter_start) ?>">
                </div>
                <div class="col-md-2">
                    <label class="form-label"><i class="fas fa-calendar-end me-1"></i> Sampai</label>
                    <input type="date" name="end_date" class="form-control" value="<?= htmlspecialchars($filter_end) ?>">
                </div>
                <div class="col-md-3">
                    <div class="d-flex gap-2">
                        <button type="submit" class="btn-gold">
                            <i class="fas fa-filter"></i> Filter
                        </button>
                        <a href="index.php" class="btn-outline-gold">
                            <i class="fas fa-undo"></i> Reset
                        </a>
                        <button type="button" class="btn-outline-gold" onclick="refreshLog()">
                            <i class="fas fa-sync-alt"></i> Refresh
                        </button>
                    </div>
                </div>
            </form>
        </div>

        <!-- Tabel Log -->
        <div class="card-custom">
            <div class="card-header-custom">
                <h3><i class="fas fa-list"></i> Riwayat Aktivitas</h3>
                <span class="badge-stats" style="background:rgba(247,184,1,0.1); color:#F7B801; padding:4px 12px; border-radius:20px; font-size:12px;">
                    <i class="fas fa-database"></i> <?= number_format($total_logs) ?> Data
                </span>
            </div>
            <div class="table-responsive">
                <table class="table-custom table" id="dataTable">
                    <thead>
                        <tr>
                            <th>No</th>
                            <th>Waktu</th>
                            <th>Pengguna</th>
                            <th>Nama</th>
                            <th>Role</th>
                            <th>Aktivitas</th>
                            <th>IP Address</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if (!empty($logs)):
                            $no = $offset + 1;
                            foreach ($logs as $log): 
                                // Tentukan badge berdasarkan aktivitas
                                $badgeClass = 'badge-other';
                                $aktivitas = strtolower($log['aktivitas']);
                                if (strpos($aktivitas, 'login') !== false || strpos($aktivitas, 'masuk') !== false) {
                                    $badgeClass = 'badge-login';
                                } elseif (strpos($aktivitas, 'tambah') !== false || strpos($aktivitas, 'create') !== false || strpos($aktivitas, 'menambahkan') !== false) {
                                    $badgeClass = 'badge-create';
                                } elseif (strpos($aktivitas, 'edit') !== false || strpos($aktivitas, 'update') !== false || strpos($aktivitas, 'mengubah') !== false) {
                                    $badgeClass = 'badge-update';
                                } elseif (strpos($aktivitas, 'hapus') !== false || strpos($aktivitas, 'delete') !== false || strpos($aktivitas, 'menghapus') !== false) {
                                    $badgeClass = 'badge-delete';
                                }
                        ?>
                            <tr>
                                <td><?= $no++ ?></td>
                                <td><?= date('d/m/Y H:i:s', strtotime($log['created_at'])) ?></td>
                                <td><strong><?= htmlspecialchars($log['username']) ?></strong></td>
                                <td><?= htmlspecialchars($log['nama'] ?? '-') ?></td>
                                <td>
                                    <?php if ($log['role'] == 'super_admin'): ?>
                                        <span class="badge-role-super"><i class="fas fa-crown me-1"></i> Super Admin</span>
                                    <?php else: ?>
                                        <span class="badge-role-bpk"><i class="fas fa-user-tie me-1"></i> Admin BPK</span>
                                    <?php endif; ?>
                                </td>
                                <td>
                                    <span class="<?= $badgeClass ?>"><?= htmlspecialchars($log['aktivitas']) ?></span>
                                </td>
                                <td><span style="font-family: monospace; font-size:12px;"><?= htmlspecialchars($log['ip_address'] ?? '-') ?></span></td>
                            </tr>
                        <?php endforeach; else: ?>
                            <tr>
                                <td colspan="7" class="text-center py-5">
                                    <i class="fas fa-inbox fa-3x mb-3 d-block" style="color: #999;"></i>
                                    <p class="mb-0">Belum ada data log aktivitas</p>
                                </td>
                            </tr>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
            <?php if ($total_pages > 1): ?>
                <div style="padding: 16px 24px; border-top: 1px solid rgba(0,0,0,0.08); text-align: center;">
                    <nav>
                        <ul class="pagination justify-content-center mb-0">
                            <?php for ($i = 1; $i <= $total_pages; $i++): ?>
                                <li class="page-item <?= $i == $page ? 'active' : '' ?>">
                                    <a class="page-link" href="?page=<?= $i ?>&role=<?= urlencode($filter_role) ?>&user_id=<?= urlencode($filter_user) ?>&start_date=<?= urlencode($filter_start) ?>&end_date=<?= urlencode($filter_end) ?>&limit=<?= $limit ?>">
                                        <?= $i ?>
                                    </a>
                                </li>
                            <?php endfor; ?>
                        </ul>
                    </nav>
                </div>
            <?php endif; ?>
        </div>
    </div>

    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://cdn.datatables.net/1.13.4/js/jquery.dataTables.min.js"></script>
    <script src="https://cdn.datatables.net/1.13.4/js/dataTables.bootstrap5.min.js"></script>

    <script>
        // DataTables
        $('#dataTable').DataTable({
            language: {
                url: '//cdn.datatables.net/plug-ins/1.13.4/i18n/id.json'
            },
            order: [[1, 'desc']],
            paging: false,
            info: false,
            searching: false
        });

        function refreshLog() {
            location.reload();
        }

        // User dropdown
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