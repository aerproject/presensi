<?php

namespace App\Controllers\Admin;

use App\Controllers\BaseController;
use App\Models\LibursekolahModel;
use App\Models\TapelModel;

class Libursekolah extends BaseController
{
    protected $liburModel;
    protected $tapelModel;

    public function __construct()
    {
        $this->liburModel = new LibursekolahModel();
        $this->tapelModel = new TapelModel();
    }


    // Halaman index: daftar libur sekolah
    public function index()
    {

        if ($response = $this->requireAdmin()) {
            return $response;
        }

        $data['title'] = 'Libur Sekolah | Admin Panel Absensi Digital';

        // Ambil nilai perPage dari query string, default 10
        $perPage = (int)($this->request->getGet('perPage') ?? 10);

        /** * PERBAIKAN: 
         * Tambahkan select() untuk menentukan kolom yang diambil
         * Tambahkan join() untuk menghubungkan ke tabel tapel
         */
        $libur = $this->liburModel
            ->select('libursekolah.*, tapel.tahun_pelajaran, tapel.semester')
            ->join('tapel', 'tapel.id = libursekolah.tapel_id')
            ->orderBy('libursekolah.tanggal', 'ASC')
            ->paginate($perPage, 'default');

        $data['libur']   = $libur;
        $data['pager']   = $this->liburModel->pager;
        $data['perPage'] = $perPage;

        return view('admin/libursekolah/index', $data);
    }

    // Form tambah libur sekolah
    public function create()
    {

        if ($response = $this->requireAdmin()) {
            return $response;
        }

        // ambil tahun pelajaran & semester aktif dari tabel tapel
        $tapel = $this->tapelModel->getAktif();

        $data = [
            'tahun_pelajaran' => $tapel['tahun_pelajaran'] ?? '',
            'semester'        => $tapel['semester'] ?? ''
        ];

        return view('admin/libursekolah/created', $data);
    }

    // Simpan data libur sekolah
    public function store()
    {

        if ($response = $this->requireAdmin()) {
            return $response;
        }

        $tapel = $this->tapelModel->getAktif();

        $this->liburModel->insert([
            'tapel_id'   => $tapel['id'],
            'tanggal'    => $this->request->getPost('tanggal'),
            'keterangan' => $this->request->getPost('keterangan')
        ]);

        return redirect()->to('/admin/libursekolah')
                         ->with('success', 'Data libur sekolah berhasil ditambahkan');
    }

    // Form edit libur sekolah
    public function edit($id)
    {

        if ($response = $this->requireAdmin()) {
            return $response;
        }

        // Menggunakan join yang sama dengan fungsi index agar kolom tahun_pelajaran ikut terbawa
        $libur = $this->liburModel
            ->select('libursekolah.*, tapel.tahun_pelajaran, tapel.semester')
            ->join('tapel', 'tapel.id = libursekolah.tapel_id')
            ->find($id);

        // Jika data tidak ditemukan
        if (!$libur) {
            return redirect()->to('/admin/libursekolah')
                            ->with('error', 'Data libur tidak ditemukan');
        }

        $data = [
            'title' => 'Edit Libur Sekolah | Admin Panel',
            'libur' => $libur,
            // Karena sudah di-join, data ini sekarang ada di dalam array $libur
            'tahun_pelajaran' => $libur['tahun_pelajaran'],
            'semester'        => $libur['semester']
        ];

        return view('admin/libursekolah/edit', $data);
    }

    // Update data libur sekolah
    public function update($id)
    {

        if ($response = $this->requireAdmin()) {
            return $response;
        }

        $tapel = $this->tapelModel->getAktif();

        $this->liburModel->update($id, [
            'tapel_id'   => $tapel['id'],
            'tanggal'    => $this->request->getPost('tanggal'),
            'keterangan' => $this->request->getPost('keterangan')
        ]);

        return redirect()->to('/admin/libursekolah')
                         ->with('success', 'Data libur sekolah berhasil diperbarui');
    }

    // Hapus data libur sekolah
    public function delete($id)
    {

        if ($response = $this->requireAdmin()) {
            return $response;
        }

        $this->liburModel->delete($id);
        return redirect()->to('/admin/libursekolah')
                         ->with('success', 'Data libur sekolah berhasil dihapus');
    }
}
