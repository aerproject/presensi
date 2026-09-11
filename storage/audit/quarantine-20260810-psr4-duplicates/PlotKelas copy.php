<?php

namespace App\Controllers\Admin;

use App\Controllers\BaseController;
use App\Models\PlotkelasModel;
use App\Models\SiswaModel;
use App\Models\KelasModel;
use App\Models\TapelModel;
use App\Models\KelasMappingModel;


class PlotKelas extends BaseController
{
    protected $plotkelasModel;
    protected $siswaModel;
    protected $kelasModel;
    protected $tapelModel;
    protected $kelasMappingModel;

    public function __construct()
        {
            $this->plotkelasModel = new PlotkelasModel();
            $this->siswaModel     = new SiswaModel();
            $this->kelasModel     = new KelasModel();
            $this->tapelModel     = new TapelModel();
            $this->kelasMappingModel = new KelasMappingModel();

        }

    // INDEX: tampilkan daftar relasi
    public function index()
    {
        $perPage = $this->request->getGet('perPage') ?? 10;
        $kelas   = $this->request->getGet('kelas') ?? '';
        $keyword = $this->request->getGet('keyword') ?? '';

        $data['plotkelas']  = $this->plotkelasModel->getAllPlotKelas($perPage);
        $data['pager']      = $this->plotkelasModel->pager;
        $data['kelasList']  = $this->kelasModel->findAll();
        $data['perPage']    = $perPage;
        $data['kelas']      = $kelas;
        $data['keyword']    = $keyword;
        $data['title']      = 'Daftar Plot Kelas';

        return view('Admin/plotkelas/index', $data);
    }



// CREATE: tampilkan form tambah relasi
public function create()
    {
        $data = [
            'siswa' => $this->siswaModel->findAll(),
            'kelas' => $this->kelasModel->findAll(),
            'tapel' => $this->tapelModel->findAll(),
            'title' => 'Tambah Plot Kelas'
        ];

        // gunakan path sesuai folder di Views (huruf kecil)
        return view('admin/plotkelas/created', $data);
    }


    // STORE: simpan relasi baru
    public function store()
    {
        $mode = $this->request->getPost('mode');

        $rules = [
            'tapel_id'  => 'required|integer',
            'shift'     => 'permit_empty|in_list[pagi,siang]',
            'jam_masuk' => 'required',
            'jam_pulang'=> 'required'
        ];

        // validasi tambahan sesuai mode
        if ($mode === 'satuan') {
            $rules['siswa_id'] = 'required|integer';
            $rules['kelas_id'] = 'required|integer';
        } elseif ($mode === 'kelas') {
            $rules['kelas_id'] = 'required|integer';
        }

        if (!$this->validate($rules)) {
            return redirect()->back()->withInput()->with('errors', $this->validator->getErrors());
        }

        $tapelId   = $this->request->getPost('tapel_id');
        $kelasId   = $this->request->getPost('kelas_id');
        $shift     = $this->request->getPost('shift') ?: null;
        $jamMasuk  = $this->request->getPost('jam_masuk');
        $jamPulang = $this->request->getPost('jam_pulang');

        if ($mode === 'satuan') {
            $siswaId = $this->request->getPost('siswa_id');
            $data = [
                'siswa_id'  => $siswaId,
                'kelas_id'  => $kelasId,
                'tapel_id'  => $tapelId,
                'shift'     => $shift,
                'jam_masuk' => $jamMasuk,
                'jam_pulang'=> $jamPulang,
                'status'    => 'aktif',
                'created_at'=> date('Y-m-d H:i:s')
            ];
            $this->plotkelasModel->insert($data);

        } elseif ($mode === 'kelas') {
            // ambil semua siswa dalam kelas
            $siswaList = $this->siswaModel->where('kelas_id', $kelasId)->findAll();
            foreach ($siswaList as $s) {
                $data = [
                    'siswa_id'  => $s['id'],
                    'kelas_id'  => $kelasId,
                    'tapel_id'  => $tapelId,
                    'shift'     => $shift,
                    'jam_masuk' => $jamMasuk,
                    'jam_pulang'=> $jamPulang,
                    'status'    => 'aktif',
                    'created_at'=> date('Y-m-d H:i:s')
                ];
                $this->plotkelasModel->insert($data);
            }
        }

        return redirect()->to(base_url('admin/plotkelas'))->with('success', 'Relasi berhasil ditambahkan');
    }

    // EDIT: tampilkan form edit relasi
    public function edit($id)
    {
        $data['plotkelas'] = $this->plotkelasModel->find($id);
        $data['siswa']     = $this->siswaModel->findAll();
        $data['kelas']     = $this->kelasModel->findAll();
        $data['tapel']     = $this->tapelModel->findAll();

        if (!$data['plotkelas']) {
            throw \CodeIgniter\Exceptions\PageNotFoundException::forPageNotFound("Data plot_kelas dengan ID $id tidak ditemukan");
        }

        $data['title'] = 'Edit Plot Kelas';
        return view('Admin/plotkelas/edit', $data);
    }

    // UPDATE: simpan perubahan relasi
    public function update($id)
    {
        $rules = [
            'siswa_id'  => 'required|integer',
            'kelas_id'  => 'required|integer',
            'tapel_id'  => 'required|integer',
            'shift'     => 'permit_empty|in_list[pagi,siang]',
            'jam_masuk' => 'required',
            'jam_pulang'=> 'required'
        ];

        if (!$this->validate($rules)) {
            return redirect()->back()->withInput()->with('errors', $this->validator->getErrors());
        }

        $data = [
            'siswa_id'  => $this->request->getPost('siswa_id'),
            'kelas_id'  => $this->request->getPost('kelas_id'),
            'tapel_id'  => $this->request->getPost('tapel_id'),
            'shift'     => $this->request->getPost('shift') ?: null,
            'jam_masuk' => $this->request->getPost('jam_masuk'),
            'jam_pulang'=> $this->request->getPost('jam_pulang'),
            'updated_at'=> date('Y-m-d H:i:s')
        ];

        $this->plotkelasModel->update($id, $data);

        return redirect()->to(base_url('admin/plotkelas'))->with('success', 'Data berhasil diperbarui');
    }

    // DELETE: hapus relasi
    public function delete($id)
    {
        if (!$this->plotkelasModel->delete($id)) {
            return redirect()->back()->with('error', "Data plot_kelas dengan ID $id tidak ditemukan");
        }

        return redirect()->to(base_url('admin/plotkelas'))->with('success', 'Data berhasil dihapus');
    }

    // Copy Semester
    public function copyForm()
    {
        $data['tapel'] = $this->tapelModel->findAll();
        $data['kelas'] = $this->kelasModel->findAll();
        $data['title'] = 'Salin Siswa Aktif ke Semester Baru';
        return view('admin/plotkelas/semester', $data);
    }

    // COPY: salin semua siswa aktif dari semester lama ke semester baru
    public function copySemester()
    {
        // Ambil input dari form
        $oldTapelId = $this->request->getPost('old_tapel_id');
        $newTapelId = $this->request->getPost('new_tapel_id');
        $kelasId    = $this->request->getPost('kelas_id'); // optional, jika ingin per kelas

        if (!$oldTapelId || !$newTapelId) {
            return redirect()->back()->with('error', 'Tapel lama dan tapel baru wajib dipilih');
        }

        // Query ambil siswa aktif dari tapel lama
        $builder = $this->plotkelasModel->builder();
        $builder->select('siswa_id, kelas_id, shift, jam_masuk, jam_pulang');
        $builder->where('tapel_id', $oldTapelId);
        $builder->where('status', 'aktif');
        if ($kelasId) {
            $builder->where('kelas_id', $kelasId);
        }
        $rows = $builder->get()->getResultArray();

        if (empty($rows)) {
            return redirect()->back()->with('error', 'Tidak ada data siswa aktif di tapel lama');
        }

        // Insert ke tapel baru
        foreach ($rows as $row) {
            $data = [
                'siswa_id'  => $row['siswa_id'],
                'kelas_id'  => $row['kelas_id'],
                'tapel_id'  => $newTapelId,
                'shift'     => $row['shift'],
                'jam_masuk' => $row['jam_masuk'],
                'jam_pulang'=> $row['jam_pulang'],
                'status'    => 'aktif',
                'created_at'=> date('Y-m-d H:i:s')
            ];
            $this->plotkelasModel->insert($data);
        }

        return redirect()->to(base_url('admin/semester'))
                        ->with('success', 'Data siswa aktif berhasil disalin ke tapel baru');
    }

    // PROMOTE: naik kelas semua siswa aktif dari tapel lama ke tapel baru
    public function promotePreview()
    {
        $oldTapelId = $this->request->getPost('old_tapel_id');
        $newTapelId = $this->request->getPost('new_tapel_id');

        if (!$oldTapelId || !$newTapelId) {
            return redirect()->back()->with('error', 'Tapel lama dan tapel baru wajib dipilih');
        }

        // Ambil siswa aktif di tapel lama
        $builder = $this->plotkelasModel->builder();
        $builder->select('plot_kelas.*, siswa.nama_siswa, kelas.nama_kelas');
        $builder->join('siswa', 'siswa.id = plot_kelas.siswa_id');
        $builder->join('kelas', 'kelas.id = plot_kelas.kelas_id');
        $builder->where('plot_kelas.tapel_id', $oldTapelId);
        $builder->where('plot_kelas.status', 'aktif');
        $rows = $builder->get()->getResultArray();

        $preview = [];

        foreach ($rows as $row) {
            $mapping = $this->kelasMappingModel->getNextClass($row['kelas_id']);

            $preview[] = [
                'siswa'        => $row['nama_siswa'],
                'kelas_lama'   => $row['nama_kelas'],
                'kelas_baru'   => $mapping && $mapping['next_kelas_id']
                                    ? $this->kelasModel->find($mapping['next_kelas_id'])['nama_kelas']
                                    : null,
                'status'       => $mapping
                                    ? ($mapping['next_kelas_id'] ? 'Naik Kelas' : 'Lulus')
                                    : 'Mapping Tidak Ada'
            ];
        }

        $data = [
            'preview' => $preview,
            'old_tapel_id' => $oldTapelId,
            'new_tapel_id' => $newTapelId,
            'title' => 'Preview Naik Kelas'
        ];

        return view('admin/promote/index', $data);
    }

    public function promoteForm()
    {
        $data['tapel'] = $this->tapelModel->findAll();
        $data['title'] = 'Naik Kelas Siswa Aktif';
        return view('admin/promote/created', $data);
    }


    public function promoteClass()
    {
        $oldTapelId = $this->request->getPost('old_tapel_id');
        $newTapelId = $this->request->getPost('new_tapel_id');

        if (!$oldTapelId || !$newTapelId) {
            return redirect()->back()->with('error', 'Tapel lama dan tapel baru wajib dipilih');
        }

        // Ambil semua siswa aktif di tapel lama
        $builder = $this->plotkelasModel->builder();
        $builder->select('siswa_id, kelas_id, shift, jam_masuk, jam_pulang');
        $builder->where('tapel_id', $oldTapelId);
        $builder->where('status', 'aktif');
        $rows = $builder->get()->getResultArray();

        if (empty($rows)) {
            return redirect()->back()->with('error', 'Tidak ada data siswa aktif di tapel lama');
        }

        foreach ($rows as $row) {

            // Ambil kelas baru dari tabel mapping
            $mapping = $this->kelasMappingModel->getNextClass($row['kelas_id']);

            // Jika tidak ada mapping → siswa dianggap lulus
            if (!$mapping || $mapping['next_kelas_id'] === null) {
                continue;
            }

            $newKelasId = $mapping['next_kelas_id'];

            // Simpan record baru
            $data = [
                'siswa_id'  => $row['siswa_id'],
                'kelas_id'  => $newKelasId,
                'tapel_id'  => $newTapelId,
                'shift'     => $row['shift'],
                'jam_masuk' => $row['jam_masuk'],
                'jam_pulang'=> $row['jam_pulang'],
                'status'    => 'aktif',
                'created_at'=> date('Y-m-d H:i:s')
            ];

            $this->plotkelasModel->insert($data);
        }

        return redirect()->to(base_url('admin/promote'))
                        ->with('success', 'Proses naik kelas berhasil dilakukan');
    }




}
