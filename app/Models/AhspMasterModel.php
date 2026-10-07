<?php

namespace App\Models;

use CodeIgniter\Model;

class AhspMasterModel extends Model
{
    protected $table            = 'ahsp_master';
    protected $primaryKey       = 'id';
    protected $useAutoIncrement = true;
    protected $returnType       = 'array';
    protected $useSoftDeletes   = false;
    protected $protectFields    = true;
    protected $allowedFields    = [
        'kategori_id',
        'kode_ahsp',
        'nama_pekerjaan',
        'satuan',
        'keterangan',
        'is_active',
    ];

    protected $useTimestamps = true;
    protected $createdField  = 'created_at';
    protected $updatedField  = 'updated_at';

    protected $validationRules = [
        'kategori_id'    => 'required|integer',
        'kode_ahsp'      => 'required|max_length[30]',
        'nama_pekerjaan' => 'required|max_length[255]',
        'satuan'         => 'required|max_length[20]',
    ];

    /**
     * Ambil AHSP beserta nama kategori
     */
    public function getWithKategori(?int $id = null)
    {
        $builder = $this->select('ahsp_master.*, ahsp_kategori.nama_kategori, ahsp_kategori.kode_kategori')
                        ->join('ahsp_kategori', 'ahsp_kategori.id = ahsp_master.kategori_id')
                        ->where('ahsp_master.is_active', 1)
                        ->orderBy('ahsp_master.kode_ahsp', 'ASC');

        if ($id !== null) {
            return $builder->where('ahsp_master.id', $id)->first();
        }

        return $builder->findAll();
    }

    /**
     * Matching berdasarkan kode (prioritas) atau nama pekerjaan
     */
    public function findByKodeOrNama(string $kode = '', string $nama = '')
    {
        if (!empty($kode)) {
            $row = $this->where('kode_ahsp', $kode)
                        ->where('is_active', 1)
                        ->first();
            if ($row) {
                return $row;
            }
        }

        if (!empty($nama)) {
            // Exact match dulu
            $row = $this->where('nama_pekerjaan', $nama)
                        ->where('is_active', 1)
                        ->first();
            if ($row) {
                return $row;
            }

            // Like match (case insensitive via LOWER)
            return $this->where('is_active', 1)
                        ->like('nama_pekerjaan', $nama, 'both')
                        ->first();
        }

        return null;
    }
}
