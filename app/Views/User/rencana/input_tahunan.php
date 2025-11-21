<?= $this->extend('layouts/main') ?>

<?= $this->section('title') ?><?= esc($page_title ?? 'Input Rencana Kerja') ?><?= $this->endSection() ?>

<?= $this->section('page_title') ?>
Input & Kelola Rencana Kerja Tahunan
<?= $this->endSection() ?>

<?= $this->section('styles') ?>
<link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/select2-bootstrap-5-theme@1.3.0/dist/select2-bootstrap-5-theme.min.css" />
<?= $this->endSection() ?>

<?= $this->section('content') ?>
<div class="card">
    <div class="card-body">

        <form method="GET" action="<?= site_url('user/rencana/input') ?>" id="filterTahunForm" class="mb-4">
            <div class="row align-items-center">
                <div class="col-md-4">
                    <label for="tahun_anggaran_filter" class="form-label fw-bold">Pilih Tahun Perencanaan</label>
                    <select name="tahun" id="tahun_anggaran_filter" class="form-select" onchange="this.form.submit()">
                        <?php
                            $tahun_sekarang = date("Y");
                            $daftar_tahun_opsi = [];
                            if (isset($existing_years_json)) {
                                $daftar_tahun_opsi = json_decode($existing_years_json);
                            }
                            for ($i = $tahun_sekarang; $i <= $tahun_sekarang + 5; $i++) {
                                $daftar_tahun_opsi[] = (string)$i;
                            }
                            $daftar_tahun_opsi = array_unique($daftar_tahun_opsi);
                            rsort($daftar_tahun_opsi);
                        ?>
                        <?php foreach ($daftar_tahun_opsi as $tahun_opsi): ?>
                            <option value="<?= $tahun_opsi; ?>" <?= ($tahun_terpilih == $tahun_opsi) ? 'selected' : ''; ?>><?= $tahun_opsi; ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>
                <div class="col-md-8">
                    <div class="alert alert-info d-none p-3 text-center" id="warning-box">
                        <p class="mb-2"><i class="bi bi-info-circle-fill me-2"></i>Data untuk tahun <strong id="tahun-terpilih"></strong> sudah ada. Anda bisa menambahkan atau memodifikasi rencana di bawah ini.</p>
                        <a href="#" id="link-edit" class="btn btn-sm btn-primary">Kelola Target & Realisasi Tahun Ini &rarr;</a>
                    </div>
                </div>
            </div>
        </form>

        <div id="form-content">
            <form action="<?= site_url('user/rencana/store') ?>" method="POST" id="formRencana">
                <?= csrf_field() ?>
                <input type="hidden" name="tahun_anggaran" value="<?= esc($tahun_terpilih) ?>">

                <div class="table-responsive">
                    <table class="table table-bordered" id="tabelRencana">
                        <thead class="table-light">
                            <tr>
                                <th style="width: 5%;">No</th>
                                <th style="width: 25%; min-width: 300px;">Sasaran Program/Kegiatan <span class="text-danger">*</span></th>
                                <th style="width: 25%; min-width: 300px;">Indikator Kinerja <span class="text-danger">*</span></th>
                                <th style="width: 10%; min-width: 100px;">Satuan <span class="text-danger">*</span></th>
                                <th style="width: 10%; min-width: 100px;">Target Tahunan <span class="text-danger">*</span></th>
                                <th style="min-width: 200px;">Kegiatan</th>
                                <th style="width: 5%;">Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php if (!empty($rencana_kinerja)): ?>
                                <?php foreach ($rencana_kinerja as $index => $row): ?>
                                    <tr>
                                        <input type="hidden" name="rencana_id[]" value="<?= esc($row['id']) ?>">
                                        <td class="nomor-baris text-center"><?= $index + 1 ?></td>
                                        <td>
                                            <select name="sasaran_program[]" class="form-select select2-dropdown" required>
                                                <option value="">-- Pilih Sasaran --</option>
                                                <?php foreach($daftar_sasaran as $sasaran): ?>
                                                    <option value="<?= esc($sasaran['nama_sasaran']) ?>" <?= ($row['sasaran_program'] == $sasaran['nama_sasaran']) ? 'selected' : '' ?>><?= esc($sasaran['nama_sasaran']) ?></option>
                                                <?php endforeach; ?>
                                            </select>
                                        </td>
                                        <td>
                                            <select name="indikator_kinerja[]" class="form-select select2-dropdown" required>
                                                <option value="">-- Pilih Indikator --</option>
                                                <?php foreach($daftar_indikator as $indikator): ?>
                                                    <option value="<?= esc($indikator['nama_indikator']) ?>" <?= ($row['indikator_kinerja'] == $indikator['nama_indikator']) ? 'selected' : '' ?>><?= esc($indikator['nama_indikator']) ?></option>
                                                <?php endforeach; ?>
                                            </select>
                                        </td>
                                        <td>
                                            <select name="satuan[]" class="form-select select2-dropdown" required>
                                                <option value="">-- Pilih Satuan --</option>
                                                <?php foreach($daftar_satuan as $satuan): ?>
                                                    <option value="<?= esc($satuan['nama_satuan']) ?>" <?= ($row['satuan'] == $satuan['nama_satuan']) ? 'selected' : '' ?>><?= esc($satuan['nama_satuan']) ?></option>
                                                <?php endforeach; ?>
                                            </select>
                                        </td>
                                        <td><input type="number" step="any" name="target_utama[]" class="form-control" value="<?= esc($row['target_utama']) ?>" required></td>
                                        <td><textarea name="kegiatan[]" class="form-control" rows="2"><?= esc($row['kegiatan']) ?></textarea></td>
                                        <td class="text-center"><button type="button" class="btn btn-danger btn-sm hapus-baris"><i class="bi bi-trash"></i></button></td>
                                    </tr>
                                <?php endforeach; ?>
                            <?php else: ?>
                                <tr>
                                    <input type="hidden" name="rencana_id[]" value="">
                                    <td class="nomor-baris text-center">1</td>
                                    <td>
                                        <select name="sasaran_program[]" class="form-select select2-dropdown" required>
                                            <option value="">-- Pilih Sasaran --</option>
                                            <?php foreach($daftar_sasaran as $sasaran): ?>
                                                <option value="<?= esc($sasaran['nama_sasaran']) ?>"><?= esc($sasaran['nama_sasaran']) ?></option>
                                            <?php endforeach; ?>
                                        </select>
                                    </td>
                                    <td>
                                        <select name="indikator_kinerja[]" class="form-select select2-dropdown" required>
                                            <option value="">-- Pilih Indikator --</option>
                                            <?php foreach($daftar_indikator as $indikator): ?>
                                                <option value="<?= esc($indikator['nama_indikator']) ?>"><?= esc($indikator['nama_indikator']) ?></option>
                                            <?php endforeach; ?>
                                        </select>
                                    </td>
                                    <td>
                                        <select name="satuan[]" class="form-select select2-dropdown" required>
                                            <option value="">-- Pilih Satuan --</option>
                                            <?php foreach($daftar_satuan as $satuan): ?>
                                                <option value="<?= esc($satuan['nama_satuan']) ?>"><?= esc($satuan['nama_satuan']) ?></option>
                                            <?php endforeach; ?>
                                        </select>
                                    </td>
                                    <td><input type="number" step="any" name="target_utama[]" class="form-control" required></td>
                                    <td><textarea name="kegiatan[]" class="form-control" rows="2"></textarea></td>
                                    <td class="text-center"><button type="button" class="btn btn-danger btn-sm hapus-baris"><i class="bi bi-trash"></i></button></td>
                                </tr>
                            <?php endif; ?>
                        </tbody>
                    </table>
                </div>
                <div class="d-flex justify-content-between">
                    <button type="button" id="tambahBaris" class="btn btn-success"><i class="bi bi-plus-circle"></i> Tambah Rencana</button>
                    <button type="submit" id="tombolSimpan" class="btn btn-primary">Simpan & Lanjut ke Alokasi Bulanan <i class="bi bi-arrow-right"></i></button>
                </div>
            </form>
        </div>
    </div>
</div>
<?= $this->endSection() ?>

<?= $this->section('scripts') ?>
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>
<script>
    $(document).ready(function() {
        // Fungsi untuk inisialisasi Select2
        function initSelect2(selector) {
            selector.select2({
                theme: 'bootstrap-5',
                width: '100%'
            });
        }
        
        // Inisialisasi Select2 pada semua dropdown yang ada saat halaman dimuat
        initSelect2($('.select2-dropdown'));

        const tabelRencana = $('#tabelRencana tbody');
        const tambahBarisBtn = $('#tambahBaris');

        function updateRowNumbers() {
            tabelRencana.find('tr').each(function(index) {
                $(this).find('.nomor-baris').text(index + 1);
            });
        }

        tambahBarisBtn.on('click', function() {
            const newRow = tabelRencana.find('tr:first').clone();
            
            // Hapus instance Select2 yang lama dari baris yang di-clone
            newRow.find('.select2-container').remove();
            newRow.find('.select2-dropdown').removeClass('select2-hidden-accessible').removeAttr('data-select2-id').removeAttr('aria-hidden').removeAttr('tabindex');

            newRow.find('input[name="rencana_id[]"]').val('');
            newRow.find('input[type="text"], input[type="number"], textarea, select').val('');
            
            tabelRencana.append(newRow);
            
            // Inisialisasi kembali Select2 pada dropdown di baris baru
            initSelect2(newRow.find('.select2-dropdown'));

            updateRowNumbers();
        });

        tabelRencana.on('click', '.hapus-baris', function() {
            if (tabelRencana.find('tr').length > 1) {
                $(this).closest('tr').remove();
                updateRowNumbers();
            } else {
                alert('Minimal harus ada satu baris rencana.');
            }
        });

        // Logika validasi tahun
        const existingYears = <?= $existing_years_json ?? '[]' ?>;
        const tahunSelect = document.getElementById('tahun_anggaran_filter');
        const warningBox = document.getElementById('warning-box');
        const tahunTerpilihSpan = document.getElementById('tahun-terpilih');
        const linkEdit = document.getElementById('link-edit');

        function checkYear(selectedYear) {
            if (existingYears.map(String).includes(selectedYear)) {
                tahunTerpilihSpan.textContent = selectedYear;
                linkEdit.href = `<?= site_url('user/alokasi/bulanan?tahun=') ?>${selectedYear}`;
                warningBox.classList.remove('d-none');
                warningBox.classList.add('d-flex');
            } else {
                warningBox.classList.add('d-none');
                warningBox.classList.remove('d-flex');
            }
        }
        checkYear(tahunSelect.value);
    });
</script>
<?= $this->endSection() ?>