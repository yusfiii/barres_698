<?php
require_once __DIR__ . '/../../includes/config.php';
require_once __DIR__ . '/../../includes/session.php';
require_once __DIR__ . '/../../includes/functions.php';

checkAuth();
checkRole(['super_admin']);

$conn = getConnection();

// Ambil data anggota per BPK
$query = "SELECT b.id, b.nama_bpk, b.nomor_registrasi, b.logo,
          (SELECT COUNT(*) FROM anggota WHERE bpk_id = b.id) as total_anggota,
          (SELECT COUNT(*) FROM anggota WHERE bpk_id = b.id AND status = 'aktif') as aktif,
          (SELECT COUNT(*) FROM anggota WHERE bpk_id = b.id AND status = 'nonaktif') as nonaktif
          FROM bpk b 
          ORDER BY b.nomor_registrasi ASC";

$bpk_list = $conn->query($query);

// Total keseluruhan
$total_all = $conn->query("SELECT COUNT(*) as total FROM anggota")->fetch_assoc()['total'];
$total_aktif = $conn->query("SELECT COUNT(*) as total FROM anggota WHERE status = 'aktif'")->fetch_assoc()['total'];
$total_nonaktif = $conn->query("SELECT COUNT(*) as total FROM anggota WHERE status = 'nonaktif'")->fetch_assoc()['total'];

$conn->close();

$title = 'LAPORAN ANGGOTA';
$subtitle = 'Jumlah Anggota pada Kesatuan BPK Sekota Banjarbaru';
include 'template-laporan.php';
?>

<div style="margin-bottom: 20px; padding: 15px; background: #F8F8F8; border-radius: 8px; display: flex; gap: 30px; flex-wrap: wrap;">
    <div><strong>Total Anggota</strong> : <span style="color: #1A1A1A; font-weight: 700;"><?= $total_all ?></span> orang</div>
    <div><strong>Aktif</strong> : <span style="color: #28a745; font-weight: 700;"><?= $total_aktif ?></span> orang</div>
    <div><strong>Nonaktif</strong> : <span style="color: #dc3545; font-weight: 700;"><?= $total_nonaktif ?></span> orang</div>
</div>

<table class="table table-bordered" style="font-size: 12px; width: 100%; border-collapse: collapse;">
    <thead>
        <tr style="background: #F7B801; color: #1A1A1A;">
            <th style="padding: 10px; text-align: center; border: 1px solid #ddd;">No</th>
            <th style="padding: 10px; border: 1px solid #ddd;">Nama BPK</th>
            <th style="padding: 10px; text-align: center; border: 1px solid #ddd;">Registrasi</th>
            <th style="padding: 10px; text-align: center; border: 1px solid #ddd;">Total Anggota</th>
            <th style="padding: 10px; text-align: center; border: 1px solid #ddd;">Aktif</th>
            <th style="padding: 10px; text-align: center; border: 1px solid #ddd;">Nonaktif</th>
        </tr>
    </thead>
    <tbody>
        <?php 
        if ($bpk_list && $bpk_list->num_rows > 0):
            $no = 1;
            while ($bpk = $bpk_list->fetch_assoc()):
        ?>
            <tr>
                <td style="padding: 8px 10px; text-align: center; border: 1px solid #ddd;"><?= $no++ ?></td>
                <td style="padding: 8px 10px; border: 1px solid #ddd;"><?= htmlspecialchars($bpk['nama_bpk']) ?></td>
                <td style="padding: 8px 10px; text-align: center; border: 1px solid #ddd;"><?= htmlspecialchars($bpk['nomor_registrasi']) ?></td>
                <td style="padding: 8px 10px; text-align: center; border: 1px solid #ddd; font-weight: 700;"><?= $bpk['total_anggota'] ?? 0 ?></td>
                <td style="padding: 8px 10px; text-align: center; border: 1px solid #ddd; color: #28a745;"><?= $bpk['aktif'] ?? 0 ?></td>
                <td style="padding: 8px 10px; text-align: center; border: 1px solid #ddd; color: #dc3545;"><?= $bpk['nonaktif'] ?? 0 ?></td>
            </tr>
        <?php 
            endwhile;
        else:
        ?>
            <tr>
                <td colspan="6" style="padding: 20px; text-align: center; border: 1px solid #ddd; color: #999;">
                    Belum ada data BPK
                </td>
            </tr>
        <?php endif; ?>
    </tbody>
</table>

<?php generateReportFooter(); ?>