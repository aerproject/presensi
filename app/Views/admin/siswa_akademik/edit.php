<?= $this->extend('layouts/admin') ?>
<?= $this->section('content') ?>

<h3>Edit Penempatan Siswa</h3>

<form action="<?= base_url('admin/siswa-akademik/update/' . $siswa['id']) ?>" method="post">

    <!-- ===================== -->
    <!-- JAM BELAJAR -->
    <!-- ===================== -->
    <div class="mb-3">
        <label>Jam Belajar / Shift</label>

        <select name="jam_belajar_id" class="form-control" required>

            <option value="">Pilih Shift</option>

            <?php foreach ($jam_belajar as $jb): ?>
                <option value="<?= $jb['id'] ?>"
                    <?= $jb['id'] == $siswa['jam_belajar_id'] ? 'selected' : '' ?>>

                    <?= ucfirst($jb['shift']) ?>
                    (<?= $jb['jam_masuk'] ?> - <?= $jb['jam_pulang'] ?>)

                </option>
            <?php endforeach; ?>

        </select>
    </div>

    <hr>

    <!-- ===================== -->
    <!-- KELAS -->
    <!-- ===================== -->
    <div class="mb-3">
        <label>Kelas</label>

        <select name="kelas_id" class="form-control" required>

            <?php foreach ($kelas as $k): ?>

                <option value="<?= $k['id'] ?>"
                    <?= $k['id'] == $siswa['kelas_id'] ? 'selected' : '' ?>>

                    <?= $k['nama_kelas'] ?>

                </option>

            <?php endforeach; ?>

        </select>
    </div>

    <!-- ===================== -->
    <!-- JURUSAN -->
    <!-- ===================== -->
    <div class="mb-3">
        <label>Jurusan</label>

        <select name="jurusan_id" class="form-control" required>

            <?php foreach ($jurusan as $j): ?>

                <option value="<?= $j['id'] ?>"
                    <?= $j['id'] == $siswa['jurusan_id'] ? 'selected' : '' ?>>

                    <?= $j['nama_jurusan'] ?>

                </option>

            <?php endforeach; ?>

        </select>
    </div>

    <!-- ===================== -->
    <!-- BUTTON -->
    <!-- ===================== -->
    <button type="submit" class="btn btn-primary">
        Update Data
    </button>

    <a href="<?= base_url('admin/siswa-akademik') ?>"
       class="btn btn-secondary">
        Batal / Kembali
    </a>

</form>

<?= $this->endSection() ?>
