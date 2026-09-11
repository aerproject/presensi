<?= $this->extend('layouts/admin') ?>
<?= $this->section('content') ?>
<div class="container-fluid">

  <h4><?= esc($title) ?></h4>

  <?php if (session()->getFlashdata('error')): ?>
    <div class="alert alert-danger"><?= session()->getFlashdata('error') ?></div>
  <?php endif; ?>

  <form action="<?= base_url('admin/promote/preview') ?>" method="post">
    <?= csrf_field() ?>

    <!-- Tapel Lama -->
    <div class="mb-3">
      <label for="old_tapel_id" class="form-label">Tapel Lama</label>
      <select name="old_tapel_id" id="old_tapel_id" class="form-select" required>
        <option value="">-- Pilih Tapel Lama --</option>
        <?php foreach ($tapel as $t): ?>
          <option value="<?= $t['id'] ?>">
            <?= esc($t['tahun_pelajaran']) ?> (<?= esc($t['semester']) ?>)
          </option>
        <?php endforeach ?>
      </select>
    </div>

    <!-- Tapel Baru -->
    <div class="mb-3">
      <label for="new_tapel_id" class="form-label">Tapel Baru</label>
      <select name="new_tapel_id" id="new_tapel_id" class="form-select" required>
        <option value="">-- Pilih Tapel Baru --</option>
        <?php foreach ($tapel as $t): ?>
          <option value="<?= $t['id'] ?>">
            <?= esc($t['tahun_pelajaran']) ?> (<?= esc($t['semester']) ?>)
          </option>
        <?php endforeach ?>
      </select>
    </div>

    <button type="submit" class="btn btn-primary">Preview Naik Kelas</button>
    <a href="<?= base_url('admin/plotkelas') ?>" class="btn btn-secondary">Kembali</a>
  </form>

</div>
<?= $this->endSection() ?>
