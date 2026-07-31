<?php
require 'function.php';
require 'cek.php';

// User cabang tidak boleh menambah Material masuk
if($_SESSION['level'] == 'user_cabang'){
    setNotifikasi('error', 'Anda tidak memiliki akses untuk menambah Material masuk!');
    header("Location: stok_cabang.php?id=" . $_SESSION['cabang_id']);
    exit;
}

if (isset($_POST['barang_masuk'])) {
    $tanggal = date('Y-m-d', strtotime(str_replace('/', '-', $_POST['tanggal'])));
    $id_material = $_POST['id_material'];
    $qty = $_POST['qty'];
    $supplier = $_POST['supplier'] ?? '';

    if ($qty <= 0) {
        setNotifikasi('error', 'Jumlah harus lebih dari 0!');
        header("Location: barang_masuk.php");
        exit;
    }

    // Mulai transaksi
    mysqli_begin_transaction($conn);

    try {
        // 1. Insert ke transaksi masuk gudang
        mysqli_query($conn, "INSERT INTO transaksi_masuk_gudang (id_material, tanggal, qty, supplier) 
                            VALUES ('$id_material', '$tanggal', '$qty', '$supplier')");

        // 2. Update stok gudang
        $cek_stok = mysqli_query($conn, "SELECT * FROM stock_gudang WHERE id_material='$id_material'");
        if (mysqli_num_rows($cek_stok) > 0) {
            mysqli_query($conn, "UPDATE stock_gudang SET jumlah = jumlah + $qty WHERE id_material='$id_material'");
        } else {
            mysqli_query($conn, "INSERT INTO stock_gudang (id_material, jumlah) VALUES ('$id_material', '$qty')");
        }

        mysqli_commit($conn);
        setNotifikasi('success', 'Material masuk berhasil ditambahkan!');
        header("Location: barang_masuk.php");
        exit;
    } catch (Exception $e) {
        mysqli_rollback($conn);
        setNotifikasi('error', 'Terjadi kesalahan saat menyimpan data!');
        header("Location: barang_masuk.php");
        exit;
    }
} else {
    header("Location: barang_masuk.php");
    exit;
}
