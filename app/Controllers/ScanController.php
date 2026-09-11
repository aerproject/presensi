<?php

namespace App\Controllers;

use App\Controllers\BaseController;
use App\Models\PlotkelasModel;
use App\Models\AttendanceModel;
use App\Models\ParentModel;
use App\Models\MessageModel;
use CodeIgniter\API\ResponseTrait;

class ScanController extends BaseController
{
    use ResponseTrait;

    protected $helpers = ['form'];

    /**
     * Halaman utama scanner QR Code
     */
    public function index()
    {
        $data['title'] = 'QR Code Scanner';
        return view('scan/view', $data);
    }

    /**
     * Proses hasil scan QR Code
     */
    public function processScan()
    {
        try {
            $qrCode = trim($this->request->getJSON()->qr_code ?? '');

            if (!$qrCode) {
                return $this->respond(['status' => 'error', 'message' => 'QR code tidak dikirim.']);
            }

            $plotkelasModel  = new PlotkelasModel();
            $attendanceModel = new AttendanceModel();
            $parentModel     = new ParentModel();
            $messageModel    = new MessageModel();

            // Cari siswa berdasarkan QR code via plot_kelas
            $student = $plotkelasModel
                ->select('plot_kelas.id as plot_id, siswa.id as siswa_id, siswa.nama_siswa, kelas.nama_kelas, jurusan.nama_jurusan')
                ->join('siswa', 'siswa.id = plot_kelas.siswa_id')
                ->join('kelas', 'kelas.id = plot_kelas.kelas_id')
                ->join('jurusan', 'jurusan.id = kelas.jurusan_id', 'left')
                ->where('siswa.qr_code', $qrCode)
                ->first();

            if (!$student) {
                return $this->respond(['status' => 'error', 'message' => 'QR code tidak ditemukan.']);
            }

            $plotID   = $student['plot_id'];
            $siswaID  = $student['siswa_id'];
            $nama     = $student['nama_siswa'];
            $kelas    = $student['nama_kelas'];
            $jurusan  = $student['nama_jurusan'];

            $today     = date('Y-m-d');
            $now       = date('H:i:s');
            $jamMasuk  = '07:15:00';
            $jamPulang = '15:45:00';

            // Cek apakah sudah absen hari ini
            $absen = $attendanceModel
                ->where('plot_kelas_id', $plotID)
                ->where('tanggal', $today)
                ->first();

            if ($absen) {
                return $this->respond(['status' => 'error', 'message' => 'Siswa sudah melakukan absensi hari ini.']);
            }

            // Tentukan status
            if ($now <= $jamMasuk) {
                $status     = 'hadir';
                $keterangan = null;
            } elseif ($now > $jamMasuk && $now < $jamPulang) {
                $status     = 'hadir';
                $keterangan = 'terlambat';
            } else {
                $status     = 'alpha';
                $keterangan = 'tidak hadir';
            }

            // Simpan absensi
            $attendanceModel->insert([
                'plot_kelas_id' => $plotID,
                'tanggal'       => $today,
                'jam_masuk'     => $now,
                'status'        => $status,
                'keterangan'    => $keterangan
            ]);

            // Cari orang tua berdasarkan siswa_id
            $parent = $parentModel->where('siswa_id', $siswaID)->first();
            $waOrtu = $parent['wa_ortu'] ?? null;

            if ($waOrtu) {
                $jenisAbsen = ($status === 'alpha') ? 'absen gagal' : 'absen masuk';
                $pesan = "Halo, orang tua dari $nama ($kelas - $jurusan), siswa telah melakukan $jenisAbsen pada pukul $now.";

                $messageModel->insert([
                    'siswa_id'    => $siswaID,
                    'wa_ortu'     => $waOrtu,
                    'pesan'       => $pesan,
                    'waktu_kirim' => date('Y-m-d H:i:s')
                ]);

                // TODO: Integrasi API WhatsApp
                // sendWhatsApp($waOrtu, $pesan);
            }

            return $this->respond([
                'status'  => 'success',
                'message' => "$nama berhasil absen dengan status: $status" . ($keterangan ? " ($keterangan)" : "")
            ]);
        } catch (\Throwable $e) {
            return $this->respond([
                'status'  => 'error',
                'message' => 'Exception: ' . $e->getMessage()
            ]);
        }
    }
}
