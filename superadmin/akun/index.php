<?php
require_once __DIR__ . '/../../includes/config.php';
require_once __DIR__ . '/../../includes/session.php';
require_once __DIR__ . '/../../includes/functions.php';

checkAuth();
checkRole(['super_admin']);

$user = getCurrentUser();
$message = '';
$messageType = '';

// Get total BPK untuk sidebar
$conn = getConnection();
$total_bpk = $conn->query("SELECT COUNT(*) as total FROM bpk")->fetch_assoc()['total'];
$conn->close();

// Include sidebar
include __DIR__ . '/../../includes/sidebar.php';

// Handle delete
if (isset($_GET['delete'])) {
    $id = intval($_GET['delete']);
    
    // Cek jangan hapus sendiri
    if ($id == $_SESSION['user_id']) {
        $message = "Anda tidak dapat menghapus akun sendiri!";
        $messageType = "danger";
    } else {
        $conn = getConnection();
        $stmt = $conn->prepare("DELETE FROM users WHERE id = ?");
        $stmt->bind_param("i", $id);
        if ($stmt->execute()) {
            $message = "Akun berhasil dihapus!";
            $messageType = "success";
        } else {
            $message = "Gagal menghapus akun!";
            $messageType = "danger";
        }
        $stmt->close();
        $conn->close();
    }
}

// Get all users
$conn = getConnection();
$query = "SELECT u.*, b.nama_bpk as bpk_nama FROM users u 
          LEFT JOIN bpk b ON u.bpk_id = b.id 
          ORDER BY u.created_at DESC";
$result = $conn->query($query);
$users = [];
while ($row = $result->fetch_assoc()) {
    $users[] = $row;
}
$conn->close();
?>

<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Data Akun - Super Admin BARRES 698</title>

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

        .btn-tambah {
            background: linear-gradient(135deg, #F7B801, #E5A800);
            border: none; padding: 10px 20px; border-radius: 12px;
            font-weight: 600; font-size: 13px; color: #1A1A1A;
            transition: all 0.3s ease; display: flex; align-items: center; gap: 8px;
        }
        .btn-tambah:hover { transform: translateY(-2px); box-shadow: 0 5px 15px rgba(247,184,1,0.3); }

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

        .btn-action { background: transparent; border: none; padding: 6px 10px; border-radius: 10px; cursor: pointer; }
        .btn-action i { font-size: 14px; color: #999; }
        .btn-action:hover i { color: #F7B801; }
        .btn-action.danger:hover i { color: #dc3545; }

        .badge-role-super { background: rgba(247,184,1,0.15); color: #B8860B; padding: 4px 12px; border-radius: 20px; font-size: 11px; display: inline-block; }
        .badge-role-bpk { background: rgba(23,162,184,0.1); color: #17a2b8; padding: 4px 12px; border-radius: 20px; font-size: 11px; display: inline-block; }

        .alert-custom { border-radius: 14px; padding: 12px 18px; margin-bottom: 20px; animation: slideDown 0.3s ease; display: flex; align-items: center; gap: 12px; }
        .alert-success { background: #d4edda; border-left: 4px solid #28a745; color: #155724; }
        .alert-danger { background: #f8d7da; border-left: 4px solid #dc3545; color: #721c24; }
        @keyframes slideDown { from { opacity: 0; transform: translateY(-10px); } to { opacity: 1; transform: translateY(0); } }

        /* Modal */
        .modal-content { border-radius: 20px; overflow: hidden; border: none; }
        .modal-header { padding: 18px 24px; }
        .modal-header .modal-title { font-size: 18px; font-weight: 600; display: flex; align-items: center; gap: 10px; }
        .modal-header-gradient {
            background: linear-gradient(135deg, #F7B801, #E5A800);
            color: #1A1A1A; border: none;
        }
        .modal-header-gradient .btn-close { filter: brightness(0); }

        .form-label { font-size: 13px; font-weight: 600; margin-bottom: 8px; display: block; color: #1A1A1A; }
        .form-label .required { color: #F7B801; }
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
        }
    </style>
</head>

<body>

    <!-- Sidebar sudah di-include -->
    <div class="main-content">
        <div class="top-navbar">
            <div class="page-title">
                <h2>Data Akun</h2>
                <p>Kelola akun pengguna sistem BARRES 698</p>
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

        <?php if ($message): ?>
            <div class="alert-custom alert-<?= $messageType ?>">
                <i class="fas <?= $messageType == 'success' ? 'fa-check-circle' : 'fa-exclamation-triangle' ?>"></i>
                <span><?= $message ?></span>
            </div>
        <?php endif; ?>

        <!-- Statistik -->
        <?php
        $total_super = 0;
        $total_bpk_admin = 0;
        foreach ($users as $u) {
            if ($u['role'] == 'super_admin') $total_super++;
            else $total_bpk_admin++;
        }
        ?>
        <div class="row g-4 mb-4">
            <div class="col-md-3 col-6">
                <div class="stat-card">
                    <div class="stat-icon"><i class="fas fa-users"></i></div>
                    <div class="stat-number"><?= count($users) ?></div>
                    <div class="stat-label">Total Akun</div>
                </div>
            </div>
            <div class="col-md-3 col-6">
                <div class="stat-card">
                    <div class="stat-icon"><i class="fas fa-crown"></i></div>
                    <div class="stat-number"><?= $total_super ?></div>
                    <div class="stat-label">Super Admin</div>
                </div>
            </div>
            <div class="col-md-3 col-6">
                <div class="stat-card">
                    <div class="stat-icon"><i class="fas fa-user-tie"></i></div>
                    <div class="stat-number"><?= $total_bpk_admin ?></div>
                    <div class="stat-label">Admin BPK</div>
                </div>
            </div>
            <div class="col-md-3 col-6">
                <button class="btn-tambah w-100 py-3" onclick="bukaModalTambah()">
                    <i class="fas fa-plus"></i> Tambah Akun
                </button>
            </div>
        </div>

        <!-- Tabel -->
        <div class="card-custom">
            <div class="card-header-custom">
                <h3><i class="fas fa-list"></i> Daftar Akun</h3>
                <span class="badge-stats" style="background:rgba(247,184,1,0.1); color:#F7B801; padding:4px 12px; border-radius:20px; font-size:12px;">
                    <i class="fas fa-users"></i> <?= count($users) ?> Akun
                </span>
            </div>
            <div class="table-responsive">
                <table class="table-custom table" id="dataTable">
                    <thead>
                        <tr>
                            <th>No</th>
                            <th>Nama</th>
                            <th>Username</th>
                            <th>No HP</th>
                            <th>Role</th>
                            <th>BPK</th>
                            <th>Dibuat</th>
                            <th>Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($users as $index => $row): ?>
                            <tr>
                                <td><?= $index + 1 ?></td>
                                <td><strong><?= htmlspecialchars($row['nama'] ?? '-') ?></strong></td>
                                <td><?= htmlspecialchars($row['username']) ?></td>
                                <td><?= htmlspecialchars($row['no_hp'] ?? '-') ?></td>
                                <td>
                                    <?php if ($row['role'] == 'super_admin'): ?>
                                        <span class="badge-role-super"><i class="fas fa-crown me-1"></i> Super Admin</span>
                                    <?php else: ?>
                                        <span class="badge-role-bpk"><i class="fas fa-user-tie me-1"></i> Admin BPK</span>
                                    <?php endif; ?>
                                </td>
                                <td><?= htmlspecialchars($row['bpk_nama'] ?? '-') ?></td>
                                <td><?= date('d/m/Y H:i', strtotime($row['created_at'])) ?></td>
                                <td>
                                    <button class="btn-action btn-edit" data-id="<?= $row['id'] ?>"><i class="fas fa-edit"></i></button>
                                    <?php if ($row['id'] != $_SESSION['user_id']): ?>
                                        <a href="?delete=<?= $row['id'] ?>" class="btn-action danger" onclick="return confirm('Yakin hapus akun ini?')"><i class="fas fa-trash"></i></a>
                                    <?php else: ?>
                                        <button class="btn-action" disabled style="opacity:0.5;" title="Tidak bisa menghapus akun sendiri"><i class="fas fa-lock"></i></button>
                                    <?php endif; ?>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <!-- MODAL TAMBAH -->
    <div class="modal fade" id="modalTambah" tabindex="-1" data-bs-backdrop="static">
        <div class="modal-dialog modal-lg modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header modal-header-gradient">
                    <h5 class="modal-title"><i class="fas fa-user-plus"></i> Tambah Akun</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <form id="formTambah">
                    <div class="modal-body">
                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label class="form-label required">Nama Lengkap</label>
                                <input type="text" name="nama" class="form-control" placeholder="Nama pengguna" required>
                            </div>
                            <div class="col-md-6 mb-3">
                                <label class="form-label required">Username</label>
                                <input type="text" name="username" class="form-control" placeholder="Username" required>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label class="form-label required">Password</label>
                                <input type="password" name="password" class="form-control" placeholder="Minimal 6 karakter" required minlength="6">
                            </div>
                            <div class="col-md-6 mb-3">
                                <label class="form-label">No HP</label>
                                <input type="text" name="no_hp" class="form-control" placeholder="08xxxxxxxxxx">
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label class="form-label required">Role</label>
                                <select name="role" class="form-select" required onchange="toggleBpkField('tambah')">
                                    <option value="">Pilih Role</option>
                                    <option value="super_admin">Super Admin</option>
                                    <option value="admin_bpk">Admin BPK</option>
                                </select>
                            </div>
                            <div class="col-md-6 mb-3" id="bpk_field_tambah" style="display:none;">
                                <label class="form-label required">Pilih BPK</label>
                                <select name="bpk_id" class="form-select">
                                    <option value="">Pilih BPK</option>
                                    <?php
                                    $conn = getConnection();
                                    $bpk_list = $conn->query("SELECT id, nama_bpk FROM bpk ORDER BY nama_bpk");
                                    while ($bpk = $bpk_list->fetch_assoc()):
                                    ?>
                                        <option value="<?= $bpk['id'] ?>"><?= htmlspecialchars($bpk['nama_bpk']) ?></option>
                                    <?php endwhile;
                                    $conn->close(); ?>
                                </select>
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn-outline-gold" data-bs-dismiss="modal">Batal</button>
                        <button type="submit" class="btn-gold"><i class="fas fa-save"></i> Simpan</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- MODAL EDIT -->
    <div class="modal fade" id="modalEdit" tabindex="-1" data-bs-backdrop="static">
        <div class="modal-dialog modal-lg modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header modal-header-gradient">
                    <h5 class="modal-title"><i class="fas fa-edit"></i> Edit Akun</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <form id="formEdit">
                    <input type="hidden" name="id" id="edit_id">
                    <div class="modal-body">
                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label class="form-label required">Nama Lengkap</label>
                                <input type="text" name="nama" id="edit_nama" class="form-control" required>
                            </div>
                            <div class="col-md-6 mb-3">
                                <label class="form-label required">Username</label>
                                <input type="text" name="username" id="edit_username" class="form-control" required>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label class="form-label">Password <small class="text-muted">(Kosongkan jika tidak diubah)</small></label>
                                <input type="password" name="password" id="edit_password" class="form-control" placeholder="Minimal 6 karakter" minlength="6">
                            </div>
                            <div class="col-md-6 mb-3">
                                <label class="form-label">No HP</label>
                                <input type="text" name="no_hp" id="edit_no_hp" class="form-control" placeholder="08xxxxxxxxxx">
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label class="form-label required">Role</label>
                                <select name="role" id="edit_role" class="form-select" required onchange="toggleBpkField('edit')">
                                    <option value="">Pilih Role</option>
                                    <option value="super_admin">Super Admin</option>
                                    <option value="admin_bpk">Admin BPK</option>
                                </select>
                            </div>
                            <div class="col-md-6 mb-3" id="bpk_field_edit" style="display:none;">
                                <label class="form-label required">Pilih BPK</label>
                                <select name="bpk_id" id="edit_bpk_id" class="form-select">
                                    <option value="">Pilih BPK</option>
                                    <?php
                                    $conn = getConnection();
                                    $bpk_list = $conn->query("SELECT id, nama_bpk FROM bpk ORDER BY nama_bpk");
                                    while ($bpk = $bpk_list->fetch_assoc()):
                                    ?>
                                        <option value="<?= $bpk['id'] ?>"><?= htmlspecialchars($bpk['nama_bpk']) ?></option>
                                    <?php endwhile;
                                    $conn->close(); ?>
                                </select>
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn-outline-gold" data-bs-dismiss="modal">Batal</button>
                        <button type="submit" class="btn-gold"><i class="fas fa-save"></i> Update</button>
                    </div>
                </form>
            </div>
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
            order: [[0, 'asc']]
        });

        // Toggle BPK Field
        function toggleBpkField(type) {
            const prefix = type == 'tambah' ? '' : 'edit_';
            const role = document.getElementById(prefix + 'role').value;
            const field = document.getElementById('bpk_field_' + type);
            if (role == 'admin_bpk') {
                field.style.display = 'block';
                document.getElementById(prefix + 'bpk_id').setAttribute('required', 'required');
            } else {
                field.style.display = 'none';
                document.getElementById(prefix + 'bpk_id').removeAttribute('required');
            }
        }

        // Buka Modal Tambah
        function bukaModalTambah() {
            document.getElementById('formTambah').reset();
            document.getElementById('bpk_field_tambah').style.display = 'none';
            const modal = new bootstrap.Modal(document.getElementById('modalTambah'));
            modal.show();
        }

        // Buka Modal Edit
        document.querySelectorAll('.btn-edit').forEach(btn => {
            btn.addEventListener('click', function() {
                const id = this.dataset.id;
                $.ajax({
                    url: 'get_data.php',
                    type: 'GET',
                    data: { id: id },
                    dataType: 'json',
                    success: function(response) {
                        if (response.success) {
                            const d = response.data;
                            document.getElementById('edit_id').value = d.id;
                            document.getElementById('edit_nama').value = d.nama || '';
                            document.getElementById('edit_username').value = d.username;
                            document.getElementById('edit_no_hp').value = d.no_hp || '';
                            document.getElementById('edit_role').value = d.role;
                            
                            // BPK Field
                            const bpkField = document.getElementById('bpk_field_edit');
                            const bpkSelect = document.getElementById('edit_bpk_id');
                            if (d.role == 'admin_bpk') {
                                bpkField.style.display = 'block';
                                bpkSelect.setAttribute('required', 'required');
                                bpkSelect.value = d.bpk_id || '';
                            } else {
                                bpkField.style.display = 'none';
                                bpkSelect.removeAttribute('required');
                            }
                            
                            const modal = new bootstrap.Modal(document.getElementById('modalEdit'));
                            modal.show();
                        } else {
                            alert('Gagal mengambil data: ' + response.message);
                        }
                    },
                    error: function() {
                        alert('Terjadi kesalahan!');
                    }
                });
            });
        });

        // Submit Tambah
        $('#formTambah').on('submit', function(e) {
            e.preventDefault();
            const formData = new FormData(this);
            formData.append('action', 'tambah');
            
            // Validasi password minimum 6
            const password = formData.get('password');
            if (password.length < 6) {
                alert('Password minimal 6 karakter!');
                return;
            }
            
            $.ajax({
                url: 'save_data.php',
                type: 'POST',
                data: formData,
                processData: false,
                contentType: false,
                dataType: 'json',
                success: function(response) {
                    if (response.success) {
                        location.reload();
                    } else {
                        alert('Error: ' + response.message);
                    }
                },
                error: function() {
                    alert('Terjadi kesalahan!');
                }
            });
        });

        // Submit Edit
        $('#formEdit').on('submit', function(e) {
            e.preventDefault();
            const formData = new FormData(this);
            formData.append('action', 'edit');
            
            // Validasi password jika diisi
            const password = formData.get('password');
            if (password && password.length < 6) {
                alert('Password minimal 6 karakter!');
                return;
            }
            
            $.ajax({
                url: 'save_data.php',
                type: 'POST',
                data: formData,
                processData: false,
                contentType: false,
                dataType: 'json',
                success: function(response) {
                    if (response.success) {
                        location.reload();
                    } else {
                        alert('Error: ' + response.message);
                    }
                },
                error: function() {
                    alert('Terjadi kesalahan!');
                }
            });
        });

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