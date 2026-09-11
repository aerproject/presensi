<?= $this->extend('layouts/admin') ?>

<?= $this->section('content') ?>
<div class="container-fluid mt-4">

    <h3 class="mb-3">Buat Pesan Broadcast</h3>

    <?php if (session()->getFlashdata('error')): ?>
        <div class="alert alert-danger alert-dismissible fade show" role="alert">
            <?= session()->getFlashdata('error') ?>
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    <?php elseif (session()->getFlashdata('success')): ?>
        <div class="alert alert-success alert-dismissible fade show" role="alert">
            <?= session()->getFlashdata('success') ?>
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    <?php endif; ?>

    <form id="broadcastForm" action="<?= base_url('/admin/broadcast/store') ?>" method="post" data-phone-url="<?= base_url('/admin/broadcast/getPhoneByNis') ?>">
        <?= csrf_field() ?>

        <!-- Jenis Pesan -->
        <div class="mb-3">
            <label for="jenis_pesan" class="form-label">Jenis Pesan</label>
            <select class="form-select" id="jenis_pesan" name="jenis_pesan" required>
                <option value="">-- Pilih Jenis Pesan --</option>
                <option value="perorangan">Perorangan</option>
                <option value="kelas">Kelas</option>
                <option value="tingkat">Tingkat</option>
                <option value="jurusan">Jurusan</option>
            </select>
        </div>

        <!-- Perorangan -->
        <div class="mb-3 d-none" id="field_perorangan">
            <label for="nis" class="form-label">NIS Siswa</label>
            <input type="hidden" id="student_id" name="student_id">
            <input type="text" class="form-control" id="nis" name="nis" placeholder="contoh: 20231234">
            <div class="form-text">Masukkan NIS untuk mencari nomor WA orang tua.</div>

            <label for="phone" class="form-label mt-3">Nomor WA Tujuan</label>
            <input type="text" class="form-control" id="phone" name="phone" placeholder="contoh: 6281234567890" readonly>
            <div class="form-text">Nomor WA orang tua akan otomatis muncul setelah NIS ditemukan.</div>
        </div>

        <!-- Kelas -->
        <div class="mb-3 d-none" id="field_kelas">
            <label for="kelas_id" class="form-label">Pilih Kelas</label>
            <select class="form-select" id="kelas_id" name="kelas_id">
                <option value="">-- Pilih Kelas --</option>
                <?php foreach ($kelasList as $k): ?>
                    <option value="<?= $k['id'] ?>"><?= esc($k['nama_kelas']) ?></option>
                <?php endforeach; ?>
            </select>
        </div>

        <!-- Tingkat -->
        <div class="mb-3 d-none" id="field_tingkat">
            <label for="tingkat" class="form-label">Pilih Tingkat</label>
            <select class="form-select" id="tingkat" name="tingkat">
                <option value="">-- Pilih Tingkat --</option>
                <option value="X">Kelas 10</option>
                <option value="XI">Kelas 11</option>
                <option value="XII">Kelas 12</option>
            </select>
        </div>

        <!-- Jurusan -->
        <div class="mb-3 d-none" id="field_jurusan">
            <label for="jurusan_id" class="form-label">Pilih Jurusan</label>
            <select class="form-select" id="jurusan_id" name="jurusan_id">
                <option value="">-- Pilih Jurusan --</option>
                <?php foreach ($jurusanList as $j): ?>
                    <option value="<?= $j['id'] ?>"><?= esc($j['nama_jurusan']) ?></option>
                <?php endforeach; ?>
            </select>
        </div>

        <!-- Judul -->
        <div class="mb-3">
            <label for="judul" class="form-label">Judul Pesan</label>
            <input type="text" class="form-control" id="judul" name="judul" required>
        </div>

        <!-- Isi Pesan -->
        <div class="mb-3">
            <label for="isi_pesan" class="form-label">Isi Pesan</label>
            <textarea
                id="isi_pesan"
                name="isi_pesan"
                class="form-control"
                rows="10"
                placeholder="Tulis pesan WhatsApp di sini..."
                required
            ></textarea>
            <div class="form-text">
                Gunakan teks biasa. Baris baru akan dipertahankan saat pesan dikirim.
            </div>
        </div>

        <button type="submit" class="btn btn-primary">Simpan Broadcast</button>
    </form>
    <script src="<?= base_url('js/admin-broadcast.js') ?>"></script>
</div>
<?= $this->endSection() ?>
