<?php

namespace App\Controllers\Auth;

use App\Controllers\BaseController;
use App\Models\UserModel;

class AuthController extends BaseController
{
    public function loginForm()
    {
        return view('auth/login', ['title' => 'Login PLPI']);
    }

    public function login()
    {
        $username = trim((string) $this->request->getPost('username'));
        $password = (string) $this->request->getPost('password');

        if ($username === '' || $password === '') {
            return redirect()->back()->withInput()->with('error', 'Username dan password wajib diisi.');
        }

        $userModel = new UserModel();
        $user = $userModel
            ->groupStart()
            ->where('username', $username)
            ->orWhere('email', $username)
            ->groupEnd()
            ->first();

        if (! $user || ! (bool) ($user['is_active'] ?? 0)) {
            return redirect()->back()->withInput()->with('error', 'Akun tidak ditemukan atau tidak aktif.');
        }
        if (! password_verify($password, (string) ($user['password'] ?? ''))) {
            return redirect()->back()->withInput()->with('error', 'Username atau password salah.');
        }

        session()->set([
            'isLoggedIn' => true,
            'user_id'    => (int) $user['id'],
            'username'   => (string) $user['username'],
            'name'       => (string) $user['name'],
            'email'      => (string) $user['email'],
            'role'       => (string) $user['role'],
        ]);

        return redirect()->to(site_url('dashboard'));
    }

    public function logout()
    {
        session()->destroy();

        return redirect()->to(site_url('login'))->with('success', 'Berhasil logout.');
    }
}
