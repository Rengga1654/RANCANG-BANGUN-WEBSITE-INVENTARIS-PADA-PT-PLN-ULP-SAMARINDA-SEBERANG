<?php
require 'function.php';
require 'cek.php';

if (isset($_POST['tambah_stok_material'])) {
    $id_material = $_POST['id_material'];
    $tambah_stok = $_POST['tambah_stok'];

    $cek = mysqli_query($conn, "SELECT * FROM stock_gudang WHERE id_material='$id_material'");
    if (mysqli_num_rows($cek) > 0) {
        $update = mysqli_query($conn, "UPDATE stock_gudang SET jumlah = jumlah + $tambah_stok WHERE id_material='$id_material'");
    } else {
        $update = mysqli_query($conn, "INSERT INTO stock_gudang (id_material, jumlah) VALUES ('$id_material', '$tambah_stok')");
    }

    if ($update) {
        setNotifikasi('success', 'Stok berhasil ditambahkan!');
    } else {
        setNotifikasi('error', 'Gagal menambahkan stok!');
    }
    header("Location: index.php");
    exit;
}
