<?php
$activeTab         = $activeTab ?? 'daftar';
$perms             = $perms ?? [];
$canViewStaffBoard = !empty($canViewStaffBoard);
$canManage         = !empty($perms['can_manage']);
$canViewInternal   = !empty($perms['can_view_internal']);
$canValidate       = !empty($perms['can_validate']);

$tabs = [
    ['key' => 'daftar',     'label' => 'Daftar Lowongan', 'url' => base_url('ketarunaan/lowongan'),            'show' => true],
    ['key' => 'perusahaan', 'label' => 'Perusahaan Mitra', 'url' => base_url('ketarunaan/lowongan/perusahaan'), 'show' => $canViewStaffBoard],
    ['key' => 'dokumen',    'label' => 'Dokumen & Sumber', 'url' => base_url('ketarunaan/lowongan/dokumen'),    'show' => $canViewInternal],
    ['key' => 'verifikasi', 'label' => 'Verifikasi',       'url' => base_url('ketarunaan/lowongan/verifikasi'), 'show' => $canManage || $canValidate],
    ['key' => 'riwayat',    'label' => 'Riwayat & Tenggat', 'url' => base_url('ketarunaan/lowongan/riwayat'),   'show' => $canViewStaffBoard],
];

$activeLabel = 'Daftar Lowongan';
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
                <li class="breadcrumb-item"><a href="<?= base_url('ketarunaan/lowongan') ?>" class="text-decoration-none ecc-text-muted-aa">Lowongan Pekerjaan</a></li>
                <li class="breadcrumb-item active text-dark fw-semibold" aria-current="page"><?= esc($activeLabel) ?></li>
            </ol>
        </nav>
        <h1 class="h3 fw-bold text-dark mb-1">Lowongan Pekerjaan</h1>
        <p class="ecc-text-muted-aa mb-0 small">
            <?= $canViewStaffBoard
                ? 'Direktori lowongan kerja mitra industri, pemeriksaan sumber, verifikasi terpisah dari publikasi, dan kendali tenggat.'
                : 'Direktori lowongan kerja resmi terverifikasi untuk alumni dan taruna tingkat akhir.' ?>
        </p>
    </div>
    <?php if ($canManage): ?>
        <div class="d-flex flex-wrap align-items-center gap-2 flex-shrink-0">
            <?php if ($activeTab !== 'perusahaan'): ?>
                <a href="<?= base_url('ketarunaan/lowongan/perusahaan') ?>" class="btn btn-outline-primary ecc-control-sm px-3 fw-semibold d-inline-flex align-items-center justify-content-center gap-2 text-nowrap btn-tactile low-touch-ctrl">
                    Perusahaan Mitra
                </a>
            <?php endif; ?>
            <a href="<?= base_url('ketarunaan/lowongan/tambah') ?>" class="btn btn-primary ecc-control-sm px-3 fw-semibold d-inline-flex align-items-center justify-content-center gap-2 text-nowrap btn-tactile low-touch-ctrl shadow-sm">
                <i class="bi bi-plus-lg" aria-hidden="true"></i> Tambah Lowongan
            </a>
        </div>
    <?php endif; ?>
</div>

<?php if ($canViewStaffBoard): ?>
    <nav class="ecc-tab-nav" aria-label="Navigasi Modul Lowongan Pekerjaan">
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
<?php endif; ?>

<?php if (session()->getFlashdata('success')): ?>
    <div class="alert alert-success border-0 rounded-3 mb-3 py-2 px-3 d-flex align-items-center justify-content-between" role="alert">
        <span class="small"><?= esc((string) session()->getFlashdata('success')) ?></span>
        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Tutup"></button>
    </div>
<?php endif; ?>
<?php if (session()->getFlashdata('warning')): ?>
    <div class="alert alert-warning border-0 rounded-3 mb-3 py-2 px-3 d-flex align-items-center justify-content-between" role="alert">
        <span class="small"><?= esc((string) session()->getFlashdata('warning')) ?></span>
        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Tutup"></button>
    </div>
<?php endif; ?>
<?php if (session()->getFlashdata('error')): ?>
    <div class="alert alert-danger border-0 rounded-3 mb-3 py-2 px-3 d-flex align-items-center justify-content-between" role="alert">
        <span class="small"><?= esc((string) session()->getFlashdata('error')) ?></span>
        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Tutup"></button>
    </div>
<?php endif; ?>
