<?= $this->extend('layouts/admin') ?>
<?= $this->section('content') ?>

<div class="container-fluid py-3">
    <div class="d-flex flex-wrap justify-content-between align-items-center gap-2 mb-3">
        <div>
            <h3 class="mb-1">Manajemen User</h3>
            <div class="text-muted">Kelola akun pengguna aplikasi presensi.</div>
        </div>
        <a href="<?= base_url('/admin/users/create') ?>" class="btn btn-primary">
            <i class="bi bi-person-plus me-1"></i> Tambah User
        </a>
    </div>

    <?php if ($message = session()->getFlashdata('success')): ?>
        <div class="alert alert-success alert-dismissible fade show" role="alert">
            <?= esc($message) ?><button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    <?php endif; ?>

    <?php if ($message = session()->getFlashdata('error')): ?>
        <div class="alert alert-danger alert-dismissible fade show" role="alert">
            <?= esc($message) ?><button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    <?php endif; ?>

    <div class="card shadow-sm border-0">
        <div class="card-body">
            <form method="get" action="<?= base_url('/admin/users') ?>" class="row g-2 mb-3">
                <div class="col-md-6">
                    <input type="search" name="q" class="form-control"
                           placeholder="Cari username, email, atau role..."
                           value="<?= esc($q) ?>">
                </div>
                <div class="col-md-3">
                    <select name="role" class="form-select">
                        <option value="">Semua Role</option>
                        <?php foreach ($roles as $value => $label): ?>
                            <option value="<?= esc($value) ?>" <?= $role === $value ? 'selected' : '' ?>>
                                <?= esc($label) ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>
                <div class="col-md-2">
                    <select name="perPage"
                            class="form-select"
                            onchange="this.form.submit()">
                        <option value="10" <?= $perPage == 10 ? 'selected' : '' ?>>
                            10 Data
                        </option>
                        <option value="25" <?= $perPage == 25 ? 'selected' : '' ?>>
                            25 Data
                        </option>
                        <option value="50" <?= $perPage == 50 ? 'selected' : '' ?>>
                            50 Data
                        </option>
                        <option value="100" <?= $perPage == 100 ? 'selected' : '' ?>>
                            100 Data
                        </option>
                    </select>
                </div>

                <div class="col-md-4 d-flex gap-2">
                    <button class="btn btn-outline-primary flex-fill">
                        <i class="bi bi-search me-1"></i> Cari
                    </button>
                    <a href="<?= base_url('/admin/users') ?>" class="btn btn-outline-secondary">
                        Reset
                    </a>
                </div>
            </form>

        </div>

    </div>


    <!-- ========================= -->
    <!-- TABLE -->
    <!-- ========================= -->
    <div class="card">

        <div class="card-body">

            <div class="table-responsive">

                <table class="table table-bordered table-hover align-middle">

                    <thead class="table-dark text-center">

                    <tr>

                        <th width="5%">NO</th>

                        <th>USERNAME</th>

                        <th>EMAIL</th>

                        <th width="15%">ROLE</th>

                        <th width="18%">DIBUAT</th>

                        <th width="20%">AKSI</th>

                    </tr>

                    </thead>


                    <tbody>

                    <?php if (empty($users)): ?>

                        <tr>

                            <td colspan="6"
                                class="text-center text-muted">

                                Tidak ada user ditemukan

                            </td>

                        </tr>

                    <?php else: ?>

                        <?php
                        $no = 1 + (($pager->getCurrentPage() - 1) * $perPage);

                        foreach ($users as $row):
                        ?>

                            <?php
                            $roleLabel = $roles[$row['role']]
                                ?? ucfirst((string) $row['role']);

                            $isSelf = (int) session()->get('user_id')
                                === (int) $row['id'];

                            $badge = match ($row['role']) {
                                'admin'     => 'danger',
                                'operator'  => 'warning',
                                'walikelas' => 'info',
                                'ortu'      => 'success',
                                default     => 'secondary',
                            };
                            ?>

                            <tr>

                                <td class="text-center">

                                    <?= $no++ ?>

                                </td>

                                <td>

                                    <strong>
                                        <?= esc($row['username']) ?>
                                    </strong>

                                    <?php if ($isSelf): ?>

                                        <span class="badge bg-success ms-1">
                                            Anda
                                        </span>

                                    <?php endif; ?>

                                </td>

                                <td>

                                    <?= esc($row['email'] ?: '-') ?>

                                </td>

                                <td class="text-center">

                                    <span class="badge bg-<?= $badge ?>">

                                        <?= esc($roleLabel) ?>

                                    </span>

                                </td>

                                <td class="text-center">

                                    <?= esc($row['created_at'] ?: '-') ?>

                                </td>

                                <td class="text-center">

                                    <a href="<?= base_url('/admin/users/edit/' . $row['id']) ?>"
                                       class="btn btn-sm btn-warning mb-1">

                                        <i class="bi bi-pencil-square"></i>
                                        Edit

                                    </a>

                                    <a href="<?= base_url('/admin/users/password/' . $row['id']) ?>"
                                       class="btn btn-sm btn-info mb-1">

                                        <i class="bi bi-key"></i>
                                        Password

                                    </a>

                                </td>

                            </tr>

                        <?php endforeach; ?>

                    <?php endif; ?>

                    </tbody>

                </table>

            </div>


            <!-- PAGINATION -->
            <div class="mt-3 d-flex justify-content-center">

                <?= $pager->links('default', 'bootstrap') ?>

            </div>

        </div>

    </div>

</div>

<?= $this->endSection() ?>
