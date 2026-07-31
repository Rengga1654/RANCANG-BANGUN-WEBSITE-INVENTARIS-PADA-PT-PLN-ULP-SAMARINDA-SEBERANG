<?php
require 'function.php';
require 'cek.php';

if ($_SESSION['level'] != 'admin') {
    header("Location: index.php");
    exit;
}

if (isset($_POST['tambah_user'])) {
    $email = mysqli_real_escape_string($conn, $_POST['email']);
    $password = mysqli_real_escape_string($conn, $_POST['password']);
    $nama_lengkap = mysqli_real_escape_string($conn, $_POST['nama_lengkap'] ?? '');
    $level = $_POST['level'];
    $cabang_id = $_POST['cabang_id'] ? $_POST['cabang_id'] : NULL;

    // Cek email sudah ada
    $cek = mysqli_query($conn, "SELECT * FROM login WHERE email = '$email'");
    if (mysqli_num_rows($cek) > 0) {
        setNotifikasi('error', 'Email sudah terdaftar!');
        header("Location: user.php");
        exit;
    }

    $insert = mysqli_query($conn, "INSERT INTO login (email, password, nama_lengkap, level, cabang_id) 
                                VALUES ('$email', '$password', '$nama_lengkap', '$level', '$cabang_id')");

    if ($insert) {
        setNotifikasi('success', 'User berhasil ditambahkan!');
    } else {
        setNotifikasi('error', 'Gagal menambahkan user!');
    }
    header("Location: user.php");
    exit;
}
