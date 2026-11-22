<?= $this->extend('layouts/ecc2_layout') ?>

<?= $this->section('title') ?>Detail Pengajuan &amp; Penerima Beasiswa<?= $this->endSection() ?>

<?= $this->section('styles') ?>
<?= $this->include('ketarunaan/beasiswa/_styles') ?>
<?= $this->endSection() ?>

<?= $this->section('content') ?>
<?php
$detail       = $detail ?? [];
$p            = $detail['penerima'] ?? [];
$dokumen      = $detail['dokumen'] ?? [];
$riwayat      = $detail['riwayat'] ?? [];
$perms        = $perms ?? [];
$isSelfTaruna = !empty($isSelfTaruna);

$id           = (int) ($p['id'] ?? 0);
$jenis        = (string) ($p['jenis_beasiswa'] ?? '');
$verif        = (string) ($p['hasil_verifikasi'] ?? 'belum_diperiksa');
$tetap        = (string) ($p['status_penetapan'] ?? 'kandidat');
$valid        = (string) ($p['status_validasi'] ?? 'draf');

$canVerify    = !empty($perms['can_verify']);
$canDecide    = !empty($perms['can_decide']);
$canSubmit    = !empty($perms['can_submit_claim']);
$canEdit      = $canSubmit && $tetap === 'kandidat' && in_array($valid, ['draf', 'perlu_perbaikan'], true);
$existingSkRaw = trim((string) ($p['dokumen_keputusan'] ?? ''));
?>
<div class="container-fluid px-0">
    <?= $this->include('ketarunaan/beasiswa/_nav_tabs') ?>

    <div class="d-flex flex-wrap justify-content-between align-items-center gap-2 mb-3">
        <a href="<?= base_url('ketarunaan/beasiswa/penerima') ?>" class="btn btn-light border btn-sm rounded-pill px-3 bea-touch-ctrl d-inline-flex align-items-center gap-2">
            <i class="bi bi-arrow-left" aria-hidden="true"></i> Kembali ke Daftar Penerima
        </a>
        <div class="d-flex flex-wrap gap-2">
            <?php if ($canEdit && $jenis !== 'apresiasi'): ?>
                <a href="<?= base_url('ketarunaan/beasiswa/klaim/ajukan/' . esc($jenis) . '?edit=' . $id) ?>" class="btn btn-light border btn-sm rounded-pill px-3 bea-touch-ctrl d-inline-flex align-items-center">
                    Ubah Pengajuan
                </a>
            <?php endif; ?>
            <?php if ($canVerify && !in_array($tetap, ['ditetapkan', 'dibatalkan'], true) && $valid !== 'draf' && !$isSelfTaruna): ?>
                <a href="<?= base_url('ketarunaan/beasiswa/verifikasi/' . $id) ?>" class="btn btn-outline-primary btn-sm rounded-pill px-3 bea-touch-ctrl d-inline-flex align-items-center">
                    Periksa Kelayakan
                </a>
            <?php endif; ?>
            <?php if ($canSubmit && $valid === 'draf' && $tetap !== 'ditetapkan'): ?>
                <form method="post" action="<?= base_url('ketarunaan/beasiswa/hapus/' . $id) ?>" class="d-inline"
                      onsubmit="return window.eccBeaConfirmSubmit(event, 'Hapus Draf?', 'Draf pengajuan beasiswa ini akan dihapus.', 'Ya, Hapus', '#dc3545');">
                    <?= csrf_field() ?>
                    <button type="submit" class="btn btn-outline-danger btn-sm rounded-pill px-3 bea-touch-ctrl">Hapus Draf</button>
                </form>
            <?php endif; ?>
        </div>
    </div>

    <div class="row g-4">
        <!-- Kolom Kiri: Profil, Detail Jalur, Lampiran, Riwayat -->
        <div class="col-12 col-lg-7">
            <div class="ecc-bea-card mb-4">
                <div class="d-flex flex-wrap gap-2 mb-2">
                    <?= ketarunaan_jenis_beasiswa_badge($jenis, true) ?>
                    <?= ketarunaan_verifikasi_beasiswa_badge($verif) ?>
                    <?= ketarunaan_penetapan_beasiswa_badge($tetap) ?>
                    <?= ketarunaan_validasi_badge($valid) ?>
                </div>
                <h2 class="h5 fw-bold text-dark mb-1"><?= esc((string) ($p['nama_lengkap'] ?: $p['nomor_taruna'])) ?></h2>
                <div class="ecc-text-muted-aa ecc-num small mb-3">
                    <?= esc((string) $p['nomor_taruna']) ?> &bull; <?= esc((string) ($p['nama_prodi'] ?? '-')) ?> &bull; Kelas <?= esc((string) ($p['nama_kelas'] ?: ($p['kelas_snapshot'] ?? '-'))) ?>
                </div>

                <div class="row g-3 border-top pt-3 small">
                    <div class="col-12 col-md-6">
                        <div class="ecc-text-muted-aa" style="font-size: 0.75rem;">Program &amp; Pemberi</div>
                        <div class="fw-semibold text-dark"><?= esc((string) ($p['nama_program'] ?? '-')) ?></div>
                        <div class="ecc-text-muted-aa"><?= esc((string) ($p['pemberi'] ?? '-')) ?></div>
                    </div>
                    <div class="col-6 col-md-3">
                        <div class="ecc-text-muted-aa" style="font-size: 0.75rem;">Nominal</div>
                        <div class="fw-bold text-dark ecc-num"><?= esc(ketarunaan_rupiah($p['nominal'] ?? 0)) ?></div>
                    </div>
                    <div class="col-6 col-md-3">
                        <div class="ecc-text-muted-aa" style="font-size: 0.75rem;">Sumber &amp; Pencatat</div>
                        <div class="fw-semibold"><?= esc(ucfirst((string) ($p['sumber_klaim'] ?? '-'))) ?></div>
                        <div class="ecc-text-muted-aa" style="font-size: 0.75rem;"><?= esc((string) ($p['dicatat_oleh'] ?: '-')) ?></div>
                    </div>
                </div>
            </div>

            <!-- Rincian Jalur Spesifik -->
            <div class="ecc-bea-card mb-4">
                <h2 class="h6 fw-bold text-dark mb-3">Rincian Dasar Pengajuan</h2>
                <?php if ($jenis === 'apresiasi'): ?>
                    <div class="row g-3 small">
                        <div class="col-4">
                            <div class="ecc-text-muted-aa" style="font-size: 0.75rem;">IP Semester</div>
                            <div class="fw-bold fs-5 text-primary ecc-num"><?= number_format((float) ($p['ip_semester'] ?? 0), 2, ',', '.') ?></div>
                        </div>
                        <div class="col-4">
                            <div class="ecc-text-muted-aa" style="font-size: 0.75rem;">Peringkat Kelas</div>
                            <div class="fw-bold fs-5 ecc-num">#<?= (int) ($p['peringkat_kelas'] ?? 0) ?></div>
                        </div>
                        <div class="col-4">
                            <div class="ecc-text-muted-aa" style="font-size: 0.75rem;">Kelas Penilaian</div>
                            <div class="fw-semibold"><?= esc((string) ($p['kelas_snapshot'] ?: '-')) ?></div>
                        </div>
                    </div>
                <?php elseif ($jenis === 'prestasi'): ?>
                    <div class="row g-2 small">
                        <div class="col-12"><strong>Nama Prestasi:</strong> <?= esc((string) ($p['nama_prestasi'] ?: '-')) ?></div>
                        <div class="col-6"><strong>Kategori / Tingkat:</strong> <?= esc((string) (($p['kategori_prestasi'] ?: '-') . ' / ' . ($p['tingkat_prestasi'] ?: '-'))) ?></div>
                        <div class="col-6"><strong>Peringkat:</strong> <?= esc((string) ($p['peringkat_prestasi'] ?: '-')) ?></div>
                        <div class="col-6"><strong>Penyelenggara:</strong> <?= esc((string) ($p['penyelenggara_prestasi'] ?: '-')) ?></div>
                        <div class="col-6 ecc-num"><strong>Tanggal:</strong> <?= esc(ketarunaan_format_tanggal((string) ($p['tanggal_prestasi'] ?: ''), true)) ?></div>
                    </div>
                <?php elseif ($jenis === 'daerah_3tp'): ?>
                    <div class="row g-2 small">
                        <div class="col-12"><strong>Alamat Domisili:</strong> <?= esc((string) ($p['alamat_domisili'] ?: '-')) ?></div>
                        <div class="col-6"><strong>Kabupaten / Provinsi:</strong> <?= esc(trim(($p['kabupaten_kota'] ?? '') . ', ' . ($p['provinsi'] ?? ''), ', ')) ?></div>
                        <div class="col-6 ecc-num"><strong>Kode Wilayah:</strong> <?= esc((string) ($p['kode_wilayah'] ?: '-')) ?></div>
                        <div class="col-12"><strong>Acuan Resmi 3TP:</strong> <?= esc((string) ($p['acuan_wilayah_3tp'] ?: 'Belum tercatat di acuan aktif')) ?></div>
                    </div>
                <?php else: ?>
                    <?php if (!empty($p['economy_masked'])): ?>
                        <div class="alert alert-secondary border-0 rounded-4 p-3 mb-0 small">
                            Data ekonomi dan lampiran pendukung bersifat terbatas dan disembunyikan untuk peran Anda.
                        </div>
                    <?php else: ?>
                        <div class="row g-3 small">
                            <div class="col-4">
                                <div class="ecc-text-muted-aa" style="font-size: 0.75rem;">Pekerjaan Orang Tua</div>
                                <div class="fw-semibold"><?= esc((string) ($p['pekerjaan_orang_tua'] ?: '-')) ?></div>
                            </div>
                            <div class="col-4">
                                <div class="ecc-text-muted-aa" style="font-size: 0.75rem;">Penghasilan / Bulan</div>
                                <div class="fw-semibold ecc-num"><?= esc(ketarunaan_rupiah($p['penghasilan_bulanan'] ?? 0)) ?></div>
                            </div>
                            <div class="col-4">
                                <div class="ecc-text-muted-aa" style="font-size: 0.75rem;">Jumlah Tanggungan</div>
                                <div class="fw-semibold ecc-num"><?= (int) ($p['jumlah_tanggungan'] ?? 0) ?> orang</div>
                            </div>
                            <div class="col-12">
                                <div class="ecc-text-muted-aa" style="font-size: 0.75rem;">Uraian Kondisi</div>
                                <div><?= esc((string) ($p['uraian_pengajuan'] ?: '-')) ?></div>
                            </div>
                        </div>
                    <?php endif; ?>
                <?php endif; ?>
            </div>

            <!-- Dokumen Bukti & Keputusan -->
            <div class="ecc-bea-card mb-4">
                <h2 class="h6 fw-bold text-dark mb-3">Dokumen Bukti &amp; Surat Keputusan</h2>
                <?php if (empty($dokumen)): ?>
                    <div class="ecc-text-muted-aa small">Belum ada tautan atau berkas dokumen.</div>
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

                    <!-- Kartu Mobile Dokumen -->
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
                <h2 class="h6 fw-bold text-dark mb-3">Riwayat Proses &amp; Audit</h2>
                <?php if (empty($riwayat)): ?>
                    <div class="ecc-text-muted-aa small">Belum ada riwayat proses.</div>
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

        <!-- Kolom Kanan: Penetapan SK Resmi & Pembatalan -->
        <div class="col-12 col-lg-5" id="panelPenetapanSk">
            <div class="ecc-bea-card mb-4 bea-form-scope">
                <h2 class="h6 fw-bold text-dark mb-2">Keputusan Penetapan Penerima (SK)</h2>

                <?php if ($tetap === 'ditetapkan'): ?>
                    <div class="p-3 rounded-4 border bg-light mb-3 small">
                        <div class="ecc-text-muted-aa" style="font-size: 0.75rem;">Nomor Keputusan</div>
                        <div class="fw-bold text-dark mb-2 ecc-num"><?= esc((string) ($p['nomor_keputusan'] ?: '-')) ?></div>
                        <div class="row g-2">
                            <div class="col-6">
                                <div class="ecc-text-muted-aa" style="font-size: 0.75rem;">Tanggal Penetapan</div>
                                <div class="fw-semibold ecc-num"><?= esc(ketarunaan_format_tanggal((string) ($p['tanggal_penetapan'] ?: ''), true)) ?></div>
                            </div>
                            <div class="col-6">
                                <div class="ecc-text-muted-aa" style="font-size: 0.75rem;">Pejabat Penetap</div>
                                <div class="fw-semibold"><?= esc((string) ($p['pejabat_penetap'] ?: '-')) ?></div>
                            </div>
                        </div>
                        <?php if (!empty($p['catatan_penetapan'])): ?>
                            <div class="mt-2 pt-2 border-top ecc-text-muted-aa"><?= esc((string) $p['catatan_penetapan']) ?></div>
                        <?php endif; ?>
                    </div>

                    <?php if ($canDecide && $valid !== 'tervalidasi'): ?>
                        <form method="post" action="<?= base_url('ketarunaan/beasiswa/penetapan/' . $id . '/simpan') ?>" class="mb-3"
                              onsubmit="return window.eccBeaConfirmSubmit(event, 'Sahkan Keputusan SK?', 'Penerima akan disahkan dalam rekap resmi beasiswa.', 'Ya, Sahkan', '#198754');">
                            <?= csrf_field() ?>
                            <input type="hidden" name="nomor_keputusan" value="<?= esc((string) ($p['nomor_keputusan'] ?? '')) ?>">
                            <input type="hidden" name="tanggal_penetapan" value="<?= esc((string) ($p['tanggal_penetapan'] ?: date('Y-m-d'))) ?>">
                            <input type="hidden" name="pejabat_penetap" value="<?= esc((string) ($p['pejabat_penetap'] ?? 'Direktur PKTJ')) ?>">
                            <input type="hidden" name="catatan_penetapan" value="<?= esc((string) ($p['catatan_penetapan'] ?? '')) ?>">
                            <input type="hidden" name="aksi_penetapan" value="tetapkan_validasi">
                            <button type="submit" class="btn btn-success rounded-pill w-100 btn-tactile bea-touch-ctrl">
                                Sahkan &amp; Validasi Keputusan Ini
                            </button>
                        </form>
                    <?php endif; ?>

                    <?php if ($canDecide): ?>
                        <div class="border-top pt-3">
                            <div class="fw-semibold text-danger mb-2 small">Pembatalan Keputusan Penerima</div>
                            <form id="formBatalPenetapan" method="post" action="<?= base_url('ketarunaan/beasiswa/penetapan/' . $id . '/batal') ?>"
                                  onsubmit="return validateCancelPenetapan(event);">
                                <?= csrf_field() ?>
                                <div class="mb-2">
                                    <label for="alasan_pembatalan" class="form-label small fw-semibold visually-hidden">Alasan Pembatalan</label>
                                    <input type="text" id="alasan_pembatalan" name="alasan_pembatalan" class="form-control ecc-control-sm" required placeholder="Alasan resmi pembatalan penetapan...">
                                </div>
                                <button type="submit" class="btn btn-outline-danger btn-sm rounded-pill w-100 bea-touch-ctrl">
                                    Batalkan Penetapan Beasiswa
                                </button>
                            </form>
                        </div>
                    <?php endif; ?>

                <?php elseif ($tetap === 'dibatalkan'): ?>
                    <div class="alert alert-danger border-0 rounded-4 p-3 mb-0 small">
                        <strong>Dibatalkan:</strong> <?= esc((string) ($p['catatan_penetapan'] ?: $p['catatan_pemeriksa'] ?: 'Tidak ditetapkan sebagai penerima.')) ?>
                    </div>

                <?php elseif ($verif !== 'memenuhi'): ?>
                    <div class="alert alert-warning border-warning rounded-4 p-3 mb-0 small">
                        Penetapan penerima terkunci sampai pengajuan dinyatakan <strong>Memenuhi</strong> pada tahap Verifikasi Kelayakan.
                    </div>

                <?php elseif (!$canDecide): ?>
                    <div class="alert alert-secondary border-0 rounded-4 p-3 mb-0 small">
                        Pengajuan lolos verifikasi dan menunggu penetapan SK oleh pejabat berwenang.
                    </div>

                <?php else: ?>
                    <form id="formPenetapanSk" method="post" action="<?= base_url('ketarunaan/beasiswa/penetapan/' . $id . '/simpan') ?>"
                          onsubmit="return validatePenetapanSk(event);">
                        <?= csrf_field() ?>
                        <div class="mb-3">
                            <label for="nomor_keputusan" class="form-label fw-semibold">Nomor Keputusan (SK) <span class="text-danger">*</span></label>
                            <input type="text" id="nomor_keputusan" name="nomor_keputusan" class="form-control ecc-control-sm" required maxlength="100"
                                   value="<?= esc((string) old('nomor_keputusan', '')) ?>" placeholder="Contoh: SK.BEASISWA/2026/01">
                        </div>
                        <div class="mb-3">
                            <label for="tanggal_penetapan" class="form-label fw-semibold">Tanggal Penetapan <span class="text-danger">*</span></label>
                            <input type="text" id="tanggal_penetapan" name="tanggal_penetapan" class="form-control ecc-control-sm ecc-num bg-white js-bea-date" required
                                   value="<?= esc((string) old('tanggal_penetapan', date('Y-m-d'))) ?>">
                        </div>
                        <div class="mb-3">
                            <label for="pejabat_penetap" class="form-label fw-semibold">Pejabat Penetap <span class="text-danger">*</span></label>
                            <input type="text" id="pejabat_penetap" name="pejabat_penetap" class="form-control ecc-control-sm" required maxlength="120"
                                   value="<?= esc((string) old('pejabat_penetap', 'Direktur PKTJ')) ?>">
                        </div>
                        <div class="mb-3">
                            <label for="dokumen_keputusan_link" class="form-label fw-semibold">Tautan Berkas SK (Google Drive)</label>
                            <input type="url" id="dokumen_keputusan_link" name="dokumen_keputusan_link" class="form-control ecc-control-sm" maxlength="255"
                                   placeholder="https://drive.google.com/file/d/.../view"
                                   value="<?= esc((string) old('dokumen_keputusan_link', ketarunaan_is_http_url($existingSkRaw, 2000) ? $existingSkRaw : '')) ?>">
                        </div>
                        <div class="mb-3">
                            <label for="catatan_penetapan" class="form-label fw-semibold">Catatan Keputusan</label>
                            <textarea id="catatan_penetapan" name="catatan_penetapan" rows="2" class="form-control rounded-3"><?= esc((string) old('catatan_penetapan', '')) ?></textarea>
                        </div>
                        <input type="hidden" name="aksi_penetapan" value="tetapkan_validasi">
                        <button type="submit" class="btn btn-success rounded-pill w-100 btn-tactile bea-touch-ctrl shadow-sm">
                            <i class="bi bi-check-lg me-1" aria-hidden="true"></i> Tetapkan &amp; Sahkan Penerima
                        </button>
                    </form>
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
    if (typeof flatpickr !== 'undefined') {
        var fpLocale = (flatpickr.l10ns && flatpickr.l10ns.id) ? 'id' : 'default';
        document.querySelectorAll('.js-bea-date').forEach(function(el) {
            flatpickr(el, {
                locale: fpLocale,
                dateFormat: 'Y-m-d',
                altInput: true,
                altFormat: 'd F Y',
                allowInput: true
            });
        });
    }
});

function validatePenetapanSk(event) {
    event.preventDefault();
    var noSk   = (document.getElementById('nomor_keputusan')?.value || '').trim();
    var tglSk  = (document.getElementById('tanggal_penetapan')?.value || '').trim();
    var pjSk   = (document.getElementById('pejabat_penetap')?.value || '').trim();
    var linkSk = (document.getElementById('dokumen_keputusan_link')?.value || '').trim();

    if (noSk === '' || tglSk === '' || pjSk === '') {
        window.eccBeaShowMessage('Data Belum Lengkap', 'Nomor keputusan, tanggal penetapan, dan pejabat penetap wajib diisi.');
        return false;
    }
    if (linkSk !== '' && (!/^https?:\/\/\S+/i.test(linkSk) || linkSk.length > 255)) {
        window.eccBeaShowMessage('Tautan SK Tidak Valid', 'Tautan berkas SK harus diawali http:// atau https:// (maksimal 255 karakter).');
        return false;
    }
    return window.eccBeaConfirmSubmit(event, 'Tetapkan & Sahkan Penerima?', 'Taruna akan ditetapkan secara resmi sebagai penerima beasiswa berdasarkan SK ini.', 'Ya, Tetapkan & Sahkan', '#198754');
}

function validateCancelPenetapan(event) {
    event.preventDefault();
    var alasan = (document.getElementById('alasan_pembatalan')?.value || '').trim();
    if (alasan === '') {
        window.eccBeaShowMessage('Alasan Wajib Diisi', 'Isi alasan resmi pembatalan penetapan terlebih dahulu.');
        return false;
    }
    return window.eccBeaConfirmSubmit(event, 'Batalkan Penetapan?', 'Penerima akan dikeluarkan dari daftar penerima resmi.', 'Ya, Batalkan', '#dc3545');
}
</script>
<?= $this->endSection() ?>
