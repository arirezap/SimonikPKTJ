<?php
$activeTab    = $activeTab ?? 'ringkasan';
$perms        = $perms ?? [];
$isSelfAlumni = !empty($isSelfAlumni) || !empty($perms['is_self_alumni']);
$canManage    = !empty($perms['can_manage']);
$canAnalytic  = !empty($perms['can_view_analytic']);
$selectedPid  = (int) ($selectedPid ?? 0);

$pidQuery = $selectedPid > 0 ? ('?periode_id=' . $selectedPid) : '';
$tabs = [
    ['key' => 'ringkasan', 'label' => 'Ringkasan & Hasil', 'url' => base_url('ketarunaan/tracer-study' . $pidQuery),          'show' => $canAnalytic],
    ['key' => 'periode',   'label' => 'Periode & Kontrol', 'url' => base_url('ketarunaan/tracer-study/periode'),              'show' => $canAnalytic],
    ['key' => 'bundel',    'label' => 'Bundel Pertanyaan', 'url' => base_url('ketarunaan/tracer-study/bundel'),               'show' => $canAnalytic],
    ['key' => 'responden', 'label' => 'Progres & Responden', 'url' => base_url('ketarunaan/tracer-study/responden' . $pidQuery), 'show' => $canAnalytic],
];

$activeLabel = 'Ringkasan & Hasil';
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
                <li class="breadcrumb-item"><a href="<?= base_url('ketarunaan/tracer-study') ?>" class="text-decoration-none ecc-text-muted-aa">Tracer Study</a></li>
                <?php if (!$isSelfAlumni): ?>
                    <li class="breadcrumb-item active text-dark fw-semibold" aria-current="page"><?= esc($activeLabel) ?></li>
                <?php endif; ?>
            </ol>
        </nav>
        <h1 class="h3 fw-bold text-dark mb-1">Tracer Study</h1>
        <p class="ecc-text-muted-aa mb-0 small">
            <?= $isSelfAlumni
                ? 'Isi survei penelusuran lulusan pada periode yang sedang dibuka untuk mendukung pengembangan kampus.'
                : 'Pengelolaan bundel pertanyaan, kontrol buka tutup survei, dan pemantauan progres serta hasil per periode.' ?>
        </p>
    </div>

    <?php if (!$isSelfAlumni && $canAnalytic): ?>
        <div class="d-flex flex-wrap align-items-center gap-2 flex-shrink-0">
            <?php if ($canManage && $activeTab === 'periode'): ?>
                <button type="button" class="btn btn-primary ecc-control-sm px-3 fw-semibold d-inline-flex align-items-center justify-content-center gap-2 text-nowrap btn-tactile ts-touch-ctrl shadow-sm" data-bs-toggle="modal" data-bs-target="#modalPeriodeTracer" onclick="resetPeriodeModal()">
                    <i class="bi bi-plus-lg" aria-hidden="true"></i> Buat Periode
                </button>
            <?php elseif ($canManage && $activeTab === 'bundel'): ?>
                <button type="button" class="btn btn-primary ecc-control-sm px-3 fw-semibold d-inline-flex align-items-center justify-content-center gap-2 text-nowrap btn-tactile ts-touch-ctrl shadow-sm" data-bs-toggle="modal" data-bs-target="#modalBundleTracer" onclick="resetBundleModal()">
                    <i class="bi bi-plus-lg" aria-hidden="true"></i> Buat Bundel
                </button>
            <?php elseif ($activeTab === 'ringkasan'): ?>
                <?php if ($selectedPid > 0): ?>
                    <a href="<?= base_url('ketarunaan/tracer-study/ekspor?periode_id=' . $selectedPid) ?>" class="btn btn-outline-secondary ecc-control-sm px-3 fw-semibold d-inline-flex align-items-center justify-content-center gap-2 text-nowrap btn-tactile ts-touch-ctrl">
                        <i class="bi bi-download" aria-hidden="true"></i> Ekspor CSV
                    </a>
                <?php endif; ?>
                <?php if ($canManage): ?>
                    <a href="<?= base_url('ketarunaan/tracer-study/periode') ?>" class="btn btn-primary ecc-control-sm px-3 fw-semibold d-inline-flex align-items-center justify-content-center gap-2 text-nowrap btn-tactile ts-touch-ctrl shadow-sm">
                        Kelola Periode
                    </a>
                <?php endif; ?>
            <?php endif; ?>
        </div>
    <?php endif; ?>
</div>

<?php if (!$isSelfAlumni && $canAnalytic): ?>
    <nav class="ecc-tab-nav" aria-label="Navigasi Modul Tracer Study">
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
