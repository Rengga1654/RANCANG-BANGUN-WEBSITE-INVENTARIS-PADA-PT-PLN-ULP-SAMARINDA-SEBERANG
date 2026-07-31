<?php
require 'function.php';
require 'cek.php';

// Di awal file
if ($_SESSION['level'] != 'admin') {
    header("Location: index.php");
    exit;
}

if (isset($_POST['edit_material'])) {
    $id_material = $_POST['id_material'];
    $kode_material = mysqli_real_escape_string($conn, $_POST['kode_material']);
    $nama_material = mysqli_real_escape_string($conn, $_POST['nama_material']);
    $satuan = mysqli_real_escape_string($conn, $_POST['satuan']);
    
    // Ambil harga dari form (format ribuan dengan titik)
    $harga = str_replace('.', '', $_POST['harga']);
    $harga = !empty($harga) ? (int) $harga : 0;

    // Cek duplikat kode (kecuali untuk dirinya sendiri)
    $cek_kode = mysqli_query($conn, "SELECT * FROM material WHERE kode_material = '$kode_material' AND id_material != '$id_material'");
    if (mysqli_num_rows($cek_kode) > 0) {
        setNotifikasi('error', 'Kode material sudah digunakan material lain!');
        header("Location: material.php");
        exit;
    }

    // Cek duplikat nama (kecuali untuk dirinya sendiri)
    $cek_nama = mysqli_query($conn, "SELECT * FROM material WHERE nama = '$nama_material' AND id_material != '$id_material'");
    if (mysqli_num_rows($cek_nama) > 0) {
        setNotifikasi('error', 'Nama material sudah digunakan material lain!');
        header("Location: material.php");
        exit;
    }

    // Update data (termasuk harga)
    $update = mysqli_query($conn, "UPDATE material SET 
                                    kode_material='$kode_material', 
                                    nama='$nama_material', 
                                    satuan='$satuan',
                                    harga='$harga'
                                WHERE id_material='$id_material'");

    if ($update) {
        setNotifikasi('success', 'Material berhasil diupdate!');
    } else {
        setNotifikasi('error', 'Gagal mengupdate material!');
    }
    header("Location: material.php");
    exit;
}