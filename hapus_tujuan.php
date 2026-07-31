<?php
require 'function.php';
require 'cek.php';

// Di awal file
if($_SESSION['level'] != 'admin'){
    header("Location: index.php");
    exit;
}

$id_tujuan = $_GET['id'];

// Cek apakah tujuan masih digunakan di berbagai tabel
$cek_stok_tujuan = mysqli_query($conn, "SELECT * FROM stock_tujuan WHERE id_tujuan = '$id_tujuan'");
$cek_keluar_gudang = mysqli_query($conn, "SELECT * FROM transaksi_keluar_gudang WHERE id_tujuan = '$id_tujuan'");
$cek_keluar_tujuan = mysqli_query($conn, "SELECT * FROM transaksi_keluar_tujuan WHERE dari_tujuan = '$id_tujuan' OR ke_tujuan = '$id_tujuan'");
$cek_masuk_tujuan = mysqli_query($conn, "SELECT * FROM transaksi_masuk_tujuan WHERE id_tujuan = '$id_tujuan'");
$cek_keluar_cabang = mysqli_query($conn, "SELECT * FROM transaksi_keluar_dari_cabang WHERE id_tujuan = '$id_tujuan'");

if (
    mysqli_num_rows($cek_stok_tujuan) > 0 ||
    mysqli_num_rows($cek_keluar_gudang) > 0 ||
    mysqli_num_rows($cek_keluar_tujuan) > 0 ||
    mysqli_num_rows($cek_masuk_tujuan) > 0 ||
    mysqli_num_rows($cek_keluar_cabang) > 0
) {

    setNotifikasi('error', 'Tujuan tidak bisa dihapus karena sudah memiliki riwayat transaksi atau stok!');
    header("Location: tujuan.php");
    exit;
} else {
    // Hapus data di stock_tujuan terlebih dahulu jika ada
    mysqli_query($conn, "DELETE FROM stock_tujuan WHERE id_tujuan = '$id_tujuan'");

    // Hapus tujuan
    $hapus = mysqli_query($conn, "DELETE FROM tujuan WHERE id_tujuan = '$id_tujuan'");

    if ($hapus) {
        setNotifikasi('success', 'Tujuan berhasil dihapus!');
    } else {
        setNotifikasi('error', 'Gagal menghapus tujuan!');
    }
    header("Location: tujuan.php");
    exit;
}
