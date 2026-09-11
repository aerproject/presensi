<?php

namespace App\Controllers\Admin;

use App\Controllers\BaseController;
use App\Models\KelasModel;
use App\Models\JurusanModel;

class Kelas extends BaseController
{
    protected $kelasModel;

    public function __construct()
    {
        $this->kelasModel = new KelasModel();
    }

    public function index()
    {

        if ($response = $this->requireAdmin()) {
            return $response;
        }

        $data['title'] = 'Kelas | Admin Panel Absensi Digital';

        // Ambil nilai perPage dari query string, default 10
        $perPage = (int)($this->request->getGet('perPage') ?? 10);

        // Gunakan paginate agar bisa pakai pager
        $kelas = $this->kelasModel
            ->select('kelas.*, jurusan.nama_jurusan')
            ->join('jurusan', 'jurusan.id = kelas.jurusan_id', 'left')
            ->orderBy('kelas.id', 'ASC')
            ->paginate($perPage, 'default'); // gunakan grup 'default'

        $data['kelas']   = $kelas;
        $data['pager']   = $this->kelasModel->pager;
        $data['perPage'] = $perPage;

        return view('admin/kelas/index', $data);
    }


    public function create()
    {

        if ($response = $this->requireAdmin()) {
            return $response;
        }

        $data['jurusan'] = (new JurusanModel())->findAll();
        return view('admin/kelas/created', $data);
    }

    public function store()
    {

        if ($response = $this->requireAdmin()) {
            return $response;
        }

        $rules = [
            'kelas'      => 'required|max_length[5]',
            'nama_kelas' => 'required|max_length[50]',
            'jurusan_id' => 'required|numeric',
        ];

        if (!$this->validate($rules)) {
            return redirect()->back()->withInput()->with('errors', $this->validator->getErrors());
        }

        $this->kelasModel->insert([
            'kelas'      => $this->request->getPost('kelas'),
            'nama_kelas' => $this->request->getPost('nama_kelas'),
            'jurusan_id' => $this->request->getPost('jurusan_id'),
            'created_at'=> date('Y-m-d H:i:s')
        ]);

        session()->setFlashdata('success', 'Data kelas berhasil ditambahkan.');
        return redirect()->to('/admin/kelas');
    }

    public function edit($id)
    {

        if ($response = $this->requireAdmin()) {
            return $response;
        }

        $data['kelas']   = $this->kelasModel->find($id);
        $data['jurusan'] = (new JurusanModel())->findAll();
        return view('admin/kelas/edit', $data);
    }

    public function update($id)
    {

        if ($response = $this->requireAdmin()) {
            return $response;
        }

        $rules = [
            'kelas'      => 'required|max_length[5]',
            'nama_kelas' => 'required|max_length[50]',
            'jurusan_id' => 'required|numeric',
        ];

        if (!$this->validate($rules)) {
            return redirect()->back()->withInput()->with('errors', $this->validator->getErrors());
        }

        $this->kelasModel->update($id, [
            'kelas'      => $this->request->getPost('kelas'),
            'nama_kelas' => $this->request->getPost('nama_kelas'),
            'jurusan_id' => $this->request->getPost('jurusan_id'),
        ]);

        session()->setFlashdata('success', 'Data kelas berhasil diperbarui.');
        return redirect()->to('/admin/kelas');
    }

    public function delete($id)
    {

        if ($response = $this->requireAdmin()) {
            return $response;
        }

        $this->kelasModel->delete($id);
        session()->setFlashdata('success', 'Data kelas berhasil dihapus.');
        return redirect()->to('/admin/kelas');
    }
}
