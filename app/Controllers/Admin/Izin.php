<?php

namespace App\Controllers\Admin;

use App\Controllers\BaseController;
use App\Models\IzinModel;
use App\Models\SuratModel;
use App\Models\AttendanceModel;
use App\Models\SiswaAkademikModel;

class Izin extends BaseController
{
    protected $izinModel;
    protected $suratModel;
    protected $attendanceModel;
    protected $siswaAkademikModel;
    protected $db;

    public function __construct()
    {
        $this->izinModel = new IzinModel();
        $this->suratModel = new SuratModel();
        $this->attendanceModel = new AttendanceModel();
        $this->siswaAkademikModel = new SiswaAkademikModel();

        $this->db = \Config\Database::connect();
    }

    /*
    ======================================
    INDEX
    ======================================
    */
    public function index()
    {
        if ($response = $this->requireAdminOperator()) {
            return $response;
        }

        $nama = trim((string) $this->request->getGet('nama'));
        $perPage = (int) ($this->request->getGet('perPage') ?? 10);

        if (!in_array($perPage, [10, 25, 50, 100], true)) {
            $perPage = 10;
        }

        /*
        ======================================
        SUMBER DATA PENGAJUAN ORANG TUA
        ======================================

        Pengajuan izin dari Orang Tua disimpan
        pada tabel `surat`.

        Mapping untuk View Admin:
            surat.jenis  -> jenis_izin
            surat.status -> status_izin
        */

        $builder = $this->db->table('surat')
            ->select("
                surat.*,
                surat.jenis AS jenis_izin,
                surat.status AS status_izin,
                siswa.nama_siswa,
                siswa_akademik.kelas_id,
                siswa_akademik.jurusan_id,
                kelas.nama_kelas,
                jurusan.nama_jurusan
            ")
            ->join(
                'siswa',
                'siswa.id = surat.siswa_id',
                'left'
            )
            ->join(
                'siswa_akademik',
                'siswa_akademik.siswa_id = siswa.id',
                'left'
            )
            ->join(
                'kelas',
                'kelas.id = siswa_akademik.kelas_id',
                'left'
            )
            ->join(
                'jurusan',
                'jurusan.id = siswa_akademik.jurusan_id',
                'left'
            )
            ->orderBy('surat.id', 'DESC');

        if ($nama !== '') {
            $builder->like('siswa.nama_siswa', $nama);
        }

        $page = max(
            1,
            (int) ($this->request->getGet('page') ?? 1)
        );

        $total = $builder->countAllResults(false);

        $izinList = $builder
            ->limit($perPage, ($page - 1) * $perPage)
            ->get()
            ->getResultArray();

        $pager = service('pager');

        $pager->makeLinks(
            $page,
            $perPage,
            $total,
            'bootstrap',
            0,
            'default'
        );

        return view('admin/izin/index', [
            'title'    => 'Data Pengajuan Izin Siswa',
            'izinList' => $izinList,
            'pager'    => $pager,
            'nama'     => $nama,
            'perPage'  => $perPage
        ]);
    }


    /*
    ======================================
    CREATE
    ======================================
    */
    public function create()
    {

        if ($response = $this->requireAdminOperator()) {
            return $response;
        }

        return view('admin/izin/created', [
            'title' => 'Tambah Izin'
        ]);
    }


    /*
    ======================================
    AJAX SEARCH SISWA
    ======================================
    */
public function cariSiswa()
{

        if ($response = $this->requireAdminOperator()) {
            return $response;
        }

    $term = trim($this->request->getGet('term'));

    try {

        $results = $this->siswaAkademikModel
            ->select("
                siswa_akademik.id AS siswa_akademik_id,
                siswa.id AS siswa_id,
                siswa.nama_siswa,
                siswa.nis,
                kelas.nama_kelas,
                jurusan.nama_jurusan
            ")
            ->join('siswa', 'siswa.id = siswa_akademik.siswa_id')
            ->join('kelas', 'kelas.id = siswa_akademik.kelas_id')
            ->join('jurusan', 'jurusan.id = siswa_akademik.jurusan_id', 'left')
            ->where('siswa_akademik.status_akademik_id', 1);

        if ($term != '') {

            $results->groupStart()
                ->like('siswa.nama_siswa', $term)
                ->orLike('siswa.nis', $term)
                ->groupEnd();
        }

        $results = $results
            ->orderBy('siswa.nama_siswa', 'ASC')
            ->findAll(10);

        $data = [];

        foreach ($results as $row) {

            $data[] = [
                'id' => $row['siswa_id'],

                'siswa_akademik_id' =>
                    $row['siswa_akademik_id'],

                'text' =>
                    $row['nama_siswa'] .
                    ' (' . $row['nis'] . ')',

                'kelas' =>
                    $row['nama_kelas'],

                'jurusan' =>
                    $row['nama_jurusan'] ?? '-'
            ];
        }

        return $this->response->setJSON([
            'results' => $data
        ]);

    } catch (\Throwable $e) {

        log_message('error', $e->getMessage());

        return $this->response->setJSON([
            'results' => []
        ]);
    }
}


/*
    ======================================
    STORE / SIMPAN DATA SURAT IZIN
    ======================================
    */
    public function store()
    {

        if ($response = $this->requireAdminOperator()) {
            return $response;
        }

        /*
        |--------------------------------------------------------------------------
        | SURAT MANUAL MASUK KE TABEL `surat`
        |--------------------------------------------------------------------------
        | Alur manual disamakan dengan pengajuan orang tua:
        |
        | surat
        |   -> diajukan
        |   -> halaman proses
        |   -> disetujui / ditangguhkan / ditolak
        |
        | `tanggal_absensi` adalah tanggal siswa sebenarnya tidak hadir.
        */

        $rules = [
            'siswa_id'     => 'required',
            'tanggal'      => 'required|valid_date[Y-m-d]',
            'jenis'        => 'required|in_list[sakit,izin]',
            'isi_pesan'    => 'required',
            'upload_surat' => 'uploaded[upload_surat]|max_size[upload_surat,2048]|ext_in[upload_surat,pdf,jpg,jpeg,png]'
        ];

        if (!$this->validate($rules)) {
            return redirect()->back()
                ->withInput()
                ->with('errors', $this->validator->getErrors());
        }

        $siswaId        = (int) $this->request->getPost('siswa_id');
        $tanggalAbsensi = $this->request->getPost('tanggal');
        $jenis          = $this->request->getPost('jenis');
        $isiPesan       = trim((string) $this->request->getPost('isi_pesan'));

        /*
        |--------------------------------------------------------------------------
        | Ambil orang tua siswa
        |--------------------------------------------------------------------------
        */
        $siswa = $this->db->table('siswa')
            ->select('id, ortu_id')
            ->where('id', $siswaId)
            ->get()
            ->getRowArray();

        if (!$siswa) {
            return redirect()->back()
                ->withInput()
                ->with('error', 'Data siswa tidak ditemukan.');
        }

        /*
        |--------------------------------------------------------------------------
        | Upload surat
        |--------------------------------------------------------------------------
        */
        $file = $this->request->getFile('upload_surat');

        if (!$file || !$file->isValid() || $file->hasMoved()) {
            return redirect()->back()
                ->withInput()
                ->with('error', 'Gagal mengunggah berkas surat.');
        }

        $fileName = $jenis . '_' . time() . '.' . $file->getExtension();

        $file->move(
            ROOTPATH . 'public/uploads/surat_izin',
            $fileName
        );

        /*
        |--------------------------------------------------------------------------
        | Simpan ke tabel `surat`
        |--------------------------------------------------------------------------
        |
        | created_at       = waktu surat masuk ke sistem
        | updated_at       = waktu terakhir data diproses
        | tanggal_absensi  = tanggal siswa sebenarnya tidak hadir
        |
        */

        $now = date('Y-m-d H:i:s');

        $inserted = $this->suratModel->insert([
            'siswa_id'        => $siswaId,
            'ortu_id'         => $siswa['ortu_id'] ?? 0,
            'jenis'           => $jenis,
            'tanggal_absensi' => $tanggalAbsensi,
            'isi_pesan'       => $isiPesan,
            'status'          => 'diajukan',
            'upload_surat'    => $fileName,
            'created_at'      => $now,
            'updated_at'      => null
        ]);

        if (!$inserted) {
            return redirect()->back()
                ->withInput()
                ->with('error', 'Data surat manual gagal disimpan.');
        }

        return redirect()->to(base_url('admin/izin'))
            ->with('success', 'Surat manual berhasil disimpan dan masuk ke proses persetujuan.');
    }


    /*
    ======================================
    EDIT
    ======================================
    */
    public function edit($id)
    {

        if ($response = $this->requireAdminOperator()) {
            return $response;
        }

        /*
        |--------------------------------------------------------------------------
        | DATA PENGAJUAN ORANG TUA BERADA DI TABEL surat
        |--------------------------------------------------------------------------
        */

        $izin = $this->suratModel
            ->select("
                surat.*,
                surat.id AS izin_id,
                surat.jenis AS jenis_izin,
                surat.status AS status_izin,
                siswa.nama_siswa,
                siswa_akademik.id AS siswa_akademik_id,
                kelas.nama_kelas,
                jurusan.nama_jurusan
            ")
            ->join(
                'siswa',
                'siswa.id = surat.siswa_id',
                'left'
            )
            ->join(
                'siswa_akademik',
                'siswa_akademik.siswa_id = surat.siswa_id',
                'left'
            )
            ->join(
                'kelas',
                'kelas.id = siswa_akademik.kelas_id',
                'left'
            )
            ->join(
                'jurusan',
                'jurusan.id = siswa_akademik.jurusan_id',
                'left'
            )
            ->where('surat.id', $id)
            ->first();

        if (!$izin) {
            return redirect()->to(base_url('admin/izin'))
                ->with('error', 'Data surat izin tidak ditemukan.');
        }

        return view('admin/izin/edit', [
            'title' => 'Konfirmasi Surat Izin',
            'izin'  => $izin
        ]);
    }


    /*
    ======================================
    UPDATE
    ======================================
    */
    public function update($id)
    {
        if ($response = $this->requireAdminOperator()) {
            return $response;
        }

        $status = trim((string) $this->request->getPost('status_izin'));

        $allowedStatus = [
            'diajukan',
            'ditangguhkan',
            'disetujui',
            'ditolak'
        ];

        if (!in_array($status, $allowedStatus, true)) {
            return redirect()->back()
                ->withInput()
                ->with('error', 'Status izin tidak valid.');
        }

        /*
        |--------------------------------------------------------------------------
        | DATA PENGAJUAN BERADA DI TABEL surat
        |--------------------------------------------------------------------------
        */

        $surat = $this->suratModel->find($id);

        if (!$surat) {
            return redirect()->to(base_url('admin/izin'))
                ->with('error', 'Data surat izin tidak ditemukan.');
        }

        /*
        |--------------------------------------------------------------------------
        | TANGGAL KETIDAKHADIRAN
        |--------------------------------------------------------------------------
        |
        | Sumber tanggal attendance adalah tanggal_absensi,
        | bukan created_at.
        |
        | Jika form mengirim tanggal, gunakan tanggal tersebut.
        | Jika tidak dikirim, pertahankan tanggal yang sudah tersimpan.
        |
        */

        $tanggalAbsensi = trim((string) $this->request->getPost('tanggal_absensi'));

        if ($tanggalAbsensi === '') {
            $tanggalAbsensi = trim((string) ($surat['tanggal_absensi'] ?? ''));
        }

        if ($tanggalAbsensi === '' || !preg_match('/^\d{4}-\d{2}-\d{2}$/', $tanggalAbsensi)) {
            return redirect()->back()
                ->withInput()
                ->with('error', 'Tanggal ketidakhadiran wajib diisi dengan format yang benar.');
        }

        $tanggalObj = \DateTime::createFromFormat('Y-m-d', $tanggalAbsensi);

        if (!$tanggalObj || $tanggalObj->format('Y-m-d') !== $tanggalAbsensi) {
            return redirect()->back()
                ->withInput()
                ->with('error', 'Tanggal ketidakhadiran tidak valid.');
        }

        /*
        |--------------------------------------------------------------------------
        | JENIS SURAT
        |--------------------------------------------------------------------------
        */

        $attendanceStatus = $surat['jenis'];

        if (!in_array($attendanceStatus, ['izin', 'sakit'], true)) {
            return redirect()->to(base_url('admin/izin'))
                ->with('error', 'Jenis surat izin tidak valid.');
        }

        /*
        |--------------------------------------------------------------------------
        | JIKA DISETUJUI, VALIDASI DATA AKADEMIK TERLEBIH DAHULU
        |--------------------------------------------------------------------------
        */

        $siswaAkademik = null;

        if ($status === 'disetujui') {

            $siswaAkademik = $this->siswaAkademikModel
                ->where('siswa_id', $surat['siswa_id'])
                ->where('tapel_id', function ($builder) {
                    $builder->select('id')
                        ->from('tapel')
                        ->where('aktif', 1)
                        ->limit(1);
                })
                ->first();

            /*
             * Fallback ke data akademik terbaru siswa.
             */
            if (!$siswaAkademik) {
                $siswaAkademik = $this->siswaAkademikModel
                    ->where('siswa_id', $surat['siswa_id'])
                    ->orderBy('id', 'DESC')
                    ->first();
            }

            if (!$siswaAkademik) {
                return redirect()->to(base_url('admin/izin'))
                    ->with('error', 'Data akademik siswa tidak ditemukan.');
            }
        }

        /*
        |--------------------------------------------------------------------------
        | UPDATE SURAT
        |--------------------------------------------------------------------------
        */

        /*
        |--------------------------------------------------------------------------
        | UPDATE SURAT
        |--------------------------------------------------------------------------
        |
        | created_at tidak boleh berubah.
        | updated_at mencatat waktu proses terakhir.
        |
        */

        $now = date('Y-m-d H:i:s');

        $updated = $this->suratModel->update($id, [
            'status'          => $status,
            'tanggal_absensi' => $tanggalAbsensi,
            'updated_at'      => $now
        ]);

        if (!$updated) {
            return redirect()->back()
                ->withInput()
                ->with('error', 'Data surat izin gagal diperbarui.');
        }

        /*
        |--------------------------------------------------------------------------
        | ATTENDANCE HANYA DIBUAT / DIPERBARUI JIKA DISETUJUI
        |--------------------------------------------------------------------------
        */

        if ($status === 'disetujui') {

            $attendance = $this->attendanceModel
                ->where('siswa_akademik_id', $siswaAkademik['id'])
                ->where('tanggal', $tanggalAbsensi)
                ->first();

            if ($attendance) {

                $this->attendanceModel->update($attendance['id'], [
                    'status'     => $attendanceStatus,
                    'keterangan' => trim((string) ($surat['isi_pesan'] ?? '')),
                    'updated_at' => date('Y-m-d H:i:s')
                ]);

            } else {

                $now = date('Y-m-d H:i:s');

                $inserted = $this->attendanceModel->insert([
                    'siswa_akademik_id' => $siswaAkademik['id'],
                    'tanggal'           => $tanggalAbsensi,
                    'status'            => $attendanceStatus,
                    'keterangan'        => trim((string) ($surat['isi_pesan'] ?? '')),
                    'created_at'        => $now,
                    'updated_at'        => null
                ]);

                if (!$inserted) {
                    return redirect()->to(base_url('admin/izin'))
                        ->with('error', 'Status surat berhasil diperbarui, tetapi attendance gagal disimpan.');
                }
            }
        }

        return redirect()->to(base_url('admin/izin'))
            ->with(
                'success',
                'Status surat izin berhasil diperbarui menjadi "' .
                ucfirst($status) .
                '".'
            );
    }

}
