<?php

namespace App\Models;

use CodeIgniter\Model;

class PenyewaModel extends Model
{
    protected $table            = 'penyewa';
    protected $primaryKey       = 'id';
    protected $useAutoIncrement = true;
    protected $returnType       = 'array';
    protected $useSoftDeletes   = false;

    protected $allowedFields = [
        'kamar_id',
        'nama',
        'no_hp',
        'email',
        'alamat_asal',
        'tanggal_masuk',
        'status',
    ];

    protected $useTimestamps = true;
    protected $createdField  = 'created_at';
    protected $updatedField  = 'updated_at';

    protected $validationRules = [
        'kamar_id'      => 'required|numeric',
        'nama'          => 'required|min_length[3]|max_length[100]',
        'no_hp'         => 'required|max_length[20]',
        'email'         => 'permit_empty|valid_email',
        'tanggal_masuk' => 'required|valid_date',
    ];

    /**
     * Join ke tabel kamar supaya nomor kamar & tipe kamar ikut tampil
     * tanpa perlu query terpisah di controller/view (hindari N+1 query).
     */
    public function getPenyewaWithKamar($id = null)
    {
        $builder = $this->select('penyewa.*, kamar.nomor_kamar, kamar.tipe_kamar, kamar.harga_bulanan')
                         ->join('kamar', 'kamar.id = penyewa.kamar_id');

        if ($id !== null) {
            return $builder->where('penyewa.id', $id)->first();
        }

        return $builder->orderBy('penyewa.created_at', 'DESC')->findAll();
    }
}
