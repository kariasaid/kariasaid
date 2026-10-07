<?php

namespace App\Models;

use CodeIgniter\Model;

class RabDetailModel extends Model
{
    protected $table            = 'rab_detail';
    protected $primaryKey       = 'id';
    protected $useAutoIncrement = true;
    protected $returnType       = 'array';
    protected $useSoftDeletes   = false;
    protected $protectFields    = true;
    protected $allowedFields    = [
        'rab_header_id',
        'no_urut',
        'kode_pekerjaan',
        'nama_pekerjaan',
        'satuan',
        'volume',
        'harga_satuan',
        'jumlah',
        'ahsp_id',
        'match_status',
    ];

    protected $useTimestamps = true;
    protected $createdField  = 'created_at';
    protected $updatedField  = ''; // tidak ada updated_at di tabel

    public function getByHeader(int $headerId)
    {
        return $this->select('rab_detail.*, ahsp_master.kode_ahsp, ahsp_master.nama_pekerjaan AS nama_ahsp')
                    ->join('ahsp_master', 'ahsp_master.id = rab_detail.ahsp_id', 'left')
                    ->where('rab_detail.rab_header_id', $headerId)
                    ->orderBy('rab_detail.no_urut', 'ASC')
                    ->findAll();
    }

    public function countMatched(int $headerId): int
    {
        return $this->where('rab_header_id', $headerId)
                    ->where('match_status', 'matched')
                    ->countAllResults();
    }

    public function countUnmatched(int $headerId): int
    {
        return $this->where('rab_header_id', $headerId)
                    ->where('match_status', 'unmatched')
                    ->countAllResults();
    }
}
