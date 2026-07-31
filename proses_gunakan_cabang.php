
<?php
require 'function.php';
require 'cek.php';

if (isset($_POST['gunakan_cabang'])) {
    $id_material = $_POST['id_material'];
    $id_tujuan = $_POST['id_tujuan'];
    $qty = $_POST['qty'];
    $tanggal = $_POST['tanggal'];
    $keterangan = $_POST['keterangan'] ?? '';

    if ($qty <= 0) {
        setNotifikasi('error', 'Jumlah harus lebih dari 0!');
        header("Location: stok_cabang.php?id=$id_tujuan");
        exit;
    }

    // Cek stok cabang
    $cek_stok = mysqli_query($conn, "SELECT jumlah FROM stock_tujuan 
                                    WHERE id_material='$id_material' AND id_tujuan='$id_tujuan'");
    $stok_data = mysqli_fetch_assoc($cek_stok);
    $stok_tersedia = $stok_data ? $stok_data['jumlah'] : 0;

    if ($stok_tersedia >= $qty) {
        // Mulai transaksi
        mysqli_begin_transaction($conn);

        try {
            // 1. Catat transaksi Material keluar dari cabang (digunakan)
            mysqli_query($conn, "INSERT INTO transaksi_keluar_dari_cabang (id_material, id_tujuan, tanggal, qty, keterangan) 
                                VALUES ('$id_material', '$id_tujuan', '$tanggal', '$qty', '$keterangan')");

            // 2. Kurangi stok cabang
            mysqli_query($conn, "UPDATE stock_tujuan SET jumlah = jumlah - $qty 
                                WHERE id_material='$id_material' AND id_tujuan='$id_tujuan'");

            mysqli_commit($conn);
            setNotifikasi('success', 'Material berhasil digunakan!');
            header("Location: stok_cabang.php?id=$id_tujuan");
            exit;
        } catch (Exception $e) {
            mysqli_rollback($conn);
            setNotifikasi('error', 'Terjadi kesalahan saat memproses!');
            header("Location: stok_cabang.php?id=$id_tujuan");
            exit;
        }
    } else {
        setNotifikasi('error', "Stok tidak mencukupi! Tersedia: $stok_tersedia");
        header("Location: stok_cabang.php?id=$id_tujuan");
        exit;
    }
} else {
    header("Location: index.php");
    exit;
}
