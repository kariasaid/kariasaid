<?php

namespace App\Controllers;

use App\Models\RabHeaderModel;
use App\Models\RabDetailModel;
use App\Models\AhspMasterModel;
use App\Services\RabCalculationService;
use PhpOffice\PhpSpreadsheet\IOFactory;

class ImportRABController extends BaseController
{
    protected RabHeaderModel $headerModel;
    protected RabDetailModel $detailModel;
    protected AhspMasterModel $ahspModel;
    protected RabCalculationService $calcService;

    public function __construct()
    {
        $this->headerModel = new RabHeaderModel();
        $this->detailModel = new RabDetailModel();
        $this->ahspModel   = new AhspMasterModel();
        $this->calcService = new RabCalculationService();
    }

    public function index()
    {
        $data = [
            'title'  => 'Upload & Import RAB',
            'projek' => $this->headerModel->orderBy('created_at', 'DESC')->findAll(),
        ];

        return view('rab/upload', $data);
    }

    public function upload()
    {
        $file = $this->request->getFile('file_rab');

        if (!$file || !$file->isValid() || $file->hasMoved()) {
            return redirect()->back()->with('error', 'File tidak valid atau gagal diunggah.');
        }

        $ext = strtolower($file->getClientExtension());
        if (!in_array($ext, ['xlsx', 'xls', 'csv'])) {
            return redirect()->back()->with('error', 'Hanya file Excel (.xlsx, .xls) atau CSV yang diperbolehkan.');
        }

        // Pastikan folder upload ada
        $uploadPath = WRITEPATH . 'uploads/rab/';
        if (!is_dir($uploadPath)) {
            mkdir($uploadPath, 0775, true);
        }

        $newName = $file->getRandomName();
        $file->move($uploadPath, $newName);
        $fullPath = $uploadPath . $newName;

        // Buat header proyek
        $headerId = $this->headerModel->insert([
            'kode_proyek' => 'RAB-' . date('YmdHis'),
            'nama_proyek' => $this->request->getPost('nama_proyek') ?: 'Proyek ' . date('d-m-Y H:i'),
            'lokasi'      => $this->request->getPost('lokasi'),
            'pemilik'     => $this->request->getPost('pemilik'),
            'file_asli'   => $file->getClientName(),
            'status'      => 'proses',
        ]);

        try {
            $spreadsheet = IOFactory::load($fullPath);
            $sheet = $spreadsheet->getActiveSheet();
            $rows  = $sheet->toArray(null, true, true, true);

            /*
             * Asumsi struktur kolom RAB:
             * A = No
             * B = Kode Pekerjaan
             * C = Uraian / Nama Pekerjaan
             * D = Satuan
             * E = Volume
             * F = Harga Satuan (opsional)
             *
             * Baris 1 = header, data mulai baris 2
             */
            $batchDetail = [];
            $startRow = 2;
            $totalNilai = 0;

            foreach ($rows as $i => $row) {
                if ($i < $startRow) {
                    continue;
                }

                $kode   = trim((string) ($row['B'] ?? ''));
                $nama   = trim((string) ($row['C'] ?? ''));
                $satuan = trim((string) ($row['D'] ?? ''));

                // Bersihkan format angka Indonesia (1.234,56 → 1234.56)
                $volumeRaw = (string) ($row['E'] ?? '0');
                $volumeRaw = str_replace(['.', ' '], '', $volumeRaw);
                $volumeRaw = str_replace(',', '.', $volumeRaw);
                $volume    = (float) $volumeRaw;

                $hargaRaw = (string) ($row['F'] ?? '0');
                $hargaRaw = str_replace(['.', ' '], '', $hargaRaw);
                $hargaRaw = str_replace(',', '.', $hargaRaw);
                $harga    = (float) $hargaRaw;

                if (empty($nama) || $volume <= 0) {
                    continue;
                }

                // Matching AHSP
                $ahsp = $this->ahspModel->findByKodeOrNama($kode, $nama);

                $jumlah = $volume * $harga;
                $totalNilai += $jumlah;

                $batchDetail[] = [
                    'rab_header_id'  => $headerId,
                    'no_urut'        => $i - 1,
                    'kode_pekerjaan' => $kode ?: null,
                    'nama_pekerjaan' => $nama,
                    'satuan'         => $satuan ?: null,
                    'volume'         => $volume,
                    'harga_satuan'   => $harga,
                    'jumlah'         => $jumlah,
                    'ahsp_id'        => $ahsp['id'] ?? null,
                    'match_status'   => $ahsp ? 'matched' : 'unmatched',
                ];
            }

            if (empty($batchDetail)) {
                $this->headerModel->update($headerId, [
                    'status'  => 'error',
                    'catatan' => 'Tidak ada baris data valid yang ditemukan di file.',
                ]);
                return redirect()->back()->with('error', 'File kosong atau format tidak sesuai. Pastikan data mulai dari baris ke-2.');
            }

            // Insert batch detail
            foreach (array_chunk($batchDetail, 300) as $chunk) {
                $this->detailModel->insertBatch($chunk);
            }

            // Update total nilai RAB
            $this->headerModel->update($headerId, [
                'total_nilai_rab' => $totalNilai,
            ]);

            // Langsung jalankan kalkulasi
            $result = $this->calcService->calculate($headerId);

            $msg = $result['message'];
            $matched = $this->detailModel->countMatched($headerId);
            $unmatched = $this->detailModel->countUnmatched($headerId);
            $msg .= " (Matched: {$matched}, Unmatched: {$unmatched})";

            return redirect()->to('/rab/detail/' . $headerId)->with('success', $msg);

        } catch (\Throwable $e) {
            $this->headerModel->update($headerId, [
                'status'  => 'error',
                'catatan' => $e->getMessage(),
            ]);

            return redirect()->back()->with('error', 'Gagal memproses file: ' . $e->getMessage());
        }
    }

    public function detail($id)
    {
        $header = $this->headerModel->find($id);
        if (!$header) {
            return redirect()->to('/rab')->with('error', 'Proyek RAB tidak ditemukan.');
        }

        $data = [
            'title'     => 'Detail RAB - ' . $header['nama_proyek'],
            'header'    => $header,
            'details'   => $this->detailModel->getByHeader($id),
            'matched'   => $this->detailModel->countMatched($id),
            'unmatched' => $this->detailModel->countUnmatched($id),
            'totals'    => $this->calcService->getTotalByJenis($id),
        ];

        return view('rab/detail', $data);
    }

    public function rekapBahan($id)
    {
        $header = $this->headerModel->find($id);
        if (!$header) {
            return redirect()->to('/rab')->with('error', 'Proyek RAB tidak ditemukan.');
        }

        $data = [
            'title'  => 'Rekapitulasi Kebutuhan Bahan',
            'header' => $header,
            'rekap'  => $this->calcService->getRekapitulasi($id, 'material'),
            'jenis'  => 'material',
        ];

        return view('rab/rekap_bahan', $data);
    }

    public function rekapUpah($id)
    {
        $header = $this->headerModel->find($id);
        if (!$header) {
            return redirect()->to('/rab')->with('error', 'Proyek RAB tidak ditemukan.');
        }

        $data = [
            'title'  => 'Rekapitulasi Kebutuhan Upah',
            'header' => $header,
            'rekap'  => $this->calcService->getRekapitulasi($id, 'upah'),
            'jenis'  => 'upah',
        ];

        return view('rab/rekap_upah', $data);
    }

    public function recalculate($id)
    {
        $header = $this->headerModel->find($id);
        if (!$header) {
            return redirect()->to('/rab')->with('error', 'Proyek RAB tidak ditemukan.');
        }

        $result = $this->calcService->calculate($id);

        return redirect()->to('/rab/detail/' . $id)
                         ->with($result['success'] ? 'success' : 'error', $result['message']);
    }
}
