<?php

namespace App\Controllers;

use App\Models\PembayaranModel;
use App\Models\PenyewaModel;

class Pembayaran extends BaseController
{
    protected PembayaranModel $pembayaranModel;
    protected PenyewaModel $penyewaModel;

    // Daftar nama bulan dalam Bahasa Indonesia, urut Januari - Desember.
    // Dipakai berulang kali untuk menghitung "bulan berikutnya yang belum dibayar".
    private array $bulanList = [
        'Januari', 'Februari', 'Maret', 'April', 'Mei', 'Juni',
        'Juli', 'Agustus', 'September', 'Oktober', 'November', 'Desember',
    ];

    public function __construct()
    {
        $this->pembayaranModel = new PembayaranModel();
        $this->penyewaModel    = new PenyewaModel();
    }

    public function index()
    {
        $data = [
            'title'      => 'Data Pembayaran',
            'pembayaran' => $this->pembayaranModel->getPembayaranWithPenyewa(),
        ];

        return view('pembayaran/index', $data);
    }

    public function create()
    {
        $data = [
            'title'        => 'Tambah Pembayaran',
            'penyewa'      => $this->penyewaModel->getPenyewaWithKamar(), // sudah include nomor_kamar, tipe_kamar, harga_bulanan
            // Data ini dikirim ke JavaScript di view untuk auto-fill form saat penyewa dipilih
            'infoPenyewa'  => $this->buildInfoPenyewaUntukJs(),
        ];

        return view('pembayaran/create', $data);
    }

    public function store()
    {
        // Bukti bayar boleh berupa gambar (foto struk/screenshot transfer) atau PDF
        $buktiRules = [
            'bukti_bayar' => 'permit_empty|max_size[bukti_bayar,2048]|mime_in[bukti_bayar,image/jpg,image/jpeg,image/png,image/webp,application/pdf]',
        ];

        if (! $this->validate($buktiRules)) {
            return redirect()->back()->withInput()->with('errors', $this->validator->getErrors());
        }

        $penyewaId = $this->request->getPost('penyewa_id');
        $jumlahBayar = $this->request->getPost('jumlah_bayar');

        // AMBIL HARGA KAMAR BERDASARKAN PENYEWA YANG DIPILIH
        $hargaKamar = 0;
        $penyewaList = $this->penyewaModel->getPenyewaWithKamar();
        foreach ($penyewaList as $p) {
            if ($p['id'] == $penyewaId) {
                $hargaKamar = $p['harga_bulanan'];
                break;
            }
        }

        // LOGIKA PENENTUAN STATUS OTOMATIS
        if ($jumlahBayar <= 0) {
            $status = 'belum_bayar';
        } elseif ($jumlahBayar < $hargaKamar) {
            $status = 'belum_lunas';
        } else {
            $status = 'lunas';
        }

        $data = [
            'penyewa_id'    => $penyewaId,
            'bulan'         => $this->request->getPost('bulan'),
            'tahun'         => $this->request->getPost('tahun'),
            'jumlah_bayar'  => $jumlahBayar,
            'tanggal_bayar' => $this->request->getPost('tanggal_bayar'),
            'status'        => $status, // Memasukkan hasil hitungan status ke database
            'catatan'       => $this->request->getPost('catatan'),
        ];

        // Cegah input pembayaran dobel: penyewa yang sama + periode (bulan & tahun) yang sama
        if ($this->pembayaranModel->sudahBayarPeriodeIni($data['penyewa_id'], $data['bulan'], $data['tahun'])) {
            return redirect()->back()->withInput()->with(
                'error',
                "Pembayaran untuk penyewa ini pada periode {$data['bulan']} {$data['tahun']} sudah pernah diinput sebelumnya."
            );
        }

        $namaBukti = $this->uploadBuktiBayar();
        if ($namaBukti) {
            $data['bukti_bayar'] = $namaBukti;
        }

        if (! $this->pembayaranModel->insert($data)) {
            return redirect()->back()->withInput()->with('errors', $this->pembayaranModel->errors());
        }

        return redirect()->to('/pembayaran')->with('success', 'Data pembayaran berhasil ditambahkan.');
    }

    public function edit($id)
    {
        $pembayaran = $this->pembayaranModel->find($id);

        if (! $pembayaran) {
            return redirect()->to('/pembayaran')->with('error', 'Data pembayaran tidak ditemukan.');
        }

        $data = [
            'title'       => 'Edit Pembayaran',
            'pembayaran'  => $pembayaran,
            'penyewa'     => $this->penyewaModel->getPenyewaWithKamar(),
            'infoPenyewa' => $this->buildInfoPenyewaUntukJs(),
        ];

        return view('pembayaran/edit', $data);
    }

    public function update($id)
    {
        $pembayaran = $this->pembayaranModel->find($id);

        if (! $pembayaran) {
            return redirect()->to('/pembayaran')->with('error', 'Data pembayaran tidak ditemukan.');
        }

        $buktiRules = [
            'bukti_bayar' => 'permit_empty|max_size[bukti_bayar,2048]|mime_in[bukti_bayar,image/jpg,image/jpeg,image/png,image/webp,application/pdf]',
        ];

        if (! $this->validate($buktiRules)) {
            return redirect()->back()->withInput()->with('errors', $this->validator->getErrors());
        }

        $penyewaId = $this->request->getPost('penyewa_id');
        $jumlahBayar = $this->request->getPost('jumlah_bayar');

        // AMBIL HARGA KAMAR BERDASARKAN PENYEWA YANG DIPILIH
        $hargaKamar = 0;
        $penyewaList = $this->penyewaModel->getPenyewaWithKamar();
        foreach ($penyewaList as $p) {
            if ($p['id'] == $penyewaId) {
                $hargaKamar = $p['harga_bulanan'];
                break;
            }
        }

        // LOGIKA PENENTUAN STATUS OTOMATIS
        if ($jumlahBayar <= 0) {
            $status = 'belum_bayar';
        } elseif ($jumlahBayar < $hargaKamar) {
            $status = 'belum_lunas';
        } else {
            $status = 'lunas';
        }

        $data = [
            'penyewa_id'    => $penyewaId,
            'bulan'         => $this->request->getPost('bulan'),
            'tahun'         => $this->request->getPost('tahun'),
            'jumlah_bayar'  => $jumlahBayar,
            'tanggal_bayar' => $this->request->getPost('tanggal_bayar'),
            'status'        => $status, // Memasukkan hasil hitungan status ke database
            'catatan'       => $this->request->getPost('catatan'),
        ];

        // Cegah dobel, tapi kecualikan record ini sendiri (id yang sedang diedit)
        if ($this->pembayaranModel->sudahBayarPeriodeIni($data['penyewa_id'], $data['bulan'], $data['tahun'], $id)) {
            return redirect()->back()->withInput()->with(
                'error',
                "Pembayaran untuk penyewa ini pada periode {$data['bulan']} {$data['tahun']} sudah pernah diinput sebelumnya."
            );
        }

        $namaBukti = $this->uploadBuktiBayar();
        if ($namaBukti) {
            $this->hapusFileLama('pembayaran', $pembayaran['bukti_bayar']);
            $data['bukti_bayar'] = $namaBukti;
        }

        if (! $this->pembayaranModel->update($id, $data)) {
            return redirect()->back()->withInput()->with('errors', $this->pembayaranModel->errors());
        }

        return redirect()->to('/pembayaran')->with('success', 'Data pembayaran berhasil diperbarui.');
    }

    public function delete($id)
    {
        $pembayaran = $this->pembayaranModel->find($id);

        if (! $pembayaran) {
            return redirect()->to('/pembayaran')->with('error', 'Data pembayaran tidak ditemukan.');
        }

        $this->hapusFileLama('pembayaran', $pembayaran['bukti_bayar']);

        $this->pembayaranModel->delete($id);

        return redirect()->to('/pembayaran')->with('success', 'Data pembayaran berhasil dihapus.');
    }

    private function uploadBuktiBayar(): ?string
    {
        $file = $this->request->getFile('bukti_bayar');

        if (! $file || ! $file->isValid() || $file->hasMoved()) {
            return null;
        }

        $namaBaru = $file->getRandomName();
        $file->move(FCPATH . 'uploads/pembayaran', $namaBaru);

        return $namaBaru;
    }

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
    private function buildInfoPenyewaUntukJs(): array
    {
        $penyewaList = $this->penyewaModel->getPenyewaWithKamar();
        $info        = [];

        foreach ($penyewaList as $p) {
            $periodeSudahBayar = $this->pembayaranModel->getPeriodeSudahBayar($p['id']);
            $berikutnya        = $this->hitungPeriodeBelumBayar($p['tanggal_masuk'], $periodeSudahBayar);

            $info[$p['id']] = [
                'kamar'         => $p['nomor_kamar'] . ' - ' . $p['tipe_kamar'],
                'harga'         => $p['harga_bulanan'],
                // TAMBAHKAN BARIS INI (Format tanggal: 31-08-2026)
                'tanggal_masuk' => date('d-m-Y', strtotime($p['tanggal_masuk'])), 
                'bulan'         => $berikutnya['bulan'],
                'tahun'         => $berikutnya['tahun'],
            ];
        }

        return $info;
    }

    private function hitungPeriodeBelumBayar(string $tanggalMasuk, array $periodeSudahBayar): array
    {
        $pointer = new \DateTime(date('Y-m-01', strtotime($tanggalMasuk)));
        $now     = new \DateTime(date('Y-m-01'));

        while ($pointer <= $now) {
            $bulanNama = $this->bulanList[(int) $pointer->format('n') - 1];
            $tahun     = (int) $pointer->format('Y');

            if (! in_array($bulanNama . '-' . $tahun, $periodeSudahBayar, true)) {
                return ['bulan' => $bulanNama, 'tahun' => $tahun];
            }

            $pointer->modify('+1 month');
        }

        // Semua periode sampai bulan berjalan sudah lunas -> sarankan bulan berikutnya
        $bulanNama = $this->bulanList[(int) $pointer->format('n') - 1];
        $tahun     = (int) $pointer->format('Y');

        return ['bulan' => $bulanNama, 'tahun' => $tahun];
    }
}