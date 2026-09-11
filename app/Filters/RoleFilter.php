<?php

namespace App\Filters;

use CodeIgniter\HTTP\RequestInterface;
use CodeIgniter\HTTP\ResponseInterface;
use CodeIgniter\Filters\FilterInterface;

class RoleFilter implements FilterInterface
{
    public function before(RequestInterface $request, $arguments = null)
    {
        $session = session();

        // Jika belum login
        if (!$session->get('isLoggedIn')) {
            return redirect()->to('/auth/login');
        }

        $role = $session->get('role');

        // Jika ada argumen role yang diminta, cek apakah sesuai
        if ($arguments && !in_array($role, $arguments)) {

            return redirect()->to('/admin/dashboard')
                ->with('error', 'Anda tidak memiliki akses ke menu ini.');

        }

        // Khusus operator: blokir akses ke menu pengaturan
        if ($role === 'operator') {
            // gunakan service('uri') atau $request->getUri()
            $uri = service('uri'); 
            // contoh URL: /admin/pengaturan → segment1=admin, segment2=pengaturan
            $segment2 = $uri->getSegment(2);

            if ($segment2 === 'pengaturan') {
                return redirect()->to('/admin/dashboard')
                                 ->with('error', 'Operator tidak boleh mengakses menu Pengaturan.');
            }
        }

        // Jika lolos semua pengecekan, lanjutkan
    }

    public function after(RequestInterface $request, ResponseInterface $response, $arguments = null)
    {
        // Tidak digunakan
    }
}
