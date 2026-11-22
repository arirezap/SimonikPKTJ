<?php
$activeTab    = $activeTab ?? 'ringkasan';
$isSelfTaruna = !empty($isSelfTaruna);
$perms        = $perms ?? [];

$tabs = [
    ['key' => 'ringkasan',  'label' => 'Ringkasan',         'url' => base_url('ketarunaan/beasiswa'),            'show' => true],
    ['key' => 'program',    'label' => 'Program Beasiswa',  'url' => base_url('ketarunaan/beasiswa/program'),    'show' => true],
    ['key' => 'apresiasi',  'label' => 'Seleksi Apresiasi', 'url' => base_url('ketarunaan/beasiswa/apresiasi'),  'show' => !$isSelfTaruna],
    ['key' => 'klaim',      'label' => $isSelfTaruna ? 'Klaim Saya' : 'Klaim Beasiswa', 'url' => base_url('ketarunaan/beasiswa/klaim'), 'show' => true],
    ['key' => 'verifikasi', 'label' => 'Verifikasi',        'url' => base_url('ketarunaan/beasiswa/verifikasi'), 'show' => !$isSelfTaruna],
    ['key' => 'penerima',   'label' => $isSelfTaruna ? 'Riwayat & SK Saya' : 'Penerima & SK', 'url' => base_url('ketarunaan/beasiswa/penerima'), 'show' => true],
];

$activeLabel = 'Ringkasan';
foreach ($tabs as $t) {
    if ($t['key'] === $activeTab) {
        $activeLabel = $t['label'];
        break;
    }
}
?>

<div class="d-flex flex-column flex-md-row justify-content-between align-items-md-center gap-3 mb-3">
    <div>
        <nav aria-label="breadcrumb" class="mb-1">
            <ol class="breadcrumb mb-0" style="font-size: 0.75rem;">
                <li class="breadcrumb-item"><a href="<?= esc(ketarunaan_home_url()) ?>" class="text-decoration-none ecc-text-muted-aa">Ketarunaan</a></li>
                <li class="breadcrumb-item"><a href="<?= base_url('ketarunaan/beasiswa') ?>" class="text-decoration-none ecc-text-muted-aa">Beasiswa Taruna</a></li>
                <li class="breadcrumb-item active text-dark fw-semibold" aria-current="page"><?= esc($activeLabel) ?></li>
            </ol>
        </nav>
        <h1 class="h3 fw-bold text-dark mb-1">Beasiswa Taruna</h1>
        <p class="ecc-text-muted-aa mb-0 small">
            <?= $isSelfTaruna
                ? 'Informasi program beasiswa, pengajuan klaim mandiri, pemantauan verifikasi, dan status penetapan SK.'
                : 'Pengelolaan program, seleksi pemeringkatan IP semester, pengajuan klaim, verifikasi kelayakan, dan penetapan penerima resmi.' ?>
        </p>
    </div>
    <div class="d-flex flex-wrap align-items-center gap-2 flex-shrink-0">
        <?php if ($activeTab === 'program' && !empty($perms['can_manage'])): ?>
            <a href="<?= base_url('ketarunaan/beasiswa/kelola-program') ?>" class="btn btn-primary ecc-control-sm px-3 fw-semibold d-inline-flex align-items-center justify-content-center gap-2 text-nowrap btn-tactile bea-touch-ctrl shadow-sm">
                <i class="bi bi-plus-lg" aria-hidden="true"></i> Tambah Program
            </a>
        <?php else: ?>
            <?php if (!empty($perms['can_manage']) && $activeTab === 'ringkasan'): ?>
                <a href="<?= base_url('ketarunaan/beasiswa/kelola-program') ?>" class="btn btn-outline-primary ecc-control-sm px-3 fw-semibold d-inline-flex align-items-center justify-content-center gap-2 text-nowrap btn-tactile bea-touch-ctrl">
                    <i class="bi bi-plus-lg" aria-hidden="true"></i> Program Baru
                </a>
            <?php endif; ?>
            <?php if (!empty($perms['can_submit_claim']) && in_array($activeTab, ['ringkasan', 'klaim'], true)): ?>
                <a href="<?= base_url('ketarunaan/beasiswa/klaim/ajukan/prestasi') ?>" class="btn btn-primary ecc-control-sm px-3 fw-semibold d-inline-flex align-items-center justify-content-center gap-2 text-nowrap btn-tactile bea-touch-ctrl shadow-sm">
                    <i class="bi bi-plus-lg" aria-hidden="true"></i> Ajukan Klaim
                </a>
            <?php endif; ?>
        <?php endif; ?>
    </div>
</div>

<nav class="ecc-tab-nav" aria-label="Navigasi Modul Beasiswa">
    <?php foreach ($tabs as $tab): ?>
        <?php if ($tab['show']): ?>
            <?php $isCurrent = ($activeTab === $tab['key']); ?>
            <a href="<?= esc($tab['url']) ?>"
               class="ecc-tab-link <?= $isCurrent ? 'active' : '' ?>"<?= $isCurrent ? ' aria-current="page"' : '' ?>>
                <?= esc($tab['label']) ?>
            </a>
        <?php endif; ?>
    <?php endforeach; ?>
</nav>

<?php if (session()->getFlashdata('success')): ?>
    <div class="alert alert-success border-0 rounded-3 mb-4 py-2 px-3 d-flex align-items-center justify-content-between" role="alert">
        <span class="small"><?= esc((string) session()->getFlashdata('success')) ?></span>
        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Tutup"></button>
    </div>
<?php endif; ?>
<?php if (session()->getFlashdata('warning')): ?>
    <div class="alert alert-warning border-0 rounded-3 mb-4 py-2 px-3 d-flex align-items-center justify-content-between" role="alert">
        <span class="small"><?= esc((string) session()->getFlashdata('warning')) ?></span>
        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Tutup"></button>
    </div>
<?php endif; ?>
<?php if (session()->getFlashdata('error')): ?>
    <div class="alert alert-danger border-0 rounded-3 mb-4 py-2 px-3 d-flex align-items-center justify-content-between" role="alert">
        <span class="small"><?= esc((string) session()->getFlashdata('error')) ?></span>
        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Tutup"></button>
    </div>
<?php endif; ?>
