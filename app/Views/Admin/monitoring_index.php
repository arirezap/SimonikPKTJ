<?= $this->extend('layouts/main') ?>

<?= $this->section('title') ?><?= esc($page_title ?? 'Monitoring Kinerja') ?><?= $this->endSection() ?>

<?= $this->section('content') ?>
<h1 class="mb-4">Monitoring Kinerja Tim/Unit/Pokja</h1>

<?php if (session()->getFlashdata('error')): ?>
    <div class="alert alert-danger"><?= session()->getFlashdata('error') ?></div>
<?php endif; ?>

<div class="card">
    <div class="card-header">
        <h5>Pilih Tim/Unit/Pokja dan Tahun</h5>
    </div>
    <div class="card-body">
        <form id="monitoringForm" method="GET">
            <div class="row g-3 align-items-end">
                <div class="col-md-5">
                    <label for="unit" class="form-label">Pilih Tim/Unit/Pokja</label>
                    <select class="form-select" id="unit" name="unit" required>
                        <option value="" disabled selected>-- Pilih salah satu --</option>
                        <?php foreach ($unit_pokja as $unit): ?>
                            <option value="<?= $unit['id']; ?>"><?= esc($unit['nama_lengkap']); ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>
                <div class="col-md-5">
                    <label for="tahun" class="form-label">Pilih Tahun Anggaran</label>
                    <select class="form-select" id="tahun" name="tahun" required>
                         <?php foreach ($daftar_tahun as $item): ?>
                            <option value="<?= $item['tahun_anggaran']; ?>"><?= esc($item['tahun_anggaran']); ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>
                <div class="col-md-2">
                    <button type="submit" class="btn btn-primary w-100">Lihat Detail</button>
                </div>
            </div>
        </form>
    </div>
</div>
<?= $this->endSection() ?>

<?= $this->section('scripts') ?>
<script>
    // Script ini akan mengambil nilai dari form dan membuat URL yang benar
    document.getElementById('monitoringForm').addEventListener('submit', function(e) {
        e.preventDefault();
        const unitId = document.getElementById('unit').value;
        const tahun = document.getElementById('tahun').value;
        if (unitId && tahun) {
            window.location.href = `<?= site_url('admin/monitoring/detail/') ?>${unitId}/${tahun}`;
        } else {
            alert('Silakan pilih Tim/Unit/Pokja dan Tahun terlebih dahulu.');
        }
    });
</script>
<?= $this->endSection() ?>
