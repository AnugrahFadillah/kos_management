<?php

namespace App\Models;

use CodeIgniter\Model;

class PengajuanModel extends Model
{
    protected $table            = 'pengajuan';
    protected $primaryKey       = 'id';
    protected $useAutoIncrement = true;
    protected $returnType       = 'array';
    protected $useSoftDeletes   = false;

    // allow insert/update for these fields
    protected $allowedFields = [
        'kamar_id',
        'user_id',
        'nama',
        'no_hp',
        'email',
        'foto_identitas',
        'tanggal_masuk',
        'status',
        'created_at',
        'updated_at',
    ];

    protected $useTimestamps = true;
    protected $createdField  = 'created_at';
    protected $updatedField  = 'updated_at';

    // Validation rules (controller masih menangani aturan khusus login/guest)
    protected $validationRules = [
        'kamar_id'      => 'required|numeric',
        'nama'          => 'permit_empty|min_length[3]|max_length[100]',
        'no_hp'         => 'permit_empty|max_length[20]',
        'email'         => 'permit_empty|valid_email',
        'tanggal_masuk' => 'required|valid_date[Y-m-d]',
    ];

    /**
     * Join ke kamar supaya admin bisa lihat kamar mana yang diminati
     * tanpa query terpisah untuk tiap baris.
     */
    public function getPengajuanWithKamar($id = null)
    {
        $builder = $this->select('pengajuan.*, kamar.nomor_kamar, kamar.tipe_kamar')
                         ->join('kamar', 'kamar.id = pengajuan.kamar_id');

        if ($id !== null) {
            return $builder->where('pengajuan.id', $id)->first();
        }

        return $builder->orderBy('pengajuan.created_at', 'DESC')->findAll();
    }

    // Hitung pengajuan yang statusnya masih "baru" (belum ditindaklanjuti admin)
    public function countBaru()
    {
        return $this->where('status', 'baru')->countAllResults();
    }

    // Ambil riwayat pengajuan berdasarkan user_id
    public function getPengajuanByUser($userId)
    {
        return $this->select('pengajuan.*, kamar.nomor_kamar, kamar.tipe_kamar, kamar.harga_bulanan')
                    ->join('kamar', 'kamar.id = pengajuan.kamar_id')
                    ->where('pengajuan.user_id', $userId)
                    ->orderBy('pengajuan.created_at', 'DESC')
                    ->findAll();
    }
}
