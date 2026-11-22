<?= $this->extend('layouts/ecc2_layout') ?>

<?= $this->section('title') ?>Dokumen Bukti Kasus<?= $this->endSection() ?>

<?= $this->section('styles') ?>
<?= $this->include('ketarunaan/pelanggaran/_styles') ?>
<?= $this->endSection() ?>

<?= $this->section('content') ?>
<?php
$detail       = $detail ?? [];
$kasus        = $detail['kasus'] ?? [];
$dokumen      = $detail['dokumen'] ?? [];
$checklist    = $detail['checklist'] ?? [];
$refs         = $refs ?? [];
$canViewFull  = !empty($canViewFull);
$idKasus      = (int) ($kasus['id'] ?? 0);

$laporanAda     = !empty($checklist['laporan_ada']);
$bapAda         = !empty($checklist['bap_ada']);
$penetapanAda   = !empty($checklist['penetapan_ada']);
$penetapanWajib = !empty($checklist['penetapan_wajib']);
$noKeputusanAda = !empty($kasus['nomor_keputusan']);
$sisaSlotDok    = max(0, 10 - count($dokumen));
?>

<div class="container-fluid px-0">
    <?= view('ketarunaan/pelanggaran/_wizard_header', [
        'activeStep'   => 4,
        'pageTitle'    => 'Dokumen Bukti Kasus',
        'pageSubtitle' => 'Mengelola tautan dokumen laporan, pemeriksaan, dan keputusan untuk validasi pencatatan.',
        'kasus'        => $kasus,
    ]) ?>

    <div class="bento-card ecc-pl-card pl-form-scope">
        <div class="row g-4">
            <!-- Kolom Kiri: Daftar Dokumen & Tambah Tautan (Opsi A - Google Drive) -->
            <div class="col-12 col-lg-7">
                <div class="ecc-panel-box h-100">
                    <div class="d-flex justify-content-between align-items-center gap-2 mb-1">
                        <h2 class="h6 fw-bold text-dark mb-0">Daftar Dokumen Bukti</h2>
                        <span class="text-secondary ecc-num small"><?= count($dokumen) ?> / 10</span>
                    </div>
                    <p class="text-secondary mb-3 small">Seluruh dokumen berstatus terbatas dan hanya dapat dibuka oleh petugas berwenang.</p>

                    <!-- Tabel Desktop Dokumen -->
                    <div class="table-responsive ecc-pl-scroll d-none d-md-block mb-4">
                        <table class="table table-hover ecc-table-pl mb-0" data-sortable-table="true">
                            <thead>
                                <tr>
                                    <th aria-sort="none">
                                        <button type="button" class="pl-sort-btn" data-sort-col="0">
                                            <span>Tautan / Berkas</span><i class="bi bi-arrow-down-up text-muted opacity-50 sort-indicator" aria-hidden="true"></i>
                                        </button>
                                    </th>
                                    <th aria-sort="none">
                                        <button type="button" class="pl-sort-btn" data-sort-col="1">
                                            <span>Jenis</span><i class="bi bi-arrow-down-up text-muted opacity-50 sort-indicator" aria-hidden="true"></i>
                                        </button>
                                    </th>
                                    <th aria-sort="none">
                                        <button type="button" class="pl-sort-btn" data-sort-col="2">
                                            <span>Klasifikasi</span><i class="bi bi-arrow-down-up text-muted opacity-50 sort-indicator" aria-hidden="true"></i>
                                        </button>
                                    </th>
                                    <th class="text-end">Aksi</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php if (empty($dokumen)): ?>
                                    <tr class="pl-empty-row">
                                        <td colspan="4" class="text-center text-secondary py-4">Belum ada dokumen bukti yang tersimpan.</td>
                                    </tr>
                                <?php else: ?>
                                    <?php foreach ($dokumen as $dok): ?>
                                        <?php
                                        $rawDoc    = trim((string) ($dok['nama_berkas'] ?? ''));
                                        $isDocUrl  = ketarunaan_is_http_url($rawDoc, 2000);
                                        $docLabel  = ketarunaan_format_bukti_label($rawDoc);
                                        $tglUnggah = ketarunaan_format_tanggal((string) ($dok['tanggal_unggah'] ?? ''), true);
                                        $isSangat  = (($dok['klasifikasi'] ?? 'terbatas') === 'sangat_terbatas');
                                        $bisaAkses = !empty($dok['can_access']) && $canViewFull;
                                        $docHref   = $isDocUrl
                                            ? $rawDoc
                                            : base_url('ketarunaan/pelanggaran/unduh-dokumen/' . $idKasus . '?file=' . rawurlencode(basename($rawDoc)) . '&inline=1');
                                        ?>
                                        <tr>
                                            <td data-sort-val="<?= esc(strtolower($docLabel)) ?>">
                                                <div class="fw-semibold text-dark text-truncate" style="max-width: 240px;" title="<?= esc($docLabel) ?>"><?= esc($docLabel) ?></div>
                                                <div class="text-secondary ecc-num" style="font-size: 0.75rem;">
                                                    Dicatat <?= esc($tglUnggah) ?>
                                                    <?php if (!$isDocUrl && !empty($dok['ukuran_kb'])): ?>
                                                        &bull; <?= number_format((int) $dok['ukuran_kb'], 0, ',', '.') ?> KB
                                                    <?php endif; ?>
                                                </div>
                                            </td>
                                            <td data-sort-val="<?= esc(strtolower((string) ($dok['jenis_dokumen'] ?? 'Pendukung'))) ?>"><?= esc((string) ($dok['jenis_dokumen'] ?? 'Pendukung')) ?></td>
                                            <td data-sort-val="<?= $isSangat ? 'sangat_terbatas' : 'terbatas' ?>">
                                                <?php if ($isSangat): ?>
                                                    <span class="badge bg-danger-subtle text-danger-emphasis border border-danger-subtle rounded-pill px-3 py-1" style="font-size: 0.75rem;">Sangat Terbatas</span>
                                                <?php else: ?>
                                                    <span class="badge bg-primary-subtle text-primary-emphasis border border-primary-subtle rounded-pill px-3 py-1" style="font-size: 0.75rem;">Terbatas</span>
                                                <?php endif; ?>
                                            </td>
                                            <td class="text-end text-nowrap">
                                                <?php if ($bisaAkses): ?>
                                                    <a href="<?= esc($docHref) ?>" target="_blank" rel="noopener noreferrer" class="btn btn-outline-primary btn-sm rounded-3 px-3 btn-tactile">
                                                        <?= $isDocUrl ? 'Buka' : 'Pratinjau' ?>
                                                    </a>
                                                <?php else: ?>
                                                    <span class="badge bg-light text-secondary border rounded-pill px-3 py-1">Terkunci</span>
                                                <?php endif; ?>
                                                <button type="button" class="btn btn-outline-danger btn-sm rounded-3 px-3 btn-hapus-dok btn-tactile" data-filename="<?= esc($rawDoc) ?>" data-label="<?= esc($docLabel) ?>" aria-label="Hapus dokumen <?= esc($docLabel) ?>">Hapus</button>
                                            </td>
                                        </tr>
                                    <?php endforeach; ?>
                                <?php endif; ?>
                            </tbody>
                        </table>
                    </div>

                    <!-- Kartu Mobile Dokumen -->
                    <div class="d-md-none d-flex flex-column gap-2 mb-4">
                        <?php if (empty($dokumen)): ?>
                            <div class="text-center text-secondary py-4 small">Belum ada dokumen bukti yang tersimpan.</div>
                        <?php else: ?>
                            <?php foreach ($dokumen as $dok): ?>
                                <?php
                                $rawDoc    = trim((string) ($dok['nama_berkas'] ?? ''));
                                $isDocUrl  = ketarunaan_is_http_url($rawDoc, 2000);
                                $docLabel  = ketarunaan_format_bukti_label($rawDoc);
                                $tglUnggah = ketarunaan_format_tanggal((string) ($dok['tanggal_unggah'] ?? ''), true);
                                $isSangat  = (($dok['klasifikasi'] ?? 'terbatas') === 'sangat_terbatas');
                                $bisaAkses = !empty($dok['can_access']) && $canViewFull;
                                $docHref   = $isDocUrl
                                    ? $rawDoc
                                    : base_url('ketarunaan/pelanggaran/unduh-dokumen/' . $idKasus . '?file=' . rawurlencode(basename($rawDoc)) . '&inline=1');
                                ?>
                                <div class="border rounded-3 p-3 bg-white">
                                    <div class="d-flex justify-content-between align-items-start gap-2 mb-1">
                                        <div class="fw-bold text-dark small text-truncate"><?= esc($docLabel) ?></div>
                                        <span class="badge <?= $isSangat ? 'bg-danger-subtle text-danger-emphasis border border-danger-subtle' : 'bg-primary-subtle text-primary-emphasis border border-primary-subtle' ?> rounded-pill px-3 py-1 flex-shrink-0" style="font-size: 0.75rem;">
                                            <?= $isSangat ? 'Sangat Terbatas' : 'Terbatas' ?>
                                        </span>
                                    </div>
                                    <div class="text-secondary ecc-num mb-2" style="font-size: 0.75rem;">
                                        <?= esc((string) ($dok['jenis_dokumen'] ?? 'Pendukung')) ?> &bull; <?= esc($tglUnggah) ?>
                                    </div>
                                    <div class="d-flex gap-2">
                                        <?php if ($bisaAkses): ?>
                                            <a href="<?= esc($docHref) ?>" target="_blank" rel="noopener noreferrer" class="btn btn-outline-primary btn-sm rounded-3 px-3 flex-fill fw-semibold pl-touch-ctrl d-inline-flex align-items-center justify-content-center">
                                                <?= $isDocUrl ? 'Buka' : 'Pratinjau' ?>
                                            </a>
                                        <?php endif; ?>
                                        <button type="button" class="btn btn-outline-danger btn-sm rounded-3 px-3 flex-fill fw-semibold btn-hapus-dok pl-touch-ctrl" data-filename="<?= esc($rawDoc) ?>" data-label="<?= esc($docLabel) ?>" aria-label="Hapus dokumen <?= esc($docLabel) ?>">Hapus</button>
                                    </div>
                                </div>
                            <?php endforeach; ?>
                        <?php endif; ?>
                    </div>

                    <form id="formDokumenKasus" method="post" action="<?= base_url('ketarunaan/pelanggaran/dokumen/' . $idKasus . '/simpan') ?>" class="border rounded-3 p-3 bg-light">
                        <?= csrf_field() ?>
                        <h3 class="h6 fw-semibold text-dark mb-2">Tambah Tautan Dokumen Baru (Opsional)</h3>
                        <?php if ($sisaSlotDok > 0): ?>
                            <div class="row g-2">
                                <div class="col-12">
                                    <label for="inputDokUrl" class="form-label text-secondary fw-semibold mb-1" style="font-size: 0.75rem;">Tautan Google Drive / Cloud</label>
                                    <div class="input-group">
                                        <span class="input-group-text bg-white border-secondary-subtle text-secondary">
                                            <i class="bi bi-link-45deg" aria-hidden="true"></i>
                                        </span>
                                        <input
                                            type="url"
                                            id="inputDokUrl"
                                            name="dokumen_url"
                                            class="form-control ecc-control-sm"
                                            value="<?= esc((string) old('dokumen_url', '')) ?>"
                                            placeholder="https://drive.google.com/file/d/.../view"
                                            maxlength="500"
                                            autocomplete="off"
                                        >
                                    </div>
                                    <div class="form-text ecc-num" style="font-size: 0.75rem;">
                                        Awali dengan http:// atau https:// (maks. 500 karakter, tersisa <?= $sisaSlotDok ?> slot).
                                    </div>
                                </div>
                                <div class="col-12 col-sm-6">
                                    <label for="selectJenisDok" class="form-label text-secondary fw-semibold mb-1" style="font-size: 0.75rem;">Jenis Dokumen</label>
                                    <select id="selectJenisDok" name="jenis_dokumen" class="form-select ecc-control-sm">
                                        <?php foreach ($refs['jenis_dokumen_options'] ?? [] as $opt): ?>
                                            <option value="<?= esc($opt) ?>" <?= old('jenis_dokumen') === $opt ? 'selected' : '' ?>><?= esc($opt) ?></option>
                                        <?php endforeach; ?>
                                    </select>
                                </div>
                                <div class="col-12 col-sm-6">
                                    <label for="selectKlasifikasiDok" class="form-label text-secondary fw-semibold mb-1" style="font-size: 0.75rem;">Klasifikasi Akses</label>
                                    <select id="selectKlasifikasiDok" name="klasifikasi" class="form-select ecc-control-sm">
                                        <option value="terbatas" <?= old('klasifikasi') !== 'sangat_terbatas' ? 'selected' : '' ?>>Terbatas</option>
                                        <option value="sangat_terbatas" <?= old('klasifikasi') === 'sangat_terbatas' ? 'selected' : '' ?>>Sangat Terbatas</option>
                                    </select>
                                </div>
                            </div>
                        <?php else: ?>
                            <div class="text-secondary small">Batas maksimal 10 dokumen per kasus telah tercapai. Hapus dokumen lama bila ingin mengganti.</div>
                        <?php endif; ?>
                    </form>
                </div>
            </div>

            <!-- Kolom Kanan: Status Ketersediaan Dokumen -->
            <div class="col-12 col-lg-5">
                <div class="ecc-panel-box h-100 d-flex flex-column justify-content-between">
                    <div>
                        <h2 class="h6 fw-bold text-dark mb-1">Status Ketersediaan Dokumen</h2>
                        <p class="text-secondary mb-3 small">Periksa kelengkapan dokumen sesuai tahapan penanganan kasus.</p>

                        <div class="d-flex flex-column gap-2 mb-3">
                            <div class="ecc-check-item">
                                <span class="fw-medium text-dark small">1. Laporan awal tercatat</span>
                                <span class="badge <?= $laporanAda ? 'bg-success-subtle text-success-emphasis border border-success-subtle' : 'bg-warning-subtle text-warning-emphasis border border-warning-subtle' ?> rounded-pill px-3 py-1">
                                    <?= $laporanAda ? 'Tersedia' : 'Belum Lengkap' ?>
                                </span>
                            </div>

                            <div class="ecc-check-item">
                                <span class="fw-medium text-dark small">2. Berita acara pemeriksaan (BAP)</span>
                                <span class="badge <?= $bapAda ? 'bg-success-subtle text-success-emphasis border border-success-subtle' : 'bg-secondary-subtle text-secondary-emphasis border border-secondary-subtle' ?> rounded-pill px-3 py-1">
                                    <?= $bapAda ? 'Tersedia' : 'Belum Tersedia' ?>
                                </span>
                            </div>

                            <div class="ecc-check-item">
                                <span class="fw-medium text-dark small">
                                    3. Dokumen penetapan <?= $penetapanWajib ? '(Wajib untuk Terbukti)' : '(Opsional untuk Dugaan)' ?>
                                </span>
                                <span class="badge <?= $penetapanAda ? 'bg-success-subtle text-success-emphasis border border-success-subtle' : ($penetapanWajib ? 'bg-danger-subtle text-danger-emphasis border border-danger-subtle' : 'bg-secondary-subtle text-secondary-emphasis border border-secondary-subtle') ?> rounded-pill px-3 py-1">
                                    <?= $penetapanAda ? 'Tersedia' : 'Belum Tersedia' ?>
                                </span>
                            </div>
                        </div>

                        <?php if ($penetapanWajib && !$noKeputusanAda): ?>
                            <div class="alert alert-warning border-0 rounded-3 px-3 py-2 mb-3 small">
                                Isi Nomor Keputusan pada Tahap 3 sebelum mengajukan validasi untuk kasus berstatus Terbukti.
                            </div>
                        <?php endif; ?>

                        <div class="rounded-3 p-3 small" style="background-color: #eff6ff; border: 1px solid #bfdbfe; color: #1e3a8a;">
                            Kasus dugaan dapat diajukan untuk validasi pencatatan dan tetap tidak dihitung sebagai pelanggaran terbukti.
                        </div>
                    </div>

                    <div class="d-flex flex-column flex-sm-row gap-2 pt-3 mt-4 border-top">
                        <button type="submit" form="formDokumenKasus" name="aksi_submit" value="draf" class="btn btn-outline-secondary ecc-control-sm px-3 fw-semibold flex-fill btn-tactile pl-touch-ctrl js-submit-pl4">
                            Simpan Draf
                        </button>
                        <button type="submit" form="formDokumenKasus" name="aksi_submit" value="diajukan" class="btn btn-primary ecc-control-sm px-3 fw-semibold flex-fill btn-tactile pl-touch-ctrl js-submit-pl4">
                            Ajukan Validasi
                        </button>
                    </div>
                </div>
            </div>
        </div>

        <div class="d-flex flex-wrap justify-content-between align-items-center gap-2 pt-3 mt-4 border-top">
            <a href="<?= base_url('ketarunaan/pelanggaran/penetapan/' . $idKasus) ?>" class="btn btn-light border ecc-control-sm px-3 fw-semibold d-inline-flex align-items-center justify-content-center gap-2 btn-tactile pl-touch-ctrl">
                <i class="bi bi-arrow-left" aria-hidden="true"></i> Sebelumnya: Penetapan &amp; Tindak Lanjut
            </a>
            <a href="<?= base_url('ketarunaan/pelanggaran/detail/' . $idKasus) ?>" class="btn btn-outline-primary ecc-control-sm px-3 fw-semibold btn-tactile pl-touch-ctrl d-inline-flex align-items-center justify-content-center">
                Lihat Detail Kasus
            </a>
        </div>
    </div>

    <!-- Form Terpisah Hapus Dokumen (Menghindari Nested Form) -->
    <form id="formHapusDok" method="post" action="<?= base_url('ketarunaan/pelanggaran/dokumen/' . $idKasus . '/hapus') ?>" class="d-none">
        <?= csrf_field() ?>
        <input type="hidden" name="filename" id="hapusDokFilename" value="">
    </form>
</div>
<?= $this->endSection() ?>

<?= $this->section('scripts') ?>
<script>
document.addEventListener('DOMContentLoaded', function () {
    var formDok        = document.getElementById('formDokumenKasus');
    var inputDokUrl    = document.getElementById('inputDokUrl');
    var laporanAda     = <?= $laporanAda ? 'true' : 'false' ?>;
    var penetapanWajib = <?= $penetapanWajib ? 'true' : 'false' ?>;
    var noKeputusanAda = <?= $noKeputusanAda ? 'true' : 'false' ?>;

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

    var lastAction = 'draf';
    document.querySelectorAll('.js-submit-pl4').forEach(function (btn) {
        btn.addEventListener('click', function () {
            lastAction = btn.value || 'draf';
        });
    });

    if (formDok) {
        formDok.addEventListener('submit', function (e) {
            var aksi = (e.submitter && e.submitter.value) ? e.submitter.value : lastAction;
            var rawUrl = inputDokUrl ? inputDokUrl.value.trim() : '';

            if (rawUrl !== '' && (!/^https?:\/\/\S+/i.test(rawUrl) || rawUrl.length > 500)) {
                e.preventDefault();
                if (inputDokUrl) inputDokUrl.focus();
                showWarn('Tautan Tidak Valid', 'Tautan dokumen harus diawali http:// atau https:// (maksimal 500 karakter).');
                return;
            }

            if (aksi === 'diajukan') {
                if (!laporanAda) {
                    e.preventDefault();
                    showWarn('Laporan Belum Lengkap', 'Lengkapi uraian laporan awal pada Tahap 1 sebelum mengajukan validasi.');
                    return;
                }
                if (penetapanWajib && !noKeputusanAda) {
                    e.preventDefault();
                    showWarn('Nomor Keputusan Wajib Diisi', 'Isi nomor keputusan pada Tahap 3 sebelum mengajukan kasus berstatus Terbukti.');
                    return;
                }
            }

            document.querySelectorAll('.js-submit-pl4').forEach(function (btn) {
                btn.disabled = true;
            });
            var hiddenAksi = formDok.querySelector('input[name="aksi_submit"][data-generated="1"]');
            if (!hiddenAksi) {
                hiddenAksi = document.createElement('input');
                hiddenAksi.type = 'hidden';
                hiddenAksi.name = 'aksi_submit';
                hiddenAksi.setAttribute('data-generated', '1');
                formDok.appendChild(hiddenAksi);
            }
            hiddenAksi.value = aksi;
        });

        window.addEventListener('pageshow', function () {
            document.querySelectorAll('.js-submit-pl4').forEach(function (btn) {
                btn.disabled = false;
            });
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

    var formHapusDok = document.getElementById('formHapusDok');
    var inputHapus   = document.getElementById('hapusDokFilename');

    document.querySelectorAll('.btn-hapus-dok').forEach(function (btn) {
        btn.addEventListener('click', function () {
            var filename = this.getAttribute('data-filename') || '';
            var label    = this.getAttribute('data-label') || filename;
            if (!filename || !formHapusDok || !inputHapus) return;
            inputHapus.value = filename;

            if (typeof Swal !== 'undefined') {
                Swal.fire({
                    title: 'Hapus Dokumen?',
                    text: 'Dokumen "' + label + '" akan dihapus dari kasus ini.',
                    icon: 'warning',
                    showCancelButton: true,
                    confirmButtonColor: '#dc2626',
                    cancelButtonColor: '#64748b',
                    confirmButtonText: 'Ya, Hapus',
                    cancelButtonText: 'Batal',
                    reverseButtons: true,
                    customClass: { popup: 'rounded-4 shadow border-0' }
                }).then(function (res) {
                    if (res.isConfirmed) {
                        formHapusDok.submit();
                    }
                });
            } else if (confirm('Dokumen "' + label + '" akan dihapus dari kasus ini.')) {
                formHapusDok.submit();
            }
        });
    });
});
</script>
<?= $this->endSection() ?>
