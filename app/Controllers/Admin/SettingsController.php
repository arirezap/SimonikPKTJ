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
            'enable_log_deadline' => [
                'setting_name'  => 'Status Batas Waktu Laporan Harian',
                'setting_value' => '0',
                'description'   => 'Aktifkan (ON) untuk membatasi input laporan kegiatan harian dengan batas toleransi hari, atau Non-aktifkan (OFF) agar bebas diisi kapanpun (tanggal masa depan tetap dilarang).'
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
            'title'       => 'Pengaturan Sistem',
            'settingsMap' => $settingsMap,
            'isTargetDeadlineActive'    => ($settingsMap['enable_target_deadline']['setting_value'] ?? '0') === '1',
            'isLogDeadlineActive'       => ($settingsMap['enable_log_deadline']['setting_value'] ?? '0') === '1',
            'isPenilaianDeadlineActive' => ($settingsMap['enable_penilaian_deadline']['setting_value'] ?? '0') === '1',
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
            'enable_target_deadline'    => $this->request->getPost('enable_target_deadline') ? '1' : '0',
            'enable_log_deadline'       => $this->request->getPost('enable_log_deadline') ? '1' : '0',
            'enable_penilaian_deadline' => $this->request->getPost('enable_penilaian_deadline') ? '1' : '0',
        ];

        foreach ($toggles as $tKey => $tVal) {
            $settingModel->update($tKey, [
                'setting_value' => $tVal,
                'updated_at'    => date('Y-m-d H:i:s')
            ]);
        }

        // 2. Simpan nilai parameter angka
        $settings = $this->request->getPost('settings');
        if ($settings && is_array($settings)) {
            foreach ($settings as $key => $value) {
                $valClean = max(1, (int)trim($value));
                $settingModel->update($key, [
                    'setting_value' => (string)$valClean,
                    'updated_at'    => date('Y-m-d H:i:s')
                ]);
            }
        }

        log_audit('UPDATE', 'settings', 'individual_deadlines', null, [
            'toggles' => $toggles,
            'values'  => $settings
        ]);
        
        return redirect()->back()->with('success', 'Pengaturan batas waktu berhasil diperbarui.');
    }
}
