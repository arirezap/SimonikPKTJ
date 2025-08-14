<?= $this->extend('layouts/main') ?>

<?= $this->section('title') ?><?= esc($page_title ?? 'Input Rencana Kerja') ?><?= $this->endSection() ?>

<?= $this->section('content') ?>
<h1>Input & Kelola Rencana Kerja Tahunan</h1>

<div class="card">
    <div class="card-body">

        <form method="GET" action="<?= site_url('user/rencana/input') ?>" id="filterTahunForm" class="mb-4">
            <div class="row">
                <div class="col-md-3">
                    <label for="tahun_anggaran_filter" class="form-label fw-bold">Pilih Tahun Perencanaan</label>
                    <select name="tahun" id="tahun_anggaran_filter" class="form-select" onchange="this.form.submit()">
                        <?php
                        // Logika untuk menampilkan tahun yang bisa dipilih
                        $tahun_sekarang = date("Y");
                        $daftar_tahun_opsi = [];
                        // Ambil tahun yang sudah ada datanya
                        if (isset($existing_years_json)) {
                            $daftar_tahun_opsi = json_decode($existing_years_json);
                        }
                        // Tambahkan tahun sekarang dan 5 tahun ke depan
                        for ($i = $tahun_sekarang; $i <= $tahun_sekarang + 5; $i++) {
                            $daftar_tahun_opsi[] = (string)$i;
                        }
                        // Buat unik dan urutkan
                        $daftar_tahun_opsi = array_unique($daftar_tahun_opsi);
                        rsort($daftar_tahun_opsi);
                        ?>
                        <?php foreach ($daftar_tahun_opsi as $tahun_opsi): ?>
                            <option value="<?= $tahun_opsi; ?>" <?= ($tahun_terpilih == $tahun_opsi) ? 'selected' : ''; ?>><?= $tahun_opsi; ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>
            </div>
        </form>

        <form action="<?= site_url('user/rencana/store') ?>" method="POST" id="formRencana">
            <?= csrf_field() ?>
            <input type="hidden" name="tahun_anggaran" value="<?= esc($tahun_terpilih) ?>">

            <div class="table-responsive">
                <table class="table table-bordered" id="tabelRencana">
                    <thead class="table-light">
                        <tr>
                            <th style="width: 5%;">No</th>
                            <th>Sasaran Program/Kegiatan <span class="text-danger">*</span></th>
                            <th>Indikator Kinerja <span class="text-danger">*</span></th>
                            <th style="width: 10%;">Satuan <span class="text-danger">*</span></th>
                            <th style="width: 15%;">Target Tahunan <span class="text-danger">*</span></th>
                            <th>Kegiatan</th>
                            <th style="width: 5%;">Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if (!empty($rencana_kinerja)): ?>
                            <?php foreach ($rencana_kinerja as $index => $row): ?>
                                <tr>
                                    <input type="hidden" name="rencana_id[]" value="<?= esc($row['id']) ?>">
                                    <td class="nomor-baris text-center"><?= $index + 1 ?></td>
                                    <td><textarea name="sasaran_program[]" class="form-control" rows="2" required><?= esc($row['sasaran_program']) ?></textarea></td>
                                    <td><textarea name="indikator_kinerja[]" class="form-control" rows="2" required><?= esc($row['indikator_kinerja']) ?></textarea></td>
                                    <td><input type="text" name="satuan[]" class="form-control" value="<?= esc($row['satuan']) ?>" required></td>
                                    <td><input type="number" step="any" name="target_utama[]" class="form-control" value="<?= esc($row['target_utama']) ?>" required></td>
                                    <td><textarea name="kegiatan[]" class="form-control" rows="2"><?= esc($row['kegiatan']) ?></textarea></td>
                                    <td class="text-center"><button type="button" class="btn btn-danger btn-sm hapus-baris"><i class="bi bi-trash"></i></button></td>
                                </tr>
                            <?php endforeach; ?>
                        <?php else: ?>
                            <tr>
                                <input type="hidden" name="rencana_id[]" value="">
                                <td class="nomor-baris text-center">1</td>
                                <td><textarea name="sasaran_program[]" class="form-control" rows="2" required></textarea></td>
                                <td><textarea name="indikator_kinerja[]" class="form-control" rows="2" required></textarea></td>
                                <td><input type="text" name="satuan[]" class="form-control" required></td>
                                <td><input type="number" step="any" name="target_utama[]" class="form-control" required></td>
                                <td><textarea name="kegiatan[]" class="form-control" rows="2"></textarea></td>
                                <td class="text-center"><button type="button" class="btn btn-danger btn-sm hapus-baris"><i class="bi bi-trash"></i></button></td>
                            </tr>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
            <div class="d-flex justify-content-between">
                <button type="button" id="tambahBaris" class="btn btn-success"><i class="bi bi-plus-circle"></i> Tambah Rencana</button>
                <button type="submit" id="tombolSimpan" class="btn btn-primary">Simpan & Lanjut ke Alokasi Bulanan <i class="bi bi-arrow-right"></i></button>
            </div>
        </form>
    </div>
</div>
<?= $this->endSection() ?>

<?= $this->section('scripts') ?>
<script>
    document.addEventListener('DOMContentLoaded', function() {
        // Ambil elemen tabel dan tombol
        const tabelRencana = document.getElementById('tabelRencana').getElementsByTagName('tbody')[0];
        const tambahBarisBtn = document.getElementById('tambahBaris');

        // Fungsi untuk memperbarui nomor urut baris
        function updateRowNumbers() {
            const rows = tabelRencana.getElementsByTagName('tr');
            for (let i = 0; i < rows.length; i++) {
                rows[i].querySelector('.nomor-baris').textContent = i + 1;
            }
        }

        // Fungsi saat tombol "Tambah Rencana" diklik
        tambahBarisBtn.addEventListener('click', function() {
            // Hentikan jika tidak ada baris sama sekali untuk di-clone
            if (tabelRencana.rows.length === 0) {
                // Logika ini bisa diperluas untuk membuat baris dari string HTML jika diperlukan
                alert('Tidak ada baris template untuk disalin.');
                return;
            }

            // Clone baris pertama sebagai template
            const newRow = tabelRencana.rows[0].cloneNode(true);

            // Pastikan input hidden untuk ID dikosongkan agar dianggap data baru
            const hiddenInput = newRow.querySelector('input[name="rencana_id[]"]');
            if (hiddenInput) {
                hiddenInput.value = '';
            }

            // Kosongkan nilai semua input dan textarea di baris baru
            newRow.querySelectorAll('input[type="text"], input[type="number"], textarea').forEach(input => {
                input.value = '';
            });

            // Tambahkan baris baru ke tabel
            tabelRencana.appendChild(newRow);
            // Perbarui nomor urut
            updateRowNumbers();
        });

        // Fungsi saat tombol "Hapus" di salah satu baris diklik
        tabelRencana.addEventListener('click', function(e) {
            // Cari tombol hapus yang diklik
            const btn = e.target.closest('.hapus-baris');
            if (btn) {
                // Hanya hapus jika baris lebih dari satu
                if (tabelRencana.rows.length > 1) {
                    const rowToRemove = btn.closest('tr');
                    rowToRemove.remove();
                    updateRowNumbers();
                } else {
                    alert('Minimal harus ada satu baris rencana.');
                }
            }
        });
    });
</script>
<?= $this->endSection() ?>