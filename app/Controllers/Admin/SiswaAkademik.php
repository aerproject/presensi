<?php

namespace App\Controllers\Admin;

use App\Controllers\BaseController;
use App\Models\SiswaModel;
use App\Models\KelasModel;
use App\Models\JurusanModel;
use App\Models\TapelModel;
use App\Models\SiswaAkademikModel;
use App\Models\JamBelajarModel;

class SiswaAkademik extends BaseController
{
    protected $siswaModel;
    protected $kelasModel;
    protected $jurusanModel;
    protected $tapelModel;
    protected $siswaAkademikModel;
    protected $jamBelajarModel;

    public function __construct()
    {
        $this->siswaModel          = new SiswaModel();
        $this->kelasModel         = new KelasModel();
        $this->jurusanModel       = new JurusanModel();
        $this->tapelModel         = new TapelModel();
        $this->siswaAkademikModel = new SiswaAkademikModel();
        $this->jamBelajarModel    = new JamBelajarModel();
    }

    /*
    =====================================================
    INDEX
    =====================================================
    */
    public function index()
    {

        if ($response = $this->requireAdmin()) {
            return $response;
        }

        $tapel = $this->tapelModel
            ->where('aktif', 1)
            ->first();

        if (!$tapel) {
            return redirect()
                ->back()
                ->with('error', 'Tahun pelajaran aktif belum diatur di sistem');
        }

        $perPage = (int) ($this->request->getGet('perPage') ?? 10);
        $keyword = trim($this->request->getGet('keyword') ?? '');

        $builder = $this->siswaAkademikModel
            ->select("
                siswa_akademik.*,
                siswa.nama_siswa,
                siswa.nis,
                kelas.nama_kelas,
                jurusan.nama_jurusan,
                jam_belajar.shift,
                jam_belajar.jam_masuk,
                jam_belajar.jam_pulang
            ")
            ->join('siswa', 'siswa.id = siswa_akademik.siswa_id')
            ->join('kelas', 'kelas.id = siswa_akademik.kelas_id')
            ->join('jurusan', 'jurusan.id = siswa_akademik.jurusan_id', 'left')
            ->join('jam_belajar', 'jam_belajar.id = siswa_akademik.jam_belajar_id')
            ->where('siswa_akademik.tapel_id', $tapel['id']);

        if (!empty($keyword)) {
            $builder->groupStart()
                ->like('siswa.nama_siswa', $keyword)
                ->orLike('siswa.nis', $keyword)
                ->groupEnd();
        }

        $builder->orderBy('kelas.nama_kelas', 'ASC')
                ->orderBy('siswa.nama_siswa', 'ASC');

        $data = [
            'title'       => 'Data Akademik Siswa',
            'siswa'       => $builder->paginate($perPage),
            'pager'       => $this->siswaAkademikModel->pager,
            'perPage'     => $perPage,
            'keyword'     => $keyword,
            'tapel_aktif' => $tapel
        ];

        return view('admin/siswa_akademik/index', $data);
    }

    /*
    =====================================================
    CREATE
    =====================================================
    */
    public function create()
    {

        if ($response = $this->requireAdmin()) {
            return $response;
        }

        $tapel = $this->tapelModel
            ->where('aktif', 1)
            ->first();

        if (!$tapel) {
            return redirect()
                ->back()
                ->with('error', 'Tahun pelajaran aktif belum diset');
        }

        $data = [
            'title'       => 'Plot Siswa ke Kelas',
            'siswa'       => $this->siswaAkademikModel
                                  ->getSiswaBelumPlot($tapel['id']),
            'kelas'       => $this->kelasModel->findAll(),
            'jurusan'     => $this->jurusanModel->findAll(),
            'jam_belajar' => $this->jamBelajarModel->findAll(),
            'tapel'       => $tapel
        ];

        return view('admin/siswa_akademik/create', $data);
    }

    /*
    =====================================================
    STORE
    =====================================================
    */
    public function store()
    {
        if ($response = $this->requireAdmin()) {
            return $response;
        }

        // ====================================================
        // TAHUN PELAJARAN AKTIF
        // ====================================================

        $tapel = $this->tapelModel
            ->where('aktif', 1)
            ->first();

        if (!$tapel) {
            return redirect()
                ->back()
                ->withInput()
                ->with(
                    'error',
                    'Tahun pelajaran aktif belum diset.'
                );
        }

        // ====================================================
        // DATA FORM
        // ====================================================

        $siswaIds = $this->request->getPost('siswa_id');

        $kelasId = (int) $this->request->getPost('kelas_id');

        $jurusanId = (int) $this->request->getPost('jurusan_id');

        $jamBelajarId = (int) $this->request
            ->getPost('jam_belajar_id');


        // ====================================================
        // VALIDASI SISWA
        // ====================================================

        if (empty($siswaIds) || !is_array($siswaIds)) {

            return redirect()
                ->back()
                ->withInput()
                ->with(
                    'error',
                    'Pilih minimal 1 siswa.'
                );
        }


        $siswaIds = array_values(
            array_unique(
                array_map('intval', $siswaIds)
            )
        );


        // ====================================================
        // VALIDASI KELAS
        // ====================================================

        if ($kelasId <= 0 ||
            !$this->kelasModel->find($kelasId)) {

            return redirect()
                ->back()
                ->withInput()
                ->with(
                    'error',
                    'Kelas yang dipilih tidak valid.'
                );
        }


        // ====================================================
        // VALIDASI JURUSAN
        // ====================================================

        if ($jurusanId <= 0 ||
            !$this->jurusanModel->find($jurusanId)) {

            return redirect()
                ->back()
                ->withInput()
                ->with(
                    'error',
                    'Jurusan yang dipilih tidak valid.'
                );
        }


        // ====================================================
        // VALIDASI JAM BELAJAR
        // ====================================================

        if ($jamBelajarId <= 0 ||
            !$this->jamBelajarModel->find($jamBelajarId)) {

            return redirect()
                ->back()
                ->withInput()
                ->with(
                    'error',
                    'Jam belajar / shift yang dipilih tidak valid.'
                );
        }


        // ====================================================
        // TRANSACTION
        // ====================================================

        $db = \Config\Database::connect();

        $db->transStart();

        try {

            $jumlahBerhasil = 0;

            foreach ($siswaIds as $siswaId) {

                // Pastikan siswa benar-benar ada
                $siswa = $this->siswaModel->find($siswaId);

                if (!$siswa) {
                    continue;
                }


                // Jangan plot ulang pada tapel aktif
                $exists = $this->siswaAkademikModel
                    ->cekSudahPlot(
                        $siswaId,
                        $tapel['id']
                    );


                if ($exists) {
                    continue;
                }


                $inserted =
                    $this->siswaAkademikModel->insert([
                        'siswa_id'           => $siswaId,
                        'kelas_id'           => $kelasId,
                        'jurusan_id'         => $jurusanId,
                        'tapel_id'           => $tapel['id'],
                        'jam_belajar_id'     => $jamBelajarId,
                        'status_akademik_id' => 1
                    ]);


                if ($inserted === false) {

                    throw new \RuntimeException(
                        'Gagal memetakan siswa ID ' .
                        $siswaId . '.'
                    );

                }


                $jumlahBerhasil++;

            }


            if ($db->transStatus() === false) {

                throw new \RuntimeException(
                    'Transaction gagal.'
                );

            }


            $db->transComplete();


            // =================================================
            // HASIL
            // =================================================

            if ($jumlahBerhasil === 0) {

                return redirect()
                    ->to('/admin/siswa-akademik/create')
                    ->with(
                        'error',
                        'Tidak ada siswa baru yang dipetakan. ' .
                        'Siswa mungkin sudah diplot pada tahun pelajaran aktif.'
                    );
            }


            return redirect()
                ->to('/admin/siswa-akademik')
                ->with(
                    'success',
                    $jumlahBerhasil .
                    ' siswa berhasil dipetakan.'
                );


        } catch (\Throwable $e) {

            $db->transRollback();

            log_message(
                'error',
                'Plot siswa gagal: ' .
                $e->getMessage()
            );


            return redirect()
                ->back()
                ->withInput()
                ->with(
                    'error',
                    'Data siswa gagal dipetakan: ' .
                    $e->getMessage()
                );
        }
    }


    public function edit($id)
    {

        if ($response = $this->requireAdmin()) {
            return $response;
        }

        $siswaAkademik = $this->siswaAkademikModel->find($id);

        if (!$siswaAkademik) {
            return redirect()
                ->to('/admin/siswa-akademik')
                ->with('error', 'Data tidak ditemukan');
        }

        $data = [
            'title'       => 'Edit Penempatan Siswa',
            'siswa'       => $siswaAkademik,
            'kelas'       => $this->kelasModel->findAll(),
            'jurusan'     => $this->jurusanModel->findAll(),
            'jam_belajar' => $this->jamBelajarModel->findAll()
        ];

        return view('admin/siswa_akademik/edit', $data);
    }

    /*
    =====================================================
    UPDATE
    =====================================================
    */
    public function update($id)
    {

        if ($response = $this->requireAdmin()) {
            return $response;
        }

        $cek = $this->siswaAkademikModel->find($id);

        if (!$cek) {
            return redirect()
                ->to('/admin/siswa-akademik')
                ->with('error', 'Data tidak ditemukan');
        }

        $data = [
            'kelas_id'       => $this->request->getPost('kelas_id'),
            'jurusan_id'     => $this->request->getPost('jurusan_id'),
            'jam_belajar_id' => $this->request->getPost('jam_belajar_id')
        ];

        $this->siswaAkademikModel->update($id, $data);

        return redirect()
            ->to('/admin/siswa-akademik')
            ->with('success', 'Data berhasil diupdate');
    }

    /*
    =====================================================
    DELETE
    =====================================================
    */
    public function delete($id)
    {

        if ($response = $this->requireAdmin()) {
            return $response;
        }

        $cek = $this->siswaAkademikModel->find($id);

        if (!$cek) {
            return redirect()
                ->to('/admin/siswa-akademik')
                ->with('error', 'Data tidak ditemukan');
        }

        $this->siswaAkademikModel->delete($id);

        return redirect()
            ->to('/admin/siswa-akademik')
            ->with('success', 'Data berhasil dihapus');
    }
}

