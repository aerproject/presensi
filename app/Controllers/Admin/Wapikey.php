<?php

namespace App\Controllers\Admin;

use App\Controllers\BaseController;
use App\Models\WapikeyModel;

class Wapikey extends BaseController
{
    /**
     * Tampilkan daftar provider WA API
     */
    public function index()
    {

        if ($response = $this->requireAdmin()) {
            return $response;
        }

        $model = new WapikeyModel();
        $providers = $model->findAll();

        return view('admin/api/wapikey_view', ['providers' => $providers]);
    }

    /**
     * Form tambah provider baru
     */
    public function create()
    {

        if ($response = $this->requireAdmin()) {
            return $response;
        }

        return view('admin/api/wapikey_created');
    }

    /**
     * Simpan provider baru
     */
    public function store()
    {

        if ($response = $this->requireAdmin()) {
            return $response;
        }

        $model = new WapikeyModel();
        $data = [
            'provider'    => $this->request->getPost('provider'),
            'wa_api_url'  => $this->request->getPost('wa_api_url'),
            'wa_api_key'  => $this->request->getPost('wa_api_key'),
            'admin_phone' => $this->request->getPost('admin_phone'),
            'status'      => $this->request->getPost('status') ?? 'inactive',
            'created_at'  => date('Y-m-d H:i:s'),
            'updated_at'  => date('Y-m-d H:i:s'),
        ];
        $model->insert($data);

        return redirect()->to('/admin/wapikey')->with('success', 'Provider baru berhasil ditambahkan.');
    }

    /**
     * Form edit provider
     */
    public function edit($id)
    {

        if ($response = $this->requireAdmin()) {
            return $response;
        }

        $model = new WapikeyModel();
        $provider = $model->find($id);

        if (!$provider) {
            return redirect()->to('/admin/wapikey')->with('error', 'Provider tidak ditemukan.');
        }

        return view('admin/api/wapikey_edit', ['provider' => $provider]);
    }

    /**
     * Update provider
     */
    public function update($id)
    {

        if ($response = $this->requireAdmin()) {
            return $response;
        }

        $model = new WapikeyModel();
        $data = [
            'provider'    => $this->request->getPost('provider'),
            'wa_api_url'  => $this->request->getPost('wa_api_url'),
            'wa_api_key'  => $this->request->getPost('wa_api_key'),
            'admin_phone' => $this->request->getPost('admin_phone'),
            'status'      => $this->request->getPost('status'),
            'updated_at'  => date('Y-m-d H:i:s'),
        ];
        $model->update($id, $data);

        return redirect()->to('/admin/wapikey')->with('success', 'Provider berhasil diperbarui.');
    }

    /**
     * Hapus provider
     */
    public function delete($id)
    {

        if ($response = $this->requireAdmin()) {
            return $response;
        }

        $model = new WapikeyModel();
        $provider = $model->find($id);

        if (!$provider) {
            return redirect()->to('/admin/wapikey')->with('error', 'Provider tidak ditemukan.');
        }

        $model->delete($id);
        return redirect()->to('/admin/wapikey')->with('success', 'Provider berhasil dihapus.');
    }

    /**
     * Set provider menjadi Active
     */
    public function setActive($id)
    {

        if ($response = $this->requireAdmin()) {
            return $response;
        }

        $model = new WapikeyModel();

        // Nonaktifkan semua provider dulu
        $model->updateBatch(
            array_map(fn($p) => ['id' => $p['id'], 'status' => 'inactive'], $model->findAll()),
            'id'
        );

        // Aktifkan provider terpilih
        $model->update($id, ['status' => 'active', 'updated_at' => date('Y-m-d H:i:s')]);

        return redirect()->to('/admin/wapikey')->with('success', 'Provider berhasil diaktifkan.');
    }

    /**
     * Set provider menjadi Inactive
     */
    public function setInactive($id)
    {

        if ($response = $this->requireAdmin()) {
            return $response;
        }

        $model = new WapikeyModel();
        $model->update($id, ['status' => 'inactive', 'updated_at' => date('Y-m-d H:i:s')]);

        return redirect()->to('/admin/wapikey')->with('success', 'Provider berhasil dinonaktifkan.');
    }
}
