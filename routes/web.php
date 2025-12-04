<?php

use Illuminate\Support\Facades\Auth;

// File route utama memanggil file lain
require __DIR__ . '/home.php';
require __DIR__ . '/admin.php';
require __DIR__ . '/hr.php';
require __DIR__ . '/hakim.php';
require __DIR__ . '/ketua.php';
require __DIR__ . '/pimpinan.php';
require __DIR__ . '/pegawai.php';
require __DIR__ . '/webhook.php';

// Auth
Auth::routes();