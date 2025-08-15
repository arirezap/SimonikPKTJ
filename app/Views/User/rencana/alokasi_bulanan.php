<?= $this->extend('layouts/main') ?>
    
<?= $this->section('title') ?><?= esc($page_title ?? 'Kelola Rencana') ?><?= $this->endSection() ?>

<?= $this->section('styles') ?>
<style>
    /* Style untuk memperjelas tab yang aktif */
    .nav-tabs .nav-link {
        border-bottom-width: 0;
        color: #6c757d;
    }
    .nav-tabs .nav-link.active {
        background-color: #f8f9fa;
        border-color: #dee2e6 #dee2e6 #f8f9fa;
        color: #0d6efd;
        font-weight: bold;
    }
    /* Memberi background dan border pada konten tab */
    .tab-content {
        background-color: #f8f9fa;
        border-radius: 0 0.375rem 0.375rem 0.375rem;
    }
</style>
<?= $this->endSection() ?>

<?= $this->section('content') ?>
<h1>Kelola Target & Realisasi Bulanan <?= esc($tahun_terpilih) ?></h1>
<?php if (session()->getFlashdata('success')): ?>
<div class="alert alert-success alert-dismissible fade show" role="alert">
    <?= session()->getFlashdata('success') ?>
    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
</div>
<?php endif; ?>

<div class="card">
    <div class="card-body">
        <form action="<?= site_url('user/alokasi/update') ?>" method="POST">
            <?= csrf_field() ?>
            <input type="hidden" name="tahun" value="<?= esc($tahun_terpilih) ?>">

            <?php if(!empty($rencana_kinerja)): ?>
                
                <!-- Navigasi Tab untuk Bulan -->
                <ul class="nav nav-tabs" id="bulanTab" role="tablist">
                    <?php 
                        $bulan_sekarang = date('n');
                        $tahun_sekarang = date('Y');
                        for ($i=1; $i<=12; $i++): 
                    ?>
                        <li class="nav-item" role="presentation">
                            <button class="nav-link <?= ($i == $bulan_sekarang && $tahun_terpilih == $tahun_sekarang) ? 'active' : '' ?>" id="tab-<?= $i ?>" data-bs-toggle="tab" data-bs-target="#konten-<?= $i ?>" type="button" role="tab" aria-controls="konten-<?= $i ?>" aria-selected="<?= ($i == $bulan_sekarang && $tahun_terpilih == $tahun_sekarang) ? 'true' : 'false' ?>">
                                <!-- PERUBAHAN: Menggunakan helper bulan_indo() -->
                                <?= bulan_indo($i) ?>
                            </button>
                        </li>
                    <?php endfor; ?>
                </ul>

                <!-- Konten untuk Setiap Tab -->
                <div class="tab-content p-3 border border-top-0" id="bulanTabContent">
                    <?php for ($i=0; $i<12; $i++): ?>
                        <?php
                            $bulan_index = $i + 1;
                            $is_future_month = ($tahun_terpilih > $tahun_sekarang) || ($tahun_terpilih == $tahun_sekarang && $bulan_index > $bulan_sekarang);
                            $readonly_attr = $is_future_month ? 'readonly' : '';
                            $placeholder_text = $is_future_month ? 'Belum Waktunya' : '0';
                            $bg_class = $is_future_month ? 'bg-light' : '';
                        ?>
                        <div class="tab-pane fade <?= ($bulan_index == $bulan_sekarang && $tahun_terpilih == $tahun_sekarang) ? 'show active' : '' ?>" id="konten-<?= $bulan_index ?>" role="tabpanel" aria-labelledby="tab-<?= $bulan_index ?>">
                            <div class="table-responsive">
                                <table class="table table-hover">
                                    <thead class="table-light">
                                        <tr>
                                            <th>Indikator Kinerja</th>
                                            <th class="text-center" style="width: 20%;">Target</th>
                                            <th class="text-center" style="width: 20%;">Realisasi</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php foreach($rencana_kinerja as $row): ?>
                                            <?php 
                                                $target_bulanan = json_decode($row['target_bulanan'], true);
                                                $realisasi_bulanan = json_decode($row['realisasi_bulanan'], true);
                                            ?>
                                            <tr>
                                                <td>
                                                    <div class="fw-bold"><?= esc($row['indikator_kinerja']) ?></div>
                                                    <small class="text-muted">Target Tahunan: <?= esc($row['target_utama']) . ' ' . esc($row['satuan']) ?></small>
                                                    <input type="hidden" name="indikator_kinerja[<?= $row['id'] ?>]" value="<?= esc($row['indikator_kinerja']); ?>">
                                                    <input type="hidden" name="target_utama[<?= $row['id'] ?>]" value="<?= esc($row['target_utama']); ?>">
                                                </td>
                                                <td>
                                                    <input type="number" min="0" name="target_bulanan[<?= $row['id'] ?>][<?= $i ?>]" class="form-control" value="<?= $target_bulanan[$i] ?? 0 ?>">
                                                </td>
                                                <td>
                                                    <input type="number" min="0" name="realisasi_bulanan[<?= $row['id'] ?>][<?= $i ?>]" class="form-control <?= $bg_class ?>" value="<?= $realisasi_bulanan[$i] ?? '' ?>" placeholder="<?= $placeholder_text ?>" <?= $readonly_attr ?>>
                                                </td>
                                            </tr>
                                        <?php endforeach; ?>
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    <?php endfor; ?>
                </div>
                
                <div class="text-end mt-4">
                    <button type="submit" id="simpanAlokasi" class="btn btn-primary"><i class="bi bi-save"></i> Simpan Semua Perubahan</button>
                </div>
            <?php else: ?>
                <div class="text-center p-4">
                    <p class="mb-1">Tidak ada data rencana untuk tahun ini.</p>
                    <a href="<?= site_url('user/rencana/input?tahun='.$tahun_terpilih) ?>">Buat Rencana Kerja untuk Tahun <?= esc($tahun_terpilih) ?></a>
                </div>
            <?php endif; ?>
        </form>
    </div>
</div>
<?= $this->endSection() ?>
