<?php

namespace App\Services;

use App\Models\AhspRincianModel;
use App\Models\RabDetailModel;
use App\Models\RabBreakdownModel;
use App\Models\RabHeaderModel;

/**
 * Service untuk perhitungan breakdown kebutuhan Bahan & Upah
 * Dirancang efisien untuk memproses RAB berukuran besar.
 */
class RabCalculationService
{
    protected AhspRincianModel $rincianModel;
    protected RabDetailModel $detailModel;
    protected RabBreakdownModel $breakdownModel;
    protected RabHeaderModel $headerModel;

    public function __construct()
    {
        $this->rincianModel   = new AhspRincianModel();
        $this->detailModel    = new RabDetailModel();
        $this->breakdownModel = new RabBreakdownModel();
        $this->headerModel    = new RabHeaderModel();
    }

    /**
     * Hitung breakdown untuk satu proyek RAB
     *
     * Algoritma:
     * 1. Ambil detail yang sudah matched
     * 2. Kumpulkan ahsp_id unik
     * 3. Ambil SEMUA rincian AHSP dalam 1 query
     * 4. Group rincian ke map untuk akses O(1)
     * 5. Hitung kebutuhan = volume × koefisien
     * 6. Insert batch dalam chunk agar hemat memory
     */
    public function calculate(int $rabHeaderId): array
    {
        // 1. Ambil semua detail yang sudah matched
        $details = $this->detailModel
            ->where('rab_header_id', $rabHeaderId)
            ->whereIn('match_status', ['matched', 'manual'])
            ->where('ahsp_id IS NOT NULL')
            ->findAll();

        if (empty($details)) {
            return [
                'success' => false,
                'message' => 'Tidak ada item yang berhasil di-matching dengan AHSP.',
                'total_rows' => 0,
            ];
        }

        // 2. Kumpulkan semua ahsp_id unik
        $ahspIds = array_unique(array_column($details, 'ahsp_id'));

        // 3. Ambil SEMUA rincian AHSP sekaligus (1 query saja)
        $allRincian = $this->rincianModel->getRincianByAhspIds($ahspIds);

        // 4. Group rincian berdasarkan ahsp_id → akses O(1)
        $rincianMap = [];
        foreach ($allRincian as $r) {
            $rincianMap[$r['ahsp_id']][] = $r;
        }

        // 5. Hapus breakdown lama (jika re-calculate)
        $this->breakdownModel->where('rab_header_id', $rabHeaderId)->delete();

        // 6. Proses perhitungan & siapkan batch data
        $batchData = [];
        $now = date('Y-m-d H:i:s');

        foreach ($details as $detail) {
            $ahspId = (int) $detail['ahsp_id'];
            $volume = (float) $detail['volume'];

            if (!isset($rincianMap[$ahspId]) || $volume <= 0) {
                continue;
            }

            foreach ($rincianMap[$ahspId] as $rincian) {
                $koefisien   = (float) $rincian['koefisien'];
                $kebutuhan   = $volume * $koefisien;
                $hargaSatuan = (float) $rincian['harga_satuan'];
                $totalHarga  = $kebutuhan * $hargaSatuan;

                $batchData[] = [
                    'rab_header_id'  => $rabHeaderId,
                    'rab_detail_id'  => $detail['id'],
                    'sumber_daya_id' => $rincian['sumber_daya_id'],
                    'jenis'          => $rincian['jenis'],
                    'volume_rab'     => $volume,
                    'koefisien'      => $koefisien,
                    'kebutuhan'      => round($kebutuhan, 6),
                    'harga_satuan'   => $hargaSatuan,
                    'total_harga'    => round($totalHarga, 2),
                    'created_at'     => $now,
                ];
            }
        }

        if (empty($batchData)) {
            return [
                'success' => false,
                'message' => 'Tidak ada rincian koefisien AHSP yang ditemukan untuk item yang matched.',
                'total_rows' => 0,
            ];
        }

        // 7. Insert dalam chunk (hindari memory overflow)
        $chunkSize = 500;
        $chunks = array_chunk($batchData, $chunkSize);

        foreach ($chunks as $chunk) {
            $this->breakdownModel->insertBatch($chunk);
        }

        // 8. Update status header
        $this->headerModel->update($rabHeaderId, [
            'status' => 'selesai',
        ]);

        return [
            'success'    => true,
            'total_rows' => count($batchData),
            'message'    => 'Kalkulasi berhasil. ' . number_format(count($batchData)) . ' baris breakdown dihasilkan.',
        ];
    }

    /**
     * Rekapitulasi Material / Upah / Alat (agregasi per sumber daya)
     */
    public function getRekapitulasi(int $rabHeaderId, string $jenis = 'material'): array
    {
        $db = \Config\Database::connect();

        return $db->table('rab_breakdown rb')
            ->select('sd.kode_sd, 
                      sd.nama_sd, 
                      sd.satuan,
                      SUM(rb.kebutuhan) AS total_volume,
                      rb.harga_satuan,
                      SUM(rb.total_harga) AS total_harga')
            ->join('sumber_daya sd', 'sd.id = rb.sumber_daya_id')
            ->where('rb.rab_header_id', $rabHeaderId)
            ->where('rb.jenis', $jenis)
            ->groupBy(['rb.sumber_daya_id', 'rb.harga_satuan', 'sd.kode_sd', 'sd.nama_sd', 'sd.satuan'])
            ->orderBy('sd.nama_sd', 'ASC')
            ->get()
            ->getResultArray();
    }

    /**
     * Total keseluruhan berdasarkan jenis
     */
    public function getTotalByJenis(int $rabHeaderId): array
    {
        $db = \Config\Database::connect();

        $rows = $db->table('rab_breakdown')
            ->select('jenis, SUM(total_harga) AS total')
            ->where('rab_header_id', $rabHeaderId)
            ->groupBy('jenis')
            ->get()
            ->getResultArray();

        $result = [
            'material' => 0,
            'upah'     => 0,
            'alat'     => 0,
        ];

        foreach ($rows as $row) {
            $result[$row['jenis']] = (float) $row['total'];
        }

        $result['grand_total'] = $result['material'] + $result['upah'] + $result['alat'];

        return $result;
    }
}
