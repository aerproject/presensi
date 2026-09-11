<?= $this->extend('layouts/admin') ?>
<?= $this->section('content') ?>

<div class="container-fluid mt-4">

    <h3 class="mb-4">
        <i class="bi bi-gear-fill me-2"></i>Pengaturan Aplikasi
    </h3>

    <?php if(session()->getFlashdata('success')): ?>
        <div class="alert alert-success">
            <?= session()->getFlashdata('success') ?>
        </div>
    <?php endif; ?>

    <?php if(session()->getFlashdata('error')): ?>
        <div class="alert alert-danger">
            <?= session()->getFlashdata('error') ?>
        </div>
    <?php endif; ?>

<form action="<?= base_url('admin/aplikasi/update') ?>" method="post" enctype="multipart/form-data">

<input type="hidden" name="id" value="<?= $pengaturan['id'] ?? 1 ?>">

<!-- IDENTITAS -->

<div class="card mb-4">
<div class="card-header bg-primary text-white">Identitas</div>
<div class="card-body">

<div class="mb-3">
<label>Nama Aplikasi</label>
<input type="text" name="nama_aplikasi" class="form-control"
value="<?= $pengaturan['nama_aplikasi'] ?? '' ?>">
</div>

<div class="mb-3">
<label>Nama Sekolah</label>
<input type="text" name="nama_sekolah" class="form-control"
value="<?= $pengaturan['nama_sekolah'] ?? '' ?>">
</div>

<div class="mb-3">
<label>Tahun Pelajaran</label>

<select name="tahun_pelajaran" class="form-select">

<?php foreach($tapelList as $tapel): ?>

<option value="<?= $tapel['tahun_pelajaran'] ?>"
<?= ($pengaturan['tahun_pelajaran'] == $tapel['tahun_pelajaran']) ? 'selected':'' ?>>

<?= $tapel['tahun_pelajaran'] ?>

</option>

<?php endforeach; ?>

</select>
</div>

</div>
</div>


<!-- HARI KERJA -->

<div class="card mb-4">
<div class="card-header bg-success text-white">Hari Kerja</div>
<div class="card-body">

<div class="row">

<?php foreach($hariKerja as $hari): ?>

<div class="col-md-2">

<div class="form-check">

<input type="checkbox"
class="form-check-input"
name="hari_kerja[]"
value="<?= $hari['hari'] ?>"
<?= $hari['aktif'] ? 'checked' : '' ?>

>

<label class="form-check-label">

<?= $hari['hari'] ?>

</label>

</div>

</div>

<?php endforeach; ?>

</div>

</div>
</div>


<!-- JAM BELAJAR -->

<div class="card mb-4">

<div class="card-header bg-info text-white">

Master Jam Belajar

</div>

<div class="card-body">

<table class="table table-bordered">

<thead>

<tr>
<th>Shift</th>
<th>Jam Masuk</th>
<th>Jam Pulang</th>
</tr>

</thead>

<tbody>

<?php foreach($jamBelajar as $jb): ?>

<tr>

<td>

<?= ucfirst($jb['shift']) ?>

</td>

<td>

<input type="time"

name="jam_belajar[<?= $jb['id'] ?>][jam_masuk]"

value="<?= substr($jb['jam_masuk'],0,5) ?>"

class="form-control">

</td>

<td>

<input type="time"

name="jam_belajar[<?= $jb['id'] ?>][jam_pulang]"

value="<?= substr($jb['jam_pulang'],0,5) ?>"

class="form-control">

</td>

</tr>

<?php endforeach; ?>

</tbody>

</table>

</div>

</div>


<!-- BATAS ABSENSI -->

<div class="card mb-4">

<div class="card-header bg-warning text-dark">
    Batas Waktu Absensi
</div>

<div class="card-body">

<div class="alert alert-info mb-4">
    <i class="bi bi-info-circle me-1"></i>
    Atur toleransi waktu absensi berdasarkan jam masuk dan jam pulang pada Master Jam Belajar.
</div>

<div class="row">

<div class="col-md-6">

<h6 class="fw-semibold mb-3">Absensi Masuk</h6>

<div class="mb-3">
<label class="form-label">Boleh absen sebelum jam masuk</label>

<div class="input-group">
<input type="number"
       name="batas_masuk_sebelum"
       class="form-control"
       min="0"
       step="1"
       value="<?= esc($pengaturan['batas_masuk_sebelum'] ?? 60) ?>">

<span class="input-group-text">menit</span>
</div>

<small class="text-muted">
Contoh: 60 = boleh absen mulai 1 jam sebelum jam masuk.
</small>
</div>

<div class="mb-3">
<label class="form-label">Boleh absen setelah jam masuk</label>

<div class="input-group">
<input type="number"
       name="batas_masuk_sesudah"
       class="form-control"
       min="0"
       step="1"
       value="<?= esc($pengaturan['batas_masuk_sesudah'] ?? 120) ?>">

<span class="input-group-text">menit</span>
</div>

<small class="text-muted">
Contoh: 120 = batas absensi masuk sampai 2 jam setelah jam masuk.
</small>
</div>

</div>


<div class="col-md-6">

<h6 class="fw-semibold mb-3">Absensi Pulang</h6>

<div class="mb-3">
<label class="form-label">Boleh absen sebelum jam pulang</label>

<div class="input-group">
<input type="number"
       name="batas_pulang_sebelum"
       class="form-control"
       min="0"
       step="1"
       value="<?= esc($pengaturan['batas_pulang_sebelum'] ?? 0) ?>">

<span class="input-group-text">menit</span>
</div>

<small class="text-muted">
Contoh: 0 = siswa hanya boleh absen mulai tepat pada jam pulang.
</small>
</div>

<div class="mb-3">
<label class="form-label">Boleh absen setelah jam pulang</label>

<div class="input-group">
<input type="number"
       name="batas_pulang_sesudah"
       class="form-control"
       min="0"
       step="1"
       value="<?= esc($pengaturan['batas_pulang_sesudah'] ?? 180) ?>">

<span class="input-group-text">menit</span>
</div>

<small class="text-muted">
Contoh: 180 = batas absensi pulang sampai 3 jam setelah jam pulang.
</small>
</div>

</div>

</div>

</div>
</div>


<!-- FILE -->

<div class="card mb-4 shadow-sm">

    <div class="card-header bg-secondary text-white">
        Dokumen Sekolah
    </div>

    <div class="card-body">

        <!-- ========================= -->
        <!-- LOGO SEKOLAH -->
        <!-- ========================= -->

        <div class="mb-4">

            <label class="form-label fw-semibold">
                Logo Sekolah
            </label>

            <input type="file"
                   name="logo_sekolah"
                   class="form-control">

            <?php if (!empty($pengaturan['logo_sekolah'])): ?>

                <div class="mt-3">

                    <small class="text-muted d-block mb-2">
                        Logo saat ini:
                    </small>

                    <img src="/uploads/logo/<?= esc($pengaturan['logo_sekolah']) ?>"
                         alt="Logo Sekolah"
                         class="img-thumbnail"
                         style="max-height:120px;">

                </div>

            <?php endif; ?>

        </div>


        <!-- ========================= -->
        <!-- KOP SURAT -->
        <!-- ========================= -->

        <div class="mb-3">

            <label class="form-label fw-semibold">
                Kop Surat
            </label>

            <input type="file"
                   name="kop_surat"
                   class="form-control">

            <?php if (!empty($pengaturan['kop_surat'])): ?>

                <div class="mt-3">

                    <small class="text-muted d-block mb-2">
                        Kop surat saat ini:
                    </small>

                    <img src="/uploads/kop/<?= esc($pengaturan['kop_surat']) ?>"
                         alt="Kop Surat"
                         class="img-thumbnail"
                         style="max-height:120px;">

                </div>

            <?php endif; ?>

        </div>

    </div>

</div>


<button type="submit" class="btn btn-primary">

Simpan Pengaturan

</button>

</form>

</div>

<?= $this->endSection() ?>