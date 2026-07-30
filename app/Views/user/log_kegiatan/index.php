<?= $this->extend('layouts/main') ?>

<?= $this->section('title') ?>Lapor Kegiatan Harian<?= $this->endSection() ?>

<?= $this->section('page_title') ?>
Lapor Kegiatan Harian
<?= $this->endSection() ?>

<?= $this->section('styles') ?>
<style>
    .table th {
        background-color: #f8f9fa !important;
        color: #333;
        vertical-align: middle;
    }
    .table td, .table th {
        border-color: #eaeaea;
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
        <form method="POST" action="<?= site_url('log-kegiatan') ?>" class="mb-3 px-1">
            <?= csrf_field() ?>
            <div class="row align-items-center">
                <div class="col-md-3">
                    <label class="form-label fw-bold text-dark small mb-2">Pilih Tanggal Kegiatan</label>
                    <div class="input-group shadow-sm">
                        <span class="input-group-text bg-primary text-white border-primary"><i class="bi bi-calendar3"></i></span>
                        <input type="date" name="tanggal" class="form-control border-primary fw-bold text-primary" value="<?= esc($tanggal_terpilih) ?>" max="<?= date('Y-m-d') ?>" onchange="this.form.submit()">
                    </div>
                </div>
                <div class="col-md-9 text-muted mt-2 mt-md-0 pt-md-4 small">
                    <i class="bi bi-info-circle me-1"></i> Jika pilihan target (RHK) kosong, silakan buat <strong>Target Kinerja Bulanan</strong> terlebih dahulu untuk bulan ini.
                </div>
            </div>
        </form>

        <?php if (isset($target_status) && $target_status === 'belum_ada'): ?>
            <div class="alert alert-danger mb-4 shadow-sm">
                <i class="bi bi-exclamation-triangle-fill me-2"></i> <strong>Target Bulanan Belum Ada!</strong> Anda belum membuat Target Kinerja Bulanan untuk bulan ini. Silakan buat dan kirimkan target terlebih dahulu pada menu <strong>Target Kinerja Bulanan</strong> agar dapat mengisi laporan harian.
            </div>
        <?php elseif (isset($target_status) && $target_status === 'belum_disetujui'): ?>
            <div class="alert alert-warning mb-4 shadow-sm">
                <i class="bi bi-clock-history me-2"></i> <strong>Target Bulanan Belum Disetujui!</strong> Target Kinerja Bulanan Anda untuk bulan ini belum disetujui oleh atasan langsung. Anda baru dapat mengisi Lapor Kegiatan Harian setelah target Anda disetujui.
            </div>
        <?php elseif ($is_locked): ?>
            <div class="alert alert-warning mb-4 shadow-sm">
                <i class="bi bi-lock-fill me-2"></i> <strong>Akses Terkunci!</strong> Laporan hari ini telah dikirim dan tidak dapat diubah lagi.
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
                                    <td class="nomor-baris text-center fw-bold"><?= $rowIndex++ ?></td>
                                    <?php if ($is_locked || (isset($row['status']) && $row['status'] === 'terkirim')): ?>
                                        <!-- Read-only View -->
                                        <td>
                                            <?php 
                                            $targetName = '-';
                                            foreach($daftar_target as $target) {
                                                if ($target['id'] == $row['target_id']) {
                                                    $targetName = esc($target['indikator_kinerja']) . ' (' . str_replace('.', ',', (float)$target['target_bulanan']) . ' ' . esc($target['satuan']) . ')';
                                                    break;
                                                }
                                            }
                                            ?>
                                            <input type="hidden" class="target-select-hidden" value="<?= esc($row['target_id']) ?>">
                                            <div class="p-2 border rounded bg-light text-muted"><?= $targetName ?></div>
                                        </td>
                                        <td>
                                            <div class="p-2 border rounded bg-light text-muted"><?= nl2br(esc($row['deskripsi_kegiatan'])) ?></div>
                                        </td>
                                        <td>
                                            <div class="p-2 border rounded bg-light text-center fw-bold text-muted"><?= str_replace('.', ',', (float)$row['jumlah_capaian']) ?> <?= esc($row['satuan']) ?></div>
                                        </td>
                                        <td>
                                            <?php if (!empty($row['link_bukti'])): ?>
                                                <div class="p-2 border rounded bg-light text-center"><a href="<?= esc($row['link_bukti']) ?>" target="_blank" class="btn btn-sm btn-outline-primary"><i class="bi bi-link-45deg"></i> Buka Link</a></div>
                                            <?php else: ?>
                                                <div class="p-2 border rounded bg-light text-center text-muted">-</div>
                                            <?php endif; ?>
                                        </td>
                                        <td class="text-center text-success">
                                            <i class="bi bi-lock-fill text-warning" title="Terkunci"></i>
                                        </td>
                                    <?php else: ?>
                                        <!-- Editable Draft View -->
                                        <input type="hidden" name="log_id[]" value="<?= $row['id'] ?>">
                                        <td>
                                            <select name="target_id[]" class="form-select" required>
                                                <option value="">-- Pilih Target / RHK --</option>
                                                <?php foreach($daftar_target as $target): ?>
                                                    <?php 
                                                    $labelTarget = esc($target['indikator_kinerja']) . ' (' . str_replace('.', ',', (float)$target['target_bulanan']) . ' ' . esc($target['satuan']) . ')'; 
                                                    $selected = ($target['id'] == $row['target_id']) ? 'selected' : '';
                                                    ?>
                                                    <option value="<?= esc($target['id']) ?>" <?= $selected ?>><?= $labelTarget ?></option>
                                                <?php endforeach; ?>
                                            </select>
                                        </td>
                                        <td>
                                            <textarea name="deskripsi_kegiatan[]" class="form-control" rows="2" placeholder="Jelaskan apa yang Anda kerjakan hari ini..." required><?= esc($row['deskripsi_kegiatan']) ?></textarea>
                                        </td>
                                        <td>
                                            <div class="input-group">
                                                <input type="number" step="0.01" name="jumlah_capaian[]" class="form-control text-center" placeholder="Angka" value="<?= (float)$row['jumlah_capaian'] ?>" required>
                                                <span class="input-group-text"><?= esc($row['satuan'] ?? '-') ?></span>
                                            </div>
                                        </td>
                                        <td>
                                            <input type="url" name="link_bukti[]" class="form-control" placeholder="https://..." value="<?= esc($row['link_bukti']) ?>" required>
                                        </td>
                                        <td class="text-center">
                                            <button type="button" class="btn btn-outline-danger btn-sm hapus-baris" data-id="<?= $row['id'] ?>" title="Hapus baris"><i class="bi bi-trash"></i></button>
                                        </td>
                                    <?php endif; ?>
                                </tr>
                            <?php endforeach; ?>
                        <?php endif; ?>

                        <?php if (!$is_locked): ?>
                            <!-- Baris Input Baru -->
                            <tr class="input-row">
                                <input type="hidden" name="log_id[]" value="">
                                <td class="nomor-baris text-center fw-bold"><?= $rowIndex++ ?></td>
                                <td>
                                    <select name="target_id[]" class="form-select" required>
                                        <option value="">-- Pilih Target / RHK --</option>
                                        <?php foreach($daftar_target as $target): ?>
                                            <?php $labelTarget = esc($target['indikator_kinerja']) . ' (' . str_replace('.', ',', (float)$target['target_bulanan']) . ' ' . esc($target['satuan']) . ')'; ?>
                                            <option value="<?= esc($target['id']) ?>"><?= $labelTarget ?></option>
                                        <?php endforeach; ?>
                                    </select>
                                </td>
                                <td>
                                    <textarea name="deskripsi_kegiatan[]" class="form-control" rows="2" placeholder="Jelaskan apa yang Anda kerjakan hari ini..." required></textarea>
                                </td>
                                <td>
                                    <div class="input-group">
                                        <input type="number" step="0.01" name="jumlah_capaian[]" class="form-control text-center" placeholder="Angka" required>
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
                        
                        <?php if (empty($rekap_data)): ?>
                            <tr><td colspan="6" class="text-center text-muted">Tidak ada laporan pada tanggal ini.</td></tr>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>

            <?php if (!$is_locked): ?>
            <div class="d-flex justify-content-between align-items-center mt-4">
                <button type="button" id="tambahBaris" class="btn btn-success rounded-pill shadow-sm px-4 py-2 fw-bold"><i class="bi bi-plus-circle me-2"></i> Tambah Kegiatan Lain</button>
                <div class="d-flex gap-2">
                    <button type="button" id="btnSimpanSementara" class="btn btn-outline-primary rounded-pill shadow-sm px-4 py-2 fw-bold"><i class="bi bi-cloud-arrow-up me-2"></i> Simpan Sementara</button>
                    <button type="submit" class="btn btn-primary rounded-pill shadow-sm px-4 py-2 fw-bold"><i class="bi bi-send me-2"></i> Simpan & Kirim</button>
                </div>
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
            let selectedText = $(this).find('option:selected').text();
            let matches = selectedText.match(/\d+ (.+)\)$/);
            let satuan = matches ? matches[1] : '-';
            $(this).closest('tr').find('.input-group-text').text(satuan);
            updateDropdownOptions(); // Panggil saat pilihan berubah
        });

        // Initialize disable options on load
        updateDropdownOptions();

        // Fungsi Simpan Sementara (AJAX)
        $('#btnSimpanSementara').on('click', function() {
            let btn = $(this);
            let originalText = btn.html();
            
            let isValid = true;
            $('#formLog input[required], #formLog textarea[required], #formLog select[required]').each(function() {
                if ($(this).val().trim() === '') {
                    isValid = false;
                }
            });

            if (!isValid) {
                alert('Harap isi semua kolom wajib sebelum menyimpan sementara.');
                return;
            }

            btn.html('<span class="spinner-border spinner-border-sm me-2" role="status" aria-hidden="true"></span> Menyimpan...').prop('disabled', true);
            
            $.ajax({
                url: '<?= site_url('log-kegiatan/store') ?>',
                type: 'POST',
                data: $('#formLog').serialize(),
                dataType: 'json',
                success: function(response) {
                    if (response.csrf_hash) {
                        $('input[name="<?= csrf_token() ?>"]').val(response.csrf_hash);
                    }

                    if (response.success) {
                        btn.html('<i class="bi bi-check-lg me-2"></i> Tersimpan').removeClass('btn-outline-primary').addClass('btn-outline-success');
                        
                        if (response.new_ids) {
                            for (let index in response.new_ids) {
                                $('#formLog input[name="log_id[]"]').eq(index).val(response.new_ids[index]);
                            }
                        }
                        
                        setTimeout(() => {
                            btn.html(originalText).removeClass('btn-outline-success').addClass('btn-outline-primary').prop('disabled', false);
                        }, 2500);
                    } else {
                        alert('Gagal menyimpan: ' + (response.message || 'Error tidak diketahui'));
                        btn.html(originalText).prop('disabled', false);
                    }
                },
                error: function(xhr, status, error) {
                    console.error(error);
                    alert('Terjadi kesalahan jaringan atau server. Silakan coba lagi.');
                    btn.html(originalText).prop('disabled', false);
                }
            });
        });

    });
</script>
<?= $this->endSection() ?>
