<?php

namespace App\Models;

use CodeIgniter\Model;

class KamarModel extends Model
{
    protected $table            = 'kamar';
    protected $primaryKey       = 'id';
    protected $useAutoIncrement = true;
    protected $returnType       = 'array';
    protected $useSoftDeletes   = false;

    // Kolom yang boleh diisi lewat mass-assignment (insert/update)
    protected $allowedFields = [
        'nomor_kamar',
        'tipe_kamar',
        'harga_bulanan',
        'fasilitas',
        'status',
        'foto_kamar',
    ];

    protected $useTimestamps = true;
    protected $createdField  = 'created_at';
    protected $updatedField  = 'updated_at';

    // Validasi otomatis dijalankan oleh insert()/update() bawaan CI4
    protected $validationRules = [
        'nomor_kamar'   => 'required|max_length[10]|is_unique[kamar.nomor_kamar,id,{id}]',
        'tipe_kamar'    => 'required|in_list[Standar,VIP,Deluxe]',
        'harga_bulanan' => 'required|numeric',
        'status'        => 'permit_empty|in_list[kosong,terisi]',
    ];

    protected $validationMessages = [
        'nomor_kamar' => [
            'is_unique' => 'Nomor kamar sudah digunakan, silakan pilih nomor lain.',
        ],
    ];

    /**
     * Ambil semua kamar yang berstatus kosong (dipakai di form tambah penyewa)
     */
    public function getKamarKosong()
    {
        return $this->where('status', 'kosong')->findAll();
    }
}
