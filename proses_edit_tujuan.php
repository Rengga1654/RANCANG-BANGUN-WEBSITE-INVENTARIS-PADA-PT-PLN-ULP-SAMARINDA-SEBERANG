<?php
require 'function.php';
require 'cek.php';

// Di awal file
if($_SESSION['level'] != 'admin'){
    header("Location: index.php");
    exit;
}

if (isset($_POST['edit_tujuan'])) {
    $id_tujuan = $_POST['id_tujuan'];
    $nama = mysqli_real_escape_string($conn, $_POST['nama']);

    if (empty($nama)) {
        setNotifikasi('error', 'Nama tujuan tidak boleh kosong!');
        header("Location: tujuan.php");
        exit;
    }

    // Cek duplikat (kecuali dirinya sendiri)
    $cek = mysqli_query($conn, "SELECT * FROM tujuan WHERE nama = '$nama' AND id_tujuan != '$id_tujuan'");
    if (mysqli_num_rows($cek) > 0) {
        setNotifikasi('error', 'Nama tujuan sudah digunakan oleh tujuan lain!');
        header("Location: tujuan.php");
        exit;
    }

    $update = mysqli_query($conn, "UPDATE tujuan SET nama = '$nama' WHERE id_tujuan = '$id_tujuan'");

    if ($update) {
        setNotifikasi('success', 'Tujuan berhasil diupdate!');
    } else {
        setNotifikasi('error', 'Gagal mengupdate tujuan!');
    }
    header("Location: tujuan.php");
    exit;
} else {
    header("Location: tujuan.php");
    exit;
}
