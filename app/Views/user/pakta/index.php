<?= $this->extend('layouts/main') ?>

<?= $this->section('content') ?>
<script src="https://cdnjs.cloudflare.com/ajax/libs/html2pdf.js/0.10.1/html2pdf.bundle.min.js"></script>





<div class="container-fluid">
    
    <div class="d-flex justify-content-between align-items-center mb-4 btn-print-area">
        <h1 class="h3 mb-0 text-gray-800">Pakta Integritas</h1>
        <div>
            <a href="<?= site_url('dashboard') ?>" class="btn btn-secondary btn-sm me-2">
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
            <div class="paper-container paper-container-pakta" id="element-to-print">
                
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