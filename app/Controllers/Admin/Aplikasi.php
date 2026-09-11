<?php

namespace App\Controllers\Admin;

use App\Controllers\BaseController;
use App\Models\AplikasiModel;
use App\Models\TapelModel;
use App\Models\HariKerjaModel;
use App\Models\JamBelajarModel;

class Aplikasi extends BaseController
{
    protected $aplikasiModel;
    protected $hariKerjaModel;
    protected $jamBelajarModel;

    public function __construct()
    {
        $this->aplikasiModel  = new AplikasiModel();
        $this->hariKerjaModel = new HariKerjaModel();
        $this->jamBelajarModel = new JamBelajarModel();
    }

    /*
    |--------------------------------------------------------------------------
    | HALAMAN PENGATURAN
    |--------------------------------------------------------------------------
    */
    public function index()
    {

        if ($response = $this->requireAdmin()) {
            return $response;
        }

        $tapelModel = new TapelModel();

        return view('admin/pengaturan/index', [
            'pengaturan' => $this->aplikasiModel->first(),

            'tapelList' => $tapelModel
                ->orderBy('tahun_pelajaran', 'ASC')
                ->findAll(),

            'hariKerja' => $this->hariKerjaModel
                ->orderBy('id', 'ASC')
                ->findAll(),

            'jamBelajar' => $this->jamBelajarModel
                ->orderBy('id', 'ASC')
                ->findAll()
        ]);
    }


    /*
    |--------------------------------------------------------------------------
    | UPDATE PENGATURAN
    |--------------------------------------------------------------------------
    */
    public function update()
    {

        if ($response = $this->requireAdmin()) {
            return $response;
        }

        $db = \Config\Database::connect();

        $id = $this->request->getPost('id') ?? 1;

        /*
        ============================================================
        UPDATE TABEL PENGATURAN
        ============================================================
        */

        $pengaturanData = [
            'nama_aplikasi'        => $this->request->getPost('nama_aplikasi'),
            'nama_sekolah'         => $this->request->getPost('nama_sekolah'),
            'tahun_pelajaran'      => $this->request->getPost('tahun_pelajaran'),
            'batas_masuk_sebelum'  => max(0, (int) ($this->request->getPost('batas_masuk_sebelum') ?? 60)),
            'batas_masuk_sesudah'  => max(0, (int) ($this->request->getPost('batas_masuk_sesudah') ?? 120)),
            'batas_pulang_sebelum' => max(0, (int) ($this->request->getPost('batas_pulang_sebelum') ?? 0)),
            'batas_pulang_sesudah' => max(0, (int) ($this->request->getPost('batas_pulang_sesudah') ?? 180))
        ];

        $db->table('pengaturan')
            ->where('id', $id)
            ->update($pengaturanData);


        /*
        ============================================================
        UPDATE HARI KERJA
        ============================================================
        */

        $hariDipilih = $this->request->getPost('hari_kerja') ?? [];

        // reset semua hari nonaktif
        $db->table('hari_kerja')->update([
            'aktif' => 0
        ]);

        // aktifkan hari dipilih
        foreach ($hariDipilih as $hari) {

            $db->table('hari_kerja')
                ->where('hari', $hari)
                ->update([
                    'aktif' => 1
                ]);
        }


        /*
        ============================================================
        UPDATE JAM BELAJAR
        ============================================================
        */

        $jamBelajar = $this->request->getPost('jam_belajar');

        if (!empty($jamBelajar)) {

            foreach ($jamBelajar as $idJam => $jam) {

                $db->table('jam_belajar')
                    ->where('id', $idJam)
                    ->update([
                        'jam_masuk'  => $jam['jam_masuk'],
                        'jam_pulang' => $jam['jam_pulang']
                    ]);
            }
        }


        /*
        ============================================================
        UPLOAD LOGO SEKOLAH
        ============================================================
        */

        $logo = $this->request->getFile('logo_sekolah');

        if ($logo && $logo->isValid() && !$logo->hasMoved()) {

            $fileName = uniqid('logo_') . '.' . $logo->getExtension();

            $logo->move(
                ROOTPATH . 'public/uploads/logo',
                $fileName
            );

            $db->table('pengaturan')
                ->where('id', $id)
                ->update([
                    'logo_sekolah' => $fileName
                ]);
        }


        /*
        ============================================================
        UPLOAD KOP SURAT
        ============================================================
        */

        $kop = $this->request->getFile('kop_surat');

        if ($kop && $kop->isValid() && !$kop->hasMoved()) {

            $fileName = uniqid('kop_') . '.' . $kop->getExtension();

            $kop->move(
                ROOTPATH . 'public/uploads/kop',
                $fileName
            );

            $db->table('pengaturan')
                ->where('id', $id)
                ->update([
                    'kop_surat' => $fileName
                ]);
        }


        return redirect()
            ->to('/admin/aplikasi')
            ->with('success', 'Pengaturan berhasil disimpan.');
    }

    
    // ✅ GET: tampilkan form upload
    public function upload()
    {

        if ($response = $this->requireAdmin()) {
            return $response;
        }

        return view('admin/pengaturan/upload'); // view form upload CSV/Excel
    }

    // ✅ POST: proses file upload CSV
    public function process_upload()
    {

        if ($response = $this->requireAdmin()) {
            return $response;
        }

        $file = $this->request->getFile('csv_file');

        if (!$file || !$file->isValid()) {
            return redirect()->back()
                ->with('error', 'File CSV tidak valid.');
        }

        $handle = fopen($file->getTempName(), 'r');

        if ($handle === false) {
            return redirect()->back()
                ->with('error', 'File CSV tidak dapat dibaca.');
        }

        $header = fgetcsv($handle, 0, ';');

        if ($header === false || empty($header)) {
            fclose($handle);

            return redirect()->back()
                ->with('error', 'Header CSV tidak ditemukan.');
        }

        $header = array_map(function ($h) {
            $h = preg_replace('/^\xEF\xBB\xBF/', '', (string) $h);
            return strtolower(trim($h));
        }, $header);

        $requiredHeaders = [
            'nis',
            'password',
            'email',
            'nama_siswa',
            'wa_siswa',
            'tahun_masuk',
            'nama_ortu',
            'wa_ortu'
        ];

        $missingHeaders = array_values(
            array_diff($requiredHeaders, $header)
        );

        if (!empty($missingHeaders)) {
            fclose($handle);

            return redirect()->back()->with(
                'error',
                'Header CSV tidak lengkap: ' .
                implode(', ', $missingHeaders)
            );
        }

        $db = \Config\Database::connect();

        /*
        |--------------------------------------------------------------------------
        | PREFLIGHT: baca seluruh file dulu
        |--------------------------------------------------------------------------
        */
        $rows = [];
        $invalidRows = [];
        $seenNis = [];

        $lineNumber = 1;

        while (($line = fgetcsv($handle, 0, ';')) !== false) {
            $lineNumber++;

            if (empty(array_filter($line))) {
                continue;
            }

            if (count($line) < count($requiredHeaders)) {
                $invalidRows[] =
                    'Baris ' . $lineNumber .
                    ': jumlah kolom tidak mencukupi.';
                continue;
            }

            $data = [];

            foreach ($header as $index => $field) {
                $data[$field] = isset($line[$index])
                    ? trim((string) $line[$index])
                    : '';
            }

            $nis = trim((string) ($data['nis'] ?? ''));

            if ($nis === '') {
                $invalidRows[] =
                    'Baris ' . $lineNumber . ': NIS kosong.';
                continue;
            }

            if (isset($seenNis[$nis])) {
                $invalidRows[] =
                    'Baris ' . $lineNumber .
                    ' (NIS ' . $nis . '): duplicate NIS dalam file.';
                continue;
            }

            $seenNis[$nis] = true;

            /*
            |--------------------------------------------------------------------------
            | WA
            |--------------------------------------------------------------------------
            */
            $waSiswa = $this->normalizeWaNumber(
                $data['wa_siswa'] ?? ''
            );

            $waOrtu = $this->normalizeWaNumber(
                $data['wa_ortu'] ?? ''
            );

            if ($waSiswa === false || $waOrtu === false) {
                $invalidFields = [];

                if ($waSiswa === false) {
                    $invalidFields[] = 'wa_siswa';
                }

                if ($waOrtu === false) {
                    $invalidFields[] = 'wa_ortu';
                }

                $invalidRows[] =
                    'Baris ' . $lineNumber .
                    ' (NIS ' . $nis . '): ' .
                    implode(', ', $invalidFields) .
                    ' tidak valid.';

                continue;
            }

            /*
            |--------------------------------------------------------------------------
            | Existing master check
            |--------------------------------------------------------------------------
            */
            $exists = $db->table('siswa')
                ->where('nis', $nis)
                ->countAllResults();

            if ($exists > 0) {
                $invalidRows[] =
                    'Baris ' . $lineNumber .
                    ' (NIS ' . $nis . '): siswa sudah ada di database.';
                continue;
            }

            /*
            |--------------------------------------------------------------------------
            | Email
            |--------------------------------------------------------------------------
            */
            $email = $this->normalizeImportEmail(
                $data['email'] ?? '',
                $nis
            );

            $emailOrtu = $this->normalizeImportEmail(
                $data['email_ortu'] ?? '',
                $nis . '_ortu'
            );

            $rows[] = [
                'nis'           => $nis,
                'password'      => $data['password'] ?? '123456',
                'email'         => $email,
                'nama_siswa'    => $data['nama_siswa'] ?? '',
                'wa_siswa'      => $waSiswa,
                'tahun_masuk'   => $data['tahun_masuk'] ?? null,
                'nama_ortu'     => $data['nama_ortu'] ?? '',
                'wa_ortu'       => $waOrtu,
                'email_ortu'    => $emailOrtu,
                'password_ortu' => $data['password_ortu'] ?? '123456',
            ];
        }

        fclose($handle);

        /*
        |--------------------------------------------------------------------------
        | PREFLIGHT SUMMARY
        |--------------------------------------------------------------------------
        */
        $totalRows = count($rows) + count($invalidRows);

        if (!empty($invalidRows)) {
            $preview = array_slice($invalidRows, 0, 15);

            $errorMessage =
                'Preflight gagal. ' .
                count($invalidRows) .
                ' baris ditolak dari ' .
                $totalRows .
                ' baris. ' .
                implode(' | ', $preview);

            if (count($invalidRows) > 15) {
                $errorMessage .= ' | ...';
            }

            return redirect()->back()
                ->with(
                    'error',
                    $errorMessage
                )
                ->with(
                    'success',
                    'Tidak ada data diinsert karena preflight belum bersih.'
                );
        }

        if (empty($rows)) {
            return redirect()->back()
                ->with('error', 'Tidak ada baris valid untuk diimport.');
        }

        /*
        |--------------------------------------------------------------------------
        | IMPORT SETELAH PREFLIGHT LULUS
        |--------------------------------------------------------------------------
        */
        $bulkModel = new \App\Models\BulkUploadModel();

        $chunkSize = 500;

        foreach (array_chunk($rows, $chunkSize) as $chunk) {
            if (!$bulkModel->bulkInsert($chunk)) {
                return redirect()->back()->with(
                    'error',
                    'Import gagal saat proses database. Tidak ada baris berikutnya yang diproses.'
                );
            }
        }

        return redirect()->back()->with(
            'success',
            'Import berhasil. ' .
            count($rows) .
            ' data siswa diproses.'
        );
    }

    /**
     * Normalisasi nomor WhatsApp Indonesia.
     *
     * string = valid
     * null   = kosong
     * false  = invalid
     */
    private function normalizeWaNumber($value)
    {
        $value = trim((string) $value);

        if ($value === '') {
            return null;
        }

        if (preg_match(
            '/^[+-]?\d+(?:[.,]\d+)?[Ee][+-]?\d+$/i',
            $value
        )) {
            return false;
        }

        $normalized = preg_replace(
            '/[\s\-\(\)\.]/',
            '',
            $value
        );

        if ($normalized === '') {
            return null;
        }

        if (strpos($normalized, '+62') === 0) {
            $normalized = '62' . substr($normalized, 3);
        } elseif (strpos($normalized, '08') === 0) {
            $normalized = '62' . substr($normalized, 1);
        }

        if (!preg_match('/^62\d+$/', $normalized)) {
            return false;
        }

        $length = strlen($normalized);

        if ($length < 10 || $length > 15) {
            return false;
        }

        return $normalized;
    }

    /**
     * Placeholder email Excel tidak boleh menyebabkan UNIQUE collision.
     */
    private function normalizeImportEmail($value, string $fallbackKey)
    {
        $value = trim((string) $value);

        if (
            $value === '' ||
            strtolower($value) === 'siswa@example.com' ||
            strtolower($value) === 'ortu@example.com'
        ) {
            return $fallbackKey . '@dummy.local';
        }

        return strtolower($value);
    }

    public function download_template()
    {

        if ($response = $this->requireAdmin()) {
            return $response;
        }

        $fileName = 'template_upload.csv';

        $headers = [
            'nis','password','email','nama_siswa',
            'wa_siswa','tahun_masuk',
            'nama_ortu','wa_ortu','email_ortu'
        ];

        $rows = [
            implode(';', $headers),
            ';;;;;;;',
            ';;;;;;;',
            ';;;;;;;'
        ];

        $content = "\xEF\xBB\xBF" . implode("\n", $rows); // UTF-8 BOM biar Excel aman

        return $this->response
            ->setHeader('Content-Type', 'text/csv; charset=utf-8')
            ->setHeader('Content-Disposition', 'attachment; filename="'.$fileName.'"')
            ->setBody($content);
    }

}