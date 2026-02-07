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
        
        /* Tinggi Minimum diset pas A4 */
        min-height: 296mm; 
        
        /* Padding Atas/Bawah 10mm (Rapat), Kiri/Kanan 25mm */
        padding: 10mm 25mm; 
        
        margin: 0 auto;
        box-shadow: 0 0 15px rgba(0,0,0,0.1);
        
        font-family: 'Bookman Old Style', 'URW Bookman L', 'Times New Roman', serif;
        font-size: 12pt; 
        line-height: 1.1; 
        
        color: #000;
        box-sizing: border-box; 
        
        /* FIX BLANK PAGE */
        overflow: hidden; 
    }

    /* --- HEADER (TENGAH) --- */
    .header-center {
        text-align: center;
        margin-bottom: 10px; 
        margin-top: 0;
    }
    
    .logo-kemenhub {
        width: 120px; /* Logo Besar */
        height: auto;
        margin-bottom: 2px; 
    }
    
    .header-text {
        text-transform: uppercase;
        font-weight: normal; 
        font-size: 13pt; 
        letter-spacing: 1px;
        margin-bottom: 5px; 
    }
    
    .header-title {
        text-transform: uppercase;
        font-weight: normal; 
        font-size: 13pt;
        margin-bottom: 10px; 
    }

    /* --- ISI DOKUMEN --- */
    .doc-content { 
        text-align: justify; 
        line-height: 1.1; 
        font-size: 12pt; 
    }
    
    /* Tabel Biodata */
    .doc-table { 
        width: 100%; 
        border-collapse: collapse; 
        margin-bottom: 5px; 
    }
    .doc-table td { 
        vertical-align: top; 
        padding: 1px 0; 
    }
    
    .col-label { width: 130px; }
    .col-sep { width: 15px; text-align: center; }

    /* List Poin 1-7 */
    .poin-list { 
        padding-left: 20px; 
        margin-top: 5px; 
        margin-bottom: 5px; 
    }
    .poin-list li { 
        margin-bottom: 2px; 
        padding-left: 10px; 
        text-align: justify;
    }

    /* --- TANDA TANGAN --- */
    .signature-table { 
        margin-top: 15px; 
        width: 100%; 
        font-size: 12pt;
    }
    .signature-table td { 
        text-align: center; 
        vertical-align: top; 
    }
    
    /* Area Kosong TTD */
    .ttd-space { 
        height: 100px; 
        position: relative; /* Wajib relative agar hint materai bisa absolute */
    } 

    /* Hint Materai Custom */
    .materai-box {
        position: absolute;
        top: 50%;             /* Posisi vertikal tengah */
        transform: translateY(-50%);
        left: 20%;            /* Posisi Horizontal: Agak ke kiri (20% dari lebar kolom) */
        
        /* Styling Kotak */
        width: 60px;
        height: 30px;
        border: 1px dashed #ccc; /* Garis putus-putus samar */
        color: #ccc;
        font-size: 9pt;
        display: flex;
        align-items: center;
        justify-content: center;
        text-align: center;
    }

    /* Paragraf */
    p { margin-bottom: 3px; margin-top: 0; }

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
            padding: 10mm 25mm !important; 
            box-shadow: none;
            overflow: hidden; 
        }
        
        .sidebar, header, footer, .btn-print-area { display: none !important; }
    }
</style>

<div class="container-fluid">
    
    <div class="d-flex justify-content-between align-items-center mb-4 btn-print-area">
        <h1 class="h3 mb-0 text-gray-800">Pakta Integritas</h1>
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
                
                <div class="header-center">
                    <img src="<?= base_url('assets/logo_kemenhub.png') ?>" alt="Kemenhub" class="logo-kemenhub">
                    <div class="header-text">KEMENTERIAN PERHUBUNGAN</div>
                    <div class="header-title">PAKTA INTEGRITAS</div>
                </div>

                <div class="doc-content">
                    <p>Saya yang bertanda tangan di bawah ini:</p>
                    
                    <table class="doc-table">
                        <tr>
                            <td class="col-label">Nama</td>
                            <td class="col-sep">:</td>
                            <td><?= esc(format_nama_gelar($user['nama_lengkap'])) ?></td>
                        </tr>
                        <tr>
                            <td class="col-label">NIP</td>
                            <td class="col-sep">:</td>
                            <td><?= esc($user['nip']) ?></td>
                        </tr>
                        <tr>
                            <td class="col-label">Pangkat/Gol</td>
                            <td class="col-sep">:</td>
                            <td><?= esc($user['pangkat'] ?? '-') ?></td>
                        </tr>
                        <tr>
                            <td class="col-label">Jabatan</td>
                            <td class="col-sep">:</td>
                            <td><?= esc($user['jabatan'] ?? '-') ?></td>
                        </tr>
                        <tr>
                            <td class="col-label">Unit Kerja</td>
                            <td class="col-sep">:</td>
                            <td><?= esc($user['unit'] ?? 'Politeknik Keselamatan Transportasi Jalan') ?></td>
                        </tr>
                    </table>

                    <p>Dengan ini menyatakan Pakta Integritas sebagai berikut:</p>

                    <ol class="poin-list">
                        <li>Berperan secara proaktif dalam upaya pencegahan dan pemberantasan Korupsi, Kolusi, dan Nepotisme (KKN).</li>
                        <li>Tidak akan melakukan praktik KKN serta tidak akan terlibat dalam perbuatan tercela yang dapat merugikan negara.</li>
                        <li>Tidak akan menerima atau memberi hadiah, bantuan, gratifikasi, atau suap dalam bentuk apa pun yang berkaitan dengan pelaksanaan tugas.</li>
                        <li>Melaksanakan tugas dan fungsi jabatan secara profesional, jujur, objektif, transparan, dan akuntabel.</li>
                        <li>Mematuhi seluruh peraturan perundang-undangan, kode etik ASN, serta ketentuan disiplin pegawai.</li>
                        <li>Bersedia menerima sanksi sesuai ketentuan peraturan perundang-undangan apabila melanggar Pakta Integritas ini.</li>
                        <li>Apabila mengetahui adanya indikasi pelanggaran integritas, saya bersedia melaporkan kepada pihak yang berwenang.</li>
                    </ol>

                    <p>Demikian Pakta Integritas ini saya buat dengan sebenar-benarnya, penuh kesadaran, dan tanpa paksaan dari pihak mana pun, untuk dilaksanakan dengan sungguh-sungguh.</p>
                </div>

                <table class="signature-table">
                    <tr>
                        <td style="width: 50%;">
                            Mengetahui,<br>
                            Direktur Politeknik<br>
                            Keselamatan Transportasi Jalan
                        </td>
                        <td style="width: 50%;">
                            Tegal, <?= esc($tanggal) ?><br>
                            Yang Membuat Pernyataan
                        </td>
                    </tr>
                    <tr>
                        <td class="ttd-space"></td>
                        
                        <td class="ttd-space">
                            <div class="materai-box">
                                Materai
                            </div>
                        </td>
                    </tr>
                    <tr>
                        <td>
                            <span style="text-decoration: underline; font-weight: bold;"><?= esc(format_nama_gelar($direktur['nama_lengkap'])) ?></span><br>
                            NIP. <?= esc($direktur['nip']) ?>
                        </td>
                        <td>
                            <span style="text-decoration: underline; font-weight: bold;"><?= esc(format_nama_gelar($user['nama_lengkap'])) ?></span><br>
                            NIP. <?= esc($user['nip']) ?>
                        </td>
                    </tr>
                </table>
            
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
            filename:     'Pakta_Integritas_' + tahun + '_' + safeNama + '.pdf',
            image:        { type: 'jpeg', quality: 0.98 },
            html2canvas:  { 
                scale: 2, 
                useCORS: true, 
                scrollY: 0,
                windowHeight: 1123 
            }, 
            jsPDF:        { unit: 'mm', format: 'a4', orientation: 'portrait' }
        };
        html2pdf().set(opt).from(element).save();
    }
</script>

<?= $this->endSection() ?>