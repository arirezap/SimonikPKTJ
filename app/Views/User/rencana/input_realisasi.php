<?= $this->extend('layouts/main') ?>

<?= $this->section('title') ?><?= esc($page_title) ?><?= $this->endSection() ?>

<?= $this->section('content') ?>
<h1 class="mb-2">Input Realisasi Kinerja</h1>
<h4 class="text-muted mb-4">Periode: <?= esc($nama_bulan_sekarang) . ' ' . esc($tahun_sekarang) ?></h4>

<?php if (session()->getFlashdata('success')): ?>
    <div class="alert alert-success alert-dismissible fade show" role="alert">
        <?= session()->getFlashdata('success') ?>
        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
    </div>
<?php endif; ?>
<?php if (session()->getFlashdata('error')): ?>
    <div class="alert alert-danger alert-dismissible fade show" role="alert">
        <?= session()->getFlashdata('error') ?>
        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
    </div>
<?php endif; ?>

<div class="card">
    <div class="card-header">
        <h5>Formulir Input Realisasi Bulan Ini</h5>
    </div>
    <div class="card-body">
        <form action="<?= site_url('user/realisasi/store') ?>" method="POST">
            <?= csrf_field() ?>
            <div class="table-responsive">
                <table class="table table-bordered align-middle">
                    <thead class="table-light">
                        <tr>
                            <th style="width: 5%;">No</th>
                            <th>Indikator Kinerja</th>
                            <th class="text-center" style="width: 20%;">Target Bulan Ini</th>
                            <th class="text-center" style="width: 20%;">Realisasi Bulan Ini</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if (!empty($rencana_kinerja)): ?>
                            <?php $no = 1; foreach ($rencana_kinerja as $row): ?>
                                <?php
                                    $target_bulanan = json_decode($row['target_bulanan'], true);
                                    $realisasi_bulanan = json_decode($row['realisasi_bulanan'], true);
                                    $target_bulan_ini = $target_bulanan[$bulan_sekarang - 1] ?? 0;
                                    $realisasi_bulan_ini = $realisasi_bulanan[$bulan_sekarang - 1] ?? '';
                                ?>
                                <tr>
                                    <td class="text-center"><?= $no++ ?></td>
                                    <td><?= esc($row['indikator_kinerja']) ?></td>
                                    <td class="text-center fw-bold"><?= esc($target_bulan_ini) . ' ' . esc($row['satuan']) ?></td>
                                    <td>
                                        <input type="number" min="0" step="any" name="realisasi[<?= $row['id'] ?>]" class="form-control" value="<?= esc($realisasi_bulan_ini) ?>" placeholder="Input Realisasi">
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        <?php else: ?>
                            <tr>
                                <td colspan="4" class="text-center p-4">
                                    <p class="mb-1">Belum ada data Rencana Kerja untuk tahun ini.</p>
                                    <a href="<?= site_url('user/rencana/input') ?>">Buat Rencana Kerja Baru</a>
                                </td>
                            </tr>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
            <?php if (!empty($rencana_kinerja)): ?>
                <button type="submit" class="btn btn-primary mt-3 float-end"><i class="bi bi-save"></i> Simpan Realisasi</button>
            <?php endif; ?>
        </form>
    </div>
</div>
<?= $this->endSection() ?>
