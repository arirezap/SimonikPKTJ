<?= $this->extend('layouts/main') ?>

<?= $this->section('content') ?>
<script src="https://cdnjs.cloudflare.com/ajax/libs/html2pdf.js/0.10.1/html2pdf.bundle.min.js"></script>





<div class="container-fluid">
    
    <div class="d-flex justify-content-between align-items-center mb-4 btn-print-area">
        <h1 class="h3 mb-0 text-gray-800">Kontrak Kinerja</h1>
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