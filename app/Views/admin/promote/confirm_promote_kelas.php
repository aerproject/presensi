<?= $this->extend('layouts/admin') ?>
<?= $this->section('content') ?>

<h3>Konfirmasi Naik Kelas Massal</h3>

<div class="card">
    <div class="card-body">

        <table class="table">

            <tr>
                <td>Kelas Sekarang</td>
                <td><b><?= $kelasAwal['nama_kelas'] ?></b></td>
            </tr>

            <tr>
                <td>Naik Ke</td>
                <td>
                    <b>
                        <?= $kelasTujuan['nama_kelas'] ?? 'LULUS' ?>
                    </b>
                </td>
            </tr>

            <tr>
                <td>Jumlah Siswa</td>
                <td><?= $jumlahSiswa ?> siswa</td>
            </tr>

            <tr>
                <td>Tahun Pelajaran</td>
                <td><?= $tapel['tahun_pelajaran'] ?></td>
            </tr>

        </table>

        <form action="<?= base_url('admin/promote/kelas/execute/'.$kelasAwal['id']) ?>" method="post">

            <div class="alert alert-warning">
                Apakah yakin semua siswa pada kelas ini akan diproses?
            </div>

            <button type="submit" class="btn btn-success">
                Ya, Proses Naik Kelas
            </button>

            <a href="<?= base_url('admin/promote') ?>" class="btn btn-secondary">
                Batal
            </a>

        </form>

    </div>
</div>

<?= $this->endSection() ?>