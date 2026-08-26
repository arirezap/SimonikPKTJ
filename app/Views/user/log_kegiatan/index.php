<?= $this->extend('layouts/main') ?>

<?= $this->section('title') ?>Lapor Kegiatan Harian<?= $this->endSection() ?>

<?= $this->section('styles') ?>
<style>
    @media (max-width: 767.98px) {
        .form-control, .form-select {
            font-size: 16px !important;
        }
    }
    .num-tabular {
        font-variant-numeric: tabular-nums;
    }
    .col-target-log { min-width: 240px; }
    .col-deskripsi-log { min-width: 260px; }
    .col-capaian-log { width: 155px; min-width: 150px; }
    .col-bukti-log { min-width: 190px; }
    
    .table-responsive {
        overflow-x: auto !important;
        -webkit-overflow-scrolling: touch;
    }
    .table-bento {
        border-collapse: separate;
        border-spacing: 0;
        margin-bottom: 0;
        min-width: 920px;
    }
    .table-bento thead th {
        background-color: #f8fafc !important;
        color: #475569 !important;
        font-weight: 700;
        font-size: 0.78rem;
        text-transform: uppercase;
        letter-spacing: 0.4px;
        border-bottom: 2px solid #e2e8f0;
        padding: 0.65rem 0.75rem;
        vertical-align: middle;
    }
    .table-bento tbody td {
        padding: 0.65rem 0.75rem;
        vertical-align: middle;
        border-color: #f1f5f9;
    }
    .table-bento tbody tr:last-child td {
        border-bottom: 0;
    }
    
    .input-capaian-num::-webkit-outer-spin-button,
    .input-capaian-num::-webkit-inner-spin-button {
        -webkit-appearance: none;
        margin: 0;
    }
    .input-capaian-num {
        -moz-appearance: textfield;
    }

    .readonly-box {
        background-color: #f8fafc;
        border: 1px solid #e2e8f0;
        border-radius: 8px;
        padding: 0.5rem 0.75rem;
        font-size: 0.85rem;
    }
</style>
<?= $this->endSection() ?>

<?= $this->section('content') ?>
<div class="container-fluid px-2 px-md-3">
    <!-- PAGE HEADER -->
    <div class="d-flex justify-content-between flex-wrap align-items-center pt-2 pb-2 mb-3 border-bottom gap-2">
        <div>
            <h1 class="h4 mb-0 fw-bold text-dark"><i class="bi bi-calendar-check text-primary me-2"></i>Lapor Kegiatan Harian</h1>
            <p class="text-muted small mb-0">Catat realisasi aktivitas kerja harian beserta tautan bukti pekerjaan.</p>
        </div>
        <div>
            <span class="badge bg-primary-subtle text-primary border border-primary-subtle rounded-pill px-3 py-1.5 small fw-semibold">
                <i class="bi bi-calendar-date me-1"></i> <?= date('d M Y', strtotime($tanggal_terpilih)) ?>
            </span>
        </div>
    </div>

    <?php if (session()->getFlashdata('success')) : ?>
        <div class="alert alert-success alert-dismissible fade show shadow-sm py-2 px-3 small mb-3 rounded-3" role="alert">
            <i class="bi bi-check-circle-fill me-2"></i> <?= session()->getFlashdata('success') ?>
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    <?php endif; ?>

    <?php if (session()->getFlashdata('error')) : ?>
        <div class="alert alert-danger alert-dismissible fade show shadow-sm py-2 px-3 small mb-3 rounded-3" role="alert">
            <i class="bi bi-exclamation-triangle-fill me-2"></i> <?= session()->getFlashdata('error') ?>
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    <?php endif; ?>

    <div class="card shadow-sm border-0 mb-4 rounded-4 overflow-hidden">
        <div class="card-body p-3 p-md-4">
            
            <!-- Filter Tanggal Toolbar -->
            <form method="GET" action="<?= site_url('log-kegiatan') ?>" class="mb-3 p-3 bg-light rounded-4 border border-light-subtle">
                <div class="row g-2 align-items-center">
                    <div class="col-sm-5 col-md-3">
                        <label class="form-label fw-bold text-dark small mb-1" style="font-size: 0.72rem; letter-spacing: 0.3px;"><i class="bi bi-calendar-event text-primary me-1"></i> Tanggal Kegiatan</label>
                        <div class="input-group input-group-sm shadow-sm rounded-3 overflow-hidden">
                            <span class="input-group-text bg-primary text-white border-primary"><i class="bi bi-calendar3"></i></span>
                            <input type="date" name="tanggal" class="form-control border-primary fw-bold text-primary" value="<?= esc($tanggal_terpilih) ?>" max="<?= date('Y-m-d') ?>" onchange="this.form.submit()">
                        </div>
                    </div>
                    <div class="col-sm-7 col-md-9 text-muted pt-sm-3 small">
                        <i class="bi bi-info-circle text-primary me-1"></i> Jika pilihan target kosong, pastikan Anda telah menyusun <strong>Target Kinerja Bulanan</strong> yang sudah disetujui atasan.
                    </div>
                </div>
            </form>

            <!-- SINGLE UNIFIED TOP ALERT NOTIFICATION -->
            <?php if (isset($target_status) && $target_status === 'belum_ada'): ?>
                <div class="alert alert-danger mb-3 shadow-sm py-2.5 px-3 small rounded-4 d-flex align-items-center gap-2">
                    <i class="bi bi-exclamation-triangle-fill fs-5 text-danger flex-shrink-0"></i>
                    <div>
                        <strong>Target Belum Ada.</strong> Silakan susun target pada menu <strong>Target Kinerja Bulanan</strong> terlebih dahulu.
                    </div>
                </div>
            <?php elseif (isset($target_status) && $target_status === 'belum_disetujui'): ?>
                <div class="alert alert-warning mb-3 shadow-sm py-2.5 px-3 small rounded-4 d-flex align-items-center gap-2">
                    <i class="bi bi-clock-history fs-5 text-warning-emphasis flex-shrink-0"></i>
                    <div>
                        <strong>Target Belum Disetujui.</strong> Menunggu persetujuan atasan langsung sebelum dapat mengisi laporan kegiatan harian.
                    </div>
                </div>
            <?php elseif ($is_locked): ?>
                <div class="alert alert-warning mb-3 shadow-sm py-2.5 px-3 d-flex align-items-center justify-content-between flex-wrap gap-2 small rounded-4">
                    <div class="d-flex align-items-center gap-2">
                        <i class="bi bi-lock-fill text-warning-emphasis fs-5 flex-shrink-0"></i>
                        <div>
                            <strong>Laporan Terkunci.</strong> Laporan pada tanggal ini telah dikirim ke atasan dan berada dalam status terkunci.
                        </div>
                    </div>
                    <?php if (hasRole('admin')): ?>
                    <button type="button" id="btnBukaKunci"
                        class="btn btn-sm btn-outline-danger rounded-pill px-3 fw-semibold shadow-sm"
                        data-tanggal="<?= esc($tanggal_terpilih) ?>"
                        data-user-id="<?= esc(session()->get('id') ?? session()->get('user_id')) ?>"
                        title="Buka kunci laporan agar staf dapat merevisi">
                        <i class="bi bi-unlock-fill me-1"></i> Buka Kunci (Admin)
                    </button>
                    <?php endif; ?>
                </div>
            <?php endif; ?>

        <datalist id="daftarSatuanStandar">
            <option value="Laporan">
            <option value="Dokumen">
            <option value="Pertemuan">
            <option value="Kegiatan">
            <option value="Mahasiswa">
            <option value="Peserta">
            <option value="Sertifikat">
            <option value="Paket Kegiatan">
            <option value="Jam Pelajaran (JP)">
            <option value="Surat Keputusan (SK)">
        </datalist>

        <!-- SINGLE UNIFIED FORM FOR BOTH TUGAS POKOK & TAMBAHAN -->
        <form action="<?= site_url('log-kegiatan/store') ?>" method="POST" id="formLog">
            <?= csrf_field() ?>
            <input type="hidden" name="tanggal" value="<?= esc($tanggal_terpilih) ?>">

            <div class="table-responsive mb-3 border rounded-4 shadow-sm bg-white">
                <table class="table table-bordered align-middle table-hover mb-0 table-bento" id="tabelLog">
                    <thead>
                        <tr>
                            <th style="width: 45px;" class="text-center">No</th>
                            <th class="col-target-log">Indikator RHK / Tugas</th>
                            <th class="col-deskripsi-log">Deskripsi Kegiatan</th>
                            <th class="col-capaian-log text-center">Capaian</th>
                            <th class="col-bukti-log">Bukti Pekerjaan</th>
                            <th style="width: 55px;" class="text-center">Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        <!-- ==================== BAGIAN A: TUGAS POKOK (RHK) ==================== -->
                        <tr class="table-primary text-primary fw-bold" id="rowHeaderPokok">
                            <td colspan="6" class="py-2.5 px-3 bg-primary-subtle text-primary border-primary">
                                <div class="d-flex justify-content-between align-items-center header-section-wrapper">
                                    <span class="fs-6 fw-bold"><i class="bi bi-list-task me-2"></i> A. TUGAS POKOK (RHK)</span>
                                    <?php if (!$is_locked): ?>
                                        <button type="button" id="tambahBaris" class="btn btn-sm btn-primary rounded-pill px-3 py-1 shadow-sm fw-semibold">
                                             <i class="bi bi-plus-circle me-1"></i> Tambah Kegiatan
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
                                    <td class="nomor-baris text-center fw-bold text-muted"><?= $rowIndex++ ?></td>
                                    <?php if ($is_locked || (isset($row['status']) && $row['status'] === 'terkirim')): ?>
                                        <!-- Read-only View -->
                                        <td>
                                            <?php 
                                            $targetName = '-';
                                            if (!empty($row['indikator_kinerja'])) {
                                                $targetName = esc($row['indikator_kinerja']) . (!empty($row['satuan']) ? ' (' . (!empty($row['target_bulanan']) ? str_replace('.', ',', (float)$row['target_bulanan']) . ' ' : '') . esc($row['satuan']) . ')' : '');
                                            } else {
                                                foreach($daftar_target as $target) {
                                                    if ($target['id'] == $row['target_id']) {
                                                        $targetName = esc($target['indikator_kinerja']) . ' (' . str_replace('.', ',', (float)$target['target_bulanan']) . ' ' . esc($target['satuan']) . ')';
                                                        break;
                                                    }
                                                }
                                            }
                                            ?>
                                            <input type="hidden" class="target-select-hidden" value="<?= esc($row['target_id']) ?>">
                                            <div class="readonly-box text-dark fw-semibold"><?= $targetName ?></div>
                                        </td>
                                        <td>
                                            <div class="readonly-box text-secondary"><?= nl2br(esc($row['deskripsi_kegiatan'])) ?></div>
                                        </td>
                                        <td class="col-capaian-log">
                                            <div class="readonly-box text-center fw-bold text-primary num-tabular readonly-capaian-box" title="<?= str_replace('.', ',', (float)$row['jumlah_capaian']) ?> <?= esc($row['satuan']) ?>">
                                                <?= str_replace('.', ',', (float)$row['jumlah_capaian']) ?> <?= esc($row['satuan']) ?>
                                            </div>
                                        </td>
                                        <td>
                                            <?php if (!empty($row['link_bukti'])): ?>
                                                <div class="text-center">
                                                    <a href="<?= esc($row['link_bukti']) ?>" target="_blank" class="btn btn-sm btn-light text-primary border rounded-pill px-3 py-1" title="Buka tautan bukti pekerjaan di tab baru">
                                                        <i class="bi bi-box-arrow-up-right me-1"></i> Bukti
                                                    </a>
                                                </div>
                                            <?php else: ?>
                                                <div class="readonly-box text-center text-muted">-</div>
                                            <?php endif; ?>
                                        </td>
                                        <td class="text-center text-success">
                                            <i class="bi bi-lock-fill text-warning fs-6" title="Terkunci"></i>
                                        </td>
                                    <?php else: ?>
                                        <!-- Editable Draft View -->
                                        <input type="hidden" name="log_id[]" value="<?= $row['id'] ?>">
                                        <td>
                                            <select name="target_id[]" class="form-select form-select-sm">
                                                <option value="">-- Pilih Target / RHK --</option>
                                                 <?php 
                                                 $foundSelected = false;
                                                 foreach($daftar_target as $target): 
                                                     $cleanIndikator = str_replace('`', '', esc($target['indikator_kinerja']));
                                                     $cleanSatuan = str_replace('`', '', esc($target['satuan']));
                                                     $labelTarget = $cleanIndikator . ' (' . str_replace('.', ',', (float)$target['target_bulanan']) . ' ' . $cleanSatuan . ')'; 
                                                     $selected = ($target['id'] == $row['target_id']) ? 'selected' : '';
                                                     if ($selected) $foundSelected = true;
                                                 ?>
                                                     <option value="<?= esc($target['id']) ?>" data-satuan="<?= $cleanSatuan ?>" <?= $selected ?>><?= $labelTarget ?></option>
                                                 <?php endforeach; ?>
                                                 <?php if (!$foundSelected && !empty($row['target_id']) && !empty($row['indikator_kinerja'])): ?>
                                                     <option value="<?= esc($row['target_id']) ?>" data-satuan="<?= esc($row['satuan'] ?? '') ?>" selected><?= esc($row['indikator_kinerja']) ?> (<?= esc($row['satuan'] ?? '') ?>)</option>
                                                 <?php endif; ?>
                                            </select>
                                        </td>
                                        <td>
                                            <textarea name="deskripsi_kegiatan[]" class="form-control form-control-sm" rows="2" placeholder="Jelaskan apa yang Anda kerjakan hari ini..."><?= esc($row['deskripsi_kegiatan']) ?></textarea>
                                        </td>
                                        <td class="col-capaian-log">
                                            <div class="input-group input-group-sm shadow-sm rounded-3 overflow-hidden">
                                                <input type="number" step="0.01" name="jumlah_capaian[]" class="form-control input-capaian-num input-target-val text-center num-tabular fw-bold text-primary" placeholder="0" value="<?= (float)$row['jumlah_capaian'] ?>">
                                                <span class="input-group-text badge-capaian-satuan bg-light" title="<?= esc($row['satuan'] ?? '-') ?>"><?= esc($row['satuan'] ?? '-') ?></span>
                                            </div>
                                        </td>
                                        <td>
                                            <input type="url" name="link_bukti[]" class="form-control form-control-sm" placeholder="https://..." value="<?= esc($row['link_bukti']) ?>">
                                        </td>
                                        <td class="text-center">
                                            <button type="button" class="btn btn-outline-danger btn-sm rounded-circle hapus-baris" data-id="<?= $row['id'] ?>" title="Hapus baris" style="width: 32px; height: 32px; display: inline-flex; align-items: center; justify-content: center;"><i class="bi bi-trash3"></i></button>
                                        </td>
                                    <?php endif; ?>
                                </tr>
                            <?php endforeach; ?>
                        <?php endif; ?>

                        <?php if (!$is_locked && empty($rekap_data)): ?>
                            <!-- Baris Input Baru Default Tugas Pokok -->
                            <tr class="row-tugas-pokok input-row">
                                <input type="hidden" name="log_id[]" value="">
                                <td class="nomor-baris text-center fw-bold text-muted">1</td>
                                <td>
                                    <select name="target_id[]" class="form-select form-select-sm">
                                        <option value="">-- Pilih Target / RHK --</option>
                                        <?php foreach($daftar_target as $target): ?>
                                             <?php 
                                             $cleanIndikator = str_replace('`', '', esc($target['indikator_kinerja']));
                                             $cleanSatuan = str_replace('`', '', esc($target['satuan']));
                                             $labelTarget = $cleanIndikator . ' (' . str_replace('.', ',', (float)$target['target_bulanan']) . ' ' . $cleanSatuan . ')'; 
                                             ?>
                                             <option value="<?= esc($target['id']) ?>" data-satuan="<?= $cleanSatuan ?>"><?= $labelTarget ?></option>
                                         <?php endforeach; ?>
                                    </select>
                                </td>
                                <td>
                                    <textarea name="deskripsi_kegiatan[]" class="form-control form-control-sm" rows="2" placeholder="Jelaskan apa yang Anda kerjakan hari ini..."></textarea>
                                </td>
                                <td class="col-capaian-log">
                                    <div class="input-group input-group-sm shadow-sm rounded-3 overflow-hidden">
                                        <input type="number" step="0.01" name="jumlah_capaian[]" class="form-control input-capaian-num input-target-val text-center num-tabular fw-bold text-primary" placeholder="0">
                                        <span class="input-group-text badge-capaian-satuan bg-light">-</span>
                                    </div>
                                </td>
                                <td>
                                    <input type="url" name="link_bukti[]" class="form-control form-control-sm" placeholder="https://...">
                                </td>
                                <td class="text-center">
                                    <button type="button" class="btn btn-outline-danger btn-sm rounded-circle hapus-baris" title="Hapus baris baru" style="width: 32px; height: 32px; display: inline-flex; align-items: center; justify-content: center;"><i class="bi bi-trash3"></i></button>
                                </td>
                            </tr>
                        <?php endif; ?>


                        <!-- ==================== BAGIAN B: SEPARATOR TUGAS TAMBAHAN ==================== -->
                        <tr class="table-light text-dark fw-bold" id="rowHeaderTambahan">
                            <td colspan="6" class="py-2.5 px-3 bg-light border-top border-bottom border-secondary-subtle">
                                <div class="d-flex justify-content-between align-items-center header-section-wrapper">
                                    <div>
                                        <i class="bi bi-journal-plus me-2 text-success"></i> 
                                        <span class="text-dark fs-6 fw-bold">B. TUGAS TAMBAHAN</span>
                                    </div>
                                    <?php if (!$is_locked): ?>
                                        <button type="button" id="tambahBarisTambahan" class="btn btn-sm btn-outline-success rounded-pill px-3 py-1 shadow-sm fw-semibold">
                                            <i class="bi bi-plus-circle me-1"></i> Tambah Tugas
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
                                    <td class="nomor-baris-tmb text-center fw-bold text-muted"><?= $rowTmbIndex++ ?></td>
                                    <?php if ($is_locked || (isset($rowTmb['status']) && $rowTmb['status'] === 'terkirim')): ?>
                                        <!-- Read-only View -->
                                        <td class="align-middle">
                                            <div class="badge bg-success-subtle text-success border border-success-subtle px-2.5 py-1 rounded-pill">
                                                <i class="bi bi-journal-plus me-1"></i> Tugas Tambahan
                                            </div>
                                        </td>
                                        <td>
                                            <div class="readonly-box text-secondary"><?= nl2br(esc($rowTmb['deskripsi_kegiatan'])) ?></div>
                                        </td>
                                        <td class="col-capaian-log">
                                            <div class="readonly-box text-center fw-bold text-success num-tabular readonly-capaian-box" title="<?= (isset($rowTmb['jumlah_capaian']) && $rowTmb['jumlah_capaian'] !== null && $rowTmb['jumlah_capaian'] !== '') ? str_replace('.', ',', (float)$rowTmb['jumlah_capaian']) : '-' ?> <?= esc($rowTmb['satuan'] ?? '') ?>">
                                                <?= (isset($rowTmb['jumlah_capaian']) && $rowTmb['jumlah_capaian'] !== null && $rowTmb['jumlah_capaian'] !== '') ? str_replace('.', ',', (float)$rowTmb['jumlah_capaian']) : '-' ?> <?= esc($rowTmb['satuan'] ?? '') ?>
                                            </div>
                                        </td>
                                        <td>
                                            <?php if (!empty($rowTmb['link_bukti'])): ?>
                                                <div class="text-center">
                                                    <a href="<?= esc($rowTmb['link_bukti']) ?>" target="_blank" class="btn btn-sm btn-light text-primary border rounded-pill px-3 py-1" title="Buka tautan bukti pekerjaan di tab baru">
                                                        <i class="bi bi-box-arrow-up-right me-1"></i> Bukti
                                                    </a>
                                                </div>
                                            <?php else: ?>
                                                <div class="readonly-box text-center text-muted">-</div>
                                            <?php endif; ?>
                                        </td>
                                        <td class="text-center text-success">
                                            <i class="bi bi-lock-fill text-warning fs-6" title="Terkunci"></i>
                                        </td>
                                    <?php else: ?>
                                        <!-- Editable Draft View -->
                                        <input type="hidden" name="log_tambahan_id[]" value="<?= $rowTmb['id'] ?>">
                                        <td class="align-middle">
                                            <div class="badge bg-success-subtle text-success border border-success-subtle px-2.5 py-1 rounded-pill">
                                                <i class="bi bi-journal-plus me-1"></i> Tugas Tambahan
                                            </div>
                                        </td>
                                        <td>
                                            <textarea name="deskripsi_kegiatan_tambahan[]" class="form-control form-control-sm" rows="2" placeholder="Jelaskan tugas tambahan Anda..."><?= esc($rowTmb['deskripsi_kegiatan']) ?></textarea>
                                        </td>
                                        <td class="col-capaian-log">
                                            <div class="input-group input-group-sm shadow-sm rounded-3 overflow-hidden">
                                                <input type="number" step="0.01" name="jumlah_capaian_tambahan[]" class="form-control input-capaian-num input-target-val text-center num-tabular fw-bold text-success" placeholder="0" value="<?= isset($rowTmb['jumlah_capaian']) ? (float)$rowTmb['jumlah_capaian'] : '' ?>">
                                                <input type="text" name="satuan_tambahan[]" class="form-control input-satuan-val text-center" placeholder="Satuan" list="daftarSatuanStandar" value="<?= esc($rowTmb['satuan'] ?? '') ?>" title="<?= esc($rowTmb['satuan'] ?? '') ?>">
                                            </div>
                                        </td>
                                        <td>
                                            <input type="url" name="link_bukti_tambahan[]" class="form-control form-control-sm" placeholder="https://..." value="<?= esc($rowTmb['link_bukti']) ?>">
                                        </td>
                                        <td class="text-center">
                                            <button type="button" class="btn btn-outline-danger btn-sm rounded-circle hapus-baris-tmb" data-id="<?= $rowTmb['id'] ?>" title="Hapus baris" style="width: 32px; height: 32px; display: inline-flex; align-items: center; justify-content: center;"><i class="bi bi-trash3"></i></button>
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
            <div class="d-flex justify-content-end align-items-center mt-4 gap-2 btn-action-container">
                <button type="button" id="btnSimpanSementara" class="btn btn-outline-primary rounded-pill shadow-sm px-4 py-2 fw-semibold">
                    <i class="bi bi-cloud-arrow-up me-1.5"></i> Simpan Draf
                </button>
                <button type="submit" class="btn btn-primary rounded-pill shadow-sm px-4 py-2 fw-bold">
                    <i class="bi bi-send me-1.5"></i> Kirim Laporan
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

        // Cegah perubahan nilai angka secara tidak sengaja saat pengguna scrolling halaman dengan mouse wheel
        $(document).on('wheel', 'input[type="number"]', function (e) {
            $(this).blur();
        });

        function updateRowNumbers() {
            tabelLog.find('tr.row-tugas-pokok').each(function(index) {
                $(this).find('.nomor-baris').text(index + 1);
            });
            tabelLog.find('tr.row-tugas-tambahan').each(function(index) {
                $(this).find('.nomor-baris-tmb').text(index + 1);
            });
        }

        function updateDropdownOptions() {
            // Mengizinkan 1 RHK dipilih untuk lebih dari 1 baris kegiatan harian dalam 1 hari
            tabelLog.find('select[name="target_id[]"]').each(function() {
                $(this).find('option').prop('disabled', false).show();
            });
        }

        // Tambah Baris Tugas Pokok
        $('#tambahBaris').on('click', function() {
            const templateRow = tabelLog.find('tr.row-tugas-pokok:first');
            let newRow;

            if (templateRow.length > 0 && templateRow.find('select[name="target_id[]"]').length > 0) {
                newRow = templateRow.clone();
                newRow.find('input[name="log_id[]"]').val('');
                newRow.find('input[type="number"]').val('');
                newRow.find('input[type="url"]').val('');
                newRow.find('textarea').val('');
                newRow.find('select').val('');
                newRow.find('.input-group-text').text('-');
                newRow.find('.hapus-baris').removeAttr('data-id');
                newRow.find('.is-invalid').removeClass('is-invalid');
            } else {
                newRow = `
                <tr class="row-tugas-pokok input-row">
                    <input type="hidden" name="log_id[]" value="">
                    <td class="nomor-baris text-center fw-bold text-muted">1</td>
                    <td>
                        <select name="target_id[]" class="form-select form-select-sm">
                            <option value="">-- Pilih Target / RHK --</option>
                            <?php foreach($daftar_target as $target): ?>
                                <?php 
                                $cleanIndikator = str_replace('`', '', esc($target['indikator_kinerja']));
                                $cleanSatuan = str_replace('`', '', esc($target['satuan']));
                                $labelTarget = $cleanIndikator . ' (' . str_replace('.', ',', (float)$target['target_bulanan']) . ' ' . $cleanSatuan . ')'; 
                                ?>
                                <option value="<?= esc($target['id']) ?>" data-satuan="<?= $cleanSatuan ?>"><?= $labelTarget ?></option>
                            <?php endforeach; ?>
                        </select>
                    </td>
                    <td>
                        <textarea name="deskripsi_kegiatan[]" class="form-control form-control-sm" rows="2" placeholder="Jelaskan apa yang Anda kerjakan hari ini..."></textarea>
                    </td>
                    <td class="col-capaian-log">
                        <div class="input-group input-group-sm shadow-sm rounded-3 overflow-hidden">
                            <input type="number" step="0.01" name="jumlah_capaian[]" class="form-control input-capaian-num input-target-val text-center num-tabular fw-bold text-primary" placeholder="0">
                            <span class="input-group-text badge-capaian-satuan bg-light">-</span>
                        </div>
                    </td>
                    <td>
                        <input type="url" name="link_bukti[]" class="form-control form-control-sm" placeholder="https://...">
                    </td>
                    <td class="text-center">
                        <button type="button" class="btn btn-outline-danger btn-sm rounded-circle hapus-baris" title="Hapus baris" style="width: 32px; height: 32px; display: inline-flex; align-items: center; justify-content: center;"><i class="bi bi-trash3"></i></button>
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
            const idLog = $(this).attr('data-id');
            const row = $(this).closest('tr');

            // Cek apakah baris ini kosong (tidak ada target/deskripsi terisi)
            const targetVal = row.find('select[name="target_id[]"]').val();
            const deskripsiVal = row.find('textarea[name="deskripsi_kegiatan[]"]').val();
            const isRowEmpty = !targetVal && (!deskripsiVal || !deskripsiVal.trim());

            // Cek apakah ada Tugas Tambahan sebagai alternatif
            const tambahanRows = tabelLog.find('tr.row-tugas-tambahan');
            const hasTambahan = tambahanRows.length > 0;

            // Izinkan hapus jika: ada >1 baris Pokok, ATAU baris terakhir kosong, ATAU sudah ada Tugas Tambahan
            const canDelete = pokokRows.length > 1 || isRowEmpty || hasTambahan;

            if (canDelete) {
                if (idLog) {
                    const doDeletePokok = function() {
                        let csrfTokenName = '<?= csrf_token() ?>';
                        let csrfHash = $('input[name="' + csrfTokenName + '"]').val() || '<?= csrf_hash() ?>';
                        let postData = { id: idLog };
                        postData[csrfTokenName] = csrfHash;

                        $.ajax({
                            url: '<?= site_url('log-kegiatan/hapus') ?>',
                            type: 'POST',
                            data: postData,
                            dataType: 'json',
                            success: function(response) {
                                if (response.csrf_hash) {
                                    $('input[name="' + csrfTokenName + '"]').val(response.csrf_hash);
                                    $('input[name="csrf_test_name"]').val(response.csrf_hash);
                                }
                                if (response.success) {
                                    row.remove();
                                    updateRowNumbers();
                                    updateDropdownOptions();
                                    Swal.fire({
                                        icon: 'success',
                                        title: 'Terhapus',
                                        text: 'Catatan kegiatan harian berhasil dihapus.',
                                        timer: 1500,
                                        showConfirmButton: false
                                    });
                                } else {
                                    Swal.fire('Gagal', response.message || 'Gagal menghapus data.', 'error');
                                }
                            },
                            error: function(xhr, status, error) {
                                console.error('Hapus Log Error:', xhr.responseText);
                                Swal.fire('Error', 'Terjadi kesalahan saat menghapus data. Silakan coba lagi.', 'error');
                            }
                        });
                    };

                    if (typeof Swal !== 'undefined') {
                        Swal.fire({
                            title: 'Hapus Kegiatan Harian?',
                            text: 'Apakah Anda yakin ingin menghapus catatan kegiatan ini?',
                            icon: 'warning',
                            showCancelButton: true,
                            confirmButtonColor: '#dc3545',
                            cancelButtonColor: '#6c757d',
                            confirmButtonText: '<i class="bi bi-trash3-fill me-1"></i> Ya, Hapus Kegiatan',
                            cancelButtonText: 'Batal'
                        }).then((result) => {
                            if (result.isConfirmed) {
                                doDeletePokok();
                            }
                        });
                    } else {
                        if (confirm('Apakah Anda yakin ingin menghapus catatan kegiatan ini?')) {
                            doDeletePokok();
                        }
                    }
                } else {
                    row.remove();
                    updateRowNumbers();
                    updateDropdownOptions();
                }
            } else {
                if (typeof Swal !== 'undefined') {
                    Swal.fire('Pemberitahuan', 'Minimal harus ada 1 kegiatan (Tugas Pokok atau Tugas Tambahan) yang dilaporkan.', 'info');
                } else {
                    alert('Minimal harus ada 1 kegiatan (Tugas Pokok atau Tugas Tambahan) yang dilaporkan.');
                }
            }
        });

        // Tambah Baris Tugas Tambahan
        $('#tambahBarisTambahan').on('click', function() {
            const templateRowTmb = tabelLog.find('tr.row-tugas-tambahan:first');
            let newRow;

            if (templateRowTmb.length > 0 && templateRowTmb.find('textarea[name="deskripsi_kegiatan_tambahan[]"]').length > 0) {
                newRow = templateRowTmb.clone();
                newRow.find('input[name="log_tambahan_id[]"]').val('');
                newRow.find('input[type="number"]').val('');
                newRow.find('input[name="satuan_tambahan[]"]').val('').attr('title', '');
                newRow.find('input[type="url"]').val('');
                newRow.find('textarea').val('');
                newRow.find('.hapus-baris-tmb').removeAttr('data-id');
                newRow.find('.is-invalid').removeClass('is-invalid');
            } else {
                newRow = `
                <tr class="row-tugas-tambahan">
                    <input type="hidden" name="log_tambahan_id[]" value="">
                    <td class="nomor-baris-tmb text-center fw-bold text-muted">1</td>
                    <td class="align-middle">
                        <div class="badge bg-success-subtle text-success border border-success-subtle px-2.5 py-1 rounded-pill">
                            <i class="bi bi-journal-plus me-1"></i> Tugas Tambahan
                        </div>
                    </td>
                    <td>
                        <textarea name="deskripsi_kegiatan_tambahan[]" class="form-control form-control-sm" rows="2" placeholder="Jelaskan tugas tambahan yang Anda kerjakan hari ini..."></textarea>
                    </td>
                    <td class="col-capaian-log">
                        <div class="input-group input-group-sm shadow-sm rounded-3 overflow-hidden">
                            <input type="number" step="0.01" name="jumlah_capaian_tambahan[]" class="form-control input-capaian-num input-target-val text-center num-tabular fw-bold text-success" placeholder="0">
                            <input type="text" name="satuan_tambahan[]" class="form-control input-satuan-val text-center" placeholder="Satuan" list="daftarSatuanStandar">
                        </div>
                    </td>
                    <td>
                        <input type="url" name="link_bukti_tambahan[]" class="form-control form-control-sm" placeholder="https://...">
                    </td>
                    <td class="text-center">
                        <button type="button" class="btn btn-outline-danger btn-sm rounded-circle hapus-baris-tmb" title="Hapus" style="width: 32px; height: 32px; display: inline-flex; align-items: center; justify-content: center;"><i class="bi bi-trash3"></i></button>
                    </td>
                </tr>`;
            }
            tabelLog.append(newRow);
            updateRowNumbers();
        });

        // Dynamic title update saat mengetik satuan tambahan
        $(document).on('input change', '.input-satuan-val', function() {
            $(this).attr('title', $(this).val());
        });

        // Hapus Baris Tugas Tambahan
        tabelLog.on('click', '.hapus-baris-tmb', function() {
            const tr = $(this).closest('tr');
            const logId = $(this).data('id');

            if (logId) {
                const doDeleteTambahan = function() {
                    const csrfToken = $('input[name="<?= csrf_token() ?>"]').val() || $('input[name="csrf_test_name"]').val();
                    $.ajax({
                        url: '<?= site_url("log-kegiatan/hapusTugasTambahan") ?>',
                        type: 'POST',
                        data: {
                            id: logId,
                            '<?= csrf_token() ?>': csrfToken
                        },
                        success: function(response) {
                            if (response.csrf_hash) {
                                $('input[name="<?= csrf_token() ?>"]').val(response.csrf_hash);
                                $('input[name="csrf_test_name"]').val(response.csrf_hash);
                            }
                            if(response.success) {
                                tr.remove();
                                updateRowNumbers();
                                if (typeof Swal !== 'undefined') {
                                    Swal.fire({
                                        icon: 'success',
                                        title: 'Terhapus!',
                                        text: 'Data tugas tambahan berhasil dihapus.',
                                        timer: 1500,
                                        showConfirmButton: false
                                    });
                                } else {
                                    alert('Data tugas tambahan berhasil dihapus.');
                                }
                            } else {
                                if (typeof Swal !== 'undefined') {
                                    Swal.fire('Gagal!', response.message || 'Gagal menghapus.', 'error');
                                } else {
                                    alert('Gagal: ' + (response.message || 'Gagal menghapus.'));
                                }
                            }
                        },
                        error: function() {
                            if (typeof Swal !== 'undefined') {
                                Swal.fire('Error', 'Terjadi kesalahan sistem.', 'error');
                            } else {
                                alert('Terjadi kesalahan sistem.');
                            }
                        }
                    });
                };

                if (typeof Swal !== 'undefined') {
                    Swal.fire({
                        title: 'Hapus Tugas Tambahan?',
                        text: "Data yang sudah disimpan akan dihapus permanen.",
                        icon: 'warning',
                        showCancelButton: true,
                        confirmButtonColor: '#dc3545',
                        cancelButtonColor: '#6c757d',
                        confirmButtonText: '<i class="bi bi-trash3-fill me-1"></i> Ya, Hapus Tugas',
                        cancelButtonText: 'Batal'
                    }).then((result) => {
                        if (result.isConfirmed) {
                            doDeleteTambahan();
                        }
                    });
                } else {
                    if (confirm('Hapus Tugas Tambahan? Data yang sudah disimpan akan dihapus permanen.')) {
                        doDeleteTambahan();
                    }
                }
            } else {
                tr.remove();
                updateRowNumbers();
            }
        });

        // Auto update satuan text based on selected target
        tabelLog.on('change', 'select[name="target_id[]"]', function() {
            let selectedOption = $(this).find('option:selected');
            let satuan = selectedOption.data('satuan') || selectedOption.attr('data-satuan');
            if (!satuan) {
                let selectedText = selectedOption.text();
                let matches = selectedText.match(/\((\d+(?:[\.,]\d+)?)\s+(.+)\)$/);
                satuan = matches ? matches[2] : '-';
            }
            $(this).closest('tr').find('.input-group-text').text(satuan || '-');
            updateDropdownOptions();
        });

        updateDropdownOptions();

        // Validasi Form saat klik "Simpan & Kirim Laporan Harian" (Form Submit Normal)
        $('#formLog').on('submit', function(e) {
            // Bersihkan semua hint highlight merah sebelumnya
            $('#formLog .is-invalid').removeClass('is-invalid');

            let isValid = true;
            let hasPokok = false;
            let hasTambahan = false;
            let errorHints = [];

            // Validasi Tugas Pokok
            $('#formLog tr.row-tugas-pokok').each(function(idx) {
                let rowNum = idx + 1;
                let targetElem = $(this).find('select[name="target_id[]"]');
                let deskripsiElem = $(this).find('textarea[name="deskripsi_kegiatan[]"]');
                let capaianElem = $(this).find('input[name="jumlah_capaian[]"]');
                let linkElem = $(this).find('input[name="link_bukti[]"]');

                let targetId = targetElem.val() ? targetElem.val().trim() : '';
                let deskripsi = deskripsiElem.val() ? deskripsiElem.val().trim() : '';
                let capaian = capaianElem.val() ? capaianElem.val().trim() : '';
                let link = linkElem.val() ? linkElem.val().trim() : '';

                // Jika salah satu kolom di baris ini terisi
                if (targetId || deskripsi || capaian !== '' || link) {
                    hasPokok = true;
                    let missingCols = [];

                    if (!targetId) {
                        targetElem.addClass('is-invalid');
                        missingCols.push('Target RHK');
                    }
                    if (!deskripsi) {
                        deskripsiElem.addClass('is-invalid');
                        missingCols.push('Deskripsi Kegiatan');
                    }
                    if (capaian === '') {
                        capaianElem.addClass('is-invalid');
                        missingCols.push('Jumlah Capaian');
                    }
                    if (!link || link === 'https://...') {
                        linkElem.addClass('is-invalid');
                        missingCols.push('Link Bukti Pekerjaan');
                    }

                    if (missingCols.length > 0) {
                        isValid = false;
                        errorHints.push(`<b>Tugas Pokok Baris ke-${rowNum}</b>: Kolom <i>${missingCols.join(', ')}</i> belum terisi.`);
                    }
                }
            });

            // Validasi Tugas Tambahan
            $('#formLog tr.row-tugas-tambahan').each(function(idx) {
                let rowNum = idx + 1;
                let deskripsiElem = $(this).find('textarea[name="deskripsi_kegiatan_tambahan[]"]');
                let capaianElem = $(this).find('input[name="jumlah_capaian_tambahan[]"]');
                let linkElem = $(this).find('input[name="link_bukti_tambahan[]"]');

                let deskripsiTmb = deskripsiElem.val() ? deskripsiElem.val().trim() : '';
                let capaianTmb = capaianElem.val() ? capaianElem.val().trim() : '';
                let linkTmb = linkElem.val() ? linkElem.val().trim() : '';

                if (deskripsiTmb || capaianTmb !== '' || linkTmb) {
                    hasTambahan = true;
                    let missingCols = [];

                    if (!deskripsiTmb) {
                        deskripsiElem.addClass('is-invalid');
                        missingCols.push('Deskripsi Kegiatan');
                    }
                    if (capaianTmb === '') {
                        capaianElem.addClass('is-invalid');
                        missingCols.push('Jumlah Capaian');
                    }
                    if (!linkTmb || linkTmb === 'https://...') {
                        linkElem.addClass('is-invalid');
                        missingCols.push('Link Bukti Pekerjaan');
                    }

                    if (missingCols.length > 0) {
                        isValid = false;
                        errorHints.push(`<b>Tugas Tambahan Baris ke-${rowNum}</b>: Kolom <i>${missingCols.join(', ')}</i> belum terisi.`);
                    }
                }
            });

            if (!hasPokok && !hasTambahan) {
                e.preventDefault();
                Swal.fire({
                    icon: 'warning',
                    title: 'Laporan Masih Kosong',
                    text: 'Silakan isi minimal satu kegiatan pada Tugas Pokok atau Tugas Tambahan sebelum mengirim ke atasan langsung.'
                });
                return false;
            }

            if (!isValid) {
                e.preventDefault();
                let hintHtml = `<div class="text-start small mt-2"><p class="mb-2 text-danger fw-bold">Beberapa kolom belum lengkap sebelum pengiriman:</p><ul class="ps-3 mb-0">` +
                               errorHints.map(h => `<li class="mb-1">${h}</li>`).join('') +
                               `</ul><p class="mt-2 text-muted mb-0" style="font-size:0.8rem;">💡 <i>Tips: Jika belum selesai diisi, Anda dapat menekan tombol <b>Simpan Sementara</b> terlebih dahulu.</i></p></div>`;
                
                Swal.fire({
                    icon: 'warning',
                    title: 'Data Belum Lengkap',
                    html: hintHtml,
                    confirmButtonText: 'Saya Mengerti'
                });
                return false;
            }

            // Hardening: Double submission prevention
            let submitBtn = $(this).find('button[type="submit"]');
            submitBtn.html('<span class="spinner-border spinner-border-sm me-2" role="status" aria-hidden="true"></span> Mengirim Laporan...').prop('disabled', true);
            $('#btnSimpanSementara').prop('disabled', true);
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
                        $('input[name="csrf_test_name"]').val(response.csrf_hash);
                    }

                    if (response.success) {
                        btn.html('<i class="bi bi-check-lg me-2"></i> Tersimpan Draf').removeClass('btn-outline-primary').addClass('btn-outline-success');
                        
                        if (response.new_ids) {
                            tabelLog.find('tr.row-tugas-pokok').each(function(idx) {
                                let inputHidden = $(this).find('input[name="log_id[]"]');
                                let btnHapus = $(this).find('.hapus-baris');
                                if (response.new_ids[idx]) {
                                    inputHidden.val(response.new_ids[idx]);
                                    btnHapus.attr('data-id', response.new_ids[idx]);
                                }
                            });
                        }

                        if (response.new_tambahan_ids) {
                            tabelLog.find('tr.row-tugas-tambahan').each(function(jdx) {
                                let inputHidden = $(this).find('input[name="log_tambahan_id[]"]');
                                let btnHapus = $(this).find('.hapus-baris-tmb');
                                if (response.new_tambahan_ids[jdx]) {
                                    inputHidden.val(response.new_tambahan_ids[jdx]);
                                    btnHapus.attr('data-id', response.new_tambahan_ids[jdx]);
                                }
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

        // =============================================
        // [SUPERADMIN] Buka Kunci Laporan Harian
        // =============================================
        $(document).on('click', '#btnBukaKunci', function(e) {
            e.preventDefault();
            const tanggal    = $(this).data('tanggal');
            const targetUser = $(this).data('user-id');
            const csrfName   = '<?= csrf_token() ?>';
            const csrfToken  = $('input[name="' + csrfName + '"]').first().val() || $('input[name="csrf_test_name"]').val();

            if (!tanggal || !targetUser) {
                alert('Parameter tanggal atau user ID tidak valid.');
                return;
            }

            function executeBukaKunci() {
                $.ajax({
                    url: '<?= site_url('log-kegiatan/buka-kunci') ?>',
                    type: 'POST',
                    data: {
                        target_user_id: targetUser,
                        tanggal: tanggal,
                        [csrfName]: csrfToken
                    },
                    dataType: 'json',
                    success: function(response) {
                        if (response.csrf_hash) {
                            $('input[name="' + csrfName + '"]').val(response.csrf_hash);
                            $('input[name="csrf_test_name"]').val(response.csrf_hash);
                        }
                        if (response.success) {
                            if (typeof Swal !== 'undefined') {
                                Swal.fire({
                                    icon: 'success',
                                    title: 'Berhasil!',
                                    text: response.message,
                                    timer: 2500,
                                    showConfirmButton: false
                                }).then(() => { location.reload(); });
                            } else {
                                alert(response.message);
                                location.reload();
                            }
                        } else {
                            if (typeof Swal !== 'undefined') {
                                Swal.fire('Gagal!', response.message || 'Terjadi kesalahan.', 'error');
                            } else {
                                alert('Gagal: ' + (response.message || 'Terjadi kesalahan.'));
                            }
                        }
                    },
                    error: function(xhr, status, error) {
                        console.error(xhr.responseText || error);
                        if (typeof Swal !== 'undefined') {
                            Swal.fire('Error', 'Terjadi kesalahan jaringan atau server.', 'error');
                        } else {
                            alert('Terjadi kesalahan jaringan atau server.');
                        }
                    }
                });
            }

            if (typeof Swal !== 'undefined') {
                Swal.fire({
                    title: 'Buka Kunci Laporan?',
                    html: `Laporan tanggal <strong>${tanggal}</strong> akan dibuka kuncinya.<br>Staf akan dapat merevisi dan mengirim ulang laporan.<br><br><span class='text-danger fw-bold'>Setelah revisi dikirim, laporan akan terkunci kembali otomatis.</span>`,
                    icon: 'warning',
                    showCancelButton: true,
                    confirmButtonColor: '#dc3545',
                    cancelButtonColor: '#6c757d',
                    confirmButtonText: '<i class="bi bi-unlock-fill me-1"></i> Ya, Buka Kunci Laporan',
                    cancelButtonText: 'Batal'
                }).then((result) => {
                    if (result.isConfirmed) {
                        executeBukaKunci();
                    }
                });
            } else {
                if (confirm(`Buka Kunci Laporan tanggal ${tanggal}?\n\nStaf akan dapat merevisi dan mengirim ulang laporan.`)) {
                    executeBukaKunci();
                }
            }
        });

    });
</script>
<?= $this->endSection() ?>
