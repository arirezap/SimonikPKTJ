<?= $this->extend('layouts/main') ?>

<?= $this->section('page_title') ?>Kelola Jadwal Kuliah<?= $this->endSection() ?>
<?= $this->section('title') ?><?= esc($page_title ?? 'Kelola Jadwal') ?><?= $this->endSection() ?>

<?= $this->section('content') ?>
<div class="d-flex justify-content-between align-items-center mb-4">
    <p class="text-muted">Kelola jadwal perkuliahan untuk semua program studi.</p>
    <button type="button" class="btn btn-success" data-bs-toggle="modal" data-bs-target="#addJadwalModal">
        <i class="bi bi-plus-circle me-2"></i> Tambah Jadwal Baru
    </button>
</div>

<div class="card">
    <div class="card-body">
        <div class="table-responsive">
            <table class="table table-hover">
                <thead>
                    <tr>
                        <th>Prodi</th>
                        <th>Mata Kuliah</th>
                        <th>Dosen Pengampu</th>
                        <th>Hari / Jam</th>
                        <th>Ruangan</th>
                        <th>Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach($jadwal_kuliah as $jadwal): ?>
                    <tr>
                        <td><span class="badge bg-secondary"><?= esc($jadwal['prodi']) ?></span></td>
                        <td><?= esc($jadwal['matkul']) ?></td>
                        <td><?= esc($jadwal['dosen']) ?></td>
                        <td><?= esc($jadwal['hari']) ?>, <?= esc($jadwal['jam']) ?></td>
                        <td><?= esc($jadwal['ruangan']) ?></td>
                        <td>
                            <button class="btn btn-sm btn-warning"><i class="bi bi-pencil-fill"></i></button>
                            <button class="btn btn-sm btn-danger"><i class="bi bi-trash"></i></button>
                        </td>
                    </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>

<!-- Modal Tambah Jadwal -->
<div class="modal fade" id="addJadwalModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Form Jadwal Kuliah Baru</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <form action="<?= site_url('user/akademik/jadwal/store') ?>" method="POST">
                <?= csrf_field() ?>
                <div class="modal-body">
                    <div class="mb-3">
                        <label class="form-label">Program Studi</label>
                        <select name="prodi" class="form-select" required>
                            <option value="RSTJ">Rekayasa Sistem Transportasi Jalan</option>
                            <option value="TRO">Teknologi Rekayasa Otomotif</option>
                            <option value="TO">Teknologi Otomotif</option>
                        </select>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Mata Kuliah</label>
                        <input type="text" name="matkul" class="form-control" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Dosen Pengampu</label>
                        <input type="text" name="dosen" class="form-control" required>
                    </div>
                    <div class="row">
                        <div class="col-6"><label class="form-label">Hari</label><input type="text" name="hari" class="form-control"></div>
                        <div class="col-6"><label class="form-label">Jam</label><input type="text" name="jam" class="form-control" placeholder="Contoh: 08:00 - 10:00"></div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                    <button type="submit" class="btn btn-primary">Simpan Jadwal</button>
                </div>
            </form>
        </div>
    </div>
</div>
<?= $this->endSection() ?>
