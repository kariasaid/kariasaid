<?php

namespace App\Models;

use CodeIgniter\Model;

class AhspRincianModel extends Model
{
    protected $table            = 'ahsp_rincian';
    protected $primaryKey       = 'id';
    protected $useAutoIncrement = true;
    protected $returnType       = 'array';
    protected $useSoftDeletes   = false;
    protected $protectFields    = true;
    protected $allowedFields    = [
        'ahsp_id',
        'sumber_daya_id',
        'koefisien',
    ];

    protected $useTimestamps = true;
    protected $createdField  = 'created_at';
    protected $updatedField  = 'updated_at';

    /**
     * Ambil rincian koefisien satu AHSP
     */
    public function getRincianByAhsp(int $ahspId): array
    {
        return $this->select('ahsp_rincian.*, 
                              sumber_daya.kode_sd, 
                              sumber_daya.nama_sd, 
                              sumber_daya.jenis, 
                              sumber_daya.satuan, 
                              sumber_daya.harga_satuan')
                    ->join('sumber_daya', 'sumber_daya.id = ahsp_rincian.sumber_daya_id')
                    ->where('ahsp_rincian.ahsp_id', $ahspId)
                    ->orderBy('sumber_daya.jenis', 'ASC')
                    ->orderBy('sumber_daya.nama_sd', 'ASC')
                    ->findAll();
    }

    /**
     * Ambil rincian untuk banyak AHSP sekaligus (efisien - 1 query)
     */
    public function getRincianByAhspIds(array $ahspIds): array
    {
        if (empty($ahspIds)) {
            return [];
        }

        return $this->select('ahsp_rincian.*, 
                              sumber_daya.kode_sd, 
                              sumber_daya.nama_sd, 
                              sumber_daya.jenis, 
                              sumber_daya.satuan, 
                              sumber_daya.harga_satuan')
                    ->join('sumber_daya', 'sumber_daya.id = ahsp_rincian.sumber_daya_id')
                    ->whereIn('ahsp_rincian.ahsp_id', $ahspIds)
                    ->findAll();
    }

    /**
     * Hapus semua rincian milik satu AHSP
     */
    public function deleteByAhsp(int $ahspId): bool
    {
        return $this->where('ahsp_id', $ahspId)->delete();
    }
}
