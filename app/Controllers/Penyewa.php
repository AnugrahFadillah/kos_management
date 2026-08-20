<?php

namespace App\Controllers;

use App\Models\UserModel;
use App\Models\PenyewaModel;

class Penyewa extends BaseController
{
    protected UserModel $userModel;
    protected PenyewaModel $penyewaModel;

    public function __construct()
    {
        $this->userModel = new UserModel();
        $this->penyewaModel = new PenyewaModel();
    }

    public function index()
    {
        // Ambil semua data pengguna (bisa dikecualikan admin kalau mau, tapi ini ambil semua dulu)
        $pengguna = $this->userModel->orderBy('created_at', 'DESC')->findAll();

        foreach ($pengguna as &$user) {
            // Kasih nilai default strip (-) kalau dia calon penyewa
            $user['tanggal_masuk'] = '-'; 

            // Kalau statusnya penyewa, baru kita cari tanggal masuknya
            if ($user['status'] === 'penyewa') {
                // Cari di tabel penyewa yang namanya sama dengan nama_lengkap si user
                $dataPenyewa = $this->penyewaModel->where('nama', $user['nama_lengkap'])->first();
                
                if ($dataPenyewa) {
                    $user['tanggal_masuk'] = $dataPenyewa['tanggal_masuk'];
                }
            }
        }
        
        $data = [
            'title'    => 'Data Pengguna',
            'pengguna' => $pengguna,
        ];

        return view('penyewa/index', $data);
    }
}
