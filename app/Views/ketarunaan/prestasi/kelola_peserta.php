<?= $this->extend('layouts/ecc2_layout') ?>

<?= $this->section('title') ?>Kelola Peserta Prestasi<?= $this->endSection() ?>

<?= $this->section('styles') ?>
<style>
    .ecc-pr-card {
        background: #ffffff;
        border: 1px solid #e2e8f0;
        border-radius: 16px;
        padding: 24px;
        box-shadow: 0 2px 8px rgba(15, 23, 42, 0.03);
    }
    @media (max-width: 767.98px) {
        .ecc-pr-card {
            padding: 16px;
        }
    }
    .ecc-icon-48 {
        width: 48px;
        height: 48px;
        border-radius: 12px;
        background-color: #eff6ff;
        color: #0284c7;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 1.25rem;
        flex-shrink: 0;
    }
    .ecc-wizard-nav {
        display: flex;
        flex-wrap: wrap;
        gap: 8px;
        border-bottom: 1px solid #e2e8f0;
        padding-bottom: 12px;
        margin-bottom: 24px;
    }
    .ecc-wizard-step {
        min-height: 36px;
        padding: 8px 16px;
        border-radius: 8px;
        font-size: 0.875rem;
        font-weight: 600;
        color: #475569;
        text-decoration: none;
        display: inline-flex;
        align-items: center;
        gap: 8px;
    }
    .ecc-wizard-step:hover {
        background-color: #f1f5f9;
        color: #0f172a;
    }
    .ecc-wizard-step.active {
        background-color: #0284c7;
        color: #ffffff;
    }
    .ecc-control-sm {
        min-height: 36px;
        border-radius: 8px;
        font-size: 0.875rem;
    }
    .ecc-table-pr thead th {
        background-color: #f8fafc;
        color: #475569;
        font-size: 0.75rem;
        font-weight: 700;
        text-transform: uppercase;
        letter-spacing: 0.04em;
        padding: 12px 16px;
        border-bottom: 1px solid #e2e8f0;
        white-space: nowrap;
    }
    .ecc-table-pr tbody td {
        padding: 12px 16px;
        vertical-align: middle;
        border-bottom: 1px solid #f1f5f9;
        font-size: 0.875rem;
        color: #1e293b;
    }
    .ecc-num {
        font-variant-numeric: tabular-nums;
        font-feature-settings: "tnum";
    }
</style>
<?= $this->endSection() ?>

<?= $this->section('content') ?>
<?php
$detail     = $detail ?? [];
$prestasi   = $detail['prestasi'] ?? [];
$peserta    = $detail['peserta'] ?? [];
$refs       = $refs ?? [];
$tarunaList = $tarunaList ?? [];

$idPrestasi  = (int) ($prestasi['id'] ?? 0);
$stValidasi  = ($prestasi['status_validasi'] === 'perlu_perbaikan') ? 'dikembalikan' : (string) ($prestasi['status_validasi'] ?? 'draf');
$tingkatLbl  = $refs['tingkat_catalog'][$prestasi['tingkat_prestasi']] ?? ucfirst((string) ($prestasi['tingkat_prestasi'] ?? '-'));
$existingNo  = array_column($peserta, 'nomor_taruna');
?>

<div class="container-fluid px-0">
    <!-- Header Halaman -->
    <div class="d-flex flex-column flex-md-row justify-content-between align-items-md-center gap-3 mb-4">
        <div>
            <nav aria-label="breadcrumb" class="mb-1">
                <ol class="breadcrumb mb-0" style="font-size: 0.75rem;">
                    <li class="breadcrumb-item"><a href="<?= base_url('ketarunaan/dashboard') ?>" class="text-decoration-none text-secondary">ECC 2.0</a></li>
                    <li class="breadcrumb-item"><a href="<?= base_url('ketarunaan/prestasi') ?>" class="text-decoration-none text-secondary">Prestasi</a></li>
                    <li class="breadcrumb-item active text-dark fw-semibold" aria-current="page">Kelola Peserta Prestasi</li>
                </ol>
            </nav>
            <h4 class="fw-bold text-dark mb-1">Kelola Peserta Prestasi</h4>
            <p class="text-secondary mb-0" style="font-size: 0.875rem;">Kelola daftar peserta untuk prestasi yang telah dipilih.</p>
        </div>
        <div>
            <a href="<?= base_url('ketarunaan/prestasi/detail/' . $idPrestasi) ?>" class="btn btn-light border ecc-control-sm px-3 fw-semibold d-inline-flex align-items-center gap-2">
                <i class="bi bi-chevron-left"></i> Kembali ke Detail Prestasi
            </a>
        </div>
    </div>

    <!-- Flash Messages -->
    <?php if (session()->getFlashdata('success')): ?>
        <div class="alert alert-success border-0 rounded-3 mb-4 py-2 px-3 d-flex align-items-center justify-content-between" role="alert">
            <span style="font-size: 0.875rem;"><?= esc(session()->getFlashdata('success')) ?></span>
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Tutup"></button>
        </div>
    <?php endif; ?>
    <?php if (session()->getFlashdata('error')): ?>
        <div class="alert alert-danger border-0 rounded-3 mb-4 py-2 px-3 d-flex align-items-center justify-content-between" role="alert">
            <span style="font-size: 0.875rem;"><?= esc(session()->getFlashdata('error')) ?></span>
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Tutup"></button>
        </div>
    <?php endif; ?>

    <!-- Kartu Ringkas Prestasi -->
    <div class="ecc-pr-card mb-4">
        <div class="d-flex flex-column flex-md-row justify-content-between align-items-md-center gap-3">
            <div class="d-flex align-items-center gap-3">
                <div class="ecc-icon-48">
                    <i class="bi bi-trophy-fill"></i>
                </div>
                <div>
                    <h5 class="fw-bold text-dark mb-1"><?= esc((string) $prestasi['nama_prestasi']) ?></h5>
                    <div class="text-secondary" style="font-size: 0.8125rem;">
                        <?= esc($tingkatLbl) ?> &bull; <?= esc((string) $prestasi['peringkat']) ?>
                        <?php if (!empty($prestasi['nama_tim'])): ?>
                            &bull; <?= esc((string) $prestasi['nama_tim']) ?>
                        <?php else: ?>
                            &bull; <?= esc(ucfirst((string) $prestasi['jenis_keikutsertaan'])) ?>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
            <div><?= ketarunaan_validasi_badge($stValidasi) ?></div>
        </div>
    </div>

    <!-- Kontainer Tahap 2 -->
    <div class="ecc-pr-card">
        <div class="ecc-wizard-nav">
            <a href="<?= base_url('ketarunaan/prestasi/edit/' . $idPrestasi) ?>" class="ecc-wizard-step">1. Informasi Prestasi</a>
            <span class="ecc-wizard-step active">2. Peserta</span>
            <a href="<?= base_url('ketarunaan/prestasi/dokumen/' . $idPrestasi) ?>" class="ecc-wizard-step">3. Dokumen Bukti</a>
        </div>

        <div class="d-flex flex-column flex-md-row justify-content-between align-items-md-center gap-3 mb-3">
            <div>
                <h6 class="fw-bold text-dark mb-1 ecc-num"><?= count($peserta) ?> peserta</h6>
                <p class="text-secondary mb-0" style="font-size: 0.8125rem;">Satu prestasi dicatat satu kali dan dihubungkan dengan seluruh peserta.</p>
            </div>
            <button type="button" class="btn btn-primary ecc-control-sm px-3 fw-semibold" data-bs-toggle="collapse" data-bs-target="#panelTambahPeserta" aria-expanded="true">
                <i class="bi bi-plus-lg"></i> Tambah Peserta
            </button>
        </div>

        <!-- Form Tambah Peserta -->
        <div class="collapse show mb-4" id="panelTambahPeserta">
            <form method="post" action="<?= base_url('ketarunaan/prestasi/peserta/' . $idPrestasi . '/tambah') ?>" class="border rounded-3 p-3 bg-light">
                <?= csrf_field() ?>
                <div class="row g-2 align-items-end">
                    <div class="col-12 col-md-3">
                        <label for="cariTarunaInput" class="form-label text-secondary fw-semibold mb-1" style="font-size: 0.75rem;">Saring Daftar Taruna</label>
                        <input type="text" id="cariTarunaInput" class="form-control ecc-control-sm" placeholder="Ketik Nomor Taruna atau nama...">
                    </div>
                    <div class="col-12 col-md-5">
                        <label for="selectNomorTaruna" class="form-label text-secondary fw-semibold mb-1" style="font-size: 0.75rem;">Pilih Taruna <span class="text-danger">*</span></label>
                        <select id="selectNomorTaruna" name="nomor_taruna" class="form-select ecc-control-sm" required>
                            <option value="">-- Pilih Nomor Taruna & Nama --</option>
                            <?php foreach ($tarunaList as $tr): ?>
                                <?php
                                $sudahAda = in_array($tr['nomor_taruna'], $existingNo, true);
                                $labelOpt = $tr['nomor_taruna'] . ' - ' . $tr['nama_lengkap'] . ' (' . ($tr['kode_prodi'] ?? '-') . ' / ' . ($tr['angkatan'] ?? '-') . ')';
                                ?>
                                <option value="<?= esc($tr['nomor_taruna']) ?>" data-search="<?= esc(strtolower($labelOpt)) ?>">
                                    <?= esc($labelOpt) ?><?= $sudahAda ? ' [Sudah Terdaftar]' : '' ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    <div class="col-6 col-md-2">
                        <label for="selectPeran" class="form-label text-secondary fw-semibold mb-1" style="font-size: 0.75rem;">Peran</label>
                        <select id="selectPeran" name="peran" class="form-select ecc-control-sm">
                            <?php foreach (($refs['peran_options'] ?? ['Ketua Tim', 'Anggota', 'Peserta Utama']) as $prn): ?>
                                <option value="<?= esc($prn) ?>" <?= ($prestasi['jenis_keikutsertaan'] === 'individu' && $prn === 'Peserta Utama') ? 'selected' : '' ?>>
                                    <?= esc($prn) ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    <div class="col-6 col-md-2">
                        <button type="submit" class="btn btn-primary ecc-control-sm w-100 fw-semibold">Tambahkan</button>
                    </div>
                </div>
            </form>
        </div>

        <!-- Tabel Peserta -->
        <div class="table-responsive mb-3">
            <table class="table ecc-table-pr mb-0">
                <thead>
                    <tr>
                        <th>Nomor Taruna</th>
                        <th>Nama</th>
                        <th>Prodi</th>
                        <th>Angkatan</th>
                        <th>Peran</th>
                        <th class="text-end">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (empty($peserta)): ?>
                        <tr>
                            <td colspan="6" class="text-center py-4 text-secondary">Belum ada peserta terdaftar pada prestasi ini.</td>
                        </tr>
                    <?php else: ?>
                        <?php foreach ($peserta as $ps): ?>
                            <tr>
                                <td class="fw-semibold ecc-num"><?= esc($ps['nomor_taruna']) ?></td>
                                <td class="fw-semibold text-dark"><?= esc($ps['nama_lengkap']) ?></td>
                                <td><?= esc($ps['kode_prodi'] ?? '-') ?></td>
                                <td class="ecc-num"><?= esc((string) ($ps['angkatan'] ?? '-')) ?></td>
                                <td>
                                    <?php $isKetua = ($ps['peran'] === 'Ketua Tim'); ?>
                                    <span class="badge rounded-pill px-3 py-1 fw-semibold <?= $isKetua ? 'bg-primary-subtle text-primary border border-primary-subtle' : 'bg-light text-dark border' ?>" style="font-size: 0.75rem;">
                                        <?= esc($ps['peran']) ?>
                                    </span>
                                </td>
                                <td class="text-end">
                                    <form method="post" action="<?= base_url('ketarunaan/prestasi/peserta/' . $idPrestasi . '/hapus/' . (int) $ps['peserta_id']) ?>" class="d-inline form-hapus-peserta">
                                        <?= csrf_field() ?>
                                        <button type="submit" class="btn btn-outline-danger btn-sm rounded-pill px-3" title="Hapus Peserta" data-nama="<?= esc($ps['nama_lengkap']) ?>">
                                            <i class="bi bi-trash"></i> Hapus
                                        </button>
                                    </form>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>

        <div class="px-3 py-2 rounded-3 mb-4 d-flex align-items-center gap-2" style="background-color: #fef2f2; border: 1px solid #fecaca; color: #b91c1c; font-size: 0.8125rem;">
            <i class="bi bi-exclamation-circle-fill"></i>
            <span>Nomor Taruna yang sama tidak dapat ditambahkan dua kali.</span>
        </div>

        <div class="d-flex flex-wrap justify-content-between align-items-center gap-2 pt-3 border-top">
            <a href="<?= base_url('ketarunaan/prestasi/edit/' . $idPrestasi) ?>" class="btn btn-light border ecc-control-sm px-3 fw-semibold d-inline-flex align-items-center gap-2">
                <i class="bi bi-arrow-left"></i> Sebelumnya: Informasi Prestasi
            </a>
            <div class="d-flex flex-wrap gap-2">
                <a href="<?= base_url('ketarunaan/prestasi/detail/' . $idPrestasi) ?>" class="btn btn-outline-primary ecc-control-sm px-3 fw-semibold">Simpan & Lihat Detail</a>
                <a href="<?= base_url('ketarunaan/prestasi/dokumen/' . $idPrestasi) ?>" class="btn btn-primary ecc-control-sm px-3 fw-semibold d-inline-flex align-items-center gap-2">
                    Berikutnya: Dokumen Bukti <i class="bi bi-arrow-right"></i>
                </a>
            </div>
        </div>
    </div>
</div>
<?= $this->endSection() ?>

<?= $this->section('scripts') ?>
<script>
document.addEventListener('DOMContentLoaded', function() {
    const searchInput = document.getElementById('cariTarunaInput');
    const selectEl    = document.getElementById('selectNomorTaruna');

    if (searchInput && selectEl) {
        const allOptions = Array.from(selectEl.options).slice(1);
        searchInput.addEventListener('input', function() {
            const keyword = this.value.trim().toLowerCase();
            while (selectEl.options.length > 1) {
                selectEl.remove(1);
            }
            allOptions.forEach(function(opt) {
                const hay = opt.getAttribute('data-search') || opt.text.toLowerCase();
                if (keyword === '' || hay.indexOf(keyword) !== -1) {
                    selectEl.add(opt.cloneNode(true));
                }
            });
        });
    }

    document.querySelectorAll('.form-hapus-peserta').forEach(function(form) {
        form.addEventListener('submit', function(e) {
            e.preventDefault();
            const btn  = form.querySelector('button[type="submit"]');
            const nama = btn ? (btn.getAttribute('data-nama') || 'peserta ini') : 'peserta ini';

            if (typeof Swal !== 'undefined') {
                Swal.fire({
                    title: 'Hapus Peserta?',
                    text: 'Peserta ' + nama + ' akan dihapus dari prestasi ini.',
                    icon: 'warning',
                    showCancelButton: true,
                    confirmButtonColor: '#dc2626',
                    cancelButtonColor: '#64748b',
                    confirmButtonText: 'Ya, Hapus',
                    cancelButtonText: 'Batal',
                    reverseButtons: true
                }).then(function(res) {
                    if (res.isConfirmed) {
                        form.submit();
                    }
                });
            } else if (confirm('Peserta ' + nama + ' akan dihapus dari prestasi ini.')) {
                form.submit();
            }
        });
    });
});
</script>
<?= $this->endSection() ?>
