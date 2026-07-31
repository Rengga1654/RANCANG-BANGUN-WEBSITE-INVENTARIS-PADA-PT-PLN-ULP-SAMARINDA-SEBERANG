<?php
require 'function.php';
require 'cek.php';

if ($_SESSION['level'] != 'admin') {
    header("Location: index.php");
    exit;
}

$iduser = $_GET['id'];

// Cek jangan sampai menghapus admin utama
$cek = mysqli_query($conn, "SELECT email FROM login WHERE iduser = '$iduser'");
$data = mysqli_fetch_assoc($cek);
if ($data['email'] == 'admin@gmail.com') {
    setNotifikasi('error', 'Tidak bisa menghapus user admin utama!');
    header("Location: user.php");
    exit;
}

$hapus = mysqli_query($conn, "DELETE FROM login WHERE iduser = '$iduser'");

if ($hapus) {
    setNotifikasi('success', 'User berhasil dihapus!');
} else {
    setNotifikasi('error', 'Gagal menghapus user!');
}
header("Location: user.php");
exit;
