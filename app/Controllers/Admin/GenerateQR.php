<?php

namespace App\Controllers\Admin;

use App\Controllers\BaseController;
use App\Models\PlotkelasModel;
use App\Models\QrCodeModel;
use Endroid\QrCode\Builder\Builder;
use Endroid\QrCode\Writer\PngWriter;
use Dompdf\Dompdf;

class GenerateQR extends BaseController
{
    public function index()
    {

        if ($response = $this->requireAdmin()) {
            return $response;
        }

        $qrModel = new QrCodeModel();

        // Hanya tampilkan kelas yang benar-benar memiliki
        // siswa aktif pada siswa_akademik di tapel aktif.
        $db = \Config\Database::connect();

        $tapel = $db->table('tapel')
            ->where('aktif', 1)
            ->get()
            ->getRowArray();

        $kelasList = [];

        if ($tapel) {
            $kelasList = $db->table('siswa_akademik')
                ->select('kelas.id, kelas.nama_kelas')
                ->join(
                    'kelas',
                    'kelas.id = siswa_akademik.kelas_id'
                )
                ->where(
                    'siswa_akademik.tapel_id',
                    $tapel['id']
                )
                ->where(
                    'siswa_akademik.status_akademik_id',
                    1
                )
                ->groupBy('kelas.id, kelas.nama_kelas')
                ->orderBy('kelas.nama_kelas', 'ASC')
                ->get()
                ->getResultArray();
        }

        // Ambil filter dari query string
        $kelasId = $this->request->getGet('kelas_id');
        $perPage = $this->request->getGet('perPage') ?? 10;

        // Ambil data QR Code dengan join ke tabel kelas agar nama_kelas ikut terbawa
        if ($kelasId) {
            $qrCodes = $qrModel->select('qrcodes.*, kelas.nama_kelas')
                ->join('kelas', 'kelas.id = qrcodes.kelas_id')
                ->where('qrcodes.kelas_id', $kelasId)
                ->paginate($perPage);
        } else {
            $qrCodes = $qrModel->select('qrcodes.*, kelas.nama_kelas')
                ->join('kelas', 'kelas.id = qrcodes.kelas_id')
                ->paginate($perPage);
        }

        return view('admin/qrcode/index', [
            'title'         => 'Generate QR Code',
            'kelasList'     => $kelasList,
            'qrCodes'       => $qrCodes,
            'pager'         => $qrModel->pager,
            'selectedKelas' => $kelasId,
            'perPage'       => $perPage
        ]);
    }





    public function generate()
    {

        if ($response = $this->requireAdmin()) {
            return $response;
        }

        $kelasId = $this->request->getPost('kelas_id');

        if (!$kelasId) {
            return redirect()->back()
                ->with('error', 'Kelas belum dipilih.');
        }

        $db = \Config\Database::connect();
        $qrModel = new QrCodeModel();

        /*
        |--------------------------------------------------------------------------
        | TAPEL AKTIF
        |--------------------------------------------------------------------------
        */
        $tapel = $db->table('tapel')
            ->where('aktif', 1)
            ->get()
            ->getRowArray();

        if (!$tapel) {
            return redirect()->back()
                ->with('error', 'Tapel aktif belum tersedia.');
        }

        /*
        |--------------------------------------------------------------------------
        | SISWA AKADEMIK AKTIF PADA KELAS
        |--------------------------------------------------------------------------
        */
        $siswaList = $db->table('siswa_akademik')
            ->select('
                siswa.id AS siswa_id,
                siswa.nis,
                siswa.nama_siswa,
                siswa_akademik.kelas_id
            ')
            ->join(
                'siswa',
                'siswa.id = siswa_akademik.siswa_id'
            )
            ->where(
                'siswa_akademik.tapel_id',
                $tapel['id']
            )
            ->where(
                'siswa_akademik.kelas_id',
                $kelasId
            )
            ->where(
                'siswa_akademik.status_akademik_id',
                1
            )
            ->orderBy(
                'siswa.nama_siswa',
                'ASC'
            )
            ->get()
            ->getResultArray();

        if (empty($siswaList)) {
            return redirect()->back()
                ->with(
                    'error',
                    'Belum ada siswa aktif yang ditempatkan pada kelas ini untuk tapel aktif.'
                );
        }

        /*
        |--------------------------------------------------------------------------
        | DIRECTORY
        |--------------------------------------------------------------------------
        */
        $dir = FCPATH . 'uploads/qrcodes/';

        if (!is_dir($dir)) {
            mkdir($dir, 0777, true);
        }

        $generatedCount = 0;

        /*
        |--------------------------------------------------------------------------
        | GENERATE QR
        |--------------------------------------------------------------------------
        */
        foreach ($siswaList as $siswa) {
            $nis = trim((string) $siswa['nis']);

            if ($nis === '') {
                continue;
            }

            $result = (new Builder(writer: new PngWriter(), data: $nis, size: 300, margin: 10))->build();

            $fileName = $dir .
                "kelas_{$kelasId}_{$nis}.png";

            $result->saveToFile($fileName);

            $exists = $qrModel
                ->where('nis', $nis)
                ->where('kelas_id', $kelasId)
                ->first();

            if (!$exists) {
                $qrModel->insert([
                    'nis'        => $nis,
                    'kelas_id'   => $kelasId,
                    'file_path'  => $fileName,
                    'created_at' => date('Y-m-d H:i:s')
                ]);

                $generatedCount++;
            }
        }

        if ($generatedCount > 0) {
            return redirect()
                ->to('/admin/qrcode')
                ->with(
                    'success',
                    "Berhasil generate {$generatedCount} QR Code untuk kelas ID {$kelasId}."
                );
        }

        return redirect()
            ->to('/admin/qrcode')
            ->with(
                'info',
                'QR Code sudah tersedia, tidak ada yang digenerate ulang.'
            );
    }




    public function download($kelasId)
    {

        if ($response = $this->requireAdmin()) {
            return $response;
        }

        $qrModel = new QrCodeModel();
        $data    = $qrModel->where('kelas_id', $kelasId)->findAll();

        if (!$data) {
            return redirect()->back()->with('error', 'QR Code belum digenerate untuk kelas ini.');
        }

        $zipDir = WRITEPATH . 'qrcodes/';
        if (!is_dir($zipDir)) {
            mkdir($zipDir, 0777, true);
        }

        $zipFile = $zipDir . "kelas_{$kelasId}.zip";
        $zip = new \ZipArchive();

        if ($zip->open($zipFile, \ZipArchive::CREATE | \ZipArchive::OVERWRITE) !== true) {
            return redirect()->back()->with('error', 'Gagal membuat file ZIP.');
        }

        foreach ($data as $qr) {
            if (file_exists($qr['file_path'])) {
                $zip->addFile($qr['file_path'], basename($qr['file_path']));
            }
        }
        $zip->close();

        return $this->response->download($zipFile, null)
            ->setFileName("kelas_{$kelasId}_qrcodes.zip");
    }

    public function printView()
    {

        if ($response = $this->requireAdmin()) {
            return $response;
        }

        $kelasId = $this->request->getGet('kelas_id');
        $qrModel = new QrCodeModel();

        $data = $qrModel->select('qrcodes.*, kelas.nama_kelas')
            ->join('kelas', 'kelas.id = qrcodes.kelas_id')
            ->where('qrcodes.kelas_id', $kelasId)
            ->findAll();

        if (!$data) {
            return redirect()->back()->with('error', 'QR Code belum digenerate untuk kelas ini.');
        }

        return view('admin/qrcode/print', ['qrCodes' => $data]);
    }





    public function exportPdf()
    {

        if ($response = $this->requireAdmin()) {
            return $response;
        }

        $kelasId = $this->request->getGet('kelas_id');
        $qrModel = new QrCodeModel();

        $data = $qrModel->where('kelas_id', $kelasId)->findAll();
        if (!$data) {
            return redirect()->back()->with('error', 'QR Code belum digenerate untuk kelas ini.');
        }

        $html = view('admin/qrcode/pdf', ['qrCodes' => $data]);

        $dompdf = new Dompdf();
        $dompdf->set_option('isRemoteEnabled', true); // ✅ agar bisa load gambar dari URL
        $dompdf->loadHtml($html);
        $dompdf->setPaper('A4', 'portrait');
        $dompdf->render();
        $dompdf->stream("kelas_{$kelasId}_qrcodes.pdf", ["Attachment" => true]);
    }
}
