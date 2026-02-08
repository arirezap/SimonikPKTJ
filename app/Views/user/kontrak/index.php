<?= $this->extend('layouts/main') ?>

<?= $this->section('content') ?>
<script src="https://cdnjs.cloudflare.com/ajax/libs/html2pdf.js/0.10.1/html2pdf.bundle.min.js"></script>

<?php
// FUNGSI HELPER: Nama Uppercase, Gelar Asli
function format_nama_gelar($text) {
    if (empty($text)) return '-';
    $parts = explode(',', $text, 2);
    $nama = strtoupper(trim($parts[0]));
    $gelar = isset($parts[1]) ? ',' . $parts[1] : '';
    return $nama . $gelar;
}
?>

<style>
    /* --- STYLE UTAMA KERTAS --- */
    .paper-container {
        background-color: #fff;
        width: 210mm; 
        
        /* Tinggi Minimum Pas A4 */
        min-height: 296mm; 
        
        /* Padding: Atas/Bawah 10mm (Rapat), Kiri/Kanan 20mm */
        padding: 10mm 20mm; 
        
        margin: 0 auto;
        box-shadow: 0 0 15px rgba(0,0,0,0.1);
        
        font-family: 'Bookman Old Style', 'URW Bookman L', 'Times New Roman', serif;
        
        /* Font 12pt */
        font-size: 12pt; 
        /* Line Height Rapat 1.1 */
        line-height: 1.1; 
        
        color: #000;
        box-sizing: border-box; 
        
        /* Potong overflow agar tidak ada halaman kosong */
        overflow: hidden; 
    }

    /* --- FRAME (BINGKAI 1 GARIS) --- */
    .content-frame {
        border: 2px solid #000; 
        padding: 5mm; 
        width: 100%;
        height: 100%; 
        box-sizing: border-box;
    }

    /* --- HEADER (LOGO TENGAH) --- */
    .header-layout {
        display: flex;
        justify-content: center; 
        align-items: center; 
        gap: 15px; 
        
        width: 100%;
        border-bottom: 2px solid #000; 
        padding-bottom: 5px;           
        margin-bottom: 10px;          
    }

    .header-logo {
        width: 60px; /* Logo Header */
        height: 60px;
        object-fit: contain; 
        flex-shrink: 0;
    }

    .header-text {
        text-align: center;
        flex: 1;
    }

    .header-text h3 {
        margin: 0;
        font-size: 11pt; /* Judul Kop sedikit lebih kecil agar muat 1 baris */
        font-weight: bold;
        text-transform: uppercase;
        line-height: 1.2; 
    }
    
    /* --- ISI DOKUMEN --- */
    .doc-text-justify { 
        text-align: justify; 
        line-height: 1.1; 
        font-size: 12pt; 
    }
    
    .doc-bold { font-weight: bold; }
    
    /* Tabel Biodata */
    .doc-table { 
        width: 100%; 
        border-collapse: collapse; 
        margin-top: 5px; 
        margin-bottom: 5px; 
        font-size: 12pt; 
    }
    .doc-table td { 
        vertical-align: top; 
        padding: 1px 2px; 
    }

    /* Tanda Tangan */
    .signature-table { 
        margin-top: 15px; 
        width: 100%; 
        font-size: 12pt; 
    }
    .signature-table td { 
        text-align: center; 
        vertical-align: top; 
    }
    
    /* Ruang TTD */
    .ttd-space { height: 70px; } 
    
    /* Paragraf */
    p { margin-bottom: 5px; margin-top: 0; }

    /* --- MEDIA PRINT --- */
    @media print {
        @page { size: A4; margin: 0; }
        body { margin: 0; padding: 0; background-color: #fff; }
        body * { visibility: hidden; }
        
        #element-to-print, #element-to-print * { visibility: visible; }
        
        #element-to-print {
            position: absolute; left: 0; top: 0; 
            width: 210mm; 
            height: 296mm; 
            margin: 0; 
            padding: 10mm 20mm !important; 
            box-shadow: none;
            overflow: hidden; 
        }
        
        .content-frame { border: 2px solid #000 !important; }
        .sidebar, header, footer, .btn-print-area { display: none !important; }
    }
</style>

<div class="container-fluid">
    
    <div class="d-flex justify-content-between align-items-center mb-4 btn-print-area">
        <h1 class="h3 mb-0 text-gray-800">Kontrak Kinerja</h1>
        <div>
            <a href="<?= site_url('user/dashboard') ?>" class="btn btn-secondary btn-sm me-2">
                <i class="bi bi-arrow-left"></i> Kembali
            </a>
            
            <button onclick="window.print()" class="btn btn-info btn-sm me-2 text-white">
                <i class="bi bi-printer-fill"></i> Cetak
            </button>

            <button onclick="downloadPDF()" class="btn btn-primary btn-sm">
                <i class="bi bi-file-earmark-pdf-fill"></i> Download PDF
            </button>
        </div>
    </div>

    <div class="row justify-content-center mb-5">
        <div class="col-auto">
            <div class="paper-container" id="element-to-print">
                
                <div class="content-frame">
                    
                    <div class="header-layout">
                        <img src="<?= base_url('assets/logo_kemenhub.png') ?>" alt="Kemenhub" class="header-logo" onerror="this.style.opacity='0'">
                        
                        <div class="header-text">
                            <h3>KONTRAK KINERJA</h3>
                            <h3>PEGAWAI BADAN LAYANAN UMUM</h3>
                            <h3>POLITEKNIK KESELAMATAN TRANSPORTASI JALAN</h3>
                            <h3>BADAN PENGEMBANGAN SDM PERHUBUNGAN</h3>
                            <h3>KEMENTERIAN PERHUBUNGAN R.I.</h3>
                            <h3>TAHUN <?= esc($tahun) ?></h3>
                        </div>

                        <img src="<?= base_url('assets/logo_pktj.png') ?>" alt="PKTJ" class="header-logo">
                    </div>

                    <div class="doc-text-justify">
                        <p>Dalam rangka mewujudkan tujuan Badan Layanan Umum yang efektif, transparan dan akuntabel serta berorientasi pada hasil, kami yang bertanda tangan di bawah ini:</p>
                        
                        <table class="doc-table">
                            <tr>
                                <td style="width: 120px;">Nama</td>
                                <td style="width: 10px;">:</td>
                                <td class="doc-bold"><?= esc(format_nama_gelar($user['nama_lengkap'])) ?></td>
                            </tr>
                            <tr>
                                <td>Jabatan</td>
                                <td>:</td>
                                <td><?= esc($user['jabatan'] ?? '-') ?></td>
                            </tr>
                            <tr>
                                <td colspan="3" style="padding-top: 2px;">Selanjutnya disebut sebagai <strong>PIHAK PERTAMA</strong></td>
                            </tr>
                        </table>

                        <table class="doc-table">
                            <tr>
                                <td style="width: 120px;">Nama</td>
                                <td style="width: 10px;">:</td>
                                <td class="doc-bold"><?= esc(format_nama_gelar($atasan['nama_lengkap'])) ?></td>
                            </tr>
                            <tr>
                                <td>Jabatan</td>
                                <td>:</td>
                                <td><?= esc($atasan['jabatan']) ?><br>Politeknik Keselamatan Transportasi Jalan</td>
                            </tr>
                            <tr>
                                <td colspan="3" style="padding-top: 2px;">Selanjutnya disebut sebagai <strong>PIHAK KEDUA</strong></td>
                            </tr>
                        </table>

                        <br>
                        <p>
                            PIHAK PERTAMA pada tahun <?= esc($tahun) ?> ini berjanji akan mewujudkan target kinerja tahunan sesuai lampiran perjanjian ini dalam rangka mencapai target kinerja jangka menengah seperti yang telah ditetapkan dalam dokumen perencanaan. Keberhasilan dan kegagalan pencapaian target kinerja tersebut menjadi tanggung jawab PIHAK PERTAMA.
                        </p>
                        <p>
                            PIHAK KEDUA akan memberikan supervisi yang diperlukan serta akan melakukan evaluasi akuntabilitas kinerja terhadap capaian kinerja dari perjanjian ini dan mengambil tindakan yang diperlukan dalam rangka pemberian penghargaan dan sanksi.
                        </p>
                    </div>

                    <table class="signature-table">
                        <tr>
                            <td style="width: 50%;"></td>
                            <td style="width: 50%;">Tegal, <?= esc($tanggal) ?></td>
                        </tr>
                        <tr>
                            <td class="doc-bold">PIHAK KEDUA</td>
                            <td class="doc-bold">PIHAK PERTAMA</td>
                        </tr>
                        <tr>
                            <td class="ttd-space"></td>
                            <td class="ttd-space"></td>
                        </tr>
                        <tr>
                            <td>
                                <span class="doc-bold" style="text-decoration: underline;"><?= esc(format_nama_gelar($atasan['nama_lengkap'])) ?></span><br>
                                <?= esc($atasan['pangkat']) ?><br>
                                NIP. <?= esc($atasan['nip']) ?>
                            </td>
                            <td>
                                <span class="doc-bold" style="text-decoration: underline;"><?= esc(format_nama_gelar($user['nama_lengkap'])) ?></span><br>
                                <?= esc($user['pangkat'] ?? '-') ?><br>
                                NIP. <?= esc($user['nip'] ?? '-') ?>
                            </td>
                        </tr>
                    </table>
                
                </div> 
            </div>
        </div>
    </div>
</div>

<script>
    function downloadPDF() {
        var element = document.getElementById('element-to-print');
        var namaUser = "<?= esc($user['nama_lengkap']) ?>";
        var tahun = "<?= esc($tahun) ?>";
        var safeNama = namaUser.replace(/[^a-zA-Z0-9]/g, "_");
        
        var opt = {
            margin:       0, 
            filename:     'Kontrak_Kinerja_' + tahun + '_' + safeNama + '.pdf',
            image:        { type: 'jpeg', quality: 0.98 },
            html2canvas:  { 
                scale: 2, 
                useCORS: true, 
                scrollY: 0, 
                windowHeight: 1123 
            }, 
            pagebreak: { mode: 'avoid-all' },
            jsPDF:        { unit: 'mm', format: 'a4', orientation: 'portrait' }
        };
        html2pdf().set(opt).from(element).save();
    }
</script>

<?= $this->endSection() ?>