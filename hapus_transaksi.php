<?php
require 'function.php';
require 'cek.php';

$type = $_GET['type'];

if ($type == 'masuk_gudang') {
    $id = $_GET['id'];
    $id_material = $_GET['id_material'];
    $qty = $_GET['qty'];

    mysqli_begin_transaction($conn);

    try {
        // Hapus transaksi
        mysqli_query($conn, "DELETE FROM transaksi_masuk_gudang WHERE id_transaksi='$id'");

        // Kurangi stok gudang
        mysqli_query($conn, "UPDATE stock_gudang SET jumlah = jumlah - $qty WHERE id_material='$id_material'");

        mysqli_commit($conn);
        setNotifikasi('success', 'Transaksi berhasil dihapus!');
        header("Location: barang_masuk.php");
        exit;
    } catch (Exception $e) {
        mysqli_rollback($conn);
        setNotifikasi('error', 'Gagal menghapus transaksi!');
        header("Location: barang_masuk.php");
        exit;
    }
} elseif ($type == 'keluar_gudang') {
    $id = $_GET['id'];
    $id_material = $_GET['id_material'];
    $qty = $_GET['qty'];
    $id_tujuan = $_GET['id_tujuan'];

    mysqli_begin_transaction($conn);

    try {
        // Hapus transaksi keluar gudang
        mysqli_query($conn, "DELETE FROM transaksi_keluar_gudang WHERE id_transaksi='$id'");

        // Hapus transaksi masuk tujuan
        mysqli_query($conn, "DELETE FROM transaksi_masuk_tujuan 
                             WHERE id_material='$id_material' AND id_tujuan='$id_tujuan' AND qty='$qty' 
                             ORDER BY id_transaksi DESC LIMIT 1");

        // Kembalikan stok gudang
        mysqli_query($conn, "UPDATE stock_gudang SET jumlah = jumlah + $qty WHERE id_material='$id_material'");

        // Kurangi stok cabang
        mysqli_query($conn, "UPDATE stock_tujuan SET jumlah = jumlah - $qty 
                             WHERE id_material='$id_material' AND id_tujuan='$id_tujuan'");

        mysqli_commit($conn);
        setNotifikasi('success', 'Transaksi berhasil dihapus!');
        header("Location: barang_keluar.php");
        exit;
    } catch (Exception $e) {
        mysqli_rollback($conn);
        setNotifikasi('error', 'Gagal menghapus transaksi!');
        header("Location: barang_keluar.php");
        exit;
    }
} elseif ($type == 'transfer') {
    $id = $_GET['id'];
    $id_material = $_GET['id_material'];
    $qty = $_GET['qty'];
    $dari_tujuan = $_GET['dari_tujuan'];
    $ke_tujuan = $_GET['ke_tujuan'];

    mysqli_begin_transaction($conn);

    try {
        // Hapus transaksi transfer
        mysqli_query($conn, "DELETE FROM transaksi_keluar_tujuan WHERE id_transaksi='$id'");

        // Hapus transaksi masuk tujuan
        mysqli_query($conn, "DELETE FROM transaksi_masuk_tujuan 
                            WHERE id_material='$id_material' AND id_tujuan='$ke_tujuan' AND qty='$qty' 
                            ORDER BY id_transaksi DESC LIMIT 1");

        // Kembalikan stok cabang asal
        mysqli_query($conn, "UPDATE stock_tujuan SET jumlah = jumlah + $qty 
                            WHERE id_material='$id_material' AND id_tujuan='$dari_tujuan'");

        // Kurangi stok cabang tujuan
        mysqli_query($conn, "UPDATE stock_tujuan SET jumlah = jumlah - $qty 
                            WHERE id_material='$id_material' AND id_tujuan='$ke_tujuan'");

        mysqli_commit($conn);
        setNotifikasi('success', 'Transfer berhasil dibatalkan!');
        header("Location: stok_cabang.php?id=$dari_tujuan");
        exit;
    } catch (Exception $e) {
        mysqli_rollback($conn);
        setNotifikasi('error', 'Gagal membatalkan transfer!');
        header("Location: stok_cabang.php?id=$dari_tujuan");
        exit;
    }
} elseif ($type == 'gunakan') {
    $id = $_GET['id'];
    $id_material = $_GET['id_material'];
    $qty = $_GET['qty'];
    $id_tujuan = $_GET['id_tujuan'];

    mysqli_begin_transaction($conn);

    try {
        // Hapus transaksi digunakan
        mysqli_query($conn, "DELETE FROM transaksi_keluar_dari_cabang WHERE id_transaksi='$id'");

        // Kembalikan stok cabang
        mysqli_query($conn, "UPDATE stock_tujuan SET jumlah = jumlah + $qty 
                            WHERE id_material='$id_material' AND id_tujuan='$id_tujuan'");

        mysqli_commit($conn);
        setNotifikasi('success', 'Barang digunakan berhasil dibatalkan!');
        header("Location: stok_cabang.php?id=$id_tujuan");
        exit;
    } catch (Exception $e) {
        mysqli_rollback($conn);
        setNotifikasi('error', 'Gagal membatalkan barang!');
        header("Location: stok_cabang.php?id=$id_tujuan");
        exit;
    }
}

header("Location: index.php");
exit;
