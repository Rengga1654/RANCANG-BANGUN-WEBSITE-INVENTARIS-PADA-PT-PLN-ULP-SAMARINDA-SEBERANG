<?php
require 'function.php';
require 'cek.php';

if ($_SESSION['level'] != 'admin') {
    header("Location: index.php");
    exit;
}

$action = $_GET['action'];
$id_permintaan = $_GET['id'];

if ($action == 'setujui') {

    mysqli_begin_transaction($conn);

    try {

        // Ambil semua material yang diminta
        $detail = mysqli_query($conn, "
            SELECT pd.id_material, pd.jumlah
            FROM permintaan_detail pd
            WHERE pd.id_permintaan='$id_permintaan'
        ");

        if (mysqli_num_rows($detail) == 0) {
            throw new Exception("Detail permintaan tidak ditemukan!");
        }

        // ==========================
        // CEK STOK DULU
        // ==========================
        while ($d = mysqli_fetch_assoc($detail)) {

            $id_material = $d['id_material'];
            $qty = $d['jumlah'];

            $stok = mysqli_fetch_assoc(mysqli_query(
                $conn,
                "SELECT jumlah
                 FROM stock_gudang
                 WHERE id_material='$id_material'"
            ));

            if (!$stok || $stok['jumlah'] < $qty) {

                $nama = mysqli_fetch_assoc(mysqli_query(
                    $conn,
                    "SELECT nama
                     FROM material
                     WHERE id_material='$id_material'"
                ));

                throw new Exception(
                    "Stok material " . $nama['nama'] . " tidak mencukupi."
                );
            }
        }

        // ulangi query karena pointer sudah habis
        $detail = mysqli_query($conn, "
            SELECT id_material,jumlah
            FROM permintaan_detail
            WHERE id_permintaan='$id_permintaan'
        ");

        // ==========================
        // KURANGI STOK GUDANG
        // ==========================
        while ($d = mysqli_fetch_assoc($detail)) {

            mysqli_query($conn, "
                UPDATE stock_gudang
                SET jumlah = jumlah - {$d['jumlah']}
                WHERE id_material='{$d['id_material']}'
            ");
        }

        // update status
        mysqli_query($conn, "
            UPDATE permintaan
            SET status='disetujui'
            WHERE id_permintaan='$id_permintaan'
        ");

        mysqli_commit($conn);

        setNotifikasi(
            'success',
            'Permintaan berhasil disetujui dan stok gudang telah diperbarui!'
        );
    } catch (Exception $e) {

        mysqli_rollback($conn);

        setNotifikasi(
            'error',
            $e->getMessage()
        );
    }

    header("Location: daftar_permintaan.php");
    exit;
} elseif ($action == 'tolak') {
    $update = mysqli_query($conn, "UPDATE permintaan SET status = 'ditolak' WHERE id_permintaan = '$id_permintaan'");
    if ($update) {
        setNotifikasi('success', 'Permintaan ditolak!');
    } else {
        setNotifikasi('error', 'Gagal menolak permintaan!');
    }
    header("Location: daftar_permintaan.php");
    exit;
} elseif ($action == 'kirim') {
    $tanggal = date('Y-m-d');

    // Cek surat jalan sudah dibuat
    $cek_surat = mysqli_query($conn, "SELECT * FROM surat_jalan WHERE id_permintaan = '$id_permintaan'");
    $surat = mysqli_fetch_assoc($cek_surat);

    if (!$surat) {
        setNotifikasi('error', 'Buat surat jalan terlebih dahulu sebelum mengirim Material!');
        header("Location: daftar_permintaan.php");
        exit;
    }

    $id_surat = $surat['id_surat'];
    $id_tujuan = $surat['id_tujuan'];

    // Ambil detail surat jalan (material dan jumlah)
    $query_detail = mysqli_query($conn, "
        SELECT sjd.*, m.harga 
        FROM surat_jalan_detail sjd
        JOIN material m ON m.id_material = sjd.id_material
        WHERE sjd.id_surat = '$id_surat'
    ");

    if (mysqli_num_rows($query_detail) == 0) {
        setNotifikasi('error', 'Detail surat jalan tidak ditemukan!');
        header("Location: daftar_permintaan.php");
        exit;
    }

    // Mulai transaksi
    mysqli_begin_transaction($conn);

    try {
        // Update status permintaan menjadi 'dikirim'
        mysqli_query($conn, "UPDATE permintaan SET status = 'dikirim' WHERE id_permintaan = '$id_permintaan'");

        // Looping setiap material
        while ($detail = mysqli_fetch_assoc($query_detail)) {
            $id_material = $detail['id_material'];
            $qty = $detail['jumlah_kirim'];
            $harga = $detail['harga'];


            // Catat transaksi keluar gudang
            // Catat transaksi keluar gudang
            mysqli_query($conn, "INSERT INTO transaksi_keluar_gudang (id_material, id_tujuan, tanggal, qty)
VALUES ('$id_material','$id_tujuan','$tanggal','$qty')");

            // Catat transaksi masuk tujuan
            mysqli_query($conn, "INSERT INTO transaksi_masuk_tujuan (id_material, id_tujuan, tanggal, qty, sumber) 
                                VALUES ('$id_material', '$id_tujuan', '$tanggal', '$qty', 'Permintaan')");

            // Tambah stok cabang
            $cek_stok_cabang = mysqli_query($conn, "SELECT * FROM stock_tujuan WHERE id_material='$id_material' AND id_tujuan='$id_tujuan'");
            if (mysqli_num_rows($cek_stok_cabang) > 0) {
                mysqli_query($conn, "UPDATE stock_tujuan SET jumlah = jumlah + $qty 
                                    WHERE id_material='$id_material' AND id_tujuan='$id_tujuan'");
            } else {
                mysqli_query($conn, "INSERT INTO stock_tujuan (id_material, id_tujuan, jumlah) 
                                    VALUES ('$id_material', '$id_tujuan', '$qty')");
            }
        }

        // Update status surat jalan menjadi 'dikirim'
        mysqli_query($conn, "UPDATE surat_jalan SET status = 'dikirim' WHERE id_surat = '$id_surat'");

        mysqli_commit($conn);
        setNotifikasi('success', 'Material berhasil dikirim ke cabang!');
    } catch (Exception $e) {
        mysqli_rollback($conn);
        setNotifikasi('error', 'Gagal mengirim Material: ' . $e->getMessage());
    }

    header("Location: daftar_permintaan.php");
    exit;
}
