<?= $this->extend('layouts/main') ?>

<?= $this->section('title') ?><?= esc($page_title ?? 'Kelola Rencana Kinerja') ?><?= $this->endSection() ?>

<?= $this->section('content') ?>
<div class="d-flex justify-content-between align-items-center mb-4">
    <h1>Kelola Rencana Kinerja</h1>
    <a href="<?= site_url('user/rencana/input') ?>" class="btn btn-success"><i class="bi bi-plus-circle me-2"></i> Buat Rencana Baru</a>
</div>

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

<!-- Form Filter Tahun -->
<div class="card mb-4">
    <div class="card-body">
        <form method="GET" class="row g-3 align-items-end">
            <div class="col-md-5">
                <label for="tahun" class="form-label">Pilih Tahun Rencana</label>
                <select class="form-select" id="tahun" name="tahun" onchange="this.form.submit()">
                    <option value="">-- Tampilkan Semua Tahun --</option>
                    <?php foreach ($daftar_tahun as $item): ?>
                        <option value="<?= $item['tahun_anggaran']; ?>" <?= ($tahun_terpilih == $item['tahun_anggaran']) ? 'selected' : ''; ?>>
                            Tahun Anggaran <?= esc($item['tahun_anggaran']); ?>
                        </option>
                    <?php endforeach; ?>
                </select>
            </div>
        </form>
    </div>
</div>

<!-- Tabel Detail Rencana -->
<?php if ($tahun_terpilih): ?>
<div class="card">
    <div class="card-header">
        <h5>Detail Rencana Kinerja Tahun <?= esc($tahun_terpilih) ?></h5>
    </div>
    <div class="card-body">
        <div class="table-responsive">
            <table class="table table-hover align-middle">
                <thead class="table-light">
                    <tr>
                        <th style="width: 5%;">No</th>
                        <th>Indikator Kinerja</th>
                        <th class="text-center">Target Tahunan</th>
                        <th class="text-center">Capaian Realisasi</th>
                        <th class="text-center">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (!empty($rencana_kinerja)): ?>
                        <?php $no = 1; foreach ($rencana_kinerja as $row): ?>
                            <?php
                                // --- CALCULATE REALIZATION PROGRESS ---
                                $realisasi_bulanan = json_decode($row['realisasi_bulanan'], true) ?? [];
                                $total_realisasi = array_sum($realisasi_bulanan);
                                $target_utama = (float)$row['target_utama'];
                                $persentase_capaian = 0;
                                if ($target_utama > 0) {
                                    // PERUBAHAN: Menghapus fungsi min()
                                    $persentase_capaian = ($total_realisasi / $target_utama) * 100;
                                }
                                // Untuk tampilan progress bar, tetap batasi di 100% agar tidak aneh
                                $progress_bar_width = min(100, $persentase_capaian);
                            ?>
                            <tr>
                                <td class="text-center"><?= $no++; ?></td>
                                <td>
                                    <div class="fw-bold"><?= esc($row['sasaran_program']); ?></div>
                                    <small class="text-muted"><?= esc($row['indikator_kinerja']); ?></small>
                                </td>
                                <td class="text-center fs-5"><?= esc($row['target_utama']); ?> <span class="fs-6 text-muted"><?= esc($row['satuan']); ?></span></td>
                                
                                <td>
                                    <div class="d-flex align-items-center">
                                        <div class="progress flex-grow-1" style="height: 20px;">
                                            <div class="progress-bar" role="progressbar" style="width: <?= $progress_bar_width ?>%;" aria-valuenow="<?= $persentase_capaian ?>" aria-valuemin="0" aria-valuemax="100">
                                                <?= round($persentase_capaian) ?>%
                                            </div>
                                        </div>
                                        <span class="ms-2 fw-bold"><?= $total_realisasi ?></span>
                                    </div>
                                </td>

                                <td class="text-center">
                                    <a href="<?= site_url('user/alokasi/bulanan?tahun=' . $row['tahun_anggaran']) ?>" class="btn btn-info btn-sm" title="Kelola Target & Realisasi Bulanan"><i class="bi bi-calendar-week"></i></a>
                                    <button type="button" class="btn btn-warning btn-sm btn-edit" 
                                        data-bs-toggle="modal" 
                                        data-bs-target="#editModal"
                                        data-id="<?= $row['id'] ?>"
                                        data-sasaran="<?= esc($row['sasaran_program']) ?>"
                                        data-indikator="<?= esc($row['indikator_kinerja']) ?>"
                                        data-satuan="<?= esc($row['satuan']) ?>"
                                        data-target="<?= esc($row['target_utama']) ?>"
                                        data-kegiatan="<?= esc($row['kegiatan']) ?>">
                                        <i class="bi bi-pencil-fill"></i>
                                    </button>
                                    <button type="button" class="btn btn-danger btn-sm btn-hapus" data-id="<?= $row['id'] ?>" data-nama="<?= esc($row['indikator_kinerja']); ?>"><i class="bi bi-trash"></i></button>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    <?php else: ?>
                        <tr>
                            <td colspan="5" class="text-center p-4">
                                <p class="mb-1">Belum ada data rencana untuk tahun ini.</p>
                                <a href="<?= site_url('user/rencana/input') ?>">Buat Rencana Baru</a>
                            </td>
                        </tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>
<?php endif; ?>

<!-- Form tersembunyi untuk proses hapus -->
<form action="" method="POST" id="formHapus"><?= csrf_field() ?></form>

<!-- MODAL UNTUK EDIT DATA -->
<div class="modal fade" id="editModal" tabindex="-1" aria-labelledby="editModalLabel" aria-hidden="true">
  <div class="modal-dialog modal-lg">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title" id="editModalLabel">Edit Rencana Kinerja</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
      </div>
      <form action="" method="POST" id="editForm">
        <?= csrf_field() ?>
        <div class="modal-body">
            <div class="mb-3">
                <label for="edit_sasaran" class="form-label">Sasaran Program/Kegiatan</label>
                <textarea id="edit_sasaran" name="sasaran_program" class="form-control" rows="2" required></textarea>
            </div>
            <div class="mb-3">
                <label for="edit_indikator" class="form-label">Indikator Kinerja</label>
                <textarea id="edit_indikator" name="indikator_kinerja" class="form-control" rows="2" required></textarea>
            </div>
            <div class="row">
                <div class="col-md-6 mb-3">
                    <label for="edit_satuan" class="form-label">Satuan</label>
                    <input type="text" id="edit_satuan" name="satuan" class="form-control" required>
                </div>
                <div class="col-md-6 mb-3">
                    <label for="edit_target" class="form-label">Target Tahunan</label>
                    <input type="number" step="any" id="edit_target" name="target_utama" class="form-control" required>
                </div>
            </div>
            <div class="mb-3">
                <label for="edit_kegiatan" class="form-label">Kegiatan</label>
                <textarea id="edit_kegiatan" name="kegiatan" class="form-control" rows="3"></textarea>
            </div>
        </div>
        <div class="modal-footer">
          <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
          <button type="submit" class="btn btn-primary">Simpan Perubahan</button>
        </div>
      </form>
    </div>
  </div>
</div>
<?= $this->endSection() ?>

<?= $this->section('scripts') ?>
<script>
document.addEventListener('DOMContentLoaded', function() {
    const editModal = new bootstrap.Modal(document.getElementById('editModal'));
    const editForm = document.getElementById('editForm');
    
    // Event listener untuk semua tombol edit
    document.querySelectorAll('.btn-edit').forEach(button => {
        button.addEventListener('click', function() {
            // Ambil data dari atribut data-* tombol
            const id = this.dataset.id;
            const sasaran = this.dataset.sasaran;
            const indikator = this.dataset.indikator;
            const satuan = this.dataset.satuan;
            const target = this.dataset.target;
            const kegiatan = this.dataset.kegiatan;

            // Set action form modal
            editForm.action = `<?= site_url('user/rencana/update/') ?>${id}`;

            // Isi form di dalam modal dengan data
            document.getElementById('edit_sasaran').value = sasaran;
            document.getElementById('edit_indikator').value = indikator;
            document.getElementById('edit_satuan').value = satuan;
            document.getElementById('edit_target').value = target;
            document.getElementById('edit_kegiatan').value = kegiatan;
        });
    });

    const formHapus = document.getElementById('formHapus');
    const tbody = document.querySelector('tbody');

    if (tbody) {
        tbody.addEventListener('click', function(e) {
            const btnHapus = e.target.closest('.btn-hapus');
            if (btnHapus) {
                const id = btnHapus.dataset.id;
                const nama = btnHapus.dataset.nama;
                
                if (confirm(`Apakah Anda yakin ingin menghapus Rencana Kinerja:\n"${nama}"?`)) {
                    formHapus.action = `<?= site_url('user/rencana/delete/') ?>${id}`;
                    formHapus.submit();
                }
            }
        });
    }
});
</script>
<?= $this->endSection() ?>
