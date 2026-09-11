<?php

namespace App\Controllers\Admin;

use App\Controllers\BaseController;
use App\Models\JurusanModel;

class Jurusan extends BaseController
{
    protected $jurusanModel;

    public function __construct()
    {
        $this->jurusanModel = new JurusanModel();
    }

    public function index()
    {

        if ($response = $this->requireAdmin()) {
            return $response;
        }

        $data['title']   = 'Jurusan | Admin Panel Absensi Digital';
        $data['jurusan'] = $this->jurusanModel->orderBy('id', 'ASC')->findAll();

        return view('admin/jurusan/index', $data);
    }

    public function create()
    {

        if ($response = $this->requireAdmin()) {
            return $response;
        }

        return view('admin/jurusan/created');
    }

    public function store()
	{

        if ($response = $this->requireAdmin()) {
            return $response;
        }

		$rules = [
			'nama_jurusan' => 'required',
			'kode_jurusan' => 'required|is_unique[jurusan.kode_jurusan]',
		];

		if (!$this->validate($rules)) {
			return redirect()->back()
				->withInput()
				->with('errors', $this->validator->getErrors());
		}

		$jurusanModel = new \App\Models\JurusanModel();
		$jurusanModel->insert([
			'nama_jurusan' => $this->request->getPost('nama_jurusan'),
			'kode_jurusan' => $this->request->getPost('kode_jurusan'),
		]);

		session()->setFlashdata('success', 'Data jurusan berhasil disimpan.');
		return redirect()->to('/admin/jurusan');
	}


    public function edit($id)
    {

        if ($response = $this->requireAdmin()) {
            return $response;
        }

        $data['jurusan'] = $this->jurusanModel->find($id);
        return view('admin/jurusan/edit', $data);
    }

    public function update($id)
    {

        if ($response = $this->requireAdmin()) {
            return $response;
        }

        $rules = [
            'nama_jurusan' => 'required',
            'kode_jurusan' => "required|is_unique[jurusan.kode_jurusan,id,{$id}]",
        ];

        if (!$this->validate($rules)) {
            return redirect()->back()
                ->withInput()
                ->with('errors', $this->validator->getErrors());
        }

        $this->jurusanModel->update($id, [
            'nama_jurusan' => $this->request->getPost('nama_jurusan'),
            'kode_jurusan' => $this->request->getPost('kode_jurusan'),
        ]);

        session()->setFlashdata('success', 'Data jurusan berhasil diperbarui.');
        return redirect()->to('/admin/jurusan');
    }

    public function delete($id)
    {

        if ($response = $this->requireAdmin()) {
            return $response;
        }

        if ($this->jurusanModel->delete($id)) {
            session()->setFlashdata('success', 'Data jurusan berhasil dihapus.');
        } else {
            session()->setFlashdata('error', 'Gagal menghapus data jurusan.');
        }

        return redirect()->to('/admin/jurusan');
    }
}
