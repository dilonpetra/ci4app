<?php

namespace App\Controllers;

use App\Models\KomikModel;

class Komik extends BaseController
{
    protected $komikModel; // Model untuk komik
    protected $helpers = ['form']; // Helper untuk form
    public function __construct()
    {
        $this->komikModel = new KomikModel(); // Instansiasi model komik
    }
    public function index()
    {
        // Title
        $data = [
            'title' => 'Daftar Komik ',
            'komik' => $this->komikModel->getKomik(),
        ];

        return view('komik/index', $data);
    }

    public function detail($slug)
    {
        // Get the komik with the slug
        $data = [
            'title' => 'Detail Komik',
            'komik' => $this->komikModel->getKomik(($slug))
        ];

        // If the komik doesn't exist, throw an error
        if (!$data['komik']) {
            throw new \CodeIgniter\Exceptions\PageNotFoundException('Judul komik ' . $slug . ' tidak ditemukan.');
        }

        return view('komik/detail', $data);
    }

    public function create()
    {
        // Data untuk title dan validation
        $data = [
            'title' => 'Form Tambah Data Komik',
            'validation' => \Config\Services::validation()
        ];

        // Membuka halaman create
        return view('komik/create', $data);
    }


    public function save()
    {
        $validation =  \Config\Services::validation();

        $input = $this->validate([
            'judul' => 'required|is_unique[komik.judul]',
            'penulis' => 'required',
            'penerbit' => 'required',
            'sampul' => 'required'
        ]);

        if (!$input) {
            return redirect()->to('/komik/create')->withInput()->with('validation', $validation);
        }

        $this->komikModel->save([
            'judul' => $this->request->getVar('judul'),
            'slug' => url_title($this->request->getVar('judul'), '-', true),
            'penulis' => $this->request->getVar('penulis'),
            'penerbit' => $this->request->getVar('penerbit'),
            'sampul' => $this->request->getVar('sampul')
        ]);

        session()->setFlashdata('pesan', 'Data berhasil ditambahkan.');

        return redirect()->to('/komik');
    }

    public function delete($id)
    {
        $this->komikModel->delete($id);
        session()->setFlashdata('pesan', 'Data berhasil dihapus.');
        return redirect()->to('/komik');
    }

    public function edit($slug)
    {
        $data = [
            'title' => 'Form Ubah Data Komik',
            'validation' => \Config\Services::validation(),
            'komik' => $this->komikModel->getKomik($slug)
        ];

        return view('komik/edit', $data);
    }

    public function update($id)
    {
        $komikLama = $this->komikModel->getKomik($this->request->getVar('slug'));
        if ($komikLama['judul'] == $this->request->getVar('judul')) {
            $rule_judul = 'required';
        } else {
            $rule_judul = 'required|is_unique[komik.judul]';
        }

        if (!$this->validate([
            'judul' => $rule_judul,
            'penulis' => 'required',
            'penerbit' => 'required',
            'sampul' => 'required'
        ])) {
            return redirect()->to('/komik/edit/' . $this->request->getVar('slug'))->withInput();
        }

        $this->komikModel->save([
            'id' => $id,
            'judul' => $this->request->getVar('judul'),
            'slug' => url_title($this->request->getVar('judul'), '-', true),
            'penulis' => $this->request->getVar('penulis'),
            'penerbit' => $this->request->getVar('penerbit'),
            'sampul' => $this->request->getVar('sampul')
        ]);

        session()->setFlashdata('pesan', 'Data berhasil diubah.');

        return redirect()->to('/komik');
    }
}

/*
$db = \Config\Database::connect();
        $komik = $db->query("SELECT * FROM komik");
        foreach ($komik->getResultArray() as $row) {
            d($row);
        }
        */