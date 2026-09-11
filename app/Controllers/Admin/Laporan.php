<?php

namespace App\Controllers\Admin;

use App\Controllers\BaseController;
use App\Models\AttendanceModel;
use App\Models\KelasModel;
use App\Models\AplikasiModel;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use PhpOffice\PhpSpreadsheet\Style\Border;
use PhpOffice\PhpSpreadsheet\Style\Fill;

class Laporan extends BaseController
{
    protected $attendanceModel;
    protected $kelasModel;
    protected $aplikasiModel;

    public function __construct()
    {
        $this->attendanceModel = new AttendanceModel();
        $this->kelasModel      = new KelasModel();
        $this->aplikasiModel   = new AplikasiModel();
    }


    // =====================================================
    // HALAMAN LAPORAN
    // =====================================================
    public function index()
    {

        if ($response = $this->requireAdminOperator()) {
            return $response;
        }

        $kelasId = $this->request->getGet('kelas_id');
        $bulan   = $this->request->getGet('bulan') ?? date('m');
        $tahun   = $this->request->getGet('tahun') ?? date('Y');

        $kelasList = $this->kelasModel->findAll();
        $rekap = [];

        if ($kelasId) {

            $db = \Config\Database::connect();

            $rekap = $db->table('siswa_akademik')
                ->select("
                    siswa.nama_siswa,

                    SUM(CASE WHEN attendance.status='hadir' THEN 1 ELSE 0 END) as hadir,

                    SUM(CASE WHEN attendance.status='izin' THEN 1 ELSE 0 END) as izin,

                    SUM(CASE WHEN attendance.status='sakit' THEN 1 ELSE 0 END) as sakit,

                    SUM(CASE WHEN attendance.status='alpha' THEN 1 ELSE 0 END) as alpha
                ")

                ->join('siswa', 'siswa.id = siswa_akademik.siswa_id')

                ->join(
                    'attendance',
                    "attendance.siswa_akademik_id = siswa_akademik.id
                    AND MONTH(attendance.tanggal)=" . (int)$bulan . "
                    AND YEAR(attendance.tanggal)=" . (int)$tahun,
                    'left'
                )

                ->where('siswa_akademik.kelas_id', $kelasId)

                ->where('siswa_akademik.status_akademik_id', 1)

                ->groupBy('siswa_akademik.id')

                ->orderBy('siswa.nama_siswa', 'ASC')

                ->get()

                ->getResultArray();
        }

        return view('admin/laporan/index', [

            'kelasList'     => $kelasList,

            'rekap'         => $rekap,

            'selectedKelas' => $kelasId,

            'selectedBulan' => $bulan,

            'selectedTahun' => $tahun

        ]);
    }


    // =====================================================
    // EXPORT EXCEL
    // =====================================================
    public function exportExcel()
    {

        if ($response = $this->requireAdminOperator()) {
            return $response;
        }

        $kelasId = $this->request->getGet('kelas_id');
        $bulan   = $this->request->getGet('bulan') ?? date('m');
        $tahun   = $this->request->getGet('tahun') ?? date('Y');

        $kelas = $this->kelasModel->find($kelasId);

        $namaBulan = [

            '01' => 'Januari',
            '02' => 'Februari',
            '03' => 'Maret',
            '04' => 'April',
            '05' => 'Mei',
            '06' => 'Juni',
            '07' => 'Juli',
            '08' => 'Agustus',
            '09' => 'September',
            '10' => 'Oktober',
            '11' => 'November',
            '12' => 'Desember'
        ];

        $bulanNama = $namaBulan[$bulan] ?? $bulan;

        $db = \Config\Database::connect();

                /*
        |--------------------------------------------------------------------------
        | WALI KELAS
        |--------------------------------------------------------------------------
        | Ambil wali kelas berdasarkan kelas yang sedang diekspor
        | dan tahun pelajaran yang aktif.
        |--------------------------------------------------------------------------
        */

        $waliKelas = $db->table('walikelas wk')
            ->select('g.nama_guru, g.nip')
            ->join('guru g', 'g.id = wk.guru_id', 'inner')
            ->join('tapel t', 't.id = wk.tapel_id', 'inner')
            ->where('wk.kelas_id', $kelasId)
            ->where('t.aktif', 1)
            ->where('wk.deleted_at IS NULL', null, false)
            ->where('g.deleted_at IS NULL', null, false)
            ->orderBy('wk.id', 'DESC')
            ->get()
            ->getRowArray();

        $namaWaliKelas = $waliKelas['nama_guru'] ?? '-';
        $nipWaliKelas  = $waliKelas['nip'] ?? '-';

$rekap = $db->table('siswa_akademik')

            ->select("
                siswa.nama_siswa,

                SUM(CASE WHEN attendance.status='hadir' THEN 1 ELSE 0 END) as hadir,

                SUM(CASE WHEN attendance.status='izin' THEN 1 ELSE 0 END) as izin,

                SUM(CASE WHEN attendance.status='sakit' THEN 1 ELSE 0 END) as sakit,

                SUM(CASE WHEN attendance.status='alpha' THEN 1 ELSE 0 END) as alpha
            ")

            ->join('siswa', 'siswa.id = siswa_akademik.siswa_id')

            ->join(
                'attendance',
                "attendance.siswa_akademik_id = siswa_akademik.id
                AND MONTH(attendance.tanggal)=" . (int)$bulan . "
                AND YEAR(attendance.tanggal)=" . (int)$tahun,
                'left'
            )

            ->where('siswa_akademik.kelas_id', $kelasId)

            ->where('siswa_akademik.status_akademik_id', 1)

            ->groupBy('siswa_akademik.id')

            ->orderBy('siswa.nama_siswa', 'ASC')

            ->get()

            ->getResultArray();


        $spreadsheet = new Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();
        $sheet->setTitle('Laporan Kehadiran');


        // =====================================================
        // KOP SURAT
        // =====================================================
        $kopPath = ROOTPATH . 'public/uploads/kop/default_kop.jpg';

        $pengaturan = $this->aplikasiModel->first();

        if (!empty($pengaturan['kop_surat'])) {

            $customPath = ROOTPATH . 'public/uploads/kop/' . $pengaturan['kop_surat'];

            if (file_exists($customPath)) {
                $kopPath = $customPath;
            }
        }

        $drawing = new \PhpOffice\PhpSpreadsheet\Worksheet\Drawing();

        $drawing->setPath($kopPath);

        $drawing->setCoordinates('B2');

        $drawing->setHeight(91);

        $drawing->setWorksheet($sheet);


        $sheet->getStyle('B6:G6')
            ->getBorders()
            ->getBottom()
            ->setBorderStyle(Border::BORDER_DOUBLE);


        // =====================================================
        // JUDUL
        // =====================================================
        $sheet->mergeCells('B8:G8');

        $sheet->setCellValue('B8', 'LAPORAN KEHADIRAN SISWA');

        $sheet->getStyle('B8')->getFont()->setBold(true)->setSize(14);

        $sheet->getStyle('B8')->getAlignment()->setHorizontal('center');


        $sheet->setCellValue('B10', 'Kelas : ' . $kelas['nama_kelas']);

        $sheet->setCellValue('F10', 'Bulan : ' . $bulanNama . ' ' . $tahun);


        // =====================================================
        // HEADER
        // =====================================================
        $sheet->getColumnDimension('B')->setWidth(6);
        $sheet->getColumnDimension('C')->setWidth(45);
        $sheet->getColumnDimension('D')->setWidth(10);
        $sheet->getColumnDimension('E')->setWidth(10);
        $sheet->getColumnDimension('F')->setWidth(10);
        $sheet->getColumnDimension('G')->setWidth(10);

        $sheet->setCellValue('B12', 'NO');
        $sheet->setCellValue('C12', 'NAMA SISWA');
        $sheet->setCellValue('D12', 'HADIR');
        $sheet->setCellValue('E12', 'IZIN');
        $sheet->setCellValue('F12', 'SAKIT');
        $sheet->setCellValue('G12', 'ALPHA');

        $sheet->getStyle('B12:G12')->getFont()->setBold(true);

        $sheet->getStyle('B12:G12')->getAlignment()->setHorizontal('center');

        $sheet->getStyle('B12:G12')
            ->getFill()
            ->setFillType(Fill::FILL_SOLID)
            ->getStartColor()
            ->setRGB('D9E1F2');


        // =====================================================
        // DATA
        // =====================================================
        $row = 13;
        $no = 1;

        foreach ($rekap as $r) {

            $sheet->setCellValue("B{$row}", $no++);

            $sheet->setCellValue("C{$row}", $r['nama_siswa']);

            $sheet->setCellValue("D{$row}", $r['hadir']);

            $sheet->setCellValue("E{$row}", $r['izin']);

            $sheet->setCellValue("F{$row}", $r['sakit']);

            $sheet->setCellValue("G{$row}", $r['alpha']);

            $row++;
        }


        $sheet->getStyle("B12:G" . ($row - 1))
            ->getBorders()
            ->getAllBorders()
            ->setBorderStyle(Border::BORDER_THIN);


        // =====================================================
        // TANDA TANGAN WALI KELAS
        // =====================================================

        $signatureRow = $row + 1;

        $sheet->mergeCells("E{$signatureRow}:G{$signatureRow}");
        $sheet->setCellValue(
            "E{$signatureRow}",
            'Mengetahui,'
        );

        $sheet->getStyle("E{$signatureRow}:G{$signatureRow}")
            ->getAlignment()
            ->setHorizontal('left');

        $sheet->mergeCells(
            "E" . ($signatureRow + 1) . ":G" . ($signatureRow + 1)
        );

        $sheet->setCellValue(
            "E" . ($signatureRow + 1),
            'Wali Kelas'
        );

        $sheet->getStyle(
            "E" . ($signatureRow + 1) . ":G" . ($signatureRow + 1)
        )
            ->getAlignment()
            ->setHorizontal('left');

        /*
        |--------------------------------------------------------------------------
        | Ruang tanda tangan
        |--------------------------------------------------------------------------
        */

        $sheet->mergeCells(
            "E" . ($signatureRow + 2) .
            ":G" . ($signatureRow + 5)
        );

        $sheet->getStyle(
            "E" . ($signatureRow + 2) .
            ":G" . ($signatureRow + 5)
        )
            ->getAlignment()
            ->setHorizontal('left')
            ->setVertical('center');

        /*
        |--------------------------------------------------------------------------
        | Nama wali kelas
        |--------------------------------------------------------------------------
        */

        $sheet->mergeCells(
            "E" . ($signatureRow + 6) .
            ":G" . ($signatureRow + 6)
        );

        $sheet->setCellValue(
            "E" . ($signatureRow + 6),
            $namaWaliKelas
        );

        $sheet->getStyle(
            "E" . ($signatureRow + 6) . ":G" . ($signatureRow + 6)
        )
            ->getFont()
            ->setBold(true)
            ->setUnderline(true);

        $sheet->getStyle(
            "E" . ($signatureRow + 6) . ":G" . ($signatureRow + 6)
        )
            ->getAlignment()
            ->setHorizontal('left');

        /*
        |--------------------------------------------------------------------------
        | NIP wali kelas
        |--------------------------------------------------------------------------
        */

        $sheet->mergeCells(
            "E" . ($signatureRow + 7) .
            ":G" . ($signatureRow + 7)
        );

        $sheet->setCellValue(
            "E" . ($signatureRow + 7),
            'NIP. ' . $nipWaliKelas
        );

        $sheet->getStyle(
            "E" . ($signatureRow + 7) . ":G" . ($signatureRow + 7)
        )
            ->getAlignment()
            ->setHorizontal('left');


        // =====================================================
        // DOWNLOAD
        // =====================================================
        $filename = "Laporan_Kehadiran_" .
            $kelas['nama_kelas'] .
            "_" . $bulan .
            "_" . $tahun . ".xlsx";


        header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');

        header("Content-Disposition: attachment;filename=\"{$filename}\"");

        header('Cache-Control: max-age=0');


        $writer = new Xlsx($spreadsheet);

        $writer->save('php://output');

        exit;
    }
}