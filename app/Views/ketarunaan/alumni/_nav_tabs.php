<?php
$activeTab    = $activeTab ?? 'direktori';
$perms        = $perms ?? [];
$isSelfOnly   = !empty($isSelfOnly) || !empty($perms['is_self_only']);
$canViewStaff = !empty($perms['can_view_staff']);
$canManage    = !empty($perms['can_manage']);

$tabs = [
    ['key' => 'direktori', 'label' => 'Direktori Alumni',   'url' => base_url('ketarunaan/alumni'),           'show' => true],
    ['key' => 'kelulusan', 'label' => 'Validasi Kelulusan', 'url' => base_url('ketarunaan/alumni/kelulusan'), 'show' => $canViewStaff],
    ['key' => 'koreksi',   'label' => 'Koreksi Kelulusan',  'url' => base_url('ketarunaan/alumni/koreksi'),   'show' => $canViewStaff],
];

$activeLabel = 'Direktori Alumni';
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
                <li class="breadcrumb-item"><a href="<?= base_url('ketarunaan/alumni') ?>" class="text-decoration-none ecc-text-muted-aa">Alumni</a></li>
                <li class="breadcrumb-item active text-dark fw-semibold" aria-current="page"><?= esc($activeLabel) ?></li>
            </ol>
        </nav>
        <h1 class="h3 fw-bold text-dark mb-1">Alumni</h1>
        <p class="ecc-text-muted-aa mb-0 small">
            <?= $isSelfOnly
                ? 'Profil dasar alumni terhubung dengan Nomor Taruna serta pembaruan kontak dan pekerjaan Anda.'
                : 'Direktori profil dasar lulusan tervalidasi, pembaruan kontak terlacak, dan koreksi kelulusan terkendali.' ?>
        </p>
    </div>
    <?php if ($canViewStaff && !$isSelfOnly): ?>
        <div class="d-flex flex-wrap align-items-center gap-2 flex-shrink-0">
            <?php if ($activeTab !== 'kelulusan'): ?>
                <a href="<?= base_url('ketarunaan/alumni/kelulusan') ?>" class="btn btn-outline-primary ecc-control-sm px-3 fw-semibold d-inline-flex align-items-center justify-content-center text-nowrap btn-tactile alm-touch-ctrl">
                    Antrean Kelulusan
                </a>
            <?php endif; ?>
            <?php if ($canManage && $activeTab !== 'koreksi'): ?>
                <a href="<?= base_url('ketarunaan/alumni/koreksi') ?>" class="btn btn-primary ecc-control-sm px-3 fw-semibold d-inline-flex align-items-center justify-content-center text-nowrap btn-tactile alm-touch-ctrl shadow-sm">
                    Ajukan Koreksi
                </a>
            <?php endif; ?>
        </div>
    <?php endif; ?>
</div>

<?php if ($canViewStaff && !$isSelfOnly): ?>
    <nav class="ecc-tab-nav" aria-label="Navigasi Modul Alumni">
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
