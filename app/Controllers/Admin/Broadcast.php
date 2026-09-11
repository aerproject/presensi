<?php

namespace App\Controllers\Admin;

use App\Controllers\BaseController;
use App\Models\BroadcastModel;
use App\Models\KelasModel;
use App\Models\JurusanModel;
use App\Libraries\WhatsappSender;

class Broadcast extends BaseController
{
    protected $broadcastModel;
    protected $kelasModel;
    protected $jurusanModel;

    public function __construct()
    {
        $this->broadcastModel = new BroadcastModel();
        $this->kelasModel     = new KelasModel();
        $this->jurusanModel   = new JurusanModel();
    }

/**
     * Tampilan daftar history broadcast (Hanya Tapel Aktif)
     */
    public function index()
    {

        if ($response = $this->requireAdminOperator()) {
            return $response;
        }

        $perPage = (int) ($this->request->getGet('perPage') ?? 10);

        // 1. Ambil data tahun pelajaran yang sedang aktif
        $tapelAktif = db_connect()->table('tapel')->where('aktif', 1)->get()->getRowArray();

        if (!$tapelAktif) {
            return view('admin/broadcast/index', [
                'broadcasts' => [],
                'pager'      => $this->broadcastModel->pager,
                'perPage'    => $perPage,
                'error'      => 'Tahun pelajaran aktif belum diatur.'
            ]);
        }

        // 2. Ambil semua ID referensi akademik yang valid di tapel aktif saat ini
        $db = db_connect();
        
        $studentIds = $db->table('siswa_akademik')->select('siswa_id')->where('tapel_id', $tapelAktif['id'])->get()->getResultArray();
        $kelasIds   = $db->table('siswa_akademik')->select('kelas_id')->where('tapel_id', $tapelAktif['id'])->get()->getResultArray();
        $jurusanIds = $db->table('siswa_akademik')->select('jurusan_id')->where('tapel_id', $tapelAktif['id'])->get()->getResultArray();

        // Jaga-jaga jika array kosong agar query IN tidak error (beri nilai fallback [0])
        $allowedStudents = !empty($studentIds) ? array_column($studentIds, 'siswa_id') : [0];
        $allowedKelas    = !empty($kelasIds) ? array_column($kelasIds, 'kelas_id') : [0];
        $allowedJurusan  = !empty($jurusanIds) ? array_column($jurusanIds, 'jurusan_id') : [0];

        /*
        |--------------------------------------------------------------------------
        | Filter Tampilan History Broadcast Berdasarkan Target di Tapel Aktif
        |--------------------------------------------------------------------------
        */
        $this->broadcastModel->groupStart()
            // Jika perorangan, pastikan siswa tersebut ada di tapel aktif
            ->whereIn('broadcast.student_id', $allowedStudents)
            // Jika broadcast per kelas, pastikan kelasnya ada di tapel aktif
            ->orWhereIn('broadcast.kelas_id', $allowedKelas)
            // Jika broadcast per jurusan, pastikan jurusannya ada di tapel aktif
            ->orWhereIn('broadcast.jurusan_id', $allowedJurusan)
            // Jika broadcast global tingkat (tetap tampilkan) atau jika data kosong global
            ->orWhere('broadcast.jenis_pesan', 'tingkat')
        ->groupEnd();

        $broadcasts = $this->broadcastModel->orderBy('created_at', 'DESC')->paginate($perPage, 'default');

        return view('admin/broadcast/index', [
            'broadcasts' => $broadcasts,
            'pager'      => $this->broadcastModel->pager,
            'perPage'    => $perPage,
        ]);
    }

/**
     * Tampilan pembuatan broadcast & filter penerima (Ortu)
     */
    public function create()
    {

        if ($response = $this->requireAdminOperator()) {
            return $response;
        }

        $jenisPesan = $this->request->getGet('jenis_pesan');
        $nis        = $this->request->getGet('nis');
        $kelasId    = $this->request->getGet('kelas_id');
        $tingkat    = $this->request->getGet('tingkat');
        $jurusanId  = $this->request->getGet('jurusan_id');

        // Ambil daftar nomor orang tua berdasarkan filter & Tapel Aktif
        $ortuList = [];
        if (!empty($jenisPesan)) {
            $ortuList = $this->getRecipientQuery($jenisPesan, [
                'student_id' => null,
                'nis'        => $nis,
                'kelas_id'   => $kelasId,
                'tingkat'    => $tingkat,
                'jurusan_id' => $jurusanId
            ])->get()->getResultArray();

            // Saring dan pastikan format wa_ortu berupa teks murni agar view aman
            foreach ($ortuList as &$ortu) {
                if (isset($ortu['wa_ortu'])) {
                    $ortu['wa_ortu'] = trim((string)$ortu['wa_ortu']);
                }
            }
        }

        return view('admin/broadcast/created', [
            'kelasList'   => $this->kelasModel->orderBy('nama_kelas', 'ASC')->findAll(),
            'jurusanList' => $this->jurusanModel->orderBy('nama_jurusan', 'ASC')->findAll(),
            'ortuList'    => $ortuList
        ]);
    }

    /**
     * API AJAX: Ambil nomor HP Ortu via NIS (Gunakan Siswa Akademik + Tapel Aktif)
     */
    public function getPhoneByNis()
    {

        if ($response = $this->requireAdminOperator()) {
            return $response;
        }

        $nis = $this->request->getGet('nis');
        
        $result = db_connect()->table('siswa_akademik')
            ->select('siswa.id as student_id, parents.wa_ortu')
            ->join('siswa', 'siswa.id = siswa_akademik.siswa_id')
            ->join('parents', 'parents.id = siswa.ortu_id')
            ->join('tapel', 'tapel.id = siswa_akademik.tapel_id')
            ->where('tapel.aktif', 1)
            ->where('siswa.nis', $nis)
            ->get()
            ->getRowArray();

        if ($result && isset($result['wa_ortu'])) {
            $result['wa_ortu'] = trim((string)$result['wa_ortu']);
        }

        return $this->response->setJSON($result ?? []);
    }

/**
     * Simpan data antrian broadcast ke database (Proteksi Eksponensial Angka)
     */
    public function store()
    {

        if ($response = $this->requireAdminOperator()) {
            return $response;
        }

        $jenisPesan = $this->request->getPost('jenis_pesan');
        
        // Ambil dan paksa nomor HP menjadi string murni (Mencegah 6,28E+12)
        $phonePost  = $this->request->getPost('phone');
        $phone      = $phonePost ? trim((string)$phonePost) : null;
        
        // Ambil data input lainnya dan bersihkan spasi kosongnya
        $studentId  = trim((string)$this->request->getPost('student_id'));
        $kelasId    = trim((string)$this->request->getPost('kelas_id'));
        $tingkat    = trim((string)$this->request->getPost('tingkat'));
        $jurusanId  = trim((string)$this->request->getPost('jurusan_id'));

        // Normalisasi isi TinyMCE menjadi plain text sebelum disimpan.
        $isiPesan = (string) $this->request->getPost('isi_pesan');

        $isiPesan = preg_replace(
            [
                '/<\\s*br\\s*\\/?\\s*>/i',
                '/<\\s*\\/?\\s*(p|div|h[1-6])\\b[^>]*>/i',
                '/<\\s*li\\b[^>]*>/i',
                '/<\\/\\s*li\\s*>/i',
            ],
            [
                "\\n",
                "\\n",
                "• ",
                "\\n",
            ],
            $isiPesan
        );

        $isiPesan = strip_tags($isiPesan);

        $isiPesan = html_entity_decode(
            $isiPesan,
            ENT_QUOTES | ENT_HTML5,
            'UTF-8'
        );

        $isiPesan = preg_replace('/[ \\t]+\\n/', "\\n", $isiPesan);
        $isiPesan = preg_replace('/\\n{3,}/', "\\n\\n", $isiPesan);
        $isiPesan = trim($isiPesan);

        if ($isiPesan === '') {
            return redirect()->back()
                ->withInput()
                ->with('error', 'Isi pesan wajib diisi.');
        }

        // Validasi input berdasarkan tipe broadcast
        if ($jenisPesan === 'perorangan' && empty($phone)) {
            return redirect()->back()->withInput()->with('error', 'Nomor WhatsApp wajib diisi untuk pesan perorangan.');
        }
        if ($jenisPesan === 'kelas' && empty($kelasId)) {
            return redirect()->back()->withInput()->with('error', 'Kelas wajib dipilih untuk pesan kelas.');
        }
        if ($jenisPesan === 'tingkat' && empty($tingkat)) {
            return redirect()->back()->withInput()->with('error', 'Tingkat wajib dipilih untuk pesan tingkat.');
        }
        if ($jenisPesan === 'jurusan' && empty($jurusanId)) {
            return redirect()->back()->withInput()->with('error', 'Jurusan wajib dipilih untuk pesan jurusan.');
        }

        // Simpan data antrian ke database
        $this->broadcastModel->insert([
            'judul'       => $this->request->getPost('judul'),
            'jenis_pesan' => $jenisPesan,
            'isi_pesan'   => $isiPesan,
            'phone'       => $phone, 
            
            // Konversi string kosong "" menjadi NULL agar masuk ke kolom Foreign Key Database dengan benar
            'student_id'  => ($studentId !== '') ? $studentId : null,
            'kelas_id'    => ($kelasId !== '')   ? $kelasId : null,
            'tingkat'     => ($tingkat !== '')   ? $tingkat : null,
            'jurusan_id'  => ($jurusanId !== '') ? $jurusanId : null,
            
            'status'      => 'queued'
        ]);

        return redirect()->to(base_url('admin/broadcast-view'))->with('success', 'Pesan berhasil dibuat dan masuk antrian.');
    }
    /**
     * Kirim manual satu baris item broadcast berdasarkan ID via tombol aksi admin
     */
    public function send($id)
    {

        if ($response = $this->requireAdminOperator()) {
            return $response;
        }

        $broadcast = $this->broadcastModel->find($id);

        if (!$broadcast) {
            return redirect()->back()
                ->with('error', 'Pesan broadcast tidak ditemukan.');
        }

        if ($broadcast['status'] !== 'queued') {
            return redirect()->to(base_url('admin/broadcast-view'))
                ->with(
                    'error',
                    'Broadcast ini sudah tidak berada dalam antrian.'
                );
        }

        if (!$this->acquireBroadcastLock((int) $id)) {
            return redirect()->to(base_url('admin/broadcast-view'))
                ->with(
                    'error',
                    'Broadcast sedang diproses. Silakan tunggu sampai proses selesai.'
                );
        }

        try {
            $ortuList = $this->getRecipientQuery(
                $broadcast['jenis_pesan'],
                $broadcast
            )->get()->getResultArray();

            $report = $this->executeSendBroadcast(
                $ortuList,
                $broadcast['isi_pesan']
            );

            $this->broadcastModel->update($broadcast['id'], [
                'status'        => $report['status'],
                'waktu_kirim'   => date('Y-m-d H:i:s'),
                'error_message' => $report['error_message'],
            ]);

            return redirect()->to(base_url('admin/broadcast-view'))
                ->with(
                    'success',
                    "Broadcast selesai: {$report['success']} terkirim, {$report['fail']} gagal."
                );
        } finally {
            $this->releaseBroadcastLock((int) $id);
        }
    }
    public function sendBroadcast()
    {

        if ($response = $this->requireAdminOperator()) {
            return $response;
        }

        $queued = $this->broadcastModel
            ->where('status', 'queued')
            ->findAll();

        foreach ($queued as $broadcast) {
            $id = (int) $broadcast['id'];

            if (!$this->acquireBroadcastLock($id)) {
                continue;
            }

            try {
                // Re-check status setelah lock diperoleh.
                $current = $this->broadcastModel->find($id);

                if (!$current || $current['status'] !== 'queued') {
                    continue;
                }

                $ortuList = $this->getRecipientQuery(
                    $current['jenis_pesan'],
                    $current
                )->get()->getResultArray();

                $report = $this->executeSendBroadcast(
                    $ortuList,
                    $current['isi_pesan']
                );

                $this->broadcastModel->update($id, [
                    'status'        => $report['status'],
                    'waktu_kirim'   => date('Y-m-d H:i:s'),
                    'error_message' => $report['error_message'],
                ]);
            } finally {
                $this->releaseBroadcastLock($id);
            }
        }

        return "Task broadcast selesai dijalankan.";
    }

    public function delete($id = null)
    {

        if ($response = $this->requireAdminOperator()) {
            return $response;
        }

        if ($id === null || !$this->broadcastModel->find($id)) {
            return redirect()->back()->with('error', 'Data broadcast tidak ditemukan.');
        }

        $this->broadcastModel->delete($id);
        return redirect()->to(base_url('admin/broadcast-view'))->with('success', 'Data record broadcast berhasil dihapus.');
    }

    /*
    |--------------------------------------------------------------------------
    | REUSABLE PRIVATE METHODS (SUPORT CLEAN CODE & DRY PRINCIPLE)
    |--------------------------------------------------------------------------
    */

/**
     * Pusat Query Builder: Memastikan seluruh filter target broadcast bersumber dari siswa_akademik & Tapel Aktif
     */
    /**
     * Nama advisory lock unik untuk satu broadcast.
     */
    private function broadcastLockName(int $id): string
    {
        return 'presensi.broadcast.' . $id;
    }

    /**
     * Ambil advisory lock untuk mencegah pengiriman ganda.
     */
    private function acquireBroadcastLock(int $id): bool
    {
        $db = db_connect();

        $row = $db->query(
            'SELECT GET_LOCK(?, 0) AS acquired',
            [$this->broadcastLockName($id)]
        )->getRowArray();

        return isset($row['acquired'])
            && (int) $row['acquired'] === 1;
    }

    /**
     * Lepaskan advisory lock broadcast.
     */
    private function releaseBroadcastLock(int $id): void
    {
        $db = db_connect();

        $db->query(
            'SELECT RELEASE_LOCK(?) AS released',
            [$this->broadcastLockName($id)]
        );
    }

    private function getRecipientQuery(string $jenisPesan, array $params)
    {
        $builder = db_connect()->table('siswa_akademik')
            ->select('siswa.id as student_id, siswa.nis, parents.id as parent_id, parents.wa_ortu, kelas.nama_kelas, kelas.kelas, jurusan.nama_jurusan')
            ->join('siswa', 'siswa.id = siswa_akademik.siswa_id')
            ->join('parents', 'parents.id = siswa.ortu_id')
            ->join('kelas', 'kelas.id = siswa_akademik.kelas_id')
            ->join('jurusan', 'jurusan.id = siswa_akademik.jurusan_id', 'left')
            ->join('tapel', 'tapel.id = siswa_akademik.tapel_id')
            ->where('tapel.aktif', 1);

        switch ($jenisPesan) {
            case 'perorangan':
                // Perbaikan: gunakan student_id yang dikirim dari data simpanan broadcast table
                if (!empty($params['student_id'])) {
                    $builder->where('siswa.id', $params['student_id']);
                } elseif (!empty($params['nis'])) {
                    $builder->where('siswa.nis', $params['nis']);
                }
                break;
            case 'kelas':
                $builder->where('siswa_akademik.kelas_id', $params['kelas_id']);
                break;
            case 'tingkat':
                $builder->where('kelas.kelas', $params['tingkat']);
                break;
            case 'jurusan':
                $builder->where('siswa_akademik.jurusan_id', $params['jurusan_id']);
                break;
        }

        return $builder;
    }

    /**
     * Loop Engine pengiriman via WhatsappSender Library
     */
    private function executeSendBroadcast(array $ortuList, string $pesan): array
    {
        // Jika data dari siswa_akademik tapel aktif tidak ditemukan
        if (empty($ortuList)) {
            return [
                'status'        => 'failed',
                'success'       => 0,
                'fail'          => 1,
                'error_message' => 'Data penerima tidak ditemukan di Tahun Pelajaran Aktif.'
            ];
        }

        $sender = new WhatsappSender();
        $successCount = 0;
        $failCount    = 0;
        $lastError    = null;

        foreach ($ortuList as $o) {
            if (!empty($o['wa_ortu'])) {
                [$sent, $errorMessage] = $sender->send($o['wa_ortu'], $pesan);
                if ($sent) {
                    $successCount++;
                } else {
                    $failCount++;
                    $lastError = $errorMessage ?? 'Gagal respons gateway WA';
                }
            } else {
                $failCount++;
                $lastError = 'Nomor WA Orang Tua kosong di database';
            }
        }

        $status = 'sent';
        if ($failCount > 0 && $successCount > 0) {
            $status = 'partial';
        } elseif ($failCount > 0 && $successCount === 0) {
            $status = 'failed';
        }

        return [
            'status'        => $status,
            'success'       => $successCount,
            'fail'          => $failCount,
            'error_message' => $lastError
        ];
    }
}