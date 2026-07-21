<?php
/**
 * cetak-pdf-hydrant.php
 * Generate Laporan Data Hydrant Umum dalam format PDF (DomPDF).
 */

require_once __DIR__ . '/../../includes/config.php';
require_once __DIR__ . '/../../includes/session.php';
require_once __DIR__ . '/../../includes/functions.php';
require_once __DIR__ . '/../../includes/pdf-helper.php';

checkAuth();
checkRole(['super_admin']);

$conn = getConnection();

// Filter kecamatan via query string
$filter_kecamatan = isset($_GET['kecamatan']) ? $_GET['kecamatan'] : '';

// MENGGUNAKAN TABEL hydrant
$query = "SELECT * FROM hydrant WHERE 1=1";
$params = [];
$types = "";

if (!empty($filter_kecamatan)) {
    $query .= " AND kecamatan = ?";
    $params[] = $filter_kecamatan;
    $types .= "s";
}

$query .= " ORDER BY id DESC";

$stmt = $conn->prepare($query);

if (!$stmt) {
    die("Terjadi kesalahan pada Query SQL: " . $conn->error);
}

if (!empty($params)) {
    $stmt->bind_param($types, ...$params);
}
$stmt->execute();
$result = $stmt->get_result();
$daftar_hydrant = $result->fetch_all(MYSQLI_ASSOC);
$stmt->close();
$conn->close();

// ====================== SUSUN ISI LAPORAN ======================

ob_start();
?>
<table class="data-table">
    <thead>
        <tr>
            <th style="width: 5%;">No</th>
            <th style="width: 22%;">Nama / Lokasi Hydrant</th>
            <th style="width: 18%;">Kecamatan & Kelurahan</th>
            <th style="width: 23%;">Alamat Lengkap</th>
            <th style="width: 17%;">Titik Koordinat</th>
            <th class="center" style="width: 15%;">Status</th>
        </tr>
    </thead>
    <tbody>
        <?php if (count($daftar_hydrant) > 0): ?>
            <?php foreach ($daftar_hydrant as $i => $hyd): ?>
                <tr>
                    <td class="center"><?= $i + 1 ?></td>
                    <td><strong><?= htmlspecialchars($hyd['keterangan'] ?? 'Hydrant Umum #' . $hyd['id']) ?></strong></td>
                    <td><?= htmlspecialchars($hyd['kecamatan'] ?? '-') ?> / <?= htmlspecialchars($hyd['kelurahan'] ?? '-') ?></td>
                    <td><?= htmlspecialchars($hyd['alamat'] ?? '-') ?></td>
                    <td><?= htmlspecialchars($hyd['latitude'] ?? '-') ?>,<br><?= htmlspecialchars($hyd['longitude'] ?? '-') ?></td>
                    <td class="center" style="text-transform: uppercase;">
                        <?= htmlspecialchars($hyd['status'] ?? 'berfungsi') ?>
                    </td>
                </tr>
            <?php endforeach; ?>
        <?php else: ?>
            <tr>
                <td colspan="6" class="center">Tidak ada data Hydrant terdaftar.</td>
            </tr>
        <?php endif; ?>
    </tbody>
</table>

<p style="margin-top: 10px; font-size: 10pt;">
    Total Titik Hydrant terdaftar: <strong><?= count($daftar_hydrant) ?> titik</strong>
    <?php if (!empty($filter_kecamatan)): ?>
        (Kecamatan: <?= htmlspecialchars($filter_kecamatan) ?>)
    <?php endif; ?>
</p>
<?php
$isi_html = ob_get_clean();

// ====================== RENDER PDF ======================

pdfRender([
    'judul'         => 'LAPORAN DATA FASILITAS HYDRANT UMUM',
    'nomor_urut'    => '030',
    'tanggal_acuan' => time(),
    'isi_html'      => $isi_html,
    'nama_file'     => 'Laporan-Data-Hydrant-' . date('d-m-Y') . '.pdf',
    'tampilkan_ttd' => true,
]);