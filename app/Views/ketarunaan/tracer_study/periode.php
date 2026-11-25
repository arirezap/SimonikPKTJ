<?= $this->extend('layouts/ecc2_layout') ?>

<?= $this->section('title') ?>Periode & Kontrol Tracer Study<?= $this->endSection() ?>

<?= $this->section('styles') ?>
<?= $this->include('ketarunaan/tracer_study/_styles') ?>
<?= $this->endSection() ?>

<?= $this->section('content') ?>
<?php
$periodes  = $periodes ?? [];
$refs      = $refs ?? [];
$perms     = $perms ?? [];
$canManage = !empty($perms['can_manage']);
$canToggle = !empty($perms['can_toggle']);
?>
<div class="container-fluid px-0">
    <?= $this->include('ketarunaan/tracer_study/_nav_tabs') ?>

    <div class="ecc-ts-card">
        <div class="d-flex flex-wrap justify-content-between align-items-center gap-2 mb-3">
            <div>
                <h2 class="h6 fw-bold text-dark mb-0">Daftar Periode Pembukaan Survei</h2>
                <div class="ecc-text-muted-aa small">Susun atau modifikasi pertanyaan saat berstatus Draf, lalu buka survei saat siap diisi alumni.</div>
            </div>
            <span class="ecc-text-muted-aa small ecc-num"><?= count($periodes) ?> periode</span>
        </div>

        <?php if (empty($periodes)): ?>
            <div class="text-center py-5">
                <p class="ecc-text-muted-aa small mb-3">Belum ada periode survei yang dibuat.</p>
                <?php if ($canManage): ?>
                    <button type="button" class="btn btn-primary rounded-pill px-4 fw-semibold btn-tactile" data-bs-toggle="modal" data-bs-target="#modalPeriodeTracer" onclick="resetPeriodeModal()">
                        Buat Periode Baru
                    </button>
                <?php endif; ?>
            </div>
        <?php else: ?>
            <div class="table-responsive ecc-table-ts-wrap d-none d-md-block">
                <table class="table ecc-table-ts mb-0">
                    <thead>
                        <tr>
                            <th>Periode &amp; Jadwal</th>
                            <th>Pertanyaan</th>
                            <th>Progres Partisipasi</th>
                            <th>Status</th>
                            <th class="text-end">Kontrol &amp; Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($periodes as $pr): ?>
                            <?php
                            $pid    = (int) $pr['id'];
                            $status = (string) ($pr['status_periode'] ?? 'draf');
                            ?>
                            <tr>
                                <td>
                                    <div class="d-flex flex-wrap align-items-center gap-2 mb-1">
                                        <span class="fw-semibold text-dark"><?= esc((string) $pr['nama_periode']) ?></span>
                                        <?= ketarunaan_tracer_target_badge((string) ($pr['target_responden'] ?? 'alumni')) ?>
                                    </div>
                                    <div class="ecc-text-muted-aa ecc-num" style="font-size: 0.75rem;">
                                        <?= esc((string) $pr['kode_periode']) ?>
                                        <?php if (!empty($pr['tanggal_mulai']) || !empty($pr['tanggal_selesai'])): ?>
                                            &bull; <?= esc((string) ($pr['tanggal_mulai'] ?: '-')) ?> s.d. <?= esc((string) ($pr['tanggal_selesai'] ?: '-')) ?>
                                        <?php endif; ?>
                                    </div>
                                    <?php if (!empty($pr['sumber_bundle_nama'])): ?>
                                        <div class="ecc-text-muted-aa" style="font-size: 0.72rem;">Bundel dasar: <?= esc((string) $pr['sumber_bundle_nama']) ?></div>
                                    <?php endif; ?>
                                </td>
                                <td class="ecc-num">
                                    <a href="<?= base_url('ketarunaan/tracer-study/periode/' . $pid . '/pertanyaan') ?>" class="fw-semibold text-decoration-none">
                                        <?= (int) $pr['jumlah_pertanyaan'] ?> soal
                                    </a>
                                    <div class="ecc-text-muted-aa" style="font-size: 0.72rem;">
                                        <?= $status === 'draf' ? 'Dapat diedit' : 'Terkunci' ?>
                                    </div>
                                </td>
                                <td style="min-width: 170px;">
                                    <?php if (!empty($pr['is_mitra'])): ?>
                                        <div class="small ecc-num fw-semibold text-dark"><?= (int) $pr['jumlah_respons'] ?> penilaian masuk</div>
                                        <div class="ecc-text-muted-aa" style="font-size: 0.72rem;">Terbuka untuk instansi mitra</div>
                                    <?php else: ?>
                                        <div class="d-flex justify-content-between small ecc-num mb-1">
                                            <span><?= (int) $pr['jumlah_respons'] ?> / <?= (int) $pr['sasaran_total'] ?></span>
                                            <strong><?= number_format((float) $pr['response_rate'], 1, ',', '.') ?>%</strong>
                                        </div>
                                        <div class="ts-progress">
                                            <div class="ts-progress-bar" style="width: <?= min(100, max(0, (float) $pr['response_rate'])) ?>%;"></div>
                                        </div>
                                    <?php endif; ?>
                                </td>
                                <td>
                                    <?= ketarunaan_tracer_status_periode_badge($status) ?>
                                    <?php if ($status === 'dibuka' && !empty($pr['dibuka_oleh'])): ?>
                                        <div class="ecc-text-muted-aa mt-1" style="font-size: 0.72rem;">Oleh: <?= esc((string) $pr['dibuka_oleh']) ?></div>
                                    <?php elseif ($status === 'ditutup' && !empty($pr['ditutup_oleh'])): ?>
                                        <div class="ecc-text-muted-aa mt-1" style="font-size: 0.72rem;">Oleh: <?= esc((string) $pr['ditutup_oleh']) ?></div>
                                    <?php endif; ?>
                                </td>
                                <td class="text-end text-nowrap">
                                    <div class="d-inline-flex flex-wrap justify-content-end gap-1">
                                        <a href="<?= base_url('ketarunaan/tracer-study?periode_id=' . $pid) ?>" class="btn btn-light border ecc-btn-action">
                                            Hasil
                                        </a>
                                        <?php if (!empty($pr['public_token'])): ?>
                                            <button type="button" class="btn btn-outline-secondary ecc-btn-action"
                                                    onclick="openShareTracerModal(this)"
                                                    data-nama="<?= esc((string) $pr['nama_periode']) ?>"
                                                    data-status="<?= esc($status) ?>"
                                                    data-public-url="<?= esc(ketarunaan_tracer_public_url((string) $pr['public_token'])) ?>"
                                                    data-login-url="<?= esc(base_url('ketarunaan/tracer-study/isi/' . $pid)) ?>"
                                                    data-reset-url="<?= esc(base_url('ketarunaan/tracer-study/periode/' . $pid . '/reset-token')) ?>">
                                                Bagikan
                                            </button>
                                        <?php endif; ?>
                                        <a href="<?= base_url('ketarunaan/tracer-study/periode/' . $pid . '/pertanyaan') ?>" class="btn btn-outline-primary ecc-btn-action">
                                            <?= $status === 'draf' ? 'Atur Soal' : 'Soal' ?>
                                        </a>
                                        <?php if ($canManage): ?>
                                            <button type="button" class="btn btn-light border ecc-btn-action"
                                                    onclick="openEditPeriodeModal(this)"
                                                    data-id="<?= $pid ?>"
                                                    data-kode="<?= esc((string) $pr['kode_periode']) ?>"
                                                    data-slug="<?= esc((string) ($pr['public_token'] ?? '')) ?>"
                                                    data-allow-unreg="<?= (int) ($pr['allow_unregistered_alumni'] ?? 1) ?>"
                                                    data-nama="<?= esc((string) $pr['nama_periode']) ?>"
                                                    data-mulai="<?= esc((string) ($pr['tanggal_mulai'] ?? '')) ?>"
                                                    data-selesai="<?= esc((string) ($pr['tanggal_selesai'] ?? '')) ?>"
                                                    data-target="<?= esc((string) ($pr['target_partisipasi_persen'] ?? 80)) ?>"
                                                    data-deskripsi="<?= esc((string) ($pr['deskripsi'] ?? '')) ?>"
                                                    data-status="<?= esc($status) ?>"
                                                    data-tahun='<?= esc(json_encode($pr['tahun_sasaran'] ?? [])) ?>'
                                                    data-prodi='<?= esc(json_encode($pr['prodi_sasaran'] ?? [])) ?>'>
                                                Ubah
                                            </button>
                                        <?php endif; ?>
                                        <?php if ($canToggle): ?>
                                            <?php if ($status === 'draf'): ?>
                                                <form method="post" action="<?= base_url('ketarunaan/tracer-study/periode/' . $pid . '/status') ?>" class="d-inline js-ts-confirm-form" data-title="Buka Survei?" data-text="Daftar pertanyaan akan dikunci dan alumni mulai dapat mengisi survei." data-confirm="Buka Survei">
                                                    <?= csrf_field() ?>
                                                    <input type="hidden" name="status_periode" value="dibuka">
                                                    <button type="submit" class="btn btn-success ecc-btn-action">Buka</button>
                                                </form>
                                            <?php elseif ($status === 'dibuka'): ?>
                                                <form method="post" action="<?= base_url('ketarunaan/tracer-study/periode/' . $pid . '/status') ?>" class="d-inline js-ts-confirm-form" data-title="Tutup Survei?" data-text="Pengisian survei oleh alumni pada periode ini akan dihentikan." data-confirm="Tutup Survei">
                                                    <?= csrf_field() ?>
                                                    <input type="hidden" name="status_periode" value="ditutup">
                                                    <button type="submit" class="btn btn-danger ecc-btn-action">Tutup</button>
                                                </form>
                                            <?php elseif ($status === 'ditutup'): ?>
                                                <form method="post" action="<?= base_url('ketarunaan/tracer-study/periode/' . $pid . '/status') ?>" class="d-inline js-ts-confirm-form" data-title="Buka Kembali Survei?" data-text="Alumni akan dapat kembali mengisi survei pada periode ini." data-confirm="Buka Kembali">
                                                    <?= csrf_field() ?>
                                                    <input type="hidden" name="status_periode" value="dibuka">
                                                    <button type="submit" class="btn btn-outline-success ecc-btn-action">Buka Lagi</button>
                                                </form>
                                            <?php endif; ?>
                                            <?php if (!empty($pr['can_revert'])): ?>
                                                <form method="post" action="<?= base_url('ketarunaan/tracer-study/periode/' . $pid . '/status') ?>" class="d-inline js-ts-confirm-form" data-title="Kembalikan ke Draf?" data-text="Periode ini belum memiliki respons dan akan dibuka kembali untuk pengeditan pertanyaan." data-confirm="Tarik ke Draf">
                                                    <?= csrf_field() ?>
                                                    <input type="hidden" name="status_periode" value="draf">
                                                    <button type="submit" class="btn btn-outline-secondary ecc-btn-action">Ke Draf</button>
                                                </form>
                                            <?php endif; ?>
                                        <?php endif; ?>
                                    </div>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>

            <!-- Kartu Mobile -->
            <div class="d-md-none d-flex flex-column gap-3">
                <?php foreach ($periodes as $pr): ?>
                    <?php
                    $pid    = (int) $pr['id'];
                    $status = (string) ($pr['status_periode'] ?? 'draf');
                    ?>
                    <div class="border rounded-3 p-3 bg-white">
                        <div class="d-flex justify-content-between align-items-start gap-2 mb-2">
                            <div>
                                <div class="fw-bold text-dark"><?= esc((string) $pr['nama_periode']) ?></div>
                                <div class="d-flex flex-wrap align-items-center gap-1 mt-1">
                                    <?= ketarunaan_tracer_target_badge((string) ($pr['target_responden'] ?? 'alumni')) ?>
                                    <span class="ecc-text-muted-aa ecc-num" style="font-size: 0.75rem;"><?= esc((string) $pr['kode_periode']) ?></span>
                                </div>
                            </div>
                            <?= ketarunaan_tracer_status_periode_badge($status) ?>
                        </div>
                        <div class="small ecc-num mb-2">
                            <?php if (!empty($pr['is_mitra'])): ?>
                                Penilaian masuk: <strong><?= (int) $pr['jumlah_respons'] ?></strong> &bull; <?= (int) $pr['jumlah_pertanyaan'] ?> soal
                            <?php else: ?>
                                Partisipasi: <strong><?= (int) $pr['jumlah_respons'] ?> / <?= (int) $pr['sasaran_total'] ?></strong> (<?= number_format((float) $pr['response_rate'], 1, ',', '.') ?>%) &bull; <?= (int) $pr['jumlah_pertanyaan'] ?> soal
                            <?php endif; ?>
                        </div>
                        <div class="d-flex flex-wrap gap-2 pt-2 border-top">
                            <a href="<?= base_url('ketarunaan/tracer-study?periode_id=' . $pid) ?>" class="btn btn-light border btn-sm rounded-pill px-3 ts-touch-ctrl d-inline-flex align-items-center">Hasil</a>
                            <?php if (!empty($pr['public_token'])): ?>
                                <button type="button" class="btn btn-outline-secondary btn-sm rounded-pill px-3 ts-touch-ctrl d-inline-flex align-items-center"
                                        onclick="openShareTracerModal(this)"
                                        data-nama="<?= esc((string) $pr['nama_periode']) ?>"
                                        data-status="<?= esc($status) ?>"
                                        data-public-url="<?= esc(ketarunaan_tracer_public_url((string) $pr['public_token'])) ?>"
                                        data-login-url="<?= esc(base_url('ketarunaan/tracer-study/isi/' . $pid)) ?>"
                                        data-reset-url="<?= esc(base_url('ketarunaan/tracer-study/periode/' . $pid . '/reset-token')) ?>">
                                    Bagikan
                                </button>
                            <?php endif; ?>
                            <a href="<?= base_url('ketarunaan/tracer-study/periode/' . $pid . '/pertanyaan') ?>" class="btn btn-outline-primary btn-sm rounded-pill px-3 ts-touch-ctrl d-inline-flex align-items-center"><?= $status === 'draf' ? 'Atur Soal' : 'Soal' ?></a>
                            <?php if ($canToggle && $status === 'draf'): ?>
                                <form method="post" action="<?= base_url('ketarunaan/tracer-study/periode/' . $pid . '/status') ?>" class="d-inline js-ts-confirm-form" data-title="Buka Survei?" data-text="Daftar pertanyaan akan dikunci dan alumni mulai dapat mengisi survei." data-confirm="Buka Survei">
                                    <?= csrf_field() ?>
                                    <input type="hidden" name="status_periode" value="dibuka">
                                    <button type="submit" class="btn btn-success btn-sm rounded-pill px-3 ts-touch-ctrl">Buka</button>
                                </form>
                            <?php elseif ($canToggle && $status === 'dibuka'): ?>
                                <form method="post" action="<?= base_url('ketarunaan/tracer-study/periode/' . $pid . '/status') ?>" class="d-inline js-ts-confirm-form" data-title="Tutup Survei?" data-text="Pengisian survei oleh alumni pada periode ini akan dihentikan." data-confirm="Tutup Survei">
                                    <?= csrf_field() ?>
                                    <input type="hidden" name="status_periode" value="ditutup">
                                    <button type="submit" class="btn btn-danger btn-sm rounded-pill px-3 ts-touch-ctrl">Tutup</button>
                                </form>
                            <?php endif; ?>
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>
        <?php endif; ?>
    </div>
</div>

<?php if ($canManage): ?>
<!-- Modal Buat / Ubah Periode -->
<div class="modal fade" id="modalPeriodeTracer" tabindex="-1" aria-labelledby="modalPeriodeTracerLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg modal-dialog-centered modal-dialog-scrollable">
        <form method="post" action="<?= base_url('ketarunaan/tracer-study/periode/simpan') ?>" class="modal-content border-0 rounded-4 shadow">
            <?= csrf_field() ?>
            <input type="hidden" name="periode_id" id="pModalId" value="0">

            <div class="modal-header border-bottom px-4 py-3">
                <h2 class="modal-title h5 fw-bold text-dark mb-0" id="modalPeriodeTracerLabel">Buat Periode Survei Baru</h2>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Tutup"></button>
            </div>

            <div class="modal-body px-4 py-3">
                <div class="row g-3">
                    <div class="col-12 col-md-7">
                        <label for="pModalNama" class="form-label fw-semibold small">Nama Periode Survei <span class="text-danger">*</span></label>
                        <input type="text" class="form-control ecc-control-sm" id="pModalNama" name="nama_periode" required placeholder="Contoh: Tracer Study Lulusan 2026 Gelombang 1">
                    </div>
                    <div class="col-12 col-md-5">
                        <label for="pModalKode" class="form-label fw-semibold small">Kode Periode</label>
                        <input type="text" class="form-control ecc-control-sm text-uppercase" id="pModalKode" name="kode_periode" maxlength="40" placeholder="Otomatis jika kosong">
                    </div>

                    <div class="col-12">
                        <label for="pModalSlug" class="form-label fw-semibold small">Tautan Singkat Survei (Untuk Disebar ke Alumni)</label>
                        <div class="input-group">
                            <span class="input-group-text bg-light text-secondary small" style="border-radius: 8px 0 0 8px;"><?= esc(rtrim(base_url(), '/')) ?>/</span>
                            <input type="text" class="form-control ecc-control-sm" id="pModalSlug" name="public_token" maxlength="50" placeholder="tracerstudy<?= date('Y') ?>" style="border-radius: 0 8px 8px 0;">
                        </div>
                        <div class="form-text" style="font-size: 0.75rem;">Awali dengan <code>tracer</code> (huruf kecil/angka, tanpa spasi), contoh: <code>tracerstudy2026</code>. Kosongkan untuk dibuat otomatis.</div>
                    </div>

                        <!-- Pilihan Sumber Pertanyaan (hanya saat buat baru) -->
                        <div class="col-12" id="pSumberWrap">
                            <div class="p-3 rounded-3 border bg-light">
                                <div class="fw-semibold small text-dark mb-2">Sumber Pertanyaan Survei</div>
                                <div class="d-flex flex-wrap gap-3 mb-2">
                                    <div class="form-check">
                                        <input class="form-check-input" type="radio" name="sumber_pertanyaan" id="srcBundle" value="bundle" checked onchange="toggleSumberPertanyaan()">
                                        <label class="form-check-label small fw-medium" for="srcBundle">Gunakan Bundel Pertanyaan</label>
                                    </div>
                                    <div class="form-check">
                                        <input class="form-check-input" type="radio" name="sumber_pertanyaan" id="srcPeriode" value="periode" onchange="toggleSumberPertanyaan()">
                                        <label class="form-check-label small fw-medium" for="srcPeriode">Salin dari Periode Lain</label>
                                    </div>
                                    <div class="form-check">
                                        <input class="form-check-input" type="radio" name="sumber_pertanyaan" id="srcKosong" value="kosong" onchange="toggleSumberPertanyaan()">
                                        <label class="form-check-label small fw-medium" for="srcKosong">Mulai Kosong</label>
                                    </div>
                                </div>
                                <div id="wrapSelectBundle">
                                    <select name="sumber_bundle_id" id="pSumberBundle" class="form-select ecc-control-sm" onchange="syncTargetFromSource()">
                                        <?php foreach (($refs['bundles'] ?? []) as $bn): ?>
                                            <option value="<?= (int) $bn['id'] ?>" data-target="<?= esc((string) ($bn['target_responden'] ?? 'alumni')) ?>">
                                                [<?= (($bn['target_responden'] ?? 'alumni') === 'mitra') ? 'Pengguna Lulusan' : 'Alumni' ?>] <?= esc((string) $bn['nama_bundle']) ?> (<?= (int) $bn['jumlah_pertanyaan'] ?> pertanyaan)
                                            </option>
                                        <?php endforeach; ?>
                                    </select>
                                    <div class="form-text" style="font-size: 0.75rem;">Seluruh pertanyaan &amp; surat pengantar bundel akan disalin ke periode ini dan dapat dimodifikasi sebelum survei dibuka.</div>
                                </div>
                                <div id="wrapSelectPeriode" class="d-none">
                                    <select name="sumber_periode_id" id="pSumberPeriode" class="form-select ecc-control-sm" onchange="syncTargetFromSource()">
                                        <?php if (empty($periodes)): ?>
                                            <option value="0" data-target="alumni">Belum ada periode sebelumnya</option>
                                        <?php else: ?>
                                            <?php foreach ($periodes as $sp): ?>
                                                <option value="<?= (int) $sp['id'] ?>" data-target="<?= esc((string) ($sp['target_responden'] ?? 'alumni')) ?>">
                                                    [<?= !empty($sp['is_mitra']) ? 'Pengguna Lulusan' : 'Alumni' ?>] <?= esc((string) $sp['nama_periode']) ?> (<?= (int) $sp['jumlah_pertanyaan'] ?> pertanyaan)
                                                </option>
                                            <?php endforeach; ?>
                                        <?php endif; ?>
                                    </select>
                                    <div class="form-text" style="font-size: 0.75rem;">Menyalin susunan pertanyaan periode terdahulu tanpa mengubah data periode lama.</div>
                                </div>
                                <div class="mt-2">
                                    <label for="pModalTargetResp" class="form-label fw-semibold small mb-1">Sasaran Responden</label>
                                    <select name="target_responden" id="pModalTargetResp" class="form-select ecc-control-sm" onchange="toggleTargetRespondenFields()">
                                        <option value="alumni">Lulusan / Alumni PKTJ</option>
                                        <option value="mitra">Pengguna Lulusan (Atasan Langsung / Instansi)</option>
                                    </select>
                                </div>
                            </div>
                        </div>

                        <div class="col-6 col-md-4">
                            <label for="pModalMulai" class="form-label fw-semibold small">Tanggal Mulai (Opsional)</label>
                            <input type="text" class="form-control ecc-control-sm bg-white js-ts-date" id="pModalMulai" name="tanggal_mulai" placeholder="YYYY-MM-DD">
                        </div>
                        <div class="col-6 col-md-4">
                            <label for="pModalSelesai" class="form-label fw-semibold small">Tanggal Selesai (Opsional)</label>
                            <input type="text" class="form-control ecc-control-sm bg-white js-ts-date" id="pModalSelesai" name="tanggal_selesai" placeholder="YYYY-MM-DD">
                        </div>
                        <div class="col-12 col-md-4">
                            <label for="pModalTarget" class="form-label fw-semibold small">Target Partisipasi (%)</label>
                            <input type="number" class="form-control ecc-control-sm ecc-num" id="pModalTarget" name="target_partisipasi_persen" min="1" max="100" value="80">
                        </div>

                        <div class="col-12 col-md-6" id="pSasaranTahunWrap">
                            <label class="form-label fw-semibold small">Tahun Lulus Sasaran</label>
                            <div class="border rounded-3 p-2 bg-white" style="max-height: 120px; overflow-y: auto;">
                                <?php foreach (($refs['tahun_lulus'] ?? []) as $tl): ?>
                                    <div class="form-check form-check-inline">
                                        <input class="form-check-input js-p-tahun" type="checkbox" name="tahun_lulus_sasaran[]" id="chkThn<?= (int) $tl ?>" value="<?= (int) $tl ?>">
                                        <label class="form-check-label small ecc-num" for="chkThn<?= (int) $tl ?>"><?= (int) $tl ?></label>
                                    </div>
                                <?php endforeach; ?>
                            </div>
                            <div class="form-text" style="font-size: 0.72rem;">Kosongkan untuk menargetkan semua tahun lulus alumni aktif.</div>
                        </div>

                        <div class="col-12 col-md-6" id="pSasaranProdiWrap">
                            <label class="form-label fw-semibold small">Program Studi Sasaran</label>
                            <div class="border rounded-3 p-2 bg-white" style="max-height: 120px; overflow-y: auto;">
                                <?php foreach (($refs['prodi'] ?? []) as $pd): ?>
                                    <div class="form-check">
                                        <input class="form-check-input js-p-prodi" type="checkbox" name="prodi_sasaran[]" id="chkPrd<?= (int) $pd['id'] ?>" value="<?= (int) $pd['id'] ?>">
                                        <label class="form-check-label small" for="chkPrd<?= (int) $pd['id'] ?>"><?= esc($pd['kode_prodi'] . ' - ' . $pd['nama_prodi']) ?></label>
                                    </div>
                                <?php endforeach; ?>
                            </div>
                            <div class="form-text" style="font-size: 0.72rem;">Kosongkan untuk menargetkan seluruh program studi.</div>
                        </div>

                        <div class="col-12" id="pAllowUnregWrap">
                            <div class="form-check form-switch">
                                <input class="form-check-input" type="checkbox" role="switch" id="pModalAllowUnreg" name="allow_unregistered_alumni" value="1" checked>
                                <label class="form-check-label small fw-semibold" for="pModalAllowUnreg">Izinkan alumni yang belum terdaftar di sistem untuk mengisi survei</label>
                            </div>
                            <div class="form-text" style="font-size: 0.75rem;">Jawaban alumni baru akan masuk ke antrean verifikasi pengelola sebelum ditambahkan ke Data Alumni.</div>
                        </div>

                        <div class="col-12">
                            <label for="pModalDesk" class="form-label fw-semibold small">Catatan / Deskripsi Singkat</label>
                            <textarea class="form-control" id="pModalDesk" name="deskripsi" rows="2" placeholder="Penjelasan singkat bagi responden alumni..."></textarea>
                        </div>
                    </div>
                </div>

                <div class="modal-footer border-top px-4 py-3">
                    <button type="button" class="btn btn-light border rounded-pill px-3" data-bs-dismiss="modal">Batal</button>
                    <button type="submit" class="btn btn-primary rounded-pill px-4 fw-semibold btn-tactile">Simpan Periode</button>
                </div>
            </form>
    </div>
</div>
<?php endif; ?>
<?= $this->include('ketarunaan/tracer_study/_share_link_modal') ?>
<?= $this->endSection() ?>

<?= $this->section('scripts') ?>
<script>
function toggleTargetRespondenFields() {
    var sel = document.getElementById('pModalTargetResp');
    var isMitra = sel && sel.value === 'mitra';
    var tahunWrap = document.getElementById('pSasaranTahunWrap');
    var unregWrap = document.getElementById('pAllowUnregWrap');
    if (tahunWrap) tahunWrap.classList.toggle('d-none', isMitra);
    if (unregWrap) unregWrap.classList.toggle('d-none', isMitra);
}

function syncTargetFromSource() {
    var src = document.querySelector('input[name="sumber_pertanyaan"]:checked');
    var val = src ? src.value : 'bundle';
    var targetSel = document.getElementById('pModalTargetResp');
    if (!targetSel) return;
    if (val === 'bundle') {
        var bSel = document.getElementById('pSumberBundle');
        var opt = bSel && bSel.options[bSel.selectedIndex];
        if (opt && opt.dataset.target) targetSel.value = opt.dataset.target;
    } else if (val === 'periode') {
        var pSel = document.getElementById('pSumberPeriode');
        var optP = pSel && pSel.options[pSel.selectedIndex];
        if (optP && optP.dataset.target) targetSel.value = optP.dataset.target;
    }
    toggleTargetRespondenFields();
}

function toggleSumberPertanyaan() {
    var src = document.querySelector('input[name="sumber_pertanyaan"]:checked');
    var val = src ? src.value : 'bundle';
    var wb = document.getElementById('wrapSelectBundle');
    var wp = document.getElementById('wrapSelectPeriode');
    if (wb) wb.classList.toggle('d-none', val !== 'bundle');
    if (wp) wp.classList.toggle('d-none', val !== 'periode');
    syncTargetFromSource();
}

function resetPeriodeModal() {
    document.getElementById('modalPeriodeTracerLabel').textContent = 'Buat Periode Survei Baru';
    document.getElementById('pModalId').value = '0';
    document.getElementById('pModalNama').value = '';
    document.getElementById('pModalKode').value = '';
    document.getElementById('pModalSlug').value = '';
    document.getElementById('pModalMulai').value = '';
    document.getElementById('pModalSelesai').value = '';
    document.getElementById('pModalTarget').value = '80';
    var unregChk = document.getElementById('pModalAllowUnreg');
    if (unregChk) unregChk.checked = true;
    document.getElementById('pModalDesk').value = '';
    var sw = document.getElementById('pSumberWrap');
    if (sw) sw.classList.remove('d-none');
    document.querySelectorAll('.js-p-tahun, .js-p-prodi').forEach(function(el) { el.checked = false; el.disabled = false; });
    toggleSumberPertanyaan();
}

function openEditPeriodeModal(btn) {
    var d = btn.dataset;
    document.getElementById('modalPeriodeTracerLabel').textContent = 'Ubah Periode Survei';
    document.getElementById('pModalId').value = d.id || '0';
    document.getElementById('pModalNama').value = d.nama || '';
    document.getElementById('pModalKode').value = d.kode || '';
    document.getElementById('pModalSlug').value = d.slug || '';
    document.getElementById('pModalMulai').value = d.mulai || '';
    document.getElementById('pModalSelesai').value = d.selesai || '';
    document.getElementById('pModalTarget').value = d.target || '80';
    var unregChk = document.getElementById('pModalAllowUnreg');
    if (unregChk) unregChk.checked = (d.allowUnreg !== '0');
    document.getElementById('pModalDesk').value = d.deskripsi || '';

    var sw = document.getElementById('pSumberWrap');
    if (sw) sw.classList.add('d-none');

    var tahunArr = [];
    var prodiArr = [];
    try { tahunArr = JSON.parse(d.tahun || '[]'); } catch (e) {}
    try { prodiArr = JSON.parse(d.prodi || '[]'); } catch (e) {}

    var isLocked = (d.status !== 'draf');
    document.querySelectorAll('.js-p-tahun').forEach(function(el) {
        el.checked = tahunArr.indexOf(parseInt(el.value, 10)) !== -1;
        el.disabled = isLocked;
    });
    document.querySelectorAll('.js-p-prodi').forEach(function(el) {
        el.checked = prodiArr.indexOf(parseInt(el.value, 10)) !== -1;
        el.disabled = isLocked;
    });

    var modalEl = document.getElementById('modalPeriodeTracer');
    if (window.bootstrap && modalEl) {
        bootstrap.Modal.getOrCreateInstance(modalEl).show();
    }
}

document.addEventListener('DOMContentLoaded', function() {
    if (typeof flatpickr !== 'undefined') {
        flatpickr('.js-ts-date', { dateFormat: 'Y-m-d', allowInput: true, locale: 'id' });
    }

    var params = new URLSearchParams(window.location.search);
    var createBundleId = params.get('create_bundle_id');
    if (createBundleId) {
        resetPeriodeModal();
        var bSel = document.getElementById('pSumberBundle');
        if (bSel) {
            bSel.value = createBundleId;
            syncTargetFromSource();
        }
        var modalEl = document.getElementById('modalPeriodeTracer');
        if (window.bootstrap && modalEl) {
            bootstrap.Modal.getOrCreateInstance(modalEl).show();
        }
    }

    document.querySelectorAll('.js-ts-confirm-form').forEach(function(form) {
        form.addEventListener('submit', function(e) {
            e.preventDefault();
            var title = form.dataset.title || 'Konfirmasi';
            var text  = form.dataset.text || 'Lanjutkan tindakan ini?';
            var btnTx = form.dataset.confirm || 'Ya, Lanjutkan';

            if (typeof Swal !== 'undefined') {
                Swal.fire({
                    title: title,
                    text: text,
                    icon: 'question',
                    showCancelButton: true,
                    confirmButtonText: btnTx,
                    cancelButtonText: 'Batal',
                    reverseButtons: true,
                    customClass: { popup: 'rounded-4 shadow border-0' }
                }).then(function(res) {
                    if (res.isConfirmed) form.submit();
                });
            } else if (confirm(title + '\n' + text)) {
                form.submit();
            }
        });
    });
});
</script>
<?= $this->endSection() ?>
