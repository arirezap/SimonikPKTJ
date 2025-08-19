<?= $this->extend('layouts/main') ?>

<?= $this->section('page_title') ?>Data Ketarunaan<?= $this->endSection() ?>
<?= $this->section('title') ?><?= esc($page_title ?? 'Data Ketarunaan') ?><?= $this->endSection() ?>

<?= $this->section('content') ?>
<div class="card">
    <div class="card-header d-flex justify-content-between">
        <h5>Catatan Prestasi & Pelanggaran</h5>
        <button class="btn btn-success btn-sm"><i class="bi bi-plus-circle"></i> Tambah Catatan</button>
    </div>
    <div class="card-body">
        <div class="table-responsive">
            <table class="table table-hover">
                <thead>
                    <tr>
                        <th>Nama Taruna</th>
                        <th>Tingkat</th>
                        <th>Jenis Catatan</th>
                        <th>Keterangan</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach($catatan_taruna as $catatan): ?>
                    <tr>
                        <td><?= esc($catatan['nama']) ?></td>
                        <td><?= esc($catatan['tingkat']) ?></td>
                        <td>
                            <span class="badge bg-<?= $catatan['jenis'] == 'Prestasi' ? 'success' : 'danger' ?>">
                                <?= esc($catatan['jenis']) ?>
                            </span>
                        </td>
                        <td><?= esc($catatan['keterangan']) ?></td>
                    </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>
<?= $this->endSection() ?>
