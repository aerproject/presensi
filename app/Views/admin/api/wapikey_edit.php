<?= $this->extend('layouts/admin') ?>

<?= $this->section('content') ?>
<div class="container-fluid mt-4">

    <h3 class="mb-3">✏️ Edit Provider WhatsApp API</h3>

    <?php if (session()->getFlashdata('error')): ?>
      <div class="alert alert-danger alert-dismissible fade show" role="alert">
        <?= session()->getFlashdata('error') ?>
        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
      </div>
    <?php endif ?>

    <form action="<?= base_url('/admin/wapikey/update/'.$provider['id']) ?>" method="post">
        <?= csrf_field() ?>

        <div class="mb-3">
            <label for="provider" class="form-label">Provider</label>
            <input type="text" class="form-control" id="provider" name="provider" 
                   value="<?= esc($provider['provider'] ?? '') ?>" required>
            <div class="form-text">Isi sesuai nama provider, misalnya <code>onesender</code> atau <code>wisender</code>.</div>
        </div>

        <div class="mb-3">
            <label for="wa_api_url" class="form-label">URL Request</label>
            <input type="text" class="form-control" id="wa_api_url" name="wa_api_url" 
                   value="<?= esc($provider['wa_api_url'] ?? '') ?>" required>
        </div>
        
        <div class="mb-3">
            <label for="wa_api_key" class="form-label">WA API Key</label>
            <input type="text" class="form-control" id="wa_api_key" name="wa_api_key" 
                   value="<?= esc($provider['wa_api_key'] ?? '') ?>" required>
        </div>

        <div class="mb-3">
            <label for="admin_phone" class="form-label">Nomor WA Admin</label>
            <input type="text" class="form-control" id="admin_phone" name="admin_phone" 
                   value="<?= esc($provider['admin_phone'] ?? '') ?>" placeholder="contoh: 6281234567890">
            <div class="form-text">Gunakan format internasional (628xxx).</div>
        </div>

        <div class="mb-3">
            <label for="status" class="form-label">Status Provider</label>
            <select class="form-select" id="status" name="status" required>
                <option value="inactive" <?= (isset($provider['status']) && $provider['status'] === 'inactive') ? 'selected' : '' ?>>Inactive</option>
                <option value="active" <?= (isset($provider['status']) && $provider['status'] === 'active') ? 'selected' : '' ?>>Active</option>
            </select>
        </div>

        <button type="submit" class="btn btn-primary">Update Provider</button>
        <a href="<?= base_url('/admin/wapikey') ?>" class="btn btn-secondary">Kembali</a>
    </form>

</div>
<?= $this->endSection() ?>
