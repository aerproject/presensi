<?php

namespace App\Controllers\Admin;

use App\Controllers\BaseController;
use App\Models\AttendanceModel;
use App\Models\SiswaAkademikModel;
use App\Models\KelasModel;
use App\Models\JurusanModel;
use App\Models\TapelModel;
use App\Models\MessageModel;

class Kehadiran extends BaseController
{
    protected $attendanceModel;
    protected $siswaAkademikModel;
    protected $kelasModel;
    protected $jurusanModel;
    protected $tapelModel;
    protected $messageModel;

    public function __construct()
    {
        $this->attendanceModel     = new AttendanceModel();
        $this->siswaAkademikModel = new SiswaAkademikModel();
        $this->kelasModel         = new KelasModel();
        $this->jurusanModel       = new JurusanModel();
        $this->tapelModel         = new TapelModel();
        $this->messageModel       = new MessageModel();
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
                kelas.nama_kelas,
                tapel.tahun_pelajaran,
                tapel.semester
            ")

            ->join('siswa', 'siswa.id = siswa_akademik.siswa_id')

            ->join('kelas', 'kelas.id = siswa_akademik.kelas_id')

            ->join('tapel', 'tapel.id = siswa_akademik.tapel_id')

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

        $status     = $this->request->getPost('status');
        $keterangan = $this->request->getPost('keterangan');

        if (!in_array($jenis, ['masuk', 'pulang'], true)) {
            return redirect()->back()
                ->withInput()
                ->with('error', 'Jenis presensi tidak valid.');
        }

        /*
        |--------------------------------------------------------------------------
        | STATE TRANSITION PRESENSI MANUAL
        |--------------------------------------------------------------------------
        | Sama dengan scanner:
        | 1. Belum ada record        -> hanya boleh Masuk
        | 2. Sudah Masuk             -> hanya boleh Pulang
        | 3. Sudah Masuk + Pulang    -> presensi selesai
        | Koreksi data dilakukan melalui menu Edit Presensi.
        */
        if ($jenis === 'masuk') {

            if ($existing && !empty($existing['jam_masuk'])) {
                return redirect()->back()
                    ->withInput()
                    ->with('error', 'Siswa sudah melakukan absensi masuk pada tanggal tersebut.');
            }

            if ($existing && !empty($existing['jam_pulang'])) {
                return redirect()->back()
                    ->withInput()
                    ->with('error', 'Data presensi sudah memiliki jam pulang. Gunakan Edit untuk koreksi.');
            }

            $data = [
                'status'     => $status,
                'keterangan' => $keterangan,
                'jam_masuk'  => $jam,
            ];

            if ($existing) {
                $saved = $this->attendanceModel->update($existing['id'], $data);
            } else {
                $data['siswa_akademik_id'] = $siswaAkademikId;
                $data['tanggal']            = $tanggal;

                try {
                    $saved = $this->attendanceModel->insert($data);
                } catch (\Throwable $e) {
                    return redirect()->back()
                        ->withInput()
                        ->with('error', 'Absensi pada tanggal tersebut sudah tercatat.');
                }
            }

        } else {

            if (!$existing || empty($existing['jam_masuk'])) {
                return redirect()->back()
                    ->withInput()
                    ->with('error', 'Absen pulang hanya dapat dilakukan setelah absen masuk.');
            }

            if (!empty($existing['jam_pulang'])) {
                return redirect()->back()
                    ->withInput()
                    ->with('error', 'Siswa sudah melakukan absensi lengkap pada tanggal tersebut.');
            }

            $data = [
                'status'      => $status,
                'keterangan'  => $keterangan,
                'jam_pulang'  => $jam,
            ];

            $saved = $this->attendanceModel->update($existing['id'], $data);
        }

        if ($saved === false) {
            return redirect()->back()
                ->withInput()
                ->with('error', 'Data presensi gagal disimpan. Silakan coba lagi.');
        }

        /*
        |--------------------------------------------------------------------------
        | QUEUE NOTIFIKASI WHATSAPP
        |--------------------------------------------------------------------------
        | Hanya presensi manual tanggal hari ini yang membuat notifikasi.
        | Pengiriman tetap dilakukan asynchronous oleh Message Worker.
        */
        $today = date('Y-m-d');

        if ($tanggal === $today) {

            $siswa = $this->siswaAkademikModel
                ->select("
                    siswa.users_id,
                    siswa.nama_siswa,
                    kelas.nama_kelas,
                    jurusan.nama_jurusan
                ")
                ->join('siswa', 'siswa.id = siswa_akademik.siswa_id')
                ->join('kelas', 'kelas.id = siswa_akademik.kelas_id')
                ->join(
                    'jurusan',
                    'jurusan.id = siswa_akademik.jurusan_id',
                    'left'
                )
                ->where('siswa_akademik.id', $siswaAkademikId)
                ->first();

            if ($siswa && !empty($siswa['users_id'])) {

                $nama    = $siswa['nama_siswa'];
                $kelas   = $siswa['nama_kelas'];
                $jurusan = $siswa['nama_jurusan'] ?? '-';
                $status  = $data['status'];
                $ket     = trim((string) ($data['keterangan'] ?? ''));

                if ($jenis === 'masuk') {

                    if ($status === 'hadir') {
                        $isiPesan =
                            "✅ Halo Bpk/Ibu wali {$nama} ({$kelas} - {$jurusan})\n" .
                            "Presensi masuk Ananda tercatat pukul {$jam}" .
                            ($ket !== '' ? " ({$ket})" : "");
                    } else {
                        $isiPesan =
                            "ℹ️ Halo Bpk/Ibu wali {$nama} ({$kelas} - {$jurusan})\n" .
                            "Status kehadiran Ananda: " . strtoupper($status) .
                            ($ket !== '' ? " ({$ket})" : "");
                    }

                } else {
                    $isiPesan =
                        "ℹ️ {$nama} melakukan absensi pulang pukul {$jam}" .
                        ($ket !== '' ? " ({$ket})" : "");
                }

                $pesanSudahAda = $this->messageModel
                    ->where('users_id', $siswa['users_id'])
                    ->where('jenis_pesan', $jenis)
                    ->where('waktu_kirim >=', $tanggal . ' 00:00:00')
                    ->where('waktu_kirim <=', $tanggal . ' 23:59:59')
                    ->first();

                if (!$pesanSudahAda) {
                    $this->messageModel->insert([
                        'users_id'    => $siswa['users_id'],
                        'jenis_pesan' => $jenis,
                        'isi_pesan'   => $isiPesan,
                        'waktu_kirim' => $tanggal . ' ' . $jam . ':00',
                        'status'      => 'pending'
                    ]);
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