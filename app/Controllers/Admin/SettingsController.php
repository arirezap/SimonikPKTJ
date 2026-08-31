<?php

namespace App\Controllers\Admin;

use App\Controllers\BaseController;
use App\Models\SettingModel;

class SettingsController extends BaseController
{
    private function ensureDefaultSettings(SettingModel $settingModel)
    {
        $defaultSettings = [
            'enable_target_deadline' => [
                'setting_name'  => 'Status Batas Waktu Target Bulanan',
                'setting_value' => '0',
                'description'   => 'Aktifkan (ON) untuk membatasi pengisian target bulanan hingga tanggal tertentu, atau Non-aktifkan (OFF) agar bebas diisi kapanpun.'
            ],
            'batas_input_target' => [
                'setting_name'  => 'Batas Tanggal Pengisian Target Bulanan',
                'setting_value' => '5',
                'description'   => 'Tanggal maksimal di bulan berjalan untuk menyusun dan mengirimkan target kinerja bulanan (Contoh: Tanggal 5).'
            ],
            'enable_monthly_log_deadline' => [
                'setting_name'  => 'Status Kunci Laporan Bulan Lalu',
                'setting_value' => '1',
                'description'   => 'Aktifkan (ON) untuk mengunci pengisian seluruh tanggal di bulan-bulan sebelumnya setelah melewati akhir bulan (plus toleransi hari), atau Non-aktifkan (OFF) agar bulan lalu tetap bebas diisi.'
            ],
            'toleransi_hari_bulan_lalu' => [
                'setting_name'  => 'Toleransi Hari Pengisian Bulan Lalu',
                'setting_value' => '0',
                'description'   => 'Jumlah hari toleransi tambahan setelah tanggal terakhir di bulan tersebut sebelum pengisian dikunci secara permanen (Contoh: 0 hari = terkunci tepat tanggal 1 bulan berikutnya).'
            ],
            'enable_log_deadline' => [
                'setting_name'  => 'Status Batas Waktu Harian Laporan',
                'setting_value' => '0',
                'description'   => 'Aktifkan (ON) untuk membatasi input laporan kegiatan harian dengan toleransi hari per tanggal kegiatan, atau Non-aktifkan (OFF) agar bebas diisi kapanpun.'
            ],
            'batas_input_log' => [
                'setting_name'  => 'Toleransi Hari Pelaporan Kegiatan Harian',
                'setting_value' => '3',
                'description'   => 'Jumlah toleransi hari maksimal setelah tanggal kegiatan untuk menginput laporan kegiatan harian (Contoh: 3 hari).'
            ],
            'enable_penilaian_deadline' => [
                'setting_name'  => 'Status Batas Waktu Penilaian Kinerja',
                'setting_value' => '0',
                'description'   => 'Aktifkan (ON) untuk membatasi penilaian kinerja oleh atasan hingga tanggal tertentu di bulan berikutnya, atau Non-aktifkan (OFF) agar bebas dinilai kapanpun.'
            ],
            'batas_penilaian_kinerja' => [
                'setting_name'  => 'Batas Tanggal Penilaian Kinerja oleh Atasan',
                'setting_value' => '10',
                'description'   => 'Tanggal maksimal di bulan berikutnya bagi atasan langsung untuk memberikan nilai capaian kinerja staf (Contoh: Tanggal 10).'
            ],
            'enable_maintenance_mode' => [
                'setting_name'  => 'Status Mode Pemeliharaan (Maintenance Mode)',
                'setting_value' => '0',
                'description'   => 'Aktifkan (ON) untuk mengalihkan seluruh pengguna selain Administrator ke halaman pemeliharaan sementara saat pembaruan berlangsung.'
            ],
            'maintenance_message' => [
                'setting_name'  => 'Pesan Pemeliharaan Sistem',
                'setting_value' => 'Sistem sedang melakukan sinkronisasi pembaruan performa dan peningkatan fitur terbaru. Layanan akan kembali normal dalam beberapa saat.',
                'description'   => 'Pesan kustom yang ditampilkan kepada pengguna saat mode pemeliharaan aktif.'
            ]
        ];

        foreach ($defaultSettings as $key => $data) {
            if (!$settingModel->find($key)) {
                $settingModel->insert([
                    'setting_key'   => $key,
                    'setting_name'  => $data['setting_name'],
                    'setting_value' => $data['setting_value'],
                    'description'   => $data['description'],
                    'created_at'    => date('Y-m-d H:i:s'),
                    'updated_at'    => date('Y-m-d H:i:s'),
                ]);
            }
        }
    }

    public function index()
    {
        if (!hasRole('admin')) {
            return redirect()->to('dashboard')->with('error', 'Akses ditolak. Hanya administrator yang dapat mengubah pengaturan sistem.');
        }

        $settingModel = new SettingModel();
        $this->ensureDefaultSettings($settingModel);
        
        $settingsRaw = $settingModel->findAll();
        $settingsMap = [];
        foreach ($settingsRaw as $s) {
            $settingsMap[$s['setting_key']] = $s;
        }

        $data = [
            'title'                      => 'Pengaturan Sistem',
            'settingsMap'                => $settingsMap,
            'isTargetDeadlineActive'     => ($settingsMap['enable_target_deadline']['setting_value'] ?? '0') === '1',
            'isMonthlyLogDeadlineActive' => ($settingsMap['enable_monthly_log_deadline']['setting_value'] ?? '1') === '1',
            'isLogDeadlineActive'        => ($settingsMap['enable_log_deadline']['setting_value'] ?? '0') === '1',
            'isPenilaianDeadlineActive'  => ($settingsMap['enable_penilaian_deadline']['setting_value'] ?? '0') === '1',
            'isMaintenanceActive'        => ($settingsMap['enable_maintenance_mode']['setting_value'] ?? '0') === '1',
            'maintenanceMessage'         => $settingsMap['maintenance_message']['setting_value'] ?? 'Sistem sedang melakukan sinkronisasi pembaruan performa dan peningkatan fitur terbaru. Layanan akan kembali normal dalam beberapa saat.',
        ];
        
        return view('admin/settings/index', $data);
    }

    public function store()
    {
        if (!hasRole('admin')) {
            return redirect()->to('dashboard')->with('error', 'Akses ditolak.');
        }

        $settingModel = new SettingModel();
        $this->ensureDefaultSettings($settingModel);
        
        // 1. Simpan status masing-masing saklar toggle (1 per 1)
        $toggles = [
            'enable_target_deadline'      => $this->request->getPost('enable_target_deadline') ? '1' : '0',
            'enable_monthly_log_deadline' => $this->request->getPost('enable_monthly_log_deadline') ? '1' : '0',
            'enable_log_deadline'         => $this->request->getPost('enable_log_deadline') ? '1' : '0',
            'enable_penilaian_deadline'   => $this->request->getPost('enable_penilaian_deadline') ? '1' : '0',
            'enable_maintenance_mode'     => $this->request->getPost('enable_maintenance_mode') ? '1' : '0',
        ];

        foreach ($toggles as $tKey => $tVal) {
            $settingModel->update($tKey, [
                'setting_value' => $tVal,
                'updated_at'    => date('Y-m-d H:i:s')
            ]);
        }

        // 2. Simpan nilai parameter angka dan teks dengan batasan (clamping) valid
        $settings = $this->request->getPost('settings');
        if ($settings && is_array($settings)) {
            foreach ($settings as $key => $value) {
                if ($key === 'maintenance_message') {
                    $valClean = trim((string)$value);
                } else {
                    $rawVal = (int)trim((string)$value);
                    if (in_array($key, ['batas_input_target', 'batas_penilaian_kinerja'])) {
                        $valClean = (string)min(31, max(1, $rawVal));
                    } elseif ($key === 'toleransi_hari_bulan_lalu') {
                        $valClean = (string)min(30, max(0, $rawVal)); // Min 0 hari, Max 30 hari
                    } elseif ($key === 'batas_input_log') {
                        $valClean = (string)min(60, max(1, $rawVal));
                    } else {
                        $valClean = (string)max(0, $rawVal);
                    }
                }

                $settingModel->update($key, [
                    'setting_value' => (string)$valClean,
                    'updated_at'    => date('Y-m-d H:i:s')
                ]);
            }
        }

        log_audit('UPDATE', 'settings', 'system_and_deadlines', null, [
            'toggles' => $toggles,
            'values'  => $settings
        ]);
        
        $msg = $toggles['enable_maintenance_mode'] === '1'
            ? 'Pengaturan berhasil diperbarui. MODE PEMELIHARAAN AKTIF untuk seluruh pengguna non-admin.'
            : 'Pengaturan sistem berhasil diperbarui.';

        return redirect()->back()->with('success', $msg);
    }
}
