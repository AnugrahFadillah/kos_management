<?php

namespace App\Controllers;

use App\Models\KamarModel;
use App\Models\PenyewaModel;
use App\Models\PembayaranModel;
use App\Models\PengajuanModel;

class Dashboard extends BaseController
{
    public function index()
    {
        $kamarModel      = new KamarModel();
        $penyewaModel    = new PenyewaModel();
        $pembayaranModel = new PembayaranModel();
        $pengajuanModel  = new PengajuanModel();

        // Kumpulkan data ringkasan untuk ditampilkan di kartu-kartu dashboard
        $data = [
            'title'          => 'Dashboard',
            'total_kamar'    => $kamarModel->countAll(),
            'kamar_kosong'   => $kamarModel->where('status', 'kosong')->countAllResults(),
            'kamar_terisi'   => $kamarModel->where('status', 'terisi')->countAllResults(),
            'total_penyewa'  => $penyewaModel->where('status', 'aktif')->countAllResults(),
            'pembayaran_bulan_ini' => $pembayaranModel
                ->where('bulan', date('F'))
                ->where('tahun', date('Y'))
                ->countAllResults(),
            'pengajuan_baru' => $pengajuanModel->countBaru(),
        ];

        return view('dashboard/index', $data);
    }
}
