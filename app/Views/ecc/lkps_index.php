<?= $this->extend('layouts/main') ?>

<?= $this->section('title') ?><?= esc($page_title ?? 'LKPS') ?><?= $this->endSection() ?>

<?= $this->section('page_title') ?>
LKPS (Laporan Kinerja Program Studi)
<?= $this->endSection() ?>

<?= $this->section('content') ?>

<!-- Navigasi Tab untuk 3 Prodi -->
<ul class="nav nav-tabs" id="prodiTab" role="tablist">
    <?php $first = true; ?>
    <?php foreach($prodi_data as $prodi): ?>
        <li class="nav-item" role="presentation">
            <button class="nav-link <?= $first ? 'active' : '' ?>" id="tab-<?= esc($prodi['id_prodi']) ?>" data-bs-toggle="tab" data-bs-target="#content-<?= esc($prodi['id_prodi']) ?>" type="button" role="tab" aria-controls="content-<?= esc($prodi['id_prodi']) ?>" aria-selected="<?= $first ? 'true' : 'false' ?>">
                <?= esc($prodi['nama_prodi']) ?>
            </button>
        </li>
        <?php $first = false; ?>
    <?php endforeach; ?>
</ul>

<!-- Konten Tab -->
<div class="tab-content" id="prodiTabContent">
    <?php $first = true; ?>
    <?php foreach($prodi_data as $prodi): ?>
        <div class="tab-pane fade <?= $first ? 'show active' : '' ?> p-3" id="content-<?= esc($prodi['id_prodi']) ?>" role="tabpanel" aria-labelledby="tab-<?= esc($prodi['id_prodi']) ?>">
            
            <div class="card">
                <div class="card-header">
                    <h5>Data LKPS: <?= esc($prodi['nama_prodi']) ?></h5>
                </div>
                <div class="card-body">
                    <p><?= esc($prodi['lkps_content']) ?></p>
                    <!-- Di sini Anda bisa menambahkan tabel atau data spesifik untuk LKPS -->
                </div>
            </div>

        </div>
        <?php $first = false; ?>
    <?php endforeach; ?>
</div>

<?= $this->endSection() ?>