<?php

namespace App\Controllers;

use App\Models\PengajuanModel;
use App\Models\KamarModel;
use App\Models\UserModel;

class Pengajuan extends BaseController
{
    protected PengajuanModel $pengajuanModel;
    protected KamarModel $kamarModel;

    public function __construct()
    {
        $this->pengajuanModel = new PengajuanModel();
        $this->kamarModel = new KamarModel();
    }

    // READ - Menampilkan semua pengajuan sewa yang masuk dari pengunjung
    public function index()
    {
        // coba ambil dengan join ke kamar terlebih dahulu
        $pengajuan = $this->pengajuanModel->getPengajuanWithKamar();

        // jika kosong, ambil plain records (tanpa join) untuk debugging/fallback
        if (empty($pengajuan)) {
            log_message('debug', 'Pengajuan::index - getPengajuanWithKamar returned empty; fetching plain findAll');
            $pengajuan = $this->pengajuanModel->orderBy('created_at', 'DESC')->findAll();
            log_message('debug', 'Pengajuan::index - plain pengajuan count: ' . count($pengajuan));
        } else {
            log_message('debug', 'Pengajuan::index - joined pengajuan count: ' . count($pengajuan));
        }

        $data = [
            'title'     => 'Pengajuan Sewa',
            'pengajuan' => $pengajuan,
        ];

        return view('pengajuan/index', $data);
    }

    // UPDATE - Admin mengubah status pengajuan (baru/diproses/diterima/ditolak)
    public function updateStatus($id)
    {
        $pengajuan = $this->pengajuanModel->find($id);

        if (! $pengajuan) {
            return redirect()->to('/pengajuan')->with('error', 'Data pengajuan tidak ditemukan.');
        }

        $status = $this->request->getPost('status');

        // 1. Update status di tabel pengajuan
        $this->pengajuanModel->update($id, ['status' => $status]);

        // 2. LOGIKA OTOMATIS JIKA STATUS DITERIMA
        if ($status === 'diterima' || $status === 'Diterima') {
            
            // A. Update status kamar jadi terisi
            $this->kamarModel->update($pengajuan['kamar_id'], ['status' => 'terisi']);

            // B. Masukkan data ke tabel Penyewa secara otomatis
            $penyewaModel = new \App\Models\PenyewaModel();
            
            // Cek dulu biar gak double insert kalau admin kepencet 2x
            $cekPenyewa = $penyewaModel->where('nama', $pengajuan['nama'])
                                       ->where('kamar_id', $pengajuan['kamar_id'])
                                       ->first();
                                       
            if (!$cekPenyewa) {
                $penyewaModel->insert([
                    'kamar_id'      => $pengajuan['kamar_id'],
                    'nama'          => $pengajuan['nama'],
                    'no_hp'         => $pengajuan['no_hp'],
                    'tanggal_masuk' => $pengajuan['tanggal_masuk'],
                    'status'        => 'aktif' // Default status di tabel penyewa
                ]);
            }

            // C. Ubah status user jadi "penyewa" (JIKA dia punya akun / user_id tidak kosong)
            if (!empty($pengajuan['user_id'])) {
                $userModel = new \App\Models\UserModel();
                $userModel->update($pengajuan['user_id'], ['status' => 'penyewa']);
            }
        }

        return redirect()->to('/pengajuan')->with('success', 'Status pengajuan berhasil diperbarui dan disinkronisasi.');
    }

    // DELETE - Menghapus data pengajuan
    public function delete($id)
    {
        $pengajuan = $this->pengajuanModel->find($id);

        if (! $pengajuan) {
            return redirect()->to('/pengajuan')->with('error', 'Data pengajuan tidak ditemukan.');
        }

        $this->pengajuanModel->delete($id);

        return redirect()->to('/pengajuan')->with('success', 'Data pengajuan berhasil dihapus.');
    }

    // AJUKAN SEWA - Pengunjung mengajukan sewa kamar
    public function ajukanSewa()
    {
        helper(['form', 'filesystem']);

        $userModel = new UserModel();

        // coba ambil user dari session: by id dulu, lalu by username jika id kosong
        $userId = session()->get('user_id');
        $user = null;
        if ($userId) {
            $user = $userModel->find($userId);
        } elseif (session()->get('username')) {
            $user = $userModel->where('username', session()->get('username'))->first();
        }
        if (is_object($user)) {
            $user = (array) $user;
        }

        // aturan validasi dasar
        $rules = [
            'kamar_id'       => 'required|integer',
            'tanggal_masuk'  => 'required|valid_date[Y-m-d]',
            'foto_identitas' => 'uploaded[foto_identitas]|max_size[foto_identitas,2048]|mime_in[foto_identitas,image/jpg,image/jpeg,image/png,application/pdf]',
        ];

        // Hanya minta nama/no_hp kalau pengunjung TIDAK login.
        if (! session()->get('logged_in')) {
            $rules['no_hp'] = 'required';
            $rules['nama']  = 'required';
        }

        if (! $this->validate($rules)) {
            // log validation errors sebelum redirect agar terlihat di writable/logs
            log_message('debug', 'Pengajuan validation errors: ' . print_r($this->validator->getErrors() ?? [], true));

            return redirect()->back()->withInput()->with('errors', $this->validator->getErrors());
        }

        // ambil data (prioritaskan data dari DB user bila tersedia; fallback ke session atau input)
        $kamarId = $this->request->getPost('kamar_id');
        $tanggalMasuk = $this->request->getPost('tanggal_masuk');

        $nama = $this->request->getPost('nama');
        $noHp = $this->request->getPost('no_hp');

        if ($user) {
            $nama = $user['nama_lengkap'] ?? session()->get('nama') ?? $nama;
            $noHp = $user['no_hp'] ?? session()->get('no_hp') ?? $noHp;
        }

        // proses file upload
        $file = $this->request->getFile('foto_identitas');
        $savedName = null;
        if ($file && $file->isValid() && ! $file->hasMoved()) {
            $targetDir = FCPATH . 'uploads/identitas';
            if (! is_dir($targetDir)) {
                mkdir($targetDir, 0755, true);
            }
            $savedName = $file->getRandomName();
            $file->move($targetDir, $savedName);
        }

        // simpan pengajuan — pastikan model mengizinkan field (allowedFields di PengajuanModel)
        $insertData = [
            'kamar_id'       => $kamarId,
            'user_id'        => $user['id'] ?? null,
            'nama'           => $nama,
            'no_hp'          => $noHp,
            'tanggal_masuk'  => $tanggalMasuk,
            'foto_identitas' => $savedName,
            'status'         => 'baru',
            'created_at'     => date('Y-m-d H:i:s'),
        ];

        $insertId = $this->pengajuanModel->insert($insertData);

        // log hasil insert untuk pengecekan
        if ($insertId === false) {
            log_message('error', 'Pengajuan::ajukanSewa - insert failed: ' . print_r($this->pengajuanModel->errors(), true));
            return redirect()->back()->withInput()->with('error', 'Gagal mengirim pengajuan, cek log.');
        }

        log_message('debug', 'Pengajuan::ajukanSewa - new pengajuan id: ' . $insertId . ' data: ' . print_r($insertData, true));

        // redirect non-admin ke homepage
        $isAdmin = session()->get('is_admin') ? true : false;
        if ($isAdmin) {
            return redirect()->to('/pengajuan')->with('success', 'Pengajuan sewa berhasil dikirim.');
        }

        return redirect()->to('/')->with('success', 'Pengajuan sewa berhasil dikirim.');
    }
}
