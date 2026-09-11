#!/usr/bin/env bash
set -euo pipefail

APP="/www/wwwroot/presensi.duamei.web.id"
cd "$APP"

STAMP="$(date +%Y%m%d-%H%M%S)"
BACKUP="writable/admin-user-backup-${STAMP}"
mkdir -p "$BACKUP"

echo "== ADMIN USER MANAGEMENT INSTALL =="
echo "APP=$APP"
echo "BACKUP=$BACKUP"

backup_if_exists() {
  local f="$1"
  if [ -f "$f" ]; then
    mkdir -p "$BACKUP/$(dirname "$f")"
    cp -a "$f" "$BACKUP/$f"
    echo "BACKUP $f"
  fi
}

backup_if_exists app/Controllers/Admin/Users.php
backup_if_exists app/Controllers/Admin/Account.php
backup_if_exists app/Views/admin/users/index.php
backup_if_exists app/Views/admin/users/create.php
backup_if_exists app/Views/admin/users/edit.php
backup_if_exists app/Views/admin/users/password.php
backup_if_exists app/Views/admin/account/index.php
backup_if_exists app/Config/Routes.php
backup_if_exists app/Views/_partials/sidebar.php

mkdir -p app/Controllers/Admin
mkdir -p app/Views/admin/users
mkdir -p app/Views/admin/account

cat > app/Controllers/Admin/Users.php <<'PHP'
<?php

namespace App\Controllers\Admin;

use App\Controllers\BaseController;
use App\Models\UserModel;

class Users extends BaseController
{
    protected UserModel $userModel;

    private const ROLES = [
        'admin'     => 'Admin',
        'siswa'     => 'Siswa',
        'ortu'      => 'Orang Tua',
        'walikelas' => 'Wali Kelas',
    ];

    public function __construct()
    {
        $this->userModel = new UserModel();
    }

    private function requireAdmin()
    {
        if (session()->get('role') !== 'admin') {
            return redirect()->to('/admin/dashboard')
                ->with('error', 'Akses Manajemen User hanya untuk administrator.');
        }

        return null;
    }

    public function index()
    {
        if ($response = $this->requireAdmin()) {
            return $response;
        }

        $q    = trim((string) $this->request->getGet('q'));
        $role = trim((string) $this->request->getGet('role'));

        $builder = $this->userModel
            ->select('id, username, email, role, created_at, updated_at')
            ->orderBy('id', 'DESC');

        if ($q !== '') {
            $builder->groupStart()
                ->like('username', $q)
                ->orLike('email', $q)
                ->orLike('role', $q)
                ->groupEnd();
        }

        if ($role !== '' && array_key_exists($role, self::ROLES)) {
            $builder->where('role', $role);
        }

        return view('admin/users/index', [
            'title' => 'Manajemen User | Admin Panel Absensi Digital',
            'users' => $builder->findAll(),
            'q'     => $q,
            'role'  => $role,
            'roles' => self::ROLES,
        ]);
    }

    public function create()
    {
        if ($response = $this->requireAdmin()) {
            return $response;
        }

        return view('admin/users/create', [
            'title' => 'Tambah User | Admin Panel Absensi Digital',
            'roles' => self::ROLES,
        ]);
    }

    public function store()
    {
        if ($response = $this->requireAdmin()) {
            return $response;
        }

        $username = trim((string) $this->request->getPost('username'));
        $email    = trim((string) $this->request->getPost('email'));
        $password = (string) $this->request->getPost('password');
        $confirm  = (string) $this->request->getPost('password_confirm');
        $role     = trim((string) $this->request->getPost('role'));

        $errors = $this->validateUserInput($username, $email, $password, $confirm, $role, null);

        if ($errors !== []) {
            return redirect()->back()->withInput()->with('errors', $errors);
        }

        $this->userModel->insert([
            'username' => $username,
            'email'    => $email,
            'password' => password_hash($password, PASSWORD_DEFAULT),
            'role'     => $role,
        ]);

        return redirect()->to('/admin/users')
            ->with('success', 'User berhasil ditambahkan.');
    }

    public function edit(int $id)
    {
        if ($response = $this->requireAdmin()) {
            return $response;
        }

        $user = $this->userModel
            ->select('id, username, email, role, created_at, updated_at')
            ->find($id);

        if (!$user) {
            return redirect()->to('/admin/users')
                ->with('error', 'User tidak ditemukan.');
        }

        return view('admin/users/edit', [
            'title' => 'Edit User | Admin Panel Absensi Digital',
            'user'  => $user,
            'roles' => self::ROLES,
        ]);
    }

    public function update(int $id)
    {
        if ($response = $this->requireAdmin()) {
            return $response;
        }

        $user = $this->userModel->find($id);

        if (!$user) {
            return redirect()->to('/admin/users')
                ->with('error', 'User tidak ditemukan.');
        }

        $username = trim((string) $this->request->getPost('username'));
        $email    = trim((string) $this->request->getPost('email'));
        $role     = trim((string) $this->request->getPost('role'));

        $errors = $this->validateUserInput($username, $email, '', '', $role, $id);

        if ($errors !== []) {
            return redirect()->back()->withInput()->with('errors', $errors);
        }

        if (($user['role'] ?? '') === 'admin' && $role !== 'admin') {
            $adminCount = $this->userModel
                ->where('role', 'admin')
                ->countAllResults();

            if ($adminCount <= 1) {
                return redirect()->back()->withInput()->with(
                    'error',
                    'User ini adalah administrator terakhir. Tambahkan admin lain sebelum mengubah role.'
                );
            }
        }

        \Config\Database::connect()->table('users')->where('id', $id)->update([
            'username' => $username,
            'email'    => $email,
            'role'     => $role,
            'updated_at' => date('Y-m-d H:i:s'),
        ]);

        if ((int) session()->get('user_id') === $id) {
            session()->set([
                'username' => $username,
                'role'     => $role,
            ]);
        }

        return redirect()->to('/admin/users')
            ->with('success', 'Data user berhasil diperbarui.');
    }

    public function password(int $id)
    {
        if ($response = $this->requireAdmin()) {
            return $response;
        }

        $user = $this->userModel
            ->select('id, username, email, role')
            ->find($id);

        if (!$user) {
            return redirect()->to('/admin/users')
                ->with('error', 'User tidak ditemukan.');
        }

        return view('admin/users/password', [
            'title' => 'Reset Password | Admin Panel Absensi Digital',
            'user'  => $user,
        ]);
    }

    public function updatePassword(int $id)
    {
        if ($response = $this->requireAdmin()) {
            return $response;
        }

        $user = $this->userModel->find($id);

        if (!$user) {
            return redirect()->to('/admin/users')
                ->with('error', 'User tidak ditemukan.');
        }

        $password = (string) $this->request->getPost('password');
        $confirm  = (string) $this->request->getPost('password_confirm');

        $errors = [];

        if (strlen($password) < 6) {
            $errors['password'] = 'Password minimal 6 karakter.';
        }

        if ($password !== $confirm) {
            $errors['password_confirm'] = 'Konfirmasi password tidak sama.';
        }

        if ($errors !== []) {
            return redirect()->back()->withInput()->with('errors', $errors);
        }

        \Config\Database::connect()->table('users')->where('id', $id)->update([
            'password' => password_hash($password, PASSWORD_DEFAULT),
            'updated_at' => date('Y-m-d H:i:s'),
        ]);

        return redirect()->to('/admin/users')
            ->with('success', 'Password user berhasil diubah.');
    }

    private function validateUserInput(
        string $username,
        string $email,
        string $password,
        string $confirm,
        string $role,
        ?int $ignoreId
    ): array {
        $errors = [];

        if ($username === '' || !preg_match('/^[A-Za-z0-9._-]{3,50}$/', $username)) {
            $errors['username'] = 'Username 3–50 karakter dan hanya boleh berisi huruf, angka, titik, garis bawah, atau tanda hubung.';
        }

        $usernameQuery = $this->userModel->where('username', $username);
        if ($ignoreId !== null) {
            $usernameQuery->where('id !=', $ignoreId);
        }
        if ($username !== '' && $usernameQuery->countAllResults() > 0) {
            $errors['username'] = 'Username sudah digunakan.';
        }

        if ($email === '' || !filter_var($email, FILTER_VALIDATE_EMAIL)) {
            $errors['email'] = 'Email tidak valid.';
        }

        $emailQuery = $this->userModel->where('email', $email);
        if ($ignoreId !== null) {
            $emailQuery->where('id !=', $ignoreId);
        }
        if ($email !== '' && $emailQuery->countAllResults() > 0) {
            $errors['email'] = 'Email sudah digunakan.';
        }

        if (!array_key_exists($role, self::ROLES)) {
            $errors['role'] = 'Role user tidak valid.';
        }

        if ($password !== '') {
            if (strlen($password) < 6) {
                $errors['password'] = 'Password minimal 6 karakter.';
            }

            if ($password !== $confirm) {
                $errors['password_confirm'] = 'Konfirmasi password tidak sama.';
            }
        }

        return $errors;
    }
}
PHP

cat > app/Controllers/Admin/Account.php <<'PHP'
<?php

namespace App\Controllers\Admin;

use App\Controllers\BaseController;
use App\Models\UserModel;

class Account extends BaseController
{
    protected UserModel $userModel;

    public function __construct()
    {
        $this->userModel = new UserModel();
    }

    private function currentUser(): ?array
    {
        $userId = (int) session()->get('user_id');

        if ($userId <= 0) {
            return null;
        }

        return $this->userModel->find($userId) ?: null;
    }

    public function index()
    {
        $user = $this->currentUser();

        if (!$user) {
            return redirect()->to('/auth/login');
        }

        return view('admin/account/index', [
            'title' => 'Akun Saya | Admin Panel Absensi Digital',
            'user'  => $user,
        ]);
    }

    public function password()
    {
        return $this->index();
    }

    public function updatePassword()
    {
        $user = $this->currentUser();

        if (!$user) {
            return redirect()->to('/auth/login');
        }

        $old      = (string) $this->request->getPost('current_password');
        $password = (string) $this->request->getPost('password');
        $confirm  = (string) $this->request->getPost('password_confirm');

        $errors = [];

        if (!password_verify($old, $user['password'] ?? '')) {
            $errors['current_password'] = 'Password lama tidak sesuai.';
        }

        if (strlen($password) < 6) {
            $errors['password'] = 'Password baru minimal 6 karakter.';
        }

        if ($password !== $confirm) {
            $errors['password_confirm'] = 'Konfirmasi password baru tidak sama.';
        }

        if ($old !== '' && $password === $old) {
            $errors['password'] = 'Password baru harus berbeda dari password lama.';
        }

        if ($errors !== []) {
            return redirect()->back()->withInput()->with('errors', $errors);
        }

        \Config\Database::connect()->table('users')->where('id', (int) $user['id'])->update([
            'password' => password_hash($password, PASSWORD_DEFAULT),
            'updated_at' => date('Y-m-d H:i:s'),
        ]);

        return redirect()->to('/admin/account')
            ->with('success', 'Password akun berhasil diubah.');
    }
}
PHP

cat > app/Views/admin/users/index.php <<'PHP'
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
                <div class="col-md-3 d-flex gap-2">
                    <button class="btn btn-outline-primary flex-fill">
                        <i class="bi bi-search me-1"></i> Cari
                    </button>
                    <a href="<?= base_url('/admin/users') ?>" class="btn btn-outline-secondary">Reset</a>
                </div>
            </form>

            <div class="table-responsive">
                <table class="table table-hover align-middle mb-0">
                    <thead class="table-light">
                    <tr>
                        <th style="width:60px">#</th>
                        <th>Username</th>
                        <th>Email</th>
                        <th>Role</th>
                        <th>Dibuat</th>
                        <th style="width:230px">Aksi</th>
                    </tr>
                    </thead>
                    <tbody>
                    <?php if (empty($users)): ?>
                        <tr><td colspan="6" class="text-center text-muted py-4">Tidak ada user ditemukan.</td></tr>
                    <?php else: ?>
                        <?php foreach ($users as $i => $row): ?>
                            <?php
                                $roleLabel = $roles[$row['role']] ?? ucfirst((string) $row['role']);
                                $isSelf = (int) session()->get('user_id') === (int) $row['id'];
                                $badge = match ($row['role']) {
                                    'admin' => 'danger',
                                    'operator' => 'warning',
                                    'walikelas' => 'info',
                                    'ortu' => 'success',
                                    default => 'secondary',
                                };
                            ?>
                            <tr>
                                <td><?= $i + 1 ?></td>
                                <td>
                                    <strong><?= esc($row['username']) ?></strong>
                                    <?php if ($isSelf): ?><span class="badge text-bg-success ms-1">Anda</span><?php endif; ?>
                                </td>
                                <td><?= esc($row['email'] ?: '-') ?></td>
                                <td><span class="badge text-bg-<?= $badge ?>"><?= esc($roleLabel) ?></span></td>
                                <td><?= esc($row['created_at'] ?: '-') ?></td>
                                <td>
                                    <div class="d-flex flex-wrap gap-1">
                                        <a href="<?= base_url('/admin/users/edit/' . $row['id']) ?>" class="btn btn-sm btn-outline-primary">
                                            <i class="bi bi-pencil"></i> Edit
                                        </a>
                                        <a href="<?= base_url('/admin/users/password/' . $row['id']) ?>" class="btn btn-sm btn-outline-warning">
                                            <i class="bi bi-key"></i> Password
                                        </a>
                                    </div>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

<?= $this->endSection() ?>
PHP

cat > app/Views/admin/users/create.php <<'PHP'
<?= $this->extend('layouts/admin') ?>
<?= $this->section('content') ?>

<div class="container-fluid py-3">
    <div class="mb-3"><h3 class="mb-1">Tambah User</h3><div class="text-muted">Buat akun pengguna baru.</div></div>

    <?php if ($message = session()->getFlashdata('error')): ?>
        <div class="alert alert-danger"><?= esc($message) ?></div>
    <?php endif; ?>
    <?php $errors = session()->getFlashdata('errors') ?? []; ?>
    <?php if ($errors): ?>
        <div class="alert alert-danger"><ul class="mb-0"><?php foreach ($errors as $error): ?><li><?= esc($error) ?></li><?php endforeach; ?></ul></div>
    <?php endif; ?>

    <div class="card shadow-sm border-0"><div class="card-body">
        <form method="post" action="<?= base_url('/admin/users/store') ?>">
            <?= csrf_field() ?>
            <div class="row g-3">
                <div class="col-md-6">
                    <label class="form-label">Username <span class="text-danger">*</span></label>
                    <input type="text" name="username" class="form-control" maxlength="50" required value="<?= esc(old('username')) ?>">
                    <div class="form-text">3–50 karakter: huruf, angka, titik, underscore, atau tanda hubung.</div>
                </div>
                <div class="col-md-6">
                    <label class="form-label">Email <span class="text-danger">*</span></label>
                    <input type="email" name="email" class="form-control" maxlength="100" required value="<?= esc(old('email')) ?>">
                </div>
                <div class="col-md-6">
                    <label class="form-label">Role <span class="text-danger">*</span></label>
                    <select name="role" class="form-select" required>
                        <option value="">Pilih role</option>
                        <?php foreach ($roles as $value => $label): ?>
                            <option value="<?= esc($value) ?>" <?= old('role') === $value ? 'selected' : '' ?>><?= esc($label) ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>
                <div class="col-md-6"></div>
                <div class="col-md-6">
                    <label class="form-label">Password <span class="text-danger">*</span></label>
                    <input type="password" name="password" class="form-control" minlength="6" required autocomplete="new-password">
                </div>
                <div class="col-md-6">
                    <label class="form-label">Konfirmasi Password <span class="text-danger">*</span></label>
                    <input type="password" name="password_confirm" class="form-control" minlength="6" required autocomplete="new-password">
                </div>
            </div>
            <div class="mt-4 d-flex gap-2">
                <button class="btn btn-primary"><i class="bi bi-save me-1"></i> Simpan User</button>
                <a href="<?= base_url('/admin/users') ?>" class="btn btn-secondary">Batal</a>
            </div>
        </form>
    </div></div>
</div>

<?= $this->endSection() ?>
PHP

cat > app/Views/admin/users/edit.php <<'PHP'
<?= $this->extend('layouts/admin') ?>
<?= $this->section('content') ?>

<div class="container-fluid py-3">
    <div class="mb-3"><h3 class="mb-1">Edit User</h3><div class="text-muted">Perbarui identitas dan role akun.</div></div>

    <?php if ($message = session()->getFlashdata('error')): ?><div class="alert alert-danger"><?= esc($message) ?></div><?php endif; ?>
    <?php $errors = session()->getFlashdata('errors') ?? []; ?>
    <?php if ($errors): ?>
        <div class="alert alert-danger"><ul class="mb-0"><?php foreach ($errors as $error): ?><li><?= esc($error) ?></li><?php endforeach; ?></ul></div>
    <?php endif; ?>

    <div class="card shadow-sm border-0"><div class="card-body">
        <form method="post" action="<?= base_url('/admin/users/update/' . $user['id']) ?>">
            <?= csrf_field() ?>
            <div class="row g-3">
                <div class="col-md-6">
                    <label class="form-label">Username <span class="text-danger">*</span></label>
                    <input type="text" name="username" class="form-control" maxlength="50" required value="<?= esc(old('username', $user['username'])) ?>">
                </div>
                <div class="col-md-6">
                    <label class="form-label">Email <span class="text-danger">*</span></label>
                    <input type="email" name="email" class="form-control" maxlength="100" required value="<?= esc(old('email', $user['email'])) ?>">
                </div>
                <div class="col-md-6">
                    <label class="form-label">Role <span class="text-danger">*</span></label>
                    <select name="role" class="form-select" required>
                        <?php foreach ($roles as $value => $label): ?>
                            <option value="<?= esc($value) ?>" <?= old('role', $user['role']) === $value ? 'selected' : '' ?>><?= esc($label) ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>
            </div>
            <div class="alert alert-light border mt-4 mb-0">
                <i class="bi bi-info-circle me-1"></i> Untuk mengganti password gunakan tombol <strong>Password</strong> dari halaman Manajemen User.
            </div>
            <div class="mt-4 d-flex gap-2">
                <button class="btn btn-primary"><i class="bi bi-save me-1"></i> Simpan Perubahan</button>
                <a href="<?= base_url('/admin/users') ?>" class="btn btn-secondary">Batal</a>
            </div>
        </form>
    </div></div>
</div>

<?= $this->endSection() ?>
PHP

cat > app/Views/admin/users/password.php <<'PHP'
<?= $this->extend('layouts/admin') ?>
<?= $this->section('content') ?>

<div class="container-fluid py-3">
    <div class="mb-3">
        <h3 class="mb-1">Reset Password User</h3>
        <div class="text-muted">Atur password baru untuk akun <strong><?= esc($user['username']) ?></strong>.</div>
    </div>

    <?php $errors = session()->getFlashdata('errors') ?? []; ?>
    <?php if ($errors): ?>
        <div class="alert alert-danger"><ul class="mb-0"><?php foreach ($errors as $error): ?><li><?= esc($error) ?></li><?php endforeach; ?></ul></div>
    <?php endif; ?>

    <div class="card shadow-sm border-0"><div class="card-body">
        <div class="row mb-4">
            <div class="col-md-6"><div class="small text-muted">Username</div><strong><?= esc($user['username']) ?></strong></div>
            <div class="col-md-6"><div class="small text-muted">Role</div><strong><?= esc($user['role']) ?></strong></div>
        </div>

        <form method="post" action="<?= base_url('/admin/users/password/' . $user['id']) ?>">
            <?= csrf_field() ?>
            <div class="row g-3">
                <div class="col-md-6">
                    <label class="form-label">Password Baru <span class="text-danger">*</span></label>
                    <input type="password" name="password" class="form-control" minlength="6" required autocomplete="new-password">
                </div>
                <div class="col-md-6">
                    <label class="form-label">Konfirmasi Password Baru <span class="text-danger">*</span></label>
                    <input type="password" name="password_confirm" class="form-control" minlength="6" required autocomplete="new-password">
                </div>
            </div>
            <div class="alert alert-warning mt-4">
                <i class="bi bi-shield-lock me-1"></i> Password disimpan menggunakan hashing password aplikasi. Password lama tidak dapat ditampilkan.
            </div>
            <div class="d-flex gap-2">
                <button class="btn btn-warning"><i class="bi bi-key me-1"></i> Simpan Password</button>
                <a href="<?= base_url('/admin/users') ?>" class="btn btn-secondary">Batal</a>
            </div>
        </form>
    </div></div>
</div>

<?= $this->endSection() ?>
PHP

cat > app/Views/admin/account/index.php <<'PHP'
<?= $this->extend('layouts/admin') ?>
<?= $this->section('content') ?>

<div class="container-fluid py-3">
    <div class="mb-3"><h3 class="mb-1">Akun Saya</h3><div class="text-muted">Kelola password akun yang sedang digunakan.</div></div>

    <?php if ($message = session()->getFlashdata('success')): ?>
        <div class="alert alert-success alert-dismissible fade show" role="alert"><?= esc($message) ?><button type="button" class="btn-close" data-bs-dismiss="alert"></button></div>
    <?php endif; ?>
    <?php if ($message = session()->getFlashdata('error')): ?>
        <div class="alert alert-danger alert-dismissible fade show" role="alert"><?= esc($message) ?><button type="button" class="btn-close" data-bs-dismiss="alert"></button></div>
    <?php endif; ?>
    <?php $errors = session()->getFlashdata('errors') ?? []; ?>
    <?php if ($errors): ?>
        <div class="alert alert-danger"><ul class="mb-0"><?php foreach ($errors as $error): ?><li><?= esc($error) ?></li><?php endforeach; ?></ul></div>
    <?php endif; ?>

    <div class="row g-3">
        <div class="col-lg-5">
            <div class="card shadow-sm border-0 h-100"><div class="card-body">
                <h5 class="mb-3"><i class="bi bi-person-circle me-2"></i>Informasi Akun</h5>
                <dl class="row mb-0">
                    <dt class="col-sm-4">Username</dt><dd class="col-sm-8"><?= esc($user['username']) ?></dd>
                    <dt class="col-sm-4">Email</dt><dd class="col-sm-8"><?= esc($user['email'] ?: '-') ?></dd>
                    <dt class="col-sm-4">Role</dt><dd class="col-sm-8"><?= esc($user['role']) ?></dd>
                </dl>
            </div></div>
        </div>

        <div class="col-lg-7">
            <div class="card shadow-sm border-0"><div class="card-body">
                <h5 class="mb-3"><i class="bi bi-key me-2"></i>Ubah Password</h5>
                <form method="post" action="<?= base_url('/admin/account/password') ?>">
                    <?= csrf_field() ?>
                    <div class="mb-3">
                        <label class="form-label">Password Lama <span class="text-danger">*</span></label>
                        <input type="password" name="current_password" class="form-control" required autocomplete="current-password">
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Password Baru <span class="text-danger">*</span></label>
                        <input type="password" name="password" class="form-control" minlength="6" required autocomplete="new-password">
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Konfirmasi Password Baru <span class="text-danger">*</span></label>
                        <input type="password" name="password_confirm" class="form-control" minlength="6" required autocomplete="new-password">
                    </div>
                    <button class="btn btn-primary"><i class="bi bi-shield-check me-1"></i> Ubah Password</button>
                </form>
            </div></div>
        </div>
    </div>
</div>

<?= $this->endSection() ?>
PHP

python3 - <<'PY'
from pathlib import Path

# ROUTES
p = Path("app/Config/Routes.php")
s = p.read_text()

needle = "$routes->group('admin', ['namespace' => 'App\Controllers\Admin', 'filter' => 'role:admin,operator'], function($routes) {"
if needle not in s:
    raise SystemExit("ERROR: admin route group anchor tidak ditemukan. Jalankan: grep -n \"group('admin'\" app/Config/Routes.php")

route_block = """\n    // USER MANAGEMENT + ACCOUNT\n    $routes->get('users', 'Users::index');\n    $routes->get('users/create', 'Users::create');\n    $routes->post('users/store', 'Users::store');\n    $routes->get('users/edit/(:num)', 'Users::edit/$1');\n    $routes->post('users/update/(:num)', 'Users::update/$1');\n    $routes->get('users/password/(:num)', 'Users::password/$1');\n    $routes->post('users/password/(:num)', 'Users::updatePassword/$1');\n\n    $routes->get('account', 'Account::index');\n    $routes->get('account/password', 'Account::password');\n    $routes->post('account/password', 'Account::updatePassword');\n"""

if "Users::index" not in s:
    s = s.replace(needle, needle + route_block, 1)
    p.write_text(s)

# SIDEBAR
p = Path("app/Views/_partials/sidebar.php")
s = p.read_text()

s = s.replace(
    "in_array($segment2, ['aplikasi', 'upload', 'wapikey', 'qrcode'])",
    "in_array($segment2, ['aplikasi', 'upload', 'wapikey', 'qrcode', 'users', 'account'])"
)

anchor = '<li><a class="nav-link text-white" href="<?= base_url(\'/admin/qrcode\') ?>">Generate QR Code</a></li>'
if "base_url('/admin/users')" not in s:
    desktop_insert = anchor + """
                        <li>
                            <a class="nav-link text-white <?= ($segment2 === 'users') ? 'active bg-primary bg-opacity-25 rounded' : '' ?>"
                               href="<?= base_url('/admin/users') ?>">
                                <i class="bi bi-people me-2"></i> Manajemen User
                            </a>
                        </li>
                        <li>
                            <a class="nav-link text-white <?= ($segment2 === 'account') ? 'active bg-primary bg-opacity-25 rounded' : '' ?>"
                               href="<?= base_url('/admin/account') ?>">
                                <i class="bi bi-person-circle me-2"></i> Akun Saya
                            </a>
                        </li>"""
    if anchor not in s:
        raise SystemExit("ERROR: Generate QR Code anchor tidak ditemukan.")
    s = s.replace(anchor, desktop_insert, 1)

    # The mobile menu contains a second copy of the same QR link.
    mobile_insert = anchor + """
                        <li><a class="nav-link text-white" href="<?= base_url('/admin/users') ?>">Manajemen User</a></li>
                        <li><a class="nav-link text-white" href="<?= base_url('/admin/account') ?>">Akun Saya</a></li>"""
    pos = s.find(anchor)
    pos2 = s.find(anchor, pos + len(anchor))
    if pos2 != -1:
        s = s[:pos2] + mobile_insert + s[pos2 + len(anchor):]

p.write_text(s)
PY

echo
echo "== PHP LINT =="
php -l app/Controllers/Admin/Users.php
php -l app/Controllers/Admin/Account.php
php -l app/Config/Routes.php
php -l app/Views/_partials/sidebar.php

echo
echo "== ROUTE CHECK =="
php spark routes | grep -E "admin/(users|account)" || true

echo
echo "== MENU CHECK =="
grep -n -E "admin/(users|account)|Manajemen User|Akun Saya" app/Views/_partials/sidebar.php || true

echo
echo "== CACHE CLEAR =="
php spark cache:clear || true

echo
echo "INSTALL COMPLETE"
echo "Backup: $BACKUP"
