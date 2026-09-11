<?php

namespace App\Controllers\Admin;

use App\Controllers\BaseController;
use App\Models\AttendanceModel;
use App\Models\SiswaAkademikModel;
use App\Models\KelasModel;
use App\Models\JurusanModel;
use App\Models\TapelModel;

class Kehadiran extends BaseController
{
    protected $attendanceModel;
    protected $siswaAkademikModel;
    protected $kelasModel;
    protected $jurusanModel;
    protected $tapelModel;

    public function __construct()
    {
        $this->attendanceModel     = new AttendanceModel();
        $this->siswaAkademikModel = new SiswaAkademikModel();
        $this->kelasModel         = new KelasModel();
        $this->jurusanModel       = new JurusanModel();
        $this->tapelModel         = new TapelModel();
    }

    /*
    |--------------------------------------------------------------------------
    | Helper Tapel Aktif
    |--------------------------------------------------------------------------
    */
    private function getTapelAktif()
    {
        return $this->tapelModel
            ->where('aktif', 1)
            ->first();
    }

    /*
    |--------------------------------------------------------------------------
    | INDEX DATA KEHADIRAN
    |--------------------------------------------------------------------------
    */
    public function index()
    {

        if ($response = $this->requireAdminOperator()) {
            return $response;
        }

        $perPage    = (int) ($this->request->getGet('perPage') ?? 10);
        $keyword    = trim($this->request->getGet('keyword') ?? '');
        $kelas_id   = $this->request->getGet('kelas_id');
        $jurusan_id = $this->request->getGet('jurusan_id');

        $builder = $this->attendanceModel

            ->select("
                attendance.*,
                siswa.nama_siswa,
                siswa.nis,
                kelas.nama_kelas,
                jurusan.nama_jurusan
            ")

            ->join(
                'siswa_akademik',
                'siswa_akademik.id = attendance.siswa_akademik_id'
            )

            ->join(
                'siswa',
                'siswa.id = siswa_akademik.siswa_id'
            )

            ->join(
                'kelas',
                'kelas.id = siswa_akademik.kelas_id'
            )

            ->join(
                'jurusan',
                'jurusan.id = siswa_akademik.jurusan_id',
                'left'
            );

        // search
        if (!empty($keyword)) {
            $builder->groupStart()
                ->like('siswa.nama_siswa', $keyword)
                ->orLike('siswa.nis', $keyword)
                ->groupEnd();
        }

        // filter kelas
        if (!empty($kelas_id)) {
            $builder->where('siswa_akademik.kelas_id', $kelas_id);
        }

        // filter jurusan
        if (!empty($jurusan_id)) {
            $builder->where('siswa_akademik.jurusan_id', $jurusan_id);
        }

        $data = [

            'title'       => 'Data Kehadiran',

            'kehadiran'   => $builder
                                ->orderBy('attendance.tanggal', 'DESC')
                                ->paginate($perPage, 'default'),

            'pager'       => $this->attendanceModel->pager,

            'perPage'     => $perPage,

            'kelasList'   => $this->kelasModel->findAll(),

            'jurusanList' => $this->jurusanModel->findAll(),

            'keyword'     => $keyword
        ];

        return view('admin/kehadiran/index', $data);
    }

    /*
    |--------------------------------------------------------------------------
    | LIST SISWA UNTUK INPUT PRESENSI
    |--------------------------------------------------------------------------
    */
    public function siswa()
    {

        if ($response = $this->requireAdminOperator()) {
            return $response;
        }

        $tapelAktif = $this->getTapelAktif();

        if (!$tapelAktif) {
            return redirect()->back()
                ->with('error', 'Tapel aktif belum tersedia');
        }

        $perPage = (int) ($this->request->getGet('perPage') ?? 10);
        $keyword = trim($this->request->getGet('keyword') ?? '');
        $kelasId = $this->request->getGet('kelas_id');

        $builder = $this->siswaAkademikModel

            ->select("
                siswa_akademik.*,
                siswa.nama_siswa,
                siswa.nis,
                kelas.nama_kelas
            ")

            ->join('siswa', 'siswa.id = siswa_akademik.siswa_id')

            ->join('kelas', 'kelas.id = siswa_akademik.kelas_id')

            ->where('siswa_akademik.tapel_id', $tapelAktif['id'])

            ->where('siswa_akademik.status_akademik_id', 1);

        if (!empty($keyword)) {
            $builder->groupStart()
                ->like('siswa.nama_siswa', $keyword)
                ->orLike('siswa.nis', $keyword)
                ->groupEnd();
        }

        if (!empty($kelasId)) {
            $builder->where('siswa_akademik.kelas_id', $kelasId);
        }

        $data = [

            'title'     => 'Daftar Siswa Presensi',

            'plotSiswa' => $builder->paginate($perPage),

            'pager'     => $this->siswaAkademikModel->pager,

            'kelasList' => $this->kelasModel->findAll(),

            'perPage'   => $perPage
        ];

        return view('admin/kehadiran/siswa', $data);
    }

    /*
    |--------------------------------------------------------------------------
    | FORM PRESENSI
    |--------------------------------------------------------------------------
    */
    public function presensi($id)
    {

        if ($response = $this->requireAdminOperator()) {
            return $response;
        }

        $siswa = $this->siswaAkademikModel

            ->select("
                siswa_akademik.id,
                siswa.nama_siswa,
                kelas.nama_kelas
            ")

            ->join('siswa', 'siswa.id = siswa_akademik.siswa_id')

            ->join('kelas', 'kelas.id = siswa_akademik.kelas_id')

            ->find($id);

        if (!$siswa) {
            return redirect()->back()
                ->with('error', 'Data siswa tidak ditemukan');
        }

        return view('admin/kehadiran/created', [

            'siswa' => $siswa

        ]);
    }

    /*
    |--------------------------------------------------------------------------
    | SIMPAN PRESENSI
    |--------------------------------------------------------------------------
    */
    public function prestore()
    {

        if ($response = $this->requireAdminOperator()) {
            return $response;
        }

        $siswaAkademikId = $this->request->getPost('siswa_akademik_id');
        $tanggal         = $this->request->getPost('tanggal');
        $jam             = $this->request->getPost('jam_input');
        $jenis           = $this->request->getPost('jenis_presensi');

        $existing = $this->attendanceModel

            ->where('siswa_akademik_id', $siswaAkademikId)

            ->where('tanggal', $tanggal)

            ->first();

        $data = [

            'status'     => $this->request->getPost('status'),

            'keterangan' => $this->request->getPost('keterangan')
        ];

        if ($jenis == 'masuk') {
            $data['jam_masuk'] = $jam;
        } else {
            $data['jam_pulang'] = $jam;
        }

        if ($existing) {

            $this->attendanceModel->update($existing['id'], $data);

        } else {

            $data['siswa_akademik_id'] = $siswaAkademikId;
            $data['tanggal']           = $tanggal;

            $inserted = $this->attendanceModel->insert($data);

            if ($inserted === false) {
                // Request lain mungkin sudah membuat attendance
                // untuk siswa + tanggal yang sama.
                $existingAfterInsert = $this->attendanceModel
                    ->where('siswa_akademik_id', $siswaAkademikId)
                    ->where('tanggal', $tanggal)
                    ->first();

                if ($existingAfterInsert) {
                    $this->attendanceModel->update(
                        $existingAfterInsert['id'],
                        $data
                    );
                } else {
                    return redirect()
                        ->back()
                        ->withInput()
                        ->with(
                            'error',
                            'Data presensi gagal disimpan. Silakan coba lagi.'
                        );
                }
            }
        }

        return redirect()
            ->to('/admin/kehadiran')
            ->with('success', 'Data presensi berhasil disimpan');
    }

    /*
    |--------------------------------------------------------------------------
    | EDIT PRESENSI
    |--------------------------------------------------------------------------
    */
    public function edit($id)
    {

        if ($response = $this->requireAdminOperator()) {
            return $response;
        }

        $kehadiran = $this->attendanceModel

            ->select("
                attendance.*,
                siswa.nama_siswa,
                kelas.nama_kelas
            ")

            ->join(
                'siswa_akademik',
                'siswa_akademik.id = attendance.siswa_akademik_id'
            )

            ->join(
                'siswa',
                'siswa.id = siswa_akademik.siswa_id'
            )

            ->join(
                'kelas',
                'kelas.id = siswa_akademik.kelas_id'
            )

            ->find($id);

        if (!$kehadiran) {
            return redirect()->back()
                ->with('error', 'Data tidak ditemukan');
        }

        return view('admin/kehadiran/edit', [

            'kehadiran' => $kehadiran

        ]);
    }

    /*
    |--------------------------------------------------------------------------
    | UPDATE PRESENSI
    |--------------------------------------------------------------------------
    */
    public function update($id)
    {

        if ($response = $this->requireAdminOperator()) {
            return $response;
        }

        $data = [

            'tanggal'     => $this->request->getPost('tanggal'),

            'status'      => $this->request->getPost('status'),

            'jam_masuk'   => $this->request->getPost('jam_masuk'),

            'jam_pulang'  => $this->request->getPost('jam_pulang'),

            'keterangan'  => $this->request->getPost('keterangan')
        ];

        $this->attendanceModel->update($id, $data);

        return redirect()
            ->to('/admin/kehadiran')
            ->with('success', 'Data berhasil diperbarui');
    }
}