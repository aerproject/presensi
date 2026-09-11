<?php

namespace App\Controllers;

use App\Models\UserModel;
use CodeIgniter\Controller;

class Auth extends Controller
{
    public function login()
    {
        // Tampilkan halaman login
        return view('auth/login');
    }

    public function attemptLogin()
    {
        $session  = session();
        $username = $this->request->getPost('username');
        $password = $this->request->getPost('password');

        $UserModel = new UserModel();
        $user      = $UserModel->where('username', $username)->first();

        if (!$user) {
            $session->setFlashdata('error', 'Username tidak ditemukan.');
            return redirect()->to('/auth/login');
        }

        // Validasi password
        if (!password_verify($password, $user['password'])) {
            $session->setFlashdata('error', 'Password salah.');
            return redirect()->to('/auth/login');
        }

        // Simpan sesi login
        $session->set([
            'user_id'    => $user['id'],
            'username'   => $user['username'],
            'role'       => $user['role'],
            'isLoggedIn' => true
        ]);

        // Redirect berdasarkan role
        switch ($user['role']) {
            case 'siswa':
                return redirect()->to('/siswa/dashboard');
            case 'ortu':
                return redirect()->to('/ortu/dashboard');
            case 'walikelas': // sesuaikan dengan enum di DB
                return redirect()->to('/walikelas/dashboard');
            case 'admin':
            case 'operator': // operator diarahkan ke dashboard admin
                return redirect()->to('/admin/dashboard');
            default:
                $session->destroy();
                return redirect()->to('/auth/login');
        }
    }

    public function logout()
    {
        session()->destroy();
        return redirect()->to('/');
    }
}
