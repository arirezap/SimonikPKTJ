<?= $this->extend('layouts/ecc2_layout') ?>

<?= $this->section('title') ?>Kelola Peserta Prestasi<?= $this->endSection() ?>

<?= $this->section('styles') ?>
<link rel="stylesheet" href="<?= base_url('assets/vendor/select2/select2.min.css') ?>">
<link rel="stylesheet" href="<?= base_url('assets/vendor/select2/select2-bootstrap-5-theme.min.css') ?>">
<?= $this->include('ketarunaan/prestasi/_styles') ?>
<?= $this->endSection() ?>

<?= $this->section('content') ?>
<?php
$detail     = $detail ?? [];
$prestasi   = $detail['prestasi'] ?? [];
$peserta    = $detail['peserta'] ?? [];
$refs       = $refs ?? [];
$tarunaList = $tarunaList ?? [];

$idPrestasi  = (int) ($prestasi['id'] ?? 0);
$rawStatus   = (string) ($prestasi['status_validasi'] ?? 'draf');
$tingkatLbl  = $refs['tingkat_catalog'][$prestasi['tingkat_prestasi'] ?? ''] ?? ucfirst((string) ($prestasi['tingkat_prestasi'] ?? '-'));
$existingNo  = array_column($peserta, 'nomor_taruna');
$hideTambahPeserta = !empty($isSelfTaruna) && (($prestasi['jenis_keikutsertaan'] ?? '') === 'individu');
?>

<div class="container-fluid px-0">
    <!-- Header Halaman -->
    <div class="d-flex flex-column flex-md-row justify-content-between align-items-md-center gap-3 mb-4">
        <div>
            <nav aria-label="breadcrumb" class="mb-1">
                <ol class="breadcrumb mb-0" style="font-size: 0.75rem;">
                    <li class="breadcrumb-item"><a href="<?= esc(ketarunaan_home_url()) ?>" class="text-decoration-none text-secondary">Ketarunaan</a></li>
                    <li class="breadcrumb-item"><a href="<?= base_url('ketarunaan/prestasi') ?>" class="text-decoration-none text-secondary">Prestasi</a></li>
                    <li class="breadcrumb-item active text-dark fw-semibold" aria-current="page">Kelola Peserta Prestasi</li>
                </ol>
            </nav>
            <h1 class="h3 fw-bold text-dark mb-1">Kelola Peserta Prestasi</h1>
            <p class="text-secondary mb-0 small">Kelola daftar taruna yang terhubung pada prestasi ini.</p>
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
            Prestasi ini sudah tervalidasi. Menambah atau menghapus peserta akan mengembalikan status ke <strong>Draf</strong> untuk diajukan validasi ulang.
        </div>
    <?php endif; ?>

    <!-- Kontainer Tahap 2 -->
    <div class="bento-card ecc-pr-card pr-form-scope">
        <nav class="ecc-wizard-nav" aria-label="Tahapan Pengisian Prestasi">
            <a href="<?= base_url('ketarunaan/prestasi/edit/' . $idPrestasi) ?>" class="ecc-wizard-step">1. Informasi Prestasi</a>
            <span class="ecc-wizard-step active" aria-current="step">2. Peserta</span>
            <a href="<?= base_url('ketarunaan/prestasi/dokumen/' . $idPrestasi) ?>" class="ecc-wizard-step">3. Dokumen Bukti</a>
        </nav>

        <div class="d-flex flex-column flex-md-row justify-content-between align-items-md-center gap-3 mb-3">
            <div>
                <h3 class="h6 fw-bold text-dark mb-1 ecc-num"><?= count($peserta) ?> peserta</h3>
                <p class="text-secondary mb-0 small">Satu prestasi dicatat satu kali dan dihubungkan dengan seluruh peserta.</p>
            </div>
            <?php if (!$hideTambahPeserta): ?>
                <button type="button" class="btn btn-primary ecc-control-sm px-3 fw-semibold btn-tactile pr-touch-ctrl d-inline-flex align-items-center justify-content-center gap-1" data-bs-toggle="collapse" data-bs-target="#panelTambahPeserta" aria-expanded="true">
                    <i class="bi bi-plus-lg" aria-hidden="true"></i> Tambah Peserta
                </button>
            <?php endif; ?>
        </div>

        <?php if (!$hideTambahPeserta): ?>
        <!-- Form Tambah Peserta -->
        <div class="collapse show mb-4" id="panelTambahPeserta">
            <form method="post" action="<?= base_url('ketarunaan/prestasi/peserta/' . $idPrestasi . '/tambah') ?>" id="formTambahPeserta" class="border rounded-3 p-3 bg-light">
                <?= csrf_field() ?>
                <div class="row g-2 align-items-end">
                    <div class="col-12 col-md-7">
                        <label for="selectNomorTaruna" class="form-label text-secondary fw-semibold mb-1" style="font-size: 0.75rem;">Pilih Taruna <span class="text-danger">*</span></label>
                        <select id="selectNomorTaruna" name="nomor_taruna" class="form-select ecc-control-sm" required>
                            <option value="">-- Pilih Nomor Taruna &amp; Nama --</option>
                            <?php foreach ($tarunaList as $tr): ?>
                                <?php
                                $sudahAda = in_array($tr['nomor_taruna'], $existingNo, true);
                                $angTr    = !empty($tr['angkatan'])
                                    ? ketarunaan_format_angkatan([
                                        'tahun_masuk' => $tr['angkatan'],
                                        'kode_romawi' => $tr['angkatan_romawi'] ?? '',
                                    ])
                                    : '-';
                                $labelOpt = $tr['nomor_taruna'] . ' - ' . $tr['nama_lengkap'] . ' (' . ($tr['kode_prodi'] ?? '-') . ' / ' . $angTr . ')';
                                ?>
                                <option value="<?= esc($tr['nomor_taruna']) ?>"<?= $sudahAda ? ' disabled' : '' ?>>
                                    <?= esc($labelOpt) ?><?= $sudahAda ? ' [Sudah Terdaftar]' : '' ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    <div class="col-6 col-md-3">
                        <label for="selectPeran" class="form-label text-secondary fw-semibold mb-1" style="font-size: 0.75rem;">Peran</label>
                        <?php
                        $isTimMode    = (($prestasi['jenis_keikutsertaan'] ?? 'individu') === 'tim');
                        $sudahKetua   = in_array('Ketua Tim', array_column($peserta, 'peran'), true);
                        $peranChoices = $isTimMode
                            ? ['Anggota', 'Ketua Tim']
                            : ['Peserta Utama'];
                        $defaultPeran = $isTimMode
                            ? ($sudahKetua ? 'Anggota' : 'Ketua Tim')
                            : 'Peserta Utama';
                        ?>
                        <select id="selectPeran" name="peran" class="form-select ecc-control-sm">
                            <?php foreach ($peranChoices as $prn): ?>
                                <option value="<?= esc($prn) ?>" <?= ($prn === $defaultPeran) ? 'selected' : '' ?>>
                                    <?= esc($prn) ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    <div class="col-6 col-md-2">
                        <button type="submit" class="btn btn-primary ecc-control-sm w-100 fw-semibold btn-tactile pr-touch-ctrl" id="btnTambahPeserta">Tambahkan</button>
                    </div>
                </div>
            </form>
        </div>
        <?php endif; ?>

        <!-- Tabel Peserta Desktop -->
        <div class="table-responsive ecc-pr-scroll d-none d-md-block mb-3">
            <table class="table table-hover ecc-table-pr mb-0" data-sortable-table="true">
                <thead>
                    <tr>
                        <th aria-sort="none">
                            <button type="button" class="pr-sort-btn" data-sort-col="0" data-sort-type="text">
                                <span>Nomor Taruna</span><i class="bi bi-arrow-down-up text-muted opacity-50 sort-indicator" aria-hidden="true"></i>
                            </button>
                        </th>
                        <th aria-sort="none">
                            <button type="button" class="pr-sort-btn" data-sort-col="1" data-sort-type="text">
                                <span>Nama</span><i class="bi bi-arrow-down-up text-muted opacity-50 sort-indicator" aria-hidden="true"></i>
                            </button>
                        </th>
                        <th aria-sort="none">
                            <button type="button" class="pr-sort-btn" data-sort-col="2" data-sort-type="text">
                                <span>Prodi</span><i class="bi bi-arrow-down-up text-muted opacity-50 sort-indicator" aria-hidden="true"></i>
                            </button>
                        </th>
                        <th aria-sort="none">
                            <button type="button" class="pr-sort-btn" data-sort-col="3" data-sort-type="text">
                                <span>Angkatan</span><i class="bi bi-arrow-down-up text-muted opacity-50 sort-indicator" aria-hidden="true"></i>
                            </button>
                        </th>
                        <th aria-sort="none">
                            <button type="button" class="pr-sort-btn" data-sort-col="4" data-sort-type="text">
                                <span>Peran</span><i class="bi bi-arrow-down-up text-muted opacity-50 sort-indicator" aria-hidden="true"></i>
                            </button>
                        </th>
                        <th class="text-end">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (empty($peserta)): ?>
                        <tr class="pr-empty-row">
                            <td colspan="6" class="text-center py-4 text-secondary">Belum ada peserta terdaftar pada prestasi ini.</td>
                        </tr>
                    <?php else: ?>
                        <?php foreach ($peserta as $ps): ?>
                            <?php
                            $angPs = !empty($ps['angkatan'])
                                ? ketarunaan_format_angkatan([
                                    'tahun_masuk' => $ps['angkatan'],
                                    'kode_romawi' => $ps['angkatan_romawi'] ?? '',
                                ])
                                : '-';
                            $isSelfPesertaRow = !empty($isSelfTaruna) && !empty($ownNomorTaruna) && strcasecmp((string) $ps['nomor_taruna'], (string) $ownNomorTaruna) === 0;
                            $isKetua = (($ps['peran'] ?? '') === 'Ketua Tim');
                            ?>
                            <tr>
                                <td class="fw-semibold ecc-num" data-sort-val="<?= esc((string) $ps['nomor_taruna']) ?>"><?= esc((string) $ps['nomor_taruna']) ?></td>
                                <td class="fw-semibold text-dark" data-sort-val="<?= esc(strtolower((string) $ps['nama_lengkap'])) ?>"><?= esc((string) $ps['nama_lengkap']) ?></td>
                                <td data-sort-val="<?= esc(strtolower((string) ($ps['kode_prodi'] ?? ''))) ?>"><?= esc((string) ($ps['kode_prodi'] ?? '-')) ?></td>
                                <td class="ecc-num" data-sort-val="<?= esc((string) ($ps['angkatan'] ?? '')) ?>"><?= esc($angPs) ?></td>
                                <td data-sort-val="<?= esc(strtolower((string) ($ps['peran'] ?? ''))) ?>">
                                    <span class="badge rounded-pill px-3 py-1 fw-semibold <?= $isKetua ? 'bg-primary-subtle text-primary-emphasis border border-primary-subtle' : 'bg-light text-dark border' ?>" style="font-size: 0.75rem;">
                                        <?= esc((string) $ps['peran']) ?>
                                    </span>
                                </td>
                                <td class="text-end text-nowrap">
                                    <?php if ($isSelfPesertaRow): ?>
                                        <span class="badge bg-light text-secondary border rounded-pill px-3 py-1" style="font-size: 0.75rem;">Akun Saya</span>
                                    <?php else: ?>
                                        <form method="post" action="<?= base_url('ketarunaan/prestasi/peserta/' . $idPrestasi . '/hapus/' . (int) $ps['peserta_id']) ?>" class="d-inline form-hapus-peserta">
                                            <?= csrf_field() ?>
                                            <button type="submit" class="btn btn-outline-danger btn-sm rounded-3 px-3 btn-tactile" title="Hapus Peserta" aria-label="Hapus peserta <?= esc((string) $ps['nama_lengkap']) ?>" data-nama="<?= esc((string) $ps['nama_lengkap']) ?>">
                                                <i class="bi bi-trash" aria-hidden="true"></i> Hapus
                                            </button>
                                        </form>
                                    <?php endif; ?>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>

        <!-- Kartu Mobile Peserta -->
        <div class="d-md-none d-flex flex-column gap-2 mb-3">
            <?php if (empty($peserta)): ?>
                <div class="text-center py-4 text-secondary small">Belum ada peserta terdaftar pada prestasi ini.</div>
            <?php else: ?>
                <?php foreach ($peserta as $ps): ?>
                    <?php
                    $angPs = !empty($ps['angkatan'])
                        ? ketarunaan_format_angkatan([
                            'tahun_masuk' => $ps['angkatan'],
                            'kode_romawi' => $ps['angkatan_romawi'] ?? '',
                        ])
                        : '-';
                    $isSelfPesertaRow = !empty($isSelfTaruna) && !empty($ownNomorTaruna) && strcasecmp((string) $ps['nomor_taruna'], (string) $ownNomorTaruna) === 0;
                    $isKetua = (($ps['peran'] ?? '') === 'Ketua Tim');
                    ?>
                    <div class="border rounded-3 p-3 bg-white">
                        <div class="d-flex justify-content-between align-items-start gap-2 mb-2">
                            <div>
                                <div class="fw-bold text-dark small"><?= esc((string) $ps['nama_lengkap']) ?></div>
                                <div class="text-secondary ecc-num" style="font-size: 0.75rem;">
                                    <?= esc((string) $ps['nomor_taruna']) ?> &bull; <?= esc((string) ($ps['kode_prodi'] ?? '-')) ?> &bull; <?= esc($angPs) ?>
                                </div>
                            </div>
                            <span class="badge rounded-pill px-3 py-1 fw-semibold <?= $isKetua ? 'bg-primary-subtle text-primary-emphasis border border-primary-subtle' : 'bg-light text-dark border' ?>" style="font-size: 0.75rem;">
                                <?= esc((string) $ps['peran']) ?>
                            </span>
                        </div>
                        <div class="pt-2 border-top">
                            <?php if ($isSelfPesertaRow): ?>
                                <span class="badge bg-light text-secondary border rounded-pill px-3 py-1" style="font-size: 0.75rem;">Akun Saya</span>
                            <?php else: ?>
                                <form method="post" action="<?= base_url('ketarunaan/prestasi/peserta/' . $idPrestasi . '/hapus/' . (int) $ps['peserta_id']) ?>" class="form-hapus-peserta">
                                    <?= csrf_field() ?>
                                    <button type="submit" class="btn btn-outline-danger btn-sm rounded-3 px-3 w-100 fw-semibold btn-tactile pr-touch-ctrl" aria-label="Hapus peserta <?= esc((string) $ps['nama_lengkap']) ?>" data-nama="<?= esc((string) $ps['nama_lengkap']) ?>">
                                        <i class="bi bi-trash" aria-hidden="true"></i> Hapus Peserta
                                    </button>
                                </form>
                            <?php endif; ?>
                        </div>
                    </div>
                <?php endforeach; ?>
            <?php endif; ?>
        </div>

        <div class="px-3 py-2 rounded-3 border bg-danger-subtle border-danger-subtle text-danger-emphasis mb-4 d-flex align-items-center gap-2 small">
            <i class="bi bi-exclamation-circle-fill" aria-hidden="true"></i>
            <span>Nomor Taruna yang sama tidak dapat ditambahkan dua kali.</span>
        </div>

        <div class="d-flex flex-wrap justify-content-between align-items-center gap-2 pt-3 border-top">
            <a href="<?= base_url('ketarunaan/prestasi/edit/' . $idPrestasi) ?>" class="btn btn-light border ecc-control-sm px-3 fw-semibold d-inline-flex align-items-center justify-content-center gap-2 btn-tactile pr-touch-ctrl">
                <i class="bi bi-arrow-left" aria-hidden="true"></i> Sebelumnya: Informasi Prestasi
            </a>
            <div class="d-flex flex-wrap gap-2">
                <a href="<?= base_url('ketarunaan/prestasi/detail/' . $idPrestasi) ?>" class="btn btn-outline-primary ecc-control-sm px-3 fw-semibold btn-tactile pr-touch-ctrl d-inline-flex align-items-center justify-content-center">Simpan &amp; Lihat Detail</a>
                <a href="<?= base_url('ketarunaan/prestasi/dokumen/' . $idPrestasi) ?>" class="btn btn-primary ecc-control-sm px-3 fw-semibold d-inline-flex align-items-center justify-content-center gap-2 btn-tactile pr-touch-ctrl">
                    Berikutnya: Dokumen Bukti <i class="bi bi-arrow-right" aria-hidden="true"></i>
                </a>
            </div>
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
        var selectEl = window.jQuery('#selectNomorTaruna');
        if (selectEl.length) {
            selectEl.select2({
                theme: 'bootstrap-5',
                width: '100%',
                placeholder: '-- Pilih Nomor Taruna & Nama --'
            });
        }
    }

    document.querySelectorAll('table[data-sortable-table="true"]').forEach(function (table) {
        var buttons = table.querySelectorAll('thead .pr-sort-btn');
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

                var rows = Array.from(tbody.querySelectorAll('tr:not(.pr-empty-row)'));
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

    var formTambah = document.getElementById('formTambahPeserta');
    if (formTambah) {
        formTambah.addEventListener('submit', function (e) {
            var sel = document.getElementById('selectNomorTaruna');
            if (sel && sel.value.trim() === '') {
                e.preventDefault();
                if (typeof Swal !== 'undefined') {
                    Swal.fire({
                        icon: 'warning',
                        title: 'Pilih Taruna',
                        text: 'Pilih taruna terlebih dahulu sebelum menambahkan peserta.',
                        confirmButtonText: 'Tutup',
                        confirmButtonColor: '#1e40af',
                        customClass: { popup: 'rounded-4 shadow border-0' }
                    });
                } else {
                    alert('Pilih taruna terlebih dahulu.');
                }
                return;
            }
            var btn = document.getElementById('btnTambahPeserta');
            if (btn) btn.disabled = true;
        });
    }

    document.querySelectorAll('.form-hapus-peserta').forEach(function (form) {
        form.addEventListener('submit', function (e) {
            if (form.dataset.confirmed === '1') return;
            e.preventDefault();
            var btn  = form.querySelector('button[type="submit"]');
            var nama = btn ? (btn.getAttribute('data-nama') || 'peserta ini') : 'peserta ini';

            var submitNow = function () {
                form.dataset.confirmed = '1';
                if (btn) btn.disabled = true;
                form.submit();
            };

            if (typeof Swal !== 'undefined') {
                Swal.fire({
                    title: 'Hapus Peserta?',
                    text: 'Peserta ' + nama + ' akan dihapus dari prestasi ini.',
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
            } else if (confirm('Peserta ' + nama + ' akan dihapus dari prestasi ini.')) {
                submitNow();
            }
        });
    });

    window.addEventListener('pageshow', function () {
        var btnTambah = document.getElementById('btnTambahPeserta');
        if (btnTambah) btnTambah.disabled = false;
        document.querySelectorAll('.form-hapus-peserta button[type="submit"]').forEach(function (b) {
            b.disabled = false;
        });
    });
});
</script>
<?= $this->endSection() ?>
