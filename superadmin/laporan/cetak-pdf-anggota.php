<?php
/**
 * cetak-pdf-anggota.php
 * Generate Laporan Data Anggota BPK dalam format PDF (DomPDF).
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

$query = "SELECT a.*, b.nama_bpk 
          FROM anggota a
          LEFT JOIN bpk b ON a.bpk_id = b.id 
          WHERE 1=1";
$params = [];
$types = "";

if ($filter_bpk > 0) {
    $query .= " AND a.bpk_id = ?";
    $params[] = $filter_bpk;
    $types .= "i";
}

$query .= " ORDER BY b.nama_bpk ASC, a.nomor_anggota ASC";

$stmt = $conn->prepare($query);
if (!empty($params)) {
    $stmt->bind_param($types, ...$params);
}
$stmt->execute();
$result = $stmt->get_result();
$daftar_anggota = $result->fetch_all(MYSQLI_ASSOC);
$stmt->close();

// Dapatkan nama BPK jika difilter
$nama_bpk_filter = '';
if ($filter_bpk > 0) {
    $b = $conn->query("SELECT nama_bpk FROM bpk WHERE id = $filter_bpk")->fetch_assoc();
    $nama_bpk_filter = $b ? $b['nama_bpk'] : '';
}

$conn->close();

// ====================== SUSUN ISI LAPORAN (KHUSUS ANGGOTA) ======================

ob_start();
?>

<style>
    /* CSS Khusus untuk tabel anggota agar tidak terpotong (Fixed Layout) */
    .data-table-fixed {
        width: 100%;
        border-collapse: collapse;
        table-layout: fixed; /* Mengunci lebar kolom secara absolut */
        margin-bottom: 20px;
    }
    .data-table-fixed th, .data-table-fixed td {
        border: 1px solid #666;
        padding: 6px 4px; /* Padding dikecilkan sedikit */
        font-size: 8.5pt; /* Font dikecilkan agar muat 9 kolom */
        word-wrap: break-word; /* Memaksa teks panjang (NIK/No.HP) turun ke bawah */
        vertical-align: top;
    }
    .data-table-fixed th {
        background: #ECECEC;
        text-align: center;
        font-weight: bold;
    }
    .center { text-align: center; }
</style>

<table class="data-table-fixed">
    <thead>
        <tr>
            <th style="width: 4%;">No</th>
            <th style="width: 5%;">Reg</th>
            <th style="width: 16%;">Nama</th>
            <th style="width: 15%;">Tempat, Tgl Lahir</th>
            <th style="width: 4%;">JK</th>
            <th style="width: 20%;">Alamat</th>
            <th style="width: 14%;">NIK</th>
            <th style="width: 12%;">No. HP</th>
            <th style="width: 10%;">Jabatan</th>
        </tr>
    </thead>
    <tbody>
        <?php if (count($daftar_anggota) > 0): ?>
            <?php foreach ($daftar_anggota as $i => $anggota): 
                $jk = ($anggota['jenis_kelamin'] == 'Laki-laki') ? 'L' : 'P';
                $ttl = htmlspecialchars($anggota['tempat_lahir'] ?? '') . ', ' . ($anggota['tanggal_lahir'] ? date('d/m/Y', strtotime($anggota['tanggal_lahir'])) : '');
                if($ttl == ', ') $ttl = '-';
            ?>
                <tr>
                    <td class="center"><?= $i + 1 ?></td>
                    <td class="center"><?= sprintf("%02d", $anggota['nomor_anggota']) ?></td>
                    <td>
                        <?= htmlspecialchars($anggota['nama'] ?? '-') ?><br>
                        <?php if ($filter_bpk == 0): ?>
                            <small style="color:#666; font-size:7pt;">(<?= htmlspecialchars($anggota['nama_bpk'] ?? '-') ?>)</small>
                        <?php endif; ?>
                    </td>
                    <td><?= $ttl ?></td>
                    <td class="center"><?= $jk ?></td>
                    <td><?= htmlspecialchars($anggota['alamat'] ?? '-') ?></td>
                    <td class="center"><?= htmlspecialchars($anggota['nik'] ?? '-') ?></td>
                    <td class="center"><?= htmlspecialchars($anggota['no_hp'] ?? '-') ?></td>
                    <td class="center"><?= htmlspecialchars($anggota['jabatan'] ?? '-') ?></td>
                </tr>
            <?php endforeach; ?>
        <?php else: ?>
            <tr>
                <td colspan="9" class="center">Tidak ada data Anggota terdaftar.</td>
            </tr>
        <?php endif; ?>
    </tbody>
</table>

<p style="margin-top: 10px; font-size: 10pt;">
    Total Anggota terdaftar: <strong><?= count($daftar_anggota) ?> orang</strong>
</p>

<?php
$isi_html = ob_get_clean();

// ====================== RENDER PDF ======================

// MENYESUAIKAN JUDUL LAPORAN BERDASARKAN BPK YANG DIPILIH
$judul_laporan = 'LAPORAN DATA ANGGOTA ' . (!empty($nama_bpk_filter) ? strtoupper($nama_bpk_filter) : 'BPK');

pdfRender([
    'judul'         => $judul_laporan,
    'nomor_urut'    => '024',
    'tanggal_acuan' => time(),
    'isi_html'      => $isi_html,
    'nama_file'     => 'Laporan-Data-Anggota-' . date('d-m-Y') . '.pdf',
    'tampilkan_ttd' => true,
]);