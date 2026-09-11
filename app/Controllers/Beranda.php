<?php

namespace App\Controllers;

use App\Models\AttendanceModel;
use App\Models\SiswaAkademikModel;
use App\Models\AplikasiModel;

class Beranda extends BaseController
{
    protected $attendanceModel;
    protected $siswaAkademikModel;

    public function __construct()
    {
        $this->attendanceModel     = new AttendanceModel();
        $this->siswaAkademikModel = new SiswaAkademikModel();
    }

    /*
    ==========================================
    HALAMAN BERANDA
    ==========================================
    */
    public function index()
    {
        $aplikasiModel = new AplikasiModel();
        $pengaturan    = $aplikasiModel->first();

        $namaAplikasi = $pengaturan['nama_aplikasi'] ?? 'Absensi Digital';
        $namaSekolah  = $pengaturan['nama_sekolah'] ?? '';

        $today = date('Y-m-d');

        /*
        |--------------------------------------------------------------------------
        | Siswa yang sudah absen hari ini
        |--------------------------------------------------------------------------
        */
        $sudahAbsen = $this->attendanceModel
            ->select("
                attendance.*,
                siswa.nama_siswa
            ")
            ->join(
                'siswa_akademik',
                'siswa_akademik.id = attendance.siswa_akademik_id'
            )
            ->join(
                'siswa',
                'siswa.id = siswa_akademik.siswa_id'
            )
            ->where('attendance.tanggal', $today)
            ->findAll();

        $absenIds = array_column($sudahAbsen, 'siswa_akademik_id');

        /*
        |--------------------------------------------------------------------------
        | Siswa belum absen
        |--------------------------------------------------------------------------
        */
        $builder = $this->siswaAkademikModel
            ->select("
                siswa_akademik.id,
                siswa.nama_siswa
            ")
            ->join(
                'siswa',
                'siswa.id = siswa_akademik.siswa_id'
            )
            ->where('siswa_akademik.status_akademik_id', 1);

        if (!empty($absenIds)) {
            $builder->whereNotIn('siswa_akademik.id', $absenIds);
        }

        $belumAbsen = $builder->findAll();

        return view('beranda/index', [
            'belumAbsen'    => $belumAbsen,
            'sudahAbsen'    => $sudahAbsen,
            'namaAplikasi' => $namaAplikasi,
            'namaSekolah'  => $namaSekolah
        ]);
    }


    /*
    ==========================================
    RENDER ABSENSI BERDASARKAN STATUS
    ==========================================
    */

    /*
    |--------------------------------------------------------------------------
    | PUBLIC ABSENSI BERDASARKAN KONDISI HARI INI
    |--------------------------------------------------------------------------
    |
    | Aturan:
    |
    | 1. Izin
    |    Mengambil siswa aktif yang mempunyai surat jenis "izin"
    |    untuk tanggal_absensi hari ini.
    |
    | 2. Sakit
    |    Mengambil siswa aktif yang mempunyai surat jenis "sakit"
    |    untuk tanggal_absensi hari ini.
    |
    | 3. Alpha
    |    Mengambil seluruh siswa aktif pada siswa_akademik yang:
    |      - belum mempunyai presensi valid hari ini
    |      - tidak mempunyai surat izin/sakit hari ini
    |
    |    Attendance dengan status "alpha" tidak dianggap sebagai
    |    presensi valid sehingga siswa tetap tampil di halaman Alpha.
    |
    */

    private function renderPublicStatus(
        string $status,
        string $view,
        string $varName
    ) {
        $request = service('request');

        $nama = trim((string) $request->getGet('nama'));

        $perPage = (int) ($request->getGet('perPage') ?? 10);

        if ($perPage <= 0) {
            $perPage = 10;
        }

        $page = max(
            1,
            (int) ($request->getGet('page') ?? 1)
        );

        $today = date('Y-m-d');

        $db = \Config\Database::connect();

        /*
        |--------------------------------------------------------------------------
        | IZIN / SAKIT
        |--------------------------------------------------------------------------
        */
        if (in_array($status, ['izin', 'sakit'], true)) {

            $builder = $db->table('surat');

            $builder->select("
                surat.id,
                surat.tanggal_absensi,
                surat.jenis,
                surat.status AS status_surat,
                surat.isi_pesan,

                siswa.id AS siswa_id,
                siswa.nama_siswa,

                siswa_akademik.id AS siswa_akademik_id,

                kelas.nama_kelas,
                jurusan.nama_jurusan
            ");

            $builder->join(
                'siswa',
                'siswa.id = surat.siswa_id',
                'inner'
            );

            $builder->join(
                'siswa_akademik',
                'siswa_akademik.siswa_id = siswa.id',
                'inner'
            );

            $builder->join(
                'kelas',
                'kelas.id = siswa_akademik.kelas_id',
                'inner'
            );

            $builder->join(
                'jurusan',
                'jurusan.id = siswa_akademik.jurusan_id',
                'left'
            );

            $builder->join(
                'tapel',
                'tapel.id = siswa_akademik.tapel_id',
                'inner'
            );

            /*
            | Hanya siswa aktif pada tahun pelajaran aktif.
            */
            $builder->where(
                'siswa_akademik.status_akademik_id',
                1
            );

            $builder->where(
                'tapel.aktif',
                1
            );

            /*
            | Tanggal sebenarnya siswa tidak hadir.
            | BUKAN created_at surat.
            */
            $builder->where(
                'surat.tanggal_absensi',
                $today
            );

            $builder->where(
                'surat.jenis',
                $status
            );

            /*
            | Pengajuan dan yang sudah disetujui sama-sama
            | merupakan kabar dari orang tua.
            */
            $builder->whereIn(
                'surat.status',
                ['diajukan', 'disetujui']
            );

            if ($nama !== '') {
                $builder->like(
                    'siswa.nama_siswa',
                    $nama
                );
            }

            /*
            | Satu surat = satu baris.
            */
            $builder->groupBy(
                'surat.id'
            );

            $total = $builder->countAllResults(false);

            $offset = ($page - 1) * $perPage;

            $dataList = $builder
                ->orderBy('surat.id', 'DESC')
                ->limit($perPage, $offset)
                ->get()
                ->getResultArray();

        /*
        |--------------------------------------------------------------------------
        | ALPHA
        |--------------------------------------------------------------------------
        */
        } else {

            /*
            | Ambil seluruh siswa aktif pada siswa_akademik
            | dan tapel aktif.
            */
            $builder = $db->table('siswa_akademik');

            $builder->select("
                siswa_akademik.id AS siswa_akademik_id,
                siswa.id AS siswa_id,
                siswa.nama_siswa,

                kelas.nama_kelas,
                jurusan.nama_jurusan
            ");

            $builder->join(
                'siswa',
                'siswa.id = siswa_akademik.siswa_id',
                'inner'
            );

            $builder->join(
                'kelas',
                'kelas.id = siswa_akademik.kelas_id',
                'inner'
            );

            $builder->join(
                'jurusan',
                'jurusan.id = siswa_akademik.jurusan_id',
                'left'
            );

            $builder->join(
                'tapel',
                'tapel.id = siswa_akademik.tapel_id',
                'inner'
            );

            $builder->where(
                'siswa_akademik.status_akademik_id',
                1
            );

            $builder->where(
                'tapel.aktif',
                1
            );

            if ($nama !== '') {
                $builder->like(
                    'siswa.nama_siswa',
                    $nama
                );
            }

            $semuaSiswa = $builder
                ->orderBy(
                    'nama_siswa',
                    'ASC'
                )
                ->get()
                ->getResultArray();

            /*
            |--------------------------------------------------------------------------
            | ATTENDANCE VALID HARI INI
            |--------------------------------------------------------------------------
            |
            | Status alpha TIDAK dimasukkan ke daftar ini.
            |
            | Alasannya:
            | Jika CronController sudah membuat:
            |
            | attendance.status = alpha
            |
            | siswa tersebut tetap harus tampil sebagai Alpha.
            */
            $attendanceRows = $db->table('attendance')
                ->select('siswa_akademik_id')
                ->where(
                    'tanggal',
                    $today
                )
                ->where(
                    'status !=',
                    'alpha'
                )
                ->get()
                ->getResultArray();

            $attendanceIds = array_map(
                'intval',
                array_column(
                    $attendanceRows,
                    'siswa_akademik_id'
                )
            );

            /*
            |--------------------------------------------------------------------------
            | SURAT IZIN / SAKIT HARI INI
            |--------------------------------------------------------------------------
            |
            | Surat diajukan maupun disetujui dianggap sebagai
            | kabar sehingga siswa tidak dianggap Alpha.
            */
            $suratRows = $db->table('surat')
                ->select('siswa_id')
                ->where(
                    'tanggal_absensi',
                    $today
                )
                ->whereIn(
                    'jenis',
                    ['izin', 'sakit']
                )
                ->whereIn(
                    'status',
                    ['diajukan', 'disetujui']
                )
                ->get()
                ->getResultArray();

            $suratSiswaIds = array_map(
                'intval',
                array_column(
                    $suratRows,
                    'siswa_id'
                )
            );

            /*
            |--------------------------------------------------------------------------
            | BENTUK DATA ALPHA
            |--------------------------------------------------------------------------
            */
            $alphaList = [];

            foreach ($semuaSiswa as $row) {

                $siswaAkademikId =
                    (int) $row['siswa_akademik_id'];

                $siswaId =
                    (int) $row['siswa_id'];

                /*
                | Sudah melakukan presensi hari ini
                | => bukan Alpha.
                */
                if (
                    in_array(
                        $siswaAkademikId,
                        $attendanceIds,
                        true
                    )
                ) {
                    continue;
                }

                /*
                | Sudah mempunyai kabar izin/sakit hari ini
                | => bukan Alpha.
                */
                if (
                    in_array(
                        $siswaId,
                        $suratSiswaIds,
                        true
                    )
                ) {
                    continue;
                }

                $row['tanggal_absensi'] = $today;

                $row['isi_pesan'] =
                    'Belum melakukan presensi dan tidak ada kabar izin/sakit hari ini.';

                $row['status_surat'] = null;
                $row['jenis'] = 'alpha';

                $alphaList[] = $row;
            }

            $total = count($alphaList);

            $offset = ($page - 1) * $perPage;

            $dataList = array_slice(
                $alphaList,
                $offset,
                $perPage
            );
        }

        /*
        |--------------------------------------------------------------------------
        | PAGINATION
        |--------------------------------------------------------------------------
        */
        $pager = \Config\Services::pager();

        /*
        | Register pagination untuk view.
        */
        $pager->makeLinks(
            $page,
            $perPage,
            $total,
            'bootstrap'
        );

        return view(
            $view,
            [
                $varName => $dataList,
                'pager'   => $pager,
                'nama'    => $nama,
                'perPage' => $perPage,
                'offset'  => $offset,
                'today'   => $today
            ]
        );
    }


    public function izin()
    {
        return $this->renderPublicStatus(
            'izin',
            'beranda/izin',
            'izinList'
        );
    }


    public function sakit()
    {
        return $this->renderPublicStatus(
            'sakit',
            'beranda/sakit',
            'sakitList'
        );
    }


    public function alpha()
    {
        return $this->renderPublicStatus(
            'alpha',
            'beranda/alpha',
            'alpha'
        );
    }


    public function getStats()
    {
        $today = date('Y-m-d');

        $totalSiswa = $this->siswaAkademikModel
            ->where('status_akademik_id', 1)
            ->countAllResults();

        $hadir = $this->attendanceModel
            ->where([
                'tanggal' => $today,
                'status'  => 'hadir'
            ])
            ->countAllResults();

        $izin = $this->attendanceModel
            ->where([
                'tanggal' => $today,
                'status'  => 'izin'
            ])
            ->countAllResults();

        $sakit = $this->attendanceModel
            ->where([
                'tanggal' => $today,
                'status'  => 'sakit'
            ])
            ->countAllResults();

        $alpha = $totalSiswa - ($hadir + $izin + $sakit);

        return $this->response->setJSON([
            'totalSiswa' => $totalSiswa,
            'hadir'      => $hadir,
            'izin'       => $izin,
            'sakit'      => $sakit,
            'alpha'      => $alpha
        ]);
    }


    /*
    ==========================================
    API CHART
    ==========================================
    */
    public function chartData()
    {
        $today = date('Y-m-d');

        $totalSiswa = $this->siswaAkademikModel
            ->where('status_akademik_id', 1)
            ->countAllResults();

        $hadir = $this->attendanceModel
            ->where([
                'tanggal' => $today,
                'status'  => 'hadir'
            ])
            ->countAllResults();

        $izin = $this->attendanceModel
            ->where([
                'tanggal' => $today,
                'status'  => 'izin'
            ])
            ->countAllResults();

        $sakit = $this->attendanceModel
            ->where([
                'tanggal' => $today,
                'status'  => 'sakit'
            ])
            ->countAllResults();

        $alpha = $totalSiswa - ($hadir + $izin + $sakit);

        return $this->response->setJSON([
            'labels' => [date('d-m-Y')],
            'datasets' => [
                [
                    'label' => 'Hadir',
                    'data'  => [$hadir],
                    'backgroundColor' => '#28a745'
                ],
                [
                    'label' => 'Izin',
                    'data'  => [$izin],
                    'backgroundColor' => '#ffc107'
                ],
                [
                    'label' => 'Sakit',
                    'data'  => [$sakit],
                    'backgroundColor' => '#17a2b8'
                ],
                [
                    'label' => 'Alpha',
                    'data'  => [$alpha],
                    'backgroundColor' => '#dc3545'
                ]
            ],
            'totalSiswa'      => $totalSiswa,
            'totalHadir'      => $hadir,
            'totalTidakHadir' => $izin + $sakit + $alpha
        ]);
    }
}