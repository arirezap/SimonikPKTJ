<?= $this->extend('layouts/ecc2_layout') ?>

<?= $this->section('title') ?>Pemeriksaan Kasus<?= $this->endSection() ?>

<?= $this->section('styles') ?>
<link rel="stylesheet" href="<?= base_url('assets/vendor/select2/select2.min.css') ?>">
<link rel="stylesheet" href="<?= base_url('assets/vendor/select2/select2-bootstrap-5-theme.min.css') ?>">
<?= $this->include('ketarunaan/pelanggaran/_styles') ?>
<?= $this->endSection() ?>

<?= $this->section('content') ?>
<?php
$detail      = $detail ?? [];
$kasus       = $detail['kasus'] ?? [];
$pihak       = $detail['pihak'] ?? [];
$refs        = $refs ?? [];
$tarunaList  = $tarunaList ?? [];
$canViewFull = !empty($canViewFull);
$idKasus     = (int) ($kasus['id'] ?? 0);

$bapRaw       = trim((string) ($kasus['berkas_bap'] ?? ''));
$hasBapUrl    = ($bapRaw !== '' && ketarunaan_is_http_url($bapRaw, 2000));
$hasBapLegacy = ($bapRaw !== '' && !$hasBapUrl);
$valBapUrl    = (string) old('berkas_bap', $hasBapUrl ? $bapRaw : '');
$existingNo   = array_values(array_filter(array_column($pihak, 'nomor_taruna')));
?>

<div class="container-fluid px-0">
    <?= view('ketarunaan/pelanggaran/_wizard_header', [
        'activeStep'   => 2,
        'pageTitle'    => 'Pemeriksaan Kasus',
        'pageSubtitle' => 'Mencatat klarifikasi, dasar peraturan, tautan berita acara, dan pihak terkait kasus.',
        'kasus'        => $kasus,
    ]) ?>

    <!-- Kontainer Form Detail Pemeriksaan -->
    <div class="bento-card ecc-pl-card mb-4 pl-form-scope">
        <h2 class="h6 fw-bold text-dark mb-1">Detail Pemeriksaan</h2>
        <p class="text-secondary mb-3 small">Catat hasil pemeriksaan, klarifikasi pihak terkait, dan tautan berita acara pemeriksaan (BAP).</p>

        <form id="formPemeriksaanKasus" method="post" action="<?= base_url('ketarunaan/pelanggaran/pemeriksaan/' . $idKasus . '/simpan') ?>">
            <?= csrf_field() ?>

            <div class="row g-3">
                <div class="col-12 col-md-4">
                    <label for="inputTglPeriksa" class="form-label fw-semibold text-dark small">
                        Tanggal Pemeriksaan <span class="text-danger">*</span>
                    </label>
                    <input
                        type="text"
                        id="inputTglPeriksa"
                        name="tanggal_pemeriksaan"
                        class="form-control ecc-control-sm ecc-flatpickr ecc-num bg-white"
                        value="<?= esc(old('tanggal_pemeriksaan', (string) ($kasus['tanggal_pemeriksaan'] ?: date('Y-m-d')))) ?>"
                        placeholder="Pilih tanggal pemeriksaan"
                    >
                </div>

                <div class="col-12 col-md-4">
                    <label for="inputPemeriksa" class="form-label fw-semibold text-dark small">
                        Pemeriksa <span class="text-danger">*</span>
                    </label>
                    <select id="inputPemeriksa" name="pemeriksa" class="form-select ecc-control-sm" required>
                        <?php $pemVal = old('pemeriksa', (string) ($kasus['pemeriksa'] ?: 'Tim Pemeriksa Unit')); ?>
                        <?php foreach ($refs['pemeriksa_options'] ?? [] as $opt): ?>
                            <option value="<?= esc($opt) ?>" <?= $pemVal === $opt ? 'selected' : '' ?>><?= esc($opt) ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>

                <div class="col-12 col-md-4">
                    <label for="inputTingkatPelanggaran" class="form-label fw-semibold text-dark small">
                        Tingkat Pelanggaran <span class="text-danger">*</span>
                    </label>
                    <select id="inputTingkatPelanggaran" name="tingkat_pelanggaran" class="form-select ecc-control-sm" required>
                        <?php $tktVal = old('tingkat_pelanggaran', (string) ($kasus['tingkat_pelanggaran'] ?: 'belum_ditetapkan')); ?>
                        <?php foreach ($refs['tingkat_catalog'] ?? [] as $k => $lbl): ?>
                            <option value="<?= esc($k) ?>" <?= $tktVal === $k ? 'selected' : '' ?>><?= esc($lbl) ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>

                <div class="col-12 col-md-6">
                    <label for="inputDasarPeraturan" class="form-label fw-semibold text-dark small">
                        Dasar Peraturan <span class="text-danger">*</span>
                    </label>
                    <select id="inputDasarPeraturan" name="dasar_peraturan" class="form-select ecc-control-sm" required>
                        <?php $dasarVal = old('dasar_peraturan', (string) ($kasus['dasar_peraturan'] ?: 'Pedoman Tata Tertib Taruna PKTJ')); ?>
                        <?php foreach ($refs['dasar_peraturan_options'] ?? [] as $opt): ?>
                            <option value="<?= esc($opt) ?>" <?= $dasarVal === $opt ? 'selected' : '' ?>><?= esc($opt) ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>

                <div class="col-12 col-md-6">
                    <label for="inputBerkasBap" class="form-label fw-semibold text-dark small">
                        Tautan BAP Pemeriksaan (Google Drive)
                    </label>
                    <div class="input-group">
                        <span class="input-group-text bg-white border-secondary-subtle text-secondary">
                            <i class="bi bi-link-45deg" aria-hidden="true"></i>
                        </span>
                        <input
                            type="url"
                            id="inputBerkasBap"
                            name="berkas_bap"
                            class="form-control ecc-control-sm"
                            value="<?= esc($valBapUrl) ?>"
                            placeholder="https://drive.google.com/file/d/.../view"
                            maxlength="255"
                            autocomplete="off"
                        >
                    </div>
                    <div class="form-text" style="font-size: 0.75rem;">
                        Opsional. Awali http:// atau https:// (maks. 255 karakter).
                        <?php if ($bapRaw !== ''): ?>
                            Tersimpan: <strong><?= esc(ketarunaan_format_bukti_label($bapRaw)) ?></strong>
                            <?php if ($canViewFull): ?>
                                (<a href="<?= esc($hasBapUrl ? $bapRaw : base_url('ketarunaan/pelanggaran/unduh-dokumen/' . $idKasus . '?file=' . rawurlencode(basename($bapRaw)) . '&inline=1')) ?>" target="_blank" rel="noopener noreferrer"><?= $hasBapUrl ? 'Buka' : 'Pratinjau' ?></a>)
                            <?php endif; ?>
                            <?php if ($hasBapLegacy): ?>
                                &mdash; isi tautan baru di atas untuk mengganti.
                            <?php endif; ?>
                        <?php endif; ?>
                    </div>
                </div>

                <div class="col-12 col-md-6">
                    <label for="inputKlarifikasi" class="form-label fw-semibold text-dark small">
                        Ringkasan Klarifikasi <span class="text-danger">*</span>
                    </label>
                    <textarea
                        id="inputKlarifikasi"
                        name="ringkasan_klarifikasi"
                        class="form-control rounded-3"
                        rows="4"
                        maxlength="1000"
                        placeholder="Ringkasan keterangan pihak terkait dan bukti yang diperiksa..."
                        required
                    ><?= esc(old('ringkasan_klarifikasi', (string) ($kasus['ringkasan_klarifikasi'] ?? ''))) ?></textarea>
                </div>

                <div class="col-12 col-md-6">
                    <label for="inputHasilPeriksa" class="form-label fw-semibold text-dark small">
                        Hasil Pemeriksaan <span class="text-danger">*</span>
                    </label>
                    <textarea
                        id="inputHasilPeriksa"
                        name="hasil_pemeriksaan"
                        class="form-control rounded-3"
                        rows="4"
                        maxlength="1000"
                        placeholder="Hasil pemeriksaan sementara atau kesimpulan tim pemeriksa..."
                        required
                    ><?= esc(old('hasil_pemeriksaan', (string) ($kasus['hasil_pemeriksaan'] ?? ''))) ?></textarea>
                </div>
            </div>

            <div class="mt-3 px-3 py-2 rounded-3 small" style="background-color: #eff6ff; border: 1px solid #bfdbfe; color: #1e3a8a;">
                Tingkat dan sanksi ditetapkan setelah pemeriksaan sesuai kewenangan yang berlaku. Pencatatan kasus bukan merupakan penetapan sanksi.
            </div>

            <div class="d-flex flex-wrap justify-content-between align-items-center gap-2 pt-3 mt-4 border-top">
                <a href="<?= base_url('ketarunaan/pelanggaran/edit/' . $idKasus) ?>" class="btn btn-light border ecc-control-sm px-3 fw-semibold d-inline-flex align-items-center justify-content-center gap-2 btn-tactile pl-touch-ctrl">
                    <i class="bi bi-arrow-left" aria-hidden="true"></i> Sebelumnya: Pencatatan
                </a>
                <div class="d-flex flex-wrap gap-2">
                    <button type="submit" name="aksi_submit" value="draf" class="btn btn-outline-secondary ecc-control-sm px-3 fw-semibold btn-tactile pl-touch-ctrl js-submit-pl2">Simpan Draf</button>
                    <button type="submit" name="aksi_submit" value="next" class="btn btn-primary ecc-control-sm px-3 fw-semibold d-inline-flex align-items-center justify-content-center gap-2 btn-tactile pl-touch-ctrl js-submit-pl2">
                        Berikutnya: Penetapan <i class="bi bi-arrow-right" aria-hidden="true"></i>
                    </button>
                </div>
            </div>
        </form>
    </div>

    <!-- Kontainer Pihak Terkait Kasus -->
    <div class="bento-card ecc-pl-card pl-form-scope">
        <div class="d-flex flex-column flex-md-row justify-content-between align-items-md-center gap-2 mb-3">
            <div>
                <h2 class="h6 fw-bold text-dark mb-1 ecc-num">Pihak Terkait Kasus (<?= count($pihak) ?>)</h2>
                <p class="text-secondary mb-0 small">Catat taruna terkait atau pihak eksternal tanpa memaksakan Nomor Taruna.</p>
            </div>
        </div>

        <form id="formTambahPihak" method="post" action="<?= base_url('ketarunaan/pelanggaran/pemeriksaan/' . $idKasus . '/pihak/tambah') ?>" class="border rounded-3 p-3 bg-light mb-4">
            <?= csrf_field() ?>
            <div class="row g-2 align-items-end">
                <div class="col-12 col-md-2">
                    <label for="selectJenisPihak" class="form-label text-secondary fw-semibold mb-1" style="font-size: 0.75rem;">Jenis Pihak</label>
                    <select id="selectJenisPihak" name="jenis_pihak" class="form-select ecc-control-sm">
                        <option value="taruna">Taruna</option>
                        <option value="eksternal">Pihak Eksternal</option>
                    </select>
                </div>

                <div class="col-12 col-md-4" id="blokPihakTaruna">
                    <label for="selectPihakTaruna" class="form-label text-secondary fw-semibold mb-1" style="font-size: 0.75rem;">Pilih Taruna</label>
                    <select id="selectPihakTaruna" name="nomor_taruna" class="form-select ecc-control-sm ecc-num">
                        <option value="">-- Pilih Taruna --</option>
                        <?php foreach ($tarunaList as $idx => $tr): ?>
                            <?php
                            $noTr     = (string) $tr['nomor_taruna'];
                            $sudahAda = in_array($noTr, $existingNo, true);
                            $dispNo   = ketarunaan_mask_nomor_taruna($noTr, $canViewFull);
                            $dispNm   = $canViewFull ? (string) $tr['nama_lengkap'] : ('Taruna #' . ($idx + 1));
                            $angTr    = !empty($tr['angkatan'])
                                ? ketarunaan_format_angkatan([
                                    'tahun_masuk' => $tr['angkatan'],
                                    'kode_romawi' => $tr['angkatan_romawi'] ?? '',
                                ])
                                : '-';
                            ?>
                            <option value="<?= esc($noTr) ?>"<?= $sudahAda ? ' disabled' : '' ?>>
                                <?= esc($dispNo . ' - ' . $dispNm . ' (' . ($tr['kode_prodi'] ?? '-') . ' / ' . $angTr . ')') ?><?= $sudahAda ? ' [Sudah Terdaftar]' : '' ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>

                <div class="col-12 col-md-4 d-none" id="blokPihakEksternal">
                    <label for="inputNamaEksternal" class="form-label text-secondary fw-semibold mb-1" style="font-size: 0.75rem;">Nama Pihak Eksternal</label>
                    <input type="text" id="inputNamaEksternal" name="nama_pihak" class="form-control ecc-control-sm" placeholder="Misal: Petugas Piket / Saksi Luar" maxlength="150">
                </div>

                <div class="col-6 col-md-2">
                    <label for="selectPeranPihak" class="form-label text-secondary fw-semibold mb-1" style="font-size: 0.75rem;">Peran</label>
                    <select id="selectPeranPihak" name="peran" class="form-select ecc-control-sm">
                        <?php foreach ($refs['peran_pihak_options'] ?? [] as $prOpt): ?>
                            <option value="<?= esc($prOpt) ?>"><?= esc($prOpt) ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>

                <div class="col-6 col-md-2">
                    <label for="inputKetPihak" class="form-label text-secondary fw-semibold mb-1" style="font-size: 0.75rem;">Keterangan</label>
                    <input type="text" id="inputKetPihak" name="keterangan" class="form-control ecc-control-sm" placeholder="Opsional" maxlength="255">
                </div>

                <div class="col-12 col-md-2 d-grid">
                    <button type="submit" id="btnTambahPihak" class="btn btn-primary ecc-control-sm fw-semibold btn-tactile pl-touch-ctrl">Tambah Pihak</button>
                </div>
            </div>
        </form>

        <!-- Tabel Pihak Desktop -->
        <div class="table-responsive ecc-pl-scroll d-none d-md-block">
            <table class="table table-hover ecc-table-pl mb-0" data-sortable-table="true">
                <thead>
                    <tr>
                        <th aria-sort="none">
                            <button type="button" class="pl-sort-btn" data-sort-col="0">
                                <span>Nama Pihak</span><i class="bi bi-arrow-down-up text-muted opacity-50 sort-indicator" aria-hidden="true"></i>
                            </button>
                        </th>
                        <th aria-sort="none">
                            <button type="button" class="pl-sort-btn" data-sort-col="1">
                                <span>Nomor Taruna</span><i class="bi bi-arrow-down-up text-muted opacity-50 sort-indicator" aria-hidden="true"></i>
                            </button>
                        </th>
                        <th aria-sort="none">
                            <button type="button" class="pl-sort-btn" data-sort-col="2">
                                <span>Jenis</span><i class="bi bi-arrow-down-up text-muted opacity-50 sort-indicator" aria-hidden="true"></i>
                            </button>
                        </th>
                        <th aria-sort="none">
                            <button type="button" class="pl-sort-btn" data-sort-col="3">
                                <span>Peran</span><i class="bi bi-arrow-down-up text-muted opacity-50 sort-indicator" aria-hidden="true"></i>
                            </button>
                        </th>
                        <th>Keterangan</th>
                        <th class="text-end">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (empty($pihak)): ?>
                        <tr class="pl-empty-row">
                            <td colspan="6" class="text-center text-secondary py-4">Belum ada pihak tercatat pada kasus ini.</td>
                        </tr>
                    <?php else: ?>
                        <?php foreach ($pihak as $p): ?>
                            <?php $isUtama = (($p['nomor_taruna'] ?? '') === ($kasus['nomor_taruna'] ?? '') && ($p['peran'] ?? '') === 'Terlapor'); ?>
                            <tr>
                                <td class="fw-semibold text-dark" data-sort-val="<?= esc(strtolower((string) $p['display_nama'])) ?>"><?= esc((string) $p['display_nama']) ?></td>
                                <td class="ecc-num" data-sort-val="<?= esc((string) $p['display_nomor']) ?>"><?= esc((string) $p['display_nomor']) ?></td>
                                <td data-sort-val="<?= ($p['jenis_pihak'] ?? 'taruna') === 'eksternal' ? 'eksternal' : 'taruna' ?>"><?= ($p['jenis_pihak'] ?? 'taruna') === 'eksternal' ? 'Eksternal' : 'Taruna' ?></td>
                                <td data-sort-val="<?= esc(strtolower((string) $p['peran'])) ?>"><span class="badge bg-light text-dark border rounded-pill px-3 py-1" style="font-size: 0.75rem;"><?= esc((string) $p['peran']) ?></span></td>
                                <td><?= esc((string) ($p['keterangan'] ?: '-')) ?></td>
                                <td class="text-end text-nowrap">
                                    <?php if (!$isUtama): ?>
                                        <form method="post" action="<?= base_url('ketarunaan/pelanggaran/pemeriksaan/' . $idKasus . '/pihak/hapus/' . (int) $p['id']) ?>" class="d-inline form-hapus-pihak">
                                            <?= csrf_field() ?>
                                            <button type="submit" class="btn btn-outline-danger btn-sm rounded-3 px-3 btn-tactile" aria-label="Hapus pihak <?= esc((string) $p['display_nama']) ?>" data-nama="<?= esc((string) $p['display_nama']) ?>">Hapus</button>
                                        </form>
                                    <?php else: ?>
                                        <span class="badge bg-light text-secondary border rounded-pill px-3 py-1" style="font-size: 0.75rem;">Utama</span>
                                    <?php endif; ?>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>

        <!-- Kartu Mobile Pihak -->
        <div class="d-md-none d-flex flex-column gap-2">
            <?php if (empty($pihak)): ?>
                <div class="text-center text-secondary py-4 small">Belum ada pihak tercatat pada kasus ini.</div>
            <?php else: ?>
                <?php foreach ($pihak as $p): ?>
                    <?php $isUtama = (($p['nomor_taruna'] ?? '') === ($kasus['nomor_taruna'] ?? '') && ($p['peran'] ?? '') === 'Terlapor'); ?>
                    <div class="border rounded-3 p-3 bg-white">
                        <div class="d-flex justify-content-between align-items-start gap-2 mb-1">
                            <div>
                                <div class="fw-bold text-dark small"><?= esc((string) $p['display_nama']) ?></div>
                                <div class="text-secondary ecc-num" style="font-size: 0.75rem;">
                                    <?= esc((string) $p['display_nomor']) ?> &bull; <?= ($p['jenis_pihak'] ?? 'taruna') === 'eksternal' ? 'Eksternal' : 'Taruna' ?>
                                </div>
                            </div>
                            <span class="badge bg-light text-dark border rounded-pill px-3 py-1" style="font-size: 0.75rem;"><?= esc((string) $p['peran']) ?></span>
                        </div>
                        <div class="text-secondary small mb-2"><?= esc((string) ($p['keterangan'] ?: '-')) ?></div>
                        <?php if (!$isUtama): ?>
                            <form method="post" action="<?= base_url('ketarunaan/pelanggaran/pemeriksaan/' . $idKasus . '/pihak/hapus/' . (int) $p['id']) ?>" class="form-hapus-pihak">
                                <?= csrf_field() ?>
                                <button type="submit" class="btn btn-outline-danger btn-sm rounded-3 px-3 w-100 fw-semibold btn-tactile pl-touch-ctrl" aria-label="Hapus pihak <?= esc((string) $p['display_nama']) ?>" data-nama="<?= esc((string) $p['display_nama']) ?>">Hapus Pihak</button>
                            </form>
                        <?php else: ?>
                            <span class="badge bg-light text-secondary border rounded-pill px-3 py-1" style="font-size: 0.75rem;">Terlapor Utama</span>
                        <?php endif; ?>
                    </div>
                <?php endforeach; ?>
            <?php endif; ?>
        </div>
    </div>
</div>
<?= $this->endSection() ?>

<?= $this->section('scripts') ?>
<script src="<?= base_url('assets/vendor/jquery/jquery.min.js') ?>"></script>
<script src="<?= base_url('assets/vendor/select2/select2.min.js') ?>"></script>
<script>
document.addEventListener('DOMContentLoaded', function () {
    if (window.jQuery && typeof window.jQuery.fn.select2 === 'function') {
        var selectPihak = window.jQuery('#selectPihakTaruna');
        if (selectPihak.length) {
            selectPihak.select2({
                theme: 'bootstrap-5',
                width: '100%',
                placeholder: '-- Pilih Taruna --'
            });
        }
    }

    var tglEl = document.getElementById('inputTglPeriksa');
    var fpTgl = null;
    if (typeof flatpickr !== 'undefined' && tglEl) {
        var fpLocale = (flatpickr.l10ns && flatpickr.l10ns.id) ? 'id' : 'default';
        fpTgl = flatpickr(tglEl, {
            locale: fpLocale,
            dateFormat: 'Y-m-d',
            altInput: true,
            altFormat: 'd F Y',
            onReady: function (d, s, inst) {
                if (inst && inst.altInput) inst.altInput.setAttribute('aria-label', 'Tanggal Pemeriksaan');
            }
        });
    }

    var jenisSelect   = document.getElementById('selectJenisPihak');
    var blokTaruna    = document.getElementById('blokPihakTaruna');
    var blokEksternal = document.getElementById('blokPihakEksternal');
    var selectTaruna  = document.getElementById('selectPihakTaruna');
    var inputEkst     = document.getElementById('inputNamaEksternal');

    function syncJenisPihak() {
        if (!jenisSelect || !blokTaruna || !blokEksternal) return;
        var isEkst = jenisSelect.value === 'eksternal';
        blokTaruna.classList.toggle('d-none', isEkst);
        blokEksternal.classList.toggle('d-none', !isEkst);
        if (selectTaruna) selectTaruna.required = !isEkst;
        if (inputEkst) inputEkst.required = isEkst;
    }
    if (jenisSelect) {
        jenisSelect.addEventListener('change', syncJenisPihak);
        syncJenisPihak();
    }

    function showWarn(title, text) {
        if (typeof Swal !== 'undefined') {
            Swal.fire({
                icon: 'warning',
                title: title,
                text: text,
                confirmButtonText: 'Tutup',
                confirmButtonColor: '#1e40af',
                customClass: { popup: 'rounded-4 shadow border-0' }
            });
        } else {
            alert(text);
        }
    }

    var formPeriksa = document.getElementById('formPemeriksaanKasus');
    var lastAction  = 'next';
    document.querySelectorAll('.js-submit-pl2').forEach(function (btn) {
        btn.addEventListener('click', function () {
            lastAction = btn.value || 'next';
        });
    });

    if (formPeriksa) {
        formPeriksa.addEventListener('submit', function (e) {
            var bapEl   = document.getElementById('inputBerkasBap');
            var klarEl  = document.getElementById('inputKlarifikasi');
            var hasilEl = document.getElementById('inputHasilPeriksa');

            if (tglEl && tglEl.value.trim() === '') {
                e.preventDefault();
                if (fpTgl && fpTgl.altInput) fpTgl.altInput.focus();
                showWarn('Tanggal Wajib Diisi', 'Pilih tanggal pemeriksaan terlebih dahulu.');
                return;
            }
            if (bapEl && bapEl.value.trim() !== '') {
                var u = bapEl.value.trim();
                if (!/^https?:\/\/\S+/i.test(u) || u.length > 255) {
                    e.preventDefault();
                    bapEl.focus();
                    showWarn('Tautan Tidak Valid', 'Tautan BAP harus diawali http:// atau https:// (maksimal 255 karakter).');
                    return;
                }
            }
            if (klarEl && klarEl.value.trim() === '') {
                e.preventDefault();
                klarEl.focus();
                showWarn('Klarifikasi Wajib Diisi', 'Isi ringkasan klarifikasi pemeriksaan.');
                return;
            }
            if (hasilEl && hasilEl.value.trim() === '') {
                e.preventDefault();
                hasilEl.focus();
                showWarn('Hasil Wajib Diisi', 'Isi hasil pemeriksaan terlebih dahulu.');
                return;
            }

            var aksi = (e.submitter && e.submitter.value) ? e.submitter.value : lastAction;
            document.querySelectorAll('.js-submit-pl2').forEach(function (btn) {
                btn.disabled = true;
            });
            var hiddenAksi = formPeriksa.querySelector('input[name="aksi_submit"][data-generated="1"]');
            if (!hiddenAksi) {
                hiddenAksi = document.createElement('input');
                hiddenAksi.type = 'hidden';
                hiddenAksi.name = 'aksi_submit';
                hiddenAksi.setAttribute('data-generated', '1');
                formPeriksa.appendChild(hiddenAksi);
            }
            hiddenAksi.value = aksi;
        });
    }

    var formPihak = document.getElementById('formTambahPihak');
    if (formPihak) {
        formPihak.addEventListener('submit', function () {
            var btn = document.getElementById('btnTambahPihak');
            if (btn) btn.disabled = true;
        });
    }

    document.querySelectorAll('table[data-sortable-table="true"]').forEach(function (table) {
        var buttons = table.querySelectorAll('thead .pl-sort-btn');
        var tbody = table.querySelector('tbody');
        if (!tbody || buttons.length === 0) return;
        var activeCol = null;
        var activeDir = 'asc';

        buttons.forEach(function (btn) {
            btn.addEventListener('click', function () {
                var th = btn.closest('th');
                var colIdx = parseInt(btn.getAttribute('data-sort-col'), 10);
                if (isNaN(colIdx)) return;

                if (activeCol === colIdx) {
                    activeDir = (activeDir === 'asc') ? 'desc' : 'asc';
                } else {
                    activeCol = colIdx;
                    activeDir = 'asc';
                }

                table.querySelectorAll('thead th[aria-sort]').forEach(function (h) {
                    h.classList.remove('th-sorted');
                    h.setAttribute('aria-sort', 'none');
                    var icon = h.querySelector('.sort-indicator');
                    if (icon) icon.className = 'bi bi-arrow-down-up text-muted opacity-50 sort-indicator';
                });

                if (th) {
                    th.classList.add('th-sorted');
                    th.setAttribute('aria-sort', activeDir === 'asc' ? 'ascending' : 'descending');
                }
                var activeIcon = btn.querySelector('.sort-indicator');
                if (activeIcon) {
                    activeIcon.className = activeDir === 'asc'
                        ? 'bi bi-arrow-up text-primary sort-indicator'
                        : 'bi bi-arrow-down text-primary sort-indicator';
                }

                var rows = Array.from(tbody.querySelectorAll('tr:not(.pl-empty-row)'));
                rows.sort(function (a, b) {
                    var cellA = a.children[colIdx];
                    var cellB = b.children[colIdx];
                    var rawA = cellA ? (cellA.getAttribute('data-sort-val') || cellA.textContent.trim().toLowerCase()) : '';
                    var rawB = cellB ? (cellB.getAttribute('data-sort-val') || cellB.textContent.trim().toLowerCase()) : '';
                    var cmp = rawA.localeCompare(rawB, 'id', { numeric: true });
                    return activeDir === 'asc' ? cmp : -cmp;
                });
                rows.forEach(function (r) { tbody.appendChild(r); });
            });
        });
    });

    document.querySelectorAll('.form-hapus-pihak').forEach(function (form) {
        form.addEventListener('submit', function (e) {
            if (form.dataset.confirmed === '1') return;
            e.preventDefault();
            var btn  = form.querySelector('button[type="submit"]');
            var nama = btn ? (btn.getAttribute('data-nama') || 'pihak ini') : 'pihak ini';
            var submitNow = function () {
                form.dataset.confirmed = '1';
                if (btn) btn.disabled = true;
                form.submit();
            };
            if (typeof Swal !== 'undefined') {
                Swal.fire({
                    title: 'Hapus Pihak?',
                    text: nama + ' akan dihapus dari daftar pihak kasus.',
                    icon: 'warning',
                    showCancelButton: true,
                    confirmButtonColor: '#dc2626',
                    cancelButtonColor: '#64748b',
                    confirmButtonText: 'Ya, Hapus',
                    cancelButtonText: 'Batal',
                    reverseButtons: true,
                    customClass: { popup: 'rounded-4 shadow border-0' }
                }).then(function (res) {
                    if (res.isConfirmed) submitNow();
                });
            } else if (confirm(nama + ' akan dihapus dari daftar pihak kasus.')) {
                submitNow();
            }
        });
    });

    window.addEventListener('pageshow', function () {
        document.querySelectorAll('.js-submit-pl2, #btnTambahPihak, .form-hapus-pihak button[type="submit"]').forEach(function (btn) {
            btn.disabled = false;
        });
    });
});
</script>
<?= $this->endSection() ?>
