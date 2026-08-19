<?= $this->extend('layouts/main') ?>

<?= $this->section('title') ?><?= esc($page_title ?? 'Kelola Tim Saya') ?><?= $this->endSection() ?>

<?= $this->section('styles') ?>
<link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/select2-bootstrap-5-theme@1.3.0/dist/select2-bootstrap-5-theme.min.css" />
<style>
.select2-container--bootstrap-5 .select2-selection {
    border-radius: 0.6rem;
    padding: 0.5rem 0.85rem;
    font-size: 0.88rem;
    border-color: #cbd5e1;
    min-height: 42px;
}
.select2-container--bootstrap-5 .select2-dropdown {
    border-radius: 0.75rem;
    box-shadow: 0 12px 30px rgba(0,0,0,0.15);
    border: 1px solid #e2e8f0;
    overflow: hidden;
}
.select2-container--bootstrap-5 .select2-search--dropdown {
    padding: 10px;
    background: #f8fafc;
    border-bottom: 1px solid #e2e8f0;
}
.select2-container--bootstrap-5 .select2-search__field {
    border-radius: 0.5rem;
    padding: 0.45rem 0.75rem;
    border: 1px solid #cbd5e1;
    font-size: 0.85rem;
}
.select2-container--bootstrap-5 .select2-results__options {
    padding: 6px;
    max-height: 290px;
}
.select2-container--bootstrap-5 .select2-results__option {
    border-radius: 8px;
    margin-bottom: 4px;
    padding: 6px 10px !important;
    transition: all 0.15s ease-in-out;
    background-color: transparent !important;
    color: #1e293b !important;
}
.select2-container--bootstrap-5 .select2-results__option--highlighted,
.select2-container--bootstrap-5 .select2-results__option:hover {
    background-color: #f1f5f9 !important;
    color: #0f172a !important;
}
.select2-container--bootstrap-5 .select2-results__option--highlighted .nama-pegawai {
    color: var(--primary-color, #0d6efd) !important;
}
.select2-container--bootstrap-5 .select2-results__option--selected {
    background-color: #e0f2fe !important;
}
.select2-pegawai-card {
    display: flex;
    align-items: center;
    gap: 12px;
    width: 100%;
}
</style>
<?= $this->endSection() ?>

<?= $this->section('content') ?>
<div class="container-fluid">
    <div class="d-flex justify-content-between flex-wrap flex-md-nowrap align-items-center pt-3 pb-2 mb-3 border-bottom gap-2">
        <div class="d-flex align-items-center gap-2">
            <h1 class="h3 mb-0 fw-bold text-dark"><i class="bi bi-people-fill text-primary me-2"></i>Kelola Tim Saya</h1>
            <span class="badge bg-primary-subtle text-primary border border-primary-subtle rounded-pill px-2.5 py-1 small">
                Total <?= count($staf) ?> Anggota
            </span>
        </div>
        <div class="btn-toolbar mb-2 mb-md-0">
            <button type="button" class="btn btn-sm btn-primary shadow-sm fw-semibold" data-bs-toggle="modal" data-bs-target="#addStafModal">
                <i class="bi bi-person-plus-fill me-1"></i> Tambah Anggota Tim
            </button>
        </div>
    </div>

    <?php if (session()->getFlashdata('success')): ?>
        <div class="alert alert-success alert-dismissible fade show" role="alert">
            <i class="bi bi-check-circle-fill me-2"></i><?= session()->getFlashdata('success') ?>
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    <?php endif; ?>
    
    <?php if (session()->getFlashdata('error')): ?>
        <div class="alert alert-danger alert-dismissible fade show" role="alert">
            <i class="bi bi-exclamation-triangle-fill me-2"></i><?= session()->getFlashdata('error') ?>
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    <?php endif; ?>

    <div class="card shadow-sm border-0 mb-4 rounded-4 overflow-hidden">
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-hover align-middle mb-0" width="100%" cellspacing="0">
                    <thead class="table-light text-muted" style="font-size: 0.85rem; text-transform: uppercase; letter-spacing: 0.5px;">
                        <tr>
                            <th style="width: 50px;" class="py-3 px-3 text-center border-0">No</th> 
                            <th class="py-3 fw-bold text-dark border-0" style="min-width: 220px;">Pegawai</th>
                            <th class="py-3 fw-bold text-dark border-0" style="min-width: 180px;">Jabatan</th>
                            <th class="py-3 fw-bold text-dark border-0" style="min-width: 220px; max-width: 260px;">Unit Kerja</th>
                            <th style="width: 140px; min-width: 140px;" class="text-center py-3 fw-bold text-dark px-3 border-0">Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if (empty($staf)): ?>
                        <tr>
                            <td colspan="5" class="text-center py-5 text-muted">
                                <i class="bi bi-people display-4 d-block mb-2 opacity-50"></i>
                                <span class="fw-medium">Belum ada anggota tim yang terdaftar.</span><br>
                                <small class="text-muted">Klik tombol 'Tambah Anggota Tim' di kanan atas untuk menambahkan staf.</small>
                            </td>
                        </tr>
                        <?php else: ?>
                            <?php $i = 1; foreach ($staf as $b): ?>
                            <tr>
                                <td class="px-3 text-center fw-bold text-muted" style="font-size: 0.8rem;"><?= $i++ ?></td>
                                <td style="min-width: 220px;">
                                    <div class="d-flex align-items-center">
                                        <?= render_user_avatar($b, $b['nama_lengkap'], 38) ?>
                                        <div class="d-flex flex-column gap-0.5" style="min-width: 0;">
                                            <span class="fw-bold text-dark text-break lh-sm mb-1" style="font-size: 0.88rem;"><?= esc($b['nama_lengkap']) ?></span>
                                            <span class="text-muted small text-nowrap d-inline-flex align-items-center" style="font-size: 0.75rem;">
                                                <i class="bi bi-person-badge me-1 opacity-75"></i><?= esc($b['nip'] ?? '-') ?>
                                            </span>
                                        </div>
                                    </div>
                                </td>
                                <td style="min-width: 180px;">
                                    <span class="badge bg-secondary-subtle text-secondary border border-secondary-subtle rounded-pill px-2.5 py-1 text-wrap text-start lh-sm" style="font-size: 0.75rem;">
                                        <?= esc($b['jabatan'] ?? 'Staf') ?>
                                    </span>
                                </td>
                                <td style="min-width: 220px; max-width: 260px;">
                                    <select class="form-select form-select-sm unit-kerja-select" data-user-id="<?= $b['id'] ?>" style="font-size: 0.82rem;">
                                        <option value="">-- Pilih Unit --</option>
                                        <?php foreach ($unit_kerja_list as $unit): ?>
                                            <option value="<?= esc($unit['nama_unit']) ?>" <?= ($b['unit'] == $unit['nama_unit']) ? 'selected' : '' ?>>
                                                <?= esc($unit['nama_unit']) ?>
                                            </option>
                                        <?php endforeach; ?>
                                    </select>
                                    <div class="update-status small mt-1"></div>
                                </td>
                                <td class="text-center px-3" style="width: 140px; min-width: 140px;">
                                    <form action="<?= site_url('tim/remove') ?>" method="POST" class="d-inline form-remove-staf">
                                        <?= csrf_field() ?>
                                        <input type="hidden" name="staf_id" value="<?= $b['id'] ?>">
                                        <button type="button" class="btn btn-outline-danger btn-sm rounded-pill px-3 py-1 text-nowrap d-inline-flex align-items-center justify-content-center gap-1 btn-remove-staf" data-nama="<?= esc($b['nama_lengkap']) ?>" title="Keluarkan dari Tim">
                                            <i class="bi bi-person-x-fill"></i><span>Keluarkan</span>
                                        </button>
                                    </form>
                                </td>
                            </tr>
                            <?php endforeach; ?>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

<!-- Modal Tambah Staf -->
<div class="modal fade" id="addStafModal" tabindex="-1" aria-labelledby="addStafModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content rounded-4 border-0 shadow">
            <div class="modal-header border-bottom">
                <h5 class="modal-title fw-bold" id="addStafModalLabel"><i class="bi bi-person-plus text-primary me-2"></i>Tambah Anggota Tim</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form action="<?= site_url('tim/add') ?>" method="post">
                <?= csrf_field() ?>
                <div class="modal-body p-4">
                    <div class="mb-3">
                        <label for="staf_id" class="form-label fw-bold small text-uppercase text-muted">Cari Pegawai <span class="text-danger">*</span></label>
                        <select name="staf_id" id="staf_id" class="form-select select2-pegawai" required style="width: 100%;">
                            <option value="">-- Ketik Nama, NIP, Jabatan, atau Unit --</option>
                            <?php foreach ($semua_pegawai as $p): ?>
                                <?php if($p['atasan_id'] == session()->get('id')) continue; ?>
                                <option value="<?= $p['id'] ?>"
                                    data-nama="<?= esc($p['nama_lengkap']) ?>"
                                    data-nip="<?= esc($p['nip'] ?? '-') ?>"
                                    data-jabatan="<?= esc($p['jabatan'] ?? 'Staf') ?>"
                                    data-unit="<?= esc($p['unit'] ?? 'Tanpa Unit') ?>"
                                    data-has-atasan="<?= !empty($p['atasan_id']) ? '1' : '0' ?>">
                                    <?= esc($p['nama_lengkap']) ?> - <?= esc($p['jabatan'] ?? 'Staf') ?> [NIP: <?= esc($p['nip'] ?? '-') ?>] (<?= esc($p['unit'] ?? 'Tanpa Unit') ?>)
                                </option>
                            <?php endforeach; ?>
                        </select>
                        <div class="form-text small mt-2 text-muted">
                            <i class="bi bi-info-circle me-1 text-primary"></i> Anda dapat mencari pegawai dengan mengetik <strong>Nama</strong>, <strong>Nomor NIP</strong>, <strong>Jabatan</strong>, ataupun <strong>Unit Kerja</strong>.
                        </div>
                    </div>
                </div>
                <div class="modal-footer border-top bg-light rounded-bottom-4 px-4 py-3">
                    <button type="button" class="btn btn-sm btn-secondary" data-bs-dismiss="modal">Batal</button>
                    <button type="submit" class="btn btn-sm btn-primary fw-semibold"><i class="bi bi-plus-circle me-1"></i> Tambahkan ke Tim</button>
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
    // Template Hasil Pencarian Pegawai di Select2
    function formatPegawaiOption(state) {
        if (!state.id || !state.element) {
            return state.text;
        }
        const nama = state.element.dataset.nama || state.text;
        const nip = state.element.dataset.nip || '-';
        const jabatan = state.element.dataset.jabatan || 'Staf';
        const unit = state.element.dataset.unit || 'Tanpa Unit';
        const hasAtasan = state.element.dataset.hasAtasan === '1';

        // Hitung inisial
        const parts = nama.trim().split(' ').filter(Boolean);
        const initials = (parts.length >= 2 ? (parts[0][0] + parts[1][0]) : (parts[0] ? parts[0].substring(0, 2) : 'PG')).toUpperCase();

        return $(`
            <div class="select2-pegawai-card py-1">
                <div class="rounded-circle bg-primary-subtle text-primary fw-bold d-flex align-items-center justify-content-center flex-shrink-0" style="width: 36px; height: 36px; font-size: 0.8rem;">
                    ${initials}
                </div>
                <div class="d-flex flex-column flex-grow-1" style="min-width: 0;">
                    <div class="d-flex align-items-center justify-content-between gap-2">
                        <span class="fw-bold nama-pegawai text-truncate" style="font-size: 0.88rem; color: #1e293b;">${nama}</span>
                        <span class="badge bg-secondary-subtle text-secondary border border-secondary-subtle rounded-pill px-2 py-0.5 flex-shrink-0" style="font-size: 0.7rem;">${jabatan}</span>
                    </div>
                    <div class="d-flex align-items-center gap-2 mt-1" style="font-size: 0.75rem; color: #64748b;">
                        <span class="text-nowrap flex-shrink-0 font-monospace"><i class="bi bi-person-badge me-1 opacity-75"></i>${nip}</span>
                        <span class="opacity-50 flex-shrink-0">•</span>
                        <span class="text-truncate flex-grow-1"><i class="bi bi-building me-1 opacity-75"></i>${unit}</span>
                        ${hasAtasan ? '<span class="badge bg-warning-subtle text-warning-emphasis border border-warning-subtle rounded-pill px-2 py-0.5 flex-shrink-0 ms-auto" style="font-size: 0.65rem;">Sudah punya atasan</span>' : ''}
                    </div>
                </div>
            </div>
        `);
    }

    // Matcher Cerdas Multi-Field (Nama, NIP, Jabatan, Unit)
    function matchPegawai(params, data) {
        if ($.trim(params.term) === '') {
            return data;
        }
        if (typeof data.text === 'undefined') {
            return null;
        }
        const term = params.term.toLowerCase();
        const nama = (data.element?.dataset.nama || '').toLowerCase();
        const nip = (data.element?.dataset.nip || '').toLowerCase();
        const jabatan = (data.element?.dataset.jabatan || '').toLowerCase();
        const unit = (data.element?.dataset.unit || '').toLowerCase();
        const rawText = data.text.toLowerCase();

        if (nama.includes(term) || nip.includes(term) || jabatan.includes(term) || unit.includes(term) || rawText.includes(term)) {
            return data;
        }
        return null;
    }

    // Inisialisasi Select2
    $('.select2-pegawai').select2({
        theme: 'bootstrap-5',
        dropdownParent: $('#addStafModal'),
        placeholder: '-- Ketik Nama, NIP, Jabatan, atau Unit --',
        allowClear: true,
        templateResult: formatPegawaiOption,
        templateSelection: function(state) {
            if (!state.id || !state.element) return state.text;
            const nama = state.element.dataset.nama || state.text;
            const jabatan = state.element.dataset.jabatan || '';
            return `${nama} (${jabatan})`;
        },
        matcher: matchPegawai
    });

    // Auto-focus field pencarian saat modal dibuka
    $('#addStafModal').on('shown.bs.modal', function () {
        $('.select2-pegawai').select2('open');
    });

    // AJAX untuk ubah Unit Kerja
    const unitKerjaSelects = document.querySelectorAll('.unit-kerja-select');
    unitKerjaSelects.forEach(select => {
        select.addEventListener('change', function() {
            const userId = this.dataset.userId;
            const newUnit = this.value;
            const statusDiv = this.nextElementSibling;

            statusDiv.innerHTML = '<i class="bi bi-arrow-repeat"></i> Menyimpan...';
            statusDiv.className = 'update-status small mt-1 text-muted';

            const csrfTokenName = '<?= csrf_token() ?>';
            const csrfInput = document.querySelector('input[name="' + csrfTokenName + '"]');
            const csrfHash = csrfInput ? csrfInput.value : '';

            const formData = new FormData();
            formData.append('user_id', userId);
            formData.append('unit', newUnit);
            formData.append(csrfTokenName, csrfHash);

            fetch('<?= site_url('tim/update_unit') ?>', {
                method: 'POST',
                headers: {
                    'X-Requested-With': 'XMLHttpRequest'
                },
                body: formData
            })
            .then(response => response.json())
            .then(data => {
                if (data[csrfTokenName]) {
                    document.querySelectorAll('input[name="' + csrfTokenName + '"]').forEach(input => {
                        input.value = data[csrfTokenName];
                    });
                }

                if (data.success) {
                    statusDiv.innerHTML = '<i class="bi bi-check-circle-fill"></i> Tersimpan';
                    statusDiv.className = 'update-status small mt-1 text-success';
                } else {
                    statusDiv.innerHTML = '<i class="bi bi-x-circle-fill"></i> ' + (data.message || 'Gagal');
                    statusDiv.className = 'update-status small mt-1 text-danger';
                }
                setTimeout(() => { statusDiv.innerHTML = ''; }, 3000);
            })
            .catch(error => {
                console.error('Error:', error);
                statusDiv.innerHTML = '<i class="bi bi-x-circle-fill"></i> Error jaringan!';
                statusDiv.className = 'update-status small mt-1 text-danger';
                setTimeout(() => { statusDiv.innerHTML = ''; }, 3000);
            });
        });
    });

    // Konfirmasi Hapus Anggota Tim (SweetAlert2)
    const removeButtons = document.querySelectorAll('.btn-remove-staf');
    removeButtons.forEach(btn => {
        btn.addEventListener('click', function(e) {
            e.preventDefault();
            const form = this.closest('form');
            const namaPegawai = this.dataset.nama || 'pegawai ini';

            if (typeof Swal !== 'undefined') {
                Swal.fire({
                    title: 'Keluarkan dari Tim?',
                    text: `Apakah Anda yakin ingin mengeluarkan ${namaPegawai} dari tim Anda?`,
                    icon: 'warning',
                    showCancelButton: true,
                    confirmButtonColor: '#dc3545',
                    cancelButtonColor: '#6c757d',
                    confirmButtonText: '<i class="bi bi-person-x-fill me-1"></i> Ya, Keluarkan',
                    cancelButtonText: 'Batal',
                    reverseButtons: true
                }).then((result) => {
                    if (result.isConfirmed) {
                        form.submit();
                    }
                });
            } else if (confirm(`Keluarkan ${namaPegawai} dari tim Anda?`)) {
                form.submit();
            }
        });
    });
});
</script>
<?= $this->endSection() ?>
