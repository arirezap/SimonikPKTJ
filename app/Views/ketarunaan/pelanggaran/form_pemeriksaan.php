<?= $this->extend('layouts/ecc2_layout') ?>

<?= $this->section('title') ?>Pemeriksaan Kasus<?= $this->endSection() ?>

<?= $this->section('styles') ?>
<style>
    .ecc-pl-card {
        background: #ffffff;
        border: 1px solid #e2e8f0;
        border-radius: 16px;
        padding: 24px;
        box-shadow: 0 2px 8px rgba(15, 23, 42, 0.03);
    }
    @media (max-width: 767.98px) {
        .ecc-pl-card {
            padding: 16px;
        }
    }
    .ecc-icon-48 {
        width: 48px;
        height: 48px;
        border-radius: 12px;
        background-color: #fef2f2;
        color: #dc2626;
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
    .ecc-table-pl thead th {
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
    .ecc-table-pl tbody td {
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
$detail      = $detail ?? [];
$kasus       = $detail['kasus'] ?? [];
$pihak       = $detail['pihak'] ?? [];
$refs        = $refs ?? [];
$tarunaList  = $tarunaList ?? [];
$canViewFull = !empty($canViewFull);
$idKasus     = (int) ($kasus['id'] ?? 0);
$bapFile     = (string) ($kasus['berkas_bap'] ?? '');
?>

<div class="container-fluid px-0">
    <?= view('ketarunaan/pelanggaran/_wizard_header', [
        'activeStep'   => 2,
        'pageTitle'    => 'Pemeriksaan Kasus',
        'pageSubtitle' => 'Mencatat klarifikasi, dasar peraturan, berita acara, dan pihak terkait kasus.',
        'kasus'        => $kasus,
    ]) ?>

    <!-- Kontainer Form Detail Pemeriksaan -->
    <div class="ecc-pl-card mb-4">
        <h6 class="fw-bold text-dark mb-1">Detail Pemeriksaan</h6>
        <p class="text-secondary mb-3" style="font-size: 0.8125rem;">Catat hasil pemeriksaan, klarifikasi pihak terkait, dan berita acara pemeriksaan.</p>

        <form method="post" action="<?= base_url('ketarunaan/pelanggaran/pemeriksaan/' . $idKasus . '/simpan') ?>" enctype="multipart/form-data">
            <?= csrf_field() ?>

            <div class="row g-3">
                <div class="col-12 col-md-4">
                    <label for="inputTglPeriksa" class="form-label fw-semibold text-dark" style="font-size: 0.875rem;">
                        Tanggal Pemeriksaan <span class="text-danger">*</span>
                    </label>
                    <input
                        type="text"
                        id="inputTglPeriksa"
                        name="tanggal_pemeriksaan"
                        class="form-control ecc-control-sm ecc-flatpickr ecc-num"
                        value="<?= esc(old('tanggal_pemeriksaan', (string) ($kasus['tanggal_pemeriksaan'] ?: date('Y-m-d')))) ?>"
                        required
                    >
                </div>

                <div class="col-12 col-md-4">
                    <label for="inputPemeriksa" class="form-label fw-semibold text-dark" style="font-size: 0.875rem;">
                        Pemeriksa <span class="text-danger">*</span>
                    </label>
                    <select id="inputPemeriksa" name="pemeriksa" class="form-select ecc-control-sm" required>
                        <?php $pemVal = old('pemeriksa', (string) ($kasus['pemeriksa'] ?: 'Tim Pemeriksa Unit')); ?>
                        <?php foreach ($refs['pemeriksa_options'] ?? [] as $opt): ?>
                            <option value="<?= esc($opt) ?>" <?= $pemVal === $opt ? 'selected' : '' ?>><?= esc($opt) ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>

                <div class="col-12 col-md-4">
                    <label for="inputTingkatPelanggaran" class="form-label fw-semibold text-dark" style="font-size: 0.875rem;">
                        Tingkat Pelanggaran <span class="text-danger">*</span>
                    </label>
                    <select id="inputTingkatPelanggaran" name="tingkat_pelanggaran" class="form-select ecc-control-sm" required>
                        <?php $tktVal = old('tingkat_pelanggaran', (string) ($kasus['tingkat_pelanggaran'] ?: 'belum_ditetapkan')); ?>
                        <?php foreach ($refs['tingkat_catalog'] ?? [] as $k => $lbl): ?>
                            <option value="<?= esc($k) ?>" <?= $tktVal === $k ? 'selected' : '' ?>><?= esc($lbl) ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>

                <div class="col-12 col-md-6">
                    <label for="inputDasarPeraturan" class="form-label fw-semibold text-dark" style="font-size: 0.875rem;">
                        Dasar Peraturan <span class="text-danger">*</span>
                    </label>
                    <select id="inputDasarPeraturan" name="dasar_peraturan" class="form-select ecc-control-sm" required>
                        <?php $dasarVal = old('dasar_peraturan', (string) ($kasus['dasar_peraturan'] ?: 'Pedoman Tata Tertib Taruna PKTJ')); ?>
                        <?php foreach ($refs['dasar_peraturan_options'] ?? [] as $opt): ?>
                            <option value="<?= esc($opt) ?>" <?= $dasarVal === $opt ? 'selected' : '' ?>><?= esc($opt) ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>

                <div class="col-12 col-md-6">
                    <label for="inputBerkasBap" class="form-label fw-semibold text-dark" style="font-size: 0.875rem;">
                        Berita Acara Pemeriksaan (PDF)
                    </label>
                    <input
                        type="file"
                        id="inputBerkasBap"
                        name="berkas_bap"
                        class="form-control ecc-control-sm"
                        accept=".pdf,application/pdf"
                    >
                    <div class="form-text" style="font-size: 0.75rem;">
                        Format PDF, maksimal 5 MB.
                        <?php if ($bapFile !== ''): ?>
                            Terunggah: <strong><?= esc(basename($bapFile)) ?></strong>
                            <?php if ($canViewFull): ?>
                                (<a href="<?= base_url('ketarunaan/pelanggaran/unduh-dokumen/' . $idKasus . '?file=' . rawurlencode(basename($bapFile)) . '&inline=1') ?>" target="_blank" rel="noopener">Pratinjau</a>)
                            <?php endif; ?>
                        <?php endif; ?>
                    </div>
                </div>

                <div class="col-12 col-md-6">
                    <label for="inputKlarifikasi" class="form-label fw-semibold text-dark" style="font-size: 0.875rem;">
                        Ringkasan Klarifikasi <span class="text-danger">*</span>
                    </label>
                    <textarea
                        id="inputKlarifikasi"
                        name="ringkasan_klarifikasi"
                        class="form-control"
                        rows="4"
                        maxlength="1000"
                        placeholder="Ringkasan keterangan pihak terkait dan bukti yang diperiksa..."
                        required
                    ><?= esc(old('ringkasan_klarifikasi', (string) ($kasus['ringkasan_klarifikasi'] ?? ''))) ?></textarea>
                </div>

                <div class="col-12 col-md-6">
                    <label for="inputHasilPeriksa" class="form-label fw-semibold text-dark" style="font-size: 0.875rem;">
                        Hasil Pemeriksaan <span class="text-danger">*</span>
                    </label>
                    <textarea
                        id="inputHasilPeriksa"
                        name="hasil_pemeriksaan"
                        class="form-control"
                        rows="4"
                        maxlength="1000"
                        placeholder="Hasil pemeriksaan sementara atau kesimpulan tim pemeriksa..."
                        required
                    ><?= esc(old('hasil_pemeriksaan', (string) ($kasus['hasil_pemeriksaan'] ?? ''))) ?></textarea>
                </div>
            </div>

            <div class="mt-3 px-3 py-2 rounded-3" style="background-color: #eff6ff; border: 1px solid #bfdbfe; color: #1e3a8a; font-size: 0.8125rem;">
                Tingkat dan sanksi ditetapkan setelah pemeriksaan sesuai kewenangan yang berlaku. Pencatatan kasus bukan merupakan penetapan sanksi.
            </div>

            <div class="d-flex flex-wrap justify-content-between align-items-center gap-2 pt-3 mt-4 border-top">
                <a href="<?= base_url('ketarunaan/pelanggaran/edit/' . $idKasus) ?>" class="btn btn-light border ecc-control-sm px-3 fw-semibold d-inline-flex align-items-center gap-2">
                    <i class="bi bi-arrow-left"></i> Sebelumnya: Pencatatan
                </a>
                <div class="d-flex flex-wrap gap-2">
                    <button type="submit" name="aksi_submit" value="draf" class="btn btn-outline-secondary ecc-control-sm px-3 fw-semibold">Simpan Draf</button>
                    <button type="submit" name="aksi_submit" value="next" class="btn btn-primary ecc-control-sm px-3 fw-semibold d-inline-flex align-items-center gap-2">
                        Berikutnya: Penetapan <i class="bi bi-arrow-right"></i>
                    </button>
                </div>
            </div>
        </form>
    </div>

    <!-- Kontainer Pihak Terkait Kasus (Taruna maupun Pihak Eksternal) -->
    <div class="ecc-pl-card">
        <div class="d-flex flex-column flex-md-row justify-content-between align-items-md-center gap-2 mb-3">
            <div>
                <h6 class="fw-bold text-dark mb-1 ecc-num">Pihak Terkait Kasus (<?= count($pihak) ?>)</h6>
                <p class="text-secondary mb-0" style="font-size: 0.8125rem;">Catat taruna terkait atau pihak eksternal tanpa memaksakan Nomor Taruna.</p>
            </div>
        </div>

        <form method="post" action="<?= base_url('ketarunaan/pelanggaran/pemeriksaan/' . $idKasus . '/pihak/tambah') ?>" class="border rounded-3 p-3 bg-light mb-4">
            <?= csrf_field() ?>
            <div class="row g-2 align-items-end">
                <div class="col-12 col-md-2">
                    <label for="selectJenisPihak" class="form-label text-secondary fw-semibold mb-1" style="font-size: 0.75rem;">Jenis Pihak</label>
                    <select id="selectJenisPihak" name="jenis_pihak" class="form-select ecc-control-sm">
                        <option value="taruna">Taruna</option>
                        <option value="eksternal">Pihak Eksternal</option>
                    </select>
                </div>

                <div class="col-12 col-md-4" id="blokPihakTaruna">
                    <label for="selectPihakTaruna" class="form-label text-secondary fw-semibold mb-1" style="font-size: 0.75rem;">Pilih Taruna</label>
                    <select id="selectPihakTaruna" name="nomor_taruna" class="form-select ecc-control-sm ecc-num">
                        <option value="">Pilih Taruna</option>
                        <?php foreach ($tarunaList as $idx => $tr): ?>
                            <?php
                            $noTr   = (string) $tr['nomor_taruna'];
                            $dispNo = ketarunaan_mask_nomor_taruna($noTr, $canViewFull);
                            $dispNm = $canViewFull ? (string) $tr['nama_lengkap'] : ('Taruna #' . ($idx + 1));
                            ?>
                            <option value="<?= esc($noTr) ?>"><?= esc($dispNo . ' - ' . $dispNm) ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>

                <div class="col-12 col-md-4 d-none" id="blokPihakEksternal">
                    <label for="inputNamaEksternal" class="form-label text-secondary fw-semibold mb-1" style="font-size: 0.75rem;">Nama Pihak Eksternal</label>
                    <input type="text" id="inputNamaEksternal" name="nama_pihak" class="form-control ecc-control-sm" placeholder="Misal: Petugas Piket / Saksi Luar" maxlength="150">
                </div>

                <div class="col-12 col-md-2">
                    <label for="selectPeranPihak" class="form-label text-secondary fw-semibold mb-1" style="font-size: 0.75rem;">Peran</label>
                    <select id="selectPeranPihak" name="peran" class="form-select ecc-control-sm">
                        <?php foreach ($refs['peran_pihak_options'] ?? [] as $prOpt): ?>
                            <option value="<?= esc($prOpt) ?>"><?= esc($prOpt) ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>

                <div class="col-12 col-md-2">
                    <label for="inputKetPihak" class="form-label text-secondary fw-semibold mb-1" style="font-size: 0.75rem;">Keterangan</label>
                    <input type="text" id="inputKetPihak" name="keterangan" class="form-control ecc-control-sm" placeholder="Opsional" maxlength="255">
                </div>

                <div class="col-12 col-md-2 d-grid">
                    <button type="submit" class="btn btn-primary ecc-control-sm fw-semibold">Tambah Pihak</button>
                </div>
            </div>
        </form>

        <div class="table-responsive">
            <table class="table ecc-table-pl mb-0">
                <thead>
                    <tr>
                        <th>Nama Pihak</th>
                        <th>Nomor Taruna</th>
                        <th>Jenis</th>
                        <th>Peran</th>
                        <th>Keterangan</th>
                        <th class="text-end">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (empty($pihak)): ?>
                        <tr>
                            <td colspan="6" class="text-center text-secondary py-4">Belum ada pihak tercatat pada kasus ini.</td>
                        </tr>
                    <?php else: ?>
                        <?php foreach ($pihak as $p): ?>
                            <tr>
                                <td class="fw-semibold text-dark"><?= esc((string) $p['display_nama']) ?></td>
                                <td class="ecc-num"><?= esc((string) $p['display_nomor']) ?></td>
                                <td><?= ($p['jenis_pihak'] ?? 'taruna') === 'eksternal' ? 'Eksternal' : 'Taruna' ?></td>
                                <td><span class="badge bg-light text-dark border rounded-pill px-3 py-1"><?= esc((string) $p['peran']) ?></span></td>
                                <td><?= esc((string) ($p['keterangan'] ?: '-')) ?></td>
                                <td class="text-end">
                                    <?php $isUtama = (($p['nomor_taruna'] ?? '') === ($kasus['nomor_taruna'] ?? '') && ($p['peran'] ?? '') === 'Terlapor'); ?>
                                    <?php if (!$isUtama): ?>
                                        <form method="post" action="<?= base_url('ketarunaan/pelanggaran/pemeriksaan/' . $idKasus . '/pihak/hapus/' . (int) $p['id']) ?>" class="d-inline form-hapus-pihak">
                                            <?= csrf_field() ?>
                                            <button type="submit" class="btn btn-outline-danger btn-sm rounded-pill px-3" data-nama="<?= esc((string) $p['display_nama']) ?>">Hapus</button>
                                        </form>
                                    <?php else: ?>
                                        <span class="text-secondary" style="font-size: 0.75rem;">Utama</span>
                                    <?php endif; ?>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>
<?= $this->endSection() ?>

<?= $this->section('scripts') ?>
<script>
document.addEventListener('DOMContentLoaded', function() {
    if (typeof flatpickr !== 'undefined') {
        flatpickr('.ecc-flatpickr', {
            dateFormat: 'Y-m-d',
            allowInput: true
        });
    }

    const jenisSelect  = document.getElementById('selectJenisPihak');
    const blokTaruna   = document.getElementById('blokPihakTaruna');
    const blokEksternal = document.getElementById('blokPihakEksternal');
    const selectTaruna = document.getElementById('selectPihakTaruna');
    const inputEkst    = document.getElementById('inputNamaEksternal');

    function syncJenisPihak() {
        if (!jenisSelect || !blokTaruna || !blokEksternal) {
            return;
        }
        const isEkst = jenisSelect.value === 'eksternal';
        blokTaruna.classList.toggle('d-none', isEkst);
        blokEksternal.classList.toggle('d-none', !isEkst);
        if (selectTaruna) {
            selectTaruna.required = !isEkst;
        }
        if (inputEkst) {
            inputEkst.required = isEkst;
        }
    }
    if (jenisSelect) {
        jenisSelect.addEventListener('change', syncJenisPihak);
        syncJenisPihak();
    }

    const inputBap = document.getElementById('inputBerkasBap');
    if (inputBap) {
        inputBap.addEventListener('change', function() {
            const f = this.files && this.files[0];
            if (!f) {
                return;
            }
            const isPdf = f.name.toLowerCase().endsWith('.pdf') || f.type === 'application/pdf';
            if (!isPdf || f.size > 5 * 1024 * 1024) {
                this.value = '';
                const msg = 'Berkas BAP harus berformat PDF maksimal 5 MB.';
                if (typeof Swal !== 'undefined') {
                    Swal.fire({ title: 'Berkas Tidak Sesuai', text: msg, icon: 'warning', confirmButtonText: 'Tutup' });
                } else {
                    alert(msg);
                }
            }
        });
    }

    document.querySelectorAll('.form-hapus-pihak').forEach(function(form) {
        form.addEventListener('submit', function(e) {
            e.preventDefault();
            const btn  = form.querySelector('button[type="submit"]');
            const nama = btn ? (btn.getAttribute('data-nama') || 'pihak ini') : 'pihak ini';
            if (typeof Swal !== 'undefined') {
                Swal.fire({
                    title: 'Hapus Pihak?',
                    text: nama + ' akan dihapus dari daftar pihak kasus.',
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
            } else if (confirm(nama + ' akan dihapus dari daftar pihak kasus.')) {
                form.submit();
            }
        });
    });
});
</script>
<?= $this->endSection() ?>
