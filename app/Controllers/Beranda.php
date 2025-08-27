<?php

namespace App\Controllers;

use App\Models\PengetahuanModel;
use App\Models\PelatihanModel;
use App\Models\UserModel;
use App\Models\PengajuanModel;

class Beranda extends BaseController
{
    protected $pengetahuanModel;
    protected $pelatihanModel;

    public function __construct()
    {
        $this->pengetahuanModel = new PengetahuanModel();
        $this->pelatihanModel = new PelatihanModel();
    }

    /**
     * Menampilkan halaman beranda dengan data pengetahuan dan pelatihan terbaru.
     */

    public function index()
    {
        $pengetahuanModel = new PengetahuanModel();
        $pelatihanModel = new PelatihanModel();
        $userModel = new UserModel();
        $pengajuanModel = new PengajuanModel();

        $data = [
            'title' => 'Beranda',
            'pengetahuan' => $this->pengetahuanModel->getPublicPengetahuan(5), // Ambil 5 terbaru
            'pelatihan' => $this->pelatihanModel->getPublicPelatihan(5), // Ambil 5 terbaru
            'total_pengetahuan' => $pengetahuanModel->countAll(),
            'total_pelatihan' => $pelatihanModel->countAll(),
            'total_pengguna' => $userModel->where('level', 'user')->countAllResults(),
            'total_pengajuan' => $pengajuanModel->countAllResults(),
            'pengetahuan_terbaru' => $pengetahuanModel->orderBy('created_at', 'DESC')->findAll(5),
            'pelatihan_terbaru' => $pelatihanModel->orderBy('created_at', 'DESC')->findAll(5)
            // 'aktivitas_terbaru' => $this->getAktivitasTerbaru() // Method untuk mendapatkan aktivitas
        ];

        return view('admin/beranda', $data);
    }
}
