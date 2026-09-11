<?php

namespace App\Models;

use CodeIgniter\Model;

class MessageModel extends Model
{
    protected $table            = 'messages';
    protected $primaryKey       = 'id';

    protected $allowedFields    = [
        'users_id',
        'jenis_pesan',
        'isi_pesan',
        'waktu_kirim',
        'status',
        'created_at',
        'updated_at'
    ];

    protected $useTimestamps = true;
    protected $createdField  = 'created_at';
    protected $updatedField  = 'updated_at';

    /**
     * Query monitor pesan admin.
     *
     * Tidak bergantung pada siswa_akademik/tapel aktif.
     * Kelas/jurusan hanya ditampilkan jika data akademik tersedia.
     */
    public function getFilteredPesan(
        $nama = null,
        $kelas = null,
        $status = null,
        $jenis = null
    ) {
        $this->select(
            'messages.*,
             siswa.nama_siswa,
             siswa.nis,
             kelas.nama_kelas,
             jurusan.nama_jurusan'
        )
        ->join(
            'users',
            'users.id = messages.users_id',
            'left'
        )
        ->join(
            'siswa',
            'siswa.users_id = users.id',
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
        );

        if (!empty($nama)) {
            $this->groupStart()
                ->like('siswa.nama_siswa', $nama)
                ->orLike('siswa.nis', $nama)
                ->groupEnd();
        }

        if (!empty($kelas)) {
            $this->where('kelas.id', $kelas);
        }

        if (!empty($status)) {
            $this->where('messages.status', $status);
        }

        if (!empty($jenis)) {
            $this->where('messages.jenis_pesan', $jenis);
        }

        return $this->orderBy('messages.waktu_kirim', 'DESC');
    }

    public function getStatusSummary(): array
    {
        $rows = $this->select('status, COUNT(*) AS total')
            ->groupBy('status')
            ->get()
            ->getResultArray();

        $summary = [
            'pending' => 0,
            'sent'    => 0,
            'failed'  => 0,
        ];

        foreach ($rows as $row) {
            $key = strtolower((string) $row['status']);

            if (array_key_exists($key, $summary)) {
                $summary[$key] = (int) $row['total'];
            }
        }

        return $summary;
    }

    public function getFailedMessages()
    {
        return $this->where('status', 'failed')
            ->orderBy('waktu_kirim', 'ASC')
            ->findAll();
    }

    public function getPendingMessages()
    {
        return $this->where('status', 'pending')
            ->orderBy('waktu_kirim', 'ASC')
            ->findAll();
    }

    public function updateStatus(int $id, string $status)
    {
        return $this->update($id, [
            'status' => $status
        ]);
    }
}
