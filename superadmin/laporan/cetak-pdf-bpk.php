<?php
/**
 * cetak-pdf-bpk.php
 * Generate Laporan Data BPK (semua BPK Sekota Banjarbaru yang terdaftar)
 * dalam format PDF (DomPDF), memakai includes/pdf-helper.php.
 */

require_once __DIR__ . '/../../includes/config.php';
require_once __DIR__ . '/../../includes/session.php';
require_once __DIR__ . '/../../includes/functions.php';
require_once __DIR__ . '/../../includes/pdf-helper.php';

checkAuth();
checkRole(['super_admin']);

$conn = getConnection();

// Opsional: filter kecamatan via query string
$filter_kecamatan = isset($_GET['kecamatan']) ? $_GET['kecamatan'] : '';

$query = "
    SELECT bpk.*, 
    (SELECT COUNT(*) FROM anggota WHERE bpk_id = bpk.id) AS jumlah_anggota 
    FROM bpk 
    WHERE 1=1
";
$params = [];
$types = "";

if (!empty($filter_kecamatan)) {
    $query .= " AND kecamatan = ?";
    $params[] = $filter_kecamatan;
    $types .= "s";
}

$query .= " ORDER BY nomor_registrasi ASC";

$stmt = $conn->prepare($query);
if (!empty($params)) {
    $stmt->bind_param($types, ...$params);
}
$stmt->execute();
$result = $stmt->get_result();
$daftar_bpk = $result->fetch_all(MYSQLI_ASSOC);
$stmt->close();
$conn->close();

// ====================== SUSUN ISI LAPORAN (KHUSUS BPK) ======================

ob_start();
?>
<table class="data-table">
    <thead>
        <tr>
            <th style="width: 5%;">No</th>
            <th style="width: 10%;">No. Reg</th>
            <th style="width: 20%;">Nama BPK/PMK</th>
            <th style="width: 18%;">Kecamatan & Kelurahan</th>
            <th style="width: 22%;">Alamat</th>
            <th style="width: 15%;">Titik Koordinat Posko</th>
            <th class="center" style="width: 10%;">Jumlah Anggota</th>
        </tr>
    </thead>
    <tbody>
        <?php if (count($daftar_bpk) > 0): ?>
            <?php foreach ($daftar_bpk as $i => $bpk): ?>
                <tr>
                    <td class="center"><?= $i + 1 ?></td>
                    <td><?= htmlspecialchars($bpk['nomor_registrasi'] ?? '-') ?></td>
                    <td><?= htmlspecialchars($bpk['nama_bpk'] ?? '-') ?></td>
                    <td><?= htmlspecialchars($bpk['kecamatan'] ?? '-') ?> / <?= htmlspecialchars($bpk['kelurahan'] ?? '-') ?></td>
                    <td><?= htmlspecialchars($bpk['alamat'] ?? '-') ?></td>
                    <td><?= htmlspecialchars($bpk['latitude'] ?? '-') ?>,<br><?= htmlspecialchars($bpk['longitude'] ?? '-') ?></td>
                    <td class="center"><?= (int) ($bpk['jumlah_anggota'] ?? 0) ?></td>
                </tr>
            <?php endforeach; ?>
        <?php else: ?>
            <tr>
                <td colspan="7" class="center">Tidak ada data BPK terdaftar.</td>
            </tr>
        <?php endif; ?>
    </tbody>
</table>

<p style="margin-top: 10px; font-size: 10pt;">
    Total BPK/PMK terdaftar: <strong><?= count($daftar_bpk) ?></strong>
    <?php if (!empty($filter_kecamatan)): ?>
        (Kecamatan: <?= htmlspecialchars($filter_kecamatan) ?>)
    <?php endif; ?>
</p>
<?php
$isi_html = ob_get_clean();

// ====================== RENDER PDF ======================

pdfRender([
    'judul'         => 'LAPORAN DATA BPK',
    'nomor_urut'    => '023',
    'tanggal_acuan' => time(),
    'isi_html'      => $isi_html,
    'nama_file'     => 'Laporan-Data-BPK-' . date('d-m-Y') . '.pdf',
    'tampilkan_ttd' => true,
]);