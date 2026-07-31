<?php
require 'function.php';
require 'cek.php';

// Di awal file
if($_SESSION['level'] != 'admin'){
    header("Location: index.php");
    exit;
}

if (isset($_POST['tambah_material'])) {
    $kode_material = mysqli_real_escape_string($conn, $_POST['kode_material']);
    $nama_material = mysqli_real_escape_string($conn, $_POST['nama_material']);
    $satuan = mysqli_real_escape_string($conn, $_POST['satuan']);

    // Cek duplikat kode
    $cek = mysqli_query($conn, "SELECT * FROM material WHERE kode_material = '$kode_material'");
    if (mysqli_num_rows($cek) > 0) {
        setNotifikasi('error', 'Kode material sudah ada! Gunakan kode yang berbeda.');
    } else {
        $insert = mysqli_query($conn, "INSERT INTO material (kode_material, nama, satuan) 
        VALUES ('$kode_material', '$nama_material', '$satuan')");
        if ($insert) {
            setNotifikasi('success', 'Material berhasil ditambahkan!');
        } else {
            setNotifikasi('error', 'Gagal menambahkan material: ' . mysqli_error($conn));
        }
    }
    header("Location: material.php");
    exit;
}
