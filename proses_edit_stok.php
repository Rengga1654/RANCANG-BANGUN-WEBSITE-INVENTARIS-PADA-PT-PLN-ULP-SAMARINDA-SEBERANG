<?php
require 'function.php';
require 'cek.php';

if (isset($_POST['edit_stok'])) {
    $id_material = $_POST['id_material'];
    $jumlah = $_POST['jumlah'];

    $update = mysqli_query($conn, "UPDATE stock_gudang SET jumlah='$jumlah' WHERE id_material='$id_material'");

    if ($update) {
        setNotifikasi('success', 'Stok berhasil diupdate!');
    } else {
        setNotifikasi('error', 'Gagal mengupdate stok!');
    }
    header("Location: index.php");
    exit;
}
