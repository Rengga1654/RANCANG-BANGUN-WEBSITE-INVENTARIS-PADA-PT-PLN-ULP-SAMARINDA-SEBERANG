<?php
require 'function.php';
require 'cek.php';

if (isset($_POST['transfer_cabang'])) {
    $id_material = $_POST['id_material'];
    $dari_tujuan = $_POST['dari_tujuan'];
    $ke_tujuan = $_POST['ke_tujuan'];
    $qty = $_POST['qty'];
    $tanggal = $_POST['tanggal'];
    $keterangan = $_POST['keterangan'] ?? '';

    if ($qty <= 0) {
        setNotifikasi('error', 'Jumlah harus lebih dari 0!');
        header("Location: stok_cabang.php?id=$dari_tujuan");
        exit;
    }

    if ($dari_tujuan == $ke_tujuan) {
        setNotifikasi('error', 'Tidak bisa transfer ke cabang yang sama!');
        header("Location: stok_cabang.php?id=$dari_tujuan");
        exit;
    }

    // Cek stok di cabang asal
    $cek_stok = mysqli_query($conn, "SELECT jumlah FROM stock_tujuan 
                                    WHERE id_material='$id_material' AND id_tujuan='$dari_tujuan'");
    $stok_data = mysqli_fetch_assoc($cek_stok);
    $stok_tersedia = $stok_data ? $stok_data['jumlah'] : 0;

    if ($stok_tersedia >= $qty) {
        // Mulai transaksi
        mysqli_begin_transaction($conn);

        try {
            // 1. Catat transaksi keluar dari cabang
            mysqli_query($conn, "INSERT INTO transaksi_keluar_tujuan (id_material, dari_tujuan, ke_tujuan, tanggal, qty, keterangan) 
                                VALUES ('$id_material', '$dari_tujuan', '$ke_tujuan', '$tanggal', '$qty', '$keterangan')");

            // 2. Kurangi stok cabang asal
            mysqli_query($conn, "UPDATE stock_tujuan SET jumlah = jumlah - $qty 
                                WHERE id_material='$id_material' AND id_tujuan='$dari_tujuan'");

            // 3. Catat transaksi masuk ke cabang tujuan
            mysqli_query($conn, "INSERT INTO transaksi_masuk_tujuan (id_material, id_tujuan, tanggal, qty, sumber, asal_transfer) 
                                VALUES ('$id_material', '$ke_tujuan', '$tanggal', '$qty', 'Transfer', 
                                        (SELECT nama FROM tujuan WHERE id_tujuan='$dari_tujuan'))");

            // 4. Tambah stok cabang tujuan
            $cek = mysqli_query($conn, "SELECT * FROM stock_tujuan WHERE id_material='$id_material' AND id_tujuan='$ke_tujuan'");
            if (mysqli_num_rows($cek) > 0) {
                mysqli_query($conn, "UPDATE stock_tujuan SET jumlah = jumlah + $qty 
                                    WHERE id_material='$id_material' AND id_tujuan='$ke_tujuan'");
            } else {
                mysqli_query($conn, "INSERT INTO stock_tujuan (id_material, id_tujuan, jumlah) 
                                    VALUES ('$id_material', '$ke_tujuan', '$qty')");
            }

            mysqli_commit($conn);
            setNotifikasi('success', 'Transfer Material berhasil!');
            header("Location: stok_cabang.php?id=$dari_tujuan");
            exit;
        } catch (Exception $e) {
            mysqli_rollback($conn);
            setNotifikasi('error', 'Terjadi kesalahan saat transfer!');
            header("Location: stok_cabang.php?id=$dari_tujuan");
            exit;
        }
    } else {
        setNotifikasi('error', "Stok tidak mencukupi! Tersedia: $stok_tersedia");
        header("Location: stok_cabang.php?id=$dari_tujuan");
        exit;
    }
} else {
    header("Location: index.php");
    exit;
}
