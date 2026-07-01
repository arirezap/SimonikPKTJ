<?= $this->extend('layouts/main') ?>

<?= $this->section('title') ?>Laporan Kegiatan Harian<?= $this->endSection() ?>

<?= $this->section('page_title') ?>
Laporan Kegiatan Harian
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
    .form-control, .form-select, .input-group-text {
        font-size: 0.85rem;
    }
    .col-target { min-width: 300px; }
    .col-deskripsi { min-width: 350px; }
    .col-capaian { min-width: 180px; }
    .col-bukti { min-width: 200px; }
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
        
        <!-- Filter Tanggal -->
        <form method="POST" action="<?= site_url('log-kegiatan') ?>" class="mb-4 p-3 bg-light rounded border">
            <?= csrf_field() ?>
            <div class="row align-items-center">
                <div class="col-md-4">
                    <label class="form-label fw-bold text-primary">Pilih Tanggal Kegiatan</label>
                    <input type="date" name="tanggal" class="form-control border-primary" value="<?= esc($tanggal_terpilih) ?>" onchange="this.form.submit()">
                </div>
                <div class="col-md-8 text-muted mt-3 mt-md-0 pt-md-4">
                    <i class="bi bi-info-circle me-1"></i> Jika pilihan target (RHK) kosong, silakan buat <strong>Target Kinerja Bulanan</strong> terlebih dahulu untuk bulan ini.
                </div>
            </div>
        </form>

        <?php if ($is_past_deadline): ?>
            <div class="alert alert-warning mb-4">
                <i class="bi bi-lock-fill me-2"></i> <strong>Akses Terkunci!</strong> Batas pelaporan harian (<?= esc($batas_log) ?> hari) telah terlewat. Anda tidak dapat menambahkan laporan baru.
            </div>
        <?php endif; ?>

        <form action="<?= site_url('log-kegiatan/store') ?>" method="POST" id="formLog">
            <?= csrf_field() ?>
            <input type="hidden" name="tanggal" value="<?= esc($tanggal_terpilih) ?>">

            <div class="table-responsive mb-3">
                <table class="table table-bordered align-middle table-hover" id="tabelLog">
                    <thead class="text-center">
                        <tr>
                            <th style="width: 50px;">No</th>
                            <th class="col-target">Indikator Kinerja Individu</th>
                            <th class="col-deskripsi">Deskripsi Kegiatan Harian</th>
                            <th class="col-capaian">Jumlah Capaian / Output</th>
                            <th class="col-bukti">Bukti Pekerjaan (Link)</th>
                            <th style="width: 80px;">Status</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php $rowIndex = 1; ?>
                        <?php if (!empty($rekap_data)): ?>
                            <?php foreach ($rekap_data as $row): ?>
                                <tr>
                                    <!-- Logika: Baris yang sudah ada di DB otomatis read-only dan tidak dikirim via form -->
                                    <td class="nomor-baris text-center fw-bold"><?= $rowIndex++ ?></td>
                                    <td>
                                        <?php 
                                        $targetName = '-';
                                        foreach($daftar_target as $target) {
                                            if ($target['id'] == $row['target_id']) {
                                                $targetName = esc($target['indikator_kinerja']) . ' (' . intval($target['target_bulanan']) . ' ' . esc($target['satuan']) . ')';
                                                break;
                                            }
                                        }
                                        // Fake the hidden input so updateDropdownOptions still counts it
                                        ?>
                                        <input type="hidden" class="target-select-hidden" value="<?= esc($row['target_id']) ?>">
                                        <div class="p-2 border rounded bg-light"><?= $targetName ?></div>
                                    </td>
                                    <td>
                                        <div class="p-2 border rounded bg-light"><?= nl2br(esc($row['deskripsi_kegiatan'])) ?></div>
                                    </td>
                                    <td>
                                        <div class="p-2 border rounded bg-light text-center fw-bold"><?= intval($row['jumlah_capaian']) ?> <?= esc($row['satuan']) ?></div>
                                    </td>
                                    <td>
                                        <?php if (!empty($row['link_bukti'])): ?>
                                            <div class="p-2 border rounded bg-light text-center"><a href="<?= esc($row['link_bukti']) ?>" target="_blank" class="btn btn-sm btn-outline-primary"><i class="bi bi-link-45deg"></i> Buka Link</a></div>
                                        <?php else: ?>
                                            <div class="p-2 border rounded bg-light text-center text-muted">-</div>
                                        <?php endif; ?>
                                    </td>
                                    <td class="text-center text-success">
                                        <i class="bi bi-check-circle-fill" title="Sudah Tersimpan"></i>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        <?php endif; ?>

                        <?php if (!$is_past_deadline): ?>
                            <!-- Baris Input Baru -->
                            <tr class="input-row">
                                <input type="hidden" name="log_id[]" value="">
                                <td class="nomor-baris text-center fw-bold"><?= $rowIndex++ ?></td>
                                <td>
                                    <select name="target_id[]" class="form-select" required>
                                        <option value="">-- Pilih Target / RHK --</option>
                                        <?php foreach($daftar_target as $target): ?>
                                            <?php $labelTarget = esc($target['indikator_kinerja']) . ' (' . intval($target['target_bulanan']) . ' ' . esc($target['satuan']) . ')'; ?>
                                            <option value="<?= esc($target['id']) ?>"><?= $labelTarget ?></option>
                                        <?php endforeach; ?>
                                    </select>
                                </td>
                                <td>
                                    <textarea name="deskripsi_kegiatan[]" class="form-control" rows="2" placeholder="Jelaskan apa yang Anda kerjakan hari ini..." required></textarea>
                                </td>
                                <td>
                                    <div class="input-group">
                                        <input type="number" step="1" name="jumlah_capaian[]" class="form-control text-center" placeholder="Angka" required>
                                        <span class="input-group-text">-</span>
                                    </div>
                                </td>
                                <td>
                                    <input type="url" name="link_bukti[]" class="form-control" placeholder="https://..." required>
                                </td>
                                <td class="text-center">
                                    <button type="button" class="btn btn-outline-danger btn-sm hapus-baris" title="Hapus baris baru"><i class="bi bi-trash"></i></button>
                                </td>
                            </tr>
                        <?php endif; ?>
                        
                        <?php if (empty($rekap_data) && $is_past_deadline): ?>
                            <tr><td colspan="5" class="text-center text-muted">Tidak ada laporan pada tanggal ini.</td></tr>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>

            <?php if (!$is_past_deadline): ?>
            <div class="d-flex justify-content-between align-items-center mt-4">
                <button type="button" id="tambahBaris" class="btn btn-success"><i class="bi bi-plus-circle me-2"></i> Tambah Kegiatan Lain</button>
                <button type="submit" class="btn btn-primary px-4"><i class="bi bi-save me-2"></i> Simpan Laporan Harian</button>
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
        const tabelLog = $('#tabelLog tbody');
        const tambahBarisBtn = $('#tambahBaris');

        function updateRowNumbers() {
            tabelLog.find('tr').each(function(index) {
                $(this).find('.nomor-baris').text(index + 1);
            });
        }

        function updateDropdownOptions() {
            let selectedValues = [];
            // Kumpulkan semua id target yang sedang dipilih (termasuk yang read-only)
            tabelLog.find('select[name="target_id[]"], .target-select-hidden').each(function() {
                if ($(this).val() !== '') {
                    selectedValues.push($(this).val());
                }
            });

            // Perbarui status disable untuk setiap opsi di semua select
            tabelLog.find('select[name="target_id[]"]').each(function() {
                let currentVal = $(this).val();
                $(this).find('option').each(function() {
                    let optionVal = $(this).val();
                    if (optionVal !== '') {
                        if (selectedValues.includes(optionVal) && optionVal !== currentVal) {
                            $(this).prop('disabled', true).hide();
                        } else {
                            $(this).prop('disabled', false).show();
                        }
                    }
                });
            });
        }

        tambahBarisBtn.on('click', function() {
            const templateRow = tabelLog.find('tr.input-row:first');
            if (templateRow.length === 0) return;
            
            const newRow = templateRow.clone();
            
            newRow.find('input[name="log_id[]"]').val('');
            newRow.find('input[type="number"]').val('');
            newRow.find('input[type="url"]').val('');
            newRow.find('textarea').val('');
            newRow.find('select').val('');
            newRow.find('.input-group-text').text('-'); // Clear satuan span in new row
            
            tabelLog.append(newRow);
            updateRowNumbers();
            updateDropdownOptions(); // Panggil fungsi ini
        });

        tabelLog.on('click', '.hapus-baris', function() {
            if (tabelLog.find('tr').length > 1) {
                const idLog = $(this).attr('data-id');
                const row = $(this).closest('tr');

                if(idLog) {
                    if(confirm('Apakah Anda yakin ingin menghapus catatan kegiatan ini?')) {
                        $.post('<?= site_url('log-kegiatan/hapus') ?>', { id: idLog, <?= csrf_token() ?>: '<?= csrf_hash() ?>' }, function(response) {
                            if(response.success) {
                                row.remove();
                                updateRowNumbers();
                                updateDropdownOptions(); // Panggil fungsi ini
                            } else {
                                alert('Gagal menghapus data.');
                            }
                        });
                    }
                } else {
                    row.remove();
                    updateRowNumbers();
                    updateDropdownOptions(); // Panggil fungsi ini
                }
            } else {
                alert('Minimal harus mengisi satu kegiatan harian.');
            }
        });
        
        // Auto update satuan text based on selected target
        tabelLog.on('change', 'select[name="target_id[]"]', function() {
            const selectedText = $(this).find("option:selected").text();
            // Ekstrak satuan dari teks "Nama Target (Target Bulanan SATUAN)"
            const match = selectedText.match(/\((.*?)\)$/);
            if(match && match[1]) {
                const parts = match[1].split(' ');
                if(parts.length > 1) {
                    parts.shift(); // Buang angka targetnya
                    $(this).closest('tr').find('.input-group-text').text(parts.join(' '));
                }
            } else {
                $(this).closest('tr').find('.input-group-text').text('-');
            }
            updateDropdownOptions(); // Panggil fungsi ini saat pilihan berubah
        });

        // Inisialisasi awal saat load
        updateDropdownOptions();
    });
</script>
<?= $this->endSection() ?>
