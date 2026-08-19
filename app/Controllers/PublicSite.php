<?php

namespace App\Controllers;

use App\Models\KamarModel;
use App\Models\PengajuanModel;
use App\Models\UserModel;

/**
 * Controller ini melayani halaman yang bisa diakses SIAPA SAJA tanpa login,
 * yaitu halaman depan (landing page) untuk calon penyewa/pengunjung.
 * Data kamar yang ditampilkan di sini diambil dari tabel yang SAMA dengan
 * yang dikelola admin (kamar), jadi otomatis sinkron.
 */
class PublicSite extends BaseController
{
    protected KamarModel $kamarModel;
    protected PengajuanModel $pengajuanModel;

    public function __construct()
    {
        $this->kamarModel     = new KamarModel();
        $this->pengajuanModel = new PengajuanModel();
    }

    // Halaman utama: profil kos + daftar kamar yang berstatus "kosong"
    public function index()
    {
        $data = [
            'title'        => 'Selamat Datang',
            'kamarTersedia'=> $this->kamarModel->where('status', 'kosong')->findAll(),
        ];

        return view('public/index', $data);
    }

    // Menyimpan form pengajuan sewa yang diisi pengunjung
    public function ajukanSewa()
    {
        ($this->request->getPost()); // Debug: tampilkan data POST yang diterima
        $rules = [
            'kamar_id'       => 'required|numeric',
            'foto_identitas' => 'permit_empty|max_size[foto_identitas,2048]|is_image[foto_identitas]|mime_in[foto_identitas,image/jpg,image/jpeg,image/png,image/webp]',
        ];

        if (! $this->validate($rules)) {
            return redirect()->back()->withInput()->with('errors', $this->validator->getErrors());
        }

        $data = [
            'kamar_id' => $this->request->getPost('kamar_id'),
            'status'   => 'baru',
        ];

        // Foto identitas (KTP/SIM) bersifat opsional saat pengajuan awal,
        // supaya tidak menghalangi pengunjung yang belum siap upload dari mengirim minat sewa.
        $file = $this->request->getFile('foto_identitas');
        if ($file && $file->isValid() && ! $file->hasMoved()) {
            $namaBaru = $file->getRandomName();
            $file->move(FCPATH . 'uploads/identitas', $namaBaru);
            $data['foto_identitas'] = $namaBaru;
        }

        $this->pengajuanModel->insert($data);

        return redirect()->to('/')->with('success', 'Terima kasih! Pengajuan sewa Anda sudah kami terima, tim kami akan segera menghubungi Anda.');
    }
}
