<?= $this->extend('layouts/main') ?>
<?= $this->section('page_title') ?>Master Satuan<?= $this->endSection() ?>
<?= $this->section('title') ?><?= esc($page_title ?? '') ?><?= $this->endSection() ?>

<?= $this->section('content') ?>
<div class="row">
    <div class="col-lg-8">
        <div class="card">
            <div class="card-header"><h5>Daftar Satuan</h5></div>
            <div class="card-body">
                <table class="table table-hover">
                    <tbody>
                        <?php foreach($items as $item): ?>
                        <tr><td><?= esc($item['nama_satuan']) ?></td></tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
    <div class="col-lg-4">
        <div class="card">
            <div class="card-header"><h5>Tambah Satuan</h5></div>
            <div class="card-body">
                <form action="<?= site_url('admin/master-data/satuan/store') ?>" method="POST">
                    <?= csrf_field() ?>
                    <div class="mb-3">
                        <label>Nama Satuan</label>
                        <input type="text" name="nama_satuan" class="form-control <?= $validation->hasError('nama_satuan') ? 'is-invalid' : '' ?>" value="<?= old('nama_satuan') ?>">
                        <?php if($validation->hasError('nama_satuan')): ?><div class="invalid-feedback"><?= $validation->getError('nama_satuan') ?></div><?php endif; ?>
                    </div>
                    <button type="submit" class="btn btn-primary">Simpan</button>
                </form>
            </div>
        </div>
    </div>
</div>
<?= $this->endSection() ?>