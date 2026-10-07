<?php

namespace App\Controllers;

use App\Models\AhspMasterModel;
use App\Models\AhspRincianModel;
use App\Models\AhspKategoriModel;
use App\Models\SumberDayaModel;

class AHSPController extends BaseController
{
    protected AhspMasterModel $ahspModel;
    protected AhspRincianModel $rincianModel;
    protected AhspKategoriModel $kategoriModel;
    protected SumberDayaModel $sdModel;

    public function __construct()
    {
        $this->ahspModel     = new AhspMasterModel();
        $this->rincianModel  = new AhspRincianModel();
        $this->kategoriModel = new AhspKategoriModel();
        $this->sdModel       = new SumberDayaModel();
    }

    public function index()
    {
        $data = [
            'title' => 'Manajemen AHSP',
            'list'  => $this->ahspModel->getWithKategori(),
        ];

        return view('ahsp/index', $data);
    }

    public function create()
    {
        $data = [
            'title'      => 'Tambah AHSP',
            'ahsp'       => null,
            'rincian'    => [],
            'kategori'   => $this->kategoriModel->where('is_active', 1)->orderBy('urutan')->findAll(),
            'sumberDaya' => $this->sdModel->where('is_active', 1)->orderBy('jenis')->orderBy('nama_sd')->findAll(),
        ];

        return view('ahsp/form', $data);
    }

    public function store()
    {
        $rules = [
            'kode_ahsp'      => 'required|is_unique[ahsp_master.kode_ahsp]|max_length[30]',
            'nama_pekerjaan' => 'required|max_length[255]',
            'satuan'         => 'required|max_length[20]',
            'kategori_id'    => 'required|integer',
        ];

        if (!$this->validate($rules)) {
            return redirect()->back()
                             ->withInput()
                             ->with('errors', $this->validator->getErrors());
        }

        $ahspId = $this->ahspModel->insert([
            'kategori_id'    => $this->request->getPost('kategori_id'),
            'kode_ahsp'      => $this->request->getPost('kode_ahsp'),
            'nama_pekerjaan' => $this->request->getPost('nama_pekerjaan'),
            'satuan'         => $this->request->getPost('satuan'),
            'keterangan'     => $this->request->getPost('keterangan'),
            'is_active'      => 1,
        ]);

        $this->saveRincian($ahspId);

        return redirect()->to('/ahsp')->with('success', 'AHSP berhasil ditambahkan.');
    }

    public function edit($id)
    {
        $ahsp = $this->ahspModel->find($id);
        if (!$ahsp) {
            return redirect()->to('/ahsp')->with('error', 'Data AHSP tidak ditemukan.');
        }

        $data = [
            'title'      => 'Edit AHSP',
            'ahsp'       => $ahsp,
            'rincian'    => $this->rincianModel->getRincianByAhsp($id),
            'kategori'   => $this->kategoriModel->where('is_active', 1)->orderBy('urutan')->findAll(),
            'sumberDaya' => $this->sdModel->where('is_active', 1)->orderBy('jenis')->orderBy('nama_sd')->findAll(),
        ];

        return view('ahsp/form', $data);
    }

    public function update($id)
    {
        $ahsp = $this->ahspModel->find($id);
        if (!$ahsp) {
            return redirect()->to('/ahsp')->with('error', 'Data AHSP tidak ditemukan.');
        }

        $rules = [
            'kode_ahsp'      => "required|max_length[30]|is_unique[ahsp_master.kode_ahsp,id,{$id}]",
            'nama_pekerjaan' => 'required|max_length[255]',
            'satuan'         => 'required|max_length[20]',
            'kategori_id'    => 'required|integer',
        ];

        if (!$this->validate($rules)) {
            return redirect()->back()
                             ->withInput()
                             ->with('errors', $this->validator->getErrors());
        }

        $this->ahspModel->update($id, [
            'kategori_id'    => $this->request->getPost('kategori_id'),
            'kode_ahsp'      => $this->request->getPost('kode_ahsp'),
            'nama_pekerjaan' => $this->request->getPost('nama_pekerjaan'),
            'satuan'         => $this->request->getPost('satuan'),
            'keterangan'     => $this->request->getPost('keterangan'),
        ]);

        // Hapus rincian lama, simpan yang baru
        $this->rincianModel->deleteByAhsp($id);
        $this->saveRincian($id);

        return redirect()->to('/ahsp')->with('success', 'AHSP berhasil diperbarui.');
    }

    public function delete($id)
    {
        $ahsp = $this->ahspModel->find($id);
        if (!$ahsp) {
            return redirect()->to('/ahsp')->with('error', 'Data AHSP tidak ditemukan.');
        }

        // Soft-ish: nonaktifkan saja (rincian ikut cascade jika hard delete)
        $this->ahspModel->update($id, ['is_active' => 0]);

        return redirect()->to('/ahsp')->with('success', 'AHSP berhasil dihapus/dinonaktifkan.');
    }

    /**
     * Simpan batch rincian koefisien dari form
     */
    private function saveRincian(int $ahspId): void
    {
        $sdIds     = $this->request->getPost('sumber_daya_id') ?? [];
        $koefisien = $this->request->getPost('koefisien') ?? [];

        $batch = [];
        foreach ($sdIds as $i => $sdId) {
            if (!empty($sdId) && isset($koefisien[$i]) && (float) $koefisien[$i] > 0) {
                $batch[] = [
                    'ahsp_id'        => $ahspId,
                    'sumber_daya_id' => (int) $sdId,
                    'koefisien'      => (float) $koefisien[$i],
                ];
            }
        }

        if (!empty($batch)) {
            $this->rincianModel->insertBatch($batch);
        }
    }
}
