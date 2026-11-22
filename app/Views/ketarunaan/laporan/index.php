<?= $this->extend('layouts/ecc2_layout') ?>

<?= $this->section('title') ?>Laporan Ketarunaan<?= $this->endSection() ?>

<?= $this->section('styles') ?>
<?= $this->include('ketarunaan/_governance_styles') ?>
<?= $this->endSection() ?>

<?= $this->section('content') ?>
<?php
$catalog      = $catalog ?? [];
$payload      = $payload ?? [];
$meta         = $payload['meta'] ?? ['kode' => 'status_pendidikan', 'label' => 'Laporan', 'satuan' => '-', 'ringkasan' => ''];
$jenisNow     = (string) ($payload['jenis'] ?? 'status_pendidikan');
$tanggalAcuan = (string) ($payload['tanggal_acuan'] ?? date('Y-m-d'));
$prodiId      = $payload['prodi_id'] ?? null;
$angkatanId   = $payload['angkatan_id'] ?? null;
$columns      = $payload['columns'] ?? [];
$rows         = $payload['rows'] ?? [];
$badges       = $payload['ringkasan_badges'] ?? [];

$hasFilter = (!empty($prodiId)) || (!empty($angkatanId)) || ($tanggalAcuan !== date('Y-m-d'));

$exportUrl = site_url('ketarunaan/laporan/ekspor') . '?' . http_build_query([
    'jenis'         => $jenisNow,
    'tanggal_acuan' => $tanggalAcuan,
    'prodi_id'      => $prodiId ?? '',
    'angkatan_id'   => $angkatanId ?? '',
]);
?>
<div class="container-fluid px-0">

    <!-- Header Halaman -->
    <div class="d-flex flex-column flex-lg-row align-items-lg-center justify-content-between gap-3 mb-3">
        <div>
            <nav aria-label="breadcrumb" class="mb-1">
                <ol class="breadcrumb mb-0" style="font-size: 0.75rem;">
                    <li class="breadcrumb-item"><a href="<?= esc(ketarunaan_home_url()) ?>" class="text-decoration-none ecc-text-muted-aa">Ketarunaan</a></li>
                    <li class="breadcrumb-item active text-dark fw-semibold" aria-current="page">Laporan</li>
                </ol>
            </nav>
            <h1 class="h3 fw-bold text-dark mb-1">Laporan Lintas Modul</h1>
            <p class="ecc-text-muted-aa mb-0 small">Pratinjau dan unduh laporan resmi dengan angka serta satuan yang sama persis seperti Dashboard</p>
        </div>
        <div class="d-flex flex-wrap gap-2 flex-shrink-0">
            <a href="<?= site_url('ketarunaan/dashboard?tanggal_acuan=' . urlencode($tanggalAcuan)) ?>" class="btn btn-light border ecc-control-sm px-3 fw-semibold d-inline-flex align-items-center justify-content-center text-nowrap btn-tactile gov-touch-ctrl">
                Kembali ke Ringkasan
            </a>
            <a href="<?= esc($exportUrl) ?>" class="btn btn-primary ecc-control-sm px-4 fw-semibold d-inline-flex align-items-center justify-content-center gap-2 text-nowrap btn-tactile gov-touch-ctrl shadow-sm">
                <i class="bi bi-download" aria-hidden="true"></i> <span>Unduh CSV Resmi</span>
            </a>
        </div>
    </div>

    <?php if (session()->getFlashdata('error')): ?>
        <div class="alert alert-danger border-0 rounded-3 mb-3 py-2 px-3 d-flex align-items-center justify-content-between" role="alert">
            <span class="small"><?= esc((string) session()->getFlashdata('error')) ?></span>
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Tutup"></button>
        </div>
    <?php endif; ?>

    <!-- Filter Laporan -->
    <div class="ecc-gov-card mb-3 py-3">
        <form method="get" action="<?= site_url('ketarunaan/laporan') ?>" class="row g-2 align-items-end gov-filter-form">
            <div class="col-12 col-md-4 col-lg-3">
                <label for="laporanJenis" class="form-label fw-semibold ecc-text-muted-aa mb-1" style="font-size: 0.75rem;">Jenis Laporan</label>
                <select id="laporanJenis" name="jenis" class="form-select ecc-control-sm" onchange="this.form.submit()">
                    <?php foreach ($catalog as $kode => $item): ?>
                        <option value="<?= esc($kode) ?>" <?= $jenisNow === $kode ? 'selected' : '' ?>>
                            <?= esc($item['label']) ?>
                        </option>
                    <?php endforeach; ?>
                </select>
            </div>
            <div class="col-12 col-md-4 col-lg-3">
                <label for="laporanProdi" class="form-label fw-semibold ecc-text-muted-aa mb-1" style="font-size: 0.75rem;">Program Studi</label>
                <select id="laporanProdi" name="prodi_id" class="form-select ecc-control-sm">
                    <option value="">Semua Program Studi</option>
                    <?php foreach ($prodi_list as $p): ?>
                        <option value="<?= esc($p['id']) ?>" <?= ((int) $prodiId === (int) $p['id']) ? 'selected' : '' ?>>
                            <?= esc($p['kode_prodi']) ?> - <?= esc($p['nama_prodi']) ?>
                        </option>
                    <?php endforeach; ?>
                </select>
            </div>
            <div class="col-6 col-md-4 col-lg-2">
                <label for="laporanAngkatan" class="form-label fw-semibold ecc-text-muted-aa mb-1" style="font-size: 0.75rem;">Angkatan</label>
                <select id="laporanAngkatan" name="angkatan_id" class="form-select ecc-control-sm">
                    <option value="">Semua Angkatan</option>
                    <?php foreach ($angkatan_list as $a): ?>
                        <option value="<?= esc($a['id']) ?>" <?= ((int) $angkatanId === (int) $a['id']) ? 'selected' : '' ?>>
                            Angkatan <?= esc(ketarunaan_format_angkatan($a)) ?>
                        </option>
                    <?php endforeach; ?>
                </select>
            </div>
            <div class="col-6 col-md-4 col-lg-2">
                <label for="laporanTanggalAcuan" class="form-label fw-semibold ecc-text-muted-aa mb-1" style="font-size: 0.75rem;">Tanggal Acuan</label>
                <input type="text" id="laporanTanggalAcuan" name="tanggal_acuan" class="form-control ecc-control-sm ecc-num" value="<?= esc($tanggalAcuan) ?>">
            </div>
            <div class="col-12 col-md-8 col-lg-2 d-flex flex-wrap justify-content-lg-end gap-2">
                <button type="submit" class="btn btn-outline-primary ecc-control-sm px-3 fw-semibold btn-tactile gov-touch-ctrl text-nowrap">Terapkan</button>
                <?php if ($hasFilter): ?>
                    <a href="<?= site_url('ketarunaan/laporan?jenis=' . urlencode($jenisNow)) ?>" class="btn btn-light border ecc-control-sm px-3 ecc-text-muted-aa d-inline-flex align-items-center justify-content-center btn-tactile gov-touch-ctrl text-nowrap">
                        Reset
                    </a>
                <?php endif; ?>
            </div>
        </form>
    </div>

    <!-- Ringkasan Kepala Laporan & Definisi Satuan -->
    <div class="ecc-gov-card mb-3">
        <div class="row g-3 align-items-center">
            <div class="col-12 col-lg-7">
                <h2 class="h5 fw-bold text-dark mb-1"><?= esc($meta['label']) ?></h2>
                <p class="ecc-text-muted-aa small mb-2"><?= esc($meta['ringkasan']) ?></p>
                <div class="d-flex flex-wrap gap-2 small ecc-text-muted-aa">
                    <span><strong class="text-dark">Satuan:</strong> <?= esc($meta['satuan']) ?></span>
                    <span>&bull;</span>
                    <span class="ecc-num"><strong class="text-dark">Acuan:</strong> <?= esc(ketarunaan_format_tanggal($tanggalAcuan)) ?></span>
                    <span>&bull;</span>
                    <span><strong class="text-dark">Lingkup:</strong> <?= esc((string) $payload['prodi_label']) ?>, <?= esc((string) $payload['angkatan_label']) ?></span>
                </div>
                <?php if (!empty($payload['catatan_operasi'])): ?>
                    <div class="ecc-text-muted-aa mt-2" style="font-size: 0.75rem;">Keterangan: <?= esc((string) $payload['catatan_operasi']) ?></div>
                <?php endif; ?>
            </div>
            <div class="col-12 col-lg-5">
                <div class="p-3 rounded-4 bg-light border d-flex flex-column gap-2">
                    <div class="d-flex align-items-center justify-content-between">
                        <span class="small fw-semibold ecc-text-muted-aa">Total Angka Utama</span>
                        <span class="h4 fw-bold text-primary mb-0 ecc-num"><?= number_format((int) ($payload['dashboard_count'] ?? 0)) ?></span>
                    </div>
                    <?php if (!empty($badges)): ?>
                        <div class="d-flex flex-wrap gap-2 pt-2 border-top">
                            <?php foreach ($badges as $b): ?>
                                <span class="badge rounded-pill <?= esc($b['class']) ?> px-3 py-1 fw-semibold ecc-num" style="font-size: 0.75rem;">
                                    <?= esc($b['label']) ?>: <?= number_format((int) $b['nilai']) ?>
                                </span>
                            <?php endforeach; ?>
                        </div>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </div>

    <!-- Tabel Pratinjau Laporan Desktop -->
    <div class="ecc-gov-card p-0 overflow-hidden d-none d-md-block">
        <div class="p-3 p-md-4 border-bottom d-flex flex-wrap align-items-center justify-content-between gap-2">
            <div>
                <h2 class="h6 fw-bold text-dark mb-0">Pratinjau Rincian Data</h2>
                <span class="ecc-text-muted-aa small">Data sensitif pribadi disaring otomatis sesuai kewenangan peran akun Anda.</span>
            </div>
            <span class="badge rounded-pill bg-light text-dark border px-3 py-1 ecc-num" style="font-size: 0.75rem;"><?= count($rows) ?> baris</span>
        </div>
        <div class="ecc-table-scroll">
            <table class="table ecc-table-gov table-hover mb-0" data-sortable-table="true">
                <thead>
                    <tr>
                        <?php foreach ($columns as $cIdx => $col): ?>
                            <th aria-sort="none">
                                <button type="button" class="gov-sort-btn" data-sort-col="<?= (int) $cIdx ?>">
                                    <span><?= esc($col) ?></span>
                                    <i class="bi bi-arrow-down-up text-muted opacity-50 sort-indicator" aria-hidden="true"></i>
                                </button>
                            </th>
                        <?php endforeach; ?>
                    </tr>
                </thead>
                <tbody>
                    <?php if (empty($rows)): ?>
                        <tr>
                            <td colspan="<?= max(1, count($columns)) ?>" class="text-center ecc-text-muted-aa py-4">Tidak ada data yang memenuhi kriteria filter laporan.</td>
                        </tr>
                    <?php else: ?>
                        <?php foreach ($rows as $r): ?>
                            <tr>
                                <?php foreach ($r as $idx => $cell): ?>
                                    <td class="<?= $idx === 0 ? 'ecc-num ecc-text-muted-aa fw-semibold' : '' ?>"><?= esc((string) $cell) ?></td>
                                <?php endforeach; ?>
                            </tr>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>

    <!-- Kartu Mobile Pratinjau Laporan -->
    <div class="d-md-none d-flex flex-column gap-3">
        <div class="d-flex justify-content-between align-items-center">
            <h2 class="h6 fw-bold text-dark mb-0">Pratinjau Rincian Data</h2>
            <span class="badge rounded-pill bg-light text-dark border px-3 py-1 ecc-num" style="font-size: 0.75rem;"><?= count($rows) ?> baris</span>
        </div>
        <?php if (empty($rows)): ?>
            <div class="ecc-gov-card text-center ecc-text-muted-aa py-4 small">Tidak ada data yang memenuhi kriteria filter laporan.</div>
        <?php else: ?>
            <?php $mobileRows = array_slice($rows, 0, 50); ?>
            <?php foreach ($mobileRows as $r): ?>
                <div class="ecc-gov-card p-3">
                    <div class="fw-bold text-dark mb-2">
                        <?= esc((string) ($r[2] ?? ($r[1] ?? '-'))) ?>
                        <?php if (!empty($r[1]) && !empty($r[2])): ?>
                            <div class="ecc-text-muted-aa ecc-num fw-normal" style="font-size: 0.75rem;"><?= esc((string) $r[1]) ?></div>
                        <?php endif; ?>
                    </div>
                    <div class="d-flex flex-column gap-1 small">
                        <?php foreach ($columns as $cIdx => $colName): ?>
                            <?php if ($cIdx === 0) { continue; } ?>
                            <div class="d-flex justify-content-between gap-2">
                                <span class="ecc-text-muted-aa"><?= esc($colName) ?></span>
                                <span class="text-dark fw-medium text-end ecc-num"><?= esc((string) ($r[$cIdx] ?? '-')) ?></span>
                            </div>
                        <?php endforeach; ?>
                    </div>
                </div>
            <?php endforeach; ?>
            <?php if (count($rows) > 50): ?>
                <div class="ecc-gov-card p-3 text-center small ecc-text-muted-aa">
                    Menampilkan 50 baris pertama di layar ponsel. Gunakan tombol <strong>Unduh CSV Resmi</strong> untuk melihat seluruh <?= count($rows) ?> baris.
                </div>
            <?php endif; ?>
        <?php endif; ?>
    </div>

</div>
<?= $this->endSection() ?>

<?= $this->section('scripts') ?>
<script>
    document.addEventListener('DOMContentLoaded', function () {
        var el = document.getElementById('laporanTanggalAcuan');
        if (el && typeof flatpickr !== 'undefined') {
            flatpickr(el, {
                dateFormat: 'Y-m-d',
                altInput: true,
                altFormat: 'd M Y',
                defaultDate: '<?= esc($tanggalAcuan) ?>',
                locale: (flatpickr.l10ns && flatpickr.l10ns.id) ? flatpickr.l10ns.id : 'default'
            });
        }

        document.querySelectorAll('table[data-sortable-table="true"]').forEach(function (table) {
            var headers = table.querySelectorAll('thead th');
            headers.forEach(function (th) {
                var btn = th.querySelector('.gov-sort-btn');
                if (!btn) return;
                btn.addEventListener('click', function () {
                    var colIdx = parseInt(btn.getAttribute('data-sort-col') || '0', 10);
                    var currentSort = th.getAttribute('aria-sort') || 'none';
                    var nextSort = currentSort === 'ascending' ? 'descending' : 'ascending';

                    headers.forEach(function (h) {
                        if (h.hasAttribute('aria-sort')) {
                            h.setAttribute('aria-sort', 'none');
                            var icon = h.querySelector('.sort-indicator');
                            if (icon) icon.className = 'bi bi-arrow-down-up text-muted opacity-50 sort-indicator';
                        }
                    });
                    th.setAttribute('aria-sort', nextSort);
                    var activeIcon = th.querySelector('.sort-indicator');
                    if (activeIcon) {
                        activeIcon.className = nextSort === 'ascending'
                            ? 'bi bi-sort-up text-primary sort-indicator'
                            : 'bi bi-sort-down text-primary sort-indicator';
                    }

                    var tbody = table.querySelector('tbody');
                    if (!tbody) return;
                    var rows = Array.prototype.slice.call(tbody.querySelectorAll('tr'));
                    if (rows.length <= 1 && rows[0] && rows[0].querySelector('td[colspan]')) return;

                    rows.sort(function (a, b) {
                        var cellA = a.children[colIdx];
                        var cellB = b.children[colIdx];
                        var valA = (cellA ? (cellA.textContent || '') : '').trim();
                        var valB = (cellB ? (cellB.textContent || '') : '').trim();
                        var numA = parseFloat(valA);
                        var numB = parseFloat(valB);
                        var cmp = (!isNaN(numA) && !isNaN(numB) && String(numA) === valA && String(numB) === valB)
                            ? (numA - numB)
                            : valA.localeCompare(valB, 'id', { numeric: true, sensitivity: 'base' });
                        return nextSort === 'ascending' ? cmp : -cmp;
                    });
                    rows.forEach(function (r) { tbody.appendChild(r); });
                });
            });
        });
    });
</script>
<?= $this->endSection() ?>
