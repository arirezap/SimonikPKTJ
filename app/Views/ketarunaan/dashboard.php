<?= $this->extend('layouts/ecc2_layout') ?>

<?= $this->section('title') ?>Ringkasan<?= $this->endSection() ?>

<?= $this->section('styles') ?>
<style>
    .status-tile {
        border-radius: 12px;
        padding: 16px;
        transition: transform 0.2s cubic-bezier(0.16, 1, 0.3, 1), box-shadow 0.2s cubic-bezier(0.16, 1, 0.3, 1);
        border: 1px solid transparent;
        position: relative;
        text-decoration: none;
        display: block;
    }
    .status-tile:hover {
        transform: translateY(-2px);
        box-shadow: 0 8px 16px rgba(15, 23, 42, 0.06);
    }
    .status-tile-aktif {
        background-color: #eff6ff;
        border-color: #bfdbfe;
    }
    .status-tile-skorsing {
        background-color: #fef3c7;
        border-color: #fde68a;
    }
    .status-tile-cuti {
        background-color: #ecfdf5;
        border-color: #a7f3d0;
    }
    .status-tile-mundur {
        background-color: #fff7ed;
        border-color: #fed7aa;
    }
    .status-tile-do {
        background-color: #fef2f2;
        border-color: #fecaca;
    }
    .status-tile-lulus {
        background-color: #f5f3ff;
        border-color: #ddd6fe;
    }

    .metric-card-ops {
        border-radius: 16px;
        background: #ffffff;
        border: 1px solid rgba(0, 0, 0, 0.05);
        box-shadow: 0 4px 12px rgba(15, 23, 42, 0.03);
        padding: 16px 24px;
        transition: transform 0.2s cubic-bezier(0.16, 1, 0.3, 1);
    }
    .metric-card-ops:hover {
        transform: translateY(-2px);
    }

    .icon-box-ecc {
        width: 48px;
        height: 48px;
        border-radius: 12px;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 1.25rem;
        flex-shrink: 0;
    }

    .bento-dark-action {
        background: #0f172a;
        color: #ffffff;
        border-radius: 16px;
        padding: 24px;
    }

    .btn-quick-ecc {
        background: #ffffff;
        border: 1px solid #e2e8f0;
        border-radius: 12px;
        padding: 12px 16px;
        text-align: left;
        transition: all 0.2s cubic-bezier(0.16, 1, 0.3, 1);
        text-decoration: none;
        color: #0f172a;
        display: flex;
        align-items: center;
        justify-content: space-between;
    }
    .btn-quick-ecc:hover {
        background: #f8fafc;
        border-color: #cbd5e1;
        color: #0284c7;
        transform: translateY(-1px);
    }
</style>
<?= $this->endSection() ?>

<?= $this->section('content') ?>
<div class="container-fluid px-0">

    <!-- 1. TOP HEADER & FILTER TOOLBAR -->
    <div class="d-flex flex-column flex-lg-row align-items-lg-center justify-content-between gap-3 mb-4">
        <div>
            <h1 class="h3 fw-bold text-dark mb-1">Dashboard ECC 2.0</h1>
            <p class="text-muted mb-0 small">Ringkasan status pendidikan, capaian, dan kualitas data</p>
        </div>

        <div class="d-flex flex-wrap align-items-center gap-2">
            <select id="filterProdi" class="form-select form-select-sm rounded-3 border-secondary-subtle" style="width: auto; min-width: 176px; height: 36px;" aria-label="Filter Program Studi">
                <option value="">Semua Program Studi</option>
                <?php foreach ($prodi_list as $p): ?>
                    <option value="<?= esc($p['id']) ?>" <?= ($prodi_id == $p['id']) ? 'selected' : '' ?>>
                        <?= esc($p['kode_prodi']) ?> - <?= esc($p['nama_prodi']) ?>
                    </option>
                <?php endforeach; ?>
            </select>

            <select id="filterAngkatan" class="form-select form-select-sm rounded-3 border-secondary-subtle" style="width: auto; min-width: 144px; height: 36px;" aria-label="Filter Angkatan">
                <option value="">Semua Angkatan</option>
                <?php foreach ($angkatan_list as $a): ?>
                    <option value="<?= esc($a['id']) ?>" <?= ($angkatan_id == $a['id']) ? 'selected' : '' ?>>
                        Angkatan <?= esc($a['tahun_masuk']) ?>
                    </option>
                <?php endforeach; ?>
            </select>

            <div class="input-group input-group-sm" style="width: auto; min-width: 224px;">
                <span class="input-group-text bg-white border-secondary-subtle text-primary"><i class="bi bi-calendar3"></i></span>
                <input type="text" id="filterTanggalAcuan" class="form-control form-control-sm border-secondary-subtle bg-white" value="<?= esc($tanggal_acuan) ?>" placeholder="Tanggal acuan" style="height: 36px;" aria-label="Tanggal acuan">
            </div>

            <button type="button" class="btn btn-primary btn-sm rounded-3 px-3 fw-semibold d-inline-flex align-items-center gap-2 btn-tactile shadow-sm" style="height: 36px;" onclick="unduhLaporanRingkasan()">
                <i class="bi bi-download"></i> <span>Unduh Laporan</span>
            </button>
        </div>
    </div>

    <!-- 2. STATUS PENDIDIKAN TARUNA (6 STATUS PILAR) -->
    <div class="bento-card p-3 p-md-4 mb-4">
        <div class="d-flex align-items-center justify-content-between flex-wrap gap-2 mb-3">
            <div>
                <h6 class="fw-bold text-dark mb-0">Status Pendidikan Taruna</h6>
                <span class="text-muted small">Posisi status pada tanggal acuan: <strong class="text-dark" id="lblTanggalAcuan"><?= esc(ketarunaan_format_tanggal($tanggal_acuan)) ?></strong></span>
            </div>
            <a href="<?= site_url('ketarunaan/taruna') ?>" class="btn btn-light btn-sm rounded-pill px-3 fw-semibold text-primary border btn-tactile" style="height: 32px;">
                Buka Database Taruna
            </a>
        </div>

        <div class="row g-3 mb-3">
            <!-- 1. Aktif -->
            <div class="col-6 col-md-4 col-xl-2">
                <a href="<?= site_url('ketarunaan/taruna?status=aktif') ?>" class="status-tile status-tile-aktif h-100">
                    <div class="d-flex align-items-center justify-content-between mb-2">
                        <span class="fw-bold text-primary small">Aktif</span>
                        <i class="bi bi-people-fill text-primary"></i>
                    </div>
                    <div class="h2 fw-bold text-dark mb-0 tabular-nums" id="statAktif"><?= number_format($status_counts['aktif']) ?></div>
                    <div class="text-muted small">taruna</div>
                </a>
            </div>

            <!-- 2. Skorsing -->
            <div class="col-6 col-md-4 col-xl-2">
                <a href="<?= site_url('ketarunaan/taruna?status=skorsing') ?>" class="status-tile status-tile-skorsing h-100">
                    <div class="d-flex align-items-center justify-content-between mb-2">
                        <span class="fw-bold text-warning-emphasis small">Skorsing</span>
                        <i class="bi bi-pause-circle-fill text-warning"></i>
                    </div>
                    <div class="h2 fw-bold text-dark mb-0 tabular-nums" id="statSkorsing"><?= number_format($status_counts['skorsing']) ?></div>
                    <div class="text-muted small">taruna</div>
                </a>
            </div>

            <!-- 3. Cuti Akademik -->
            <div class="col-6 col-md-4 col-xl-2">
                <a href="<?= site_url('ketarunaan/taruna?status=cuti') ?>" class="status-tile status-tile-cuti h-100">
                    <div class="d-flex align-items-center justify-content-between mb-2">
                        <span class="fw-bold text-success small">Cuti Akademik</span>
                        <i class="bi bi-file-text-fill text-success"></i>
                    </div>
                    <div class="h2 fw-bold text-dark mb-0 tabular-nums" id="statCuti"><?= number_format($status_counts['cuti']) ?></div>
                    <div class="text-muted small">taruna</div>
                </a>
            </div>

            <!-- 4. Mengundurkan Diri -->
            <div class="col-6 col-md-4 col-xl-2">
                <a href="<?= site_url('ketarunaan/taruna?status=mengundurkan_diri') ?>" class="status-tile status-tile-mundur h-100">
                    <div class="d-flex align-items-center justify-content-between mb-2">
                        <span class="fw-bold text-warning-emphasis small">Mengundurkan Diri</span>
                        <i class="bi bi-box-arrow-right text-warning-emphasis"></i>
                    </div>
                    <div class="h2 fw-bold text-dark mb-0 tabular-nums" id="statMundur"><?= number_format($status_counts['mengundurkan_diri']) ?></div>
                    <div class="text-muted small">taruna</div>
                </a>
            </div>

            <!-- 5. Drop Out -->
            <div class="col-6 col-md-4 col-xl-2">
                <a href="<?= site_url('ketarunaan/taruna?status=drop_out') ?>" class="status-tile status-tile-do h-100">
                    <div class="d-flex align-items-center justify-content-between mb-2">
                        <span class="fw-bold text-danger small">Drop Out</span>
                        <i class="bi bi-x-circle-fill text-danger"></i>
                    </div>
                    <div class="h2 fw-bold text-dark mb-0 tabular-nums" id="statDo"><?= number_format($status_counts['drop_out']) ?></div>
                    <div class="text-muted small">taruna</div>
                </a>
            </div>

            <!-- 6. Lulus -->
            <div class="col-6 col-md-4 col-xl-2">
                <a href="<?= site_url('ketarunaan/taruna?status=lulus') ?>" class="status-tile status-tile-lulus h-100">
                    <div class="d-flex align-items-center justify-content-between mb-2">
                        <span class="fw-bold small" style="color: #7c3aed;">Lulus</span>
                        <i class="bi bi-mortarboard-fill" style="color: #7c3aed;"></i>
                    </div>
                    <div class="h2 fw-bold text-dark mb-0 tabular-nums" id="statLulus"><?= number_format($status_counts['lulus']) ?></div>
                    <div class="text-muted small">taruna</div>
                </a>
            </div>
        </div>

        <!-- Baris Ringkasan Agregat -->
        <div class="pt-3 border-top d-flex flex-wrap align-items-center justify-content-center justify-content-md-start gap-3 gap-md-4 text-muted small">
            <div>Dalam pendidikan: <strong class="text-primary tabular-nums" id="statDalamPendidikan"><?= number_format($dalam_pendidikan) ?></strong></div>
            <span class="d-none d-md-inline">•</span>
            <div>Berhenti pendidikan: <strong class="text-danger tabular-nums" id="statBerhentiPendidikan"><?= number_format($berhenti_pendidikan) ?></strong></div>
            <span class="d-none d-md-inline">•</span>
            <div>Lulus: <strong class="tabular-nums" style="color: #7c3aed;" id="statLulusan"><?= number_format($lulusan) ?></strong></div>
            <span class="d-none d-md-inline">•</span>
            <div>Seluruh rekaman: <strong class="text-dark tabular-nums" id="statTotalRekaman"><?= number_format($total_rekaman) ?></strong></div>
        </div>
    </div>

    <!-- 3. BARIS 4 KARTU METRIK OPERASIONAL -->
    <div class="row g-3 mb-4">
        <div class="col-12 col-sm-6 col-xl-3">
            <div class="metric-card-ops h-100 d-flex align-items-center gap-3">
                <div class="icon-box-ecc bg-warning-subtle text-warning">
                    <i class="bi bi-trophy-fill"></i>
                </div>
                <div>
                    <span class="text-muted small fw-medium d-block">Prestasi Tervalidasi</span>
                    <div class="h3 fw-bold text-dark mb-0 tabular-nums">
                        <span id="statTotalPrestasi"><?= number_format($total_prestasi) ?></span> <span class="fs-6 fw-normal text-muted">prestasi</span>
                    </div>
                    <span class="text-muted small">
                        <strong class="text-dark tabular-nums" id="statTarunaBerprestasi"><?= number_format($taruna_berprestasi) ?></strong> taruna berprestasi
                    </span>
                </div>
            </div>
        </div>

        <div class="col-12 col-sm-6 col-xl-3">
            <div class="metric-card-ops h-100 d-flex align-items-center gap-3">
                <div class="icon-box-ecc bg-danger-subtle text-danger">
                    <i class="bi bi-shield-fill-exclamation"></i>
                </div>
                <div>
                    <span class="text-muted small fw-medium d-block">Pelanggaran Terbukti</span>
                    <div class="h3 fw-bold text-dark mb-0 tabular-nums">
                        <span id="statPelanggaranTerbukti"><?= number_format($pelanggaran_terbukti) ?></span> <span class="fs-6 fw-normal text-muted">kasus</span>
                    </div>
                    <span class="text-muted small">
                        <span id="statPelanggaranSelesai" class="tabular-nums"><?= $pelanggaran_selesai ?></span> selesai • <span id="statPelanggaranProses" class="tabular-nums"><?= $pelanggaran_proses ?></span> proses
                    </span>
                </div>
            </div>
        </div>

        <div class="col-12 col-sm-6 col-xl-3">
            <div class="metric-card-ops h-100 d-flex align-items-center gap-3">
                <div class="icon-box-ecc bg-success-subtle text-success">
                    <i class="bi bi-mortarboard-fill"></i>
                </div>
                <div>
                    <span class="text-muted small fw-medium d-block">Penerima Beasiswa</span>
                    <div class="h3 fw-bold text-dark mb-0 tabular-nums">
                        <span id="statPenerimaBeasiswa"><?= number_format($penerima_beasiswa) ?></span> <span class="fs-6 fw-normal text-muted">taruna</span>
                    </div>
                    <span class="text-muted small">
                        <strong class="text-dark tabular-nums" id="statProgramBeasiswa"><?= $program_beasiswa_aktif ?></strong> program aktif
                    </span>
                </div>
            </div>
        </div>

        <div class="col-12 col-sm-6 col-xl-3">
            <div class="metric-card-ops h-100 d-flex align-items-center gap-3">
                <div class="icon-box-ecc bg-primary-subtle text-primary">
                    <i class="bi bi-briefcase-fill"></i>
                </div>
                <div>
                    <span class="text-muted small fw-medium d-block">Lowongan Aktif</span>
                    <div class="h3 fw-bold text-dark mb-0 tabular-nums">
                        <span id="statLowonganAktif"><?= number_format($lowongan_aktif) ?></span> <span class="fs-6 fw-normal text-muted">lowongan</span>
                    </div>
                    <span class="text-danger small">
                        <strong class="tabular-nums" id="statLowonganSegera"><?= $lowongan_segera_berakhir ?></strong> segera berakhir
                    </span>
                </div>
            </div>
        </div>
    </div>

    <!-- 4. BENTO GRID TENGAH (3 KOLOM SEIMBANG) -->
    <div class="row g-4 mb-4">
        <!-- 4.1 Tabel Sebaran Status per Prodi -->
        <div class="col-12 col-xl-5">
            <div class="bento-card p-3 p-md-4 h-100 d-flex flex-column justify-content-between">
                <div>
                    <h6 class="fw-bold text-dark mb-1">Sebaran Status per Program Studi</h6>
                    <p class="text-muted mb-3 small">Jumlah taruna berdasarkan status pendidikan</p>

                    <div class="table-responsive">
                        <table class="table table-sm table-hover align-middle mb-0">
                            <thead class="table-light">
                                <tr class="text-muted small">
                                    <th>Prodi</th>
                                    <th class="text-center">Aktif</th>
                                    <th class="text-center">Skorsing</th>
                                    <th class="text-center">Cuti</th>
                                    <th class="text-center">Mundur</th>
                                    <th class="text-center">DO</th>
                                    <th class="text-center">Lulus</th>
                                </tr>
                            </thead>
                            <tbody id="tbodySebaranProdi">
                                <?php foreach ($sebaran_prodi as $sp): ?>
                                    <tr>
                                        <td class="fw-bold text-dark"><?= esc($sp['kode_prodi']) ?></td>
                                        <td class="text-center tabular-nums fw-medium text-primary"><?= number_format($sp['aktif']) ?></td>
                                        <td class="text-center tabular-nums"><?= number_format($sp['skorsing']) ?></td>
                                        <td class="text-center tabular-nums"><?= number_format($sp['cuti']) ?></td>
                                        <td class="text-center tabular-nums"><?= number_format($sp['mundur']) ?></td>
                                        <td class="text-center tabular-nums text-danger"><?= number_format($sp['do_count']) ?></td>
                                        <td class="text-center tabular-nums fw-medium" style="color: #7c3aed;"><?= number_format($sp['lulus']) ?></td>
                                    </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    </div>
                </div>

                <div class="pt-3 border-top mt-3 text-end">
                    <a href="<?= site_url('ketarunaan/taruna') ?>" class="text-decoration-none fw-semibold small text-primary d-inline-flex align-items-center gap-1">
                        <span>Lihat Database Taruna</span>
                        <i class="bi bi-arrow-right"></i>
                    </a>
                </div>
            </div>
        </div>

        <!-- 4.2 Widget Kualitas Data (Donut Chart) -->
        <div class="col-12 col-md-6 col-xl-4">
            <div class="bento-card p-3 p-md-4 h-100 d-flex flex-column justify-content-between">
                <div>
                    <h6 class="fw-bold text-dark mb-1">Kualitas Data</h6>
                    <p class="text-muted mb-3 small">Tingkat kelengkapan dan validasi data ketarunaan</p>

                    <div class="d-flex align-items-center justify-content-center gap-4 my-2">
                        <div style="width: 120px; height: 120px; position: relative;">
                            <canvas id="kualitasDataChart"></canvas>
                            <div class="position-absolute top-50 start-50 translate-middle text-center" style="pointer-events: none;">
                                <div class="fw-bold text-dark fs-5 tabular-nums" id="lblPersenLengkapCenter"><?= (int) $kualitas_data['persen_lengkap'] ?>%</div>
                                <div class="text-muted small">Lengkap</div>
                            </div>
                        </div>

                        <div class="flex-grow-1">
                            <div class="mb-3">
                                <div class="d-flex justify-content-between align-items-center mb-1 small">
                                    <span class="text-dark fw-medium">Kelengkapan</span>
                                    <span class="fw-bold text-primary tabular-nums" id="lblPersenLengkap"><?= (int) $kualitas_data['persen_lengkap'] ?>%</span>
                                </div>
                                <div class="progress rounded-pill" style="height: 8px;">
                                    <div class="progress-bar bg-primary" role="progressbar" style="width: <?= (int) $kualitas_data['persen_lengkap'] ?>%;"></div>
                                </div>
                            </div>

                            <div>
                                <div class="d-flex justify-content-between align-items-center mb-1 small">
                                    <span class="text-dark fw-medium">Tervalidasi</span>
                                    <span class="fw-bold text-success tabular-nums" id="lblPersenTervalidasi"><?= (int) $kualitas_data['persen_tervalidasi'] ?>%</span>
                                </div>
                                <div class="progress rounded-pill" style="height: 8px;">
                                    <div class="progress-bar bg-success" role="progressbar" style="width: <?= (int) $kualitas_data['persen_tervalidasi'] ?>%;"></div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="pt-3 border-top mt-3 text-muted small">
                    Seluruh modul MVP Ketarunaan & Alumni
                </div>
            </div>
        </div>

        <!-- 4.3 Widget Perlu Tindak Lanjut (Dark Navy Bento) -->
        <div class="col-12 col-md-6 col-xl-3">
            <div class="bento-dark-action h-100 d-flex flex-column justify-content-between">
                <div>
                    <h6 class="fw-bold text-white mb-1">Perlu Tindak Lanjut</h6>
                    <p class="text-white-50 mb-3 small">Data yang memerlukan perhatian</p>

                    <div class="d-flex flex-column gap-2 mb-3">
                        <a href="<?= site_url('ketarunaan/taruna') ?>" class="text-decoration-none text-white d-flex align-items-center justify-content-between p-2 rounded-3 bg-white bg-opacity-10 btn-tactile">
                            <span class="small">Menunggu validasi</span>
                            <div class="d-flex align-items-center gap-2">
                                <span class="badge rounded-pill bg-warning text-dark fw-bold tabular-nums px-3 py-1" id="statMenungguValidasi"><?= (int) $antrean_validasi['menunggu'] ?></span>
                                <i class="bi bi-chevron-right text-white-50 small"></i>
                            </div>
                        </a>

                        <a href="<?= site_url('ketarunaan/taruna') ?>" class="text-decoration-none text-white d-flex align-items-center justify-content-between p-2 rounded-3 bg-white bg-opacity-10 btn-tactile">
                            <span class="small">Perlu perbaikan</span>
                            <div class="d-flex align-items-center gap-2">
                                <span class="badge rounded-pill bg-danger fw-bold tabular-nums px-3 py-1" id="statPerluPerbaikan"><?= (int) $antrean_validasi['perlu_perbaikan'] ?></span>
                                <i class="bi bi-chevron-right text-white-50 small"></i>
                            </div>
                        </a>

                        <a href="<?= site_url('ketarunaan/taruna?status=cuti') ?>" class="text-decoration-none text-white d-flex align-items-center justify-content-between p-2 rounded-3 bg-white bg-opacity-10 btn-tactile">
                            <span class="small">Rencana kembali perlu ditinjau</span>
                            <div class="d-flex align-items-center gap-2">
                                <span class="badge rounded-pill bg-info text-dark fw-bold tabular-nums px-3 py-1" id="statRencanaKembali"><?= (int) $antrean_validasi['rencana_kembali'] ?></span>
                                <i class="bi bi-chevron-right text-white-50 small"></i>
                            </div>
                        </a>
                    </div>
                </div>

                <a href="<?= site_url('ketarunaan/taruna') ?>" class="btn btn-warning w-100 rounded-pill fw-bold btn-sm text-dark d-flex align-items-center justify-content-center gap-2 btn-tactile shadow-sm" style="height: 36px;">
                    <span>Buka Antrean Validasi</span>
                    <i class="bi bi-chevron-right"></i>
                </a>
            </div>
        </div>
    </div>

    <!-- 5. BENTO GRID BAWAH (2 KOLOM SEIMBANG) -->
    <div class="row g-4">
        <!-- 5.1 Aktivitas Terbaru -->
        <div class="col-12 col-xl-7">
            <div class="bento-card p-3 p-md-4 h-100">
                <h6 class="fw-bold text-dark mb-1">Aktivitas Terbaru</h6>
                <p class="text-muted mb-3 small">Daftar aktivitas terkini pada sistem ECC 2.0</p>

                <div class="table-responsive">
                    <table class="table table-sm table-hover align-middle mb-0">
                        <thead class="table-light">
                            <tr class="text-muted small">
                                <th>Modul</th>
                                <th>Aktivitas</th>
                                <th>Status</th>
                                <th class="text-end">Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($aktivitas_terbaru as $act): ?>
                                <tr>
                                    <td class="fw-bold text-dark small"><?= esc($act['modul']) ?></td>
                                    <td class="small"><?= esc($act['aktivitas']) ?></td>
                                    <td>
                                        <span class="badge rounded-pill px-3 py-1 <?= esc($act['badge']) ?>">
                                            <?= esc($act['status']) ?>
                                        </span>
                                    </td>
                                    <td class="text-end">
                                        <a href="<?= esc($act['url'] ?? site_url('ketarunaan/taruna')) ?>" class="btn btn-light btn-sm rounded-circle p-0 d-inline-flex align-items-center justify-content-center btn-tactile text-muted" style="width: 32px; height: 32px;" aria-label="Buka detail aktivitas">
                                            <i class="bi bi-chevron-right"></i>
                                        </a>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

        <!-- 5.2 Akses Cepat -->
        <div class="col-12 col-xl-5">
            <div class="bento-card p-3 p-md-4 h-100">
                <h6 class="fw-bold text-dark mb-1">Akses Cepat</h6>
                <p class="text-muted mb-3 small">Menu yang sering digunakan</p>

                <div class="row g-2">
                    <div class="col-6">
                        <a href="<?= site_url('ketarunaan/taruna?modal=tambah') ?>" class="btn-quick-ecc">
                            <div class="d-flex align-items-center gap-2">
                                <i class="bi bi-plus-circle-fill text-primary fs-5"></i>
                                <div>
                                    <div class="fw-bold small">Tambah Data</div>
                                    <div class="text-muted small">Input data ketarunaan</div>
                                </div>
                            </div>
                            <i class="bi bi-chevron-right text-muted small"></i>
                        </a>
                    </div>

                    <div class="col-6">
                        <a href="<?= site_url('ketarunaan/taruna/impor') ?>" class="btn-quick-ecc">
                            <div class="d-flex align-items-center gap-2">
                                <i class="bi bi-file-earmark-excel-fill text-success fs-5"></i>
                                <div>
                                    <div class="fw-bold small">Impor Excel</div>
                                    <div class="text-muted small">Unggah data massal</div>
                                </div>
                            </div>
                            <i class="bi bi-chevron-right text-muted small"></i>
                        </a>
                    </div>

                    <div class="col-6">
                        <a href="<?= site_url('ketarunaan/taruna/ubah-status/2401001') ?>" class="btn-quick-ecc">
                            <div class="d-flex align-items-center gap-2">
                                <i class="bi bi-person-gear text-info fs-5"></i>
                                <div>
                                    <div class="fw-bold small">Ubah Status</div>
                                    <div class="text-muted small">Perbarui status taruna</div>
                                </div>
                            </div>
                            <i class="bi bi-chevron-right text-muted small"></i>
                        </a>
                    </div>

                    <div class="col-6">
                        <a href="<?= site_url('ketarunaan/taruna/profil/2401001') ?>" class="btn-quick-ecc">
                            <div class="d-flex align-items-center gap-2">
                                <i class="bi bi-person-badge-fill text-warning fs-5"></i>
                                <div>
                                    <div class="fw-bold small">Profil Taruna</div>
                                    <div class="text-muted small">Identitas & riwayat</div>
                                </div>
                            </div>
                            <i class="bi bi-chevron-right text-muted small"></i>
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- 6. FOOTNOTE STRIP -->
    <div class="d-flex flex-column flex-md-row align-items-center justify-content-between text-muted mt-4 pt-3 border-top small">
        <div>
            Status pendidikan terpisah dari status validasi • Data simulasi
        </div>
        <div class="mt-1 mt-md-0">
            ECC 2.0 - Evidence Command Center | Politeknik Keselamatan Transportasi Jalan
        </div>
    </div>

</div>
<?= $this->endSection() ?>

<?= $this->section('scripts') ?>
<script>
    let chartKualitasData = null;

    document.addEventListener('DOMContentLoaded', function () {
        flatpickr("#filterTanggalAcuan", {
            dateFormat: "Y-m-d",
            altInput: true,
            altFormat: "d F Y",
            defaultDate: "<?= esc($tanggal_acuan) ?>",
            onChange: function() {
                applyFilter();
            }
        });

        initKualitasDataChart(<?= (int) $kualitas_data['persen_lengkap'] ?>);

        document.getElementById('filterProdi').addEventListener('change', applyFilter);
        document.getElementById('filterAngkatan').addEventListener('change', applyFilter);
    });

    function initKualitasDataChart(persenLengkap) {
        const ctx = document.getElementById('kualitasDataChart');
        if (!ctx) return;

        if (chartKualitasData) {
            chartKualitasData.destroy();
        }

        chartKualitasData = new Chart(ctx, {
            type: 'doughnut',
            data: {
                datasets: [{
                    data: [persenLengkap, 100 - persenLengkap],
                    backgroundColor: ['#0284c7', '#e2e8f0'],
                    borderWidth: 0,
                    hoverOffset: 2
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                cutout: '76%',
                plugins: {
                    legend: { display: false },
                    tooltip: { enabled: false }
                }
            }
        });
    }

    function applyFilter() {
        const prodiId = document.getElementById('filterProdi').value;
        const angkatanId = document.getElementById('filterAngkatan').value;
        const tanggalAcuan = document.getElementById('filterTanggalAcuan').value;
        const csrfMeta = document.querySelector('meta[name="X-CSRF-TOKEN"]');
        const csrfToken = csrfMeta ? csrfMeta.getAttribute('content') : '';

        const formData = new FormData();
        formData.append('prodi_id', prodiId);
        formData.append('angkatan_id', angkatanId);
        formData.append('tanggal_acuan', tanggalAcuan);
        formData.append('<?= csrf_token() ?>', csrfToken);

        fetch('<?= site_url('ketarunaan/dashboard/filter-data') ?>', {
            method: 'POST',
            body: formData,
            headers: {
                'X-Requested-With': 'XMLHttpRequest'
            }
        })
        .then(response => response.json())
        .then(res => {
            if (res.csrf_hash && csrfMeta) {
                csrfMeta.setAttribute('content', res.csrf_hash);
            }
            if (res.status !== 'success' || !res.data) {
                return;
            }

            const stats = res.data;
            document.getElementById('statAktif').textContent = Number(stats.status_counts.aktif).toLocaleString();
            document.getElementById('statSkorsing').textContent = Number(stats.status_counts.skorsing).toLocaleString();
            document.getElementById('statCuti').textContent = Number(stats.status_counts.cuti).toLocaleString();
            document.getElementById('statMundur').textContent = Number(stats.status_counts.mengundurkan_diri).toLocaleString();
            document.getElementById('statDo').textContent = Number(stats.status_counts.drop_out).toLocaleString();
            document.getElementById('statLulus').textContent = Number(stats.status_counts.lulus).toLocaleString();

            document.getElementById('statDalamPendidikan').textContent = Number(stats.dalam_pendidikan).toLocaleString();
            document.getElementById('statBerhentiPendidikan').textContent = Number(stats.berhenti_pendidikan).toLocaleString();
            document.getElementById('statLulusan').textContent = Number(stats.lulusan).toLocaleString();
            document.getElementById('statTotalRekaman').textContent = Number(stats.total_rekaman).toLocaleString();

            document.getElementById('statTotalPrestasi').textContent = Number(stats.total_prestasi).toLocaleString();
            document.getElementById('statTarunaBerprestasi').textContent = Number(stats.taruna_berprestasi).toLocaleString();
            document.getElementById('statPelanggaranTerbukti').textContent = Number(stats.pelanggaran_terbukti).toLocaleString();
            document.getElementById('statPelanggaranSelesai').textContent = Number(stats.pelanggaran_selesai).toLocaleString();
            document.getElementById('statPelanggaranProses').textContent = Number(stats.pelanggaran_proses).toLocaleString();
            document.getElementById('statPenerimaBeasiswa').textContent = Number(stats.penerima_beasiswa).toLocaleString();
            document.getElementById('statLowonganAktif').textContent = Number(stats.lowongan_aktif).toLocaleString();
            document.getElementById('statLowonganSegera').textContent = Number(stats.lowongan_segera_berakhir).toLocaleString();

            if (Array.isArray(stats.sebaran_prodi)) {
                const tbody = document.getElementById('tbodySebaranProdi');
                tbody.innerHTML = stats.sebaran_prodi.map(sp => `
                    <tr>
                        <td class="fw-bold text-dark">${sp.kode_prodi}</td>
                        <td class="text-center tabular-nums fw-medium text-primary">${Number(sp.aktif).toLocaleString()}</td>
                        <td class="text-center tabular-nums">${Number(sp.skorsing).toLocaleString()}</td>
                        <td class="text-center tabular-nums">${Number(sp.cuti).toLocaleString()}</td>
                        <td class="text-center tabular-nums">${Number(sp.mundur).toLocaleString()}</td>
                        <td class="text-center tabular-nums text-danger">${Number(sp.do_count).toLocaleString()}</td>
                        <td class="text-center tabular-nums fw-medium" style="color: #7c3aed;">${Number(sp.lulus).toLocaleString()}</td>
                    </tr>
                `).join('');
            }

            const tglObj = new Date(tanggalAcuan);
            if (!isNaN(tglObj.getTime())) {
                const months = ['Januari', 'Februari', 'Maret', 'April', 'Mei', 'Juni', 'Juli', 'Agustus', 'September', 'Oktober', 'November', 'Desember'];
                document.getElementById('lblTanggalAcuan').textContent = `${String(tglObj.getDate()).padStart(2, '0')} ${months[tglObj.getMonth()]} ${tglObj.getFullYear()}`;
            }
        })
        .catch(() => {});
    }

    function unduhLaporanRingkasan() {
        if (typeof Swal !== 'undefined') {
            Swal.fire({
                title: 'Unduh Laporan?',
                text: 'Laporan ringkasan ketarunaan akan disiapkan.',
                icon: 'question',
                showCancelButton: true,
                confirmButtonColor: '#0284c7',
                cancelButtonColor: '#64748b',
                confirmButtonText: 'Unduh',
                cancelButtonText: 'Batal',
                reverseButtons: true,
                customClass: {
                    popup: 'rounded-4 shadow border-0'
                }
            }).then((result) => {
                if (result.isConfirmed) {
                    Swal.fire({
                        icon: 'success',
                        title: 'Tersimpan',
                        text: 'Laporan ringkasan berhasil diunduh.',
                        timer: 2000,
                        showConfirmButton: false
                    });
                }
            });
        } else if (confirm('Unduh laporan ringkasan ketarunaan?')) {
            alert('Laporan ringkasan berhasil diunduh.');
        }
    }
</script>
<?= $this->endSection() ?>
