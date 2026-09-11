<?php

namespace App\Controllers\Admin;

use App\Controllers\BaseController;
use App\Models\SiswaModel;
use App\Models\SiswaAkademikModel;
use App\Models\TapelModel;
use App\Models\KelasModel;
use App\Models\KelasMappingModel;

class Promote extends BaseController
{
    protected $siswaModel;
    protected $siswaAkademikModel;
    protected $tapelModel;
    protected $kelasModel;
    protected $kelasMappingModel;

    public function __construct()
    {
        $this->siswaModel          = new SiswaModel();
        $this->siswaAkademikModel = new SiswaAkademikModel();
        $this->tapelModel         = new TapelModel();
        $this->kelasModel         = new KelasModel();
        $this->kelasMappingModel  = new KelasMappingModel();
    }


    // =====================================================
    // HELPER : TAPEL AKTIF
    // =====================================================
    private function getTapelAktif()
    {
        return $this->tapelModel
            ->where('aktif', 1)
            ->first();
    }


    // =====================================================
    // HELPER : TAPEL BERIKUTNYA
    // =====================================================
    private function getTapelTujuan($tapelAktifId)
    {
        return $this->tapelModel
            ->where('id >', $tapelAktifId)
            ->orderBy('id', 'ASC')
            ->first();
    }


    // =====================================================
    // INDEX DATA SISWA AKTIF PADA TAPEL AKTIF
    // =====================================================
    public function index()
    {

        if ($response = $this->requireAdmin()) {
            return $response;
        }

        $tapelAktif = $this->getTapelAktif();

        if (!$tapelAktif) {
            return redirect()->back()
                ->with('error', 'Tahun pelajaran aktif belum tersedia');
        }

        // jumlah data per halaman
        $perPage = (int) ($this->request->getGet('perPage') ?? 10);

        // keyword pencarian
        $keyword = trim($this->request->getGet('keyword') ?? '');

        // query builder
        $builder = $this->siswaAkademikModel

            ->select('
                siswa_akademik.*,
                siswa.nis,
                siswa.nama_siswa,
                kelas.nama_kelas
            ')

            ->join('siswa', 'siswa.id = siswa_akademik.siswa_id')

            ->join('kelas', 'kelas.id = siswa_akademik.kelas_id')

            ->where('siswa_akademik.tapel_id', $tapelAktif['id'])

            ->where('siswa_akademik.status_akademik_id', 1);

        // filter pencarian
        if (!empty($keyword)) {

            $builder->groupStart()

                ->like('siswa.nama_siswa', $keyword)

                ->orLike('siswa.nis', $keyword)

                ->groupEnd();
        }

        // ambil data paginate
        $siswaAktif = $builder

            ->orderBy('kelas.nama_kelas', 'ASC')

            ->orderBy('siswa.nama_siswa', 'ASC')

            ->paginate($perPage, 'default');

        // kirim ke view
        return view('admin/promote/index', [

            'title'       => 'Promosi / Naik Kelas',

            'siswaAktif'  => $siswaAktif,

            'pager'       => $this->siswaAkademikModel->pager,

            'tapelAktif'  => $tapelAktif,

            'perPage'     => $perPage,

            'keyword'     => $keyword

        ]);
    }


    // =====================================================
    // KONFIRMASI NAIK KELAS INDIVIDU
    // =====================================================
    public function confirmPromote($siswa_id)
    {

        if ($response = $this->requireAdmin()) {
            return $response;
        }

        $tapelSekarang = $this->getTapelAktif();

        if (!$tapelSekarang) {
            return redirect()->to('/admin/promote')
                ->with('error', 'Tahun pelajaran aktif belum tersedia');
        }

        $tapelTujuan = $this->getTapelTujuan($tapelSekarang['id']);

        if (!$tapelTujuan) {
            return redirect()->to('/admin/promote')
                ->with('error', 'Tahun pelajaran tujuan belum dibuat');
        }

        $siswa = $this->siswaAkademikModel

            ->select('
                siswa_akademik.*,
                siswa.nama_siswa,
                siswa.nis,
                kelas.nama_kelas
            ')

            ->join('siswa', 'siswa.id = siswa_akademik.siswa_id')

            ->join('kelas', 'kelas.id = siswa_akademik.kelas_id')

            ->where('siswa_akademik.siswa_id', $siswa_id)

            ->where('siswa_akademik.tapel_id', $tapelSekarang['id'])

            ->where('siswa_akademik.status_akademik_id', 1)

            ->first();

        if (!$siswa) {
            return redirect()->to('/admin/promote')
                ->with('error', 'Data siswa tidak ditemukan');
        }

        $mapping = $this->kelasMappingModel
            ->where('kelas_id', $siswa['kelas_id'])
            ->first();

        $kelasTujuan = null;

        if ($mapping && $mapping['next_kelas_id']) {
            $kelasTujuan = $this->kelasModel
                ->find($mapping['next_kelas_id']);
        }

        return view('admin/promote/confirm_promote', [

            'title'         => 'Konfirmasi Naik Kelas',
            'siswa'         => $siswa,
            'kelasTujuan'   => $kelasTujuan,
            'tapelSekarang' => $tapelSekarang,
            'tapelTujuan'   => $tapelTujuan
        ]);
    }


    // =====================================================
    // EXECUTE NAIK KELAS INDIVIDU
    // =====================================================
    public function promoteIndividu($siswa_id)
    {

        if ($response = $this->requireAdmin()) {
            return $response;
        }

        $db = \Config\Database::connect();
        $db->transStart();

        $tapelAktif = $this->getTapelAktif();
        $tapelTujuan = $this->getTapelTujuan($tapelAktif['id']);

        if (!$tapelAktif || !$tapelTujuan) {
            return redirect()->back()
                ->with('error', 'Tapel tidak tersedia');
        }

        $akademik = $this->siswaAkademikModel

            ->where('siswa_id', $siswa_id)

            ->where('tapel_id', $tapelAktif['id'])

            ->where('status_akademik_id', 1)

            ->first();

        if (!$akademik) {
            return redirect()->back()
                ->with('error', 'Data siswa tidak ditemukan');
        }

        $mapping = $this->kelasMappingModel

            ->where('kelas_id', $akademik['kelas_id'])

            ->first();

        // kelas akhir → lulus
        if (!$mapping || $mapping['next_kelas_id'] == null) {

            $this->siswaModel->update($siswa_id, [
                'status_siswa_id' => 2
            ]);

            $this->siswaAkademikModel->update($akademik['id'], [
                'status_akademik_id' => 2,
                'tanggal_naik_kelas' => date('Y-m-d')
            ]);

            $db->transComplete();

            return redirect()->to('/admin/promote')
                ->with('success', 'Siswa berhasil diluluskan');
        }

        // tutup record lama
        $this->siswaAkademikModel->update($akademik['id'], [

            'status_akademik_id' => 2,

            'tanggal_naik_kelas' => date('Y-m-d')

        ]);

        // insert record baru
        $this->siswaAkademikModel->insert([

            'siswa_id' => $siswa_id,

            'kelas_id' => $mapping['next_kelas_id'],

            'jurusan_id' => $akademik['jurusan_id'],

            'tapel_id' => $tapelTujuan['id'],

            'shift' => $akademik['shift'],

            'jam_masuk' => $akademik['jam_masuk'],

            'jam_pulang' => $akademik['jam_pulang'],

            'status_akademik_id' => 1,

            'tanggal_naik_kelas' => date('Y-m-d')
        ]);

        $db->transComplete();

        if ($db->transStatus() === false) {
            return redirect()->back()
                ->with('error', 'Promosi gagal');
        }

        return redirect()->to('/admin/promote')
            ->with('success', 'Siswa berhasil naik kelas');
    }


    // =====================================================
    // KONFIRMASI MASSAL PER KELAS
    // =====================================================
    public function confirmPromoteKelas($kelas_id)
    {

        if ($response = $this->requireAdmin()) {
            return $response;
        }

        $tapel = $this->getTapelAktif();

        $tapelTujuan = $this->getTapelTujuan($tapel['id']);

        if (!$tapelTujuan) {
            return redirect()->back()
                ->with('error', 'Tapel tujuan belum dibuat');
        }

        $kelasAwal = $this->kelasModel->find($kelas_id);

        $mapping = $this->kelasMappingModel
            ->where('kelas_id', $kelas_id)
            ->first();

        $kelasTujuan = null;

        if ($mapping && $mapping['next_kelas_id']) {
            $kelasTujuan = $this->kelasModel
                ->find($mapping['next_kelas_id']);
        }

        $jumlahSiswa = $this->siswaAkademikModel

            ->where('kelas_id', $kelas_id)

            ->where('tapel_id', $tapel['id'])

            ->where('status_akademik_id', 1)

            ->countAllResults();

        return view('admin/promote/confirm_promote_kelas', [

            'kelasAwal'   => $kelasAwal,
            'kelasTujuan' => $kelasTujuan,
            'jumlahSiswa' => $jumlahSiswa,
            'tapel'       => $tapel,
            'tapelTujuan' => $tapelTujuan
        ]);
    }


    // =====================================================
    // EXECUTE MASSAL PER KELAS
    // =====================================================
    public function promoteKelas($kelas_id)
    {

        if ($response = $this->requireAdmin()) {
            return $response;
        }

        $db = \Config\Database::connect();
        $db->transStart();

        $tapelAktif = $this->getTapelAktif();
        $tapelTujuan = $this->getTapelTujuan($tapelAktif['id']);

        $mapping = $this->kelasMappingModel

            ->where('kelas_id', $kelas_id)

            ->first();

        $siswaList = $this->siswaAkademikModel

            ->where('kelas_id', $kelas_id)

            ->where('tapel_id', $tapelAktif['id'])

            ->where('status_akademik_id', 1)

            ->findAll();

        foreach ($siswaList as $row) {

            // kelas akhir = lulus
            if (!$mapping || $mapping['next_kelas_id'] == null) {

                $this->siswaModel->update($row['siswa_id'], [
                    'status_siswa_id' => 2
                ]);

                $this->siswaAkademikModel->update($row['id'], [
                    'status_akademik_id' => 2,
                    'tanggal_naik_kelas' => date('Y-m-d')
                ]);

                continue;
            }

            // update lama
            $this->siswaAkademikModel->update($row['id'], [

                'status_akademik_id' => 2,

                'tanggal_naik_kelas' => date('Y-m-d')

            ]);

            // insert baru
            $this->siswaAkademikModel->insert([

                'siswa_id' => $row['siswa_id'],

                'kelas_id' => $mapping['next_kelas_id'],

                'jurusan_id' => $row['jurusan_id'],

                'tapel_id' => $tapelTujuan['id'],

                'shift' => $row['shift'],

                'jam_masuk' => $row['jam_masuk'],

                'jam_pulang' => $row['jam_pulang'],

                'status_akademik_id' => 1,

                'tanggal_naik_kelas' => date('Y-m-d')
            ]);
        }

        $db->transComplete();

        if ($db->transStatus() === false) {
            return redirect()->back()
                ->with('error', 'Promosi massal gagal');
        }

        return redirect()->to('/admin/promote')
            ->with('success', 'Promosi massal berhasil');
    }


    // =====================================================
    // LULUSKAN MANUAL
    // =====================================================
    public function graduate($siswa_id)
    {

        if ($response = $this->requireAdmin()) {
            return $response;
        }

        $tapelAktif = $this->getTapelAktif();

        $siswa = $this->siswaModel->find($siswa_id);

        if (!$siswa) {
            return redirect()->back()
                ->with('error', 'Siswa tidak ditemukan');
        }

        $this->siswaModel->update($siswa_id, [
            'status_siswa_id' => 2
        ]);

        $this->siswaAkademikModel

            ->where('siswa_id', $siswa_id)

            ->where('tapel_id', $tapelAktif['id'])

            ->set([

                'status_akademik_id' => 2,

                'tanggal_naik_kelas' => date('Y-m-d')

            ])

            ->update();

        return redirect()->to('/admin/promote')
            ->with('success', 'Siswa berhasil diluluskan');
    }
}