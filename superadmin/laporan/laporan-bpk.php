<?php
require_once __DIR__ . '/../../includes/config.php';
require_once __DIR__ . '/../../includes/session.php';
require_once __DIR__ . '/../../includes/functions.php';

checkAuth();
checkRole(['super_admin']);

$conn = getConnection();
$bpk_list = $conn->query("SELECT b.*, 
    (SELECT COUNT(*) FROM anggota WHERE bpk_id = b.id) as total_anggota,
    (SELECT COUNT(*) FROM anggota WHERE bpk_id = b.id AND status = 'aktif') as anggota_aktif
    FROM bpk b ORDER BY b.nomor_registrasi ASC");
$conn->close();

$title = 'LAPORAN DATA BPK';
$subtitle = 'Seluruh BPK/PMK & Emergency Sekota Banjarbaru';
include 'template-laporan.php';
?>

<?php if ($bpk_list && $bpk_list->num_rows > 0): ?>
    <?php 
    $no = 1;
    while ($bpk = $bpk_list->fetch_assoc()): 
    ?>
        <div style="margin-bottom: 25px; border-bottom: 1px solid #eee; padding-bottom: 20px;">
            <div style="display: flex; align-items: center; gap: 15px; margin-bottom: 10px;">
                <?php if ($bpk['logo'] && file_exists('../../assets/img/uploads/logo/' . $bpk['logo'])): ?>
                    <img src="../../assets/img/uploads/logo/<?= $bpk['logo'] ?>" style="width: 60px; height: 60px; object-fit: contain; border-radius: 8px; border: 1px solid #eee;">
                <?php else: ?>
                    <div style="width: 60px; height: 60px; background: #F7B801; border-radius: 8px; display: flex; align-items: center; justify-content: center; color: white; font-weight: 800; font-size: 24px;">
                        <?= substr($bpk['nama_bpk'], 0, 1) ?>
                    </div>
                <?php endif; ?>
                <div>
                    <h6 style="font-weight: 700; margin: 0; color: #1A1A1A;"><?= $no ?>. <?= htmlspecialchars($bpk['nama_bpk']) ?></h6>
                    <span style="font-size: 11px; color: #F7B801; font-weight: 600;">Reg. <?= htmlspecialchars($bpk['nomor_registrasi']) ?></span>
                </div>
            </div>
            
            <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 4px 20px; font-size: 12px; padding-left: 75px;">
                <div><span style="font-weight: 600;">Alamat</span> : <?= htmlspecialchars($bpk['alamat'] ?? '-') ?></div>
                <div><span style="font-weight: 600;">Kecamatan</span> : <?= htmlspecialchars($bpk['kecamatan'] ?? '-') ?></div>
                <div><span style="font-weight: 600;">Kelurahan</span> : <?= htmlspecialchars($bpk['kelurahan'] ?? '-') ?></div>
                <div><span style="font-weight: 600;">Tahun Berdiri</span> : <?= $bpk['tahun_berdiri'] ?? '-' ?></div>
                <div><span style="font-weight: 600;">Total Anggota</span> : <?= $bpk['total_anggota'] ?? 0 ?> orang</div>
                <div><span style="font-weight: 600;">Anggota Aktif</span> : <?= $bpk['anggota_aktif'] ?? 0 ?> orang</div>
                <div><span style="font-weight: 600;">Koordinat</span> : <?= number_format($bpk['latitude'] ?? 0, 6) ?>, <?= number_format($bpk['longitude'] ?? 0, 6) ?></div>
            </div>
        </div>
    <?php 
        $no++;
    endwhile; 
    ?>
    
    <div style="margin-top: 10px; padding: 15px; background: #F8F8F8; border-radius: 8px;">
        <p style="margin: 0; font-size: 13px;">
            <strong>Total BPK : <?= $bpk_list->num_rows ?> organisasi</strong>
        </p>
    </div>

<?php else: ?>
    <div style="text-align: center; padding: 40px 20px; color: #999;">
        <i class="fas fa-building fa-4x mb-3 d-block" style="color: #ddd;"></i>
        <p style="font-size: 16px; color: #666;">Belum ada data BPK</p>
    </div>
<?php endif; ?>

<?php generateReportFooter(); ?>