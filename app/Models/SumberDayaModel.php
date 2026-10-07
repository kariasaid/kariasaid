<?php

namespace App\Models;

use CodeIgniter\Model;

class SumberDayaModel extends Model
{
    protected $table            = 'sumber_daya';
    protected $primaryKey       = 'id';
    protected $useAutoIncrement = true;
    protected $returnType       = 'array';
    protected $useSoftDeletes   = false;
    protected $protectFields    = true;
    protected $allowedFields    = [
        'kode_sd',
        'nama_sd',
        'jenis',
        'satuan',
        'harga_satuan',
        'keterangan',
        'is_active',
    ];

    protected $useTimestamps = true;
    protected $createdField  = 'created_at';
    protected $updatedField  = 'updated_at';

    protected $validationRules = [
        'kode_sd'      => 'required|max_length[30]',
        'nama_sd'      => 'required|max_length[150]',
        'jenis'        => 'required|in_list[material,upah,alat]',
        'satuan'       => 'required|max_length[20]',
        'harga_satuan' => 'required|decimal',
    ];

    public function getByJenis(string $jenis = '')
    {
        $builder = $this->where('is_active', 1)->orderBy('nama_sd', 'ASC');

        if (!empty($jenis)) {
            $builder->where('jenis', $jenis);
        }

        return $builder->findAll();
    }
}
