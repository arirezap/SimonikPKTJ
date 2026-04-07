<?= $this->extend('layouts/main') ?>

<?= $this->section('page_title') ?>
Detail Sasaran Kinerja Pegawai
<?= $this->endSection() ?>

<?= $this->section('content') ?>

<?php
// Helper Format Nama
function format_nama_skp($nama, $gelar_depan = '', $gelar_belakang = '')
{
    $nama_lengkap = strtoupper($nama);
    if ($gelar_depan) $nama_lengkap = $gelar_depan . ' ' . $nama_lengkap;
    if ($gelar_belakang) $nama_lengkap = $nama_lengkap . ', ' . $gelar_belakang;
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
            <button class="btn text-white shadow-sm" style="background-color: #6f42c1;" data-bs-toggle="modal" data-bs-target="#modalTambahRHK">
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

    <div class="modal fade" id="modalTambahRHK" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-lg">
            <form action="<?= site_url('user/skp/target/store') ?>" method="post">
                <?= csrf_field() ?>
                <input type="hidden" name="skp_header_id" value="<?= $header['id'] ?>">

                <div class="modal-content">
                    <div class="modal-header bg-primary text-white">
                        <h5 class="modal-title fw-bold">Tambah Rencana Hasil Kerja</h5>
                        <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body">

                        <div class="row">
                            <div class="col-md-12 mb-3">
                                <label class="form-label fw-bold">Jenis RHK</label>
                                <select name="jenis" class="form-select" required>
                                    <option value="Utama">Utama</option>
                                    <option value="Tambahan">Tambahan</option>
                                </select>
                            </div>
                        </div>

                        <hr>

                        <?php if ($isDirektur): ?>

                            <div class="alert alert-info py-2 small">
                                <i class="bi bi-info-circle me-1"></i> Sebagai Direktur, RHK Anda diambil dari Indikator Kinerja Utama.
                            </div>

                            <div class="mb-3">
                                <label class="form-label fw-bold">Rencana Hasil Kerja (Pilih dari Master Data)</label>
                                <select name="rencana_kinerja_select" class="form-select" required>
                                    <option value="">-- Pilih Indikator Kinerja Utama --</option>

                                    <?php if (!empty($masterIndikator)): ?>
                                        <?php foreach ($masterIndikator as $mi): ?>
                                            <option value="<?= esc($mi['nama_indikator']) ?>">
                                                <?= esc($mi['nama_indikator']) ?>
                                            </option>
                                        <?php endforeach; ?>
                                    <?php else: ?>
                                        <option value="" disabled>Data Indikator Kosong.</option>
                                    <?php endif; ?>

                                </select>
                                <div class="form-text">Data ini berasal dari Menu Admin > Master Data > Indikator Kinerja.</div>
                            </div>

                        <?php else: ?>

                            <div class="mb-3">
                                <label class="form-label fw-bold">RHK Pimpinan yang Diintervensi</label>
                                <textarea name="rhk_pimpinan" class="form-control" rows="2" placeholder="Contoh: Terlaksananya kegiatan akademik..." required></textarea>
                                <div class="form-text">Isi sesuai RHK atasan yang Anda dukung.</div>
                            </div>

                            <div class="mb-3">
                                <label class="form-label fw-bold">Rencana Hasil Kerja (Individu)</label>
                                <textarea name="rencana_kinerja_text" class="form-control" rows="3" placeholder="Uraikan rencana kerja Anda..." required></textarea>
                            </div>

                        <?php endif; ?>

                        <hr>
                        <h6 class="fw-bold text-muted mb-3">INDIKATOR KINERJA INDIVIDU (IKI)</h6>

                        <div class="row">
                            <div class="col-md-4 mb-3">
                                <label class="form-label">Aspek</label>
                                <select name="aspek" class="form-select" required>
                                    <option value="Kuantitas">Kuantitas</option>
                                    <option value="Kualitas">Kualitas</option>
                                    <option value="Waktu">Waktu</option>
                                </select>
                            </div>
                            <div class="col-md-8 mb-3">
                                <label class="form-label">Indikator (Kalimat)</label>
                                <input type="text" name="indikator" class="form-control" placeholder="Cth: Jumlah dokumen laporan..." required>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label class="form-label">Target</label>
                                <input type="text" name="target" class="form-control" placeholder="Cth: 100 / 90%" required>
                            </div>
                            <div class="col-md-6 mb-3">
                                <label class="form-label">Satuan</label>
                                <input type="text" name="satuan" class="form-control" placeholder="Cth: Dokumen / Laporan / Kegiatan" required>
                            </div>
                        </div>

                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                        <button type="submit" class="btn btn-primary">Simpan RHK</button>
                    </div>
                </div>
            </form>
        </div>
    </div>

    <div class="mb-5"></div>

</div>
<?= $this->endSection() ?>