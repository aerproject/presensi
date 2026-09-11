<?php

namespace App\Services;

use App\Models\StudentModel;
use App\Models\AttendanceModel;
use App\Models\MessageModel;
use App\Models\ParentModel;
use App\Models\IzinModel;

class OrtuDashboardService
{
    protected $userId;

    public function __construct($userId)
    {
        $this->userId = $userId;
    }

    public function getData($request)
    {
        $parentModel     = new ParentModel();
        $studentModel    = new StudentModel();
        $attendanceModel = new AttendanceModel();
        $messageModel    = new MessageModel();
        $izinModel       = new IzinModel();

        $ortu = $parentModel->where('users_id', $this->userId)->first();
        $namaOrtu = $ortu['nama_ortu'] ?? 'Orang Tua';

        $anak = $studentModel
            ->select('students.*, kelas.nama_kelas, jurusan.nama_jurusan')
            ->join('kelas', 'kelas.id = students.kelas_id', 'left')
            ->join('jurusan', 'jurusan.id = students.jurusan_id', 'left')
            ->where('ortu_id', $ortu['id'] ?? 0)
            ->first();

        if (!$anak) {
            return ['anak' => null];
        }

        $today = date('Y-m-d');
        $perPageAbsensi = $request->getGet('perPageAbsensi') ?? 5;
        $perPageMessage = $request->getGet('perPageMessage') ?? 5;
        $perPageIzin    = $request->getGet('perPageIzin') ?? 5;

        return [
            'namaOrtu'        => $namaOrtu,
            'anak'            => $anak,
            'absenHariIni'    => $attendanceModel
                                    ->where('siswa_id', $anak['id'])
                                    ->where('tanggal', $today)
                                    ->first(),
            'riwayat'         => $attendanceModel
                                    ->where('siswa_id', $anak['id'])
                                    ->orderBy('tanggal', 'DESC')
                                    ->paginate($perPageAbsensi, 'absensi'),
            'pagerAbsensi'    => $attendanceModel->pager,
            'perPageAbsensi'  => $perPageAbsensi,
            'riwayatMessage'  => $messageModel
                                    ->select('messages.*')
                                    ->join('users', 'users.id = messages.users_id')
                                    ->join('students', 'students.users_id = users.id')
                                    ->join('parents', 'parents.id = students.ortu_id')
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
            'statistik'       => $attendanceModel
                                    ->select("status, COUNT(*) as jumlah")
                                    ->where('siswa_id', $anak['id'])
                                    ->groupBy('status')
                                    ->findAll()
        ];
    }
}