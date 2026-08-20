<?php

namespace App\Controllers;

use App\Models\UserModel;

class Auth extends BaseController
{
    protected UserModel $userModel;

    public function __construct()
    {
        $this->userModel = new UserModel();
    }

    // Menampilkan form login
    public function login()
    {
        // Jika sudah login, arahkan berdasarkan role
        if (session()->get('logged_in')) {
            if (session()->get('is_admin')) {
                return redirect()->to('/dashboard');
            }
            return redirect()->to('/'); // ganti sesuai route homepage Anda
        }

        return view('auth/login');
    }

    // Memproses submit form login
    public function attemptLogin()
    {
        $rules = [
            'username' => 'required',
            'password' => 'required',
        ];

        if (! $this->validate($rules)) {
            return redirect()->back()->withInput()->with('errors', $this->validator->getErrors());
        }

        $username = $this->request->getPost('username');
        $password = $this->request->getPost('password');

        $user = $this->userModel->where('username', $username)->first();

        // Ambil creden admin dari .env
        $envUsername = getenv('ADMIN_USERNAME');
        $envPassword = getenv('ADMIN_PASSWORD');

        $isAdmin = false;

        // 1) Jika user ada di DB dan password cocok -> pakai data DB
        if ($user && ! empty($user['password']) && password_verify($password, $user['password'])) {
            $isAdmin = isset($user['is_admin']) && (int) $user['is_admin'] === 1;

            session()->set([
                'user_id'   => $user['id'] ?? null,
                'username'  => $user['username'] ?? $username,
                'nama'      => $user['nama_lengkap'] ?? $user['username'] ?? $username,
                'no_hp'     => $user['no_hp'] ?? $user['nomer_hp'] ?? null, // <- tambahin ini
                'is_admin'  => $isAdmin,
                'logged_in' => true,
            ]);
        }
        // 2) Jika tidak ada di DB tapi cocok dengan ADMIN di .env -> treat as admin
        elseif ($username === $envUsername && $password === $envPassword) {
            $isAdmin = true;

            session()->set([
                'user_id'   => null,
                'username'  => $envUsername,
                'nama'      => $envUsername,
                'is_admin'  => true,
                'logged_in' => true,
            ]);
        }
        // 3) Kalau keduanya gagal -> error
        else {
            return redirect()->back()->withInput()->with('error', 'Username atau password salah.');
        }

        // Redirect berdasarkan role
        if ($isAdmin) {
            return redirect()->to('/dashboard')->with('success', 'Login berhasil, selamat datang ' . session()->get('nama') . '!');
        }

        return redirect()->to('/')->with('success', 'Login berhasil, selamat datang ' . session()->get('nama') . '!'); // ganti '/' kalau punya route lain
    }

    public function logout()
    {
        session()->destroy();
        return redirect()->to('/')->with('success', 'Anda telah logout.');
    }

    public function register()
    {
        return view('auth/register');
    }

    public function attemptRegister()
    {
        helper('form');

        $rules = [
            'nama_lengkap' => 'required|min_length[3]',
            'username'     => 'required|min_length[3]|is_unique[users.username]',
            'email'        => 'required|valid_email',
            'nomer_hp'     => 'required',
            'password'     => 'required|min_length[6]',
        ];

        if (! $this->validate($rules)) {
            log_message('debug', 'Register validation errors: ' . print_r($this->validator->getErrors(), true));
            return redirect()->back()->withInput()->with('errors', $this->validator->getErrors());
        }

        $data = [
            'nama_lengkap' => $this->request->getPost('nama_lengkap'),
            'username'     => $this->request->getPost('username'),
            'email'        => $this->request->getPost('email'),
            'no_hp'        => $this->request->getPost('nomer_hp'),
            'alamat'       => $this->request->getPost('alamat'),
            'password'     => password_hash($this->request->getPost('password'), PASSWORD_DEFAULT),
            'status'       => 'calon_penyewa',
        ];

        // log data (tanpa password sebenarnya, tapi untuk debug singkat)
        log_message('debug', 'Register payload: ' . print_r(array_diff_key($data, ['password'=>1]), true));

        $insertId = $this->userModel->insert($data);

        if ($insertId === false) {
            // log model errors dan kembalikan ke form
            log_message('debug', 'UserModel insert errors: ' . print_r($this->userModel->errors(), true));
            return redirect()->back()->withInput()->with('error', 'Gagal membuat akun. Cek log untuk detail.');
        }

        log_message('debug', 'New user id: ' . $insertId);

        return redirect()->to('/login')->with('success', 'Akun berhasil dibuat. Silakan login.');
    }
}
