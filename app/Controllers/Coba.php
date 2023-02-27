<?php

namespace App\Controllers;

class Coba extends BaseController
{
    public function index()
    {
        echo "ini controler coba method index";
    }


    public function about($nama = 'dilon')
    {
        echo " halo saya $nama ";
    }
}
