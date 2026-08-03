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
    .col-target { min-width: 250px; }
    .col-deskripsi { min-width: 300px; }
    .col-capaian { min-width: 170px; }
    .col-bukti { min-width: 180px; }
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

        <!-- SINGLE UNIFIED TOP ALERT NOTIFICATION -->
        <?php if (isset($target_status) && $target_status === 'belum_ada'): ?>
            <div class="alert alert-danger mb-4 shadow-sm">
                <i class="bi bi-exclamation-triangle-fill me-2"></i> <strong>Target Bulanan Belum Ada!</strong> Anda belum membuat Target Kinerja Bulanan untuk bulan ini. Silakan buat dan kirimkan target terlebih dahulu pada menu <strong>Target Kinerja Bulanan</strong> agar dapat mengisi laporan harian dan tugas tambahan.
            </div>
        <?php elseif (isset($target_status) && $target_status === 'belum_disetujui'): ?>
            <div class="alert alert-warning mb-4 shadow-sm">
                <i class="bi bi-clock-history me-2"></i> <strong>Target Bulanan Belum Disetujui!</strong> Target Kinerja Bulanan Anda untuk bulan ini belum disetujui oleh atasan langsung. Anda baru dapat mengisi Lapor Kegiatan Harian & Tugas Tambahan setelah target Anda disetujui.
            </div>
        <?php elseif ($is_locked): ?>
            <div class="alert alert-warning mb-4 shadow-sm">
                <i class="bi bi-lock-fill me-2"></i> <strong>Akses Terkunci!</strong> Laporan kegiatan harian dan tugas tambahan hari ini telah dikirim dan tidak dapat diubah lagi.
            </div>
        <?php endif; ?>

        <!-- SINGLE UNIFIED FORM FOR BOTH TUGAS POKOK & TAMBAHAN -->
        <form action="<?= site_url('log-kegiatan/store') ?>" method="POST" id="formLog">
            <?= csrf_field() ?>
            <input type="hidden" name="tanggal" value="<?= esc($tanggal_terpilih) ?>">

            <div class="table-responsive mb-3 border rounded shadow-sm bg-white">
                <table class="table table-bordered align-middle table-hover mb-0" id="tabelLog">
                    <thead class="text-center bg-light">
                        <tr>
                            <th style="width: 50px;">No</th>
                            <th class="col-target">Indikator Kinerja / RHK & Tugas</th>
                            <th class="col-deskripsi">Deskripsi Kegiatan</th>
                            <th class="col-capaian" style="width: 170px;">Jumlah Capaian / Output</th>
                            <th class="col-bukti">Bukti Pekerjaan (Link)</th>
                            <th style="width: 70px;">Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        <!-- ==================== BAGIAN A: TUGAS POKOK (RHK) ==================== -->
                        <tr class="table-primary text-primary fw-bold" id="rowHeaderPokok">
                            <td colspan="6" class="py-2.5 px-3 bg-primary-subtle text-primary border-primary">
                                <div class="d-flex justify-content-between align-items-center">
                                    <span class="fs-6"><i class="bi bi-list-task me-2"></i> A. TUGAS POKOK (Rencana Hasil Kerja Utama)</span>
                                    <?php if (!$is_locked): ?>
                                        <button type="button" id="tambahBaris" class="btn btn-sm btn-primary rounded-pill px-3 py-1 shadow-sm">
                                            <i class="bi bi-plus-circle me-1"></i> Tambah Kegiatan Utama
                                        </button>
                                    <?php endif; ?>
                                </div>
                            </td>
                        </tr>

                        <!-- BARIS TUGAS POKOK -->
                        <?php $rowIndex = 1; ?>
                        <?php if (!empty($rekap_data)): ?>
                            <?php foreach ($rekap_data as $row): ?>
                                <tr class="row-tugas-pokok">
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

                        <?php if (!$is_locked && empty($rekap_data)): ?>
                            <!-- Baris Input Baru Default Tugas Pokok -->
                            <tr class="row-tugas-pokok input-row">
                                <input type="hidden" name="log_id[]" value="">
                                <td class="nomor-baris text-center fw-bold">1</td>
                                <td>
                                    <select name="target_id[]" class="form-select">
                                        <option value="">-- Pilih Target / RHK --</option>
                                        <?php foreach($daftar_target as $target): ?>
                                            <?php $labelTarget = esc($target['indikator_kinerja']) . ' (' . str_replace('.', ',', (float)$target['target_bulanan']) . ' ' . esc($target['satuan']) . ')'; ?>
                                            <option value="<?= esc($target['id']) ?>"><?= $labelTarget ?></option>
                                        <?php endforeach; ?>
                                    </select>
                                </td>
                                <td>
                                    <textarea name="deskripsi_kegiatan[]" class="form-control" rows="2" placeholder="Jelaskan apa yang Anda kerjakan hari ini..."></textarea>
                                </td>
                                <td>
                                    <div class="input-group">
                                        <input type="number" step="0.01" name="jumlah_capaian[]" class="form-control text-center" placeholder="Angka">
                                        <span class="input-group-text">-</span>
                                    </div>
                                </td>
                                <td>
                                    <input type="url" name="link_bukti[]" class="form-control" placeholder="https://...">
                                </td>
                                <td class="text-center">
                                    <button type="button" class="btn btn-outline-danger btn-sm hapus-baris" title="Hapus baris baru"><i class="bi bi-trash"></i></button>
                                </td>
                            </tr>
                        <?php endif; ?>


                        <!-- ==================== BAGIAN B: SEPARATOR TUGAS TAMBAHAN ==================== -->
                        <tr class="table-light text-dark fw-bold" id="rowHeaderTambahan">
                            <td colspan="6" class="py-2.5 px-3 bg-light border-top border-bottom border-secondary-subtle">
                                <div class="d-flex justify-content-between align-items-center">
                                    <div>
                                        <i class="bi bi-journal-plus me-2 text-primary"></i> 
                                        <span class="text-dark fs-6">B. TUGAS TAMBAHAN</span>
                                        <small class="text-muted ms-2 fw-normal">(Opsional - Tugas di luar RHK utama bulan ini)</small>
                                    </div>
                                    <?php if (!$is_locked): ?>
                                        <button type="button" id="tambahBarisTambahan" class="btn btn-sm btn-outline-primary rounded-pill px-3 py-1 shadow-sm">
                                            <i class="bi bi-plus-circle me-1"></i> Tambah Tugas Tambahan
                                        </button>
                                    <?php endif; ?>
                                </div>
                            </td>
                        </tr>

                        <!-- BARIS TUGAS TAMBAHAN -->
                        <?php $rowTmbIndex = 1; ?>
                        <?php if (!empty($rekap_data_tambahan)): ?>
                            <?php foreach ($rekap_data_tambahan as $rowTmb): ?>
                                <tr class="row-tugas-tambahan">
                                    <td class="nomor-baris-tmb text-center fw-bold"><?= $rowTmbIndex++ ?></td>
                                    <?php if ($is_locked || (isset($rowTmb['status']) && $rowTmb['status'] === 'terkirim')): ?>
                                        <!-- Read-only View -->
                                        <td class="align-middle">
                                            <div class="p-2 border rounded bg-light text-muted small text-center fw-semibold">
                                                <i class="bi bi-journal-plus text-primary me-1"></i> Tugas Tambahan
                                            </div>
                                        </td>
                                        <td>
                                            <div class="p-2 border rounded bg-light text-muted"><?= nl2br(esc($rowTmb['deskripsi_kegiatan'])) ?></div>
                                        </td>
                                        <td>
                                            <div class="p-2 border rounded bg-light text-center fw-bold text-muted">
                                                <?= !empty($rowTmb['jumlah_capaian']) ? str_replace('.', ',', (float)$rowTmb['jumlah_capaian']) : '-' ?> <?= esc($rowTmb['satuan'] ?? '') ?>
                                            </div>
                                        </td>
                                        <td>
                                            <?php if (!empty($rowTmb['link_bukti'])): ?>
                                                <div class="p-2 border rounded bg-light text-center"><a href="<?= esc($rowTmb['link_bukti']) ?>" target="_blank" class="btn btn-sm btn-outline-primary"><i class="bi bi-link-45deg"></i> Buka Link</a></div>
                                            <?php else: ?>
                                                <div class="p-2 border rounded bg-light text-center text-muted">-</div>
                                            <?php endif; ?>
                                        </td>
                                        <td class="text-center text-success">
                                            <i class="bi bi-lock-fill text-warning" title="Terkunci"></i>
                                        </td>
                                    <?php else: ?>
                                        <!-- Editable Draft View -->
                                        <input type="hidden" name="log_tambahan_id[]" value="<?= $rowTmb['id'] ?>">
                                        <td class="align-middle">
                                            <div class="p-2 border rounded bg-light text-muted small text-center fw-semibold">
                                                <i class="bi bi-journal-plus text-primary me-1"></i> Tugas Tambahan
                                            </div>
                                        </td>
                                        <td>
                                            <textarea name="deskripsi_kegiatan_tambahan[]" class="form-control" rows="2" placeholder="Jelaskan tugas tambahan Anda..."><?= esc($rowTmb['deskripsi_kegiatan']) ?></textarea>
                                        </td>
                                        <td>
                                            <div class="input-group">
                                                <input type="number" step="0.01" name="jumlah_capaian_tambahan[]" class="form-control text-center" placeholder="Angka" value="<?= isset($rowTmb['jumlah_capaian']) ? (float)$rowTmb['jumlah_capaian'] : '' ?>">
                                                <input type="text" name="satuan_tambahan[]" class="form-control text-center" placeholder="Satuan" value="<?= esc($rowTmb['satuan'] ?? '') ?>" style="max-width: 90px;">
                                            </div>
                                        </td>
                                        <td>
                                            <input type="url" name="link_bukti_tambahan[]" class="form-control" placeholder="https://..." value="<?= esc($rowTmb['link_bukti']) ?>">
                                        </td>
                                        <td class="text-center">
                                            <button type="button" class="btn btn-outline-danger btn-sm hapus-baris-tmb" data-id="<?= $rowTmb['id'] ?>" title="Hapus baris"><i class="bi bi-trash"></i></button>
                                        </td>
                                    <?php endif; ?>
                                </tr>
                            <?php endforeach; ?>
                        <?php endif; ?>

                    </tbody>
                </table>
            </div>

            <!-- SINGLE UNIFIED BUTTON TOOLBAR AT BOTTOM -->
            <?php if (!$is_locked): ?>
            <div class="d-flex justify-content-end align-items-center mt-4 gap-2">
                <button type="button" id="btnSimpanSementara" class="btn btn-outline-primary rounded-pill shadow-sm px-4 py-2 fw-bold">
                    <i class="bi bi-cloud-arrow-up me-2"></i> Simpan Sementara
                </button>
                <button type="submit" class="btn btn-primary rounded-pill shadow-sm px-4 py-2 fw-bold">
                    <i class="bi bi-send me-2"></i> Simpan & Kirim Laporan Harian
                </button>
            </div>
            <?php endif; ?>
        </form>
    </div>
</div>

<?= $this->endSection() ?>

<?= $this->section('scripts') ?>
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script>
    $(document).ready(function() {
        const tabelLog = $('#tabelLog tbody');

        function updateRowNumbers() {
            tabelLog.find('tr.row-tugas-pokok').each(function(index) {
                $(this).find('.nomor-baris').text(index + 1);
            });
            tabelLog.find('tr.row-tugas-tambahan').each(function(index) {
                $(this).find('.nomor-baris-tmb').text(index + 1);
            });
        }

        function updateDropdownOptions() {
            let selectedValues = [];
            tabelLog.find('select[name="target_id[]"], .target-select-hidden').each(function() {
                if ($(this).val() !== '') {
                    selectedValues.push($(this).val());
                }
            });

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

        // Tambah Baris Tugas Pokok
        $('#tambahBaris').on('click', function() {
            const templateRow = tabelLog.find('tr.row-tugas-pokok:first');
            let newRow;

            if (templateRow.length > 0) {
                newRow = templateRow.clone();
                newRow.find('input[name="log_id[]"]').val('');
                newRow.find('input[type="number"]').val('');
                newRow.find('input[type="url"]').val('');
                newRow.find('textarea').val('');
                newRow.find('select').val('');
                newRow.find('.input-group-text').text('-');
                newRow.find('.hapus-baris').removeAttr('data-id');
            } else {
                newRow = `
                <tr class="row-tugas-pokok input-row">
                    <input type="hidden" name="log_id[]" value="">
                    <td class="nomor-baris text-center fw-bold">1</td>
                    <td>
                        <select name="target_id[]" class="form-select">
                            <option value="">-- Pilih Target / RHK --</option>
                            <?php foreach($daftar_target as $target): ?>
                                <?php $labelTarget = esc($target['indikator_kinerja']) . ' (' . str_replace('.', ',', (float)$target['target_bulanan']) . ' ' . esc($target['satuan']) . ')'; ?>
                                <option value="<?= esc($target['id']) ?>"><?= $labelTarget ?></option>
                            <?php endforeach; ?>
                        </select>
                    </td>
                    <td>
                        <textarea name="deskripsi_kegiatan[]" class="form-control" rows="2" placeholder="Jelaskan apa yang Anda kerjakan hari ini..."></textarea>
                    </td>
                    <td>
                        <div class="input-group">
                            <input type="number" step="0.01" name="jumlah_capaian[]" class="form-control text-center" placeholder="Angka">
                            <span class="input-group-text">-</span>
                        </div>
                    </td>
                    <td>
                        <input type="url" name="link_bukti[]" class="form-control" placeholder="https://...">
                    </td>
                    <td class="text-center">
                        <button type="button" class="btn btn-outline-danger btn-sm hapus-baris" title="Hapus baris"><i class="bi bi-trash"></i></button>
                    </td>
                </tr>`;
            }
            
            $('#rowHeaderTambahan').before(newRow);
            updateRowNumbers();
            updateDropdownOptions();
        });

        // Hapus Baris Tugas Pokok
        tabelLog.on('click', '.hapus-baris', function() {
            const pokokRows = tabelLog.find('tr.row-tugas-pokok');
            if (pokokRows.length > 1) {
                const idLog = $(this).attr('data-id');
                const row = $(this).closest('tr');

                if(idLog) {
                    if(confirm('Apakah Anda yakin ingin menghapus catatan kegiatan ini?')) {
                        $.post('<?= site_url('log-kegiatan/hapus') ?>', { id: idLog, <?= csrf_token() ?>: '<?= csrf_hash() ?>' }, function(response) {
                            if(response.success) {
                                row.remove();
                                updateRowNumbers();
                                updateDropdownOptions();
                            } else {
                                alert('Gagal menghapus data.');
                            }
                        });
                    }
                } else {
                    row.remove();
                    updateRowNumbers();
                    updateDropdownOptions();
                }
            } else {
                alert('Minimal harus mengisi satu kegiatan harian utama.');
            }
        });

        // Tambah Baris Tugas Tambahan
        $('#tambahBarisTambahan').on('click', function() {
            const templateRow = tabelLog.find('tr.row-tugas-tambahan:last');
            let newRow;

            if (templateRow.length > 0) {
                newRow = templateRow.clone();
                newRow.find('input[name="log_tambahan_id[]"]').val('');
                newRow.find('textarea').val('');
                newRow.find('input[name="jumlah_capaian_tambahan[]"]').val('');
                newRow.find('input[name="satuan_tambahan[]"]').val('');
                newRow.find('input[type="url"]').val('');
                newRow.find('.hapus-baris-tmb').removeAttr('data-id');
            } else {
                newRow = `
                <tr class="row-tugas-tambahan">
                    <input type="hidden" name="log_tambahan_id[]" value="">
                    <td class="nomor-baris-tmb text-center fw-bold">1</td>
                    <td class="align-middle">
                        <div class="p-2 border rounded bg-light text-muted small text-center fw-semibold">
                            <i class="bi bi-journal-plus text-primary me-1"></i> Tugas Tambahan
                        </div>
                    </td>
                    <td>
                        <textarea name="deskripsi_kegiatan_tambahan[]" class="form-control" rows="2" placeholder="Jelaskan tugas tambahan yang Anda kerjakan hari ini..."></textarea>
                    </td>
                    <td>
                        <div class="input-group">
                            <input type="number" step="0.01" name="jumlah_capaian_tambahan[]" class="form-control text-center" placeholder="Angka">
                            <input type="text" name="satuan_tambahan[]" class="form-control text-center" placeholder="Satuan" style="max-width: 90px;">
                        </div>
                    </td>
                    <td>
                        <input type="url" name="link_bukti_tambahan[]" class="form-control" placeholder="https://...">
                    </td>
                    <td class="text-center">
                        <button type="button" class="btn btn-outline-danger btn-sm hapus-baris-tmb" title="Hapus"><i class="bi bi-trash"></i></button>
                    </td>
                </tr>`;
            }
            tabelLog.append(newRow);
            updateRowNumbers();
        });

        // Hapus Baris Tugas Tambahan
        tabelLog.on('click', '.hapus-baris-tmb', function() {
            const tr = $(this).closest('tr');
            const logId = $(this).data('id');

            if (logId) {
                Swal.fire({
                    title: 'Hapus Tugas Tambahan?',
                    text: "Data yang sudah disimpan akan dihapus permanen.",
                    icon: 'warning',
                    showCancelButton: true,
                    confirmButtonColor: '#d33',
                    cancelButtonColor: '#3085d6',
                    confirmButtonText: 'Ya, Hapus!'
                }).then((result) => {
                    if (result.isConfirmed) {
                        $.ajax({
                            url: '<?= site_url("log-kegiatan/hapusTugasTambahan") ?>',
                            type: 'POST',
                            data: {
                                id: logId,
                                csrf_test_name: $('input[name="csrf_test_name"]').val()
                            },
                            success: function(response) {
                                if(response.success) {
                                    if (response.csrf_hash) $('input[name="csrf_test_name"]').val(response.csrf_hash);
                                    tr.remove();
                                    updateRowNumbers();
                                    Swal.fire('Terhapus!', 'Data tugas tambahan berhasil dihapus.', 'success');
                                } else {
                                    Swal.fire('Gagal!', response.message || 'Gagal menghapus.', 'error');
                                }
                            },
                            error: function() {
                                Swal.fire('Error', 'Terjadi kesalahan sistem.', 'error');
                            }
                        });
                    }
                });
            } else {
                tr.remove();
                updateRowNumbers();
            }
        });

        // Auto update satuan text based on selected target
        tabelLog.on('change', 'select[name="target_id[]"]', function() {
            let selectedText = $(this).find('option:selected').text();
            let matches = selectedText.match(/\d+ (.+)\)$/);
            let satuan = matches ? matches[1] : '-';
            $(this).closest('tr').find('.input-group-text').text(satuan);
            updateDropdownOptions();
        });

        updateDropdownOptions();

        // Validasi Form saat klik "Simpan & Kirim Laporan Harian" (Form Submit Normal)
        $('#formLog').on('submit', function(e) {
            let isValid = true;
            let hasAtLeastOne = false;

            $('#formLog tr.row-tugas-pokok').each(function() {
                let targetId = $(this).find('select[name="target_id[]"]').val();
                let deskripsi = $(this).find('textarea[name="deskripsi_kegiatan[]"]').val();
                let capaian = $(this).find('input[name="jumlah_capaian[]"]').val();
                let link = $(this).find('input[name="link_bukti[]"]').val();

                if (targetId) targetId = targetId.trim();
                if (deskripsi) deskripsi = deskripsi.trim();
                if (capaian) capaian = capaian.trim();
                if (link) link = link.trim();

                // Jika salah satu kolom di baris ini terisi
                if (targetId || deskripsi || capaian || link) {
                    hasAtLeastOne = true;
                    if (!targetId || !deskripsi || capaian === '' || !link) {
                        isValid = false;
                    }
                }
            });

            if (!hasAtLeastOne) {
                e.preventDefault();
                Swal.fire('Peringatan', 'Silakan isi minimal satu kegiatan harian pada Tugas Pokok sebelum mengirim ke atasan langsung.', 'warning');
                return false;
            }

            if (!isValid) {
                e.preventDefault();
                Swal.fire('Peringatan', 'Untuk mengirim laporan harian ke atasan langsung, pastikan Target RHK, Deskripsi Kegiatan, Jumlah Capaian, dan Link Bukti Pekerjaan pada Tugas Pokok terisi dengan lengkap.', 'warning');
                return false;
            }
        });

        // Single Unified Submit "Simpan Sementara" (AJAX)
        $('#btnSimpanSementara').on('click', function() {
            let btn = $(this);
            let originalText = btn.html();
            
            btn.html('<span class="spinner-border spinner-border-sm me-2" role="status" aria-hidden="true"></span> Menyimpan...').prop('disabled', true);
            
            $.ajax({
                url: '<?= site_url('log-kegiatan/store') ?>',
                type: 'POST',
                data: $('#formLog').serialize() + '&action=draft',
                dataType: 'json',
                success: function(response) {
                    if (response.csrf_hash) {
                        $('input[name="<?= csrf_token() ?>"]').val(response.csrf_hash);
                    }

                    if (response.success) {
                        btn.html('<i class="bi bi-check-lg me-2"></i> Tersimpan Draf').removeClass('btn-outline-primary').addClass('btn-outline-success');
                        
                        if (response.new_ids) {
                            let i = 0;
                            tabelLog.find('tr.row-tugas-pokok').each(function() {
                                let inputHidden = $(this).find('input[name="log_id[]"]');
                                if (!inputHidden.val() && response.new_ids[i]) {
                                    inputHidden.val(response.new_ids[i]);
                                }
                                i++;
                            });
                        }

                        if (response.new_tambahan_ids) {
                            let j = 0;
                            tabelLog.find('tr.row-tugas-tambahan').each(function() {
                                let inputHidden = $(this).find('input[name="log_tambahan_id[]"]');
                                let btnHapus = $(this).find('.hapus-baris-tmb');
                                if (!inputHidden.val() && response.new_tambahan_ids[j]) {
                                    inputHidden.val(response.new_tambahan_ids[j]);
                                    btnHapus.attr('data-id', response.new_tambahan_ids[j]);
                                }
                                j++;
                            });
                        }
                        
                        Swal.fire({
                            icon: 'success',
                            title: 'Tersimpan Draf',
                            text: response.message || 'Laporan harian & tugas tambahan berhasil disimpan sementara.',
                            timer: 2000,
                            showConfirmButton: false
                        });

                        setTimeout(() => {
                            btn.html(originalText).removeClass('btn-outline-success').addClass('btn-outline-primary').prop('disabled', false);
                        }, 2500);
                    } else {
                        Swal.fire('Gagal', response.message || 'Gagal menyimpan.', 'error');
                        btn.html(originalText).prop('disabled', false);
                    }
                },
                error: function(xhr, status, error) {
                    console.error(error);
                    Swal.fire('Error', 'Terjadi kesalahan jaringan atau server. Silakan coba lagi.', 'error');
                    btn.html(originalText).prop('disabled', false);
                }
            });
        });

    });
</script>
<?= $this->endSection() ?>
