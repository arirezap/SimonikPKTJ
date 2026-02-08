<?= $this->extend('layouts/main') ?>

<?= $this->section('page_title') ?>
Detail Sasaran Kinerja Pegawai
<?= $this->endSection() ?>

<?= $this->section('content') ?>

<?php
// Helper Format Nama
function format_nama_skp($nama, $gelar_depan = '', $gelar_belakang = '') {
    $nama_lengkap = strtoupper($nama);
    if($gelar_depan) $nama_lengkap = $gelar_depan . ' ' . $nama_lengkap;
    if($gelar_belakang) $nama_lengkap = $nama_lengkap . ', ' . $gelar_belakang;
    return $nama_lengkap;
}
?>

<div class="container-fluid px-0">

    <div class="text-center mb-4">
        <h5 class="fw-bold text-uppercase mb-1">SASARAN KERJA PEGAWAI</h5>
        <span class="text-muted text-uppercase small" style="letter-spacing: 1px;">PENDEKATAN HASIL KERJA KUANTITATIF</span>
    </div>

    <div class="d-flex flex-wrap gap-2 justify-content-between mb-3">
        <div>
            <button class="btn text-white shadow-sm" style="background-color: #6f42c1;">
                <i class="bi bi-plus-circle me-1"></i> Tambah RHK
            </button>
        </div>
        <div class="d-flex gap-2">
            <button class="btn text-white shadow-sm" style="background-color: #ffc107; color: #000 !important;">
                <i class="bi bi-printer-fill me-1"></i> Cetak
            </button>
            <button class="btn text-white shadow-sm" style="background-color: #dc3545;">
                <i class="bi bi-send-fill me-1"></i> Ajukan SKP
            </button>
        </div>
    </div>

    <div class="d-flex justify-content-between align-items-center mb-3">
        <span class="badge bg-success rounded-pill px-3 py-2">
            <?= strtoupper($header['status']) ?>
        </span>
        <span class="badge bg-secondary rounded-pill px-3 py-2">
            PERIODE SKP : <?= date('M Y', strtotime($header['periode_awal'])) ?> - <?= date('M Y', strtotime($header['periode_akhir'])) ?>
        </span>
    </div>

    <div class="row g-0 mb-4 border rounded shadow-sm overflow-hidden">
        <div class="col-md-6 border-end">
            <div class="bg-light p-2 border-bottom fw-bold small text-muted text-uppercase">
                PEGAWAI YANG DINILAI
            </div>
            <div class="p-3">
                <table class="table table-borderless table-sm mb-0 small">
                    <tr>
                        <td width="130" class="fw-bold text-muted">NAMA</td>
                        <td><?= esc($pegawai['nama_lengkap']) ?></td>
                    </tr>
                    <tr>
                        <td class="fw-bold text-muted">NIP</td>
                        <td><?= esc($pegawai['nip']) ?></td>
                    </tr>
                    <tr>
                        <td class="fw-bold text-muted">JABATAN</td>
                        <td><?= esc($pegawai['jabatan']) ?></td>
                    </tr>
                    <tr>
                        <td class="fw-bold text-muted">PANGKAT/GOL</td>
                        <td><?= esc($pegawai['pangkat'] ?? '-') ?></td>
                    </tr>
                    <tr>
                        <td class="fw-bold text-muted">UNIT KERJA</td>
                        <td><?= esc($pegawai['unit'] ?? '-') ?></td>
                    </tr>
                </table>
            </div>
        </div>

        <div class="col-md-6">
            <div class="bg-light p-2 border-bottom fw-bold small text-muted text-uppercase">
                PEJABAT PENILAI KINERJA
            </div>
            <div class="p-3">
                <table class="table table-borderless table-sm mb-0 small">
                    <tr>
                        <td width="130" class="fw-bold text-muted">NAMA</td>
                        <td><?= $atasan ? esc($atasan['nama_lengkap']) : '<span class="text-danger fst-italic">- Belum Diset -</span>' ?></td>
                    </tr>
                    <tr>
                        <td class="fw-bold text-muted">NIP</td>
                        <td><?= $atasan ? esc($atasan['nip']) : '-' ?></td>
                    </tr>
                    <tr>
                        <td class="fw-bold text-muted">JABATAN</td>
                        <td><?= $atasan ? esc($atasan['jabatan']) : '-' ?></td>
                    </tr>
                    <tr>
                        <td class="fw-bold text-muted">PANGKAT/GOL</td>
                        <td><?= $atasan ? esc($atasan['pangkat'] ?? '-') : '-' ?></td>
                    </tr>
                    <tr>
                        <td class="fw-bold text-muted">UNIT KERJA</td>
                        <td><?= $atasan ? esc($atasan['unit'] ?? '-') : '-' ?></td>
                    </tr>
                </table>
            </div>
        </div>
    </div>

    <div class="card shadow-sm border-0">
        <div class="card-header bg-white py-3">
            <h6 class="m-0 fw-bold text-uppercase small text-muted">HASIL KERJA</h6>
        </div>
        <div class="card-body p-0">
            <table class="table table-bordered mb-0 align-middle" style="font-size: 0.9rem;">
                <thead class="bg-light text-secondary text-uppercase text-center small align-middle">
                    <tr>
                        <th width="50">NO</th>
                        <th width="25%">RENCANA HASIL KERJA PIMPINAN YANG DIINTERVENSI</th>
                        <th width="25%">RENCANA HASIL KERJA</th>
                        <th width="10%">ASPEK</th>
                        <th width="25%">INDIKATOR KINERJA INDIVIDU</th>
                        <th width="10%">TARGET TAHUNAN</th>
                    </tr>
                </thead>
                <tbody>
                    <tr class="bg-light fw-bold text-muted small">
                        <td colspan="6" class="px-3">UTAMA</td>
                    </tr>
                    
                    <?php 
                    $noUtama = 1;
                    $skpUtama = array_filter($targets, function($t) { return $t['jenis'] == 'Utama'; });
                    
                    if(empty($skpUtama)): ?>
                        <tr><td colspan="6" class="text-center py-3 text-muted">Belum ada RHK Utama</td></tr>
                    <?php else: 
                        foreach($skpUtama as $row): ?>
                        <tr>
                            <td class="text-center"><?= $noUtama++ ?></td>
                            <td><?= esc($row['rhk_pimpinan'] ?? '-') ?></td>
                            <td><?= esc($row['rencana_kinerja']) ?></td>
                            <td class="text-center fw-bold text-uppercase small"><?= esc($row['aspek']) ?></td>
                            <td><?= esc($row['indikator']) ?></td>
                            <td class="text-center"><?= esc($row['target']) ?> <?= esc($row['satuan']) ?></td>
                        </tr>
                    <?php endforeach; endif; ?>


                    <tr class="bg-light fw-bold text-muted small">
                        <td colspan="6" class="px-3">TAMBAHAN</td>
                    </tr>

                    <?php 
                    $noTambahan = 1;
                    $skpTambahan = array_filter($targets, function($t) { return $t['jenis'] == 'Tambahan'; });
                    
                    if(empty($skpTambahan)): ?>
                        <tr><td colspan="6" class="text-center py-3 text-muted fst-italic text-small">- Tidak ada RHK Tambahan -</td></tr>
                    <?php else: 
                        foreach($skpTambahan as $row): ?>
                        <tr>
                            <td class="text-center"><?= $noTambahan++ ?></td>
                            <td>-</td> <td><?= esc($row['rencana_kinerja']) ?></td>
                            <td class="text-center fw-bold text-uppercase small"><?= esc($row['aspek']) ?></td>
                            <td><?= esc($row['indikator']) ?></td>
                            <td class="text-center"><?= esc($row['target']) ?> <?= esc($row['satuan']) ?></td>
                        </tr>
                    <?php endforeach; endif; ?>

                </tbody>
            </table>
        </div>
    </div>

    <div class="mb-5"></div>

</div>
<?= $this->endSection() ?>