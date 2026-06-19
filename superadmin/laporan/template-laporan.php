<?php
// template-laporan.php - Template Dasar Laporan BARRES 698

function generateReportHeader($title, $subtitle = '') {
    ?>
    <!DOCTYPE html>
    <html>
    <head>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <title><?= $title ?> - BARRES 698</title>
        <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet">
        <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
        <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
        <style>
            * { margin: 0; padding: 0; box-sizing: border-box; }
            
            @page { 
                margin: 2cm; 
                size: A4;
            }
            
            body { 
                font-family: 'Poppins', sans-serif; 
                background: white; 
                color: #1A1A1A;
                padding: 0;
                font-size: 12px;
            }
            
            .container-report {
                max-width: 1000px;
                margin: 0 auto;
                padding: 20px 0;
            }
            
            /* KOP SURAT */
            .kop-surat {
                border-bottom: 3px double #F7B801;
                padding-bottom: 15px;
                margin-bottom: 20px;
                text-align: center;
            }
            
            .kop-surat .logo-container {
                display: flex;
                align-items: center;
                justify-content: center;
                gap: 15px;
                margin-bottom: 5px;
            }
            
            .kop-surat .logo-placeholder {
                width: 70px;
                height: 70px;
                background: #F7B801;
                border-radius: 50%;
                display: flex;
                align-items: center;
                justify-content: center;
                font-size: 32px;
                color: white;
                font-weight: 800;
                flex-shrink: 0;
            }
            
            .kop-surat .kop-text {
                text-align: left;
            }
            
            .kop-surat h1 {
                font-size: 18px;
                font-weight: 800;
                color: #1A1A1A;
                margin: 0;
                letter-spacing: 1px;
                line-height: 1.3;
            }
            
            .kop-surat .subtitle {
                font-size: 12px;
                color: #444;
                font-weight: 600;
            }
            
            .kop-surat .address {
                font-size: 10px;
                color: #666;
                margin: 1px 0;
            }
            
            .kop-surat .contact {
                font-size: 10px;
                color: #777;
                margin: 1px 0;
            }
            
            /* Nomor & Perihal */
            .surat-info {
                display: flex;
                justify-content: space-between;
                margin: 15px 0 10px 0;
                font-size: 12px;
            }
            
            .surat-info .left {
                text-align: left;
            }
            
            .surat-info .right {
                text-align: right;
            }
            
            .surat-info .label {
                font-weight: 600;
            }
            
            /* JUDUL LAPORAN */
            .report-title {
                text-align: center;
                margin: 20px 0;
            }
            
            .report-title h3 {
                font-weight: 700;
                font-size: 18px;
                color: #1A1A1A;
                text-transform: uppercase;
                letter-spacing: 1px;
            }
            
            .report-title .period {
                font-size: 12px;
                color: #666;
                margin-top: 4px;
            }
            
            /* Detail Item - Format seperti contoh */
            .detail-item {
                display: flex;
                padding: 6px 0;
                border-bottom: 1px dashed #E8E8E8;
                font-size: 12px;
            }
            
            .detail-item .label {
                font-weight: 600;
                width: 180px;
                color: #333;
                flex-shrink: 0;
            }
            
            .detail-item .value {
                flex: 1;
                color: #1A1A1A;
            }
            
            /* Section untuk Foto */
            .foto-section {
                margin: 20px 0;
                padding: 15px;
                border: 1px dashed #ddd;
                border-radius: 8px;
                text-align: center;
                min-height: 150px;
                background: #FAFAFA;
            }
            
            .foto-section .foto-placeholder {
                color: #999;
                font-size: 13px;
            }
            
            .foto-section .foto-placeholder i {
                font-size: 48px;
                display: block;
                margin-bottom: 10px;
                color: #ddd;
            }
            
            /* TTD */
            .ttd-section {
                margin-top: 40px;
                text-align: right;
                padding-top: 15px;
            }
            
            .ttd-section .ttd-place {
                font-size: 12px;
                color: #555;
            }
            
            .ttd-section .ttd-name {
                font-weight: 600;
                font-size: 14px;
                color: #1A1A1A;
                margin-top: 35px;
            }
            
            .ttd-section .ttd-title {
                font-size: 12px;
                color: #666;
            }
            
            .ttd-section .ttd-stamp {
                margin-top: 3px;
                font-size: 10px;
                color: #999;
                font-style: italic;
            }
            
            /* Footer */
            .footer-report {
                margin-top: 20px;
                text-align: center;
                font-size: 10px;
                color: #999;
                border-top: 1px solid #E8E8E8;
                padding-top: 12px;
            }
            
            /* Tombol */
            .btn-print {
                position: fixed;
                top: 20px;
                right: 20px;
                background: #F7B801;
                color: #1A1A1A;
                border: none;
                padding: 10px 24px;
                border-radius: 12px;
                font-weight: 600;
                cursor: pointer;
                z-index: 1000;
                font-family: 'Poppins', sans-serif;
                font-size: 13px;
                box-shadow: 0 4px 12px rgba(247, 184, 1, 0.3);
            }
            
            .btn-print:hover {
                background: #E5A800;
            }
            
            .btn-back {
                position: fixed;
                top: 20px;
                left: 20px;
                background: #F0F0F0;
                color: #333;
                border: none;
                padding: 10px 20px;
                border-radius: 12px;
                font-weight: 600;
                cursor: pointer;
                z-index: 1000;
                font-family: 'Poppins', sans-serif;
                font-size: 13px;
                text-decoration: none;
            }
            
            .btn-back:hover {
                background: #E0E0E0;
            }
            
            @media print {
                .btn-print, .btn-back, .no-print { display: none !important; }
                body { padding: 0; margin: 0; }
                .container-report { padding: 0; }
            }
        </style>
    </head>
    <body>
        
        <!-- Tombol -->
        <button class="btn-print no-print" onclick="window.print()">
            <i class="fas fa-print me-2"></i> Cetak Laporan
        </button>
        <a href="index.php" class="btn-back no-print">
            <i class="fas fa-arrow-left me-2"></i> Kembali
        </a>
        
        <div class="container-report">
            
            <!-- KOP SURAT -->
            <div class="kop-surat">
                <div class="logo-container">
                    <div class="logo-placeholder">B</div>
                    <div class="kop-text">
                        <h1>SEKRETARIAT BANJARBARU RESCUE "BARRES 698"</h1>
                        <div class="subtitle">KOTA BANJARBARU</div>
                        <div class="address">Nomor AHU-0006775.AH.01.07. Tahun 2025</div>
                        <div class="address">Jl. Zafri Zamzam II Komplek H. KA Ganie No. 06 RT. 013 RW. 003</div>
                        <div class="address">Kel. Kemuning Kec. Banjarbaru Selatan, Kota Banjarbaru</div>
                        <div class="contact">WhatsApp : 0851 868 14698 | Freq : 15.698.0 Mhz</div>
                        <div class="contact">E-mail : barres698.banjarbaru@gmail.com</div>
                    </div>
                </div>
            </div>
            
            <!-- NOMOR & PERIHAL -->
            <div class="surat-info">
                <div class="left">
                    <span class="label">Nomor</span> : <?= isset($_GET['nomor']) ? $_GET['nomor'] : '022/BARRES698/I/2026' ?><br>
                    <span class="label">Lampiran</span> : 1 (satu) berkas
                </div>
                <div class="right">
                    Banjarbaru, <?= date('d F Y') ?>
                </div>
            </div>
            
            <!-- JUDUL -->
            <div class="report-title">
                <h3><?= strtoupper($title) ?></h3>
                <?php if ($subtitle): ?>
                    <div class="period"><?= $subtitle ?></div>
                <?php endif; ?>
            </div>
            
            <!-- KONTEN -->
            <div class="report-content">
    <?php
}

function generateReportFooter($ttd_name = 'Kemas Akhmad Rudi Indrajaya', $ttd_title = 'KETUA UMUM BARRES 698') {
    ?>
            </div>
            
            <!-- TTD -->
            <div class="ttd-section">
                <div class="ttd-place">Banjarbaru, <?= date('d F Y') ?></div>
                <div class="ttd-name"><?= $ttd_name ?></div>
                <div class="ttd-title"><?= $ttd_title ?></div>
                <div class="ttd-stamp">*Laporan ini dicetak secara elektronik dan sah tanpa tanda tangan basah</div>
            </div>
            
            <!-- FOOTER -->
            <div class="footer-report">
                Laporan Resmi BARRES 698 - Dicetak pada <?= date('d/m/Y H:i') ?>
            </div>
            
        </div>
        
        <script>
            <?php if (isset($_GET['print']) && $_GET['print'] == 1): ?>
            window.onload = function() { setTimeout(function() { window.print(); }, 500); }
            <?php endif; ?>
        </script>
    </body>
    </html>
    <?php
}

// Fungsi untuk menampilkan detail item
function showDetail($label, $value) {
    echo '<div class="detail-item">';
    echo '<div class="label">' . $label . '</div>';
    echo '<div class="value">: ' . ($value ?: '-') . '</div>';
    echo '</div>';
}
?>