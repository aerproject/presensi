<?php

namespace App\Services;

use App\Models\ParentModel;
use App\Models\SuratModel;
use App\Models\AttendanceModel;
use App\Models\MessageModel;
use App\Models\IzinModel;

class OrtuService
{
    protected $userId;

    public function __construct($userId)
    {
        $this->userId = $userId;
    }

    public function getAnak()
    {
        $db = \Config\Database::connect();

        return $db->table('siswa s')
            ->select("
                s.id AS id,
                s.id AS siswa_id,
                s.nama_siswa,
                s.nis,
                s.ortu_id,
                sa.id AS siswa_akademik_id,
                sa.kelas_id,
                sa.jurusan_id,
                sa.tapel_id,
                k.nama_kelas,
                j.nama_jurusan,
                t.aktif AS tapel_aktif
            ")
            ->join(
                'siswa_akademik sa',
                'sa.siswa_id = s.id',
                'inner'
            )
            ->join(
                'tapel t',
                't.id = sa.tapel_id',
                'inner'
            )
            ->join(
                'kelas k',
                'k.id = sa.kelas_id',
                'left'
            )
            ->join(
                'jurusan j',
                'j.id = sa.jurusan_id',
                'left'
            )
            ->where('s.ortu_id', function ($builder) {
                $builder
                    ->select('id')
                    ->from('parents')
                    ->where('users_id', $this->userId);
            })
            ->where('t.aktif', 1)
            ->orderBy('sa.id', 'DESC')
            ->get()
            ->getRowArray();
    }
    public function getDashboardData($request)
    {
        $anak = $this->getAnak();
        if (!$anak) return ['anak' => null];

        $attendanceModel = new AttendanceModel();
        $messageModel    = new MessageModel();
        $izinModel       = new IzinModel();
        $parentModel     = new ParentModel();

        $ortu = $parentModel->where('users_id', $this->userId)->first();
        $today = date('Y-m-d');

        /*
        |--------------------------------------------------------------------------
        | REKAP STATUS KEHADIRAN
        |--------------------------------------------------------------------------
        */
        $rekapRows = $attendanceModel
            ->select('status, COUNT(*) AS jumlah')
            ->where('siswa_akademik_id', $anak['siswa_akademik_id'])
            ->groupBy('status')
            ->findAll();

        $rekap = [
            'hadir' => 0,
            'izin'  => 0,
            'sakit' => 0,
            'alpha' => 0,
        ];

        foreach ($rekapRows as $row) {
            $status = strtolower(trim($row['status'] ?? ''));

            if (array_key_exists($status, $rekap)) {
                $rekap[$status] = (int) $row['jumlah'];
            }
        }

        $perPageAbsensi = $request->getGet('perPageAbsensi') ?? 5;
        $perPageMessage = $request->getGet('perPageMessage') ?? 5;
        $perPageIzin    = $request->getGet('perPageIzin') ?? 5;

        $perPageSurat = $request->getGet('perPageSurat') ?? 5;

        $suratModel = new SuratModel();

        $suratList = $suratModel
            ->where('siswa_id', $anak['siswa_id'])
            ->orderBy('created_at', 'DESC')
            ->paginate($perPageSurat, 'surat');

        $pagerSurat = $suratModel->pager;

        // Riwayat Pengajuan Izin untuk dashboard orang tua.
        // Sumber canonical: tabel surat.
        $riwayat_izin = [];

        foreach ($suratList as $surat) {
            $riwayat_izin[] = [
                'tanggal'    => $surat['tanggal_absensi'],
                'jenis'      => $surat['jenis'],
                'status'     => $surat['status'],
                'keterangan' => $surat['isi_pesan'],
            ];
        }

        return [
            // =====================================================
            // DATA IDENTITAS DASHBOARD ORANG TUA
            // =====================================================
            'namaOrtu'        => $ortu['nama_ortu'] ?? 'Orang Tua',
            'namaSiswa'       => $anak['nama_siswa'] ?? '-',
            'namaKelas'       => $anak['nama_kelas'] ?? '-',
            'namaJurusan'     => $anak['nama_jurusan'] ?? '-',

            // Data lengkap siswa aktif
            'anak'            => $anak,
            'rekap'            => $rekap,
            'absenHariIni'    => $attendanceModel
                                    ->where('siswa_akademik_id', $anak['siswa_akademik_id'])
                                    ->where('tanggal', $today)
                                    ->first(),
            'riwayat'         => $attendanceModel
                                    ->where('siswa_akademik_id', $anak['siswa_akademik_id'])
                                    ->orderBy('tanggal', 'DESC')
                                    ->paginate($perPageAbsensi, 'absensi'),
            'pagerAbsensi'    => $attendanceModel->pager,
            'perPageAbsensi'  => $perPageAbsensi,
            'riwayatMessage'  => $messageModel
                                    ->select('messages.*')
                                    ->join('users', 'users.id = messages.users_id')
                                    ->join('siswa', 'siswa.users_id = users.id')
                                    ->join('parents', 'parents.id = siswa.ortu_id')
                                    ->where('parents.users_id', $this->userId)
                                    ->orderBy('messages.waktu_kirim', 'DESC')
                                    ->paginate($perPageMessage, 'message'),
            'pagerMessage'    => $messageModel->pager,
            'perPageMessage'  => $perPageMessage,
            'izinList'        => $izinModel
                                    ->where('siswa_id', $anak['id'])
                                    ->orderBy('created_at', 'DESC')
                                    ->paginate($perPageIzin, 'izin'),
            'pagerIzin'       => $izinModel->pager,
            'perPageIzin'     => $perPageIzin,
            'suratList'       => $suratList,
            'pagerSurat'      => $pagerSurat,
            'perPageSurat'    => $perPageSurat,
            'riwayat_izin'    => $riwayat_izin,
            'statistik'       => $attendanceModel
                                    ->select("status, COUNT(*) as jumlah")
                                    ->where('siswa_akademik_id', $anak['siswa_akademik_id'])
                                    ->groupBy('status')
                                    ->findAll()
        ];
    }

    public function submitIzin($request)
    {
        $izinModel = new IzinModel();
        $anak = $this->getAnak();

        if (!$anak) {
            return ['status' => 'error', 'message' => 'Data anak tidak ditemukan.'];
        }

        $validation = \Config\Services::validation();
        $validation->setRules([
            'jenis_izin'   => 'required',
            'isi_pesan'    => 'required|min_length[10]',
            'upload_surat' => 'max_size[upload_surat,2048]|ext_in[upload_surat,pdf,jpg,png]'
        ]);

        if (!$validation->withRequest($request)->run()) {
            return [
                'status' => 'invalid',
                'errors' => $validation->getErrors()
            ];
        }

        $data = [
            'siswa_id'    => $anak['id'],
            'jenis_izin'  => $request->getPost('jenis_izin'),
            'isi_pesan'   => $request->getPost('isi_pesan'),
            'status_izin' => 'diajukan',
            'created_at'  => date('Y-m-d')
        ];

        $uploadSurat = $request->getFile('upload_surat');
        if ($uploadSurat && $uploadSurat->isValid() && !$uploadSurat->hasMoved()) {
            $newName = $uploadSurat->getRandomName();
            $uploadSurat->move(ROOTPATH . 'public/uploads/surat_izin', $newName);
            $data['upload_surat'] = $newName;
        }

        if ($izinModel->insert($data)) {
            return ['status' => 'success', 'message' => 'Pengajuan izin berhasil dikirim.'];
        } else {
            return ['status' => 'error', 'message' => 'Gagal mengirim pengajuan izin.'];
        }
    }
}
