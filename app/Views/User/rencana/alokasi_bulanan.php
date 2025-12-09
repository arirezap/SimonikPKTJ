<?= $this->extend('layouts/main') ?>
    
<?= $this->section('title') ?><?= esc($page_title ?? 'Kelola Rencana') ?><?= $this->endSection() ?>

<?= $this->section('styles') ?>
<style>
    .nav-tabs .nav-link { border-bottom-width: 0; color: #6c757d; }
    .nav-tabs .nav-link.active { background-color: #f8f9fa; border-color: #dee2e6 #dee2e6 #f8f9fa; color: #0d6efd; font-weight: bold; }
    .tab-content { background-color: #f8f9fa; border-radius: 0 0.375rem 0.375rem 0.375rem; }
    input[readonly] { background-color: #e9ecef; cursor: not-allowed; }
    #loadingOverlay {
        display: none; position: fixed; top: 0; left: 0; width: 100%; height: 100%;
        background: rgba(0,0,0,0.5); z-index: 9999; text-align: center; padding-top: 20%; color: white;
    }
</style>
<?= $this->endSection() ?>

<?= $this->section('content') ?>

<div id="loadingOverlay">
    <div class="spinner-border text-light" role="status" style="width: 3rem; height: 3rem;"></div>
    <h3 class="mt-3">Menyimpan Data...</h3>
    <p>Mohon jangan tutup halaman ini.</p>
</div>

<div class="d-flex justify-content-between align-items-center mb-3">
    <h1>Kelola Target & Realisasi Bulanan <?= esc($tahun_terpilih) ?></h1>
    <span class="badge bg-secondary" id="itemCount">Memuat...</span>
</div>

<?php if (session()->getFlashdata('success')): ?>
    <div class="alert alert-success alert-dismissible fade show"><?= session()->getFlashdata('success') ?><button type="button" class="btn-close" data-bs-dismiss="alert"></button></div>
<?php endif; ?>

<div class="card shadow-sm">
    <div class="card-body">
        <form id="formAlokasi">
            <?= csrf_field() ?>
            <input type="hidden" id="tahunInput" value="<?= esc($tahun_terpilih) ?>">

            <?php if(!empty($rencana_kinerja)): ?>
                
                <ul class="nav nav-tabs" id="bulanTab" role="tablist">
                    <?php 
                        $bulan_sekarang = date('n');
                        $tahun_sekarang = date('Y');
                        for ($i=1; $i<=12; $i++): 
                    ?>
                        <li class="nav-item" role="presentation">
                            <button class="nav-link <?= ($i == $bulan_sekarang && $tahun_terpilih == $tahun_sekarang) ? 'active' : '' ?>" id="tab-<?= $i ?>" data-bs-toggle="tab" data-bs-target="#konten-<?= $i ?>" type="button" role="tab">
                                <?= bulan_indo($i) ?>
                            </button>
                        </li>
                    <?php endfor; ?>
                </ul>

                <div class="tab-content p-3 border border-top-0" id="bulanTabContent">
                    <?php for ($i=0; $i<12; $i++): 
                        $bulan_index = $i + 1;
                        $is_future = ($tahun_terpilih > $tahun_sekarang) || ($tahun_terpilih == $tahun_sekarang && $bulan_index > $bulan_sekarang);
                        $ro = $is_future ? 'readonly' : '';
                        $cls = $is_future ? 'bg-light' : '';
                    ?>
                    <div class="tab-pane fade <?= ($bulan_index == $bulan_sekarang && $tahun_terpilih == $tahun_sekarang) ? 'show active' : '' ?>" id="konten-<?= $bulan_index ?>" role="tabpanel">
                        <div class="table-responsive">
                            <table class="table table-hover align-middle table-sm">
                                <thead class="table-light">
                                    <tr>
                                        <th>Indikator Kinerja</th>
                                        <th class="text-center" width="15%">Target</th>
                                        <th class="text-center" width="15%">Realisasi</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php foreach($rencana_kinerja as $row): 
                                        // Decode data dengan aman
                                        $t = is_string($row['target_bulanan']) ? json_decode($row['target_bulanan'], true) : $row['target_bulanan'];
                                        $r = is_string($row['realisasi_bulanan']) ? json_decode($row['realisasi_bulanan'], true) : $row['realisasi_bulanan'];
                                        $t = is_array($t) ? $t : array_fill(0,12,0);
                                        $r = is_array($r) ? $r : array_fill(0,12,null);
                                    ?>
                                    <tr class="data-row" data-id="<?= $row['id'] ?>">
                                        <td>
                                            <div class="fw-bold"><?= esc($row['indikator_kinerja']) ?></div>
                                            <small class="text-muted">Satuan: <?= esc($row['satuan']) ?></small>
                                        </td>
                                        <td>
                                            <input type="number" step="any" class="form-control inp-target form-control-sm" data-month="<?= $i ?>" value="<?= $t[$i] ?? 0 ?>">
                                        </td>
                                        <td>
                                            <input type="number" step="any" class="form-control inp-realisasi form-control-sm <?= $cls ?>" data-month="<?= $i ?>" value="<?= $r[$i] ?? '' ?>" <?= $ro ?>>
                                        </td>
                                    </tr>
                                    <?php endforeach; ?>
                                </tbody>
                            </table>
                        </div>
                    </div>
                    <?php endfor; ?>
                </div>
                
                <div class="d-grid gap-2 d-md-flex justify-content-md-end mt-4">
                    <button type="button" id="btnSimpan" class="btn btn-primary btn-lg px-5"><i class="bi bi-save"></i> Simpan Perubahan</button>
                </div>
            <?php else: ?>
                <div class="text-center p-5 bg-light rounded">
                    <p class="mb-3 text-muted">Belum ada data rencana kerja untuk tahun ini.</p>
                    <a href="<?= site_url('user/rencana/input?tahun='.$tahun_terpilih) ?>" class="btn btn-outline-primary">Buat Rencana Kerja</a>
                </div>
            <?php endif; ?>
        </form>
    </div>
</div>
<?= $this->endSection() ?>

<?= $this->section('scripts') ?>
<script>
document.addEventListener('DOMContentLoaded', function() {
    const btnSimpan = document.getElementById('btnSimpan');
    const loadingOverlay = document.getElementById('loadingOverlay');
    
    // Hitung item unik untuk info
    const uniqueIds = new Set();
    document.querySelectorAll('.data-row[data-id]').forEach(row => uniqueIds.add(row.getAttribute('data-id')));
    document.getElementById('itemCount').innerText = `${uniqueIds.size} Indikator`;

    if (btnSimpan) {
        btnSimpan.addEventListener('click', function() {
            if(!confirm('Apakah Anda yakin ingin menyimpan semua perubahan?')) return;

            loadingOverlay.style.display = 'block';
            btnSimpan.disabled = true;

            const tahun = document.getElementById('tahunInput').value;
            
            // Ambil CSRF Token
            const csrfTokenName = '<?= csrf_token() ?>';
            const csrfHash = document.querySelector('input[name="' + csrfTokenName + '"]').value;

            // Kumpulkan Data
            const collectedData = {};
            document.querySelectorAll('.data-row').forEach(row => {
                const id = row.getAttribute('data-id');
                if (!collectedData[id]) {
                    collectedData[id] = {
                        id: id,
                        target_bulanan: new Array(12).fill(0),
                        realisasi_bulanan: new Array(12).fill(null)
                    };
                }
                const inpTarget = row.querySelector('.inp-target');
                const inpReal = row.querySelector('.inp-realisasi');
                
                if (inpTarget) collectedData[id].target_bulanan[parseInt(inpTarget.dataset.month)] = inpTarget.value;
                if (inpReal) collectedData[id].realisasi_bulanan[parseInt(inpReal.dataset.month)] = inpReal.value;
            });

            // Susun Payload: Masukkan CSRF Token ke dalam BODY JSON (Penting!)
            const payload = {
                [csrfTokenName]: csrfHash, // Token masuk sini
                tahun: tahun,
                items: Object.values(collectedData)
            };

            fetch('<?= site_url('user/alokasi/update') ?>', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-Requested-With': 'XMLHttpRequest'
                },
                body: JSON.stringify(payload)
            })
            .then(async response => {
                // Cek apakah respon JSON valid
                const contentType = response.headers.get("content-type");
                if (contentType && contentType.indexOf("application/json") !== -1) {
                    return response.json();
                } else {
                    // Jika server mengembalikan HTML error page
                    const text = await response.text();
                    throw new Error("Server Error (Not JSON): " + text.substring(0, 100) + "...");
                }
            })
            .then(data => {
                if (data.status === 'success') {
                    alert('Berhasil! Data telah disimpan.');
                    window.location.href = data.redirect;
                } else {
                    throw new Error(data.message || 'Gagal menyimpan.');
                }
            })
            .catch(error => {
                console.error('Error:', error);
                alert('Gagal Menyimpan Data!\n\nDetail Error: ' + error.message);
                loadingOverlay.style.display = 'none';
                btnSimpan.disabled = false;
            });
        });
    }
});
</script>
<?= $this->endSection() ?>