<?php

namespace App\Controllers\Admin;

use App\Controllers\BaseController;
use App\Models\UserModel;

class Users extends BaseController
{
    protected UserModel $userModel;

    private const ROLES = [
        'admin'     => 'Admin',
        'operator'  => 'Operator',
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

        $this->userModel->update($id, [
            'username' => $username,
            'email'    => $email,
            'role'     => $role,
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

        $this->userModel->update($id, [
            'password' => password_hash($password, PASSWORD_DEFAULT),
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
