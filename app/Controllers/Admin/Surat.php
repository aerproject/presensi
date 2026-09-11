<?php

namespace App\Controllers\Admin;

use App\Controllers\BaseController;
use App\Models\SuratModel;
use App\Models\UserModel;
use App\Models\StudentModel;
use App\Models\ParentModel;
use App\Models\AttendanceModel;


class Surat extends BaseController
{
    protected $SuratModel;
    protected $UserModel;
	protected $StudentModel;
	protected $ParentModel;
	protected $AttendanceModel;

    public function __construct()
    {
        $this->suratModel = new SuratModel();
        $this->userModel    = new UserModel(); // jika ingin menampilkan nama siswa
		$this->studentModel    = new StudentModel();
		$this->parentModel    = new ParentModel();
		$this->attendanceModel    = new AttendanceModel();
    }

    // ✅ Tampilkan semua izin
    public function index()
    {
		$data['title']      = 'Surat Siswa | Admin Panel Absensi Digital';
        $data['suratList'] = $this->suratModel
            ->select('surat.*, students.nama_siswa, parents.nama_ortu')
            ->join('students', 'students.id = surat.siswa_id')
            ->join('parents', 'parents.id = surat.ortu_id')
            ->orderBy('surat.created_at', 'DESC')
            ->findAll();


        return view('admin/surat_view', $data);
    }

    // ✅ Form tambah izin
    public function create()
    {
        $data['userList'] = $this->userModel->findAll();
        return view('admin/izin/create', $data);
    }

    // ✅ Simpan izin baru
    public function store()
    {
        $this->messageModel->insert([
            'users_id'    => $this->request->getPost('users_id'),
            'jenis_pesan' => 'izin',
            'isi_pesan'   => $this->request->getPost('isi_pesan'),
            'waktu_kirim' => $this->request->getPost('waktu_kirim'),
            'status'      => $this->request->getPost('status'),
        ]);

        return redirect()->to('/admin/izin')->with('success', 'Izin berhasil ditambahkan.');
    }

    // ✅ Form edit surat
    public function edit($id)
    {
		//dd($id);
        // Ambil data surat berdasarkan ID, termasuk relasi ke siswa dan orang tua
		$data['surat'] = $this->suratModel
			->select('surat.*, students.nama_siswa, kelas.nama_kelas, jurusan.nama_jurusan')
			->join('students', 'students.id = surat.siswa_id')
			->join('kelas', 'kelas.id = students.kelas_id')
			->join('jurusan', 'jurusan.id = students.jurusan_id')
			->where('surat.id', $id)
			->first();

		if (!$data['surat']) {
			throw \CodeIgniter\Exceptions\PageNotFoundException::forPageNotFound("Surat dengan ID $id tidak ditemukan.");
		}
		
		return view('admin/surat_edit', $data);
    }

    // ✅ Update surat
    public function update($id)
	{
		// Langkah 1: Siapkan data dari form
		$siswa_id = $this->request->getPost('siswa_id');
		$jenis    = $this->request->getPost('jenis');
		$status   = $this->request->getPost('status');
		
		//dd($siswa_id, $jenis, $status);

		// Langkah 2: Update surat dan attendance sesuai kondisi
		// Update surat terlebih dahulu
		$this->suratModel->update($id, [
			'status' => $status
		]);

		// Logika kondisi untuk update attendance
		if ($jenis === 'sakit' && $status === 'disetujui') {
			$this->attendanceModel->where('siswa_id', $siswa_id)->set(['status' => 'sakit'])->update();
		} elseif ($jenis === 'izin' && $status === 'disetujui') {
			$this->attendanceModel->where('siswa_id', $siswa_id)->set(['status' => 'izin'])->update();
		}
		// Jika status ditolak, hanya update surat (sudah dilakukan di atas)

		return redirect()->to('/admin/surat')->with('success', 'Surat berhasil diperbarui.');
	}

}
