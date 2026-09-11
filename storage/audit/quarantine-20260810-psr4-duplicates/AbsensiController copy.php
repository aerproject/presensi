<?php

namespace App\Controllers;

use App\Controllers\BaseController;
use App\Models\StudentModel;
use App\Models\AttendanceModel;
use App\Models\ParentModel;
use App\Models\MessageModel;
use App\Models\JamsekModel;
use App\Libraries\WhatsappSender;
use CodeIgniter\API\ResponseTrait;
use CodeIgniter\I18n\Time;

class AbsensiController extends BaseController
{
    use ResponseTrait;

    public function index()
    {
        $data['title'] = 'QR Code Scanner';
        return view('absensi/scan', $data);
    }

    public function proses()
    {
        $request   = $this->request->getJSON();
        $username  = trim($request->scan_user ?? '');

        $studentModel    = new StudentModel();
        $attendanceModel = new AttendanceModel();
        $parentModel     = new ParentModel();
        $messageModel    = new MessageModel();
        $jamsekModel     = new JamsekModel();
        $waSender        = new WhatsappSender();

        $now    = Time::now('Asia/Jakarta')->toTimeString();
        $today  = date('Y-m-d');

        $hariMap = [
            'Monday'    => 'Senin',
            'Tuesday'   => 'Selasa',
            'Wednesday' => 'Rabu',
            'Thursday'  => 'Kamis',
            'Friday'    => 'Jumat',
            'Saturday'  => 'Sabtu',
            'Sunday'    => 'Minggu'
        ];

        $hariIndo = $hariMap[date('l')];
        $jadwal   = $jamsekModel->where('hari', $hariIndo)->first();

        $jamMasuk  = $jadwal['jam_masuk'] ?? '07:00:00';
        $jamPulang = $jadwal['jam_pulang'] ?? '13:00:00';

        // ✅ Cari siswa berdasarkan username
        $siswa = $studentModel
            ->select('students.*, kelas.nama_kelas, jurusan.nama_jurusan, users.username')
            ->join('kelas', 'kelas.id = students.kelas_id')
            ->join('jurusan', 'jurusan.id = students.jurusan_id')
            ->join('users', 'users.id = students.users_id')
            ->where('users.username', $username)
            ->first();

        if (!$siswa) {
            return $this->respond([
                'status'  => 'error',
                'message' => 'Username tidak ditemukan.'
            ]);
        }

        $studentID = $siswa['id'];
        $kelas     = $siswa['nama_kelas'];
        $jurusan   = $siswa['nama_jurusan'];
        $nama      = $siswa['nama_siswa'];

        $absenHariIni = $attendanceModel
            ->where('siswa_id', $studentID)
            ->where('tanggal', $today)
            ->first();

        if (!$absenHariIni) {
            if ($now <= $jamMasuk) {
                $status     = 'hadir';
                $keterangan = 'tepat waktu';
            } elseif ($now < $jamPulang) {
                $status     = 'hadir';
                $terlambat  = (strtotime($now) - strtotime($jamMasuk)) / 60;
                $keterangan = 'terlambat ' . round($terlambat) . ' menit';
            } else {
                $status     = 'alpha';
                $keterangan = 'tidak hadir';
            }

            $attendanceModel->insert([
                'siswa_id'   => $studentID,
                'tanggal'    => $today,
                'jam_masuk'  => $now,
                'jam_pulang' => null,
                'status'     => $status,
                'keterangan' => $keterangan,
                'created_at' => date('Y-m-d H:i:s')
            ]);

            $pesan = "Absensi berhasil: $nama ($kelas - $jurusan) Status: $status absen pada pukul $now ($keterangan).";
        } elseif (!$absenHariIni['jam_pulang']) {
            if ($now < $jamPulang) {
                return $this->respond([
                    'status'  => 'error',
                    'message' => 'Absen pulang baru bisa dilakukan setelah pukul ' . $jamPulang
                ]);
            }

            $keterangan = ($now > $jamPulang) ? 'pulang terlambat' : 'pulang tepat waktu';
            $status     = $absenHariIni['status'];

            $attendanceModel->update($absenHariIni['id'], [
                'jam_pulang' => $now
            ]);

            $pesan = "Absensi pulang: $nama ($kelas - $jurusan) pada pukul $now dengan status: $status dan $keterangan.";
        } else {
            return $this->respond([
                'status'  => 'error',
                'message' => 'Siswa sudah melakukan absensi lengkap hari ini.'
            ]);
        }

        // 🔔 Kirim notifikasi ke orang tua via WhatsappSender
        $ortu = $parentModel->find($siswa['ortu_id']);
        if ($ortu && !empty($ortu['wa_ortu'])) {
            $jenis    = (!$absenHariIni || !$absenHariIni['jam_pulang']) ? 'masuk' : 'pulang';

            // ✅ Template pesan sesuai status + emoji
            switch ($status) {
                case 'izin':
                    $isiPesan = "📄 Halo, Bpk/Ibu wali dari $nama ($kelas - $jurusan).\n"
                        . "Hari ini Ananda tercatat ✅ *IZIN* tidak masuk sekolah pada tanggal $today.";
                    break;
                case 'sakit':
                    $isiPesan = "🤒 Halo, Bpk/Ibu wali dari $nama ($kelas - $jurusan).\n"
                        . "Hari ini Ananda tercatat 🤕 *SAKIT* dan tidak dapat mengikuti kegiatan belajar pada tanggal $today.";
                    break;
                case 'alpha':
                    $isiPesan = "❌ Halo, Bpk/Ibu wali dari $nama ($kelas - $jurusan).\n"
                        . "Hari ini Ananda tercatat ❌ *ALPHA* (tidak hadir tanpa keterangan) pada tanggal $today.";
                    break;
                case 'hadir':
                    if (strpos($keterangan, 'terlambat') !== false) {
                        $isiPesan = "⚠️ Halo, Bpk/Ibu wali dari $nama ($kelas - $jurusan).\n"
                            . "Ananda hadir namun ⚠️ *TERLAMBAT* pada pukul $now ($keterangan).";
                    } else {
                        $isiPesan = "✅ Halo, Bpk/Ibu wali dari $nama ($kelas - $jurusan).\n"
                            . "Ananda hadir ✅ *TEPAT WAKTU* pada pukul $now.";
                    }
                    break;
                default:
                    $isiPesan = "ℹ️ Halo, Bpk/Ibu wali dari $nama ($kelas - $jurusan).\n"
                        . "Ananda telah melakukan absen $jenis pada pukul $now ($keterangan).";
                    break;
            }

            // Cek apakah pesan untuk jenis ini sudah pernah dikirim hari ini
            $pesanSudahAda = $messageModel
                ->where('users_id', $siswa['users_id'])
                ->where('jenis_pesan', $jenis)
                ->where('DATE(waktu_kirim)', date('Y-m-d'))
                ->first();

            if (!$pesanSudahAda) {
                $messageId = $messageModel->insert([
                    'users_id'    => $siswa['users_id'],
                    'jenis_pesan' => $jenis,
                    'isi_pesan'   => $isiPesan,
                    'waktu_kirim' => date('Y-m-d H:i:s'),
                    'status'      => 'pending'
                ]);

                // ✅ Kirim pesan menggunakan library core WhatsappSender
                [$sent, $errorMessage] = $waSender->send($ortu['wa_ortu'], $isiPesan);

                // Update status pesan
                $messageModel->update($messageId, [
                    'status'        => $sent ? 'sent' : 'failed',
                    'error_message' => $errorMessage
                ]);
            }
        }

        return $this->respond([
            'status'  => 'success',
            'message' => $pesan
        ]);
    }
}
