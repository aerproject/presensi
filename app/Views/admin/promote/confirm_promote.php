<?= $this->extend('layouts/admin') ?>
<?= $this->section('content') ?>

<h3>Konfirmasi Naik Kelas</h3>

<div class="card">

    <div class="card-header bg-warning">
        <strong>Pastikan data promosi sudah benar</strong>
    </div>

    <div class="card-body">

        <table class="table table-bordered">

            <tr>
                <td width="30%">Nama Siswa</td>
                <td>
                    <b><?= $siswa['nama_siswa'] ?></b>
                </td>
            </tr>

            <tr>
                <td>NIS</td>
                <td><?= $siswa['nis'] ?></td>
            </tr>

            <tr>
                <td>Kelas Sekarang</td>
                <td>
                    <?= $siswa['nama_kelas'] ?> <?= $tapelSekarang['tahun_pelajaran'] ?>
                </td>
            </tr>

            <tr>
                <td>Naik Ke Kelas</td>
                <td>
                    <b>
                        <?= $kelasTujuan['nama_kelas'] ?? 'LULUS' ?> <?= $tapelTujuan['tahun_pelajaran'] ?? '-' ?>
                    </b>
                </td>
            </tr>

        </table>


        <form action="<?= base_url('admin/promote/naik/execute/'.$siswa['siswa_id']) ?>" method="post">

            <div class="alert alert-warning">

                <strong>Perhatian :</strong><br>

                Proses ini akan:

                <ul class="mb-0 mt-2">

                    <li>Menyimpan histori akademik lama</li>

                    <li>Membuat data akademik baru pada tahun pelajaran tujuan</li>

                    <li>Mengubah status akademik sebelumnya menjadi non aktif</li>

                </ul>

            </div>


            <button type="submit" class="btn btn-success">

                Ya, Naikkan Siswa

            </button>

            <a href="<?= base_url('admin/promote') ?>" class="btn btn-secondary">

                Batal

            </a>

        </form>

    </div>

</div>

<?= $this->endSection() ?>