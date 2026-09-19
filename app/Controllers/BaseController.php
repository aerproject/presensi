<?php

namespace App\Controllers;

use CodeIgniter\Controller;
use CodeIgniter\HTTP\CLIRequest;
use CodeIgniter\HTTP\IncomingRequest;
use CodeIgniter\HTTP\RequestInterface;
use CodeIgniter\HTTP\ResponseInterface;
use Psr\Log\LoggerInterface;

abstract class BaseController extends Controller
{
    protected $request;

    protected $helpers = ['form'];

    protected $session;
    protected $db;   // TAMBAH INI

    public function initController(
        RequestInterface $request,
        ResponseInterface $response,
        LoggerInterface $logger
    ) {
        parent::initController($request, $response, $logger);


        $this->db = \Config\Database::connect();   // TAMBAH INI
    }


    protected function requireAdmin()
    {
        if (
            session()->get('role') !== 'admin'
        ) {
            return redirect()
                ->to('/admin/dashboard')
                ->with(
                    'error',
                    'Anda tidak memiliki akses ke menu ini.'
                );
        }

        return null;
    }

    protected function requireAdminOperator()
    {
        $role = session()->get('role');

        if (!in_array($role, ['admin', 'operator'])) {

            return redirect()
                ->to('/admin/dashboard')
                ->with(
                    'error',
                    'Anda tidak memiliki akses ke menu ini.'
                );
        }

        return null;
    }


    /**
     * Guard User Management
     *
     * Admin:
     * - akses penuh
     *
     * Operator:
     * - boleh masuk menu user
     * - pembatasan aksi dilakukan di Users controller
     */
    protected function requireUserManagement()
    {
        $role = session()->get('role');

        if (!in_array($role, ['admin', 'operator'])) {

            return redirect()
                ->to('/admin/dashboard')
                ->with(
                    'error',
                    'Anda tidak memiliki akses ke menu user.'
                );
        }

        return null;
    }


    /**
     * Guard Role Umum
     *
     * Digunakan untuk controller:
     * - walikelas
     * - ortu
     * - siswa
     *
     * Contoh:
     * requireRole('siswa')
     * requireRole(['admin','operator'])
     */
    protected function requireRole($roles)
    {
        $role = session()->get('role');

        if (!in_array($role, (array) $roles, true)) {

            return redirect()
                ->to('/auth/login')
                ->with(
                    'error',
                    'Anda tidak memiliki akses ke halaman ini.'
                );
        }

        return null;
    }


}