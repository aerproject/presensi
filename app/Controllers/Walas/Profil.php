<?php

namespace App\Controllers\Walas;

use App\Controllers\BaseController;
use App\Models\UserModel;

class Profil extends BaseController
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
        if ($response = $this->requireRole('walikelas')) {
            return $response;
        }

        $user = $this->currentUser();

        if (!$user) {
            return redirect()->to('/auth/login');
        }

        return view('walas/profil', [
            'title' => 'Profil Saya',
            'user'  => $user,
        ]);
    }

    public function password()
    {
        if ($response = $this->requireRole('walikelas')) {
            return $response;
        }

        $user = $this->currentUser();

        if (!$user) {
            return redirect()->to('/auth/login');
        }

        return view('walas/profil_password', [
            'title' => 'Ganti Password',
            'user'  => $user,
        ]);
    }

    public function updatePassword()
    {
        if ($response = $this->requireRole('walikelas')) {
            return $response;
        }

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
            return redirect()->back()
                ->withInput()
                ->with('errors', $errors);
        }

        $this->userModel->update((int) $user['id'], [
            'password' => password_hash($password, PASSWORD_DEFAULT),
            'updated_at' => date('Y-m-d H:i:s'),
        ]);

        return redirect()
            ->to('/walikelas/profil')
            ->with('success', 'Password berhasil diubah.');
    }
}
