<?php

namespace App\Commands;

use CodeIgniter\CLI\BaseCommand;
use CodeIgniter\CLI\CLI;
use App\Models\SiswaAkademikModel;

class TestWalasSiswa extends BaseCommand
{
    protected $group = 'Temp';
    protected $name = 'temp:test-walas-siswa';
    protected $description = 'Diagnostik query siswa walikelas';

    public function run(array $params)
    {
        $model = new SiswaAkademikModel();

        $builder = $model
            ->select("
                siswa_akademik.id AS siswa_akademik_id,
                siswa_akademik.siswa_id,
                siswa_akademik.kelas_id,
                siswa_akademik.jurusan_id,
                siswa_akademik.tapel_id,
                siswa_akademik.status_akademik_id,
                siswa.nis,
                siswa.nama_siswa,
                kelas.nama_kelas,
                jurusan.nama_jurusan,
                jam_belajar.shift,
                jam_belajar.jam_masuk,
                jam_belajar.jam_pulang
            ")
            ->join('siswa', 'siswa.id = siswa_akademik.siswa_id')
            ->join('kelas', 'kelas.id = siswa_akademik.kelas_id')
            ->join('jurusan', 'jurusan.id = siswa_akademik.jurusan_id', 'left')
            ->join('jam_belajar', 'jam_belajar.id = siswa_akademik.jam_belajar_id', 'left')
            ->where('siswa_akademik.kelas_id', 1)
            ->where('siswa_akademik.tapel_id', 2)
            ->where('siswa_akademik.status_akademik_id', 1)
            ->orderBy('siswa.nama_siswa', 'ASC');

        CLI::write('===== SQL SEBELUM PAGINATE =====', 'yellow');
        CLI::write($model->builder()->getCompiledSelect(false));

        CLI::newLine();

        $rows = $builder->paginate(10, 'siswa');

        CLI::write('===== HASIL PAGINATE =====', 'yellow');
        CLI::write('JUMLAH: ' . count($rows));

        foreach ($rows as $row) {
            CLI::write(
                $row['nis'] . ' | ' .
                $row['nama_siswa'] . ' | ' .
                'status_akademik_id=' . $row['status_akademik_id']
            );
        }

        CLI::newLine();

        CLI::write('===== PAGER =====', 'yellow');
        CLI::write(get_class($model->pager));
    }
}
