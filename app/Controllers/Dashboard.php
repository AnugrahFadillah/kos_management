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

        // Bikin array terjemahan bulan ke Bahasa Indonesia
        $bulanList = [
            1 => 'Januari', 2 => 'Februari', 3 => 'Maret', 4 => 'April',
            5 => 'Mei', 6 => 'Juni', 7 => 'Juli', 8 => 'Agustus',
            9 => 'September', 10 => 'Oktober', 11 => 'November', 12 => 'Desember'
        ];
        
        // Ambil nama bulan saat ini dalam Bahasa Indonesia (Agustus) dan Tahunnya
        $bulanIni = $bulanList[(int) date('n')];
        $tahunIni = date('Y');

        // 1. Hitung TOTAL UANG dari pembayaran yang SUDAH LUNAS bulan ini
        $pemasukan = $pembayaranModel
            ->selectSum('jumlah_bayar') 
            ->where('bulan', $bulanIni) // <-- Udah pakai bahasa Indonesia
            ->where('tahun', $tahunIni)
            ->where('status', 'lunas')
            ->first();
        $total_pemasukan = $pemasukan['jumlah_bayar'] ?? 0;

        // 2. Hitung JUMLAH TAGIHAN yang BELUM DIBAYAR bulan ini
        $tagihan_nunggak = $pembayaranModel
            ->where('bulan', $bulanIni) // <-- Udah pakai bahasa Indonesia
            ->where('tahun', $tahunIni)
            ->where('status !=', 'lunas')
            ->countAllResults();

        // Kumpulkan data ringkasan untuk ditampilkan di kartu-kartu dashboard
        $data = [
            'title'           => 'Dashboard',
            'total_kamar'     => $kamarModel->countAll(),
            'kamar_kosong'    => $kamarModel->where('status', 'kosong')->countAllResults(),
            'kamar_terisi'    => $kamarModel->where('status', 'terisi')->countAllResults(),
            'total_penyewa'   => $penyewaModel->where('status', 'aktif')->countAllResults(),
            'pengajuan_baru'  => $pengajuanModel->countBaru(),
            
            // Masukkan data keuangan ke view
            'total_pemasukan' => $total_pemasukan,
            'tagihan_nunggak' => $tagihan_nunggak,
        ];

        return view('dashboard/index', $data);
    }
}