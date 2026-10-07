<?php

namespace App\Models;

use CodeIgniter\Model;

class AhspKategoriModel extends Model
{
    protected $table            = 'ahsp_kategori';
    protected $primaryKey       = 'id';
    protected $useAutoIncrement = true;
    protected $returnType       = 'array';
    protected $useSoftDeletes   = false;
    protected $protectFields    = true;
    protected $allowedFields    = [
        'kode_kategori',
        'nama_kategori',
        'deskripsi',
        'urutan',
        'is_active',
    ];

    protected $useTimestamps = true;
    protected $createdField  = 'created_at';
    protected $updatedField  = 'updated_at';

    protected $validationRules = [
        'kode_kategori' => 'required|max_length[20]',
        'nama_kategori' => 'required|max_length[100]',
    ];
}
