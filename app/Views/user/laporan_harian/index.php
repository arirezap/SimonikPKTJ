<?= $this->extend('layouts/main') ?>

<?= $this->section('title') ?>Target Laporan Bulanan<?= $this->endSection() ?>

<?= $this->section('page_title') ?>
Target Laporan Bulanan
<?= $this->endSection() ?>

<?= $this->section('styles') ?>
<style>
    .table th {
        background-color: #f8f9fa !important;
        color: #333;
        vertical-align: middle;
    }   
    .table {
        font-size: 0.85rem;
    }
    .form-control, .form-select {
        font-size: 0.85rem;
    }
    /* Pastikan kolom tidak terlalu sempit di layar kecil */
    .col-rhk, .col-indikator {
        min-width: 250px;
    }
    .col-target, .col-satuan {
        min-width: 120px;
    }
</style>
<?= $this->endSection() ?>

<?= $this->section('content') ?>

<?php if (session()->getFlashdata('success')) : ?>
    <div class="alert alert-success alert-dismissible fade show" role="alert">
        <i class="bi bi-check-circle-fill me-2"></i> <?= session()->getFlashdata('success') ?>
        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
    </div>
<?php endif; ?>

<?php if (session()->getFlashdata('error')) : ?>
    <div class="alert alert-danger alert-dismissible fade show" role="alert">
        <i class="bi bi-exclamation-triangle-fill me-2"></i> <?= session()->getFlashdata('error') ?>
        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
    </div>
<?php endif; ?>

<div class="card shadow-sm border-0 mb-4">
    <div class="card-body">
        
        <!-- Filter Bulan/Tahun -->
        <form method="POST" action="<?= site_url('laporan-harian') ?>" class="mb-4 p-3 bg-light rounded border">
            <?= csrf_field() ?>
            <div class="row align-items-center">
                <div class="col-md-3">
                    <label class="form-label fw-bold text-primary">Pilih Bulan Target</label>
                    <select name="bulan" class="form-select border-primary" onchange="this.form.submit()">
                        <?php foreach($bulan_indo as $index => $nama): ?>
                            <option value="<?= $index + 1 ?>" <?= ($bulan_terpilih == $index + 1) ? 'selected' : '' ?>><?= $nama ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>
                <div class="col-md-2 mt-3 mt-md-0">
                    <label class="form-label fw-bold text-primary">Tahun</label>
                    <input type="number" name="tahun" class="form-control border-primary" value="<?= esc($tahun_terpilih) ?>" onchange="this.form.submit()">
                </div>
                <div class="col-md-7 text-muted mt-3 mt-md-0 pt-md-4">
                    <i class="bi bi-info-circle me-1"></i> Buat rancangan target pekerjaan Anda untuk bulan ini pada tabel di bawah.
                </div>
            </div>
        </form>

        <?php if ($is_locked): ?>
            <div class="alert alert-warning mb-4">
                <i class="bi bi-lock-fill me-2"></i> <strong>Akses Terkunci!</strong> Batas waktu pengisian atau perubahan target untuk bulan ini telah ditutup (Batas: tanggal <?= esc($batas_target) ?>). Anda hanya dapat melihat data.
            </div>
        <?php endif; ?>

        <form action="<?= site_url('laporan-harian/store') ?>" method="POST" id="formLaporan">
            <?= csrf_field() ?>
            <input type="hidden" name="bulan" value="<?= esc($bulan_terpilih) ?>">
            <input type="hidden" name="tahun" value="<?= esc($tahun_terpilih) ?>">

            <div class="table-responsive mb-3">
                <table class="table table-bordered align-middle table-hover" id="tabelLaporan">
                    <thead class="text-center">
                        <tr>
                            <th style="width: 50px;">No</th>
                            <th class="col-rhk">Rencana Hasil Kerja (RHK)</th>
                            <th class="col-indikator">Indikator Kinerja Individu</th>
                            <th class="col-target">Target Bulan <br><span class="text-primary"><?= esc($nama_bulan) ?></span></th>
                            <th class="col-satuan">Satuan</th>
                            <?php if (!$is_locked): ?>
                                <th style="width: 60px;">Aksi</th>
                            <?php endif; ?>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if (!empty($rekap_data)): ?>
                            <?php foreach ($rekap_data as $index => $row): ?>
                                <tr>
                                    <input type="hidden" name="laporan_id[]" value="<?= esc($row['id']) ?>">
                                    <td class="nomor-baris text-center fw-bold"><?= $index + 1 ?></td>
                                    <td>
                                        <textarea name="sasaran_program[]" class="form-control" rows="2" placeholder="Ketik Rencana Hasil Kerja..." required <?= $is_locked ? 'disabled' : '' ?>><?= esc($row['sasaran_program']) ?></textarea>
                                    </td>
                                    <td>
                                        <textarea name="indikator_kinerja[]" class="form-control" rows="2" placeholder="Ketik Indikator Kinerja..." required <?= $is_locked ? 'disabled' : '' ?>><?= esc($row['indikator_kinerja']) ?></textarea>
                                    </td>
                                    <td>
                                        <input type="number" step="1" name="target_bulanan[]" class="form-control text-center" value="<?= intval($row['target_bulanan']) ?>" placeholder="Angka" required <?= $is_locked ? 'disabled' : '' ?>>
                                    </td>
                                    <td>
                                        <input type="text" name="satuan[]" class="form-control text-center" value="<?= esc($row['satuan']) ?>" placeholder="Ketik Satuan" required <?= $is_locked ? 'disabled' : '' ?>>
                                    </td>
                                    <?php if (!$is_locked): ?>
                                    <td class="text-center">
                                        <button type="button" class="btn btn-outline-danger btn-sm hapus-baris" data-id="<?= esc($row['id']) ?>" title="Hapus"><i class="bi bi-trash"></i></button>
                                    </td>
                                    <?php endif; ?>
                                </tr>
                            <?php endforeach; ?>
                        <?php elseif (!$is_locked): ?>
                            <!-- Baris Kosong Default -->
                            <tr>
                                <input type="hidden" name="laporan_id[]" value="">
                                <td class="nomor-baris text-center fw-bold">1</td>
                                <td>
                                    <textarea name="sasaran_program[]" class="form-control" rows="2" placeholder="Ketik Rencana Hasil Kerja..." required></textarea>
                                </td>
                                <td>
                                    <textarea name="indikator_kinerja[]" class="form-control" rows="2" placeholder="Ketik Indikator Kinerja..." required></textarea>
                                </td>
                                <td>
                                    <input type="number" step="1" name="target_bulanan[]" class="form-control text-center" placeholder="Angka" required>
                                </td>
                                <td>
                                    <input type="text" name="satuan[]" class="form-control text-center" placeholder="Ketik Satuan" required>
                                </td>
                                <td class="text-center">
                                    <button type="button" class="btn btn-outline-danger btn-sm hapus-baris" data-id="" title="Hapus"><i class="bi bi-trash"></i></button>
                                </td>
                            </tr>
                        <?php else: ?>
                            <tr><td colspan="5" class="text-center text-muted">Belum ada data target untuk bulan ini.</td></tr>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>

            <?php if (!$is_locked): ?>
            <div class="d-flex justify-content-between align-items-center mt-4">
                <button type="button" id="tambahBaris" class="btn btn-success"><i class="bi bi-plus-circle me-2"></i> Tambah Baris Kosong</button>
                <button type="submit" class="btn btn-primary px-4"><i class="bi bi-save me-2"></i> Simpan Target</button>
            </div>
            <?php endif; ?>
        </form>
    </div>
</div>

<?= $this->endSection() ?>

<?= $this->section('scripts') ?>
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script>
    $(document).ready(function() {
        const tabelLaporan = $('#tabelLaporan tbody');
        const tambahBarisBtn = $('#tambahBaris');

        function updateRowNumbers() {
            tabelLaporan.find('tr').each(function(index) {
                $(this).find('.nomor-baris').text(index + 1);
            });
        }

        tambahBarisBtn.on('click', function() {
            const newRow = tabelLaporan.find('tr:first').clone();
            
            newRow.find('input[name="laporan_id[]"]').val('');
            newRow.find('input[type="number"]').val('');
            newRow.find('input[type="text"]').val('');
            newRow.find('textarea').val('');
            newRow.find('.hapus-baris').attr('data-id', '');
            
            tabelLaporan.append(newRow);
            updateRowNumbers();
        });

        tabelLaporan.on('click', '.hapus-baris', function() {
            if (tabelLaporan.find('tr').length > 1) {
                const idLaporan = $(this).attr('data-id');
                const row = $(this).closest('tr');

                if(idLaporan) {
                    if(confirm('Apakah Anda yakin ingin menghapus target ini?')) {
                        $.post('<?= site_url('laporan-harian/hapus') ?>', { id: idLaporan, <?= csrf_token() ?>: '<?= csrf_hash() ?>' }, function(response) {
                            if(response.success) {
                                row.remove();
                                updateRowNumbers();
                            } else {
                                alert('Gagal menghapus data.');
                            }
                        });
                    }
                } else {
                    row.remove();
                    updateRowNumbers();
                }
            } else {
                alert('Tabel minimal harus memiliki satu baris isian.');
            }
        });
    });
</script>
<?= $this->endSection() ?>
