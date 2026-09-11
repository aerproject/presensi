<?php

namespace App\Controllers;

use App\Controllers\BaseController;
use App\Models\AttendanceModel;
use App\Models\MessageModel;
use App\Libraries\WhatsappSender;
use CodeIgniter\I18n\Time;
use Config\Database;

class CronController extends BaseController
{
    /**
     * Tandai siswa aktif yang belum melakukan absensi
     * sebagai alpha setelah batas waktu yang ditentukan.
     *
     * Sumber siswa:
     * siswa_akademik + siswa + tapel aktif
     *
     * Notifikasi hanya dibuat sebagai pending message.
     * Pengiriman WhatsApp akan diproses oleh worker terpisah.
     */
    public function tandaiAlpha()
    {
        $time  = Time::now('Asia/Jakarta');
        $today = $time->toDateString();
        $now   = $time->toTimeString();
$db = Database::connect();

        $tapelAktif = $db->table('tapel')
            ->where('aktif', 1)
            ->get()
            ->getRowArray();

        if (!$tapelAktif) {
            return $this->response->setJSON([
                'status'  => 'error',
                'message' => 'Tapel aktif belum tersedia.'
            ]);
        }

        $attendanceModel = new AttendanceModel();
        $messageModel    = new MessageModel();

        $siswaBelumAbsen = $db->table('siswa_akademik')
            ->select('
                siswa_akademik.id AS siswa_akademik_id,
                siswa.id AS siswa_id,
                siswa.nama_siswa,
                siswa.users_id,
                siswa.ortu_id,
                kelas.nama_kelas,
                jurusan.nama_jurusan,
                jam_belajar.jam_masuk,
                parents.wa_ortu
            ')
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
            )
              ->join(
                  'jam_belajar',
                  'jam_belajar.id = siswa_akademik.jam_belajar_id',
                  'inner'
              )

            ->join(
                'parents',
                'parents.id = siswa.ortu_id',
                'left'
            )
            ->where(
                'siswa_akademik.tapel_id',
                $tapelAktif['id']
            )
            ->where(
                'siswa_akademik.status_akademik_id',
                1
            )
            /*
             |--------------------------------------------------------------------------
             | SUDAH MEMILIKI ATTENDANCE HARI INI
             |--------------------------------------------------------------------------
             |
             | Semua status attendance hari ini dianggap sudah diproses.
             | Termasuk alpha agar tidak terjadi insert alpha ganda.
             |
             */
            ->whereNotIn(
                'siswa_akademik.id',
                function ($builder) use ($today) {
                    return $builder
                        ->select('siswa_akademik_id')
                        ->from('attendance')
                        ->where('tanggal', $today);
                }
            )

            /*
             |--------------------------------------------------------------------------
             | SURAT IZIN / SAKIT YANG SUDAH DISETUJUI
             |--------------------------------------------------------------------------
             |
             | Surat yang sudah disetujui adalah alasan ketidakhadiran yang sah.
             |
             | Surat:
             | - diajukan
             | - ditangguhkan
             | - ditolak
             |
             | TIDAK dikecualikan dari Alpha.
             |
             */
            ->whereNotIn(
                'siswa_akademik.siswa_id',
                function ($builder) use ($today) {
                    return $builder
                        ->select('siswa_id')
                        ->from('surat')
                        ->where('tanggal_absensi', $today)
                        ->whereIn('jenis', ['izin', 'sakit'])
                        ->where('status', 'disetujui');
                }
            )

            ->orderBy('kelas.nama_kelas', 'ASC')
            ->orderBy('siswa.nama_siswa', 'ASC')
            ->get()
            ->getResultArray();

        $totalAlpha      = 0;
        $totalNotification = 0;

        foreach ($siswaBelumAbsen as $siswa) {
            
            /*
            |--------------------------------------------------------------------------
            | JAM MASUK PER SISWA
            |--------------------------------------------------------------------------
            | Alpha hanya dibuat setelah jam masuk siswa tercapai.
            */

            $jamMasukSiswa = trim((string) ($siswa['jam_masuk'] ?? ''));

            if (
                $jamMasukSiswa === '' ||
                $now < $jamMasukSiswa
            ) {
                continue;
            }

            $attendanceData = [
                'siswa_akademik_id' => $siswa['siswa_akademik_id'],
                'tanggal'           => $today,
                'jam_masuk'         => null,
                'jam_pulang'        => null,
                'status'            => 'alpha',
                'keterangan'        => 'tidak hadir'
            ];

            $inserted = $attendanceModel->insert($attendanceData);

            if ($inserted === false) {
                // Bisa terjadi apabila request lain sudah lebih dulu
                // membuat attendance untuk siswa + tanggal yang sama.
                continue;
            }

            $totalAlpha++;

            if (
                !empty($siswa['users_id']) &&
                !empty($siswa['wa_ortu'])
            ) {
                $isiPesan =
                    "Halo, orang tua dari {$siswa['nama_siswa']} " .
                    "({$siswa['nama_kelas']} - {$siswa['nama_jurusan']}), " .
                    "siswa belum melakukan absensi hingga jam masuk {$jamMasukSiswa} " .
                    "dan dinyatakan tidak hadir (alpha).";

                $pesanSudahAda = $messageModel
                    ->where(
                        'users_id',
                        $siswa['users_id']
                    )
                    ->where(
                        'jenis_pesan',
                        'alpha'
                    )
                    ->where(
                        'waktu_kirim >=',
                        $today . ' 00:00:00'
                    )
                    ->where(
                        'waktu_kirim <=',
                        $today . ' 23:59:59'
                    )
                    ->first();

                if (!$pesanSudahAda) {
                    $messageModel->insert([
                        'users_id'    => $siswa['users_id'],
                        'jenis_pesan' => 'alpha',
                        'isi_pesan'   => $isiPesan,
                        'waktu_kirim' => date('Y-m-d H:i:s'),
                        'status'      => 'pending'
                    ]);

                    $totalNotification++;
                }
            }
        }

        return $this->response->setJSON([
            'status'              => 'success',
            'message'             => "{$totalAlpha} siswa ditandai alpha.",
            'notifications_queued' => $totalNotification
        ]);
    }
    /**
     * Proses queue WhatsApp yang masih berstatus pending.
     *
     * Tahap awal sengaja dibatasi maksimal 1 pesan
     * untuk pengujian integrasi MakeSender.
     */
    public function processPending()
    {
        $cronSecret = (string) env('CRON_WORKER_SECRET');
        $requestSecret = (string) ($this->request->getGet('key') ?? '');

        if ($cronSecret === '' || !hash_equals($cronSecret, $requestSecret)) {
            return $this->response
                ->setStatusCode(403)
                ->setJSON([
                    'status' => 'error',
                    'message' => 'Unauthorized worker request.'
                ]);
        }

        $limit = (int) ($this->request->getGet('limit') ?? 20);

        // Batas aman jumlah pesan per sekali proses.
        if ($limit < 1) {
            $limit = 1;
        }

        if ($limit > 50) {
            $limit = 50;
        }

        $db = Database::connect();

        $messages = $db->table('messages m')
            ->select('
                m.id,
                m.users_id,
                m.jenis_pesan,
                m.isi_pesan,
                m.status,
                s.nama_siswa,
                s.ortu_id,
                p.wa_ortu
            ')
            ->join(
                'siswa s',
                's.users_id = m.users_id',
                'left'
            )
            ->join(
                'parents p',
                'p.id = s.ortu_id',
                'left'
            )
            ->where('m.status', 'pending')
            ->orderBy('m.id', 'ASC')
            ->limit($limit)
            ->get()
            ->getResultArray();

        $sender = new WhatsappSender();

        $processed = 0;
        $sent      = 0;
        $failed    = 0;
        $results   = [];

        foreach ($messages as $message) {
            $processed++;

            $messageId = (int) $message['id'];
            $phone     = trim((string) ($message['wa_ortu'] ?? ''));
            $text      = (string) ($message['isi_pesan'] ?? '');

            if ($phone === '') {
                $messageModel = new MessageModel();
                $messageModel->updateStatus($messageId, 'failed');

                $failed++;

                $results[] = [
                    'id'      => $messageId,
                    'status'  => 'failed',
                    'message' => 'Nomor WhatsApp wali tidak tersedia.'
                ];

                continue;
            }

            [$success, $error] = $sender->send($phone, $text);

            $messageModel = new MessageModel();

            if ($success) {
                $messageModel->updateStatus($messageId, 'sent');

                $sent++;

                $results[] = [
                    'id'     => $messageId,
                    'status' => 'sent'
                ];
            } else {
                $messageModel->updateStatus($messageId, 'failed');

                $failed++;

                $results[] = [
                    'id'      => $messageId,
                    'status'  => 'failed',
                    'message' => $error
                ];
            }
        }

        return $this->response->setJSON([
            'status'    => 'success',
            'limit'     => $limit,
            'processed' => $processed,
            'sent'      => $sent,
            'failed'    => $failed,
            'remaining' => (new MessageModel())
                ->where('status', 'pending')
                ->countAllResults(),
            'results'   => $results
        ]);
    }

}
