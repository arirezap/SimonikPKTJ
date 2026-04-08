<?php

namespace App\Controllers\User;

use App\Controllers\BaseController;
use App\Models\SkpHeaderModel;
use App\Models\SkpModel;
use App\Models\MasterIndikatorModel;
use App\Models\User; // <--- TAMBAHKAN INI AGAR RAPI

class Skp extends BaseController
{
    protected $skpHeaderModel;
    protected $skpModel;

    public function __construct()
    {
        $this->skpHeaderModel = new SkpHeaderModel();
        $this->skpModel       = new SkpModel();
    }

    public function index()
    {
        $userId = session()->get('id');

        $data = [
            'title' => 'Daftar Sasaran Kinerja Pegawai',
            'list_skp' => $this->skpHeaderModel->where('user_id', $userId)
                ->orderBy('tahun', 'DESC')
                ->findAll()
        ];

        return view('User/skp/index', $data);
    }

    public function store()
    {
        $userId = session()->get('id');
        $role   = session()->get('role');
        $tahun  = $this->request->getPost('tahun');
        $modelSkp = $this->request->getPost('model_skp');

        if (!$tahun) {
            return redirect()->back()->with('error', 'Tahun tidak boleh kosong.');
        }

        // 1. Cek Duplikasi
        $exist = $this->skpHeaderModel->where('user_id', $userId)->where('tahun', $tahun)->first();
        if ($exist) {
            return redirect()->back()->with('error', 'Anda sudah membuat SKP untuk tahun ' . $tahun);
        }

        // 2. Cek Role Direktur (Validasi Berjenjang)
        if ($role !== 'direktur') {
            $direkturSudahBuat = $this->skpHeaderModel->isDirekturSkpExists($tahun);
            if (!$direkturSudahBuat) {
                return redirect()->back()->with('error_modal', 'Maaf, Anda belum bisa membuat SKP Tahun ' . $tahun . ' karena Direktur belum membuat SKP.');
            }
        }

        // 3. Simpan
        $this->skpHeaderModel->save([
            'user_id'       => $userId,
            'tahun'         => $tahun,
            'model_skp'     => $modelSkp,
            'periode_awal'  => $tahun . '-01-01',
            'periode_akhir' => $tahun . '-12-31',
            'status'        => 'Draft'
        ]);

        return redirect()->to('/user/skp')->with('success', 'SKP Tahun ' . $tahun . ' (' . $modelSkp . ') berhasil dibuat.');
    }

    public function delete($id)
    {
        $skp = $this->skpHeaderModel->find($id);

        if (!$skp) {
            return redirect()->to('/user/skp')->with('error', 'Data SKP tidak ditemukan.');
        }

        // Security Check: Hanya pemilik yang bisa hapus
        if ($skp['user_id'] != session()->get('id')) {
            return redirect()->to('/user/skp')->with('error', 'Anda tidak memiliki akses untuk menghapus data ini.');
        }

        // Status Check
        if ($skp['status'] !== 'Draft') {
            return redirect()->to('/user/skp')->with('error', 'SKP yang sudah diajukan atau disetujui tidak dapat dihapus.');
        }

        $this->skpHeaderModel->delete($id);

        return redirect()->to('/user/skp')->with('success', 'Data SKP berhasil dihapus.');
    }

    public function detail($id)
    {
        $header = $this->skpHeaderModel->find($id);
        if (!$header) return redirect()->to('/user/skp')->with('error', 'Data tidak ditemukan.');

        // Gunakan use App\Models\User di atas agar lebih bersih
        $userModel = new User(); 
        $pegawai = $userModel->find($header['user_id']);
        
        // Safety check jika user terhapus
        if(!$pegawai) return redirect()->to('/user/skp')->with('error', 'Data Pegawai tidak ditemukan.');

        $rolePemilik = $pegawai['role'];

        $atasan = null;
        if (!empty($pegawai['atasan_id'])) {
            $atasan = $userModel->find($pegawai['atasan_id']);
        }

        $targets = $this->skpModel->where('skp_header_id', $id)->orderBy('jenis', 'ASC')->findAll();

        // Logika Dropdown Direktur
        $masterIndikator = [];
        if ($rolePemilik == 'direktur') {
            $masterModel = new MasterIndikatorModel();
            $masterIndikator = $masterModel->findAll();
        }

        $data = [
            'title'           => 'Detail Sasaran Kinerja Pegawai',
            'header'          => $header,
            'pegawai'         => $pegawai,
            'atasan'          => $atasan,
            'targets'         => $targets,
            'masterIndikator' => $masterIndikator,
            'isDirektur'      => ($rolePemilik == 'direktur')
        ];

        return view('User/skp/detail', $data);
    }

    public function storeTarget()
    {
        $headerId = $this->request->getPost('skp_header_id');
        $userId   = session()->get('id');
        $role     = session()->get('role');

        if (!$headerId) return redirect()->back()->with('error', 'ID Header tidak valid.');

        $data = [
            'skp_header_id'   => $headerId,
            'user_id'         => $userId,
            'jenis'           => $this->request->getPost('jenis'),
            'aspek'           => $this->request->getPost('aspek'),
            'indikator'       => $this->request->getPost('indikator'),
            'target'          => $this->request->getPost('target'),
            'satuan'          => $this->request->getPost('satuan'),
        ];

        // LOGIKA INPUT BERDASARKAN ROLE
        if ($role == 'direktur') {
            $data['rhk_pimpinan']    = null; // Direktur tidak punya intervensi atasan
            $data['rencana_kinerja'] = $this->request->getPost('rencana_kinerja_select'); // Dari Dropdown
        } else {
            $data['rhk_pimpinan']    = $this->request->getPost('rhk_pimpinan'); // Manual
            $data['rencana_kinerja'] = $this->request->getPost('rencana_kinerja_text'); // Manual
        }

        $this->skpModel->save($data);

        return redirect()->back()->with('success', 'Rencana Hasil Kerja berhasil ditambahkan.');
    }
}