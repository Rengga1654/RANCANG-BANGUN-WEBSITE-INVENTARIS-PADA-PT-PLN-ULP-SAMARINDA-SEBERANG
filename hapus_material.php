<?php
require 'function.php';
require 'cek.php';

// Di awal file
if($_SESSION['level'] != 'admin'){
    header("Location: index.php");
    exit;
}

$id = $_GET['id'];

// Cek apakah material sudah digunakan di transaksi
$cek_keluar_gudang = mysqli_query($conn, "SELECT * FROM transaksi_keluar_gudang WHERE id_material='$id'");
$cek_masuk_gudang = mysqli_query($conn, "SELECT * FROM transaksi_masuk_gudang WHERE id_material='$id'");
$cek_keluar_tujuan = mysqli_query($conn, "SELECT * FROM transaksi_keluar_tujuan WHERE id_material='$id'");
$cek_masuk_tujuan = mysqli_query($conn, "SELECT * FROM transaksi_masuk_tujuan WHERE id_material='$id'");
$cek_stok_gudang = mysqli_query($conn, "SELECT * FROM stock_gudang WHERE id_material='$id' AND jumlah > 0");
$cek_stok_tujuan = mysqli_query($conn, "SELECT * FROM stock_tujuan WHERE id_material='$id' AND jumlah > 0");

if (
    mysqli_num_rows($cek_keluar_gudang) > 0 ||
    mysqli_num_rows($cek_masuk_gudang) > 0 ||
    mysqli_num_rows($cek_keluar_tujuan) > 0 ||
    mysqli_num_rows($cek_masuk_tujuan) > 0 ||
    mysqli_num_rows($cek_stok_gudang) > 0 ||
    mysqli_num_rows($cek_stok_tujuan) > 0
) {

    setNotifikasi('error', 'Material tidak bisa dihapus karena sudah memiliki riwayat transaksi atau stok tidak nol!');
    header("Location: material.php");
    exit;
} else {
    // Hapus dari stock_gudang jika ada
    mysqli_query($conn, "DELETE FROM stock_gudang WHERE id_material='$id'");

    // Hapus material
    $hapus = mysqli_query($conn, "DELETE FROM material WHERE id_material='$id'");

    if ($hapus) {
        setNotifikasi('success', 'Material berhasil dihapus!');
    } else {
        setNotifikasi('error', 'Gagal menghapus material!');
    }
    header("Location: material.php");
    exit;
}
