<?php

namespace App\Models;

use CodeIgniter\Model;

class SiswaAkademikModel extends Model
{
    protected $table         = 'siswa_akademik';
    protected $primaryKey    = 'id';
    protected $returnType    = 'array';
    protected $useTimestamps = true;

    protected $createdField  = 'created_at';
    protected $updatedField  = 'updated_at';

    protected $allowedFields = [
        'siswa_id',
        'kelas_id',
        'jurusan_id',
        'tapel_id',
        'jam_belajar_id',
        'status_akademik_id',
        'tanggal_naik_kelas'
    ];

    /*
    |--------------------------------------------------------------------------
    | INDEX BERDASARKAN TAPEL
    |--------------------------------------------------------------------------
    */
    public function getSiswaAkademikByTapel($tapelId, $perPage = 10)
    {
        return $this->select("
                siswa_akademik.*,
                siswa.nama_siswa,
                siswa.nis,
                kelas.nama_kelas,
                jurusan.nama_jurusan,
                jam_belajar.shift,
                jam_belajar.jam_masuk,
                jam_belajar.jam_pulang
            ")
            ->join('siswa', 'siswa.id = siswa_akademik.siswa_id')
            ->join('kelas', 'kelas.id = siswa_akademik.kelas_id')
            ->join('jurusan', 'jurusan.id = siswa_akademik.jurusan_id', 'left')
            ->join('jam_belajar', 'jam_belajar.id = siswa_akademik.jam_belajar_id')
            ->where('siswa_akademik.tapel_id', $tapelId)
            ->orderBy('kelas.nama_kelas', 'ASC')
            ->orderBy('siswa.nama_siswa', 'ASC')
            ->paginate($perPage);
    }

    /*
    |--------------------------------------------------------------------------
    | SISWA BELUM MASUK TAPEL AKTIF
    |--------------------------------------------------------------------------
    */
    public function getSiswaBelumPlot($tapelId)
    {
        return $this->db->table('siswa')
            ->select('siswa.*')
            ->join(
                'siswa_akademik',
                'siswa_akademik.siswa_id = siswa.id
                 AND siswa_akademik.tapel_id = ' . (int)$tapelId,
                'left'
            )
            ->where('siswa_akademik.id', null)
            ->orderBy('siswa.nama_siswa', 'ASC')
            ->get()
            ->getResultArray();
    }

    /*
    |--------------------------------------------------------------------------
    | CEK SISWA SUDAH TERPLOT
    |--------------------------------------------------------------------------
    */
    public function cekSudahPlot($siswaId, $tapelId)
    {
        return $this->where('siswa_id', $siswaId)
            ->where('tapel_id', $tapelId)
            ->countAllResults();
    }

    /*
    |--------------------------------------------------------------------------
    | AKADEMIK AKTIF
    |--------------------------------------------------------------------------
    */
    public function getAkademikAktif($siswaId, $tapelId)
    {
        return $this->where('siswa_id', $siswaId)
            ->where('tapel_id', $tapelId)
            ->where('status_akademik_id', 1)
            ->first();
    }

    /*
    |--------------------------------------------------------------------------
    | SISWA AKTIF BERDASARKAN KELAS
    |--------------------------------------------------------------------------
    */
    public function getSiswaAktifByKelas($kelasId, $tapelId)
    {
        return $this->where('kelas_id', $kelasId)
            ->where('tapel_id', $tapelId)
            ->where('status_akademik_id', 1)
            ->findAll();
    }

    /*
    |--------------------------------------------------------------------------
    | NONAKTIFKAN AKADEMIK
    |--------------------------------------------------------------------------
    */
    public function nonaktifkanAkademik($id)
    {
        return $this->update($id, [
            'status_akademik_id' => 2
        ]);
    }

    /*
    |--------------------------------------------------------------------------
    | DETAIL
    |--------------------------------------------------------------------------
    */
    public function getDetail($id)
    {
        return $this->find($id);
    }

    /*
    |--------------------------------------------------------------------------
    | DATA PROMOTE
    |--------------------------------------------------------------------------
    */
    public function getSiswaPromote($tapelId, $perPage = 10)
    {
        return $this->select("
                siswa_akademik.*,
                siswa.nis,
                siswa.nama_siswa,
                kelas.nama_kelas
            ")
            ->join('siswa', 'siswa.id = siswa_akademik.siswa_id')
            ->join('kelas', 'kelas.id = siswa_akademik.kelas_id')
            ->where('siswa_akademik.tapel_id', $tapelId)
            ->where('siswa_akademik.status_akademik_id', 1)
            ->orderBy('kelas.nama_kelas', 'ASC')
            ->paginate($perPage);
    }

    /*
    |--------------------------------------------------------------------------
    | SEARCH SELECT2 IZIN
    |--------------------------------------------------------------------------
    */
    public function searchSiswaAktif($term = null, $tapelId = null)
    {
        $builder = $this->select("
                siswa_akademik.id as siswa_akademik_id,
                siswa.id as siswa_id,
                siswa.nama_siswa,
                siswa.nis,
                kelas.nama_kelas,
                jurusan.nama_jurusan
            ")
            ->join('siswa', 'siswa.id = siswa_akademik.siswa_id')
            ->join('kelas', 'kelas.id = siswa_akademik.kelas_id')
            ->join('jurusan', 'jurusan.id = siswa_akademik.jurusan_id', 'left')
            ->where('siswa_akademik.status_akademik_id', 1);

        if (!empty($tapelId)) {
            $builder->where('siswa_akademik.tapel_id', $tapelId);
        } else {
            $builder->join('tapel', 'tapel.id = siswa_akademik.tapel_id')
                    ->where('tapel.aktif', 1);
        }

        if (!empty($term)) {
            $builder->groupStart()
                ->like('siswa.nama_siswa', $term)
                ->orLike('siswa.nis', $term)
                ->groupEnd();
        }

        return $builder
            ->orderBy('siswa.nama_siswa', 'ASC')
            ->findAll(10);
    }
}
