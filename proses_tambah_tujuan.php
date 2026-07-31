<?php
require 'function.php';
require 'cek.php';

// Di awal file
if($_SESSION['level'] != 'admin'){
    header("Location: index.php");
    exit;
}

if (isset($_POST['tambah_tujuan'])) {
    $nama = mysqli_real_escape_string($conn, $_POST['nama']);
    
    if (empty($nama)) {
        setNotifikasi('error', 'Nama tujuan tidak boleh kosong!');
        header("Location: tujuan.php");
        exit;
    }
    
    // Cek apakah sudah ada
    $cek = mysqli_query($conn, "SELECT * FROM tujuan WHERE nama = '$nama'");
    if (mysqli_num_rows($cek) > 0) {
        setNotifikasi('error', 'Nama tujuan sudah ada!');
        header("Location: tujuan.php");
        exit;
    }
    
    // Simpan ke database
    $insert = mysqli_query($conn, "INSERT INTO tujuan (nama) VALUES ('$nama')");
    
    if ($insert) {
        setNotifikasi('success', 'Tujuan berhasil ditambahkan!');
    } else {
        setNotifikasi('error', 'Gagal menambahkan tujuan!');
    }
    header("Location: tujuan.php");
    exit;
} else {
    header("Location: tujuan.php");
    exit;
}
?>