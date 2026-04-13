<?= $this->extend('layouts/main') ?>

<?= $this->section('title') ?><?= esc($page_title ?? 'Master Data') ?><?= $this->endSection() ?>

<?= $this->section('content') ?>
<div class="container-fluid">
    <div class="d-flex justify-content-between flex-wrap flex-md-nowrap align-items-center pt-3 pb-2 mb-3 border-bottom">
        <h1 class="h2"><?= esc($page_title ?? 'Master Data') ?></h1>
        <div class="btn-toolbar mb-2 mb-md-0">
            <button type="button" class="btn btn-sm btn-primary" data-bs-toggle="modal" data-bs-target="#addModal">
                <i class="bi bi-plus-circle"></i> Tambah Unit Kerja Baru
            </button>
        </div>
    </div>

    <?php if (session()->getFlashdata('success')): ?>
        <div class="alert alert-success"><?= session()->getFlashdata('success') ?></div>
    <?php endif; ?>
    <?php if (session()->getFlashdata('error')): ?>
        <div class="alert alert-danger"><?= session()->getFlashdata('error') ?></div>
    <?php endif; ?>
    <?php if (session()->has('errors')): ?>
        <div class="alert alert-danger">
            <ul class="mb-0">
            <?php foreach (session('errors') as $error): ?>
                <li><?= esc($error) ?></li>
            <?php endforeach ?>
            </ul>
        </div>
    <?php endif; ?>

    <div class="card shadow mb-4">
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-bordered table-hover" id="dataTable" width="100%" cellspacing="0">
                    <thead class="table-light">
                        <tr>
                            <th width="5%">No</th>
                            <th>Nama Unit Kerja</th>
                            <th width="20%">Penanggung Jawab</th>
                            <th width="20%">Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if (!empty($items)): ?>
                            <?php $i = 1; foreach ($items as $item): ?>
                            <tr>
                                <td><?= $i++ ?></td>
                                <td><?= esc($item['nama_unit']) ?></td>
                                <td>
                                    <?php
                                        $parent = esc($item['parent_unit']);
                                        $badge_class = 'bg-secondary';
                                        if ($parent === 'aak') $badge_class = 'bg-success';
                                        if ($parent === 'kuk') $badge_class = 'bg-info text-dark';
                                    ?>
                                    <span class="badge <?= $badge_class ?>"><?= strtoupper($parent) ?: 'Belum Diatur' ?></span>
                                </td>
                                <td>
                                    <button type="button" class="btn btn-warning btn-sm" data-bs-toggle="modal" data-bs-target="#editModal-<?= $item['id'] ?>" title="Edit">
                                        <i class="bi bi-pencil-square"></i>
                                    </button>
                                    <a href="<?= site_url('admin/master-data/unit-kerja/delete/'.$item['id']) ?>" class="btn btn-danger btn-sm" onclick="return confirm('Yakin hapus data ini?')" title="Hapus">
                                        <i class="bi bi-trash"></i>
                                    </a>
                                </td>
                            </tr>

                            <!-- Edit Modal -->
                            <div class="modal fade" id="editModal-<?= $item['id'] ?>" tabindex="-1" aria-labelledby="editModalLabel-<?= $item['id'] ?>" aria-hidden="true">
                                <div class="modal-dialog">
                                    <div class="modal-content">
                                        <div class="modal-header">
                                            <h5 class="modal-title" id="editModalLabel-<?= $item['id'] ?>">Edit Unit Kerja</h5>
                                            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                                        </div>
                                        <form action="<?= site_url('admin/master-data/unit-kerja/update/'.$item['id']) ?>" method="post">
                                            <?= csrf_field() ?>
                                            <div class="modal-body">
                                                <div class="mb-3">
                                                    <label for="nama_unit_<?= $item['id'] ?>" class="form-label">Nama Unit Kerja</label>
                                                    <input type="text" name="nama_unit" id="nama_unit_<?= $item['id'] ?>" class="form-control" value="<?= esc($item['nama_unit']) ?>" required>
                                                </div>
                                                <div class="mb-3">
                                                    <label for="parent_unit_<?= $item['id'] ?>" class="form-label">Penanggung Jawab</label>
                                                    <select name="parent_unit" id="parent_unit_<?= $item['id'] ?>" class="form-select">
                                                        <option value="">-- Pilih Penanggung Jawab --</option>
                                                        <option value="aak" <?= ($item['parent_unit'] == 'aak') ? 'selected' : '' ?>>AAK</option>
                                                        <option value="kuk" <?= ($item['parent_unit'] == 'kuk') ? 'selected' : '' ?>>KUK</option>
                                                    </select>
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
                            <?php endforeach; ?>
                        <?php else: ?>
                            <tr>
                                <td colspan="4" class="text-center">Belum ada data.</td>
                            </tr>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

<!-- Add Modal -->
<div class="modal fade" id="addModal" tabindex="-1" aria-labelledby="addModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="addModalLabel">Tambah Unit Kerja Baru</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form action="<?= site_url('admin/master-data/unit-kerja/store') ?>" method="post">
                <?= csrf_field() ?>
                <div class="modal-body">
                    <div class="mb-3">
                        <label for="nama_unit" class="form-label">Nama Unit Kerja</label>
                        <input type="text" name="nama_unit" id="nama_unit" class="form-control" value="<?= old('nama_unit') ?>" required>
                    </div>
                    <div class="mb-3">
                        <label for="parent_unit" class="form-label">Penanggung Jawab</label>
                        <select name="parent_unit" id="parent_unit" class="form-select">
                            <option value="">-- Pilih Penanggung Jawab --</option>
                            <option value="aak" <?= (old('parent_unit') == 'aak') ? 'selected' : '' ?>>AAK</option>
                            <option value="kuk" <?= (old('parent_unit') == 'kuk') ? 'selected' : '' ?>>KUK</option>
                        </select>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                    <button type="submit" class="btn btn-primary">Simpan</button>
                </div>
            </form>
        </div>
    </div>
</div>
<?= $this->endSection() ?>