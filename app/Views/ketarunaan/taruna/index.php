<?= $this->extend('layouts/ecc2_layout') ?>

<?= $this->section('title') ?>Database Taruna<?= $this->endSection() ?>

<?= $this->section('styles') ?>
<style>
    .status-filter-tile {
        border-radius: 12px;
        padding: 12px 16px;
        border: 1px solid transparent;
        text-decoration: none;
        display: block;
        transition: transform 0.2s cubic-bezier(0.16, 1, 0.3, 1), box-shadow 0.2s cubic-bezier(0.16, 1, 0.3, 1);
    }
    .status-filter-tile:hover {
        transform: translateY(-2px);
        box-shadow: 0 8px 16px rgba(15, 23, 42, 0.06);
    }
    .status-filter-tile.active-tile {
        box-shadow: 0 0 0 2px #0284c7;
    }
    .avatar-taruna-sm {
        width: 40px;
        height: 40px;
        border-radius: 12px;
        background-color: #eff6ff;
        color: #0284c7;
        display: inline-flex;
        align-items: center;
        justify-content: center;
        font-weight: 700;
        flex-shrink: 0;
    }
</style>
<?= $this->endSection() ?>

<?= $this->section('content') ?>
<div class="container-fluid px-0">

    <!-- Flash Feedback -->
    <?php if (session()->getFlashdata('success')): ?>
        <div class="alert alert-success border-0 rounded-3 shadow-sm py-2 px-3 mb-3 d-flex align-items-center justify-content-between small">
            <div><i class="bi bi-check-circle-fill me-2"></i><?= esc(session()->getFlashdata('success')) ?></div>
            <button type="button" class="btn-close btn-sm" data-bs-dismiss="alert" aria-label="Tutup"></button>
        </div>
    <?php endif; ?>
    <?php if (session()->getFlashdata('error')): ?>
        <div class="alert alert-danger border-0 rounded-3 shadow-sm py-2 px-3 mb-3 d-flex align-items-center justify-content-between small">
            <div><i class="bi bi-exclamation-circle-fill me-2"></i><?= esc(session()->getFlashdata('error')) ?></div>
            <button type="button" class="btn-close btn-sm" data-bs-dismiss="alert" aria-label="Tutup"></button>
        </div>
    <?php endif; ?>

    <!-- 1. HEADER & PRIMARY ACTIONS -->
    <div class="d-flex flex-column flex-md-row align-items-md-center justify-content-between gap-3 mb-4">
        <div>
            <div class="text-muted small mb-1">
                <a href="<?= site_url('ketarunaan/dashboard') ?>" class="text-decoration-none text-muted">ECC 2.0</a>
                <span class="mx-1">›</span>
                <span class="text-dark fw-medium">Database Taruna</span>
            </div>
            <h1 class="h3 fw-bold text-dark mb-1">Database Taruna</h1>
            <p class="text-muted mb-0 small">Master data identitas, pendidikan, dan riwayat status taruna PKTJ</p>
        </div>

        <?php if ($can_manage): ?>
            <div class="d-flex flex-wrap align-items-center gap-2">
                <a href="<?= site_url('ketarunaan/taruna/impor') ?>" class="btn btn-outline-primary btn-sm rounded-3 px-3 fw-semibold d-inline-flex align-items-center gap-2 btn-tactile" style="height: 40px;">
                    <i class="bi bi-file-earmark-spreadsheet"></i>
                    <span>Impor Data</span>
                </a>
                <button type="button" class="btn btn-primary btn-sm rounded-3 px-3 fw-semibold d-inline-flex align-items-center gap-2 btn-tactile shadow-sm" style="height: 40px;" data-bs-toggle="modal" data-bs-target="#modalTambahTaruna">
                    <i class="bi bi-plus-lg"></i>
                    <span>Tambah Taruna</span>
                </button>
            </div>
        <?php endif; ?>
    </div>

    <!-- 2. RINGKASAN 6 STATUS PENDIDIKAN (FILTER CEPAT) -->
    <div class="row g-2 mb-4">
        <?php foreach ($status_catalog as $kode => $meta): ?>
            <?php
                $isActiveTile = (($filters['status'] ?? '') === $kode);
                $tileUrl = site_url('ketarunaan/taruna?' . http_build_query(array_filter([
                    'prodi_id'    => $filters['prodi_id'] ?? '',
                    'angkatan_id' => $filters['angkatan_id'] ?? '',
                    'status'      => $isActiveTile ? '' : $kode,
                    'q'           => $filters['q'] ?? '',
                ])));
            ?>
            <div class="col-6 col-md-4 col-xl-2">
                <a href="<?= esc($tileUrl) ?>" class="status-filter-tile h-100 <?= $isActiveTile ? 'active-tile' : '' ?>" style="background-color: <?= esc($meta['bg_soft']) ?>; border-color: <?= esc($meta['border_color']) ?>;">
                    <div class="d-flex align-items-center justify-content-between mb-1">
                        <span class="fw-bold small" style="color: <?= esc($meta['color']) ?>;"><?= esc($meta['label']) ?></span>
                        <span class="rounded-circle d-inline-block" style="width: 8px; height: 8px; background-color: <?= esc($meta['color']) ?>;"></span>
                    </div>
                    <div class="h4 fw-bold text-dark mb-0 tabular-nums"><?= number_format($status_counts[$kode] ?? 0) ?></div>
                    <span class="text-muted small">taruna</span>
                </a>
            </div>
        <?php endforeach; ?>
    </div>

    <!-- 3. FILTER & TABEL DIREKTORI TARUNA -->
    <div class="bento-card p-3 p-md-4">
        <form method="get" action="<?= site_url('ketarunaan/taruna') ?>" class="row g-2 align-items-center mb-3 pb-3 border-bottom">
            <div class="col-12 col-md-4">
                <div class="input-group input-group-sm">
                    <span class="input-group-text bg-white border-secondary-subtle text-muted"><i class="bi bi-search"></i></span>
                    <input type="text" name="q" class="form-control border-secondary-subtle" placeholder="Cari Nomor Taruna atau Nama..." value="<?= esc($filters['q'] ?? '') ?>" style="height: 36px;">
                </div>
            </div>

            <div class="col-6 col-md-2">
                <select name="prodi_id" class="form-select form-select-sm border-secondary-subtle" style="height: 36px;" onchange="this.form.submit()">
                    <option value="">Semua Prodi</option>
                    <?php foreach ($prodi_list as $p): ?>
                        <option value="<?= esc($p['id']) ?>" <?= (($filters['prodi_id'] ?? '') == $p['id']) ? 'selected' : '' ?>>
                            <?= esc($p['kode_prodi']) ?>
                        </option>
                    <?php endforeach; ?>
                </select>
            </div>

            <div class="col-6 col-md-2">
                <select name="angkatan_id" class="form-select form-select-sm border-secondary-subtle" style="height: 36px;" onchange="this.form.submit()">
                    <option value="">Semua Angkatan</option>
                    <?php foreach ($angkatan_list as $a): ?>
                        <option value="<?= esc($a['id']) ?>" <?= (($filters['angkatan_id'] ?? '') == $a['id']) ? 'selected' : '' ?>>
                            Angkatan <?= esc($a['tahun_masuk']) ?>
                        </option>
                    <?php endforeach; ?>
                </select>
            </div>

            <div class="col-6 col-md-2">
                <select name="status" class="form-select form-select-sm border-secondary-subtle" style="height: 36px;" onchange="this.form.submit()">
                    <option value="">Semua Status</option>
                    <?php foreach ($status_catalog as $kode => $meta): ?>
                        <option value="<?= esc($kode) ?>" <?= (($filters['status'] ?? '') === $kode) ? 'selected' : '' ?>>
                            <?= esc($meta['label']) ?>
                        </option>
                    <?php endforeach; ?>
                </select>
            </div>

            <div class="col-6 col-md-2 d-flex gap-2">
                <button type="submit" class="btn btn-primary btn-sm rounded-3 fw-semibold flex-grow-1 btn-tactile" style="height: 36px;">Terapkan</button>
                <?php if (!empty(array_filter($filters))): ?>
                    <a href="<?= site_url('ketarunaan/taruna') ?>" class="btn btn-light btn-sm rounded-3 border d-inline-flex align-items-center justify-content-center btn-tactile" style="height: 36px; width: 36px;" title="Reset Filter">
                        <i class="bi bi-arrow-counterclockwise"></i>
                    </a>
                <?php endif; ?>
            </div>
        </form>

        <div class="d-flex align-items-center justify-content-between mb-3 small text-muted">
            <div>Menampilkan <strong class="text-dark tabular-nums"><?= number_format(count($rows)) ?></strong> dari <strong class="text-dark tabular-nums"><?= number_format($total_filtered) ?></strong> rekaman taruna</div>
            <div>Klik baris atau tombol <strong>Profil</strong> untuk membuka 5 tab rincian taruna</div>
        </div>

        <!-- Tabel Desktop -->
        <div class="table-responsive d-none d-md-block">
            <table class="table table-hover align-middle mb-0">
                <thead class="table-light">
                    <tr class="text-muted small">
                        <th>Nomor Taruna</th>
                        <th>Nama Taruna</th>
                        <th>Program Studi</th>
                        <th class="text-center">Angkatan</th>
                        <th class="text-center">Tingkat / Sem</th>
                        <th>Jalur & Formasi</th>
                        <th class="text-center">Status Pendidikan</th>
                        <th class="text-end">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (empty($rows)): ?>
                        <tr>
                            <td colspan="8" class="text-center py-5 text-muted">
                                Data taruna tidak ditemukan untuk filter yang dipilih.
                            </td>
                        </tr>
                    <?php else: ?>
                        <?php foreach ($rows as $r): ?>
                            <?php
                                $initials = strtoupper(substr(trim((string) $r['nama_lengkap']), 0, 1));
                                $profilUrl = site_url('ketarunaan/taruna/profil/' . urlencode((string) $r['nomor_taruna']));
                            ?>
                            <tr>
                                <td class="fw-bold text-primary tabular-nums">
                                    <a href="<?= esc($profilUrl) ?>" class="text-decoration-none text-primary">
                                        <?= esc($r['nomor_taruna']) ?>
                                    </a>
                                </td>
                                <td>
                                    <div class="d-flex align-items-center gap-2">
                                        <div class="avatar-taruna-sm small"><?= esc($initials) ?></div>
                                        <div>
                                            <a href="<?= esc($profilUrl) ?>" class="fw-bold text-dark text-decoration-none d-block">
                                                <?= esc($r['nama_lengkap']) ?>
                                            </a>
                                            <span class="text-muted small"><?= $r['jenis_kelamin'] === 'P' ? 'Perempuan' : 'Laki-laki' ?> • <?= esc($r['tempat_lahir'] ?? 'Tegal') ?></span>
                                        </div>
                                    </div>
                                </td>
                                <td>
                                    <span class="fw-semibold text-dark d-block"><?= esc($r['kode_prodi'] ?? '-') ?></span>
                                    <span class="text-muted small"><?= esc($r['nama_prodi'] ?? '-') ?></span>
                                </td>
                                <td class="text-center tabular-nums fw-semibold"><?= esc($r['tahun_masuk'] ?? '-') ?></td>
                                <td class="text-center tabular-nums small">
                                    Tk. <?= (int) ($r['tingkat'] ?? 1) ?> / Sem <?= (int) ($r['semester_aktif'] ?? 1) ?>
                                    <span class="text-muted d-block">Kelas <?= esc($r['nama_kelas'] ?? 'A') ?></span>
                                </td>
                                <td class="small">
                                    <span class="text-dark d-block"><?= esc($r['jalur_penerimaan'] ?? '-') ?></span>
                                    <span class="text-muted"><?= esc($r['asal_formasi'] ?? '-') ?></span>
                                </td>
                                <td class="text-center">
                                    <?= ketarunaan_status_badge((string) $r['status_terkini']) ?>
                                </td>
                                <td class="text-end">
                                    <div class="d-inline-flex align-items-center gap-1">
                                        <a href="<?= esc($profilUrl) ?>" class="btn btn-light btn-sm rounded-pill px-3 border fw-semibold text-primary btn-tactile" style="height: 32px;">
                                            Profil
                                        </a>
                                        <a href="<?= site_url('ketarunaan/taruna/ubah-status/' . urlencode((string) $r['nomor_taruna'])) ?>" class="btn btn-light btn-sm rounded-pill px-3 border text-dark btn-tactile" style="height: 32px;">
                                            Ubah Status
                                        </a>
                                    </div>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>

        <!-- Kartu Sentuh Mobile (< 768px) -->
        <div class="d-md-none d-flex flex-column gap-2">
            <?php foreach ($rows as $r): ?>
                <?php $profilUrl = site_url('ketarunaan/taruna/profil/' . urlencode((string) $r['nomor_taruna'])); ?>
                <div class="p-3 rounded-3 border bg-white">
                    <div class="d-flex align-items-start justify-content-between gap-2 mb-2">
                        <div>
                            <div class="fw-bold text-dark"><?= esc($r['nama_lengkap']) ?></div>
                            <div class="text-primary fw-semibold small tabular-nums">Nomor Taruna <?= esc($r['nomor_taruna']) ?></div>
                        </div>
                        <?= ketarunaan_status_badge((string) $r['status_terkini']) ?>
                    </div>
                    <div class="text-muted small mb-3">
                        <?= esc($r['kode_prodi'] ?? '-') ?> • Angkatan <?= esc($r['tahun_masuk'] ?? '-') ?> • Semester <?= (int) ($r['semester_aktif'] ?? 1) ?>
                    </div>
                    <div class="d-flex gap-2">
                        <a href="<?= esc($profilUrl) ?>" class="btn btn-primary btn-sm rounded-pill flex-grow-1 fw-semibold" style="height: 36px;">Buka Profil</a>
                        <a href="<?= site_url('ketarunaan/taruna/ubah-status/' . urlencode((string) $r['nomor_taruna'])) ?>" class="btn btn-outline-secondary btn-sm rounded-pill px-3" style="height: 36px;">Ubah Status</a>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>

        <!-- Paginasi -->
        <?php if ($total_pages > 1): ?>
            <div class="d-flex flex-wrap align-items-center justify-content-between gap-2 pt-3 mt-3 border-top">
                <span class="text-muted small tabular-nums">Halaman <?= $page ?> dari <?= $total_pages ?></span>
                <nav aria-label="Navigasi halaman database taruna">
                    <ul class="pagination pagination-sm mb-0 gap-1">
                        <?php
                            $prevPage = max(1, $page - 1);
                            $nextPage = min($total_pages, $page + 1);
                            $baseQuery = array_filter($filters);
                        ?>
                        <li class="page-item <?= ($page <= 1) ? 'disabled' : '' ?>">
                            <a class="page-link rounded-2" href="<?= site_url('ketarunaan/taruna?' . http_build_query(array_merge($baseQuery, ['page' => $prevPage]))) ?>">Sebelumnya</a>
                        </li>
                        <?php for ($p = max(1, $page - 2); $p <= min($total_pages, $page + 2); $p++): ?>
                            <li class="page-item <?= ($p === $page) ? 'active' : '' ?>">
                                <a class="page-link rounded-2 tabular-nums" href="<?= site_url('ketarunaan/taruna?' . http_build_query(array_merge($baseQuery, ['page' => $p]))) ?>"><?= $p ?></a>
                            </li>
                        <?php endfor; ?>
                        <li class="page-item <?= ($page >= $total_pages) ? 'disabled' : '' ?>">
                            <a class="page-link rounded-2" href="<?= site_url('ketarunaan/taruna?' . http_build_query(array_merge($baseQuery, ['page' => $nextPage]))) ?>">Berikutnya</a>
                        </li>
                    </ul>
                </nav>
            </div>
        <?php endif; ?>
    </div>

</div>

<!-- MODAL TAMBAH TARUNA BARU -->
<?php if ($can_manage): ?>
<div class="modal fade" id="modalTambahTaruna" tabindex="-1" aria-labelledby="modalTambahTarunaLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg modal-dialog-centered">
        <div class="modal-content border-0 rounded-4 shadow">
            <form method="post" action="<?= site_url('ketarunaan/taruna/store') ?>">
                <?= csrf_field() ?>
                <div class="modal-header border-bottom px-4 py-3">
                    <div>
                        <h6 class="modal-title fw-bold text-dark mb-0" id="modalTambahTarunaLabel">Tambah Data Taruna Baru</h6>
                        <span class="text-muted small">Pastikan Nomor Taruna unik sesuai SK penetapan</span>
                    </div>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Tutup"></button>
                </div>
                <div class="modal-body p-4">
                    <div class="row g-3">
                        <div class="col-md-6">
                            <label class="form-label small fw-semibold">Nomor Taruna <span class="text-danger">*</span></label>
                            <input type="text" name="nomor_taruna" class="form-control" value="<?= esc(old('nomor_taruna')) ?>" placeholder="Contoh: 2601001" required style="height: 40px;">
                        </div>
                        <div class="col-md-6">
                            <label class="form-label small fw-semibold">NIK</label>
                            <input type="text" name="nik" class="form-control" value="<?= esc(old('nik')) ?>" maxlength="16" placeholder="16 digit NIK" style="height: 40px;">
                        </div>
                        <div class="col-md-8">
                            <label class="form-label small fw-semibold">Nama Lengkap <span class="text-danger">*</span></label>
                            <input type="text" name="nama_lengkap" class="form-control" value="<?= esc(old('nama_lengkap')) ?>" placeholder="Nama lengkap sesuai ijazah" required style="height: 40px;">
                        </div>
                        <div class="col-md-4">
                            <label class="form-label small fw-semibold">Nama Panggilan</label>
                            <input type="text" name="nama_panggilan" class="form-control" value="<?= esc(old('nama_panggilan')) ?>" placeholder="Nama panggilan" style="height: 40px;">
                        </div>
                        <div class="col-md-4">
                            <label class="form-label small fw-semibold">Jenis Kelamin <span class="text-danger">*</span></label>
                            <select name="jenis_kelamin" class="form-select" style="height: 40px;">
                                <option value="L" <?= old('jenis_kelamin') === 'L' ? 'selected' : '' ?>>Laki-laki</option>
                                <option value="P" <?= old('jenis_kelamin') === 'P' ? 'selected' : '' ?>>Perempuan</option>
                            </select>
                        </div>
                        <div class="col-md-4">
                            <label class="form-label small fw-semibold">Tempat Lahir</label>
                            <input type="text" name="tempat_lahir" class="form-control" value="<?= esc(old('tempat_lahir', 'Tegal')) ?>" style="height: 40px;">
                        </div>
                        <div class="col-md-4">
                            <label class="form-label small fw-semibold">Tanggal Lahir</label>
                            <input type="date" name="tanggal_lahir" class="form-control" value="<?= esc(old('tanggal_lahir', '2005-05-15')) ?>" style="height: 40px;">
                        </div>
                        <div class="col-md-6">
                            <label class="form-label small fw-semibold">Program Studi <span class="text-danger">*</span></label>
                            <select name="program_studi_id" class="form-select" required style="height: 40px;">
                                <?php foreach ($prodi_list as $p): ?>
                                    <option value="<?= esc($p['id']) ?>"><?= esc($p['kode_prodi']) ?> - <?= esc($p['nama_prodi']) ?></option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                        <div class="col-md-3">
                            <label class="form-label small fw-semibold">Angkatan <span class="text-danger">*</span></label>
                            <select name="angkatan_id" class="form-select" required style="height: 40px;">
                                <?php foreach ($angkatan_list as $a): ?>
                                    <option value="<?= esc($a['id']) ?>"><?= esc($a['tahun_masuk']) ?></option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                        <div class="col-md-3">
                            <label class="form-label small fw-semibold">Status Awal</label>
                            <select name="status_terkini" class="form-select" style="height: 40px;">
                                <option value="aktif">Aktif</option>
                                <option value="cuti">Cuti Akademik</option>
                            </select>
                        </div>
                    </div>
                </div>
                <div class="modal-footer border-top px-4 py-3">
                    <button type="button" class="btn btn-light rounded-pill px-4 border" data-bs-dismiss="modal" style="height: 36px;">Batal</button>
                    <button type="submit" class="btn btn-primary rounded-pill px-4 fw-semibold" style="height: 36px;">Simpan Taruna</button>
                </div>
            </form>
        </div>
    </div>
</div>
<?php endif; ?>
<?= $this->endSection() ?>

<?= $this->section('scripts') ?>
<script>
    document.addEventListener('DOMContentLoaded', function () {
        const params = new URLSearchParams(window.location.search);
        if (params.get('modal') === 'tambah') {
            const modalEl = document.getElementById('modalTambahTaruna');
            if (modalEl && typeof bootstrap !== 'undefined') {
                new bootstrap.Modal(modalEl).show();
            }
        }
    });
</script>
<?= $this->endSection() ?>

