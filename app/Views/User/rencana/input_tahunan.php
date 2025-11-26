<?= $this->extend('layouts/main') ?>

<?= $this->section('title') ?><?= esc($page_title ?? 'Input Rencana Kerja') ?><?= $this->endSection() ?>

<?= $this->section('page_title') ?>
Input & Kelola Rencana Kerja Tahunan
<?= $this->endSection() ?>

<?= $this->section('styles') ?>
<link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/select2-bootstrap-5-theme@1.3.0/dist/select2-bootstrap-5-theme.min.css" />
<style>
    /* Fix agar Select2 menyesuaikan tinggi dengan input form lainnya */
    .select2-container .select2-selection--single {
        height: 38px !important;
        display: flex;
        align-items: center;
    }
    .select2-container--bootstrap-5 .select2-selection--single .select2-selection__rendered {
        line-height: 1.5;
        padding-top: 0;
        padding-bottom: 0;
    }
</style>
<?= $this->endSection() ?>

<?= $this->section('content') ?>
<div class="card">
    <div class="card-body">

        <!-- Filter Tahun -->
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
                            // Tambahkan rentang tahun jika belum ada
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
                        <div class="d-flex align-items-center justify-content-center gap-2">
                            <i class="bi bi-info-circle-fill fs-5"></i>
                            <div>
                                Data untuk tahun <strong id="tahun-terpilih"></strong> sudah ada. 
                                <a href="#" id="link-edit" class="fw-bold text-decoration-underline">Kelola Target & Realisasi Tahun Ini &rarr;</a>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </form>

        <!-- Form Input Utama -->
        <div id="form-content">
            <form action="<?= site_url('user/rencana/store') ?>" method="POST" id="formRencana">
                <?= csrf_field() ?>
                <input type="hidden" name="tahun_anggaran" value="<?= esc($tahun_terpilih) ?>">

                <div class="table-responsive mb-3">
                    <table class="table table-bordered align-middle" id="tabelRencana">
                        <thead class="table-light">
                            <tr class="text-center align-middle">
                                <th style="width: 50px;">No</th>
                                <th style="width: 30%; min-width: 250px;">Sasaran Program/Kegiatan <span class="text-danger">*</span></th>
                                <th style="width: 30%; min-width: 250px;">Indikator Kinerja <span class="text-danger">*</span></th>
                                <th style="width: 120px;">Satuan <span class="text-danger">*</span></th>
                                <th style="width: 120px;">Target <span class="text-danger">*</span></th>
                                <th style="min-width: 200px;">Kegiatan</th>
                                <th style="width: 60px;">Aksi</th>
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
                                                <option value="">-- Pilih --</option>
                                                <?php foreach($daftar_satuan as $satuan): ?>
                                                    <option value="<?= esc($satuan['nama_satuan']) ?>" <?= ($row['satuan'] == $satuan['nama_satuan']) ? 'selected' : '' ?>><?= esc($satuan['nama_satuan']) ?></option>
                                                <?php endforeach; ?>
                                            </select>
                                        </td>
                                        <td><input type="number" step="any" name="target_utama[]" class="form-control text-center" value="<?= esc($row['target_utama']) ?>" required></td>
                                        <td><textarea name="kegiatan[]" class="form-control" rows="1"><?= esc($row['kegiatan']) ?></textarea></td>
                                        <td class="text-center"><button type="button" class="btn btn-danger btn-sm hapus-baris"><i class="bi bi-trash"></i></button></td>
                                    </tr>
                                <?php endforeach; ?>
                            <?php else: ?>
                                <!-- Baris Kosong Default -->
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
                                            <option value="">-- Pilih --</option>
                                            <?php foreach($daftar_satuan as $satuan): ?>
                                                <option value="<?= esc($satuan['nama_satuan']) ?>"><?= esc($satuan['nama_satuan']) ?></option>
                                            <?php endforeach; ?>
                                        </select>
                                    </td>
                                    <td><input type="number" step="any" name="target_utama[]" class="form-control text-center" required></td>
                                    <td><textarea name="kegiatan[]" class="form-control" rows="1"></textarea></td>
                                    <td class="text-center"><button type="button" class="btn btn-danger btn-sm hapus-baris"><i class="bi bi-trash"></i></button></td>
                                </tr>
                            <?php endif; ?>
                        </tbody>
                    </table>
                </div>
                
                <div class="d-flex justify-content-between align-items-center">
                    <button type="button" id="tambahBaris" class="btn btn-success"><i class="bi bi-plus-lg me-2"></i>Tambah Baris</button>
                    <button type="submit" id="tombolSimpan" class="btn btn-primary px-4">Simpan & Lanjut <i class="bi bi-arrow-right ms-2"></i></button>
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
        
        // Fungsi inisialisasi Select2
        function initSelect2(selector) {
            selector.select2({
                theme: 'bootstrap-5',
                width: '100%',
                placeholder: $(this).data('placeholder'),
                allowClear: true
            });
        }
        
        // Init awal pada semua dropdown
        initSelect2($('.select2-dropdown'));

        const tabelRencana = $('#tabelRencana tbody');
        const tambahBarisBtn = $('#tambahBaris');

        function updateRowNumbers() {
            tabelRencana.find('tr').each(function(index) {
                $(this).find('.nomor-baris').text(index + 1);
            });
        }

        // --- LOGIKA TAMBAH BARIS (FIXED - DEEP CLEAN) ---
        tambahBarisBtn.on('click', function() {
            // 1. Clone baris pertama
            const newRow = tabelRencana.find('tr:first').clone();
            
            // 2. BERSIHKAN SISA SELECT2 DARI CLONE
            // Hapus container span Select2 (UI dropdown) yang ikut ter-copy
            newRow.find('.select2-container').remove();
            
            // Reset elemen <select> agar kembali murni HTML
            newRow.find('select').each(function() {
                const $select = $(this);
                
                // Hapus class internal Select2
                $select.removeClass('select2-hidden-accessible');
                
                // Hapus atribut internal Select2 pada TAG SELECT
                $select.removeAttr('data-select2-id');
                $select.removeAttr('tabindex');
                $select.removeAttr('aria-hidden');
                
                // PENTING: Hapus atribut 'data-select2-id' dari SEMUA OPTION di dalamnya
                // Ini yang sering bikin konflik opsi tidak bisa dipilih
                $select.find('option').removeAttr('data-select2-id');
                
                // Hapus atribut 'selected' agar tidak ada default value
                $select.find('option').removeAttr('selected');
                
                // Set value ke kosong
                $select.val(''); 
            });

            // 3. Bersihkan inputan teks/angka
            newRow.find('input[name="rencana_id[]"]').val('');
            newRow.find('input[type="text"], input[type="number"]').val('');
            newRow.find('textarea').val('');
            
            // 4. Tambahkan ke tabel
            tabelRencana.append(newRow);
            
            // 5. Inisialisasi Select2 BARU pada baris tersebut
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

        // Logika cek tahun existing
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