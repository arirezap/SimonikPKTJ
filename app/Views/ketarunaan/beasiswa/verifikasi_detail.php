<?= $this->extend('layouts/ecc2_layout') ?>

<?= $this->section('title') ?>Periksa Kelayakan Beasiswa<?= $this->endSection() ?>

<?= $this->section('styles') ?>
<?= $this->include('ketarunaan/beasiswa/_styles') ?>
<?= $this->endSection() ?>

<?= $this->section('content') ?>
<?php
$detail     = $detail ?? [];
$p          = $detail['penerima'] ?? [];
$dokumen    = $detail['dokumen'] ?? [];
$checklist  = $detail['checklist'] ?? [];
$riwayat    = $detail['riwayat'] ?? [];
$match3tp   = $detail['wilayah_3tp_match'] ?? null;
$perms      = $perms ?? [];

$id         = (int) ($p['id'] ?? 0);
$jenis      = (string) ($p['jenis_beasiswa'] ?? '');
$canVerify  = !empty($perms['can_verify']) && !in_array((string) ($p['status_penetapan'] ?? ''), ['ditetapkan', 'dibatalkan'], true);
$canDecide  = !empty($perms['can_decide']);
?>
<div class="container-fluid px-0">
    <?= $this->include('ketarunaan/beasiswa/_nav_tabs') ?>

    <div class="d-flex flex-wrap justify-content-between align-items-center gap-2 mb-3">
        <a href="<?= base_url('ketarunaan/beasiswa/verifikasi') ?>" class="btn btn-light border btn-sm rounded-pill px-3 bea-touch-ctrl d-inline-flex align-items-center gap-2">
            <i class="bi bi-arrow-left" aria-hidden="true"></i> Kembali ke Antrean Verifikasi
        </a>
        <div class="d-flex gap-2">
            <a href="<?= base_url('ketarunaan/beasiswa/detail/' . $id) ?>" class="btn btn-outline-primary btn-sm rounded-pill px-3 bea-touch-ctrl d-inline-flex align-items-center">
                Buka Halaman Penetapan &amp; Detail
            </a>
        </div>
    </div>

    <div class="row g-4">
        <!-- Kolom Kiri: Profil Pengajuan, Syarat Khusus Jenis, Lampiran & Riwayat -->
        <div class="col-12 col-lg-7">
            <div class="ecc-bea-card mb-4">
                <div class="d-flex flex-wrap gap-2 mb-2">
                    <?= ketarunaan_jenis_beasiswa_badge($jenis, true) ?>
                    <?= ketarunaan_verifikasi_beasiswa_badge((string) ($p['hasil_verifikasi'] ?? 'belum_diperiksa')) ?>
                    <?= ketarunaan_penetapan_beasiswa_badge((string) ($p['status_penetapan'] ?? 'kandidat')) ?>
                </div>
                <h2 class="h5 fw-bold text-dark mb-1"><?= esc((string) ($p['nama_lengkap'] ?: $p['nomor_taruna'])) ?></h2>
                <div class="ecc-text-muted-aa ecc-num small mb-3">
                    <?= esc((string) $p['nomor_taruna']) ?> &bull; <?= esc((string) ($p['nama_prodi'] ?? '-')) ?> &bull; Kelas <?= esc((string) ($p['nama_kelas'] ?: ($p['kelas_snapshot'] ?? '-'))) ?>
                </div>

                <div class="row g-3 border-top pt-3 small">
                    <div class="col-12 col-sm-6">
                        <div class="ecc-text-muted-aa" style="font-size: 0.75rem;">Program Beasiswa</div>
                        <div class="fw-semibold text-dark"><?= esc((string) ($p['nama_program'] ?? '-')) ?></div>
                    </div>
                    <div class="col-12 col-sm-6">
                        <div class="ecc-text-muted-aa" style="font-size: 0.75rem;">Sumber &amp; Pencatat</div>
                        <div class="fw-semibold text-dark"><?= esc(ucfirst((string) ($p['sumber_klaim'] ?? '-'))) ?> (<?= esc((string) ($p['dicatat_oleh'] ?: '-')) ?>)</div>
                    </div>
                </div>
            </div>

            <!-- Rincian Data Klaim / Pemeringkatan Berdasarkan Jenis -->
            <div class="ecc-bea-card mb-4">
                <h2 class="h6 fw-bold text-dark mb-3">Data Bukti Kelayakan Program</h2>

                <?php if ($jenis === 'apresiasi'): ?>
                    <div class="row g-3 small">
                        <div class="col-4">
                            <div class="ecc-text-muted-aa" style="font-size: 0.75rem;">IP Semester</div>
                            <div class="fw-bold text-primary fs-5 ecc-num"><?= number_format((float) ($p['ip_semester'] ?? 0), 2, ',', '.') ?></div>
                        </div>
                        <div class="col-4">
                            <div class="ecc-text-muted-aa" style="font-size: 0.75rem;">Peringkat Kelas</div>
                            <div class="fw-bold fs-5 ecc-num">#<?= (int) ($p['peringkat_kelas'] ?? 0) ?></div>
                        </div>
                        <div class="col-4">
                            <div class="ecc-text-muted-aa" style="font-size: 0.75rem;">Kelas Acuan</div>
                            <div class="fw-semibold"><?= esc((string) ($p['kelas_snapshot'] ?: '-')) ?></div>
                        </div>
                    </div>
                <?php elseif ($jenis === 'prestasi'): ?>
                    <div class="row g-3 small">
                        <div class="col-12 col-md-8">
                            <div class="ecc-text-muted-aa" style="font-size: 0.75rem;">Nama Prestasi</div>
                            <div class="fw-semibold"><?= esc((string) ($p['nama_prestasi'] ?: '-')) ?></div>
                        </div>
                        <div class="col-12 col-md-4">
                            <div class="ecc-text-muted-aa" style="font-size: 0.75rem;">Peringkat &amp; Tingkat</div>
                            <div class="fw-semibold"><?= esc((string) ($p['peringkat_prestasi'] ?: '-')) ?> (<?= esc((string) ($p['tingkat_prestasi'] ?: '-')) ?>)</div>
                        </div>
                        <div class="col-6">
                            <div class="ecc-text-muted-aa" style="font-size: 0.75rem;">Penyelenggara</div>
                            <div><?= esc((string) ($p['penyelenggara_prestasi'] ?: '-')) ?></div>
                        </div>
                        <div class="col-6">
                            <div class="ecc-text-muted-aa" style="font-size: 0.75rem;">Tautan Menu Prestasi</div>
                            <div><?= !empty($p['prestasi_id']) ? ('Terhubung ke ID #' . (int) $p['prestasi_id']) : 'Input langsung saat klaim' ?></div>
                        </div>
                    </div>
                <?php elseif ($jenis === 'daerah_3tp'): ?>
                    <div class="mb-3">
                        <?php if ($match3tp): ?>
                            <div class="alert alert-success border-0 rounded-4 p-3 mb-2 small">
                                <strong>Terdaftar pada Acuan Resmi 3TP:</strong> <?= esc($match3tp['kabupaten_kota'] . ' (' . $match3tp['kode_wilayah'] . ')') ?> &bull; <?= esc($match3tp['dasar_acuan']) ?>
                            </div>
                        <?php else: ?>
                            <div class="alert alert-warning border-warning rounded-4 p-3 mb-2 small">
                                <strong>Wilayah Belum Terverifikasi pada Acuan 3TP:</strong> Sistem menahan status Memenuhi apabila kabupaten/kota domisili tidak tercantum dalam daftar acuan resmi 3TP aktif.
                            </div>
                        <?php endif; ?>
                    </div>
                    <div class="row g-2 small">
                        <div class="col-12"><strong>Alamat:</strong> <?= esc((string) ($p['alamat_domisili'] ?: '-')) ?></div>
                        <div class="col-6"><strong>Wilayah:</strong> <?= esc(trim(($p['kabupaten_kota'] ?? '') . ', ' . ($p['provinsi'] ?? ''), ', ')) ?></div>
                        <div class="col-6 ecc-num"><strong>Kode Wilayah:</strong> <?= esc((string) ($p['kode_wilayah'] ?: '-')) ?></div>
                    </div>
                <?php else: ?>
                    <?php if (!empty($p['economy_masked'])): ?>
                        <div class="alert alert-secondary border-0 rounded-4 p-3 mb-0 small">
                            Rincian kondisi ekonomi dan penghasilan keluarga dikunci untuk peran Anda.
                        </div>
                    <?php else: ?>
                        <div class="row g-3 small">
                            <div class="col-12 col-md-4">
                                <div class="ecc-text-muted-aa" style="font-size: 0.75rem;">Pekerjaan Orang Tua</div>
                                <div class="fw-semibold"><?= esc((string) ($p['pekerjaan_orang_tua'] ?: '-')) ?></div>
                            </div>
                            <div class="col-6 col-md-4">
                                <div class="ecc-text-muted-aa" style="font-size: 0.75rem;">Penghasilan / Bulan</div>
                                <div class="fw-semibold ecc-num"><?= esc(ketarunaan_rupiah($p['penghasilan_bulanan'] ?? 0)) ?></div>
                            </div>
                            <div class="col-6 col-md-4">
                                <div class="ecc-text-muted-aa" style="font-size: 0.75rem;">Tanggungan</div>
                                <div class="fw-semibold ecc-num"><?= (int) ($p['jumlah_tanggungan'] ?? 0) ?> orang</div>
                            </div>
                            <div class="col-12">
                                <div class="ecc-text-muted-aa" style="font-size: 0.75rem;">Uraian Kondisi Ekonomi</div>
                                <div><?= esc((string) ($p['uraian_pengajuan'] ?: '-')) ?></div>
                            </div>
                        </div>
                    <?php endif; ?>
                <?php endif; ?>
            </div>

            <!-- Lampiran Dokumen -->
            <div class="ecc-bea-card mb-4">
                <h2 class="h6 fw-bold text-dark mb-3">Lampiran Berkas Pengajuan</h2>
                <?php if (empty($dokumen)): ?>
                    <div class="ecc-text-muted-aa small">Tidak ada lampiran berkas khusus pada jalur ini.</div>
                <?php else: ?>
                    <div class="table-responsive d-none d-md-block">
                        <table class="table ecc-table-bea mb-0">
                            <thead>
                                <tr>
                                    <th>Kategori</th>
                                    <th>Keterangan</th>
                                    <th class="text-end">Tautan / Berkas</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($dokumen as $dok): ?>
                                    <tr>
                                        <td class="fw-semibold"><?= esc((string) $dok['kategori']) ?></td>
                                        <td>
                                            <div><?= esc((string) ($dok['keterangan'] ?: '-')) ?></div>
                                            <div class="ecc-text-muted-aa" style="font-size: 0.75rem;"><?= esc((string) ($dok['display_label'] ?? $dok['nama_berkas'])) ?></div>
                                        </td>
                                        <td class="text-end text-nowrap">
                                            <?php if (!empty($dok['can_access'])): ?>
                                                <?php if (!empty($dok['is_url'])): ?>
                                                    <a href="<?= esc((string) $dok['preview_url']) ?>" target="_blank" rel="noopener noreferrer"
                                                       class="btn btn-outline-primary btn-sm rounded-pill px-3"
                                                       aria-label="Buka tautan <?= esc((string) $dok['kategori']) ?>">
                                                        <i class="bi bi-box-arrow-up-right me-1" aria-hidden="true"></i> Buka Tautan
                                                    </a>
                                                <?php else: ?>
                                                    <a href="<?= esc((string) $dok['preview_url']) ?>" target="_blank" rel="noopener noreferrer"
                                                       class="btn btn-light border btn-sm rounded-pill px-3"
                                                       aria-label="Pratinjau <?= esc((string) $dok['kategori']) ?>">Pratinjau</a>
                                                    <a href="<?= esc((string) $dok['download_url']) ?>"
                                                       class="btn btn-outline-primary btn-sm rounded-pill px-3"
                                                       aria-label="Unduh <?= esc((string) $dok['kategori']) ?>">Unduh</a>
                                                <?php endif; ?>
                                            <?php else: ?>
                                                <span class="badge bg-secondary-subtle text-secondary-emphasis border border-secondary-subtle rounded-pill px-3 py-1">Akses Terbatas</span>
                                            <?php endif; ?>
                                        </td>
                                    </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    </div>

                    <!-- Kartu Mobile Lampiran -->
                    <div class="d-md-none d-flex flex-column gap-2">
                        <?php foreach ($dokumen as $dok): ?>
                            <div class="border rounded-3 p-3 bg-white">
                                <div class="fw-bold text-dark small"><?= esc((string) $dok['kategori']) ?></div>
                                <div class="ecc-text-muted-aa small mb-2"><?= esc((string) ($dok['keterangan'] ?: ($dok['display_label'] ?? $dok['nama_berkas']))) ?></div>
                                <?php if (!empty($dok['can_access'])): ?>
                                    <div class="d-flex flex-wrap gap-2">
                                        <?php if (!empty($dok['is_url'])): ?>
                                            <a href="<?= esc((string) $dok['preview_url']) ?>" target="_blank" rel="noopener noreferrer" class="btn btn-outline-primary btn-sm rounded-pill px-3 bea-touch-ctrl d-inline-flex align-items-center gap-1">
                                                <i class="bi bi-box-arrow-up-right" aria-hidden="true"></i> Buka Tautan
                                            </a>
                                        <?php else: ?>
                                            <a href="<?= esc((string) $dok['preview_url']) ?>" target="_blank" rel="noopener noreferrer" class="btn btn-light border btn-sm rounded-pill px-3 bea-touch-ctrl d-inline-flex align-items-center">Pratinjau</a>
                                            <a href="<?= esc((string) $dok['download_url']) ?>" class="btn btn-outline-primary btn-sm rounded-pill px-3 bea-touch-ctrl d-inline-flex align-items-center">Unduh</a>
                                        <?php endif; ?>
                                    </div>
                                <?php else: ?>
                                    <span class="badge bg-secondary-subtle text-secondary-emphasis border border-secondary-subtle rounded-pill px-3 py-1">Akses Terbatas</span>
                                <?php endif; ?>
                            </div>
                        <?php endforeach; ?>
                    </div>
                <?php endif; ?>
            </div>

            <!-- Riwayat Proses (Tanpa Tanggal & Jam Validasi sesuai AGENTS.md §4.L) -->
            <div class="ecc-bea-card">
                <h2 class="h6 fw-bold text-dark mb-3">Riwayat Proses Pengajuan</h2>
                <?php if (empty($riwayat)): ?>
                    <div class="ecc-text-muted-aa small">Belum ada catatan proses.</div>
                <?php else: ?>
                    <div class="d-flex flex-column gap-2">
                        <?php foreach ($riwayat as $rw): ?>
                            <div class="p-3 rounded-3 border bg-light small">
                                <div class="d-flex justify-content-between align-items-center gap-2 mb-1">
                                    <span class="fw-bold text-dark"><?= esc((string) ($rw['aksi'] ?? 'Proses')) ?></span>
                                    <?php if (!empty($rw['status'])): ?>
                                        <span class="badge bg-white text-dark border rounded-pill px-3 py-1" style="font-size: 0.75rem;"><?= esc(ucwords(str_replace('_', ' ', (string) $rw['status']))) ?></span>
                                    <?php endif; ?>
                                </div>
                                <div class="ecc-text-muted-aa">
                                    Pelaku: <strong class="text-dark"><?= esc((string) ($rw['pelaku'] ?? '-')) ?></strong>
                                    <?php if (!empty($rw['catatan'])): ?>
                                        &bull; <?= esc((string) $rw['catatan']) ?>
                                    <?php endif; ?>
                                </div>
                            </div>
                        <?php endforeach; ?>
                    </div>
                <?php endif; ?>
            </div>
        </div>

        <!-- Kolom Kanan: Formulir Keputusan Verifikasi Kelayakan -->
        <div class="col-12 col-lg-5">
            <div class="ecc-bea-card bea-form-scope">
                <h2 class="h6 fw-bold text-dark mb-1">Keputusan Pemeriksaan Kelayakan</h2>
                <p class="ecc-text-muted-aa mb-3 small">
                    Hasil verifikasi dipisahkan dari penetapan penerima. Pengajuan yang dinyatakan Memenuhi akan masuk ke daftar siap Penetapan SK.
                </p>

                <?php if (($p['status_penetapan'] ?? '') === 'ditetapkan'): ?>
                    <div class="alert alert-success border-0 rounded-4 p-3 small">
                        Pengajuan ini sudah ditetapkan menjadi penerima resmi dengan keputusan <strong><?= esc((string) ($p['nomor_keputusan'] ?: '-')) ?></strong>.
                    </div>
                <?php elseif (($p['status_penetapan'] ?? '') === 'dibatalkan'): ?>
                    <div class="alert alert-danger border-0 rounded-4 p-3 small">
                        Pengajuan ini berstatus Dibatalkan dan tidak dapat diperiksa ulang.
                    </div>
                <?php elseif (!$canVerify): ?>
                    <div class="alert alert-secondary border-0 rounded-4 p-3 small">
                        Peran Anda hanya memiliki akses lihat pada halaman verifikasi ini.
                    </div>
                <?php else: ?>
                    <form id="formVerifikasiBeasiswa" method="post" action="<?= base_url('ketarunaan/beasiswa/verifikasi/' . $id . '/simpan') ?>" data-lock-submit="true">
                        <?= csrf_field() ?>

                        <div class="p-3 rounded-3 border bg-light mb-3">
                            <div class="fw-semibold mb-2 small text-dark">Checklist Pemeriksaan</div>
                            <div class="form-check mb-1">
                                <input class="form-check-input" type="checkbox" name="cek_identitas" value="1" id="cek_identitas" <?= !empty($checklist['identitas']) ? 'checked' : '' ?>>
                                <label class="form-check-label small" for="cek_identitas">Identitas &amp; status pendidikan taruna sesuai</label>
                            </div>
                            <div class="form-check mb-1">
                                <input class="form-check-input" type="checkbox" name="cek_kelayakan" value="1" id="cek_kelayakan" <?= !empty($checklist['kelayakan']) ? 'checked' : '' ?>>
                                <label class="form-check-label small" for="cek_kelayakan">Kriteria kelayakan jenis beasiswa terpenuhi</label>
                            </div>
                            <div class="form-check">
                                <input class="form-check-input" type="checkbox" name="cek_dokumen" value="1" id="cek_dokumen" <?= !empty($checklist['dokumen']) ? 'checked' : '' ?>>
                                <label class="form-check-label small" for="cek_dokumen">Tautan dan dokumen bukti lengkap serta terbaca jelas</label>
                            </div>
                        </div>

                        <div class="mb-3">
                            <label for="hasil_verifikasi" class="form-label fw-semibold">Hasil Verifikasi <span class="text-danger">*</span></label>
                            <?php $curVerif = (string) old('hasil_verifikasi', (string) ($p['hasil_verifikasi'] ?? 'belum_diperiksa')); ?>
                            <select id="hasil_verifikasi" name="hasil_verifikasi" class="form-select ecc-control-sm" required>
                                <option value="">-- Pilih Hasil Pemeriksaan --</option>
                                <option value="memenuhi" <?= $curVerif === 'memenuhi' ? 'selected' : '' ?>>Memenuhi Syarat (Lanjut ke Penetapan SK)</option>
                                <option value="perlu_perbaikan" <?= $curVerif === 'perlu_perbaikan' ? 'selected' : '' ?>>Perlu Perbaikan (Kembalikan ke Pemohon)</option>
                                <option value="tidak_memenuhi" <?= $curVerif === 'tidak_memenuhi' ? 'selected' : '' ?>>Tidak Memenuhi Syarat (Tolak Pengajuan)</option>
                            </select>
                        </div>

                        <div class="mb-3">
                            <label for="catatan_pemeriksa" class="form-label fw-semibold">
                                Catatan Pemeriksa <span id="catatanWajibMarker" class="text-danger <?= in_array($curVerif, ['perlu_perbaikan', 'tidak_memenuhi'], true) ? '' : 'd-none' ?>">*</span>
                            </label>
                            <textarea id="catatan_pemeriksa" name="catatan_pemeriksa" rows="3" class="form-control rounded-3"
                                      placeholder="Wajib diisi jika memilih Perlu Perbaikan atau Tidak Memenuhi..."><?= esc((string) old('catatan_pemeriksa', (string) ($p['catatan_pemeriksa'] ?? ''))) ?></textarea>
                        </div>

                        <button type="submit" class="btn btn-primary rounded-pill w-100 btn-tactile bea-touch-ctrl shadow-sm">
                            <i class="bi bi-check-lg me-1" aria-hidden="true"></i> Simpan Hasil Verifikasi
                        </button>
                    </form>
                <?php endif; ?>

                <?php if (($p['hasil_verifikasi'] ?? '') === 'memenuhi' && ($p['status_penetapan'] ?? '') === 'kandidat' && $canDecide): ?>
                    <div class="border-top mt-4 pt-3">
                        <div class="fw-semibold text-success mb-2 small">
                            Pengajuan Lolos Verifikasi &mdash; Siap Ditetapkan
                        </div>
                        <a href="<?= base_url('ketarunaan/beasiswa/detail/' . $id) ?>#panelPenetapanSk" class="btn btn-success rounded-pill w-100 btn-tactile bea-touch-ctrl d-inline-flex align-items-center justify-content-center">
                            Lanjutkan ke Penetapan SK
                        </a>
                    </div>
                <?php endif; ?>
            </div>
        </div>
    </div>
</div>
<?= $this->endSection() ?>

<?= $this->section('scripts') ?>
<?= $this->include('ketarunaan/beasiswa/_scripts_common') ?>
<script>
document.addEventListener('DOMContentLoaded', function() {
    var selHasil = document.getElementById('hasil_verifikasi');
    var catInput = document.getElementById('catatan_pemeriksa');
    var marker   = document.getElementById('catatanWajibMarker');
    var formVer  = document.getElementById('formVerifikasiBeasiswa');

    function syncCatatanRule() {
        if (!selHasil || !catInput) return;
        var needNote = (selHasil.value === 'perlu_perbaikan' || selHasil.value === 'tidak_memenuhi');
        catInput.required = needNote;
        if (marker) {
            marker.classList.toggle('d-none', !needNote);
        }
    }

    if (selHasil) {
        selHasil.addEventListener('change', syncCatatanRule);
        syncCatatanRule();
    }

    if (formVer && selHasil && catInput) {
        formVer.addEventListener('submit', function(e) {
            if (!selHasil.value) {
                e.preventDefault();
                window.eccBeaShowMessage('Hasil Wajib Dipilih', 'Pilih hasil verifikasi kelayakan terlebih dahulu.');
                return;
            }
            if ((selHasil.value === 'perlu_perbaikan' || selHasil.value === 'tidak_memenuhi') && catInput.value.trim() === '') {
                e.preventDefault();
                window.eccBeaShowMessage('Catatan Wajib Diisi', 'Isi catatan pemeriksaan untuk status Perlu Perbaikan atau Tidak Memenuhi.');
            }
        });
    }
});
</script>
<?= $this->endSection() ?>
