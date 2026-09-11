<?php

namespace App\Controllers\Admin;

use App\Controllers\BaseController;
use App\Models\MessageModel;

class Pesan extends BaseController
{
    protected $messageModel;

    public function __construct()
    {
        $this->messageModel = new MessageModel();
    }

    public function index()
    {
        // Filter
        $nama    = trim($this->request->getGet('nama') ?? '');
        $kelas   = $this->request->getGet('kelas');
        $status  = $this->request->getGet('status');
        $jenis   = $this->request->getGet('jenis');
        $perPage = (int) ($this->request->getGet('perPage') ?? 10);

        if ($perPage <= 0) {
            $perPage = 10;
        }

        // Query pesan
        $modelPesan = $this->messageModel->getFilteredPesan(
            $nama,
            $kelas,
            $status,
            $jenis
        );

        // Pagination
        $pesan = $modelPesan->paginate($perPage, 'default');

        // Summary GLOBAL dari tabel messages
        $summary = $this->messageModel->getStatusSummary();

        // Master kelas
        $kelasList = model('KelasModel')
            ->orderBy('nama_kelas', 'ASC')
            ->findAll();

        return view('admin/pesan/index', [
            'title'     => 'Notifikasi Log WhatsApp | Admin Panel',
            'pesan'     => $pesan,
            'pager'     => $this->messageModel->pager,
            'perPage'   => $perPage,
            'kelasList' => $kelasList,

            'nama'      => $nama,
            'kelas'     => $kelas,
            'status'    => $status,
            'jenis'     => $jenis,

            'summary'   => $summary,
        ]);
    }
}