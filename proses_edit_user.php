<?php
require 'function.php';
require 'cek.php';

if ($_SESSION['level'] != 'admin') {
    header("Location: index.php");
    exit;
}

if (isset($_POST['edit_user'])) {
    $iduser = $_POST['iduser'];
    $email = mysqli_real_escape_string($conn, $_POST['email']);
    $password = $_POST['password'];
    $nama_lengkap = mysqli_real_escape_string($conn, $_POST['nama_lengkap'] ?? '');
    
    // Ambil data user yang sedang diedit
    $query_user = mysqli_query($conn, "SELECT * FROM login WHERE iduser = '$iduser'");
    $user = mysqli_fetch_assoc($query_user);
    
    // Cek email sudah ada (kecuali dirinya sendiri)
    $cek = mysqli_query($conn, "SELECT * FROM login WHERE email = '$email' AND iduser != '$iduser'");
    if (mysqli_num_rows($cek) > 0) {
        setNotifikasi('error', 'Email sudah digunakan user lain!');
        header("Location: user.php");
        exit;
    }

    // Jika user yang diedit adalah admin sendiri
    if ($user['email'] == 'admin@gmail.com') {
        // Admin sendiri: hanya bisa edit password dan nama (email tidak bisa diubah)
        if (!empty($password)) {
            $query = mysqli_query($conn, "UPDATE login SET 
                                            password = '$password', 
                                            nama_lengkap = '$nama_lengkap' 
                                        WHERE iduser = '$iduser'");
        } else {
            $query = mysqli_query($conn, "UPDATE login SET 
                                            nama_lengkap = '$nama_lengkap' 
                                        WHERE iduser = '$iduser'");
        }
    } else {
        // User cabang: bisa edit semua
        $level = $_POST['level'];
        $cabang_id = !empty($_POST['cabang_id']) ? $_POST['cabang_id'] : NULL;
        
        if (!empty($password)) {
            $query = mysqli_query($conn, "UPDATE login SET 
                                            email = '$email', 
                                            password = '$password', 
                                            nama_lengkap = '$nama_lengkap', 
                                            level = '$level', 
                                            cabang_id = '$cabang_id' 
                                        WHERE iduser = '$iduser'");
        } else {
            $query = mysqli_query($conn, "UPDATE login SET 
                                            email = '$email', 
                                            nama_lengkap = '$nama_lengkap', 
                                            level = '$level', 
                                            cabang_id = '$cabang_id' 
                                        WHERE iduser = '$iduser'");
        }
    }

    if ($query) {
        setNotifikasi('success', 'User berhasil diupdate!');
    } else {
        setNotifikasi('error', 'Gagal mengupdate user!');
    }
    header("Location: user.php");
    exit;
}