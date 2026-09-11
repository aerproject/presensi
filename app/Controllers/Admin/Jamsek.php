<?php

namespace App\Controllers\Admin;

use App\Models\JamsekModel;
use CodeIgniter\Controller;

class Jamsek extends Controller
{
    public function index()
    {

        if ($response = $this->requireAdmin()) {
            return $response;
        }

        $jamsekModel = new JamsekModel();
        $jam = $jamsekModel->first(); // diasumsikan hanya satu baris

        return view('jamsek/index', ['jam' => $jam]);
    }

    public function edit()
    {

        if ($response = $this->requireAdmin()) {
            return $response;
        }

        $jamsekModel = new JamsekModel();
        $jam = $jamsekModel->first();

        return view('jamsek/edit', ['jam' => $jam]);
    }

    public function update()
    {

        if ($response = $this->requireAdmin()) {
            return $response;
        }

        $jamsekModel = new JamsekModel();
        $id = $this->request->getPost('id');

        $data = [
            'jam_masuk' => $this->request->getPost('jam_masuk'),
            'jam_pulang' => $this->request->getPost('jam_pulang')
        ];

        $jamsekModel->update($id, $data);

        return redirect()->to('/jamsek')->with('success', 'Jam sekolah berhasil diperbarui.');
    }
}
