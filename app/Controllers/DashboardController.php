<?php

namespace App\Controllers;

use App\Models\AhspMasterModel;
use App\Models\RabHeaderModel;
use App\Models\SumberDayaModel;

class DashboardController extends BaseController
{
    public function index()
    {
        $ahspModel   = new AhspMasterModel();
        $headerModel = new RabHeaderModel();
        $sdModel     = new SumberDayaModel();

        $data = [
            'title'           => 'Dashboard',
            'total_ahsp'      => $ahspModel->where('is_active', 1)->countAllResults(),
            'total_proyek'    => $headerModel->countAllResults(),
            'proyek_selesai'  => $headerModel->where('status', 'selesai')->countAllResults(),
            'proyek_proses'   => $headerModel->where('status', 'proses')->countAllResults(),
            'total_material'  => $sdModel->where('jenis', 'material')->where('is_active', 1)->countAllResults(),
            'total_upah'      => $sdModel->where('jenis', 'upah')->where('is_active', 1)->countAllResults(),
            'proyek_terbaru'  => $headerModel->getLatest(5),
        ];

        return view('dashboard/index', $data);
    }
}
