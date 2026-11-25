<?= $this->extend('layouts/ecc2_layout') ?>

<?= $this->section('title') ?>Progres & Responden Tracer Study<?= $this->endSection() ?>

<?= $this->section('styles') ?>
<?= $this->include('ketarunaan/tracer_study/_styles') ?>
<?= $this->endSection() ?>

<?= $this->section('content') ?>
<?php
$periodes     = $periodes ?? [];
$selectedPid  = (int) ($selectedPid ?? 0);
$filters      = $filters ?? [];
$refs         = $refs ?? [];
$data         = $data ?? null;
$perms        = $perms ?? [];
$canManage    = !empty($perms['can_manage']);
$rawSub       = (string) ($filters['sub'] ?? 'sudah');
$subTab       = in_array($rawSub, ['sudah', 'verifikasi', 'belum'], true) ? $rawSub : 'sudah';
$responded    = $data['responded'] ?? [];
$pendingVerif = $data['pending_verif'] ?? [];
$pending      = $data['pending'] ?? [];
$periode      = $data['periode'] ?? [];
$isMitra      = !empty($periode['is_mitra']);
if ($isMitra) {
    $subTab = 'sudah';
}
$transisiCat  = ketarunaan_tracer_status_transisi_catalog();
$bidangCat    = ketarunaan_tracer_kesesuaian_bidang_catalog();
?>
<div class="container-fluid px-0">
    <?= $this->include('ketarunaan/tracer_study/_nav_tabs') ?>

    <?php if (empty($periodes)): ?>
        <div class="ecc-ts-card text-center py-5 ecc-text-muted-aa small">Belum ada periode Tracer Study.</div>
    <?php else: ?>
        <!-- Filter Periode & Pencarian -->
        <div class="ecc-ts-card mb-4 py-3">
            <form method="get" action="<?= base_url('ketarunaan/tracer-study/responden') ?>" class="row g-2 align-items-end">
                <input type="hidden" name="sub" value="<?= esc($subTab) ?>">
                <div class="col-12 col-md-4">
                    <label for="respPeriode" class="form-label fw-semibold ecc-text-muted-aa mb-1" style="font-size: 0.75rem;">Periode</label>
                    <select id="respPeriode" name="periode_id" class="form-select ecc-control-sm" onchange="this.form.submit()">
                        <?php foreach ($periodes as $pr): ?>
                            <option value="<?= (int) $pr['id'] ?>" <?= (int) $pr['id'] === $selectedPid ? 'selected' : '' ?>>
                                <?= esc($pr['nama_periode']) ?> (<?= strtoupper(esc((string) $pr['status_periode'])) ?>)
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>
                <div class="col-6 col-md-2">
                    <label for="respProdi" class="form-label fw-semibold ecc-text-muted-aa mb-1" style="font-size: 0.75rem;">Prodi</label>
                    <select id="respProdi" name="prodi_id" class="form-select ecc-control-sm" onchange="this.form.submit()">
                        <option value="0">Semua Prodi</option>
                        <?php foreach (($refs['prodi'] ?? []) as $pd): ?>
                            <option value="<?= (int) $pd['id'] ?>" <?= (int) ($filters['prodi_id'] ?? 0) === (int) $pd['id'] ? 'selected' : '' ?>>
                                <?= esc($pd['kode_prodi']) ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>
                <div class="col-6 col-md-2">
                    <label for="respTahun" class="form-label fw-semibold ecc-text-muted-aa mb-1" style="font-size: 0.75rem;">Tahun Lulus</label>
                    <select id="respTahun" name="tahun_lulus" class="form-select ecc-control-sm" onchange="this.form.submit()">
                        <option value="0">Semua Tahun</option>
                        <?php foreach (($refs['tahun_lulus'] ?? []) as $tl): ?>
                            <option value="<?= (int) $tl ?>" <?= (int) ($filters['tahun_lulus'] ?? 0) === (int) $tl ? 'selected' : '' ?>>
                                <?= (int) $tl ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>
                <div class="col-12 col-md-4 d-flex gap-2">
                    <input type="text" name="q" class="form-control ecc-control-sm" value="<?= esc((string) ($filters['q'] ?? '')) ?>" placeholder="Cari nama, Notar, atau instansi...">
                    <button type="submit" class="btn btn-outline-primary ecc-control-sm px-3 fw-semibold text-nowrap">Cari</button>
                </div>
            </form>
        </div>

        <!-- Sub-Navigasi Sudah Mengisi / Verifikasi Alumni Baru / Belum Mengisi -->
        <div class="d-flex flex-wrap justify-content-between align-items-center gap-2 mb-3">
            <div class="d-flex flex-wrap gap-2">
                <a href="<?= base_url('ketarunaan/tracer-study/responden?periode_id=' . $selectedPid . '&sub=sudah') ?>"
                   class="btn btn-sm rounded-pill px-3 fw-semibold ts-touch-ctrl d-inline-flex align-items-center <?= $subTab === 'sudah' ? 'btn-primary' : 'btn-light border' ?>">
                    <?= $isMitra ? 'Penilaian Masuk' : 'Tervalidasi' ?> (<?= count($responded) ?>)
                </a>
                <?php if (!$isMitra): ?>
                    <a href="<?= base_url('ketarunaan/tracer-study/responden?periode_id=' . $selectedPid . '&sub=verifikasi') ?>"
                       class="btn btn-sm rounded-pill px-3 fw-semibold ts-touch-ctrl d-inline-flex align-items-center gap-1 <?= $subTab === 'verifikasi' ? 'btn-warning text-dark' : 'btn-light border' ?>">
                        <span>Verifikasi Alumni Baru</span>
                        <span class="badge rounded-pill <?= count($pendingVerif) > 0 ? 'bg-danger text-white' : 'bg-secondary-subtle text-secondary-emphasis' ?> ecc-num"><?= count($pendingVerif) ?></span>
                    </a>
                    <a href="<?= base_url('ketarunaan/tracer-study/responden?periode_id=' . $selectedPid . '&sub=belum') ?>"
                       class="btn btn-sm rounded-pill px-3 fw-semibold ts-touch-ctrl d-inline-flex align-items-center <?= $subTab === 'belum' ? 'btn-primary' : 'btn-light border' ?>">
                        Belum Mengisi (<?= count($pending) ?>)
                    </a>
                <?php endif; ?>
            </div>
            <div class="d-flex flex-wrap gap-2">
                <?php if ($canManage && ($periode['status_periode'] ?? '') !== 'draf'): ?>
                    <a href="<?= base_url('ketarunaan/tracer-study/isi/' . $selectedPid) ?>" class="btn btn-primary btn-sm rounded-pill px-3 fw-semibold ts-touch-ctrl d-inline-flex align-items-center">
                        <?= $isMitra ? '+ Input Penilaian PIC' : '+ Input Respons PIC' ?>
                    </a>
                <?php endif; ?>
                <a href="<?= base_url('ketarunaan/tracer-study/ekspor?periode_id=' . $selectedPid) ?>" class="btn btn-outline-secondary btn-sm rounded-pill px-3 fw-semibold ts-touch-ctrl d-inline-flex align-items-center">
                    Ekspor CSV
                </a>
            </div>
        </div>

        <div class="ecc-ts-card">
            <?php if ($subTab === 'sudah' && $isMitra): ?>
                <div class="table-responsive ecc-table-ts-wrap">
                    <table class="table ecc-table-ts mb-0">
                        <thead>
                            <tr>
                                <th>Atasan Langsung / Penilai</th>
                                <th>Instansi / Perusahaan</th>
                                <th>Alumni yang Dinilai</th>
                                <th>Prodi</th>
                                <th>Kontak</th>
                                <th class="text-end">Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php if (empty($responded)): ?>
                                <tr><td colspan="6" class="text-center ecc-text-muted-aa py-4">Belum ada penilaian pengguna lulusan yang masuk.</td></tr>
                            <?php else: ?>
                                <?php foreach ($responded as $r): ?>
                                    <tr>
                                        <td>
                                            <div class="fw-semibold text-dark"><?= esc((string) ($r['nama_lengkap'] ?: $r['nomor_taruna'])) ?></div>
                                            <div class="ecc-text-muted-aa ecc-num" style="font-size: 0.75rem;"><?= esc((string) $r['nomor_taruna']) ?></div>
                                        </td>
                                        <td class="small text-dark"><?= esc((string) ($r['nama_instansi'] ?: '-')) ?></td>
                                        <td class="fw-semibold text-dark small"><?= esc((string) ($r['nama_alumni_dinilai'] ?: '-')) ?></td>
                                        <td class="ecc-num small"><?= esc((string) ($r['kode_prodi'] ?: '-')) ?></td>
                                        <td class="small">
                                            <div class="ecc-num"><?= esc((string) ($r['telepon_alumni'] ?: '-')) ?></div>
                                            <div class="ecc-text-muted-aa" style="font-size: 0.75rem;"><?= esc((string) ($r['email_alumni'] ?: '-')) ?></div>
                                        </td>
                                        <td class="text-end text-nowrap">
                                            <a href="<?= base_url('ketarunaan/tracer-study/respons/' . (int) $r['id']) ?>" class="btn btn-outline-primary ecc-btn-action">Detail</a>
                                        </td>
                                    </tr>
                                <?php endforeach; ?>
                            <?php endif; ?>
                        </tbody>
                    </table>
                </div>
            <?php elseif ($subTab === 'sudah'): ?>
                <div class="table-responsive ecc-table-ts-wrap">
                    <table class="table ecc-table-ts mb-0">
                        <thead>
                            <tr>
                                <th>Alumni</th>
                                <th>Prodi / Lulus</th>
                                <th>Status &amp; Instansi</th>
                                <th>Masa Tunggu</th>
                                <th>Kesesuaian</th>
                                <th class="text-end">Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php if (empty($responded)): ?>
                                <tr><td colspan="6" class="text-center ecc-text-muted-aa py-4">Belum ada respons tervalidasi pada filter ini.</td></tr>
                            <?php else: ?>
                                <?php foreach ($responded as $r): ?>
                                    <?php $tMeta = $transisiCat[$r['status_transisi'] ?? ''] ?? ['label' => $r['status_transisi'], 'badge_class' => 'bg-light text-dark border']; ?>
                                    <tr>
                                        <td>
                                            <div class="fw-semibold text-dark"><?= esc((string) ($r['nama_lengkap'] ?: $r['nomor_taruna'])) ?></div>
                                            <div class="ecc-text-muted-aa ecc-num" style="font-size: 0.75rem;"><?= esc((string) $r['nomor_taruna']) ?></div>
                                        </td>
                                        <td class="ecc-num">
                                            <div><?= esc((string) ($r['kode_prodi'] ?: '-')) ?></div>
                                            <div class="ecc-text-muted-aa" style="font-size: 0.75rem;"><?= esc((string) ($r['tahun_lulus'] ?: '-')) ?></div>
                                        </td>
                                        <td>
                                            <span class="badge rounded-pill px-2 py-1 fw-semibold <?= esc($tMeta['badge_class']) ?>" style="font-size: 0.72rem;"><?= esc($tMeta['label']) ?></span>
                                            <?php if (!empty($r['nama_instansi'])): ?>
                                                <div class="small text-dark mt-1"><?= esc((string) $r['nama_instansi']) ?></div>
                                            <?php endif; ?>
                                        </td>
                                        <td class="ecc-num"><?= $r['masa_tunggu_bulan'] !== null ? ((int) $r['masa_tunggu_bulan'] . ' bln') : '-' ?></td>
                                        <td class="small"><?= esc($bidangCat[$r['kesesuaian_bidang'] ?? ''] ?? '-') ?></td>
                                        <td class="text-end text-nowrap">
                                            <a href="<?= base_url('ketarunaan/tracer-study/respons/' . (int) $r['id']) ?>" class="btn btn-outline-primary ecc-btn-action">Detail</a>
                                            <?php if ($canManage && ($periode['status_periode'] ?? '') !== 'draf'): ?>
                                                <a href="<?= base_url('ketarunaan/tracer-study/isi/' . $selectedPid . '?nomor_taruna=' . urlencode((string) $r['nomor_taruna'])) ?>" class="btn btn-light border ecc-btn-action">Ubah</a>
                                            <?php endif; ?>
                                        </td>
                                    </tr>
                                <?php endforeach; ?>
                            <?php endif; ?>
                        </tbody>
                    </table>
                </div>

            <?php elseif ($subTab === 'verifikasi'): ?>
                <div class="alert alert-light border small text-dark mb-3 py-2 px-3">
                    Daftar berikut adalah respons dari <strong>alumni yang belum tercatat di database</strong>. Setujui untuk mendaftarkan ke Data Alumni resmi (serta opsional membuatkan akun login ECC) dan memasukkan jawabannya ke statistik.
                </div>
                <div class="table-responsive ecc-table-ts-wrap">
                    <table class="table ecc-table-ts mb-0">
                        <thead>
                            <tr>
                                <th>Identitas Alumni Baru</th>
                                <th>Prodi / Lulus / Tgl Lahir</th>
                                <th>Kontak</th>
                                <th>Status &amp; Instansi</th>
                                <th class="text-end">Keputusan</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php if (empty($pendingVerif)): ?>
                                <tr><td colspan="5" class="text-center ecc-text-muted-aa py-4">Tidak ada respons alumni baru yang menunggu verifikasi.</td></tr>
                            <?php else: ?>
                                <?php foreach ($pendingVerif as $pv): ?>
                                    <?php
                                    $pvId  = (int) $pv['id'];
                                    $tMeta = $transisiCat[$pv['status_transisi'] ?? ''] ?? ['label' => $pv['status_transisi'], 'badge_class' => 'bg-light text-dark border'];
                                    ?>
                                    <tr>
                                        <td>
                                            <div class="fw-semibold text-dark"><?= esc((string) ($pv['nama_lengkap'] ?: $pv['nomor_taruna'])) ?></div>
                                            <div class="ecc-text-muted-aa ecc-num" style="font-size: 0.75rem;">Notar: <?= esc((string) $pv['nomor_taruna']) ?></div>
                                        </td>
                                        <td class="small ecc-num">
                                            <div><?= esc((string) ($pv['kode_prodi'] ?: '-')) ?> &bull; Lulus <?= esc((string) ($pv['tahun_lulus'] ?: '-')) ?></div>
                                            <div class="ecc-text-muted-aa" style="font-size: 0.75rem;">Lahir: <?= esc((string) ($pv['tanggal_lahir_responden'] ?: '-')) ?></div>
                                        </td>
                                        <td class="small">
                                            <div class="ecc-num"><?= esc((string) ($pv['telepon_alumni'] ?: '-')) ?></div>
                                            <div class="ecc-text-muted-aa" style="font-size: 0.75rem;"><?= esc((string) ($pv['email_alumni'] ?: '-')) ?></div>
                                        </td>
                                        <td>
                                            <span class="badge rounded-pill px-2 py-1 fw-semibold <?= esc($tMeta['badge_class']) ?>" style="font-size: 0.72rem;"><?= esc($tMeta['label']) ?></span>
                                            <?php if (!empty($pv['nama_instansi'])): ?>
                                                <div class="small text-dark mt-1"><?= esc((string) $pv['nama_instansi']) ?></div>
                                            <?php endif; ?>
                                        </td>
                                        <td class="text-end text-nowrap">
                                            <a href="<?= base_url('ketarunaan/tracer-study/respons/' . $pvId) ?>" class="btn btn-light border ecc-btn-action">Detail</a>
                                            <?php if ($canManage): ?>
                                                <button type="button" class="btn btn-success ecc-btn-action"
                                                        onclick="openApproveAlumniModal(this)"
                                                        data-id="<?= $pvId ?>"
                                                        data-nama="<?= esc((string) ($pv['nama_lengkap'] ?: $pv['nomor_taruna'])) ?>"
                                                        data-notar="<?= esc((string) $pv['nomor_taruna']) ?>"
                                                        data-dob="<?= esc((string) ($pv['tanggal_lahir_responden'] ?: '-')) ?>"
                                                        data-action="<?= esc(base_url('ketarunaan/tracer-study/respons/' . $pvId . '/setujui')) ?>">
                                                    Setujui
                                                </button>
                                                <button type="button" class="btn btn-outline-danger ecc-btn-action"
                                                        onclick="openRejectAlumniModal(this)"
                                                        data-nama="<?= esc((string) ($pv['nama_lengkap'] ?: $pv['nomor_taruna'])) ?>"
                                                        data-action="<?= esc(base_url('ketarunaan/tracer-study/respons/' . $pvId . '/tolak')) ?>">
                                                    Tolak
                                                </button>
                                            <?php endif; ?>
                                        </td>
                                    </tr>
                                <?php endforeach; ?>
                            <?php endif; ?>
                        </tbody>
                    </table>
                </div>

            <?php else: ?>
                <div class="table-responsive ecc-table-ts-wrap">
                    <table class="table ecc-table-ts mb-0">
                        <thead>
                            <tr>
                                <th>Alumni Sasaran</th>
                                <th>Prodi / Tahun Lulus</th>
                                <th>Kontak Terdaftar</th>
                                <th>Instansi Terakhir</th>
                                <th class="text-end">Tindak Lanjut</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php if (empty($pending)): ?>
                                <tr><td colspan="5" class="text-center ecc-text-muted-aa py-4">Seluruh alumni terdaftar sasaran pada filter ini sudah mengisi survei.</td></tr>
                            <?php else: ?>
                                <?php foreach ($pending as $al): ?>
                                    <tr>
                                        <td>
                                            <div class="fw-semibold text-dark"><?= esc((string) ($al['nama_lengkap'] ?: $al['nomor_taruna'])) ?></div>
                                            <div class="ecc-text-muted-aa ecc-num" style="font-size: 0.75rem;"><?= esc((string) $al['nomor_taruna']) ?></div>
                                        </td>
                                        <td class="ecc-num">
                                            <?= esc((string) ($al['kode_prodi'] ?: '-')) ?> &bull; <?= esc((string) ($al['tahun_lulus'] ?: '-')) ?>
                                        </td>
                                        <td class="small">
                                            <div><?= esc((string) ($al['telepon_alumni'] ?: '-')) ?></div>
                                            <div class="ecc-text-muted-aa" style="font-size: 0.75rem;"><?= esc((string) ($al['email_alumni'] ?: '-')) ?></div>
                                        </td>
                                        <td class="small"><?= esc((string) ($al['instansi_saat_ini'] ?: '-')) ?></td>
                                        <td class="text-end">
                                            <?php if ($canManage && ($periode['status_periode'] ?? '') !== 'draf'): ?>
                                                <a href="<?= base_url('ketarunaan/tracer-study/isi/' . $selectedPid . '?nomor_taruna=' . urlencode((string) $al['nomor_taruna'])) ?>" class="btn btn-outline-primary ecc-btn-action">
                                                    Inputkan Survei
                                                </a>
                                            <?php else: ?>
                                                <span class="ecc-text-muted-aa small">Menunggu</span>
                                            <?php endif; ?>
                                        </td>
                                    </tr>
                                <?php endforeach; ?>
                            <?php endif; ?>
                        </tbody>
                    </table>
                </div>
            <?php endif; ?>
        </div>
    <?php endif; ?>
</div>

<?php if ($canManage): ?>
<!-- Modal Setujui & Daftarkan Alumni Baru -->
<div class="modal fade" id="modalApproveAlumni" tabindex="-1" aria-labelledby="modalApproveAlumniLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <form method="post" action="#" id="formApproveAlumni" class="modal-content border-0 rounded-4 shadow">
            <?= csrf_field() ?>
            <div class="modal-header border-bottom px-4 py-3">
                <h2 class="modal-title h6 fw-bold text-dark mb-0" id="modalApproveAlumniLabel">Verifikasi &amp; Daftarkan Alumni Baru</h2>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Tutup"></button>
            </div>
            <div class="modal-body px-4 py-3">
                <p class="small text-dark mb-3">
                    Alumni <strong id="apprAlumniNama"></strong> (Notar: <strong id="apprAlumniNotar" class="ecc-num"></strong>) akan didaftarkan ke <strong>Data Alumni</strong> dan jawabannya disahkan ke statistik Tracer Study.
                </p>
                <div class="p-3 rounded-3 bg-light border">
                    <div class="form-check">
                        <input class="form-check-input" type="checkbox" name="create_account" id="chkCreateAlumniAcc" value="1" checked>
                        <label class="form-check-label small fw-semibold text-dark" for="chkCreateAlumniAcc">
                            Sekaligus buatkan akun login ECC (role: alumni)
                        </label>
                    </div>
                    <div class="ecc-text-muted-aa mt-1" style="font-size: 0.75rem;">
                        Username = Notar (<span id="apprAccUser" class="ecc-num fw-semibold"></span>), Kata Sandi awal = Tanggal Lahir format <code>DDMMYYYY</code> (Tgl Lahir: <span id="apprAccDob" class="ecc-num"></span>).
                    </div>
                </div>
            </div>
            <div class="modal-footer border-top px-4 py-3">
                <button type="button" class="btn btn-light border rounded-pill px-3" data-bs-dismiss="modal">Batal</button>
                <button type="submit" class="btn btn-success rounded-pill px-4 fw-semibold">Setujui &amp; Daftarkan</button>
            </div>
        </form>
    </div>
</div>

<!-- Modal Tolak Alumni Baru -->
<div class="modal fade" id="modalRejectAlumni" tabindex="-1" aria-labelledby="modalRejectAlumniLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <form method="post" action="#" id="formRejectAlumni" class="modal-content border-0 rounded-4 shadow">
            <?= csrf_field() ?>
            <div class="modal-header border-bottom px-4 py-3">
                <h2 class="modal-title h6 fw-bold text-dark mb-0" id="modalRejectAlumniLabel">Tolak Respons Alumni Baru</h2>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Tutup"></button>
            </div>
            <div class="modal-body px-4 py-3">
                <p class="small text-dark mb-2">Respons atas nama <strong id="rejAlumniNama"></strong> akan ditolak dan tidak dimasukkan ke statistik.</p>
                <label for="rejAlasan" class="form-label fw-semibold small">Alasan Penolakan</label>
                <input type="text" id="rejAlasan" name="alasan_penolakan" class="form-control ecc-control-sm" placeholder="Contoh: Data tidak valid / bukan alumni PKTJ">
            </div>
            <div class="modal-footer border-top px-4 py-3">
                <button type="button" class="btn btn-light border rounded-pill px-3" data-bs-dismiss="modal">Batal</button>
                <button type="submit" class="btn btn-danger rounded-pill px-4 fw-semibold">Tolak Respons</button>
            </div>
        </form>
    </div>
</div>
<?php endif; ?>
<?= $this->endSection() ?>

<?= $this->section('scripts') ?>
<script>
function openApproveAlumniModal(btn) {
    var d = btn.dataset;
    document.getElementById('formApproveAlumni').action = d.action || '#';
    document.getElementById('apprAlumniNama').textContent = d.nama || '';
    document.getElementById('apprAlumniNotar').textContent = d.notar || '';
    document.getElementById('apprAccUser').textContent = d.notar || '';
    document.getElementById('apprAccDob').textContent = d.dob || '-';
    var el = document.getElementById('modalApproveAlumni');
    if (window.bootstrap && el) bootstrap.Modal.getOrCreateInstance(el).show();
}
function openRejectAlumniModal(btn) {
    var d = btn.dataset;
    document.getElementById('formRejectAlumni').action = d.action || '#';
    document.getElementById('rejAlumniNama').textContent = d.nama || '';
    document.getElementById('rejAlasan').value = '';
    var el = document.getElementById('modalRejectAlumni');
    if (window.bootstrap && el) bootstrap.Modal.getOrCreateInstance(el).show();
}
</script>
<?= $this->endSection() ?>
