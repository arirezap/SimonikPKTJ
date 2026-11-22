<?= $this->extend('layouts/ecc2_layout') ?>

<?= $this->section('title') ?>Dokumen Bukti Prestasi<?= $this->endSection() ?>

<?= $this->section('styles') ?>
<?= $this->include('ketarunaan/prestasi/_styles') ?>
<?= $this->endSection() ?>

<?= $this->section('content') ?>
<?php
$detail       = $detail ?? [];
$prestasi     = $detail['prestasi'] ?? [];
$dokumentasi  = $detail['dokumentasi'] ?? [];
$checklist    = $detail['checklist'] ?? [];

$idPrestasi   = (int) ($prestasi['id'] ?? 0);
$rawStatus    = (string) ($prestasi['status_validasi'] ?? 'draf');

$tingkatMap   = ketarunaan_tingkat_prestasi_catalog();
$tingkatLbl   = $tingkatMap[$prestasi['tingkat_prestasi'] ?? ''] ?? ucfirst((string) ($prestasi['tingkat_prestasi'] ?? '-'));

$infoLengkap   = !empty($checklist['info_lengkap']);
$pesertaAda    = !empty($checklist['peserta_ada']);
$jumlahPeserta = (int) ($checklist['jumlah_peserta'] ?? 0);
$buktiAda      = !empty($checklist['bukti_ada']);

$existingBuktiRaw = trim((string) ($prestasi['bukti_berkas'] ?? ''));
$hasExistingUrl   = ($existingBuktiRaw !== '' && ketarunaan_is_http_url($existingBuktiRaw, 2000));
$hasLegacyFile    = ($existingBuktiRaw !== '' && !$hasExistingUrl);
$valBuktiLink     = (string) old('bukti_berkas', $hasExistingUrl ? $existingBuktiRaw : '');
$ukuranKb         = (int) ($prestasi['ukuran_berkas_kb'] ?? 0);
$sisaSlotDok      = max(0, 5 - count($dokumentasi));
$isSubmitReady    = ($infoLengkap && $pesertaAda);

$oldDokLinks = old('dokumentasi_links');
if (!is_array($oldDokLinks)) {
    $oldDokLinks = $sisaSlotDok > 0 ? [''] : [];
} else {
    $oldDokLinks = array_slice(array_values($oldDokLinks), 0, max(1, $sisaSlotDok));
}
?>

<div class="container-fluid px-0">
    <!-- Header Halaman -->
    <div class="d-flex flex-column flex-md-row justify-content-between align-items-md-center gap-3 mb-4">
        <div>
            <nav aria-label="breadcrumb" class="mb-1">
                <ol class="breadcrumb mb-0" style="font-size: 0.75rem;">
                    <li class="breadcrumb-item"><a href="<?= esc(ketarunaan_home_url()) ?>" class="text-decoration-none text-secondary">Ketarunaan</a></li>
                    <li class="breadcrumb-item"><a href="<?= base_url('ketarunaan/prestasi') ?>" class="text-decoration-none text-secondary">Prestasi</a></li>
                    <li class="breadcrumb-item active text-dark fw-semibold" aria-current="page">Dokumen Bukti</li>
                </ol>
            </nav>
            <h1 class="h3 fw-bold text-dark mb-1">Dokumen Bukti Prestasi</h1>
            <p class="text-secondary mb-0 small">Lengkapi tautan bukti utama dan ajukan prestasi untuk validasi.</p>
        </div>
        <div>
            <a href="<?= base_url('ketarunaan/prestasi/detail/' . $idPrestasi) ?>" class="btn btn-light border ecc-control-sm px-3 fw-semibold d-inline-flex align-items-center justify-content-center gap-2 btn-tactile pr-touch-ctrl">
                <i class="bi bi-chevron-left" aria-hidden="true"></i> Kembali ke Detail Prestasi
            </a>
        </div>
    </div>

    <!-- Flash Messages -->
    <?php if (session()->getFlashdata('success')): ?>
        <div class="alert alert-success border-0 rounded-3 mb-4 py-2 px-3 d-flex align-items-center justify-content-between" role="alert">
            <span class="small"><?= esc(session()->getFlashdata('success')) ?></span>
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Tutup"></button>
        </div>
    <?php endif; ?>
    <?php if (session()->getFlashdata('error')): ?>
        <div class="alert alert-danger border-0 rounded-3 mb-4 py-2 px-3 d-flex align-items-center justify-content-between" role="alert">
            <span class="small"><?= esc(session()->getFlashdata('error')) ?></span>
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Tutup"></button>
        </div>
    <?php endif; ?>

    <!-- Kartu Ringkas Prestasi -->
    <div class="bento-card ecc-pr-card mb-4">
        <div class="d-flex flex-column flex-md-row justify-content-between align-items-md-center gap-3">
            <div class="d-flex align-items-center gap-3">
                <div class="ecc-icon-48">
                    <i class="bi bi-trophy-fill" aria-hidden="true"></i>
                </div>
                <div>
                    <h2 class="h5 fw-bold text-dark mb-1"><?= esc((string) ($prestasi['nama_prestasi'] ?? '-')) ?></h2>
                    <div class="text-secondary small">
                        <?= esc($tingkatLbl) ?> &bull; <?= esc((string) ($prestasi['peringkat'] ?? '-')) ?>
                        <?php if (!empty($prestasi['nama_tim'])): ?>
                            &bull; <?= esc((string) $prestasi['nama_tim']) ?>
                        <?php else: ?>
                            &bull; <?= esc(ucfirst((string) ($prestasi['jenis_keikutsertaan'] ?? 'individu'))) ?>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
            <div><?= ketarunaan_validasi_badge($rawStatus) ?></div>
        </div>
    </div>

    <?php if ($rawStatus === 'perlu_perbaikan' && !empty($prestasi['catatan_validasi'])): ?>
        <div class="alert alert-warning border-0 rounded-3 mb-4 px-3 py-2 small" role="alert">
            <strong class="d-block text-dark">Catatan Perbaikan Validator</strong>
            <div class="text-dark mt-1"><?= esc((string) $prestasi['catatan_validasi']) ?></div>
        </div>
    <?php elseif ($rawStatus === 'tervalidasi'): ?>
        <div class="alert alert-info border-0 rounded-3 mb-4 px-3 py-2 small" role="alert">
            Prestasi ini sudah tervalidasi. Mengubah atau menghapus dokumen akan mengembalikan status ke <strong>Draf</strong> untuk diajukan validasi ulang.
        </div>
    <?php endif; ?>

    <!-- Kontainer Tahap 3 -->
    <div class="bento-card ecc-pr-card pr-form-scope">
        <nav class="ecc-wizard-nav" aria-label="Tahapan Pengisian Prestasi">
            <a href="<?= base_url('ketarunaan/prestasi/edit/' . $idPrestasi) ?>" class="ecc-wizard-step">1. Informasi Prestasi</a>
            <a href="<?= base_url('ketarunaan/prestasi/peserta/' . $idPrestasi) ?>" class="ecc-wizard-step">2. Peserta</a>
            <span class="ecc-wizard-step active" aria-current="step">3. Dokumen Bukti</span>
        </nav>

        <div class="row g-4">
            <!-- Kolom Kiri: Tautan Bukti & Dokumentasi (Opsi A - Google Drive) -->
            <div class="col-12 col-lg-7">
                <div class="ecc-panel-box h-100">
                    <h3 class="h6 fw-bold text-dark mb-1">Tautan Bukti &amp; Dokumentasi</h3>
                    <p class="text-secondary mb-3 small">Isi nomor sertifikat dan tempel tautan Google Drive piagam atau SK pemenang.</p>

                    <form id="formDokumenBukti" method="post" action="<?= base_url('ketarunaan/prestasi/dokumen/' . $idPrestasi . '/simpan') ?>">
                        <?= csrf_field() ?>

                        <div class="mb-3">
                            <label for="inputNomorSertifikat" class="form-label fw-semibold text-dark small">
                                Nomor Sertifikat / Piagam / SK <span class="text-danger">*</span>
                            </label>
                            <input
                                type="text"
                                id="inputNomorSertifikat"
                                name="nomor_sertifikat"
                                class="form-control ecc-control-sm"
                                value="<?= esc(old('nomor_sertifikat', (string) ($prestasi['nomor_sertifikat'] ?? ''))) ?>"
                                placeholder="Misal: PIAGAM/115/2026"
                                maxlength="100"
                            >
                        </div>

                        <div class="mb-4">
                            <label for="inputBuktiBerkas" class="form-label fw-semibold text-dark small">
                                Tautan Bukti Utama (Google Drive) <span class="text-danger">*</span>
                            </label>
                            <div class="input-group">
                                <span class="input-group-text bg-white border-secondary-subtle text-secondary">
                                    <i class="bi bi-link-45deg" aria-hidden="true"></i>
                                </span>
                                <input
                                    type="url"
                                    id="inputBuktiBerkas"
                                    name="bukti_berkas"
                                    class="form-control ecc-control-sm"
                                    value="<?= esc($valBuktiLink) ?>"
                                    placeholder="https://drive.google.com/file/d/.../view"
                                    maxlength="255"
                                    autocomplete="off"
                                >
                                <button type="button" id="btnBersihkanBukti" class="btn btn-outline-secondary ecc-control-sm px-3 pr-touch-ctrl <?= $valBuktiLink === '' ? 'd-none' : '' ?>" aria-label="Bersihkan tautan bukti utama">
                                    Bersihkan
                                </button>
                            </div>
                            <div class="form-text" style="font-size: 0.75rem;">
                                Pastikan tautan Google Drive memiliki akses lihat aktif (http:// atau https://, maks. 255 karakter).
                            </div>

                            <?php if ($existingBuktiRaw !== ''): ?>
                                <div class="ecc-file-item mt-2">
                                    <div class="d-flex align-items-center gap-2 text-truncate min-w-0">
                                        <i class="bi <?= $hasExistingUrl ? 'bi-link-45deg text-primary' : 'bi-file-earmark-pdf-fill text-danger' ?> flex-shrink-0" aria-hidden="true"></i>
                                        <div class="text-truncate min-w-0">
                                            <div class="fw-semibold text-dark text-truncate small"><?= esc(ketarunaan_format_bukti_label($existingBuktiRaw)) ?></div>
                                            <?php if ($hasLegacyFile): ?>
                                                <div class="text-secondary ecc-num" style="font-size: 0.75rem;">
                                                    Berkas lama<?= $ukuranKb > 0 ? ' &bull; ' . number_format($ukuranKb, 0, ',', '.') . ' KB' : '' ?> (isi tautan baru di atas untuk mengganti)
                                                </div>
                                            <?php endif; ?>
                                        </div>
                                    </div>
                                    <div class="d-flex align-items-center gap-2 flex-shrink-0">
                                        <?php if ($hasExistingUrl): ?>
                                            <a href="<?= esc($existingBuktiRaw) ?>" target="_blank" rel="noopener noreferrer" class="btn btn-outline-primary btn-sm rounded-3 px-3 btn-tactile pr-touch-ctrl d-inline-flex align-items-center gap-1">
                                                <i class="bi bi-box-arrow-up-right" aria-hidden="true"></i> Buka
                                            </a>
                                        <?php else: ?>
                                            <a href="<?= base_url('ketarunaan/prestasi/unduh-dokumen/' . $idPrestasi . '?inline=1') ?>" target="_blank" rel="noopener noreferrer" class="btn btn-outline-primary btn-sm rounded-3 px-3 btn-tactile pr-touch-ctrl d-inline-flex align-items-center">Pratinjau</a>
                                            <a href="<?= base_url('ketarunaan/prestasi/unduh-dokumen/' . $idPrestasi) ?>" class="btn btn-light border btn-sm rounded-3 px-3 btn-tactile pr-touch-ctrl d-inline-flex align-items-center">Unduh</a>
                                        <?php endif; ?>
                                    </div>
                                </div>
                            <?php endif; ?>
                        </div>

                        <div class="mb-0">
                            <div class="d-flex flex-wrap justify-content-between align-items-center gap-2 mb-1">
                                <?php if ($sisaSlotDok > 0): ?>
                                    <label for="dokLink0" class="form-label fw-semibold text-dark small mb-0">
                                        Tautan Dokumentasi Tambahan (Opsional)
                                    </label>
                                    <button type="button" id="btnTambahTautanDok" class="btn btn-light border btn-sm rounded-3 px-3 fw-semibold btn-tactile pr-touch-ctrl" data-max-slot="<?= $sisaSlotDok ?>">
                                        <i class="bi bi-plus-lg" aria-hidden="true"></i> Tambah Baris
                                    </button>
                                <?php else: ?>
                                    <span class="form-label fw-semibold text-dark small mb-0">
                                        Tautan Dokumentasi Tambahan (Opsional)
                                    </span>
                                <?php endif; ?>
                            </div>

                            <?php if ($sisaSlotDok > 0): ?>
                                <div id="wrapDokLinks" class="d-flex flex-column gap-2">
                                    <?php foreach ($oldDokLinks as $idxLink => $valLink): ?>
                                        <div class="input-group dok-link-row">
                                            <span class="input-group-text bg-white border-secondary-subtle text-secondary">
                                                <i class="bi bi-link-45deg" aria-hidden="true"></i>
                                            </span>
                                            <input
                                                type="url"
                                                <?= $idxLink === 0 ? 'id="dokLink0"' : '' ?>
                                                name="dokumentasi_links[]"
                                                class="form-control ecc-control-sm"
                                                value="<?= esc((string) $valLink) ?>"
                                                placeholder="https://drive.google.com/drive/folders/..."
                                                maxlength="500"
                                                aria-label="Tautan dokumentasi tambahan <?= $idxLink + 1 ?>"
                                            >
                                            <button type="button" class="btn btn-outline-secondary ecc-control-sm px-3 btn-remove-dok-row <?= $idxLink === 0 ? 'd-none' : '' ?>" aria-label="Hapus baris tautan">
                                                <i class="bi bi-x-lg" aria-hidden="true"></i>
                                            </button>
                                        </div>
                                    <?php endforeach; ?>
                                </div>
                            <?php endif; ?>

                            <div class="form-text ecc-num" style="font-size: 0.75rem;">
                                Tautan folder atau foto dokumentasi di Google Drive (maksimal 5 tautan, tersisa <?= $sisaSlotDok ?> slot).
                            </div>

                            <?php if (!empty($dokumentasi)): ?>
                                <div class="d-flex flex-column gap-2 mt-3">
                                    <?php foreach ($dokumentasi as $item): ?>
                                        <?php
                                        $rawItem   = trim((string) $item);
                                        $isItemUrl = ketarunaan_is_http_url($rawItem, 2000);
                                        $itemLabel = ketarunaan_format_bukti_label($rawItem);
                                        $itemHref  = $isItemUrl
                                            ? $rawItem
                                            : base_url('ketarunaan/prestasi/unduh-dokumen/' . $idPrestasi . '?jenis=foto&file=' . rawurlencode($rawItem) . '&inline=1');
                                        ?>
                                        <div class="ecc-file-item">
                                            <div class="d-flex align-items-center gap-2 text-truncate min-w-0">
                                                <i class="bi <?= $isItemUrl ? 'bi-link-45deg text-primary' : 'bi-file-earmark-image text-secondary' ?> flex-shrink-0" aria-hidden="true"></i>
                                                <div class="text-truncate min-w-0">
                                                    <div class="fw-medium text-dark text-truncate small"><?= esc($itemLabel) ?></div>
                                                    <?php if ($isItemUrl): ?>
                                                        <div class="text-secondary text-truncate" style="font-size: 0.75rem;" title="<?= esc($rawItem) ?>"><?= esc($rawItem) ?></div>
                                                    <?php endif; ?>
                                                </div>
                                            </div>
                                            <div class="d-flex align-items-center gap-2 flex-shrink-0">
                                                <a href="<?= esc($itemHref) ?>" target="_blank" rel="noopener noreferrer" class="btn btn-outline-secondary btn-sm rounded-3 px-3 btn-tactile pr-touch-ctrl d-inline-flex align-items-center">
                                                    <?= $isItemUrl ? 'Buka' : 'Pratinjau' ?>
                                                </a>
                                                <button type="button" class="btn btn-outline-danger btn-sm rounded-3 px-3 btn-hapus-foto btn-tactile pr-touch-ctrl" data-filename="<?= esc($rawItem) ?>" data-label="<?= esc($itemLabel) ?>" aria-label="Hapus dokumentasi <?= esc($itemLabel) ?>">Hapus</button>
                                            </div>
                                        </div>
                                    <?php endforeach; ?>
                                </div>
                            <?php endif; ?>
                        </div>
                    </form>
                </div>
            </div>

            <!-- Kolom Kanan: Pemeriksaan Pengajuan -->
            <div class="col-12 col-lg-5">
                <div class="ecc-panel-box h-100 d-flex flex-column justify-content-between">
                    <div>
                        <h3 class="h6 fw-bold text-dark mb-1">Pemeriksaan Pengajuan</h3>
                        <p class="text-secondary mb-3 small">Pastikan seluruh kelengkapan berikut sudah terpenuhi sebelum diajukan.</p>

                        <div class="d-flex flex-column gap-2 mb-3">
                            <div class="ecc-check-item">
                                <span class="fw-medium text-dark small">1. Informasi prestasi lengkap</span>
                                <?php if ($infoLengkap): ?>
                                    <span class="badge bg-success-subtle text-success-emphasis border border-success-subtle rounded-pill px-3 py-1">Lengkap</span>
                                <?php else: ?>
                                    <span class="badge bg-warning-subtle text-warning-emphasis border border-warning-subtle rounded-pill px-3 py-1">Belum Lengkap</span>
                                <?php endif; ?>
                            </div>

                            <div class="ecc-check-item">
                                <span class="fw-medium text-dark small">2. Minimal 1 peserta terhubung</span>
                                <?php if ($pesertaAda): ?>
                                    <span class="badge bg-success-subtle text-success-emphasis border border-success-subtle rounded-pill px-3 py-1 ecc-num"><?= $jumlahPeserta ?> peserta</span>
                                <?php else: ?>
                                    <span class="badge bg-danger-subtle text-danger-emphasis border border-danger-subtle rounded-pill px-3 py-1">Belum Ada</span>
                                <?php endif; ?>
                            </div>

                            <div class="ecc-check-item">
                                <span class="fw-medium text-dark small">3. Bukti utama tersedia</span>
                                <span id="badgeChecklistBukti" class="badge <?= $buktiAda ? 'bg-success-subtle text-success-emphasis border border-success-subtle' : 'bg-warning-subtle text-warning-emphasis border border-warning-subtle' ?> rounded-pill px-3 py-1">
                                    <?= $buktiAda ? 'Tersedia' : 'Belum Diisi' ?>
                                </span>
                            </div>
                        </div>

                        <?php if (!$isSubmitReady): ?>
                            <div class="alert alert-warning border-0 rounded-3 px-3 py-2 mb-3 small">
                                Lengkapi tahap informasi prestasi dan tambahkan minimal 1 peserta sebelum mengajukan validasi.
                            </div>
                        <?php endif; ?>

                        <div class="rounded-3 p-3 small" style="background-color: #eff6ff; border: 1px solid #bfdbfe; color: #1e3a8a;">
                            Data tervalidasi dihitung sebagai prestasi resmi dan tampil pada Rekam Jejak Taruna.
                        </div>
                    </div>

                    <div class="d-flex flex-column flex-sm-row gap-2 pt-3 mt-4 border-top">
                        <button type="submit" form="formDokumenBukti" formnovalidate name="aksi_submit" value="draf" class="btn btn-outline-secondary ecc-control-sm px-3 fw-semibold flex-fill btn-tactile pr-touch-ctrl js-submit-dok">
                            Simpan Draf
                        </button>
                        <button type="submit" form="formDokumenBukti" name="aksi_submit" value="diajukan" class="btn btn-primary ecc-control-sm px-3 fw-semibold flex-fill btn-tactile pr-touch-ctrl js-submit-dok">
                            Ajukan Validasi
                        </button>
                    </div>
                </div>
            </div>
        </div>

        <!-- Navigasi Bawah Tahap -->
        <div class="d-flex flex-wrap justify-content-between align-items-center gap-2 pt-3 mt-4 border-top">
            <a href="<?= base_url('ketarunaan/prestasi/peserta/' . $idPrestasi) ?>" class="btn btn-light border ecc-control-sm px-3 fw-semibold d-inline-flex align-items-center justify-content-center gap-2 btn-tactile pr-touch-ctrl">
                <i class="bi bi-arrow-left" aria-hidden="true"></i> Sebelumnya: Peserta
            </a>
            <a href="<?= base_url('ketarunaan/prestasi/detail/' . $idPrestasi) ?>" class="btn btn-outline-primary ecc-control-sm px-3 fw-semibold btn-tactile pr-touch-ctrl d-inline-flex align-items-center justify-content-center">
                Lihat Detail Prestasi
            </a>
        </div>
    </div>

    <!-- Form Terpisah Hapus Dokumentasi (Menghindari Nested Form) -->
    <form id="formHapusFoto" method="post" action="<?= base_url('ketarunaan/prestasi/dokumen/' . $idPrestasi . '/hapus-foto') ?>" class="d-none">
        <?= csrf_field() ?>
        <input type="hidden" name="filename" id="hapusFotoFilename" value="">
    </form>
</div>
<?= $this->endSection() ?>

<?= $this->section('scripts') ?>
<script>
document.addEventListener('DOMContentLoaded', function () {
    var inputNoSert   = document.getElementById('inputNomorSertifikat');
    var inputBukti    = document.getElementById('inputBuktiBerkas');
    var btnClearBukti = document.getElementById('btnBersihkanBukti');
    var badgeBukti    = document.getElementById('badgeChecklistBukti');
    var wrapDokLinks  = document.getElementById('wrapDokLinks');
    var btnAddDokRow  = document.getElementById('btnTambahTautanDok');
    var formDok       = document.getElementById('formDokumenBukti');

    var hasLegacyFile = <?= $hasLegacyFile ? 'true' : 'false' ?>;
    var infoLengkap   = <?= $infoLengkap ? 'true' : 'false' ?>;
    var pesertaAda    = <?= $pesertaAda ? 'true' : 'false' ?>;
    var maxSlot       = btnAddDokRow ? parseInt(btnAddDokRow.getAttribute('data-max-slot') || '0', 10) : 0;

    function isHttpUrl(val, maxLen) {
        var clean = (val || '').trim();
        return /^https?:\/\/\S+/i.test(clean) && clean.length <= maxLen;
    }

    function tampilPesan(judul, pesan) {
        if (typeof Swal !== 'undefined') {
            Swal.fire({
                title: judul,
                text: pesan,
                icon: 'warning',
                confirmButtonText: 'Tutup',
                confirmButtonColor: '#1e40af',
                customClass: { popup: 'rounded-4 shadow border-0' }
            });
        } else {
            alert(pesan);
        }
    }

    function syncBuktiState() {
        var val = inputBukti ? inputBukti.value.trim() : '';
        if (btnClearBukti) {
            btnClearBukti.classList.toggle('d-none', val === '');
        }
        if (badgeBukti) {
            var ready = isHttpUrl(val, 255) || (val === '' && hasLegacyFile);
            if (ready) {
                badgeBukti.className = 'badge bg-success-subtle text-success-emphasis border border-success-subtle rounded-pill px-3 py-1';
                badgeBukti.textContent = 'Tersedia';
            } else {
                badgeBukti.className = 'badge bg-warning-subtle text-warning-emphasis border border-warning-subtle rounded-pill px-3 py-1';
                badgeBukti.textContent = 'Belum Diisi';
            }
        }
    }

    if (inputBukti) {
        inputBukti.addEventListener('input', syncBuktiState);
        syncBuktiState();
    }
    if (btnClearBukti && inputBukti) {
        btnClearBukti.addEventListener('click', function () {
            inputBukti.value = '';
            syncBuktiState();
            inputBukti.focus();
        });
    }

    function syncAddRowBtn() {
        if (!btnAddDokRow || !wrapDokLinks) return;
        var count = wrapDokLinks.querySelectorAll('.dok-link-row').length;
        btnAddDokRow.disabled = (count >= maxSlot);
    }

    if (wrapDokLinks) {
        wrapDokLinks.addEventListener('click', function (e) {
            var rmBtn = e.target.closest('.btn-remove-dok-row');
            if (!rmBtn) return;
            var row = rmBtn.closest('.dok-link-row');
            if (row && wrapDokLinks.querySelectorAll('.dok-link-row').length > 1) {
                row.remove();
                syncAddRowBtn();
            }
        });
    }

    if (btnAddDokRow && wrapDokLinks) {
        btnAddDokRow.addEventListener('click', function () {
            var count = wrapDokLinks.querySelectorAll('.dok-link-row').length;
            if (count >= maxSlot) return;
            var div = document.createElement('div');
            div.className = 'input-group dok-link-row';
            div.innerHTML = ''
                + '<span class="input-group-text bg-white border-secondary-subtle text-secondary"><i class="bi bi-link-45deg" aria-hidden="true"></i></span>'
                + '<input type="url" name="dokumentasi_links[]" class="form-control ecc-control-sm" placeholder="https://drive.google.com/drive/folders/..." maxlength="500" aria-label="Tautan dokumentasi tambahan ' + (count + 1) + '">'
                + '<button type="button" class="btn btn-outline-secondary ecc-control-sm px-3 btn-remove-dok-row" aria-label="Hapus baris tautan"><i class="bi bi-x-lg" aria-hidden="true"></i></button>';
            wrapDokLinks.appendChild(div);
            syncAddRowBtn();
            var inp = div.querySelector('input');
            if (inp) inp.focus();
        });
        syncAddRowBtn();
    }

    var lastAction = 'draf';
    document.querySelectorAll('.js-submit-dok').forEach(function (btn) {
        btn.addEventListener('click', function () {
            lastAction = btn.value || 'draf';
        });
    });

    if (formDok) {
        formDok.addEventListener('submit', function (e) {
            var aksi = (e.submitter && e.submitter.value) ? e.submitter.value : lastAction;
            var noSert = inputNoSert ? inputNoSert.value.trim() : '';
            var rawBukti = inputBukti ? inputBukti.value.trim() : '';

            if (rawBukti !== '' && !isHttpUrl(rawBukti, 255)) {
                e.preventDefault();
                if (inputBukti) inputBukti.focus();
                tampilPesan('Tautan Tidak Valid', 'Tautan bukti utama harus diawali http:// atau https:// (maksimal 255 karakter).');
                return;
            }

            if (wrapDokLinks) {
                var dokInputs = wrapDokLinks.querySelectorAll('input[name="dokumentasi_links[]"]');
                for (var i = 0; i < dokInputs.length; i++) {
                    var v = dokInputs[i].value.trim();
                    if (v !== '' && !isHttpUrl(v, 500)) {
                        e.preventDefault();
                        dokInputs[i].focus();
                        tampilPesan('Tautan Dokumentasi Tidak Valid', 'Setiap tautan dokumentasi harus diawali http:// atau https:// (maksimal 500 karakter).');
                        return;
                    }
                }
            }

            if (aksi === 'diajukan') {
                if (!infoLengkap || !pesertaAda) {
                    e.preventDefault();
                    tampilPesan('Pengajuan Belum Lengkap', 'Lengkapi informasi prestasi dan tambahkan minimal 1 peserta sebelum mengajukan validasi.');
                    return;
                }
                if (noSert.length < 2) {
                    e.preventDefault();
                    if (inputNoSert) inputNoSert.focus();
                    tampilPesan('Nomor Sertifikat Wajib Diisi', 'Isi nomor sertifikat, piagam, atau SK sebelum mengajukan validasi.');
                    return;
                }
                if (rawBukti === '' && !hasLegacyFile) {
                    e.preventDefault();
                    if (inputBukti) inputBukti.focus();
                    tampilPesan('Tautan Bukti Wajib Diisi', 'Tempel tautan Google Drive bukti utama sebelum mengajukan validasi.');
                    return;
                }
            }

            document.querySelectorAll('.js-submit-dok').forEach(function (btn) {
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
            document.querySelectorAll('.js-submit-dok').forEach(function (btn) {
                btn.disabled = false;
            });
        });
    }

    var formHapusFoto = document.getElementById('formHapusFoto');
    var inputHapus    = document.getElementById('hapusFotoFilename');

    document.querySelectorAll('.btn-hapus-foto').forEach(function (btn) {
        btn.addEventListener('click', function () {
            var filename = this.getAttribute('data-filename') || '';
            var label    = this.getAttribute('data-label') || filename;
            if (!filename || !formHapusFoto || !inputHapus) return;
            inputHapus.value = filename;

            if (typeof Swal !== 'undefined') {
                Swal.fire({
                    title: 'Hapus Dokumentasi?',
                    text: 'Dokumentasi "' + label + '" akan dihapus.',
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
                        formHapusFoto.submit();
                    }
                });
            } else if (confirm('Dokumentasi "' + label + '" akan dihapus.')) {
                formHapusFoto.submit();
            }
        });
    });
});
</script>
<?= $this->endSection() ?>
