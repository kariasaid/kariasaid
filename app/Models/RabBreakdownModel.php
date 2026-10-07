<?php

namespace App\Models;

use CodeIgniter\Model;

class RabBreakdownModel extends Model
{
    protected $table            = 'rab_breakdown';
    protected $primaryKey       = 'id';
    protected $useAutoIncrement = true;
    protected $returnType       = 'array';
    protected $useSoftDeletes   = false;
    protected $protectFields    = true;
    protected $allowedFields    = [
        'rab_header_id',
        'rab_detail_id',
        'sumber_daya_id',
        'jenis',
        'volume_rab',
        'koefisien',
        'kebutuhan',
        'harga_satuan',
        'total_harga',
    ];

    protected $useTimestamps = true;
    protected $createdField  = 'created_at';
    protected $updatedField  = '';
}
