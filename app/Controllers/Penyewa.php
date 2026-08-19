<?php

namespace App\Controllers;

use App\Models\UserModel; // Panggil UserModel

class Penyewa extends BaseController
{
    protected UserModel $userModel;

    public function __construct()
    {
        $this->userModel = new UserModel();
    }

    public function index()
    {
        // Ambil semua data pengguna (bisa dikecualikan admin kalau mau, tapi ini ambil semua dulu)
        $pengguna = $this->userModel->orderBy('created_at', 'DESC')->findAll();

        $data = [
            'title'    => 'Data Pengguna',
            'pengguna' => $pengguna,
        ];

        return view('penyewa/index', $data);
    }
}
