<?php

namespace App\Filters;

use CodeIgniter\Filters\FilterInterface;
use CodeIgniter\HTTP\RequestInterface;
use CodeIgniter\HTTP\ResponseInterface;

class RoleFilter implements FilterInterface
{
    public function before(RequestInterface $request, $arguments = null)
    {
        if (! session()->get('isLoggedIn')) {
            return redirect()->to(site_url('login'));
        }

        $role = (string) session()->get('role');
        $allowed = is_array($arguments) ? $arguments : [];

        if ($allowed === [] || in_array($role, $allowed, true)) {
            return null;
        }

        return redirect()->to(site_url('dashboard'))->with('error', 'Anda tidak memiliki akses ke halaman ini.');
    }

    public function after(RequestInterface $request, ResponseInterface $response, $arguments = null)
    {
        return null;
    }
}
