<?php

namespace App\Controllers\Admin;

use App\Controllers\BaseController;
use App\Models\SettingModel;

class SettingsController extends BaseController
{
    public function index()
    {
        $settingModel = new SettingModel();
        
        $data = [
            'title' => 'Pengaturan Sistem',
            'settings' => $settingModel->findAll()
        ];
        
        return view('admin/settings/index', $data);
    }

    public function store()
    {
        $settingModel = new SettingModel();
        
        $settings = $this->request->getPost('settings');
        
        if ($settings && is_array($settings)) {
            foreach ($settings as $key => $value) {
                if (trim($value) !== '') {
                    $settingModel->update($key, ['setting_value' => $value]);
                }
            }
            return redirect()->back()->with('success', 'Pengaturan sistem berhasil diperbarui.');
        }
        
        return redirect()->back()->with('error', 'Tidak ada pengaturan yang disimpan.');
    }
}
