<?php

namespace App\Controllers\Admin;

use App\Controllers\BaseController;
use App\Models\TapelModel;

class Tapel extends BaseController
{
    protected $tapelModel;

    public function __construct()
    {
        helper('form'); // untuk validasi & form
        $this->tapelModel = new TapelModel();
    }

    // =========================
    // LIST DATA
    // =========================
    public function index()
    {

        if ($response = $this->requireAdmin()) {
            return $response;
        }

        // Ambil nilai perPage dari query string, default 10
        $perPage = $this->request->getGet('perPage') ?? 10;

        // Ambil data tapel dengan pagination
        $tapel = $this->tapelModel->orderBy('id', 'ASC')->paginate($perPage, 'default');

        // Ambil pager untuk ditampilkan di view
        $pager = $this->tapelModel->pager;

        // Kirim data ke view
        return view('admin/tapel/index', [
            'tapel'   => $tapel,
            'pager'   => $pager,
            'perPage' => $perPage,
        ]);
    }


    // =========================
    // CREATE DATA
    // =========================
    public function create()
    {

        if ($response = $this->requireAdmin()) {
            return $response;
        }

        $rules = [
            'tahun_pelajaran' => 'required|max_length[20]',
            'semester'        => 'required|in_list[Ganjil,Genap]',
            'aktif'           => 'permit_empty|in_list[0,1]'
        ];

        if (!$this->validate($rules)) {
            return redirect()->back()->withInput()->with('errors', $this->validator->getErrors());
        }

        $this->tapelModel->insert([
            'tahun_pelajaran' => $this->request->getPost('tahun_pelajaran'),
            'semester'        => $this->request->getPost('semester'),
            'aktif'           => $this->request->getPost('aktif') ?? 0
        ]);

        return redirect()->to('/admin/tapel')->with('success', 'Tahun Pelajaran berhasil ditambahkan');
    }

    // =========================
    // EDIT FORM
    // =========================
    public function edit($id)
    {

        if ($response = $this->requireAdmin()) {
            return $response;
        }

        $tapel = $this->tapelModel->find($id);

        if (!$tapel) {
            throw new \CodeIgniter\Exceptions\PageNotFoundException("Data Tahun Pelajaran dengan ID $id tidak ditemukan");
        }

        $data['tapel'] = $tapel;
        return view('admin/tapel/edit', $data);
    }

    // =========================
    // UPDATE DATA
    // =========================
    public function update($id)
    {

        if ($response = $this->requireAdmin()) {
            return $response;
        }

        $rules = [
            'tahun_pelajaran' => 'required|max_length[20]',
            'semester'        => 'required|in_list[Ganjil,Genap]'
        ];

        if (!$this->validate($rules)) {
            return redirect()->back()->withInput()->with('errors', $this->validator->getErrors());
        }

        $this->tapelModel->update($id, [
            'tahun_pelajaran' => $this->request->getPost('tahun_pelajaran'),
            'semester'        => $this->request->getPost('semester')
        ]);

        return redirect()->to('/admin/tapel')->with('success', 'Tahun Pelajaran berhasil diperbarui');
    }

    // =========================
    // SET ACTIVE
    // =========================
    public function setActive($id)
    {

        if ($response = $this->requireAdmin()) {
            return $response;
        }

        // Nonaktifkan semua tapel (pakai query builder langsung)
        $db = \Config\Database::connect();
        $db->table('tapel')->update(['aktif' => 0]);

        // Aktifkan tapel yang dipilih
        $this->tapelModel->update($id, ['aktif' => 1]);

        return redirect()->to('/admin/tapel')->with('success', 'Tahun Pelajaran berhasil diaktifkan');
    }
}
