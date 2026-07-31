<?php
require 'function.php';
require 'cek.php';

$id_permintaan = $_GET['id'];

// Ambil data permintaan dari tabel permintaan
$query = mysqli_query($conn, "SELECT id_tujuan, status FROM permintaan WHERE id_permintaan = '$id_permintaan'");
$data = mysqli_fetch_assoc($query);

if (!$data) {
    setNotifikasi('error', 'Permintaan tidak ditemukan!');
    header("Location: index.php");
    exit;
}

// Hanya bisa hapus jika status pending
if ($data['status'] != 'pending') {
    setNotifikasi('error', 'Permintaan tidak bisa dihapus karena sudah diproses!');
    header("Location: permintaan_cabang.php?id=" . $data['id_tujuan']);
    exit;
}

// User cabang hanya bisa hapus permintaan cabang sendiri
if ($_SESSION['level'] == 'user_cabang' && $_SESSION['cabang_id'] != $data['id_tujuan']) {
    setNotifikasi('error', 'Anda tidak memiliki akses!');
    header("Location: index.php");
    exit;
}

// Mulai transaksi
mysqli_begin_transaction($conn);

try {
    // Hapus detail permintaan terlebih dahulu
    mysqli_query($conn, "DELETE FROM permintaan_detail WHERE id_permintaan = '$id_permintaan'");

    // Hapus permintaan
    mysqli_query($conn, "DELETE FROM permintaan WHERE id_permintaan = '$id_permintaan'");

    mysqli_commit($conn);
    setNotifikasi('success', 'Permintaan berhasil dihapus!');
} catch (Exception $e) {
    mysqli_rollback($conn);
    setNotifikasi('error', 'Gagal menghapus permintaan!');
}

header("Location: permintaan_cabang.php?id=" . $data['id_tujuan']);
exit;
