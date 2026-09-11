<?= $this->extend('layouts/admin') ?>

<?= $this->section('content') ?>
<div class="container-fluid">

    <h4>Edit Wali Kelas</h4>

    <form action="<?= base_url('/admin/walikelas/update/' . $walas['id']) ?>" method="post">

        <div class="mb-3">
            <label>Guru</label>
            <select name="guru_id" class="form-control">
                <option value="">-- Pilih Guru --</option>
                <?php foreach ($guru as $g): ?>
                    <option value="<?= $g['id'] ?>"
                        <?= ($walas['guru_id'] ?? '') == $g['id'] ? 'selected' : '' ?>>
                        <?= esc($g['nama_guru']) ?>
                    </option>
                <?php endforeach ?>
            </select>
        </div>

        <div class="mb-3">
            <label>Kelas</label>
            <select name="kelas_id" class="form-control">
                <option value="">-- Pilih Kelas --</option>
                <?php foreach ($kelas as $k): ?>
                    <option value="<?= $k['id'] ?>"
                        <?= ($walas['kelas_id'] ?? '') == $k['id'] ? 'selected' : '' ?>>
                        <?= esc($k['nama_kelas'] ?? $k['kelas']) ?>
                    </option>
                <?php endforeach ?>
            </select>
        </div>

        <div class="mb-3">
            <label>Tahun Pelajaran</label>
            <select name="tapel_id" class="form-control">
                <option value="">-- Pilih Tahun Pelajaran --</option>
                <?php foreach ($tapel as $t): ?>
                    <option value="<?= $t['id'] ?>"
                        <?= ($walas['tapel_id'] ?? '') == $t['id'] ? 'selected' : '' ?>>
                        <?= esc($t['tahun_pelajaran']) ?>
                    </option>
                <?php endforeach ?>
            </select>
        </div>

        <div class="mb-3">
            <label>Skema Absensi</label>
            <select name="skema_absen" class="form-control">
                <option value="full_day"
                    <?= ($walas['skema_absen'] ?? '') == 'full_day' ? 'selected' : '' ?>>
                    Full Day
                </option>
                <option value="blok_time"
                    <?= ($walas['skema_absen'] ?? '') == 'blok_time' ? 'selected' : '' ?>>
                    Blok Time
                </option>
            </select>
        </div>

        <div class="mb-3">
            <label>Sesi</label>
            <select name="sesi" class="form-control">
                <option value="pagi"
                    <?= ($walas['sesi'] ?? '') == 'pagi' ? 'selected' : '' ?>>
                    Pagi
                </option>
                <option value="siang"
                    <?= ($walas['sesi'] ?? '') == 'siang' ? 'selected' : '' ?>>
                    Siang
                </option>
            </select>
        </div>

        <button type="submit" class="btn btn-success">
            Simpan
        </button>

        <a href="<?= base_url('/admin/walikelas') ?>"
           class="btn btn-secondary">
            Kembali
        </a>

    </form>

</div>
<?= $this->endSection() ?>
