<?php

namespace App\Controllers\Admin;

use App\Controllers\BaseController;
use App\Models\KelasMappingModel;
use App\Models\KelasModel;

class KelasMapping extends BaseController
{
    protected $kelasMappingModel;
    protected $kelasModel;

    public function __construct()
    {
        $this->kelasMappingModel = new KelasMappingModel();
        $this->kelasModel        = new KelasModel();
    }

    // Halaman index: daftar mapping
public function index()
{

    if ($response = $this->requireAdmin()) {
        return $response;
    }

    // Ambil pilihan per page dari query string (?perPage=25), default 10
    $perPage = $this->request->getGet('perPage') ?? 10;

    // Ambil data dengan paginate
    $data['title']   = 'Mapping Kelas';
    $data['mapping'] = $this->kelasMappingModel
        ->select('kelas_mapping.*, k1.nama_kelas as kelas_lama, k2.nama_kelas as kelas_baru')
        ->join('kelas as k1', 'k1.id = kelas_mapping.kelas_id')
        ->join('kelas as k2', 'k2.id = kelas_mapping.next_kelas_id', 'left')
        ->orderBy('kelas_mapping.id', 'ASC')
        ->paginate($perPage);

    // Kirim pager ke view
    $data['pager']   = $this->kelasMappingModel->pager;
    $data['perPage'] = $perPage;

    return view('admin/mapping/index', $data);
}


    // Form create
    public function create()
    {

        if ($response = $this->requireAdmin()) {
            return $response;
        }

        $data['title'] = 'Tambah Mapping Kelas';
        $data['kelas'] = $this->kelasModel->findAll();
        return view('admin/mapping/created', $data);
    }

    // Simpan create
    public function store()
    {

        if ($response = $this->requireAdmin()) {
            return $response;
        }

        $kelasId     = $this->request->getPost('kelas_id');
        $nextKelasId = $this->request->getPost('next_kelas_id');

        $this->kelasMappingModel->insert([
            'kelas_id'      => $kelasId,
            'next_kelas_id' => $nextKelasId ?: null
        ]);

        return redirect()->to(base_url('admin/mapping'))
                         ->with('success', 'Mapping kelas berhasil ditambahkan');
    }

    // Edit
    public function edit($id)
    {

        if ($response = $this->requireAdmin()) {
            return $response;
        }

        $data['title'] = 'Edit Mapping Kelas';
        $data['kelas'] = $this->kelasModel->findAll();
        $data['mapping'] = $this->kelasMappingModel->find($id);

        return view('admin/mapping/edit', $data);
    }

    // Update
    public function update($id)
    {

        if ($response = $this->requireAdmin()) {
            return $response;
        }

        $kelasId     = $this->request->getPost('kelas_id');
        $nextKelasId = $this->request->getPost('next_kelas_id');

        $this->kelasMappingModel->update($id, [
            'kelas_id'      => $kelasId,
            'next_kelas_id' => $nextKelasId ?: null
        ]);

        return redirect()->to(base_url('admin/mapping'))
                         ->with('success', 'Mapping kelas berhasil diperbarui');
    }

    // Delete
    public function delete($id)
    {

        if ($response = $this->requireAdmin()) {
            return $response;
        }

        $this->kelasMappingModel->delete($id);
        return redirect()->to(base_url('admin/mapping'))
                         ->with('success', 'Mapping kelas berhasil dihapus');
    }
}
