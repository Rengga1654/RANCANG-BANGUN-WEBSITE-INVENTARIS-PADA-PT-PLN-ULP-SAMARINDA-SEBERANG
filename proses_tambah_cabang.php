<?php
require 'function.php';
require 'cek.php';

if (isset($_POST['tambah_cabang'])) {
    $cabang_baru = mysqli_real_escape_string($conn, $_POST['cabang_baru']);

    // Tentukan redirect (dari form atau default ke barang_keluar.php)
    $redirect = isset($_POST['redirect']) ? $_POST['redirect'] : 'barang_keluar.php';

    if (empty($cabang_baru)) {
        setNotifikasi('error', 'Nama cabang tidak boleh kosong!');
        header("Location: $redirect");
        exit;
    }

    // Cek apakah sudah ada
    $cek = mysqli_query($conn, "SELECT * FROM tujuan WHERE nama = '$cabang_baru'");
    if (mysqli_num_rows($cek) > 0) {
        setNotifikasi('error', 'Cabang sudah ada!');
        header("Location: $redirect");
        exit;
    }

    // Insert cabang baru
    $insert = mysqli_query($conn, "INSERT INTO tujuan (nama) VALUES ('$cabang_baru')");

    if ($insert) {
        setNotifikasi('success', 'Cabang berhasil ditambahkan!');
        header("Location: $redirect");
        exit;
    } else {
        setNotifikasi('error', 'Gagal menambahkan cabang!');
        header("Location: $redirect");
        exit;
    }
} else {
    header("Location: barang_keluar.php");
    exit;
}
