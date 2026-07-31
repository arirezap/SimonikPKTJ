<?= $this->extend('layouts/main') ?>

<?= $this->section('title') ?>Panduan Penggunaan<?= $this->endSection() ?>

<?= $this->section('page_title') ?>
Panduan Penggunaan ECC Laporan Kinerja
<?= $this->endSection() ?>

<?= $this->section('styles') ?>
<style>
    .step-number {
        display: inline-flex;
        align-items: center;
        justify-content: center;
        width: 32px;
        height: 32px;
        border-radius: 50%;
        background-color: #0d6efd;
        color: white;
        font-weight: bold;
        margin-right: 12px;
        font-size: 14px;
    }
    .step-title {
        font-weight: 600;
        font-size: 1.1rem;
        color: #333;
        margin-bottom: 0.5rem;
        display: flex;
        align-items: center;
    }
    .step-content {
        padding-left: 44px;
        margin-bottom: 1.5rem;
        color: #555;
    }
    .card-header-professional {
        background-color: #f8f9fa;
        border-bottom: 2px solid #dee2e6;
        font-weight: bold;
        color: #212529;
    }
</style>
<?= $this->endSection() ?>

<?= $this->section('content') ?>

<div class="row">
    <div class="col-lg-10 mx-auto">
        <div class="card shadow-sm border-0 mb-4">
            <div class="card-header card-header-professional py-3">
                <i class="bi bi-book-half me-2"></i> Petunjuk Penggunaan ECC Laporan Kinerja
            </div>
            <div class="card-body p-4">
                <p class="text-muted mb-4">
                    Dokumen ini berisi panduan standar operasional untuk pelaporan dan penilaian kinerja bulanan pada aplikasi ECC Laporan Kinerja. Proses pelaporan kinerja terdiri dari tiga tahapan utama: penyusunan target, pelaporan kegiatan harian, dan proses penilaian oleh atasan.
                </p>

                <div class="accordion" id="accordionPanduan">
                    
                    <!-- Tahap 1: Target Bulanan -->
                    <div class="accordion-item border-0 border-bottom mb-2">
                        <h2 class="accordion-header" id="headingSatu">
                            <button class="accordion-button fw-bold bg-white text-dark" type="button" data-bs-toggle="collapse" data-bs-target="#collapseSatu" aria-expanded="true" aria-controls="collapseSatu">
                                1. Penyusunan Target Kinerja Bulanan (RHK)
                            </button>
                        </h2>
                        <div id="collapseSatu" class="accordion-collapse collapse show" aria-labelledby="headingSatu" data-bs-parent="#accordionPanduan">
                            <div class="accordion-body">
                                <p>Tahap pertama yang wajib dilakukan oleh setiap pegawai pada awal bulan adalah menyusun Target Kinerja Bulanan (Rencana Hasil Kerja / RHK).</p>
                                
                                <div class="step-title"><span class="step-number">A</span> Akses Menu Target Bulanan</div>
                                <div class="step-content">
                                    Navigasikan kursor ke menu <strong>Target Kinerja Bulanan</strong> pada bilah sisi (sidebar). Sistem akan menampilkan formulir kosong jika Anda belum membuat target pada bulan berjalan.
                                </div>

                                <div class="step-title"><span class="step-number">B</span> Pengisian Formulir Target</div>
                                <div class="step-content">
                                    Isi <strong>Indikator Kinerja</strong>, <strong>Target Kuantitatif (Angka)</strong>, dan <strong>Satuan</strong> (misalnya: Dokumen, Laporan, Kegiatan). Anda dapat menambahkan lebih dari satu target dengan menekan tombol <strong>Tambah Target Lain</strong>.
                                </div>

                                <div class="step-title"><span class="step-number">C</span> Pengajuan ke Atasan</div>
                                <div class="step-content">
                                    Setelah seluruh target diisi, klik tombol <strong>Simpan & Ajukan</strong>. Status target akan berubah menjadi "Menunggu Persetujuan". Anda belum dapat mengisi laporan harian sebelum atasan Anda menyetujui target ini.
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Tahap 2: Laporan Harian -->
                    <div class="accordion-item border-0 border-bottom mb-2">
                        <h2 class="accordion-header" id="headingDua">
                            <button class="accordion-button collapsed fw-bold bg-white text-dark" type="button" data-bs-toggle="collapse" data-bs-target="#collapseDua" aria-expanded="false" aria-controls="collapseDua">
                                2. Pelaporan Kegiatan Harian
                            </button>
                        </h2>
                        <div id="collapseDua" class="accordion-collapse collapse" aria-labelledby="headingDua" data-bs-parent="#accordionPanduan">
                            <div class="accordion-body">
                                <p>Setelah target disetujui, pegawai diwajibkan untuk melaporkan realisasi kegiatan yang dilakukan setiap harinya.</p>

                                <div class="step-title"><span class="step-number">A</span> Akses Menu Lapor Kegiatan Harian</div>
                                <div class="step-content">
                                    Buka menu <strong>Lapor Kegiatan Harian</strong>. Pilih tanggal pelaksanaan kegiatan pada kolom yang disediakan.
                                </div>

                                <div class="step-title"><span class="step-number">B</span> Mengisi Detail Kegiatan Utama</div>
                                <div class="step-content">
                                    Pilih RHK/Target yang berkaitan dari daftar pilihan turun (dropdown). Isi deskripsi kegiatan harian, realisasi angka capaian hari itu, dan tautan (URL) bukti pekerjaan (misalnya tautan Google Drive).
                                </div>

                                <div class="step-title"><span class="step-number">C</span> Mengisi Tugas Tambahan (Opsional)</div>
                                <div class="step-content">
                                    Jika Anda melaksanakan tugas di luar dari RHK yang ditetapkan (misalnya menjadi panitia acara insidental), Anda dapat melaporkannya pada formulir <strong>Lapor Tugas Tambahan</strong> yang terletak di bawah formulir kegiatan utama.
                                </div>

                                <div class="step-title"><span class="step-number">D</span> Penyimpanan dan Penguncian</div>
                                <div class="step-content">
                                    Klik <strong>Simpan & Kirim</strong> untuk menyetor laporan. Setelah laporan terkirim, data kegiatan untuk hari tersebut akan dikunci dan tidak dapat diubah kembali.
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Tahap 3: Rekap & Penilaian Kinerja -->
                    <div class="accordion-item border-0 mb-2">
                        <h2 class="accordion-header" id="headingTiga">
                            <button class="accordion-button collapsed fw-bold bg-white text-dark" type="button" data-bs-toggle="collapse" data-bs-target="#collapseTiga" aria-expanded="false" aria-controls="collapseTiga">
                                3. Rekap, Evaluasi, dan Penilaian Kinerja
                            </button>
                        </h2>
                        <div id="collapseTiga" class="accordion-collapse collapse" aria-labelledby="headingTiga" data-bs-parent="#accordionPanduan">
                            <div class="accordion-body">
                                <p>Menu <strong>Rekap & Penilaian Kinerja</strong> menyediakan dua tampilan utama yang disesuaikan dengan peran pengguna dalam organisasi:</p>

                                <div class="card border border-primary-subtle bg-light-subtle mb-4 p-3 rounded-3 shadow-sm">
                                    <h6 class="fw-bold text-primary mb-2"><i class="bi bi-person-lines-fill me-2"></i> A. Tab "Target Bulanan Saya" (Untuk Seluruh Pegawai & Atasan)</h6>
                                    <p class="small text-muted mb-2">Semua pegawai (staf biasa maupun atasan) dapat mengakses tab ini untuk memantau capaian dan nilai kinerjanya sendiri.</p>
                                    <ul class="small mb-0 text-secondary ps-3">
                                        <li class="mb-1"><strong>Melihat Target & Realisasi:</strong> Memantau perbandingan target bulanan (RHK) vs total realisasi harian beserta indikator selisih <em>(gap)</em>.</li>
                                        <li class="mb-1"><strong>Melihat Nilai Kinerja Resmi:</strong> Nilai Capaian RHK, Nilai Tugas Tambahan, dan Rata-rata Nilai Kinerja Bulanan akan tampil setelah resmi <strong>diterbitkan oleh atasan langsung</strong>.</li>
                                        <li class="mb-0"><strong>Catatan Bagi Atasan Langsung:</strong> Atasan langsung yang memiliki staf tetap memiliki tab ini untuk memantau kinerja pribadinya sendiri, karena atasan tersebut juga melaporkan kegiatan harian kepada atasan di tingkat lebih atas.</li>
                                    </ul>
                                </div>

                                <div class="card border border-success-subtle bg-light-subtle mb-3 p-3 rounded-3 shadow-sm">
                                    <h6 class="fw-bold text-success mb-2"><i class="bi bi-people-fill me-2"></i> B. Tab "Penilaian Staf" (Khusus Atasan Langsung)</h6>
                                    <p class="small text-muted mb-2">Tab khusus ini otomatis aktif bagi pegawai yang memiliki bawahan langsung untuk melakukan evaluasi bulanan.</p>

                                    <div class="step-title mt-2"><span class="step-number" style="background-color:#198754;">1</span> Pemilihan Staf & Filter Bulan</div>
                                    <div class="step-content">
                                        Pilih nama staf dari dropdown pencarian dan sesuaikan periode bulan/tahun yang ingin dinilai.
                                    </div>

                                    <div class="step-title"><span class="step-number" style="background-color:#198754;">2</span> Verifikasi Realisasi & Bukti Pekerjaan</div>
                                    <div class="step-content">
                                        Atasan mencermati selisih angka target vs realisasi, serta dapat mengklik tautan bukti pekerjaan pada tabel <strong>Bukti & Activity Log Laporan Harian Staf</strong> di bagian bawah.
                                    </div>

                                    <div class="step-title"><span class="step-number" style="background-color:#198754;">3</span> Pengisian Nilai Capaian RHK & Tugas Tambahan</div>
                                    <div class="step-content">
                                        Atasan memasukkan skor nilai capaian RHK (0 - 150%) per indikator, serta memasukkan 1 (satu) nilai akumulasi tugas tambahan (0 - 100%) untuk seluruh tugas tambahan staf pada bulan tersebut. Indikator predikat dan skor rata-rata kinerja bulanan di bagian paling bawah akan terhitung secara otomatis.
                                    </div>

                                    <div class="step-title"><span class="step-number" style="background-color:#198754;">4</span> Simpan Sementara vs Simpan Penilaian Staf</div>
                                    <div class="step-content mb-0">
                                        <ul>
                                            <li><strong>Simpan Sementara:</strong> Menyimpan draf isian penilaian pimpinan. Nilai draf ini <em>belum dipublikasikan</em> dan <em>belum dapat dilihat oleh staf</em>.</li>
                                            <li><strong>Simpan Penilaian Staf:</strong> Menerbitkan penilaian secara resmi. Staf akan menerima notifikasi dan nilai kinerja resminya dapat dilihat pada tab "Target Bulanan Saya".</li>
                                        </ul>
                                    </div>
                                </div>

                            </div>
                        </div>
                    </div>

                </div> <!-- End Accordion -->
            </div>
        </div>
    </div>
</div>

<?= $this->endSection() ?>
