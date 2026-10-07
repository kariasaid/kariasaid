<?php

namespace App\Models;

use CodeIgniter\Model;

class RabHeaderModel extends Model
{
    protected $table            = 'rab_header';
    protected $primaryKey       = 'id';
    protected $useAutoIncrement = true;
    protected $returnType       = 'array';
    protected $useSoftDeletes   = false;
    protected $protectFields    = true;
    protected $allowedFields    = [
        'kode_proyek',
        'nama_proyek',
        'lokasi',
        'pemilik',
        'file_asli',
        'total_nilai_rab',
        'status',
        'catatan',
        'created_by',
    ];

    protected $useTimestamps = true;
    protected $createdField  = 'created_at';
    protected $updatedField  = 'updated_at';

    public function getLatest(int $limit = 10)
    {
        return $this->orderBy('created_at', 'DESC')->findAll($limit);
    }

    public function countByStatus(string $status = '')
    {
        $builder = $this;
        if (!empty($status)) {
            $builder = $builder->where('status', $status);
        }
        return $builder->countAllResults();
    }
}
