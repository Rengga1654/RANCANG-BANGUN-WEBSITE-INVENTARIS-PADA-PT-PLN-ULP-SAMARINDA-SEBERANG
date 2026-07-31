<?php
require 'function.php';
require 'cek.php';

if (isset($_POST['buat_permintaan'])) {
    $id_tujuan = $_POST['id_tujuan'];
    $no_permintaan = $_POST['no_permintaan'];
    $tanggal_permintaan = date('Y-m-d', strtotime(str_replace('/', '-', $_POST['tanggal_permintaan'])));
    $catatan = $_POST['catatan'] ?? '';

    $id_material_list = $_POST['id_material'];
    $jumlah_list = $_POST['jumlah'];

    // Validasi user cabang
    if ($_SESSION['level'] == 'user_cabang' && $_SESSION['cabang_id'] != $id_tujuan) {
        setNotifikasi('error', 'Anda tidak memiliki akses!');
        header("Location: permintaan_cabang.php?id=" . $_SESSION['cabang_id']);
        exit;
    }

    // Validasi minimal 1 material
    $valid = false;
    foreach ($id_material_list as $key => $id_material) {
        if (!empty($id_material) && !empty($jumlah_list[$key]) && $jumlah_list[$key] > 0) {
            $valid = true;
            break;
        }
    }

    if (!$valid) {
        setNotifikasi('error', 'Minimal 1 material harus diisi dengan jumlah yang valid!');
        header("Location: permintaan_cabang.php?id=$id_tujuan");
        exit;
    }

    // Mulai transaksi
    mysqli_begin_transaction($conn);

    try {
        // Insert ke tabel permintaan
        $insert = mysqli_query($conn, "INSERT INTO permintaan (no_permintaan, id_tujuan, tanggal_permintaan, catatan, status) 
                                        VALUES ('$no_permintaan', '$id_tujuan', '$tanggal_permintaan', '$catatan', 'pending')");

        if (!$insert) {
            throw new Exception('Gagal menyimpan permintaan');
        }

        $id_permintaan = mysqli_insert_id($conn);

        // Insert detail permintaan
        foreach ($id_material_list as $key => $id_material) {
            if (empty($id_material)) continue;

            $jumlah = (int)$jumlah_list[$key];
            if ($jumlah <= 0) continue;

            $insert_detail = mysqli_query($conn, "INSERT INTO permintaan_detail (id_permintaan, id_material, jumlah) 
                                                    VALUES ('$id_permintaan', '$id_material', '$jumlah')");
            if (!$insert_detail) {
                throw new Exception('Gagal menyimpan detail permintaan');
            }
        }

        mysqli_commit($conn);
        setNotifikasi('success', 'Permintaan Material berhasil dikirim!');
        header("Location: permintaan_cabang.php?id=$id_tujuan");
        exit;
    } catch (Exception $e) {
        mysqli_rollback($conn);
        setNotifikasi('error', 'Gagal mengirim permintaan: ' . $e->getMessage());
        header("Location: permintaan_cabang.php?id=$id_tujuan");
        exit;
    }
} else {
    header("Location: permintaan_cabang.php");
    exit;
}
