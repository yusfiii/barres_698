<?php
/**
 * cetak-pdf-sarpras.php
 * Generate Laporan Data Sarpras BPK dalam format PDF (DomPDF).
 */

require_once __DIR__ . '/../../includes/config.php';
require_once __DIR__ . '/../../includes/session.php';
require_once __DIR__ . '/../../includes/functions.php';
require_once __DIR__ . '/../../includes/pdf-helper.php';

checkAuth();
checkRole(['super_admin']);

$conn = getConnection();

// Filter BPK via query string
$filter_bpk = isset($_GET['bpk_id']) ? (int)$_GET['bpk_id'] : 0;

$query = "SELECT b.id as bpk_id, b.nama_bpk, 
                 s.mobil_tangki, s.mobil_portabel, s.mesin_pompa, 
                 s.selang_1_5_inc, s.selang_2_5_inc, s.selang_isap, 
                 s.nozle, s.helm_apd, s.baju_apd, s.celana_apd, s.sepatu_apd 
          FROM bpk b
          LEFT JOIN sapras_bpk s ON b.id = s.bpk_id 
          WHERE 1=1";
$params = [];
$types = "";

if ($filter_bpk > 0) {
    $query .= " AND b.id = ?";
    $params[] = $filter_bpk;
    $types .= "i";
}

$query .= " ORDER BY b.nama_bpk ASC";

$stmt = $conn->prepare($query);

if (!$stmt) {
    die("Terjadi kesalahan pada Query SQL: " . $conn->error);
}

if (!empty($params)) {
    $stmt->bind_param($types, ...$params);
}
$stmt->execute();
$result = $stmt->get_result();
$daftar_sarpras = $result->fetch_all(MYSQLI_ASSOC);
$stmt->close();

// Dapatkan nama BPK jika difilter
$nama_bpk_filter = '';
if ($filter_bpk > 0) {
    $b = $conn->query("SELECT nama_bpk FROM bpk WHERE id = $filter_bpk")->fetch_assoc();
    $nama_bpk_filter = $b ? $b['nama_bpk'] : '';
}
$conn->close();

$items_map = [
    'mobil_tangki'   => ['label' => 'Mobil Tangki', 'satuan' => 'Unit'],
    'mobil_portabel' => ['label' => 'Mobil Portabel', 'satuan' => 'Unit'],
    'mesin_pompa'    => ['label' => 'Mesin Pompa', 'satuan' => 'Unit'],
    'selang_1_5_inc' => ['label' => 'Selang 1.5"', 'satuan' => 'Roll'],
    'selang_2_5_inc' => ['label' => 'Selang 2.5"', 'satuan' => 'Roll'],
    'selang_isap'    => ['label' => 'Selang Isap', 'satuan' => 'Roll'],
    'nozle'          => ['label' => 'Nozle', 'satuan' => 'Pcs'],
    'helm_apd'       => ['label' => 'Helm APD', 'satuan' => 'Pcs'],
    'baju_apd'       => ['label' => 'Baju APD', 'satuan' => 'Pcs'],
    'celana_apd'     => ['label' => 'Celana APD', 'satuan' => 'Pcs'],
    'sepatu_apd'     => ['label' => 'Sepatu APD', 'satuan' => 'Pcs']
];

// ====================== SUSUN ISI LAPORAN ======================

ob_start();
$total_seluruh_barang = 0;
?>
<table class="data-table" style="width: 100%; border-collapse: collapse;">
    <thead>
        <tr>
            <th style="width: 5%;">No</th>
            <th style="width: 35%;">Nama BPK/PMK</th>
            <th style="width: 45%;">Rincian Sarana & Prasarana</th>
            <th class="center" style="width: 15%;">Total Item</th>
        </tr>
    </thead>
    <tbody>
        <?php if (count($daftar_sarpras) > 0): ?>
            <?php foreach ($daftar_sarpras as $i => $s): 
                $rincian_html = [];
                $total_per_bpk = 0;
                $has_record = isset($s['mobil_tangki']);

                if ($has_record) {
                    foreach ($items_map as $db_col => $info) {
                        $qty = (int)$s[$db_col];
                        if ($qty > 0) {
                            $rincian_html[] = "<li style='margin-bottom: 3px;'>{$info['label']}: <strong>{$qty} {$info['satuan']}</strong></li>";
                            $total_per_bpk += $qty;
                        }
                    }
                }
                $total_seluruh_barang += $total_per_bpk;
            ?>
                <tr>
                    <td class="center" style="vertical-align: top; padding-top: 10px;"><?= $i + 1 ?></td>
                    
                    <td style="vertical-align: top; padding-top: 10px;">
                        <strong><?= htmlspecialchars($s['nama_bpk']) ?></strong>
                    </td>
                    
                    <td style="vertical-align: top; padding-top: 10px; padding-left: 5px;">
                        <?php if (!$has_record): ?>
                            <em>Belum melakukan pendataan sarpras.</em>
                        <?php elseif (empty($rincian_html)): ?>
                            <em style="color: #666;">Tidak memiliki inventaris alat.</em>
                        <?php else: ?>
                            <ul style="margin: 0; padding-left: 15px;">
                                <?= implode('', $rincian_html) ?>
                            </ul>
                        <?php endif; ?>
                    </td>
                    
                    <td class="center" style="vertical-align: top; padding-top: 10px; font-weight: bold;">
                        <?= $total_per_bpk ?>
                    </td>
                </tr>
            <?php endforeach; ?>
        <?php else: ?>
            <tr>
                <td colspan="4" class="center">Tidak ada data BPK terdaftar.</td>
            </tr>
        <?php endif; ?>
    </tbody>
</table>

<p style="margin-top: 10px; font-size: 10pt;">
    Total Jumlah Seluruh Inventaris Terdata: <strong><?= $total_seluruh_barang ?> unit/pcs/roll</strong>
</p>
<?php
$isi_html = ob_get_clean();

// ====================== RENDER PDF ======================

$judul_laporan = 'LAPORAN REKAPITULASI SARANA & PRASARANA ' . (!empty($nama_bpk_filter) ? strtoupper($nama_bpk_filter) : 'BPK');

pdfRender([
    'judul'         => $judul_laporan,
    'nomor_urut'    => '031',
    'tanggal_acuan' => time(),
    'isi_html'      => $isi_html,
    'nama_file'     => 'Laporan-Sarpras-' . (!empty($nama_bpk_filter) ? str_replace(' ', '-', $nama_bpk_filter) . '-' : '') . date('d-m-Y') . '.pdf',
    'tampilkan_ttd' => true,
]);