<?php

namespace App\Controllers;

use App\Models\PelatihanModel;
use App\Models\UserModel;

class Pelatihan extends BaseController
{
    protected $pelatihanModel;
    protected $userModel;

    public function __construct()
    {
        $this->pelatihanModel = new PelatihanModel();
        $this->userModel = new UserModel();
    }

    public function index()
    {
        $keyword = $this->request->getGet('q'); // Menggunakan 'q' sesuai dengan form

        $data = [
            'title' => 'Daftar Pelatihan',
            'pelatihan' => $this->pelatihanModel->getPelatihanWithSearch($keyword),
            'pager' => $this->pelatihanModel->pager,
            'keyword' => $keyword, // Menggunakan 'keyword' untuk konsistensi
        ];

        return view('/pelatihan/index', $data);
    }

    public function view($id)
    {
        $pelatihanModel = new PelatihanModel();
        $komentarModel    = new \App\Models\KomentarPelatihanModel();

        // Ambil data pelatihan utama
        $pelatihan = $pelatihanModel->getPelatihanWithUserById($id);

        if (!$pelatihan) {
            throw \CodeIgniter\Exceptions\PageNotFoundException::forPageNotFound(
                "Pelatihan dengan ID $id tidak ditemukan"
            );
        }

        // Ambil pelatihan lain selain ID ini (maksimal 5 data)
        $pelatihanLain = $pelatihanModel
            ->where('id !=', $id)
            ->findAll(5);

        // Ambil komentar terkait
        $komentar = $komentarModel->getKomentarByPelatihan($id);

        // Kirim semua data ke view
        return view('/pelatihan/view', [
            'pelatihan'      => $pelatihan,
            'pelatihan_lain' => $pelatihanLain,
            'komentar'         => $komentar
        ]);
    }


    public function comment($pelatihan_id)
    {
        if (!$this->request->is('post')) {
            return redirect()->back();
        }

        $validation = \Config\Services::validation();
        $validation->setRules([
            'komentar' => 'required|min_length[3]|max_length[1000]'
        ]);

        if (!$validation->withRequest($this->request)->run()) {
            return redirect()->back()->withInput()->with('errors', $validation->getErrors());
        }

        $komentarModel = new \App\Models\KomentarPelatihanModel();

        $data = [
            'pelatihan_id' => $pelatihan_id,
            'user_id' => session()->get('id'), // Pastikan user sudah login
            'komentar' => $this->request->getPost('komentar'),
            'created_at' => date('Y-m-d H:i:s')
        ];

        if ($komentarModel->addKomentar($data)) {
            return redirect()->back()->with('message', 'Komentar berhasil ditambahkan');
        } else {
            return redirect()->back()->with('error', 'Gagal menambahkan komentar');
        }
    }

    // app/Controllers/Pelatihan.php
    public function deleteComment($comment_id)
    {
        // Pastikan user sudah login
        if (!session()->get('logged_in')) {
            return redirect()->to('login')->with('error', 'Silakan login terlebih dahulu');
        }

        $komentarModel = new \App\Models\KomentarPelatihanModel();
        $user_id = session()->get('id');

        // Cek hak akses
        if (!$komentarModel->canDelete($comment_id, $user_id)) {
            return redirect()->back()->with('error', 'Anda tidak memiliki hak untuk menghapus komentar ini');
        }

        if ($komentarModel->delete($comment_id)) {
            return redirect()->back()->with('message', 'Komentar berhasil dihapus');
        } else {
            return redirect()->back()->with('error', 'Gagal menghapus komentar');
        }
    }
}
