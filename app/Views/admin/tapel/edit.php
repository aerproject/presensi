<?= $this->extend('layouts/admin') ?>
<?= $this->section('content') ?>

<div class="container-fluid mt-4">
    <h3 class="mb-3"><i class="bi bi-journal-text me-2"></i>Edit Tahun Pelajaran</h3>

    <!-- Alert -->
    <?php if(session()->getFlashdata('errors')): ?>
        <div class="alert alert-danger">
            <?php foreach(session()->getFlashdata('errors') as $error): ?>
                <div><?= esc($error) ?></div>
            <?php endforeach; ?>
        </div>
    <?php endif; ?>

    <form action="<?= base_url('/admin/tapel/update/'.$tapel['id']) ?>" method="post">
        <div class="mb-3">
            <label class="form-label">Tahun Pelajaran</label>
            <input type="text" name="tahun_pelajaran" class="form-control" 
                   value="<?= esc($tapel['tahun_pelajaran']) ?>" required>
        </div>
        <div class="mb-3">
            <label class="form-label">Semester</label>
            <select name="semester" class="form-select" required>
                <option value="Ganjil" <?= $tapel['semester']=='Ganjil'?'selected':'' ?>>Ganjil</option>
                <option value="Genap" <?= $tapel['semester']=='Genap'?'selected':'' ?>>Genap</option>
            </select>
        </div>
        <button type="submit" class="btn btn-success"><i class="bi bi-save"></i> Update</button>
        <a href="<?= base_url('/admin/tapel') ?>" class="btn btn-secondary">Kembali</a>
    </form>
</div>

<?= $this->endSection() ?>
