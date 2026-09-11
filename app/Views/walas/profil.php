<?= $this->extend('layouts/walas') ?>

<?= $this->section('content') ?>

<div class="container-fluid py-3">

    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h4 class="mb-1">Profil Saya</h4>
            <div class="text-muted">
                Informasi akun Wali Kelas
            </div>
        </div>

        <a href="<?= site_url('walikelas/profil/password') ?>"
           class="btn btn-primary">
            <i class="bi bi-key me-1"></i>
            Ganti Password
        </a>
    </div>

    <?php if (session()->getFlashdata('success')): ?>
        <div class="alert alert-success">
            <?= esc(session()->getFlashdata('success')) ?>
        </div>
    <?php endif; ?>

    <div class="card shadow-sm border-0">
        <div class="card-body">

            <div class="row mb-3">
                <div class="col-md-3 text-muted">
                    Username
                </div>
                <div class="col-md-9 fw-semibold">
                    <?= esc($user['username'] ?? '-') ?>
                </div>
            </div>

            <div class="row mb-3">
                <div class="col-md-3 text-muted">
                    Email
                </div>
                <div class="col-md-9">
                    <?= esc($user['email'] ?? '-') ?>
                </div>
            </div>

            <div class="row">
                <div class="col-md-3 text-muted">
                    Role
                </div>
                <div class="col-md-9">
                    <span class="badge bg-primary">
                        Wali Kelas
                    </span>
                </div>
            </div>

        </div>
    </div>

</div>

<?= $this->endSection() ?>
