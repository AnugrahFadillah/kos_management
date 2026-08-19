<?php

namespace App\Models;

use CodeIgniter\Model;

class PembayaranModel extends Model
{
    protected $table            = 'pembayaran';
    protected $primaryKey       = 'id';
    protected $useAutoIncrement = true;
    protected $returnType       = 'array';
    protected $useSoftDeletes   = false;

    protected $allowedFields = [
        'penyewa_id',
        'bulan',
        'tahun',
        'jumlah_bayar',
        'tanggal_bayar',
        'bukti_bayar',
        'status',
        'catatan',
    ];

    protected $useTimestamps = true;
    protected $createdField  = 'created_at';
    protected $updatedField  = 'updated_at';

    protected $validationRules = [
        'penyewa_id'    => 'required|numeric',
        'bulan'         => 'required',
        'tahun'         => 'required|numeric',
        'jumlah_bayar'  => 'required|numeric',
        'tanggal_bayar' => 'required|valid_date',
    ];

    /**
     * Join ke penyewa supaya nama penyewa ikut tampil di daftar pembayaran.
     */
    public function getPembayaranWithPenyewa($id = null)
    {
        $builder = $this->select('pembayaran.*, penyewa.nama as nama_penyewa, penyewa.tanggal_masuk, kamar.nomor_kamar')
                         ->join('penyewa', 'penyewa.id = pembayaran.penyewa_id')
                         ->join('kamar', 'kamar.id = penyewa.kamar_id');

        if ($id !== null) {
            return $builder->where('pembayaran.id', $id)->first();
        }

        return $builder->orderBy('pembayaran.tanggal_bayar', 'DESC')->findAll();
    }

    /**
     * Total pemasukan bulan & tahun tertentu, dipakai di dashboard.
     */
    public function getTotalPemasukan($bulan, $tahun)
    {
        return $this->where('bulan', $bulan)
                    ->where('tahun', $tahun)
                    ->where('status', 'lunas')
                    ->selectSum('jumlah_bayar')
                    ->first();
    }

    /**
     * Cek apakah penyewa tertentu SUDAH punya data pembayaran untuk bulan+tahun tertentu.
     * Dipakai untuk mencegah admin input pembayaran dobel di periode yang sama.
     * $excludeId dipakai saat proses EDIT, supaya record yang sedang diedit tidak
     * dianggap "bentrok" dengan dirinya sendiri.
     */
    public function sudahBayarPeriodeIni($penyewaId, $bulan, $tahun, $excludeId = null): bool
    {
        $builder = $this->where('penyewa_id', $penyewaId)
                         ->where('bulan', $bulan)
                         ->where('tahun', $tahun);

        if ($excludeId !== null) {
            $builder->where('id !=', $excludeId);
        }

        return $builder->countAllResults() > 0;
    }

    /**
     * Ambil daftar "Bulan-Tahun" yang SUDAH ada data pembayarannya untuk satu penyewa.
     * Formatnya array string seperti ["Januari-2026", "Februari-2026"].
     */
    public function getPeriodeSudahBayar($penyewaId): array
    {
        $rows = $this->select('bulan, tahun')->where('penyewa_id', $penyewaId)->findAll();

        return array_map(static fn ($r) => $r['bulan'] . '-' . $r['tahun'], $rows);
    }
}
