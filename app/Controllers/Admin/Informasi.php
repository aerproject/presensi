<?php

namespace App\Controllers\Admin;

use App\Controllers\BaseController;

class Informasi extends BaseController
{
    protected $IzinModel;

    public function __construct()
    {
       
	}


    // ✅ Tampilkan semua izin
    public function index()
    {
		$data = [
            'title' => 'Infromasi | Admin Panel Absensi Digital'
        ];
        return view('admin/info_view', $data);
    }

    // ✅ Form tambah izin
    public function create()
    {
        $data['userList'] = $this->userModel->findAll();
        return view('admin/izin/create', $data);
    }

    // ✅ Simpan izin baru
    public function store()
    {
        $this->messageModel->insert([
            'users_id'    => $this->request->getPost('users_id'),
            'jenis_pesan' => 'izin',
            'isi_pesan'   => $this->request->getPost('isi_pesan'),
            'waktu_kirim' => $this->request->getPost('waktu_kirim'),
            'status'      => $this->request->getPost('status'),
        ]);

        return redirect()->to('/admin/izin')->with('success', 'Izin berhasil ditambahkan.');
    }

    // ✅ Form edit izin
    public function edit($id)
    {
		dd($id);
        $data['izin']     = $this->messageModel->find($id);
        $data['userList'] = $this->userModel->findAll();
        return view('admin/izin_edit', $data);
    }

    // ✅ Update izin
    public function update($id)
    {
        $this->messageModel->update($id, [
            'users_id'    => $this->request->getPost('users_id'),
            'isi_pesan'   => $this->request->getPost('isi_pesan'),
            'waktu_kirim' => $this->request->getPost('waktu_kirim'),
            'status'      => $this->request->getPost('status'),
        ]);

        return redirect()->to('/admin/izin')->with('success', 'Izin berhasil diperbarui.');
    }
}
