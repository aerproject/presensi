<?php

namespace App\Controllers;

use App\Controllers\BaseController;
use App\Models\SiswaAkademikModel;
use App\Models\AttendanceModel;
use App\Models\MessageModel;
use App\Libraries\WhatsappSender;
use App\Models\AplikasiModel;
use CodeIgniter\API\ResponseTrait;
use CodeIgniter\I18n\Time;

class AbsensiController extends BaseController
{
    use ResponseTrait;

    public function index()
    {
        $aplikasiModel = new AplikasiModel();
        $pengaturan    = $aplikasiModel->first();

        $data['title']          = 'QR Code Scanner';
        $data['nama_aplikasi'] = $pengaturan['nama_aplikasi'] ?? 'Absensi Digital';
        $data['nama_sekolah']  = $pengaturan['nama_sekolah'] ?? '';

        return view('absensi/scan', $data);
    }

    public function proses()
    {
        $username = trim($this->request->getVar('scan_user'));

        if ($username === '') {
            return $this->respond([
                'status'  => 'error',
                'message' => 'Data scan tidak valid atau kosong.'
            ]);
        }

        $siswaAkademikModel = new SiswaAkademikModel();
        $attendanceModel    = new AttendanceModel();
        $messageModel       = new MessageModel();
        $aplikasiModel      = new AplikasiModel();

        $pengaturan = $aplikasiModel->first();

        $time  = Time::now('Asia/Jakarta');
        $now   = $time->toTimeString();
        $today = $time->toDateString();

        /*
        |--------------------------------------------------------------------------
        | VALIDASI HARI KERJA
        |--------------------------------------------------------------------------
        */
        $db = \Config\Database::connect();

        $hariKerjaRows = $db->table('hari_kerja')
            ->where('aktif', 1)
            ->get()
            ->getResultArray();

        $hariKerja = array_map(function ($row) {
            return trim($row['hari']);
        }, $hariKerjaRows);

        $hariMap = [
            'Monday'    => 'Senin',
            'Tuesday'   => 'Selasa',
            'Wednesday' => 'Rabu',
            'Thursday'  => 'Kamis',
            'Friday'    => 'Jumat',
            'Saturday'  => 'Sabtu',
            'Sunday'    => 'Minggu'
        ];

        $hariIndo = $hariMap[$time->format('l')] ?? null;

        if (!$hariIndo || !in_array($hariIndo, $hariKerja)) {
            return $this->respond([
                'status'  => 'error',
                'message' => "Hari {$hariIndo} bukan hari kerja."
            ]);
        }

        /*
        |--------------------------------------------------------------------------
        | CARI SISWA BERDASARKAN USERNAME QR
        |--------------------------------------------------------------------------
        */
        $siswa = $siswaAkademikModel
            ->select("
                siswa_akademik.id as siswa_akademik_id,

                jam_belajar.jam_masuk,
                jam_belajar.jam_pulang,

                siswa.nama_siswa,
                siswa.ortu_id,

                kelas.nama_kelas,
                jurusan.nama_jurusan,

                users.username,
                users.id as users_id,

                tapel.aktif
            ")
            ->join('siswa', 'siswa.id = siswa_akademik.siswa_id')
            ->join('kelas', 'kelas.id = siswa_akademik.kelas_id')
            ->join('jurusan', 'jurusan.id = siswa_akademik.jurusan_id', 'left')
            ->join('jam_belajar', 'jam_belajar.id = siswa_akademik.jam_belajar_id')
            ->join('users', 'users.id = siswa.users_id')
            ->join('tapel', 'tapel.id = siswa_akademik.tapel_id')
            ->where('users.username', $username)
            ->where('siswa_akademik.status_akademik_id', 1)
            ->where('tapel.aktif', 1)
            ->first();

        if (!$siswa) {
            return $this->respond([
                'status'  => 'error',
                'message' => 'Siswa tidak ditemukan atau tidak aktif.'
            ]);
        }

        /*
        |--------------------------------------------------------------------------
        | VALIDASI TAPEL AKTIF
        |--------------------------------------------------------------------------
        */
        if ((int)$siswa['aktif'] !== 1) {
            return $this->respond([
                'status'  => 'error',
                'message' => 'Tahun pelajaran tidak aktif.'
            ]);
        }

        $jamMasuk  = $siswa['jam_masuk'];
        $jamPulang = $siswa['jam_pulang'];

        $batasMasukSebelum = max(
            0,
            (int) ($pengaturan['batas_masuk_sebelum'] ?? 60)
        );

        $batasMasukSesudah = max(
            0,
            (int) ($pengaturan['batas_masuk_sesudah'] ?? 120)
        );

        $batasPulangSebelum = max(
            0,
            (int) ($pengaturan['batas_pulang_sebelum'] ?? 0)
        );

        $batasPulangSesudah = max(
            0,
            (int) ($pengaturan['batas_pulang_sesudah'] ?? 180)
        );

        if (empty($jamMasuk) || empty($jamPulang)) {
            return $this->respond([
                'status'  => 'error',
                'message' => 'Jadwal belajar belum diatur.'
            ]);
        }

        /*
        |--------------------------------------------------------------------------
        | CEK ABSENSI HARI INI
        |--------------------------------------------------------------------------
        */
        $absenHariIni = $attendanceModel
            ->where('siswa_akademik_id', $siswa['siswa_akademik_id'])
            ->where('tanggal', $today)
            ->first();

        $nama    = $siswa['nama_siswa'];
        $kelas   = $siswa['nama_kelas'];
        $jurusan = $siswa['nama_jurusan'];

        /*
        |--------------------------------------------------------------------------
        | ABSEN MASUK
        |--------------------------------------------------------------------------
        */
        /*
        |--------------------------------------------------------------------------
        | ALPHA -> HADIR
        |--------------------------------------------------------------------------
        | Jika Cron sudah membuat record Alpha untuk hari ini,
        | scan QR berikutnya meng-update record yang sama menjadi Hadir.
        */
        if ($absenHariIni && $absenHariIni['status'] === 'alpha') {

            $batasAwalMasuk = date(
                'H:i:s',
                strtotime($jamMasuk . ' -' . $batasMasukSebelum . ' minutes')
            );

            $batasAkhirMasuk = date(
                'H:i:s',
                strtotime($jamMasuk . ' +' . $batasMasukSesudah . ' minutes')
            );

            if ($now < $batasAwalMasuk || $now > $batasAkhirMasuk) {
                return $this->respond([
                    'status'  => 'error',
                    'message' => 'Absen masuk hanya bisa dilakukan antara '
                        . $batasAwalMasuk . ' sampai ' . $batasAkhirMasuk
                ]);
            }

            $terlambat = max(
                0,
                (strtotime($now) - strtotime($jamMasuk)) / 60
            );

            $keterangan = $terlambat > 0
                ? 'terlambat ' . round($terlambat) . ' menit'
                : 'tepat waktu';

            $updated = $attendanceModel->update($absenHariIni['id'], [
                'jam_masuk'  => $now,
                'status'     => 'hadir',
                'keterangan' => $keterangan,
                'updated_at' => date('Y-m-d H:i:s')
            ]);

            if ($updated === false) {
                return $this->respond([
                    'status'  => 'error',
                    'message' => 'Gagal mengubah status Alpha menjadi Hadir.'
                ]);
            }

            $pesan = "Absensi berhasil: {$nama} ({$kelas} - {$jurusan}) status hadir pukul {$now} ({$keterangan})";
            $jenis = 'masuk';

        } elseif (!$absenHariIni) {

            $batasAwalMasuk = date(
                'H:i:s',
                strtotime($jamMasuk . ' -' . $batasMasukSebelum . ' minutes')
            );

            $batasAkhirMasuk = date(
                'H:i:s',
                strtotime($jamMasuk . ' +' . $batasMasukSesudah . ' minutes')
            );

            if ($now < $batasAwalMasuk || $now > $batasAkhirMasuk) {
                return $this->respond([
                    'status'  => 'error',
                    'message' => 'Absen masuk hanya bisa dilakukan antara '
                        . $batasAwalMasuk . ' sampai ' . $batasAkhirMasuk
                ]);
            }

            if ($now <= $jamMasuk) {
                $status     = 'hadir';
                $keterangan = 'tepat waktu';
            } else {
                $status     = 'hadir';
                $terlambat  = (strtotime($now) - strtotime($jamMasuk)) / 60;
                $keterangan = 'terlambat ' . round($terlambat) . ' menit';
            }

            try {
                $inserted = $attendanceModel->insert([
                    'siswa_akademik_id' => $siswa['siswa_akademik_id'],
                    'tanggal'           => $today,
                    'jam_masuk'         => $now,
                    'jam_pulang'        => null,
                    'status'            => $status,
                    'keterangan'        => $keterangan,
                    'created_at'        => date('Y-m-d H:i:s')
                ]);

                if ($inserted === false) {
                    $existing = $attendanceModel
                        ->where('siswa_akademik_id', $siswa['siswa_akademik_id'])
                        ->where('tanggal', $today)
                        ->first();

                    if ($existing) {
                        return $this->respond([
                            'status'  => 'error',
                            'message' => 'Absensi hari ini sudah tercatat.'
                        ]);
                    }

                    return $this->respond([
                        'status'  => 'error',
                        'message' => 'Gagal menyimpan absensi.'
                    ]);
                }
            } catch (\Throwable $e) {
                // Request lain mungkin sudah lebih dulu membuat
                // record attendance pada siswa + tanggal yang sama.
                $existing = $attendanceModel
                    ->where('siswa_akademik_id', $siswa['siswa_akademik_id'])
                    ->where('tanggal', $today)
                    ->first();

                if ($existing) {
                    return $this->respond([
                        'status'  => 'error',
                        'message' => 'Absensi hari ini sudah tercatat.'
                    ]);
                }

                throw $e;
            }

            $pesan = "Absensi berhasil: {$nama} ({$kelas} - {$jurusan}) status {$status} pukul {$now} ({$keterangan})";
            $jenis = 'masuk';
        }

        /*
        |--------------------------------------------------------------------------
        | ABSEN PULANG
        |--------------------------------------------------------------------------
        */
        elseif (!$absenHariIni['jam_pulang']) {

            $batasAwalPulang = date(
                'H:i:s',
                strtotime($jamPulang . ' -' . $batasPulangSebelum . ' minutes')
            );

            $batasAkhirPulang = date(
                'H:i:s',
                strtotime($jamPulang . ' +' . $batasPulangSesudah . ' minutes')
            );

            if ($now < $batasAwalPulang || $now > $batasAkhirPulang) {
                return $this->respond([
                    'status'  => 'error',
                    'message' => 'Absen pulang hanya bisa dilakukan antara '
                        . $batasAwalPulang . ' sampai ' . $batasAkhirPulang
                ]);
            }

            if ($now < $jamPulang) {
                $keterangan = 'pulang lebih awal';
            } elseif ($now > $jamPulang) {
                $keterangan = 'pulang terlambat';
            } else {
                $keterangan = 'pulang tepat waktu';
            }

            $status = $absenHariIni['status'];

            $attendanceModel->update($absenHariIni['id'], [
                'jam_pulang' => $now
            ]);

            $pesan = "Absensi pulang: {$nama} ({$kelas} - {$jurusan}) pukul {$now} ({$keterangan})";
            $jenis = 'pulang';
        }

        /*
        |--------------------------------------------------------------------------
        | SUDAH ABSEN LENGKAP
        |--------------------------------------------------------------------------
        */
        else {
            return $this->respond([
                'status'  => 'error',
                'message' => 'Siswa sudah melakukan absensi lengkap hari ini.'
            ]);
        }

        /*
        |--------------------------------------------------------------------------
        | KIRIM WHATSAPP ORANG TUA
        |--------------------------------------------------------------------------
        */
        /*
        |--------------------------------------------------------------------------
        | QUEUE NOTIFIKASI WHATSAPP
        |--------------------------------------------------------------------------
        |
        | Scanner tidak lagi menunggu gateway WhatsApp.
        | Pesan hanya dimasukkan ke queue dengan status pending.
        | Message Worker menangani pengiriman secara asynchronous.
        |
        */
        if (!empty($siswa['users_id'])) {

            if ($jenis === 'masuk') {

                if (strpos($keterangan, 'terlambat') !== false) {
                    $isiPesan =
                        "⚠️ Halo Bpk/Ibu wali {$nama} ({$kelas} - {$jurusan})\n" .
                        "Ananda hadir tetapi terlambat pada pukul {$now} ({$keterangan})";
                } else {
                    $isiPesan =
                        "✅ Halo Bpk/Ibu wali {$nama} ({$kelas} - {$jurusan})\n" .
                        "Ananda hadir tepat waktu pada pukul {$now}";
                }

            } else {
                $isiPesan =
                    "ℹ️ {$nama} melakukan absensi pulang pukul {$now}";
            }

            $pesanSudahAda = $messageModel
                ->where('users_id', $siswa['users_id'])
                ->where('jenis_pesan', $jenis)
                ->where('waktu_kirim >=', $today . ' 00:00:00')
                ->where('waktu_kirim <=', $today . ' 23:59:59')
                ->first();

            if (!$pesanSudahAda) {
                $messageId = $messageModel->insert([
                    'users_id'    => $siswa['users_id'],
                    'jenis_pesan' => $jenis,
                    'isi_pesan'   => $isiPesan,
                    'waktu_kirim' => date('Y-m-d H:i:s'),
                    'status'      => 'pending'
                ]);

                // WhatsApp TIDAK dikirim langsung dari scanner.
                // Pesan sudah masuk queue dengan status pending.
                // Worker akan mengirimkannya secara asynchronous.
                if ($messageId) {
                    log_message(
                        'info',
                        'WhatsApp notification queued untuk message ID {id}',
                        ['id' => $messageId]
                    );
                }
            }
        }

                /*
        |--------------------------------------------------------------------------
        | RESPONSE
        |--------------------------------------------------------------------------
        */
        return $this->respond([
            'status'  => 'success',
            'message' => $pesan
        ]);
    }
}