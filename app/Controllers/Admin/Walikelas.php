<?php

namespace App\Controllers\Admin;

use App\Controllers\BaseController;
use App\Models\WaliKelasModel;
use App\Models\TapelModel;
use App\Models\KelasModel;
use App\Models\GuruModel;

class Walikelas extends BaseController
{
    public function index()
    {
        $db = \Config\Database::connect();

        $data = [
            'title' => 'Wali Kelas | Admin Panel Absensi Digital',
            'walikelas' => $db->table('walikelas')
                ->select('walikelas.*, guru.nama_guru, kelas.nama_kelas, tapel.tahun_pelajaran')
                ->join('guru', 'guru.id = walikelas.guru_id', 'left')
                ->join('kelas', 'kelas.id = walikelas.kelas_id', 'left')
                ->join('tapel', 'tapel.id = walikelas.tapel_id', 'left')
                ->orderBy('walikelas.id', 'ASC')
                ->get()
                ->getResultArray(),
        ];

        return view('admin/walikelas/index', $data);
    }

    public function create()
    {
        $data = [
            'title' => 'Tambah Wali Kelas',
            'guru'  => (new GuruModel())->findAll(),
            'kelas' => (new KelasModel())->findAll(),
            'tapel' => (new TapelModel())->findAll(),
        ];

        return view('admin/walikelas/created', $data);
    }

    public function store()
    {
        $model = new WaliKelasModel();

        $rules = [
            'guru_id'  => 'required',
            'kelas_id' => 'required',
            'tapel_id' => 'required',
        ];

        if (!$this->validate($rules)) {
            return redirect()
                ->back()
                ->withInput()
                ->with('errors', $this->validator->getErrors());
        }

        $model->insert([
            'guru_id'     => $this->request->getPost('guru_id'),
            'kelas_id'    => $this->request->getPost('kelas_id'),
            'tapel_id'    => $this->request->getPost('tapel_id'),
            'skema_absen' => $this->request->getPost('skema_absen') ?: 'full_day',
            'sesi'        => $this->request->getPost('sesi'),
            'created_at'  => date('Y-m-d H:i:s'),
        ]);

        session()->setFlashdata(
            'success',
            'Data wali kelas berhasil ditambahkan.'
        );

        return redirect()->to('/admin/walikelas');
    }

    public function edit($id)
    {
        $model = new WaliKelasModel();

        $data = [
            'title' => 'Edit Wali Kelas',
            'walas' => $model->find($id),
            'guru'  => (new GuruModel())->findAll(),
            'kelas' => (new KelasModel())->findAll(),
            'tapel' => (new TapelModel())->findAll(),
        ];

        return view('admin/walikelas/edit', $data);
    }

    public function update($id)
    {
        $model = new WaliKelasModel();

        $rules = [
            'guru_id'  => 'required',
            'kelas_id' => 'required',
            'tapel_id' => 'required',
        ];

        if (!$this->validate($rules)) {
            return redirect()
                ->back()
                ->withInput()
                ->with('errors', $this->validator->getErrors());
        }

        $model->update($id, [
            'guru_id'     => $this->request->getPost('guru_id'),
            'kelas_id'    => $this->request->getPost('kelas_id'),
            'tapel_id'    => $this->request->getPost('tapel_id'),
            'skema_absen' => $this->request->getPost('skema_absen') ?: 'full_day',
            'sesi'        => $this->request->getPost('sesi'),
            'updated_at'  => date('Y-m-d H:i:s'),
        ]);

        session()->setFlashdata(
            'success',
            'Data wali kelas berhasil diperbarui.'
        );

        return redirect()->to('/admin/walikelas');
    }

    public function delete($id)
    {
        $model = new WaliKelasModel();
        if ($model->delete($id)) {
            session()->setFlashdata('success', 'Data wali kelas berhasil dihapus.');
        } else {
            session()->setFlashdata('error', 'Gagal menghapus data wali kelas.');
        }
        return redirect()->to('admin/walikelas');
    }
}
