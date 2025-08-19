<?= $this->extend('layouts/main') ?>

<?= $this->section('page_title') ?>Data Diklat<?= $this->endSection() ?>
<?= $this->section('title') ?><?= esc($page_title ?? 'Data Diklat') ?><?= $this->endSection() ?>

<?= $this->section('content') ?>
<div class="card">
    <div class="card-header d-flex justify-content-between">
        <h5>Program Diklat</h5>
        <button class="btn btn-success btn-sm"><i class="bi bi-plus-circle"></i> Tambah Program Diklat</button>
    </div>
    <div class="card-body">
        <div class="table-responsive">
            <table class="table table-hover">
                <thead>
                    <tr>
                        <th>Nama Program</th>
                        <th>Periode</th>
                        <th>Jumlah Peserta</th>
                        <th>Status</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach($daftar_diklat as $diklat): ?>
                    <tr>
                        <td><?= esc($diklat['nama']) ?></td>
                        <td><?= esc($diklat['periode']) ?></td>
                        <td><?= esc($diklat['jumlah_peserta']) ?></td>
                        <td>
                            <?php
                                $status_class = 'bg-secondary';
                                if ($diklat['status'] == 'Berjalan') $status_class = 'bg-primary';
                                if ($diklat['status'] == 'Selesai') $status_class = 'bg-success';
                            ?>
                            <span class="badge <?= $status_class ?>"><?= esc($diklat['status']) ?></span>
                        </td>
                    </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>
<?= $this->endSection() ?>
