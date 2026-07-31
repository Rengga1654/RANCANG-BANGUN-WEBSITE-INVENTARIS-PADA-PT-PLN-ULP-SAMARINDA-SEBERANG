<?php
require 'function.php';
require 'cek.php';

// User cabang tidak boleh mengirim Material dari gudang
if ($_SESSION['level'] == 'user_cabang') {
    setNotifikasi('error', 'Anda tidak memiliki akses untuk mengirim Material dari gudang!');
    header("Location: stok_cabang.php?id=" . $_SESSION['cabang_id']);
    exit;
}

if (isset($_POST['barang_keluar'])) {
    $tanggal = date('Y-m-d', strtotime(str_replace('/', '-', $_POST['tanggal'])));
    $id_material = $_POST['id_material'];
    $qty = $_POST['qty'];
    $id_tujuan = $_POST['id_tujuan'];

    // Validasi jumlah
    if ($qty <= 0) {
        setNotifikasi('error', 'Jumlah harus lebih dari 0!');
        header("Location: barang_keluar.php");
        exit;
    }

    // Cek stok gudang
    $cek_stok = mysqli_query($conn, "SELECT jumlah FROM stock_gudang WHERE id_material='$id_material'");
    $stok_data = mysqli_fetch_assoc($cek_stok);
    $stok_tersedia = $stok_data ? $stok_data['jumlah'] : 0;

    if ($stok_tersedia >= $qty) {
        // Mulai transaksi
        mysqli_begin_transaction($conn);

        try {
            // 1. Insert ke transaksi keluar gudang
            mysqli_query($conn, "INSERT INTO transaksi_keluar_gudang (id_material, id_tujuan, tanggal, qty) 
                                VALUES ('$id_material', '$id_tujuan', '$tanggal', '$qty')");

            // 2. Kurangi stok gudang
            mysqli_query($conn, "UPDATE stock_gudang SET jumlah = jumlah - $qty WHERE id_material='$id_material'");

            // 3. Insert ke transaksi masuk tujuan
            mysqli_query($conn, "INSERT INTO transaksi_masuk_tujuan (id_material, id_tujuan, tanggal, qty, sumber) 
                                VALUES ('$id_material', '$id_tujuan', '$tanggal', '$qty', 'Gudang')");

            // 4. Tambah stok cabang tujuan
            $cek_stok_tujuan = mysqli_query($conn, "SELECT * FROM stock_tujuan WHERE id_material='$id_material' AND id_tujuan='$id_tujuan'");
            if (mysqli_num_rows($cek_stok_tujuan) > 0) {
                mysqli_query($conn, "UPDATE stock_tujuan SET jumlah = jumlah + $qty 
                                    WHERE id_material='$id_material' AND id_tujuan='$id_tujuan'");
            } else {
                mysqli_query($conn, "INSERT INTO stock_tujuan (id_material, id_tujuan, jumlah) 
                                    VALUES ('$id_material', '$id_tujuan', '$qty')");
            }

            // Commit semua query
            mysqli_commit($conn);
            setNotifikasi('success', 'Material berhasil dikirim ke cabang!');
            header("Location: barang_keluar.php");
            exit;
        } catch (Exception $e) {
            // Rollback jika ada error
            mysqli_rollback($conn);
            setNotifikasi('error', 'Terjadi kesalahan saat menyimpan data!');
            header("Location: barang_keluar.php");
            exit;
        }
    } else {
        setNotifikasi('error', "Stok tidak mencukupi! Stok tersedia: $stok_tersedia");
        header("Location: barang_keluar.php");
        exit;
    }
} else {
    header("Location: barang_keluar.php");
    exit;
}
