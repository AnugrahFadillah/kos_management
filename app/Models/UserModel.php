<?php

namespace App\Models;

use CodeIgniter\Model;

class UserModel extends Model
{
    protected $table            = 'users';
    protected $primaryKey       = 'id';
    protected $useAutoIncrement = true;
    protected $returnType       = 'array';
    protected $useSoftDeletes   = false;

    // allow insert/update for these fields
    protected $allowedFields    = [
        'username',
        'password',
        'nama_lengkap',
        'email',
        'no_hp',
        'alamat',
        'status',
    ];

    protected $useTimestamps = true;
    protected $createdField  = 'created_at';
    protected $updatedField  = 'updated_at';

    // simple validation rules (controller-level validation still recommended)
    protected $validationRules = [
        'username'     => 'required|min_length[3]|max_length[50]|is_unique[users.username,id,{id}]',
        'nama_lengkap' => 'required|min_length[3]|max_length[100]',
        'email'        => 'permit_empty|valid_email|max_length[100]',
        'no_hp'        => 'permit_empty|min_length[6]|max_length[20]',
        'password'     => 'permit_empty|min_length[6]',
    ];
}
