<?php

namespace App\Controllers;

use App\Models\KamarModel;

class Kamar extends BaseController
{
    protected KamarModel $kamarModel;

    public function __construct()
    {
        $this->kamarModel = new KamarModel();
    }

    // READ - Menampilkan semua data kamar
    public function index()
    {
        $data = [
            'title' => 'Data Kamar',
            'kamar' => $this->kamarModel->orderBy('nomor_kamar', 'ASC')->findAll(),
        ];

        return view('kamar/index', $data);
    }

    // CREATE - Menampilkan form tambah kamar
    public function create()
    {
        $data = ['title' => 'Tambah Kamar'];
        return view('kamar/create', $data);
    }

    // CREATE - Menyimpan data kamar baru ke database
    public function store()
    {
        // Validasi file foto secara terpisah dari validasi field teks,
        // supaya pesan errornya bisa spesifik ("harus gambar", "maks 2MB", dst).
        $fotoRules = [
            'foto_kamar' => 'permit_empty|max_size[foto_kamar,2048]|is_image[foto_kamar]|mime_in[foto_kamar,image/jpg,image/jpeg,image/png,image/webp]',
        ];

        if (! $this->validate($fotoRules)) {
            return redirect()->back()->withInput()->with('errors', $this->validator->getErrors());
        }

        $data = [
            'nomor_kamar'   => $this->request->getPost('nomor_kamar'),
            'tipe_kamar'    => $this->request->getPost('tipe_kamar'),
            'harga_bulanan' => $this->request->getPost('harga_bulanan'),
            'fasilitas'     => $this->request->getPost('fasilitas'),
            'status'        => $this->request->getPost('status') ?? 'kosong',
        ];

        // Upload foto (opsional) - kalau pengguna tidak pilih file, lewati saja
        $namaFoto = $this->uploadFotoKamar();
        if ($namaFoto) {
            $data['foto_kamar'] = $namaFoto;
        }

        // insert() otomatis menjalankan $validationRules yang ada di KamarModel
        if (! $this->kamarModel->insert($data)) {
            return redirect()->back()->withInput()->with('errors', $this->kamarModel->errors());
        }

        return redirect()->to('/kamar')->with('success', 'Data kamar berhasil ditambahkan.');
    }

    // UPDATE - Menampilkan form edit dengan data lama
    public function edit($id)
    {
        $kamar = $this->kamarModel->find($id);

        if (! $kamar) {
            return redirect()->to('/kamar')->with('error', 'Data kamar tidak ditemukan.');
        }

        $data = [
            'title' => 'Edit Kamar',
            'kamar' => $kamar,
        ];

        return view('kamar/edit', $data);
    }

    // UPDATE - Menyimpan perubahan data kamar
    public function update($id)
    {
        $kamar = $this->kamarModel->find($id);

        if (! $kamar) {
            return redirect()->to('/kamar')->with('error', 'Data kamar tidak ditemukan.');
        }

        $fotoRules = [
            'foto_kamar' => 'permit_empty|max_size[foto_kamar,2048]|is_image[foto_kamar]|mime_in[foto_kamar,image/jpg,image/jpeg,image/png,image/webp]',
        ];

        if (! $this->validate($fotoRules)) {
            return redirect()->back()->withInput()->with('errors', $this->validator->getErrors());
        }

        $data = [
            'nomor_kamar'   => $this->request->getPost('nomor_kamar'),
            'tipe_kamar'    => $this->request->getPost('tipe_kamar'),
            'harga_bulanan' => $this->request->getPost('harga_bulanan'),
            'fasilitas'     => $this->request->getPost('fasilitas'),
            'status'        => $this->request->getPost('status'),
        ];

        // Jika admin upload foto baru, ganti foto lama (dan hapus file lama dari disk)
        $namaFoto = $this->uploadFotoKamar();
        if ($namaFoto) {
            $this->hapusFileLama('kamar', $kamar['foto_kamar']);
            $data['foto_kamar'] = $namaFoto;
        }

        // Timpa rule 'nomor_kamar' khusus untuk proses update: kecualikan baris
        // kamar itu sendiri (berdasarkan $id) dari pengecekan is_unique, supaya
        // kamar yang nomornya TIDAK diubah tidak dianggap "sudah digunakan".
        $this->kamarModel->setValidationRule(
            'nomor_kamar',
            "required|max_length[10]|is_unique[kamar.nomor_kamar,id,{$id}]"
        );

        if (! $this->kamarModel->update($id, $data)) {
            return redirect()->back()->withInput()->with('errors', $this->kamarModel->errors());
        }

        return redirect()->to('/kamar')->with('success', 'Data kamar berhasil diperbarui.');
    }

    // DELETE - Menghapus data kamar
    public function delete($id)
    {
        $kamar = $this->kamarModel->find($id);

        if (! $kamar) {
            return redirect()->to('/kamar')->with('error', 'Data kamar tidak ditemukan.');
        }

        // Hapus juga file foto dari disk supaya tidak jadi sampah
        $this->hapusFileLama('kamar', $kamar['foto_kamar']);

        $this->kamarModel->delete($id);

        return redirect()->to('/kamar')->with('success', 'Data kamar berhasil dihapus.');
    }

    /**
     * Proses upload file dari field 'foto_kamar'.
     * Return nama file baru (yang sudah di-generate acak) atau null kalau tidak ada file diupload.
     */
    private function uploadFotoKamar(): ?string
    {
        $file = $this->request->getFile('foto_kamar');

        if (! $file || ! $file->isValid() || $file->hasMoved()) {
            return null;
        }

        // Nama file di-random agar tidak bentrok & tidak bisa ditebak orang lain
        $namaBaru = $file->getRandomName();
        $file->move(FCPATH . 'uploads/kamar', $namaBaru);

        return $namaBaru;
    }

    /**
     * Hapus file lama dari folder public/uploads/{folder}/ kalau ada.
     */
    private function hapusFileLama(string $folder, ?string $namaFile): void
    {
        if (empty($namaFile)) {
            return;
        }

        $path = FCPATH . 'uploads/' . $folder . '/' . $namaFile;
        if (is_file($path)) {
            unlink($path);
        }
    }
}
