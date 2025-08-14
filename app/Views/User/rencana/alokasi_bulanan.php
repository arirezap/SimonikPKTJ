<?= $this->extend('layouts/main') ?>
    
<?= $this->section('title') ?><?= esc($page_title ?? 'Kelola Rencana') ?><?= $this->endSection() ?>

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

            <div class="table-responsive">
                <table class="table table-bordered align-middle">
                    <thead class="table-light text-center">
                        <tr>
                            <th rowspan="2">Indikator Kinerja</th>
                            <th rowspan="2" style="width:12%">Target Tahunan</th>
                            <?php for ($i=1; $i<=12; $i++) { echo "<th style='width:5%'>".substr(date("F",mktime(0,0,0,$i,10)),0,3)."</th>"; } ?>
                            <th rowspan="2" style="width:8%">Total Alokasi</th>
                            <th rowspan="2" style="width:8%">Sisa Alokasi</th>
                        </tr>
                        <tr>
                            <th colspan="12">Target & Realisasi Bulanan</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if(!empty($rencana_kinerja)): ?>
                            <?php foreach($rencana_kinerja as $row): ?>
                            <?php 
                                $target_bulanan = json_decode($row['target_bulanan'], true);
                                $realisasi_bulanan = json_decode($row['realisasi_bulanan'], true);
                            ?>
                            <tr class="baris-alokasi">
                                <td rowspan="2"><input type="text" name="indikator_kinerja[<?= $row['id'] ?>]" class="form-control" value="<?= esc($row['indikator_kinerja']); ?>"></td>
                                <td rowspan="2"><input type="number" min="0" name="target_utama[<?= $row['id'] ?>]" class="form-control target-tahunan text-center fw-bold" value="<?= esc($row['target_utama']); ?>"></td>
                                
                                <!-- Baris untuk TARGET Bulanan -->
                                <?php for ($i=0; $i<12; $i++): ?>
                                <td><input type="number" min="0" name="target_bulanan[<?= $row['id'] ?>][<?= $i ?>]" class="form-control input-bulanan-target" value="<?= $target_bulanan[$i] ?? 0 ?>" placeholder="Target"></td>
                                <?php endfor; ?>
                                
                                <td rowspan="2" class="total-bulanan text-center fw-bold bg-light">0</td>
                                <td rowspan="2" class="sisa-alokasi text-center fw-bold bg-light">0</td>
                            </tr>
                            <tr class="baris-alokasi-realisasi">
                                <!-- Baris untuk REALISASI Bulanan -->
                                <?php for ($i=0; $i<12; $i++): ?>
                                <td><input type="number" min="0" name="realisasi_bulanan[<?= $row['id'] ?>][<?= $i ?>]" class="form-control input-bulanan-realisasi" value="<?= $realisasi_bulanan[$i] ?? '' ?>" placeholder="Realisasi"></td>
                                <?php endfor; ?>
                            </tr>
                            <?php endforeach; ?>
                        <?php else: ?>
                            <tr><td colspan="16" class="text-center">Tidak ada data rencana untuk tahun ini.</td></tr>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
            <button type="submit" id="simpanAlokasi" class="btn btn-primary mt-3 float-end"><i class="bi bi-save"></i> Simpan Semua Perubahan</button>
        </form>
    </div>
</div>
<?= $this->endSection() ?>

<?= $this->section('scripts') ?>
<script>
document.addEventListener('DOMContentLoaded', function () {
    const semuaBaris = document.querySelectorAll('.baris-alokasi');
    const tombolSimpan = document.getElementById('simpanAlokasi');

    function hitungAlokasi(baris) {
        const targetTahunan = parseFloat(baris.querySelector('.target-tahunan').value) || 0;
        const inputBulanan = baris.querySelectorAll('.input-bulanan-target');
        let totalAlokasi = 0;

        inputBulanan.forEach(input => {
            const nilai = parseFloat(input.value) || 0;
            totalAlokasi += Math.max(0, nilai);
        });

        const sisa = targetTahunan - totalAlokasi;
        const selTotal = baris.querySelector('.total-bulanan');
        const selSisa = baris.querySelector('.sisa-alokasi');

        selTotal.textContent = totalAlokasi;
        selSisa.textContent = sisa;

        if (sisa < 0) {
            selTotal.classList.add('text-danger');
            selSisa.classList.add('text-danger');
            return false;
        } else {
            selTotal.classList.remove('text-danger');
            selSisa.classList.remove('text-danger');
            return true;
        }
    }

    function validasiSemuaBaris() {
        let semuaValid = true;
        semuaBaris.forEach(baris => {
            if (!hitungAlokasi(baris)) {
                semuaValid = false;
            }
        });
        tombolSimpan.disabled = !semuaValid;
    }

    semuaBaris.forEach(baris => {
        baris.querySelectorAll('.input-bulanan-target, .target-tahunan').forEach(input => {
            input.addEventListener('input', validasiSemuaBaris);
        });
    });

    document.querySelectorAll('input[type="number"]').forEach(input => {
        input.addEventListener('input', function() {
            if (this.value < 0) this.value = 0;
        });
    });

    validasiSemuaBaris();
});
</script>
<?= $this->endSection() ?>
