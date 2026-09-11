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
        $this->plotkelasModel     = new PlotkelasModel();
        $this->siswaModel         = new SiswaModel();
        $this->kelasModel         = new KelasModel();
        $this->tapelModel         = new TapelModel();
        $this->kelasMappingModel  = new KelasMappingModel();
    }

    // =========================
    // 1. PLOT DATA SISWA BARU
    // =========================

    public function index()
    {

        if ($response = $this->requireAdmin()) {
            return $response;
        }

        $perPage   = $this->request->getGet('perPage') ?? 10;
        $kelasId   = $this->request->getGet('kelas_id') ?? '';
        $tapelId   = $this->request->getGet('tapel_id') ?? '';
        $keyword   = $this->request->getGet('keyword') ?? '';

        // Query melalui model, bukan builder
        $plotkelasQuery = $this->plotkelasModel
            ->select('plot_kelas.*, siswa.nama_siswa, kelas.nama_kelas, tapel.tahun_pelajaran, tapel.semester')
            ->join('siswa', 'siswa.id = plot_kelas.siswa_id')
            ->join('kelas', 'kelas.id = plot_kelas.kelas_id')
            ->join('tapel', 'tapel.id = plot_kelas.tapel_id');

        if (!empty($kelasId)) {
            $plotkelasQuery->where('plot_kelas.kelas_id', $kelasId);
        }
        if (!empty($tapelId)) {
            $plotkelasQuery->where('plot_kelas.tapel_id', $tapelId);
        }
        if (!empty($keyword)) {
            $plotkelasQuery->like('siswa.nama_siswa', $keyword);
        }

        $data['plotkelas']    = $plotkelasQuery->paginate($perPage, 'default');
        $data['pager']        = $this->plotkelasModel->pager;
        $data['kelasList']    = $this->kelasModel->findAll();
        $data['semesterList'] = $this->tapelModel->findAll();
        $data['perPage']      = $perPage;
        $data['kelas_id']     = $kelasId;
        $data['tapel_id']     = $tapelId;
        $data['keyword']      = $keyword;
        $data['title']        = 'Daftar Plot Kelas';

        return view('Admin/plotkelas/index', $data);
    }

    public function create()
    {

        if ($response = $this->requireAdmin()) {
            return $response;
        }

        $data = [
            'siswa' => $this->siswaModel->findAll(),
            'kelas' => $this->kelasModel->findAll(),
            'tapel' => $this->tapelModel->findAll(),
            'title' => 'Tambah Plot Kelas (Siswa Baru)'
        ];
        return view('admin/plotkelas/created', $data);
    }

    public function store()
    {

        if ($response = $this->requireAdmin()) {
            return $response;
        }

        $mode = $this->request->getPost('mode'); // 'satuan' atau 'kelas'

        $rules = [
            'tapel_id'  => 'required|integer',
            'shift'     => 'permit_empty|in_list[pagi,siang]',
            'jam_masuk' => 'required',
            'jam_pulang'=> 'required'
        ];

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
            // plot per siswa
            $siswaId = $this->request->getPost('siswa_id');
            $this->plotkelasModel->insert([
                'siswa_id'  => $siswaId,
                'kelas_id'  => $kelasId,
                'tapel_id'  => $tapelId,
                'shift'     => $shift,
                'jam_masuk' => $jamMasuk,
                'jam_pulang'=> $jamPulang,
                'status'    => 'aktif',
                'created_at'=> date('Y-m-d H:i:s')
            ]);
        } elseif ($mode === 'kelas') {
            // plot seluruh siswa dalam kelas
            $siswaList = $this->siswaModel->where('kelas_id', $kelasId)->findAll();
            foreach ($siswaList as $s) {
                $this->plotkelasModel->insert([
                    'siswa_id'  => $s['id'],
                    'kelas_id'  => $kelasId,
                    'tapel_id'  => $tapelId,
                    'shift'     => $shift,
                    'jam_masuk' => $jamMasuk,
                    'jam_pulang'=> $jamPulang,
                    'status'    => 'aktif',
                    'created_at'=> date('Y-m-d H:i:s')
                ]);
            }
        }

        return redirect()->to(base_url('admin/plotkelas'))
                        ->with('success', 'Plot siswa baru berhasil ditambahkan');
    }

    public function edit($id)
    {

        if ($response = $this->requireAdmin()) {
            return $response;
        }

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

    public function update($id)
    {

        if ($response = $this->requireAdmin()) {
            return $response;
        }

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

        return redirect()->to(base_url('admin/plotkelas'))->with('success', 'Data plot siswa berhasil diperbarui');
    }

    public function delete($id)
    {

        if ($response = $this->requireAdmin()) {
            return $response;
        }

        if (!$this->plotkelasModel->delete($id)) {
            return redirect()->back()->with('error', "Data plot_kelas dengan ID $id tidak ditemukan");
        }

        return redirect()->to(base_url('admin/plotkelas'))->with('success', 'Data plot siswa berhasil dihapus');
    }


    // =========================
    // 2. KENAIKAN KELAS / LULUS
    // =========================

    public function promote()
    {

        if ($response = $this->requireAdmin()) {
            return $response;
        }

        $tapelList  = $this->tapelModel->findAll();
        $tapelAktif = $this->tapelModel->where('aktif', 1)->first();

        // Ambil semua siswa aktif di tapel aktif
        $builder = $this->plotkelasModel->builder();
        $builder->select('plot_kelas.*, siswa.nama_siswa, kelas.nama_kelas, tapel.tahun_pelajaran, tapel.semester');
        $builder->join('siswa', 'siswa.id = plot_kelas.siswa_id');
        $builder->join('kelas', 'kelas.id = plot_kelas.kelas_id');
        $builder->join('tapel', 'tapel.id = plot_kelas.tapel_id');
        $builder->where('plot_kelas.status', 'aktif');

        if ($tapelAktif) {
            $builder->where('plot_kelas.tapel_id', $tapelAktif['id']);
        }

        $siswaAktif = $builder->get()->getResultArray();

        // Ambil riwayat kelas tiap siswa
        $riwayat = [];
        foreach ($siswaAktif as $row) {
            $riwayat[$row['siswa_id']] = $this->plotkelasModel
                ->select('plot_kelas.*, kelas.nama_kelas, tapel.tahun_pelajaran, tapel.semester')
                ->join('kelas', 'kelas.id = plot_kelas.kelas_id')
                ->join('tapel', 'tapel.id = plot_kelas.tapel_id')
                ->where('plot_kelas.siswa_id', $row['siswa_id'])
                ->orderBy('tapel.tahun_pelajaran', 'ASC')
                ->findAll();
        }

        $data = [
            'tapel'      => $tapelList,
            'tapelAktif' => $tapelAktif,
            'siswaAktif' => $siswaAktif,
            'riwayat'    => $riwayat,
            'title'      => 'Naik Kelas / Lulus'
        ];

        return view('admin/promote/index', $data);
    }

    public function promoteCreate()
    {
        $data = [
            'tapel' => $this->tapelModel->findAll(),
            'title' => 'Form Promosi Kelas'
        ];
        return view('admin/promote/created', $data);
    }

    public function promoteStore()
    {
        $oldTapelId = $this->request->getPost('old_tapel_id');
        $newTapelId = $this->request->getPost('new_tapel_id');

        if (!$oldTapelId || !$newTapelId) {
            return redirect()->back()->with('error', 'Tapel lama dan tapel baru wajib dipilih');
        }

        // Ambil semua siswa aktif di tapel lama
        $rows = $this->plotkelasModel
            ->select('siswa_id, kelas_id, shift, jam_masuk, jam_pulang')
            ->where('tapel_id', $oldTapelId)
            ->where('status', 'aktif')
            ->findAll();

        if (empty($rows)) {
            return redirect()->back()->with('error', 'Tidak ada data siswa aktif di tapel lama');
        }

        foreach ($rows as $row) {
            $mapping = $this->kelasMappingModel->getNextClass($row['kelas_id']);

            if (!$mapping || $mapping['next_kelas_id'] === null) {
                // siswa dianggap lulus → update status siswa
                $this->siswaModel->update($row['siswa_id'], ['status' => 'lulus']);
                continue;
            }

            // siswa naik kelas
            $this->plotkelasModel->insert([
                'siswa_id'  => $row['siswa_id'],
                'kelas_id'  => $mapping['next_kelas_id'],
                'tapel_id'  => $newTapelId,
                'shift'     => $row['shift'],
                'jam_masuk' => $row['jam_masuk'],
                'jam_pulang'=> $row['jam_pulang'],
                'status'    => 'aktif',
                'created_at'=> date('Y-m-d H:i:s')
            ]);
        }

        return redirect()->to(base_url('admin/promote'))
                        ->with('success', 'Proses promosi siswa berhasil dilakukan');
    }

    public function promoteEdit($id)
    {
        $data['plotkelas'] = $this->plotkelasModel->find($id);
        $data['kelas']     = $this->kelasModel->findAll();
        $data['tapel']     = $this->tapelModel->findAll();

        if (!$data['plotkelas']) {
            throw \CodeIgniter\Exceptions\PageNotFoundException::forPageNotFound("Data promosi dengan ID $id tidak ditemukan");
        }

        $data['title'] = 'Edit Data Promosi';
        return view('admin/promote/edit', $data);
    }

    public function promoteUpdate($id)
    {
        $rules = [
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
            'kelas_id'  => $this->request->getPost('kelas_id'),
            'tapel_id'  => $this->request->getPost('tapel_id'),
            'shift'     => $this->request->getPost('shift') ?: null,
            'jam_masuk' => $this->request->getPost('jam_masuk'),
            'jam_pulang'=> $this->request->getPost('jam_pulang'),
            'updated_at'=> date('Y-m-d H:i:s')
        ];

        $this->plotkelasModel->update($id, $data);

        return redirect()->to(base_url('admin/promote'))->with('success', 'Data promosi siswa berhasil diperbarui');
    }

    

}
